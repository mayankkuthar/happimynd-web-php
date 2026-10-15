<?php

namespace App\Http\Controllers\Invoice;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Invoice Platform dashboard stats (API_SPEC §2, endpoint 4).
 *
 * Counts only active (current spreadsheet) records. The invoice-ledger figures
 * come from the shared ledgerStats() used by /invoices/stats too. The system
 * health booleans are derived from data we can observe; the rest live on the
 * intern side (sheet connection, job worker).
 */
class DashboardController extends InvoiceApiController
{
    public function stats(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        $database = 'ok';
        try {
            $this->db()->select('SELECT 1');
        } catch (Throwable $e) {
            $database = 'unreachable';
        }

        $lastSynced = $this->db()->selectOne('SELECT MAX(`startedAt`) AS lastSyncedAt FROM `SyncRun`');

        return $this->ok(array_merge([
            'totalClients' => (int) $this->scalar("SELECT COUNT(*) FROM `Client` WHERE `active` = 1"),
            'totalSessions' => (int) $this->scalar("SELECT COUNT(*) FROM `Session` WHERE `active` = 1"),
            'totalRevenue' => $this->scalar(
                'SELECT COALESCE(SUM(p.`paymentAmount`), 0) FROM `Payment` p '
                . 'JOIN `Session` s ON s.`id` = p.`sessionId` AND s.`active` = 1'
            ),

            'pendingVoice' => (int) $this->scalar("SELECT COUNT(*) FROM `VoiceProcessing` WHERE `status` = 'PENDING'"),
            'pendingData' => (int) $this->scalar("SELECT COUNT(*) FROM `DataProcessing` WHERE `status` = 'PENDING'"),
            'processing' => (int) $this->scalar(
                "SELECT (SELECT COUNT(*) FROM `VoiceProcessing` WHERE `status` = 'PROCESSING')"
                . " + (SELECT COUNT(*) FROM `DataProcessing` WHERE `status` = 'PROCESSING')"
            ),
            'failed' => (int) $this->scalar(
                "SELECT (SELECT COUNT(*) FROM `VoiceProcessing` WHERE `status` = 'FAILED')"
                . " + (SELECT COUNT(*) FROM `DataProcessing` WHERE `status` = 'FAILED')"
            ),

            'spreadsheetConnected' => $this->scalar('SELECT COUNT(*) FROM `SyncRun`') > 0,
            'databaseConnected' => $database === 'ok',
            'workerRunning' => $this->scalar('SELECT COUNT(*) FROM `ProcessingJob`') > 0,

            'lastSyncedAt' => isset($lastSynced->lastSyncedAt) ? $this->iso($lastSynced->lastSyncedAt) : null,
        ], $this->ledgerStats()));
    }
}
