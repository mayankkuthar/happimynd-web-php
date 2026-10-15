<?php

namespace App\Http\Controllers\Invoice;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Invoice Platform sync (API_SPEC §8, endpoints 19–21).
 *
 * GET  /sync/status       — latest and last 20 sync runs.
 * GET  /sync/source-rows  — synced spreadsheet rows as raw data.
 * POST /sync/run          — trigger a sync now (ADMIN).
 *
 * The actual Google Sheets/Excel read lives in the intern's worker. POST
 * /sync/run records the intent (SYNC_TRIGGERED audit); the worker must pick it
 * up from there.
 */
class SyncController extends InvoiceApiController
{
    public function status(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        $rows = $this->db()->table('SyncRun')->orderBy('startedAt', 'desc')->get();

        return $this->ok([
            'latest' => $rows->first() ? $this->presentSyncRun($rows->first()) : null,
            'recent' => $rows->take(20)->map(function ($r) {
                return $this->presentSyncRun($r);
            })->values()->all(),
        ]);
    }

    public function sourceRows(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        [$page, $pageSize, $offset] = $this->paging($request, 50, 200);

        $total = $this->db()->table('SyncRecord')->count();

        $rows = $this->db()->table('SyncRecord')
            ->orderBy('lastSyncedAt', 'desc')
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($pageSize)
            ->get();

        $columns = [];
        $first = $rows->first();
        if ($first !== null) {
            $raw = $this->json($first->rawData, []);
            if (is_array($raw)) {
                foreach (array_keys($raw) as $key) {
                    $columns[] = (string) $key;
                }
            }
        }

        $data = $rows->map(function ($r) {
            return [
                'rowKey' => (string) $r->rowKey,
                'externalRowId' => (string) $r->externalRowId,
                'syncStatus' => (string) $r->syncStatus,
                'lastSyncedAt' => $this->iso($r->lastSyncedAt),
                'rawData' => $this->json($r->rawData, []),
            ];
        })->values()->all();

        return $this->ok([
            'columns' => $columns,
            'data' => $data,
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => (int) $total,
            'totalPages' => $total > 0 ? (int) ceil($total / $pageSize) : 0,
        ]);
    }

    public function run(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'ADMIN')) {
            return $denied;
        }

        $this->audit('SYNC_TRIGGERED', 'SyncRun', 'manual', null, ['requestedBy' => $this->user($request)['sub'] ?? null], $this->user($request)['sub'] ?? null);

        return $this->ok(['triggered' => true]);
    }

    private function presentSyncRun($r): array
    {
        return [
            'id' => (string) $r->id,
            'startedAt' => $this->iso($r->startedAt),
            'completedAt' => $this->iso($r->completedAt),
            'status' => (string) $r->status,
            'recordsAdded' => (int) $r->recordsAdded,
            'recordsUpdated' => (int) $r->recordsUpdated,
            'recordsSkipped' => (int) $r->recordsSkipped,
            'recordsErrored' => (int) $r->recordsErrored,
            'errorSummary' => $r->errorSummary !== null ? (string) $r->errorSummary : null,
        ];
    }
}
