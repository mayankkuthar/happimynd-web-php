<?php

namespace App\Http\Controllers\Enquiries;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Shared plumbing for the Website Enquiries API.
 *
 * Every endpoint calls exactly one stored procedure on the "enquiries"
 * connection. Errors raised by the procedures (SQLSTATE 45000) become 400,
 * except "Enquiry not found" which becomes 404.
 */
abstract class EnquiriesController extends Controller
{
    protected function pdo(): \PDO
    {
        return DB::connection('enquiries')->getPdo();
    }

    protected function error(string $code, string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => ['code' => $code, 'message' => $message],
        ], $status);
    }

    protected function dbFailure(Throwable $e): JsonResponse
    {
        $message = $e->getMessage();
        $sqlState = (string) $e->getCode();

        if ($e instanceof \PDOException && isset($e->errorInfo[2])) {
            $message = (string) $e->errorInfo[2];
            $sqlState = (string) $e->errorInfo[0];
        }

        if ($sqlState === '45000') {
            if (strcasecmp(trim($message), 'Enquiry not found') === 0) {
                return $this->error('NOT_FOUND', 'Enquiry not found', 404);
            }

            return $this->error('VALIDATION_ERROR', $message, 400);
        }

        Log::error('Enquiries API database failure', ['message' => $message]);

        return $this->error('INTERNAL_ERROR', 'Internal server error', 500);
    }

    /** Run a CALL and return the first result set. */
    protected function callRows(string $sql, array $params): array
    {
        $stmt = $this->pdo()->prepare($sql);
        $this->bind($stmt, $params);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        try {
            while ($stmt->nextRowset()) {
                $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        } catch (Throwable $e) {
            // Some drivers report the trailing OK packet; ignore it.
        }

        $stmt->closeCursor();

        return $rows;
    }

    /** Run a CALL that writes an OUT parameter into @enquiry_id. */
    protected function callWithId(string $sql, array $params): int
    {
        $pdo = $this->pdo();
        $stmt = $pdo->prepare($sql);
        $this->bind($stmt, $params);
        $stmt->execute();
        $stmt->closeCursor();

        $value = $pdo->query('SELECT @enquiry_id')->fetchColumn();

        return (int) $value;
    }

    /** Run a CALL that returns no result set (e.g. status updates). */
    protected function callVoid(string $sql, array $params): void
    {
        $stmt = $this->pdo()->prepare($sql);
        $this->bind($stmt, $params);
        $stmt->execute();

        try {
            while ($stmt->nextRowset()) {
                // drain trailing OK packets
            }
        } catch (Throwable $e) {
            // ignore driver bookkeeping errors
        }

        $stmt->closeCursor();
    }

    protected function bind(\PDOStatement $stmt, array $params): void
    {
        foreach (array_values($params) as $index => $value) {
            if ($value === null) {
                $stmt->bindValue($index + 1, null, \PDO::PARAM_NULL);
            } elseif (is_int($value)) {
                $stmt->bindValue($index + 1, $value, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($index + 1, (string) $value, \PDO::PARAM_STR);
            }
        }
    }

    protected function iso(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $value, new \DateTimeZone('UTC'));

        if ($dt === false) {
            $dt = new \DateTime($value, new \DateTimeZone('UTC'));
        }

        return $dt->format('Y-m-d\TH:i:s\Z');
    }

    /** snake_case column → camelCase API object (API_SPEC "Application"/row shape). */
    protected function presentRow(array $row): array
    {
        return [
            'enquiryId' => (int) $row['enquiry_id'],
            'enquiryType' => (string) $row['enquiry_type'],
            'fullName' => (string) $row['full_name'],
            'email' => (string) $row['email'],
            'phone' => $row['phone'] !== null ? (string) $row['phone'] : null,
            'organization' => $row['organization'] !== null ? (string) $row['organization'] : null,
            'interest' => $row['interest'] !== null ? (string) $row['interest'] : null,
            'message' => $row['message'] !== null ? (string) $row['message'] : null,
            'pathwayId' => $row['pathway_id'] !== null ? (string) $row['pathway_id'] : null,
            'currentRole' => $row['current_role_title'] !== null ? (string) $row['current_role_title'] : null,
            'primaryGoal' => $row['primary_goal'] !== null ? (string) $row['primary_goal'] : null,
            'preferredTimeline' => $row['preferred_timeline'] !== null ? (string) $row['preferred_timeline'] : null,
            'engagementType' => $row['engagement_type'] !== null ? (string) $row['engagement_type'] : null,
            'audience' => $row['audience'] !== null ? (string) $row['audience'] : null,
            'proposedDate' => $row['proposed_date'] !== null ? (string) $row['proposed_date'] : null,
            'eventFormat' => $row['event_format'] !== null ? (string) $row['event_format'] : null,
            'notes' => $row['notes'] !== null ? (string) $row['notes'] : null,
            'status' => (string) $row['status'],
            'adminRemarks' => $row['admin_remarks'] !== null ? (string) $row['admin_remarks'] : null,
            'createdAt' => $this->iso($row['created_at']),
            'updatedAt' => $this->iso($row['updated_at']),
        ];
    }

    protected function body(Request $request): array
    {
        $raw = $request->getContent();

        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $request->all();
    }

    /** Trimmed string body value (non-scalars become ''). */
    protected function str(Request $request, string $key, array $body): string
    {
        $value = $body[$key] ?? '';

        return is_scalar($value) ? trim((string) $value) : '';
    }

    protected function validation(string $field, string $message): JsonResponse
    {
        return $this->error('VALIDATION_ERROR', $message, 400);
    }
}
