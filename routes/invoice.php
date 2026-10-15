<?php

use App\Http\Controllers\Invoice\AuditLogController;
use App\Http\Controllers\Invoice\AuthController;
use App\Http\Controllers\Invoice\ClientController;
use App\Http\Controllers\Invoice\DashboardController;
use App\Http\Controllers\Invoice\HealthController;
use App\Http\Controllers\Invoice\InvoicesController;
use App\Http\Controllers\Invoice\MetaController;
use App\Http\Controllers\Invoice\PaymentController;
use App\Http\Controllers\Invoice\ProcessingController;
use App\Http\Controllers\Invoice\RevenueController;
use App\Http\Controllers\Invoice\SessionController;
use App\Http\Controllers\Invoice\SyncController;
use App\Http\Controllers\Invoice\TemplateController;
use App\Http\Controllers\Invoice\VerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Invoice Platform API
|--------------------------------------------------------------------------
|
| Mounted by RouteServiceProvider at /api/invoice with the `invoice`
| middleware group (UTC session settings, own `invoice` MySQL connection).
|
| Paths mirror the intern contract
| (intern_2/Invoice automation platform/Invoice Project - API
| Specification.md) relative to the /api/invoice base, so the front end's
| API base URL becomes https://<host>/api/invoice.
|
| Roles (VIEWER < OPERATOR < ADMIN) are checked inside each controller via
| requireRole(), so no per-route role middleware is needed here.
|
| Endpoints that are NOT database work are intentionally omitted and stay on
| the intern's side: `/invoices/{id}/pdf` + `/invoices/{id}/send-email`
| (stored artifact + mail provider) and `/verification/upload` (Razorpay
| spreadsheet parsing).
|
*/

// --- Sign-in (no auth) -----------------------------------------------------
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/me', [AuthController::class, 'me']);

// --- Health (no auth) ------------------------------------------------------
Route::get('/health', [HealthController::class, 'health']);

// --- Authenticated API -----------------------------------------------------
Route::middleware('invoice.auth')->group(function () {
    // Dashboard.
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Clients / sessions / payments.
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/clients/{id}', [ClientController::class, 'show'])->where('id', '[A-Za-z0-9]+');

    Route::get('/sessions', [SessionController::class, 'index']);
    Route::get('/sessions/{id}', [SessionController::class, 'show'])->where('id', '[A-Za-z0-9]+');
    Route::get('/sessions/{id}/source-record', [SessionController::class, 'sourceRecord'])->where('id', '[A-Za-z0-9]+');

    Route::get('/payments', [PaymentController::class, 'index']);

    // Invoices (ledger stats + consolidated list).
    Route::get('/invoices/stats', [InvoicesController::class, 'stats']);
    Route::get('/invoices', [InvoicesController::class, 'index']);

    // Processing (voice + invoice jobs) and retries (OPERATOR in controller).
    Route::get('/processing/stats', [ProcessingController::class, 'stats']);
    Route::get('/processing', [ProcessingController::class, 'index']);
    Route::post('/voice/{id}/retry', [ProcessingController::class, 'voiceRetry'])->where('id', '[A-Za-z0-9]+');
    Route::post('/data/{id}/retry', [ProcessingController::class, 'dataRetry'])->where('id', '[A-Za-z0-9]+');

    // Sync (run is ADMIN in controller).
    Route::get('/sync/status', [SyncController::class, 'status']);
    Route::get('/sync/source-rows', [SyncController::class, 'sourceRows']);
    Route::post('/sync/run', [SyncController::class, 'run']);

    // Revenue and payment verification.
    Route::get('/revenue', [RevenueController::class, 'index']);
    Route::get('/verification/summary', [VerificationController::class, 'summary']);
    Route::get('/verification/issues', [VerificationController::class, 'issues']);

    // Audit, templates and filter options.
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
    Route::get('/templates', [TemplateController::class, 'index']);
    Route::get('/meta/filters', [MetaController::class, 'filters']);
});
