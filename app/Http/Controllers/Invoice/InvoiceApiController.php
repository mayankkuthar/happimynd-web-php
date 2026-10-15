<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use DateTime;
use DateTimeZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Shared helpers for the Invoice Platform API (intern project).
 *
 * Contract: intern_2/Invoice automation platform/Invoice Project - API
 * Specification.md. Conventions: JSON in/out, `page`/`pageSize` paging
 * ({ data, page, pageSize, total, totalPages }), ISO 8601 UTC dates, money as
 * numbers in INR, errors { "error": "...", "details"?: {...} }.
 */
abstract class InvoiceApiController extends Controller
{
    protected function db()
    {
        return DB::connection('invoice');
    }

    /** The JWT claims attached by InvoiceAuthenticate. */
    protected function user(Request $request): array
    {
        $claims = $request->attributes->get('invoice_user');

        return is_array($claims) ? $claims : [];
    }

    /**
     * Enforce a minimum role. Returns a 403 response when the caller's role is
     * too low, or null when allowed. Roles: VIEWER < OPERATOR < ADMIN.
     */
    protected function requireRole(Request $request, string $minimum): ?JsonResponse
    {
        $roles = (array) config('invoice.roles', []);
        $current = (string) ($this->user($request)['role'] ?? '');
        $need = (int) ($roles[$minimum] ?? 0);
        $have = (int) ($roles[$current] ?? 0);

        if ($have < $need) {
            return $this->error('Requires ' . $minimum . ' role or higher', 403);
        }

        return null;
    }

    /** True when the caller has at least $minimum. */
    protected function hasRole(Request $request, string $minimum): bool
    {
        $roles = (array) config('invoice.roles', []);
        $current = (string) ($this->user($request)['role'] ?? '');

        return (int) ($roles[$current] ?? 0) >= (int) ($roles[$minimum] ?? 0);
    }

    protected function ok($data, int $status = 200): JsonResponse
    {
        return response()->json($data, $status);
    }

    protected function error(string $message, int $status, $details = null): JsonResponse
    {
        $body = ['error' => $message];
        if ($details !== null) {
            $body['details'] = $details;
        }

        return response()->json($body, $status);
    }

    /**
     * Resolve page/pageSize for a list endpoint.
     *
     * @return array{0:int,1:int,2:int} [page, pageSize, offset]
     */
    protected function paging(Request $request, int $defaultSize = null, int $maxSize = null): array
    {
        $defaultSize = $defaultSize ?? (int) config('invoice.page.default_size', 25);
        $maxSize = $maxSize ?? (int) config('invoice.page.max_size', 100);

        $page = max(1, (int) $request->query('page', 1));
        $pageSize = (int) $request->query('pageSize', $defaultSize);
        $pageSize = max(1, min($maxSize, $pageSize));
        $offset = ($page - 1) * $pageSize;

        return [$page, $pageSize, $offset];
    }

    /** Build the paged response envelope. */
    protected function paged(array $data, int $page, int $pageSize, int $total): array
    {
        return [
            'data' => $data,
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => $total,
            'totalPages' => $total > 0 ? (int) ceil($total / $pageSize) : 0,
        ];
    }

    /** Format a DATETIME(3) string as ISO 8601 UTC (…Z), null-safe. */
    protected function iso($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            $dt = new DateTime($value->format('Y-m-d H:i:s.u'), new DateTimeZone('UTC'));
        } else {
            $string = (string) $value;
            $dt = DateTime::createFromFormat('Y-m-d H:i:s.u', $string, new DateTimeZone('UTC'));
            if ($dt === false) {
                $dt = DateTime::createFromFormat('Y-m-d H:i:s', $string, new DateTimeZone('UTC'));
            }
            if ($dt === false) {
                try {
                    $dt = new DateTime($string, new DateTimeZone('UTC'));
                } catch (\Throwable $e) {
                    return null;
                }
            }
        }

