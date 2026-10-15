<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Invoice\Concerns\PresentsRows;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Invoice Platform sessions (API_SPEC §4, endpoints 7–9).
 *
 * GET /sessions        — paged active sessions, newest first, with the client
 *                        and the latest payment / voice / data row.
 * GET /sessions/{id}   — one session with its client and full child rows.
 * GET /sessions/{id}/source-record — the raw spreadsheet row it came from.
 */
class SessionController extends InvoiceApiController
{
    use PresentsRows;

    public function index(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        [$page, $pageSize, $offset] = $this->paging($request);

        $status = $request->query('status');
        $clientId = $request->query('clientId');

        $query = $this->db()->table('Session')->where('active', 1);

        if (is_string($status) && $status !== '') {
            if (!in_array($status, ['PENDING', 'BOOKED', 'COMPLETED', 'CANCELLED'], true)) {
                return $this->error('Invalid request', 400, ['status' => 'Invalid session status']);
            }
            $query->where('sessionStatus', $status);
        }

        if (is_string($clientId) && $clientId !== '') {
            $query->where('clientId', $clientId);
        }

        $total = (clone $query)->count();

        $rows = $query->orderBy('updatedAt', 'desc')
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($pageSize)
            ->get();

        $items = $this->decorateSessions($rows);

        return $this->ok($this->paged($items, $page, $pageSize, (int) $total));
    }

    public function show(string $id): JsonResponse
    {
        $session = $this->db()->table('Session')->where('id', $id)->first();
        if ($session === null) {
            return $this->error('Session not found', 404);
        }

        $client = $this->db()->table('Client')->where('id', $session->clientId)->first();
        $payments = $this->db()->table('Payment')->where('sessionId', $id)->orderBy('paymentDate')->get();
        $voice = $this->db()->table('VoiceProcessing')->where('sessionId', $id)->orderBy('createdAt')->get();
        $data = $this->db()->table('DataProcessing')->where('sessionId', $id)->orderBy('createdAt')->get();

        $templates = $this->db()->table('Template')->get()->keyBy('id');

        return $this->ok([
            'session' => array_merge($this->presentSession($session), [
                'client' => $client !== null
                    ? ['clientName' => (string) $client->clientName, 'phone' => $client->phone !== null ? (string) $client->phone : null]
                    : null,
                'payments' => $payments->map(function ($p) {
                    return $this->presentPayment($p);
                })->values()->all(),
                'voiceProcessing' => $voice->map(function ($v) use ($templates) {
                    return array_merge(
                        $this->presentVoice($v),
                        ['template' => $v->templateId !== null && $templates->has($v->templateId)
                            ? $this->presentTemplate($templates->get($v->templateId))
                            : null]
                    );
                })->values()->all(),
                'dataProcessing' => $data->map(function ($d) use ($templates) {
                    return array_merge(
                        $this->presentData($d),
                        ['template' => $d->templateId !== null && $templates->has($d->templateId)
                            ? $this->presentTemplate($templates->get($d->templateId))
                            : null]
                    );
                })->values()->all(),
            ]),
        ]);
    }

    public function sourceRecord(string $id): JsonResponse
    {
        $session = $this->db()->table('Session')->where('id', $id)->first(['id', 'externalRowId', 'lastSyncedAt']);
        if ($session === null) {
            return $this->error('Session not found', 404);
        }

        $record = $this->db()->table('SyncRecord')
            ->where('sessionId', $id)
            ->orderBy('lastSyncedAt', 'desc')
            ->first();

        if ($record === null) {
            return $this->error('Source record not found', 404);
        }

        return $this->ok([
            'source' => (string) $record->source,
            'externalRowId' => (string) $record->externalRowId,
            'lastSyncedAt' => $this->iso($record->lastSyncedAt),
            'rawData' => $this->json($record->rawData, []),
        ]);
    }

    private function decorateSessions($rows): array
    {
        $ids = $rows->pluck('id')->all();
        if ($ids === []) {
            return [];
        }

        $clients = $this->db()->table('Client')
            ->whereIn('id', $rows->pluck('clientId')->unique()->values()->all())
            ->get()
            ->keyBy('id');

        $latestPayment = $this->windowLatest('Payment', $ids, [
            'paymentAmount', 'paymentDate', 'paymentMode', 'invoiceNumber', 'paymentVerified', 'paymentStatus',
        ]);
        $latestVoice = $this->windowLatest('VoiceProcessing', $ids, ['status']);
        $latestData = $this->windowLatest('DataProcessing', $ids, ['status']);

        $out = [];
        foreach ($rows as $s) {
            $client = $clients->get($s->clientId);
            $lp = $latestPayment->get($s->id);
            $lv = $latestVoice->get($s->id);
            $ld = $latestData->get($s->id);

            $out[] = array_merge($this->presentSession($s), [
                'client' => $client !== null
                    ? ['clientName' => (string) $client->clientName, 'phone' => $client->phone !== null ? (string) $client->phone : null]
                    : null,
                'payments' => $lp !== null ? [$this->presentPayment($lp)] : [],
                'voiceProcessing' => $lv !== null ? [$this->presentVoice($lv)] : [],
                'dataProcessing' => $ld !== null ? [$this->presentData($ld)] : [],
            ]);
        }

        return $out;
    }

    /** Latest row per session using a window function (MySQL 8). */
    private function windowLatest(string $table, array $sessionIds, array $columns)
    {
        $sql = 'SELECT * FROM (SELECT *, '
            . 'ROW_NUMBER() OVER (PARTITION BY `sessionId` ORDER BY `updatedAt` DESC, `id` DESC) AS rn '
            . 'FROM `' . $table . '` WHERE `sessionId` IN (' . implode(',', array_fill(0, count($sessionIds), '?')) . ')) t '
            . 'WHERE t.rn = 1';

        return collect($this->db()->select($sql, $sessionIds))->keyBy('sessionId');
    }
}
