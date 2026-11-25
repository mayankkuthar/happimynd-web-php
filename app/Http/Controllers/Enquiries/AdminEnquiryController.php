<?php

namespace App\Http\Controllers\Enquiries;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Admin endpoints (API_SPEC §"Admin APIs"): list, detail, status update and
 * dashboard summary. All require the bearer token (middleware).
 */
class AdminEnquiryController extends EnquiriesController
{
    public function index(Request $request): JsonResponse
    {
        $type = strtoupper(trim((string) $request->query('type', '')));
        $status = strtoupper(trim((string) $request->query('status', '')));
        $fromDate = trim((string) $request->query('fromDate', ''));
        $toDate = trim((string) $request->query('toDate', ''));

        if ($type !== '' && !in_array($type, (array) config('enquiries.types'), true)) {
            return $this->validation('type', 'type must be one of: ' . implode(', ', (array) config('enquiries.types')));
        }

        if ($status !== '' && !in_array($status, (array) config('enquiries.statuses'), true)) {
            return $this->validation('status', 'status must be one of: ' . implode(', ', (array) config('enquiries.statuses')));
        }

        foreach (['fromDate' => $fromDate, 'toDate' => $toDate] as $field => $value) {
            if ($value !== '' && !$this->isDate($value)) {
                return $this->validation($field, $field . ' must be a date in YYYY-MM-DD format');
            }
        }

        $page = $this->intQuery($request, 'page', 1);

        if ($page === null) {
            return $this->validation('page', 'page must be an integer');
        }

        $pageSize = $this->intQuery($request, 'pageSize', 20);

        if ($pageSize === null) {
            return $this->validation('pageSize', 'pageSize must be an integer');
        }

        $page = max(1, $page);
        $pageSize = min(200, max(1, $pageSize));

        try {
            $stmt = $this->pdo()->prepare('CALL sp_get_enquiries(?,?,?,?,?,?)');
            $this->bind($stmt, [
                $type !== '' ? $type : null,
                $status !== '' ? $status : null,
                $fromDate !== '' ? $fromDate : null,
                $toDate !== '' ? $toDate : null,
                $page,
                $pageSize,
            ]);
            $stmt->execute();

            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            $total = 0;

            if ($stmt->nextRowset()) {
                $totals = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
                $total = (int) ($totals[0]['total_records'] ?? 0);
            }

            $stmt->closeCursor();
        } catch (Throwable $e) {
            return $this->dbFailure($e);
        }

        return response()->json([
            'data' => array_map([$this, 'presentRow'], $rows),
            'pagination' => [
                'page' => $page,
                'pageSize' => $pageSize,
                'totalRecords' => $total,
                'totalPages' => $total > 0 ? (int) ceil($total / $pageSize) : 0,
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        try {
            $rows = $this->callRows('CALL sp_get_enquiry_by_id(?)', [(int) $id]);
        } catch (Throwable $e) {
            return $this->dbFailure($e);
        }

        if ($rows === []) {
            return $this->error('NOT_FOUND', 'Enquiry not found', 404);
        }

        return response()->json(['data' => $this->presentRow($rows[0])]);
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $body = $this->body($request);
        $status = strtoupper($this->str($request, 'status', $body));

        if ($status === '') {
            return $this->validation('status', 'Invalid status value');
        }

        if (!in_array($status, (array) config('enquiries.statuses'), true)) {
            return $this->validation('status', 'Invalid status value');
        }

        $remarks = array_key_exists('adminRemarks', $body) && $body['adminRemarks'] !== null
            ? (string) $body['adminRemarks']
            : null;

        try {
            $this->callVoid('CALL sp_update_enquiry_status(?,?,?)', [(int) $id, $status, $remarks]);
        } catch (Throwable $e) {
            $failure = $this->dbFailure($e);

            // ROW_COUNT() is 0 when the values are unchanged, which the
            // procedure reports as "not found". Confirm before returning 404.
            if ($failure->getStatusCode() === 404 && $this->exists((int) $id)) {
                return response()->json([
                    'success' => true,
                    'enquiryId' => (int) $id,
                    'status' => $status,
                ]);
            }

            return $failure;
        }

        return response()->json([
            'success' => true,
            'enquiryId' => (int) $id,
            'status' => $status,
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $fromDate = trim((string) $request->query('fromDate', ''));
        $toDate = trim((string) $request->query('toDate', ''));

        foreach (['fromDate' => $fromDate, 'toDate' => $toDate] as $field => $value) {
            if ($value !== '' && !$this->isDate($value)) {
                return $this->validation($field, $field . ' must be a date in YYYY-MM-DD format');
            }
        }

        try {
            $rows = $this->callRows('CALL sp_enquiry_summary(?,?)', [
                $fromDate !== '' ? $fromDate : null,
                $toDate !== '' ? $toDate : null,
            ]);
        } catch (Throwable $e) {
            return $this->dbFailure($e);
        }

        $byType = [];
        foreach ($rows as $row) {
            $byType[(string) $row['enquiry_type']] = $row;
        }

        $data = [];
        foreach ((array) config('enquiries.types') as $type) {
            $row = $byType[$type] ?? null;

            $data[] = [
                'enquiryType' => $type,
                'total' => $row !== null ? (int) $row['total'] : 0,
                'new' => $row !== null ? (int) $row['new_count'] : 0,
                'contacted' => $row !== null ? (int) $row['contacted_count'] : 0,
                'inProgress' => $row !== null ? (int) $row['in_progress_count'] : 0,
                'closed' => $row !== null ? (int) $row['closed_count'] : 0,
                'spam' => $row !== null ? (int) $row['spam_count'] : 0,
            ];
        }

        return response()->json(['data' => $data]);
    }

    // --- Helpers ----------------------------------------------------------

    private function exists(int $id): bool
    {
        try {
            return $this->callRows('CALL sp_get_enquiry_by_id(?)', [$id]) !== [];
        } catch (Throwable $e) {
            return false;
        }
    }

    private function intQuery(Request $request, string $key, int $default): ?int
    {
        $value = $request->query($key);

        if ($value === null || $value === '') {
            return $default;
        }

        if (!is_scalar($value) || !preg_match('/^-?\d+$/', trim((string) $value))) {
            return null;
        }

        return (int) $value;
    }

    private function isDate(string $value): bool
    {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m)) {
            return false;
        }

        return checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
    }
}
