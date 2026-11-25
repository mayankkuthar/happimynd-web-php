<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Admin gate for the enquiries API: `Authorization: Bearer <ENQUIRIES_ADMIN_TOKEN>`.
 * The token is compared in constant time; an unset token denies everything.
 */
class EnquiriesAdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        $expected = (string) config('enquiries.admin_token', '');
        $header = (string) $request->header('Authorization', '');

        $provided = '';
        if (stripos($header, 'Bearer ') === 0) {
            $provided = trim(substr($header, 7));
        }

        if ($expected === '' || $provided === '' || !hash_equals($expected, $provided)) {
            return response()->json([
                'success' => false,
                'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Unauthorized'],
            ], 401);
        }

        return $next($request);
    }
}
