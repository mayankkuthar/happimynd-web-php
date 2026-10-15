<?php

namespace App\Http\Middleware;

use App\Services\Hr\HrSession;
use Closure;
use Illuminate\Http\Request;

/**
 * Gate for every HR Auto route except /health, /health/ready, /login, /logout
 * and /session (API_SPEC §2). Returns 401 { "detail": "Not authenticated" }.
 */
class HrAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        $session = app(HrSession::class);
        $cookie = $request->cookie($session->cookieName());

        if (!$session->verify(is_string($cookie) ? $cookie : null)) {
            return response()->json(['detail' => 'Not authenticated'], 401);
        }

        return $next($request);
    }
}
