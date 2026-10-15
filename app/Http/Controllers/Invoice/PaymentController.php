<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Invoice\Concerns\PresentsRows;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Invoice Platform payments (API_SPEC §5, endpoint 10).
 *
 * GET /payments — paged payments for active sessions, newest payment date
 * first, each with its client and session summary.
 */
class PaymentController extends InvoiceApiController
{
    use PresentsRows;

    public function index(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        [$page, $pageSize, $offset] = $this->paging($request);

        $paymentMode = $request->query('paymentMode');
        $invoiceNumber = $request->query('invoiceNumber');
        $dateFrom = $request->query('dateFrom');
        $dateTo = $request->query('dateTo');

        $query = $this->db()->table('Payment as p')
            ->join('Session as s', 's.id', '=', 'p.sessionId')
            ->where('s.active', 1);

        if (is_string($paymentMode) && $paymentMode !== '') {
            $query->where('p.paymentMode', $paymentMode);
        }

        if (is_string($invoiceNumber) && $invoiceNumber !== '') {
            $like = '%' . $this->escapeLike($invoiceNumber) . '%';
            $query->where('p.invoiceNumber', 'like', $like);
        }

        if (is_string($dateFrom) && $dateFrom !== '') {
            $query->where('p.paymentDate', '>=', $dateFrom . ' 00:00:00.000');
        }
        if (is_string($dateTo) && $dateTo !== '') {
            $query->where('p.paymentDate', '<=', $dateTo . ' 23:59:59.999');
        }

        $total = (clone $query)->count('p.id');

        $rows = $query->select('p.*')
            ->orderBy('p.paymentDate', 'desc')
            ->orderBy('p.id', 'desc')
            ->offset($offset)
            ->limit($pageSize)
            ->get();

        $clientIds = $rows->pluck('clientId')->unique()->values()->all();
        $sessionIds = $rows->pluck('sessionId')->filter()->unique()->values()->all();

        $clients = $clientIds === [] ? collect()
            : $this->db()->table('Client')->whereIn('id', $clientIds)->get()->keyBy('id');
        $sessions = $sessionIds === [] ? collect()
            : $this->db()->table('Session')->whereIn('id', $sessionIds)->get()->keyBy('id');

        $data = $rows->map(function ($p) use ($clients, $sessions) {
            $client = $clients->get($p->clientId);
            $session = $p->sessionId !== null ? $sessions->get($p->sessionId) : null;

            return array_merge($this->presentPayment($p), [
                'client' => $client !== null
                    ? ['clientName' => (string) $client->clientName, 'phone' => $client->phone !== null ? (string) $client->phone : null]
                    : null,
                'session' => $session !== null
                    ? ['product' => $session->product !== null ? (string) $session->product : null,
                        'sessionNumber' => (int) $session->sessionNumber]
                    : null,
            ]);
        })->values()->all();

        return $this->ok($this->paged($data, $page, $pageSize, (int) $total));
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
