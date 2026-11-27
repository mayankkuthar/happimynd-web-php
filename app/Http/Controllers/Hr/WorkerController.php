<?php

namespace App\Http\Controllers\Hr;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HR Auto worker/queue endpoints (API_SPEC §9.2, "worker mode"). The extraction
 * worker (on Render: Bedrock, OCR, GCS) never touches MySQL; these endpoints run
 * the §9.1 SQL against `records` for it:
 *
 *   claim  -> claimNext: oldest queued -> processing, attempts + 1
 *   done   -> markDone:    status done, replace fields, append log
 *   fail   -> markFailed:  status error (terminal), append log
 *   reap   -> reapStuck:   two UPDATEs: exhausted -> error, else -> queued
 *   depth  -> status counts
 *
 * JSON columns are written with parameterised raw SQL because the query
 * builder cannot bind inside an Expression.
 */
class WorkerController extends HrController
{
    private const MAX_ATTEMPTS = 3;

    private const TIMEOUT_MSG = 'Processing timed out and exceeded the retry limit.';

    /** POST /worker/claim — claimNext (MIGRATION_GUIDE §5.1). */
    public function claim(Request $request): Response
    {
        if ($denied = $this->workerDenied($request)) {
            return $denied;
        }

        $row = $this->db()->transaction(function () {
            $row = $this->db()->table('records')
                ->where('status', 'queued')
                ->orderBy('created_at')
                // Laravel 8.49 has no skipLocked(); MySQL 8 takes the clause verbatim (MIGRATION_GUIDE §5.1).
                ->lock('FOR UPDATE SKIP LOCKED')
                ->first();

            if ($row === null) {
                return null;
            }

            $this->db()->table('records')->where('id', $row->id)->update([
                'status' => 'processing',
                'claimed_at' => $this->db()->raw('NOW(3)'),
                'attempts' => $this->db()->raw('`attempts` + 1'),
            ]);

            return $this->db()->table('records')->where('id', $row->id)->first();
        });

        if ($row === null) {
            return response()->noContent();
        }

        // Portal resumes carry the candidate's own answers, in the GET /applications/:id
        // shape (§8); the worker overrides what the model read with them.
        $application = $this->db()->table('applications')->where('record_id', $row->id)->first();

        return $this->ok([
            'record' => [
                'id' => (string) $row->id,
                'file_name' => (string) $row->file_name,
                'storage_key' => $row->storage_key !== null ? (string) $row->storage_key : null,
                'application' => $application !== null ? $this->presentApplication($application) : null,
            ],
        ]);
    }

    /**
     * POST /worker/{id}/done — markDone. Replaces fields/confidence/text and
     * appends the worker's log lines to the existing log.
     */
    public function done(Request $request, string $id): Response
    {
        if ($denied = $this->workerDenied($request)) {
            return $denied;
        }

        $body = $this->jsonBody($request);

        $row = $this->db()->table('records')->where('id', $id)->first(['id', 'log', 'status']);
        if ($row === null) {
            return $this->detail('Record not found', 404);
        }
        if ((string) $row->status !== 'processing') {
            return $this->detail('Record is no longer processing', 409);
        }

        $fields = $body['fields'] ?? null;
        if ($fields !== null && !is_array($fields)) {
            return $this->invalid('fields', 'fields must be an object');
        }

        $confidence = $body['confidence'] ?? null;
        if ($confidence !== null && !is_array($confidence)) {
            return $this->invalid('confidence', 'confidence must be an object');
        }

        // §9.2 sends `text_content`; `text` is accepted from older workers.
        $text = array_key_exists('text_content', $body) ? (string) $body['text_content']
            : (array_key_exists('text', $body) ? (string) $body['text'] : null);

        $appended = $this->jsonArray($row->log);
        foreach ($this->jsonArray($body['log'] ?? null) as $entry) {
            $appended[] = $entry;
        }

        $sets = [
            '`status` = ?',
            '`error` = NULL',
            '`finished_at` = NOW(3)',
            '`claimed_at` = NULL',
            '`used_ocr` = ?',
            '`page_count` = ?',
            '`ai_status` = ?',
            '`log` = CAST(? AS JSON)',
        ];
        $bindings = [
            'done',
            !empty($body['used_ocr']) ? 1 : 0,
            isset($body['page_count']) ? max(0, (int) $body['page_count']) : 0,
            isset($body['ai_status']) ? (string) $body['ai_status'] : 'disabled',
            json_encode($appended),
        ];

        if ($fields !== null) {
            $sets[] = '`fields` = CAST(? AS JSON)';
            $bindings[] = json_encode($fields);
        }
        if ($confidence !== null) {
            $sets[] = '`confidence` = CAST(? AS JSON)';
            $bindings[] = json_encode($confidence);
        }
        if ($text !== null) {
            $sets[] = '`text_content` = ?';
            $bindings[] = $text;
        }

        $bindings[] = $id;
        $bindings[] = 'processing';
        // Only while still processing (§9.2): a reaped-and-reclaimed row must not be overwritten.
        $updated = $this->db()->update(
            'UPDATE `records` SET ' . implode(', ', $sets) . ' WHERE `id` = ? AND `status` = ?',
            $bindings
        );
        if ($updated === 0) {
            return $this->detail('Record is no longer processing', 409);
        }

        return response()->noContent();
    }

