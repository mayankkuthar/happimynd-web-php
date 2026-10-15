<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Applies the MySQL session settings for the Invoice Platform connection.
 * Timestamps are UTC; the ported queries expect consistent reads.
 */
class InvoiceSetSessionSettings
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $connection = DB::connection('invoice');
            $connection->statement('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $connection->statement("SET SESSION time_zone = '+00:00'");
        } catch (Throwable $e) {
            // Health endpoints report DB availability themselves.
        }

        return $next($request);
    }
}
