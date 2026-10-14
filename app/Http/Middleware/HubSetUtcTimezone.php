<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Ensures the Games Hub MySQL session runs in UTC, so NOW()/DATETIME
 * comparisons and the exported data (stored as UTC) line up.
 */
class HubSetUtcTimezone
{
    public function handle($request, Closure $next)
    {
        try {
            DB::connection('hub')->statement("SET time_zone = '+00:00'");
        } catch (Throwable $e) {
            // Connection may not be configured yet (e.g. during setup); the
            // actual query will surface a clearer error.
        }

        return $next($request);
    }
}
