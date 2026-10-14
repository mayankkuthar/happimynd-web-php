<?php

use App\Http\Controllers\Hub\AuthController;
use App\Http\Controllers\Hub\SsoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Games Hub auth + SSO
|--------------------------------------------------------------------------
|
| Loaded twice by RouteServiceProvider (API_SPEC §1):
|   /api/auth/...  (primary)
|   /auth/...      (alias)
|
*/

Route::post('signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login']);
Route::get('me', [AuthController::class, 'me']);
Route::post('logout', [AuthController::class, 'logout']);

Route::post('sso/create', [SsoController::class, 'create']);
Route::post('sso/exchange', [SsoController::class, 'exchange']);
