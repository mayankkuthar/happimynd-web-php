<?php

use App\Http\Controllers\Hub\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Games Hub auth
|--------------------------------------------------------------------------
|
| Loaded twice by RouteServiceProvider (API_SPEC §1):
|   /api/auth/...  (primary)
|   /auth/...      (alias)
|
| The old SSO endpoints (sso/create, sso/exchange) were removed: the games
| now run inside the hub (MIGRATION_GUIDE §6.5).
|
*/

Route::post('signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login']);
Route::get('me', [AuthController::class, 'me']);
Route::post('logout', [AuthController::class, 'logout']);
