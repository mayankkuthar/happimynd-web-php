<?php

use App\Http\Controllers\Hub\AdminController;
use App\Http\Controllers\Hub\ContentController;
use App\Http\Controllers\Hub\HealthController;
use App\Http\Controllers\Hub\ProgressController;
use App\Http\Controllers\Hub\TrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HappiMynd Games Hub API (intern project)
|--------------------------------------------------------------------------
|
| Served under /api (see RouteServiceProvider). Contract: API_SPEC.md.
| The auth/SSO endpoints live in routes/hub_auth.php so they can also be
| exposed at the /auth/* alias.
|
*/

// Health check
Route::get('health', [HealthController::class, 'health']);

// Usage tracking (text/plain bodies; 204 on success)
Route::prefix('track')->group(function () {
    Route::post('event', [TrackingController::class, 'event']);
    Route::post('session/start', [TrackingController::class, 'sessionStart']);
    Route::post('session/end', [TrackingController::class, 'sessionEnd']);
});

// Public content
Route::get('catalog', [ContentController::class, 'catalog']);
Route::get('content/{slug}', [ContentController::class, 'content']);

// Saved game progress (text/plain JSON body; 204 on save)
Route::get('progress/{slug}', [ProgressController::class, 'show']);
Route::post('progress/{slug}', [ProgressController::class, 'save']);

// Admin dashboard
Route::prefix('admin')->middleware('hub.admin.nostore')->group(function () {
    Route::post('login', [AdminController::class, 'login']);
    Route::post('logout', [AdminController::class, 'logout']);
    Route::get('session', [AdminController::class, 'session']);

    Route::middleware('hub.admin')->group(function () {
        Route::get('overview', [AdminController::class, 'overview']);
        Route::get('catalog', [AdminController::class, 'catalog']);
        Route::get('content', [AdminController::class, 'content']);
        Route::put('publish', [AdminController::class, 'publish']);
    });
});
