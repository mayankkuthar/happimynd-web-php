<?php

namespace App\Http\Middleware;

use App\Services\Invoice\Jwt;
use Closure;
use Illuminate\Http\Request;

/**
 * Bearer-token gate for the Invoice Platform API (API_SPEC §1). Verifies the
 * HS256 JWT and exposes the claims to the controller as the "invoice_user"
 * request attribute.
 */
class InvoiceAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        $header = (string) $request->header('Authorization', '');

        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }

        $secret = (string) config('invoice.auth.jwt_secret', '');

        if ($secret === '') {
            return response()->json(['error' => 'Server auth is not configured'], 500);
        }

        $claims = Jwt::decode(trim($matches[1]), $secret);

        if ($claims === null) {
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }

        $request->attributes->set('invoice_user', $claims);

        return $next($request);
    }
}
