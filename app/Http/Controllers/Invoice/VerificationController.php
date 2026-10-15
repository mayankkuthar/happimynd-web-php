<?php

namespace App\Http\Controllers\Invoice;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Invoice Platform payment verification (API_SPEC §10, endpoints 24–25).
 *
 * GET /verification/summary — latest import batch and match counts.
 * GET /verification/issues  — paid payments with no or a failed Razorpay match.
 *
 * POST /verification/upload (endpoint 23) is left on the intern side: it parses
 * a Razorpay .xlsx/.csv export, which needs a spreadsheet parser.
 */
class VerificationController extends InvoiceApiController
{
    public function summary(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        $batch = $this->db()->table('RazorpayImportBatch')->orderBy('createdAt', 'desc')->first();

        $counts = ['MATCHED' => 0, 'FALSE_PAYMENT' => 0, 'NO_MATCH' => 0, 'AMBIGUOUS' => 0];
        foreach ($this->db()->table('RazorpayPaymentRecord')
            ->selectRaw('`matchStatus`, COUNT(*) AS c')
            ->groupBy('matchStatus')->get() as $row) {
            $counts[$row->matchStatus] = (int) $row->c;
        }

        return $this->ok([
            'latestBatch' => $batch === null ? null : [
                'id' => (string) $batch->id,
                'fileName' => (string) $batch->fileName,
                'uploadedById' => $batch->uploadedById !== null ? (string) $batch->uploadedById : null,
                'rowCount' => (int) $batch->rowCount,
                'matchedCount' => (int) $batch->matchedCount,
                'falsePaymentCount' => (int) $batch->falsePaymentCount,
                'noMatchCount' => (int) $batch->noMatchCount,
                'ambiguousCount' => (int) $batch->ambiguousCount,
                'createdAt' => $this->iso($batch->createdAt),
            ],
            'counts' => $counts,
        ]);
    }

    public function issues(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        [$page, $pageSize, $offset] = $this->paging($request);

        $query = $this->db()->table('Payment as p')
            ->join('Session as s', 's.id', '=', 'p.sessionId')
            ->where('s.active', 1)
            ->where('p.paymentVerified', 1)
            ->leftJoin('RazorpayPaymentRecord as r', 'r.matchedPaymentId', '=', 'p.id')
            ->where(function ($q) {
                $q->whereNull('r.id')->orWhere('r.matchStatus', '<>', 'MATCHED');
            });

        $total = (clone $query)->count('p.id');

        $rows = $query
            ->select('p.*', 'r.status as razorpayStatus', 'r.matchStatus as matchStatus')
            ->orderBy('p.paymentDate', 'desc')
            ->orderBy('p.id', 'desc')
            ->offset($offset)
            ->limit($pageSize)
            ->get();

        $clients = $this->db()->table('Client')
            ->whereIn('id', $rows->pluck('clientId')->unique()->values()->all())
            ->get()
            ->keyBy('id');

        $data = $rows->map(function ($p) use ($clients) {
            $client = $clients->get($p->clientId);
            $hasRecord = $p->matchStatus !== null;

            return [
                'id' => (string) $p->id,
                'clientName' => $client !== null ? (string) $client->clientName : null,
                'phone' => $client !== null && $client->phone !== null ? (string) $client->phone : null,
                'invoiceNumber' => $p->invoiceNumber !== null ? (string) $p->invoiceNumber : null,
                'paymentDate' => $this->iso($p->paymentDate),
                'paymentAmount' => $this->money($p->paymentAmount),
                'paymentMode' => $p->paymentMode !== null ? (string) $p->paymentMode : null,
                'issueType' => $hasRecord && $p->matchStatus === 'FALSE_PAYMENT' ? 'FALSE_PAYMENT' : 'NO_RAZORPAY_RECORD',
                'razorpayStatus' => $p->razorpayStatus !== null ? (string) $p->razorpayStatus : null,
            ];
        })->values()->all();

        return $this->ok($this->paged($data, $page, $pageSize, (int) $total));
    }
}