        return $dt->format('Y-m-d\TH:i:s.v\Z');
    }

    /** DATETIME(3) value for right now, UTC. */
    protected function nowUtc(): string
    {
        return (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s.v');
    }

    protected function money($value): ?float
    {
        return $value === null ? null : (float) $value;
    }

    protected function asBool($value): bool
    {
        return ((int) $value) === 1 || $value === true;
    }

    /** Decode a JSON column (object or array); returns $default when invalid. */
    protected function json($value, $default = null)
    {
        if ($value === null || $value === '') {
            return $default;
        }

        $decoded = json_decode((string) $value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;
    }

    /** Insert an AuditLog row (API_SPEC write endpoints all do this). */
    protected function audit(string $action, string $entity, string $entityId, ?array $oldValue, ?array $newValue, ?string $userId): void
    {
        $this->db()->table('AuditLog')->insert([
            'id' => $this->cuid(),
            'actorType' => $userId !== null ? 'USER' : 'SYSTEM',
            'userId' => $userId,
            'action' => $action,
            'entity' => $entity,
            'entityId' => $entityId,
            'oldValue' => $oldValue === null ? null : json_encode($oldValue),
            'newValue' => $newValue === null ? null : json_encode($newValue),
            'timestamp' => $this->nowUtc(),
        ]);
    }

    /** A cuid-like id. Prisma normally generates these; our API writes the few it owns. */
    protected function cuid(): string
    {
        return 'c' . bin2hex(random_bytes(11));
    }

    /** Run a scalar query and return its numeric value. */
    protected function scalar(string $query): float
    {
        $row = $this->db()->selectOne($query);

        if ($row === null) {
            return 0.0;
        }

        $value = $row->value ?? reset($row);

        return (float) $value;
    }

    /**
     * The invoice ledger figures shared by the dashboard and /invoices/stats
     * (API_SPEC §6). totalAmount = verified + pending; it never includes
     * amounts that need review.
     */
    protected function ledgerStats(): array
    {
        $verifiedAmount = $this->scalar(
            'SELECT COALESCE(SUM(`paymentAmount`), 0) FROM `InvoiceTransaction` '
            . "WHERE `paymentVerified` = 1 AND `status` <> 'NEEDS_REVIEW'"
        );
        $pendingAmount = $this->scalar(
            'SELECT COALESCE(SUM(`paymentAmount`), 0) FROM `InvoiceTransaction` '
            . "WHERE `paymentVerified` = 0 AND `status` <> 'NEEDS_REVIEW'"
        );

        return [
            'totalRecordsSynced' => (int) $this->scalar('SELECT COUNT(*) FROM `SyncRecord`'),
            'paidTransactions' => (int) $this->scalar(
                'SELECT COUNT(*) FROM `InvoiceTransaction` WHERE `paymentVerified` = 1 AND `paymentsCount` > 0'
            ),
            'pendingPayments' => (int) $this->scalar(
                'SELECT COUNT(*) FROM `Payment` p JOIN `Session` s ON s.`id` = p.`sessionId` AND s.`active` = 1 '
                . 'WHERE p.`paymentVerified` = 0'
            ),
            'invoicesGenerated' => (int) $this->scalar(
                "SELECT COUNT(*) FROM `InvoiceTransaction` WHERE `status` = 'GENERATED'"
            ),
            'invoicesPendingGeneration' => (int) $this->scalar(
                "SELECT COUNT(*) FROM `InvoiceTransaction` WHERE `status` IN ('PENDING', 'PAYMENT_VERIFIED')"
            ),
            'duplicateTransactionsDetected' => (int) $this->scalar(
                'SELECT COUNT(*) FROM `InvoiceTransaction` WHERE `occurrenceCount` > 1'
            ),
            'failedInvoiceGenerations' => (int) $this->scalar(
                "SELECT COUNT(*) FROM `InvoiceTransaction` WHERE `status` = 'FAILED'"
            ),
            'needsReview' => (int) $this->scalar(
                "SELECT COUNT(*) FROM `InvoiceTransaction` WHERE `status` = 'NEEDS_REVIEW'"
            ),
            'totalAmount' => $verifiedAmount + $pendingAmount,
            'verifiedAmount' => $verifiedAmount,
            'pendingAmount' => $pendingAmount,
            'requiresVerificationAmount' => $this->scalar(
                "SELECT COALESCE(SUM(`paymentAmount`), 0) FROM `InvoiceTransaction` WHERE `status` = 'NEEDS_REVIEW'"
            ),
            'confirmedSessionsCount' => (int) $this->scalar(
                'SELECT COUNT(DISTINCT p.`sessionId`) FROM `Payment` p '
                . 'JOIN `Session` s ON s.`id` = p.`sessionId` AND s.`active` = 1 '
                . 'WHERE p.`sessionId` IS NOT NULL'
            ),
            'duplicateOrInvalidCount' => (int) $this->scalar(
                'SELECT COUNT(*) FROM `InvoiceTransaction` WHERE JSON_LENGTH(`flags`) > 0'
            ),
        ];
    }
}
