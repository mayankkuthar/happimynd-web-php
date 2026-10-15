<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Invoice\Concerns\PresentsRows;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Invoice Platform invoices (API_SPEC §6, endpoints 11–12).
 *
 * GET /invoices/stats — the invoice ledger figures.
 * GET /invoices       — paged consolidated invoices (one invoice can cover
 *                       several payments).
 */
class InvoicesController extends InvoiceApiController
{
    use PresentsRows;

    public function stats(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        return $this->ok($this->ledgerStats());
    }

    public function index(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        [$page, $pageSize, $offset] = $this->paging($request);

        $clientName = $request->query('clientName');
        $invoiceNumber = $request->query('invoiceNumber');
        $paymentDate = $request->query('paymentDate');
        $paymentStatus = $request->query('paymentStatus');
        $status = $request->query('status');

        if ($paymentStatus !== null && $paymentStatus !== ''
            && !in_array($paymentStatus, ['paid', 'pending'], true)) {
            return $this->error('Invalid request', 400, ['paymentStatus' => 'Expected paid or pending']);
        }

        if ($status !== null && $status !== ''
            && !in_array($status, ['PENDING', 'PAYMENT_VERIFIED', 'GENERATED', 'NEEDS_REVIEW', 'FAILED'], true)) {
            return $this->error('Invalid request', 400, ['status' => 'Unknown invoice status']);
        }

        $query = $this->db()->table('InvoiceTransaction as i')
            ->join('Client as c', 'c.id', '=', 'i.clientId')
            // One invoice can cover several payments: drop groups whose only
            // linked payment(s) were removed from the current spreadsheet.
            ->where('i.paymentsCount', '>', 0)
            ->whereExists(function ($q) {
                $q->selectRaw('1')
                    ->from('Payment as p')
                    ->join('Session as s', 's.id', '=', 'p.sessionId')
                    ->whereColumn('p.invoiceTransactionId', 'i.id')
                    ->where('s.active', 1);
            });

        if (is_string($clientName) && $clientName !== '') {
            $query->where('i.clientNameRaw', 'like', '%' . $this->escapeLike($clientName) . '%');
        }

        if (is_string($invoiceNumber) && $invoiceNumber !== '') {
            $query->where('i.invoiceNumber', 'like', '%' . $this->escapeLike($invoiceNumber) . '%');
        }

        if (is_string($paymentDate) && $paymentDate !== '') {
            $query->where('i.paymentDate', '>=', $paymentDate . ' 00:00:00.000')
                ->where('i.paymentDate', '<=', $paymentDate . ' 23:59:59.999');
        }

        if ($paymentStatus === 'paid') {
            $query->where('i.paymentVerified', 1);
        } elseif ($paymentStatus === 'pending') {
            $query->where('i.paymentVerified', 0);
        }

        if (is_string($status) && $status !== '') {
            $query->where('i.status', $status);
        }

        $total = (clone $query)->count('i.id');

        $rows = $query->select('i.*', 'c.clientName as clientClientName', 'c.phone as clientPhone', 'c.state as clientState')
            ->orderBy('i.paymentDate', 'desc')
            ->orderBy('i.id', 'desc')
            ->offset($offset)
            ->limit($pageSize)
            ->get();

        $data = $rows->map(function ($i) {
            return array_merge($this->presentInvoice($i), [
                'clientName' => $i->clientClientName !== null ? (string) $i->clientClientName : (string) $i->clientNameRaw,
                'phone' => $i->clientPhone !== null ? (string) $i->clientPhone : null,
                'state' => $i->clientState !== null ? (string) $i->clientState : null,
            ]);
        })->values()->all();

        return $this->ok($this->paged($data, $page, $pageSize, (int) $total));
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
