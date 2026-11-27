<?php

use App\Http\Controllers\Hr\ApplicationController;
use App\Http\Controllers\Hr\AuthController;
use App\Http\Controllers\Hr\DepartmentController;
use App\Http\Controllers\Hr\ExportController;
use App\Http\Controllers\Hr\HealthController;
use App\Http\Controllers\Hr\PortalController;
use App\Http\Controllers\Hr\ResumeController;
use App\Http\Controllers\Hr\WorkerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HR Auto (Resume Extractor) API
|--------------------------------------------------------------------------
|
| Mounted by RouteServiceProvider at /api/hr with the `hr` middleware group
| (UTC + READ-COMMITTED session settings, own `hr` MySQL connection).
|
| Paths mirror the intern contract (intern_2/hr-automation/mysql-migration/
| API_SPEC.md) relative to the /api/hr base, so the front end's API base URL
| becomes https://<host>/api/hr.
|
| The whole contract is served here: /schema returns resources/hr/schema.json
| (the intern app's own output), /export builds the workbook in PHP
| (App\Services\Hr\ExcelExport, a port of excel.ts), and uploads / file
| previews go through App\Services\Hr\GcsStorage. Only /api/hr/worker/* is for
| the extraction worker (on Render). POST /records (JSON, file already in
| storage) is kept for tools that upload to GCS themselves.
|
*/

// --- Sign-in (no auth) -----------------------------------------------------
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/session', [AuthController::class, 'session']);

// --- Health (no auth) ------------------------------------------------------
Route::get('/health', [HealthController::class, 'health']);
Route::get('/health/ready', [HealthController::class, 'ready']);

// Field metadata (no auth, no database; API_SPEC §3).
Route::get('/schema', [HealthController::class, 'schema']);

// --- Candidate portal (public, no session; API_SPEC §7) --------------------
Route::get('/portal/config', [PortalController::class, 'config']);
Route::post('/portal/applications', [PortalController::class, 'store']);

// --- Authenticated API -----------------------------------------------------
Route::middleware('hr.auth')->group(function () {
    // Department picker (API_SPEC §6). No rename or delete.
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::post('/departments', [DepartmentController::class, 'store']);

    // Hiring dashboard (API_SPEC §8).
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::get('/applications/meta', [ApplicationController::class, 'meta']);
    Route::get('/applications/{id}', [ApplicationController::class, 'show'])->where('id', 'APP-[A-Za-z0-9]+');
    Route::get('/applications/{id}/resume', [ApplicationController::class, 'resume'])->where('id', 'APP-[A-Za-z0-9]+');
    Route::patch('/applications/{id}/status', [ApplicationController::class, 'status'])->where('id', 'APP-[A-Za-z0-9]+');
    Route::post('/applications/{id}/notes', [ApplicationController::class, 'notes'])->where('id', 'APP-[A-Za-z0-9]+');
    Route::post('/applications/{id}/email', [ApplicationController::class, 'email'])->where('id', 'APP-[A-Za-z0-9]+');

    // Resumes / records (the router is mounted twice in the contract; keep both).
    Route::get('/resumes', [ResumeController::class, 'index']);
    Route::delete('/resumes', [ResumeController::class, 'clear']);
    Route::get('/resumes/{id}', [ResumeController::class, 'show'])->where('id', '[A-Za-z0-9]+');
    Route::patch('/resumes/{id}', [ResumeController::class, 'update'])->where('id', '[A-Za-z0-9]+');
    Route::delete('/resumes/{id}', [ResumeController::class, 'destroy'])->where('id', '[A-Za-z0-9]+');
    Route::patch('/resumes/{id}/viewed', [ResumeController::class, 'viewed'])->where('id', '[A-Za-z0-9]+');
    Route::post('/resumes/{id}/reprocess', [ResumeController::class, 'reprocess'])->where('id', '[A-Za-z0-9]+');

    // Bulk upload (multipart) and the original file for preview (API_SPEC §4).
    // Route::get also answers HEAD.
    Route::post('/resumes/upload', [ResumeController::class, 'upload']);
    Route::post('/upload', [ResumeController::class, 'upload']);
    Route::get('/resumes/{id}/file', [ResumeController::class, 'file'])->where('id', '[A-Za-z0-9]+');
    Route::get('/records/{id}/file', [ResumeController::class, 'file'])->where('id', '[A-Za-z0-9]+');

    Route::get('/records', [ResumeController::class, 'index']);
    Route::post('/records', [ResumeController::class, 'store']);
    Route::delete('/records', [ResumeController::class, 'clear']);
    Route::get('/records/{id}', [ResumeController::class, 'show'])->where('id', '[A-Za-z0-9]+');
    Route::patch('/records/{id}', [ResumeController::class, 'update'])->where('id', '[A-Za-z0-9]+');
    Route::delete('/records/{id}', [ResumeController::class, 'destroy'])->where('id', '[A-Za-z0-9]+');
    Route::patch('/records/{id}/viewed', [ResumeController::class, 'viewed'])->where('id', '[A-Za-z0-9]+');
    Route::post('/records/{id}/reprocess', [ResumeController::class, 'reprocess'])->where('id', '[A-Za-z0-9]+');

    // Extraction log.
    Route::get('/logs', [ResumeController::class, 'logs']);

    // Excel export of one source's completed records (API_SPEC §5).
    Route::post('/export', [ExportController::class, 'export']);
});

// --- Worker / queue (server-to-server) -------------------------------------
// Auth is enforced inside WorkerController::workerDenied(): a valid admin
// session cookie OR the shared X-Worker-Token header (env HR_WORKER_TOKEN).
Route::prefix('worker')->group(function () {
    Route::post('/claim', [WorkerController::class, 'claim']);
    Route::post('/reap', [WorkerController::class, 'reap']);
    Route::get('/depth', [WorkerController::class, 'depth']);
    Route::post('/{id}/done', [WorkerController::class, 'done'])->where('id', '[A-Za-z0-9]+');
    Route::post('/{id}/fail', [WorkerController::class, 'fail'])->where('id', '[A-Za-z0-9]+');
});
