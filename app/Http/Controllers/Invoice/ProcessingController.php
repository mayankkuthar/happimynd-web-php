<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Invoice\Concerns\PresentsRows;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Invoice Platform processing (API_SPEC §7, endpoints 13–16).
 *
 * GET  /processing/stats       — status counts for VOICE or DATA.
 * GET  /processing             — paged voice and data processing records.
 * POST /voice/{id}/retry       — reset a failed voice job (OPERATOR).
 * POST /data/{id}/retry        — reset a failed invoice/data job (OPERATOR).
 *
 * The PDF and send-email endpoints (17–18) are left on the intern side: they
 * need the stored artifact and a mail provider.
 */
class ProcessingController extends InvoiceApiController
{
    use PresentsRows;

    private const STATUSES = ['PENDING', 'PROCESSING', 'COMPLETED', 'FAILED', 'SKIPPED'];

    public function stats(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        $type = strtoupper((string) $request->query('type', 'VOICE'));
        if (!in_array($type, ['VOICE', 'DATA'], true)) {
            return $this->error('Invalid request', 400, ['type' => 'Expected VOICE or DATA']);
        }

        $table = $type === 'VOICE' ? 'VoiceProcessing' : 'DataProcessing';
        $rows = $this->db()->table($table)
            ->selectRaw('`status`, COUNT(*) AS c')
            ->groupBy('status')
            ->get();

        $counts = ['total' => 0, 'pending' => 0, 'processing' => 0, 'completed' => 0, 'failed' => 0];
        $map = ['PENDING' => 'pending', 'PROCESSING' => 'processing', 'COMPLETED' => 'completed', 'FAILED' => 'failed'];

        foreach ($rows as $row) {
            $counts['total'] += (int) $row->c;
            if (isset($map[$row->status])) {
                $counts[$map[$row->status]] += (int) $row->c;
            }
        }

        return $this->ok($counts);
    }

    public function index(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        [$page, $pageSize, $offset] = $this->paging($request);

        $type = $request->query('type');
        $type = $type === null || $type === '' ? null : strtoupper((string) $type);
        if ($type !== null && !in_array($type, ['VOICE', 'DATA'], true)) {
            return $this->error('Invalid request', 400, ['type' => 'Expected VOICE or DATA']);
        }

        $status = $request->query('status');
        if (is_string($status) && $status !== '' && !in_array($status, self::STATUSES, true)) {
            return $this->error('Invalid request', 400, ['status' => 'Unknown status']);
        }

        $wantVoice = $type === null || $type === 'VOICE';
        $wantData = $type === null || $type === 'DATA';

        $voice = $wantVoice ? $this->pageRows('VoiceProcessing', $status, $offset, $pageSize) : null;
        $data = $wantData ? $this->pageRows('DataProcessing', $status, $offset, $pageSize) : null;

        return $this->ok([
            'voice' => $voice === null ? [] : $voice['rows'],
            'data' => $data === null ? [] : $data['rows'],
            'voiceTotal' => $voice === null ? 0 : $voice['total'],
            'dataTotal' => $data === null ? 0 : $data['total'],
        ]);
    }

    public function voiceRetry(Request $request, string $id): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'OPERATOR')) {
            return $denied;
        }

        $row = $this->db()->table('VoiceProcessing')->where('id', $id)->first(['id', 'clientId', 'sessionId', 'status', 'error']);
        if ($row === null) {
            return $this->error('Processing record not found', 404);
        }

        $this->db()->table('VoiceProcessing')->where('id', $id)->update([
            'status' => 'PENDING',
            'error' => null,
            'updatedAt' => $this->nowUtc(),
        ]);

        $this->audit('RETRY_VOICE', 'VoiceProcessing', $id, ['status' => $row->status, 'error' => $row->error], ['status' => 'PENDING'], $this->user($request)['sub'] ?? null);

        return $this->ok(['ok' => true]);
    }

    public function dataRetry(Request $request, string $id): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'OPERATOR')) {
            return $denied;
        }

        $row = $this->db()->table('DataProcessing')->where('id', $id)->first(['id', 'clientId', 'sessionId', 'status', 'error']);
        if ($row === null) {
            return $this->error('Processing record not found', 404);
        }

        $this->db()->table('DataProcessing')->where('id', $id)->update([
            'status' => 'PENDING',
            'error' => null,
            'updatedAt' => $this->nowUtc(),
        ]);

        $this->audit('RETRY_DATA', 'DataProcessing', $id, ['status' => $row->status, 'error' => $row->error], ['status' => 'PENDING'], $this->user($request)['sub'] ?? null);

        return $this->ok(['ok' => true]);
    }

    private function pageRows(string $table, $status, int $offset, int $pageSize)
    {
        $query = $this->db()->table($table);
        if (is_string($status) && $status !== '') {
            $query->where('status', $status);
        }

        $total = (clone $query)->count();

        $rows = $query->orderBy('updatedAt', 'desc')
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($pageSize)
            ->get();

        $clientIds = $rows->pluck('clientId')->unique()->values()->all();
        $sessionIds = $rows->pluck('sessionId')->unique()->values()->all();

        $clients = $clientIds === [] ? collect()
            : $this->db()->table('Client')->whereIn('id', $clientIds)->get()->keyBy('id');
        $sessions = $sessionIds === [] ? collect()
            : $this->db()->table('Session')->whereIn('id', $sessionIds)->get()->keyBy('id');

        $invoices = collect();
        if ($table === 'DataProcessing') {
            $invoices = $this->db()->table('InvoiceTransaction')->whereIn('dataProcessingId', $rows->pluck('id')->all())->keyBy('dataProcessingId');
        }

        $decorated = $rows->map(function ($r) use ($table, $clients, $sessions, $invoices) {
            $client = $clients->get($r->clientId);
            $session = $sessions->get($r->sessionId);

            $item = array_merge($table === 'VoiceProcessing' ? $this->presentVoice($r) : $this->presentData($r), [
                'type' => $table === 'VoiceProcessing' ? 'VOICE' : 'DATA',
                'client' => $client !== null
                    ? ['id' => (string) $client->id, 'clientName' => (string) $client->clientName,
                        'phone' => $client->phone !== null ? (string) $client->phone : null]
                    : null,
                'session' => $session !== null
                    ? ['id' => (string) $session->id, 'product' => $session->product !== null ? (string) $session->product : null,
                        'sessionNumber' => (int) $session->sessionNumber]
                    : null,
            ]);

            if ($table === 'DataProcessing') {
                $inv = $invoices->get($r->id);
                $item['invoiceTransaction'] = $inv !== null ? [
                    'invoiceNumber' => $inv->invoiceNumber !== null ? (string) $inv->invoiceNumber : null,
                    'sessionsBooked' => $inv->sessionsBooked !== null ? (int) $inv->sessionsBooked : null,
                    'paymentAmount' => $this->money($inv->paymentAmount),
                    'paymentsCount' => (int) $inv->paymentsCount,
                ] : null;
            }

            return $item;
        })->values()->all();

        return ['rows' => $decorated, 'total' => (int) $total];
    }
}
