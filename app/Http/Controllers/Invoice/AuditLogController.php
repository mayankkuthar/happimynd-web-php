<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Invoice\Concerns\PresentsRows;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Invoice Platform audit logs (API_SPEC §11, endpoint 26).
 *
 * GET /audit-logs — paged audit trail, each row with its acting user.
 */
class AuditLogController extends InvoiceApiController
{
    use PresentsRows;

    public function index(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        [$page, $pageSize, $offset] = $this->paging($request, 50);

        $entity = $request->query('entity');
        $action = $request->query('action');

        $query = $this->db()->table('AuditLog as a')
            ->leftJoin('User as u', 'u.id', '=', 'a.userId');

        if (is_string($entity) && $entity !== '') {
            $query->where('a.entity', $entity);
        }
        if (is_string($action) && $action !== '') {
            $query->where('a.action', $action);
        }

        $total = (clone $query)->count('a.id');

        $rows = $query->select('a.*', 'u.name as userName', 'u.email as userEmail')
            ->orderBy('a.timestamp', 'desc')
            ->orderBy('a.id', 'desc')
            ->offset($offset)
            ->limit($pageSize)
            ->get();

        $data = $rows->map(function ($r) {
            $user = $r->userId !== null && $r->userName !== null
                ? ['name' => (string) $r->userName, 'email' => (string) $r->userEmail]
                : null;

            return $this->presentAudit($r, $user);
        })->values()->all();

        return $this->ok($this->paged($data, $page, $pageSize, (int) $total));
    }
}
