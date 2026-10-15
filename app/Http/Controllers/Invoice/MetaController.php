<?php

namespace App\Http\Controllers\Invoice;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Invoice Platform filter options (API_SPEC §13, endpoint 28).
 *
 * GET /meta/filters — distinct state/product/payment-mode values, taken from
 * live data (active sessions only) rather than a hard-coded list.
 */
class MetaController extends InvoiceApiController
{
    public function filters(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        return $this->ok([
            'states' => $this->distinct(
                'SELECT DISTINCT c.`state` AS v FROM `Client` c '
                . "JOIN `Session` s ON s.`clientId` = c.`id` AND s.`active` = 1 WHERE c.`state` IS NOT NULL AND c.`state` <> ''"
            ),
            'products' => $this->distinct(
                "SELECT DISTINCT s.`product` AS v FROM `Session` s "
                . "WHERE s.`active` = 1 AND s.`product` IS NOT NULL AND s.`product` <> ''"
            ),
            'paymentModes' => $this->distinct(
                'SELECT DISTINCT p.`paymentMode` AS v FROM `Payment` p '
                . "JOIN `Session` s ON s.`id` = p.`sessionId` AND s.`active` = 1 "
                . "WHERE p.`paymentMode` IS NOT NULL AND p.`paymentMode` <> ''"
            ),
        ]);
    }

    private function distinct(string $sql): array
    {
        $values = [];
        foreach ($this->db()->select($sql) as $row) {
            $value = trim((string) $row->v);
            if ($value !== '' && !in_array($value, $values, true)) {
                $values[] = $value;
            }
        }

        sort($values, SORT_NATURAL | SORT_FLAG_CASE);

        return $values;
    }
}
