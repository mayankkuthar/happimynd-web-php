<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Services\Hr\HrSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Shared helpers for the HR Auto API controllers.
 *
 * Contract (API_SPEC §1): core responses are plain JSON; errors use the
 * FastAPI envelope { "detail": "..." } (or a 422 list). Timestamps are UTC and
 * `created_at` is returned as epoch seconds.
 */
abstract class HrController extends Controller
{
    protected function db()
    {
        return DB::connection('hr');
    }

    protected function ok(array $data = [], int $status = 200): JsonResponse
    {
        return response()->json($data, $status);
    }

    protected function detail(string $message, int $status): JsonResponse
    {
        return response()->json(['detail' => $message], $status);
    }

    /** 422 { "detail": [ { loc, msg, type } ] } for a missing field (API_SPEC §1). */
    protected function missing(string $field, string $loc = 'query', string $type = 'missing'): JsonResponse
    {
        return response()->json(['detail' => [[
            'loc' => [$loc, $field],
            'msg' => 'Field required',
            'type' => $type,
        ]]], 422);
    }

    /** 422 for a present-but-invalid field. */
    protected function invalid(string $field, string $message, string $loc = 'body'): JsonResponse
    {
        return response()->json(['detail' => [[
            'loc' => [$loc, $field],
            'msg' => $message,
            'type' => 'value_error',
        ]]], 422);
    }

    /**
     * Validate a required source query/body value (API_SPEC §1):
     * missing → 422, unknown → 400 "Unknown source 'x'. Expected one of: ...".
     * Returns null when valid.
     */
    protected function checkSource($value, string $loc = 'body'): ?JsonResponse
    {
        $sources = (array) config('hr.sources');

        if ($value === null || $value === '') {
            return $this->missing('source', $loc);
        }

        if (!in_array($value, $sources, true)) {
            return $this->detail(
                "Unknown source '" . $value . "'. Expected one of: " . implode(', ', $sources) . '.',
                400
            );
        }

        return null;
    }

    protected function checkDepartment($value, string $loc = 'body'): ?JsonResponse
    {
        if ($value === null || $value === '') {
            return null; // department is nullable
        }

        $departments = (array) config('hr.departments');

        if (!in_array($value, $departments, true)) {
            return $this->detail(
                "Unknown department '" . $value . "'. Expected one of: " . implode(', ', $departments) . '.',
                400
            );
        }

        return null;
    }

    /** Decode a JSON column that should be an object (empty → {}). */
    protected function jsonObject($value): object
    {
        if (is_array($value)) {
            return $value === [] ? new stdClass() : (object) $this->deepObject($value);
        }

        $decoded = json_decode((string) $value, true);

        if (!is_array($decoded)) {
            return new stdClass();
        }

        return $decoded === [] ? new stdClass() : (object) $this->deepObject($decoded);
    }

    /** Decode a JSON column that should be an array of strings (empty → []). */
    protected function jsonArray($value): array
    {
        if (is_array($value)) {
            return array_values($value);
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? array_values($decoded) : [];
    }

    /** Present a `records` row as the RecordSummary shape (API_SPEC §4). */
    protected function presentSummary($row): array
    {
        return [
            'id' => (string) $row->id,
            'file_name' => (string) $row->file_name,
            'has_file' => $row->storage_key !== null && $row->storage_key !== '',
            'size_bytes' => (int) $row->size_bytes,
            'content_type' => (string) $row->content_type,
            'source' => (string) $row->source,
            'department' => $row->department !== null ? (string) $row->department : null,
            'status' => (string) $row->status,
            'viewed' => ((int) $row->viewed) === 1,
            'fields' => $this->jsonObject($row->fields),
            'confidence' => $this->jsonObject($row->confidence),
            'error' => $row->error !== null ? (string) $row->error : null,
            'used_ocr' => ((int) $row->used_ocr) === 1,
            'page_count' => (int) $row->page_count,
            'ai_status' => (string) $row->ai_status,
            'duplicate_of' => $row->duplicate_of !== null ? (string) $row->duplicate_of : null,
            'log' => $this->jsonArray($row->log),
            'created_at' => $this->epochSeconds($row->created_at),
            'elapsed_ms' => $this->elapsedMs($row->created_at, $row->finished_at ?? null),
        ];
    }

    /** Stats for a source: counts by status (API_SPEC §4). */
    protected function stats(?string $source = null): array
    {
        $query = $this->db()->table('records')->select('status', $this->db()->raw('COUNT(*) as c'))->groupBy('status');

        if ($source !== null) {
            $query->where('source', $source);
        }

        $counts = ['queued' => 0, 'processing' => 0, 'done' => 0, 'error' => 0];
        $total = 0;

        foreach ($query->get() as $row) {
            $counts[(string) $row->status] = (int) $row->c;
            $total += (int) $row->c;
        }

        return [
            'queued' => $counts['queued'],
            'processing' => $counts['processing'],
            'done' => $counts['done'],
            'error' => $counts['error'],
            'total' => $total,
        ];
    }

    protected function epochSeconds($value): float
    {
        $dt = $this->toUtc($value);

        if ($dt === null) {
            return 0.0;
        }

        return (float) ((int) $dt->getTimestamp()) + ((int) $dt->format('u')) / 1000000;
    }

    protected function elapsedMs($from, $to): ?int
    {
        if ($to === null || $to === '') {
            return null;
        }

        $a = $this->toUtc($from);
        $b = $this->toUtc($to);

        if ($a === null || $b === null) {
            return null;
        }

        $diff = (float) $b->format('U.u') - (float) $a->format('U.u');

        return (int) round($diff * 1000);
    }

    private function toUtc($value): ?\DateTime
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return new \DateTime($value->format('Y-m-d H:i:s.u'), new \DateTimeZone('UTC'));
        }

        $value = (string) $value;
        $dt = \DateTime::createFromFormat('Y-m-d H:i:s.u', $value, new \DateTimeZone('UTC'));

        if ($dt === false) {
            $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $value, new \DateTimeZone('UTC'));
        }

        if ($dt === false) {
            try {
                $dt = new \DateTime($value, new \DateTimeZone('UTC'));
            } catch (\Throwable $e) {
                return null;
            }
        }

        return $dt;
    }

    private function deepObject(array $value): array
    {
        // Keep associative arrays so they re-encode as JSON objects.
        return $value;
    }

    /** Read a JSON body regardless of Content-Type, with $request->all() fallback. */
    protected function jsonBody(Request $request): array
    {
        $raw = $request->getContent();

        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $request->all();
    }

    /**
     * Guard for the worker/queue endpoints. They are server-to-server, so they
     * accept either a valid admin session cookie or the shared
     * `X-Worker-Token` header (env HR_WORKER_TOKEN). Returns a 401 response
     * when neither is present, or null when authorised.
     */
    protected function workerDenied(Request $request): ?JsonResponse
    {
        $token = (string) env('HR_WORKER_TOKEN', '');
        $header = (string) $request->header('X-Worker-Token', '');

        if ($token !== '' && $header !== '' && hash_equals($token, $header)) {
            return null;
        }

        $session = app(HrSession::class);
        $cookie = $request->cookie($session->cookieName());

        if ($session->verify(is_string($cookie) ? $cookie : null)) {
            return null;
        }

        return $this->detail('Not authenticated', 401);
    }
}
