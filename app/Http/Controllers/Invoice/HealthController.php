<?php

namespace App\Http\Controllers\Invoice;

use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Invoice Platform health (API_SPEC §1, endpoint 1). Liveness check; no auth.
 */
class HealthController extends InvoiceApiController
{
    public function health(): JsonResponse
    {
        $database = 'unreachable';

        try {
            $this->db()->select('SELECT 1');
            $database = 'ok';
        } catch (Throwable $e) {
            $database = 'unreachable';
        }

        return response()->json([
            'status' => 'ok',
            'database' => $database,
        ], 200);
    }
}