    /** POST /worker/{id}/fail — markFailed (terminal). */
    public function fail(Request $request, string $id): Response
    {
        if ($denied = $this->workerDenied($request)) {
            return $denied;
        }

        $body = $this->jsonBody($request);
        $message = isset($body['error']) ? trim((string) $body['error']) : '';
        if ($message === '') {
            $message = 'Processing failed.';
        }

        $row = $this->db()->table('records')->where('id', $id)->first(['id', 'log', 'status']);
        if ($row === null) {
            return $this->detail('Record not found', 404);
        }
        if ((string) $row->status !== 'processing') {
            return $this->detail('Record is no longer processing', 409);
        }

        $log = $this->jsonArray($row->log);
        $log[] = 'Processing failed: ' . $message;

        $updated = $this->db()->update(
            'UPDATE `records` SET `status` = ?, `error` = ?, `log` = CAST(? AS JSON), '
            . '`finished_at` = NOW(3), `claimed_at` = NULL WHERE `id` = ? AND `status` = ?',
            ['error', $message, json_encode($log), $id, 'processing']
        );
        if ($updated === 0) {
            return $this->detail('Record is no longer processing', 409);
        }

        return response()->noContent();
    }

    /** POST /worker/reap — reapStuck: two UPDATEs, exhausted first. */
    public function reap(Request $request): JsonResponse
    {
        if ($denied = $this->workerDenied($request)) {
            return $denied;
        }

        $body = $this->jsonBody($request);
        $timeout = isset($body['timeout_ms']) ? max(0, (int) $body['timeout_ms']) : 600000;
        $seconds = (int) ceil($timeout / 1000);

        $stuck = '`status` = \'processing\' AND `claimed_at` IS NOT NULL '
            . 'AND `claimed_at` < (NOW(3) - INTERVAL ? SECOND)';

        $exhausted = $this->db()->select(
            'SELECT `id` FROM `records` WHERE ' . $stuck . ' AND `attempts` >= ?',
            [$seconds, self::MAX_ATTEMPTS]
        );
        $retried = $this->db()->select(
            'SELECT `id` FROM `records` WHERE ' . $stuck . ' AND `attempts` < ?',
            [$seconds, self::MAX_ATTEMPTS]
        );

        // 1) exhausted -> error
        $this->db()->update(
            'UPDATE `records` SET `status` = ?, `error` = ?, '
            . '`log` = JSON_ARRAY_APPEND(`log`, \'$\', CAST(? AS JSON)), '
            . '`finished_at` = NOW(3), `claimed_at` = NULL '
            . 'WHERE ' . $stuck . ' AND `attempts` >= ?',
            ['error', self::TIMEOUT_MSG, json_encode(self::TIMEOUT_MSG), $seconds, self::MAX_ATTEMPTS]
        );

        // 2) otherwise -> back on the queue
        $this->db()->update(
            'UPDATE `records` SET `status` = ?, `claimed_at` = NULL '
            . 'WHERE ' . $stuck . ' AND `attempts` < ?',
            ['queued', $seconds, self::MAX_ATTEMPTS]
        );

        return $this->ok([
            // §9.2 shape; the id lists below are kept for the admin tools.
            'requeued' => count($retried),
            'failed' => count($exhausted),
            'reaped' => array_map(function ($r) {
                return (string) $r->id;
            }, $retried),
            'exhausted' => array_map(function ($r) {
                return (string) $r->id;
            }, $exhausted),
            'stats' => $this->stats(null),
        ]);
    }

    /** GET /worker/depth — { depth: <queued rows> } (§9.2). */
    public function depth(Request $request): JsonResponse
    {
        if ($denied = $this->workerDenied($request)) {
            return $denied;
        }

        $rows = $this->db()->table('records')
            ->selectRaw('`status`, COUNT(*) AS c')
            ->groupBy('status')
            ->get();

        $depth = [
            'queued' => 0,
            'processing' => 0,
            'done' => 0,
            'error' => 0,
        ];

        foreach ($rows as $row) {
            $depth[$row->status] = (int) $row->c;
        }

        // §9.2: `depth` is the number of queued rows; per-status counts ride along.
        return $this->ok(['depth' => $depth['queued'], 'counts' => $depth]);
    }
}
