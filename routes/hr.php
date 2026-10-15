<?php

use App\Http\Controllers\Hr\AuthController;
use App\Http\Controllers\Hr\HealthController;
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
| Endpoints that are NOT database work are intentionally omitted and stay on
| the intern's side: `/schema` (needs schema.ts), `/export` (xlsx), the
| multipart `/resumes/upload` + `/upload` and `/resumes/{id}/file` (GCS).
| The database half of upload is exposed as POST /records — see
| ResumeController::store().
|
*/

// --- Sign-in (no auth) -----------------------------------------------------
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/session', [AuthController::class, 'session']);

// --- Health (no auth) ------------------------------------------------------
Route::get('/health', [HealthController::class, 'health']);
Route::get('/health/ready', [HealthController::class, 'ready']);

// --- Authenticated API -----------------------------------------------------
Route::middleware('hr.auth')->group(function () {
    // Resumes / records (the router is mounted twice in the contract; keep both).
    Route::get('/resumes', [ResumeController::class, 'index']);
    Route::delete('/resumes', [ResumeController::class, 'clear']);
    Route::get('/resumes/{id}', [ResumeController::class, 'show'])->where('id', '[A-Za-z0-9]+');
    Route::patch('/resumes/{id}', [ResumeController::class, 'update'])->where('id', '[A-Za-z0-9]+');
    Route::delete('/resumes/{id}', [ResumeController::class, 'destroy'])->where('id', '[A-Za-z0-9]+');
    Route::patch('/resumes/{id}/viewed', [ResumeController::class, 'viewed'])->where('id', '[A-Za-z0-9]+');
    Route::post('/resumes/{id}/reprocess', [ResumeController::class, 'reprocess'])->where('id', '[A-Za-z0-9]+');

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
