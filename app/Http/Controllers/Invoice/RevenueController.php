<?php

namespace App\Http\Controllers\Invoice;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Invoice Platform revenue (API_SPEC §9, endpoint 22).
 *
 * GET /revenue — revenue for active sessions with breakdowns by month,
 * product, state, payment mode and source. Each bucket is sorted by
 * paymentAmount high to low; a missing key shows as "Unspecified".
 */
class RevenueController extends InvoiceApiController
{
    public function index(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        $base = 'FROM `Payment` p '
            . 'JOIN `Session` s ON s.`id` = p.`sessionId` AND s.`active` = 1 '
            . 'JOIN `Client` c ON c.`id` = p.`clientId`';

        $totals = $this->db()->selectOne(
            'SELECT COALESCE(SUM(p.`paymentAmount`),0) AS totalRevenue, '
            . 'COALESCE(SUM(p.`taxableValue`),0) AS totalTaxableValue, '
            . 'COALESCE(SUM(COALESCE(p.`igst`,0) + COALESCE(p.`cgst`,0) + COALESCE(p.`sgst`,0)),0) AS totalGst, '
            . 'COUNT(DISTINCT p.`sessionId`) AS totalSessions, '
            . 'COUNT(*) AS paymentCount '
            . $base
        );

        $paymentCount = (int) ($totals->paymentCount ?? 0);
        $totalRevenue = (float) ($totals->totalRevenue ?? 0);
        $average = $paymentCount > 0 ? round($totalRevenue / $paymentCount, 2) : 0.0;

        return $this->ok([
            'totalRevenue' => $totalRevenue,
            'totalTaxableValue' => (float) ($totals->totalTaxableValue ?? 0),
            'totalGst' => (float) ($totals->totalGst ?? 0),
            'totalSessions' => (int) ($totals->totalSessions ?? 0),
            'averagePaymentAmount' => $average,

            'byMonth' => $this->bucket("DATE_FORMAT(p.`paymentDate`, '%Y-%m')", $base),
            'byProduct' => $this->bucket('s.`product`', $base),
            'byState' => $this->bucket('c.`state`', $base),
            'byPaymentMode' => $this->bucket('p.`paymentMode`', $base),
            'bySource' => $this->bucket('s.`source`', $base),
        ]);
    }

    private function bucket(string $expr, string $base): array
    {
        $rows = $this->db()->select(
            'SELECT COALESCE(' . $expr . ", 'Unspecified') AS `key`, "
            . 'COALESCE(SUM(p.`paymentAmount`),0) AS paymentAmount, '
            . 'COALESCE(SUM(p.`taxableValue`),0) AS taxableValue, '
            . 'COALESCE(SUM(p.`igst`),0) AS igst, '
            . 'COALESCE(SUM(p.`cgst`),0) AS cgst, '
            . 'COALESCE(SUM(p.`sgst`),0) AS sgst, '
            . 'COUNT(*) AS `count` '
            . $base . ' GROUP BY 1'
        );

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'key' => (string) $r->key,
                'paymentAmount' => (float) $r->paymentAmount,
                'taxableValue' => (float) $r->taxableValue,
                'igst' => (float) $r->igst,
                'cgst' => (float) $r->cgst,
                'sgst' => (float) $r->sgst,
                'count' => (int) $r->count,
            ];
        }

        usort($out, function ($a, $b) {
            return $b['paymentAmount'] <=> $a['paymentAmount'];
        });

        return $out;
    }
}
