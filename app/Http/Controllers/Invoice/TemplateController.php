<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Invoice\Concerns\PresentsRows;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Invoice Platform invoice templates (API_SPEC §12, endpoint 27).
 *
 * GET /templates — the template inventory (oldest first, so the default is
 * deterministic for new invoices).
 */
class TemplateController extends InvoiceApiController
{
    use PresentsRows;

    public function index(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        $rows = $this->db()->table('Template')
            ->orderBy('createdAt', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return $this->ok([
            'data' => $rows->map(function ($r) {
                return $this->presentTemplate($r);
            })->values()->all(),
        ]);
    }
}
