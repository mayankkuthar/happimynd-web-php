<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Applies the MySQL session settings the ported HR Auto queries need, without
 * changing the server configuration (MIGRATION_GUIDE §8.1):
 *
 *   - transaction_isolation = READ-COMMITTED  (the FOR UPDATE SKIP LOCKED queue)
 *   - group_concat_max_len  = 1048576         (records_candidates view)
 *   - time_zone             = '+00:00'        (all timestamps are UTC)
 *
 * The "hr" connection already declares timezone '+00:00'; the other two are set
 * here per request so no mysqld.cnf edit or restart is required.
 */
class HrSetSessionSettings
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $connection = DB::connection('hr');
            $connection->statement('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $connection->statement('SET SESSION group_concat_max_len = 1048576');
            $connection->statement("SET SESSION time_zone = '+00:00'");
        } catch (Throwable $e) {
            // A DB outage must not turn into a middleware-level 500; the
            // controller (e.g. /health) decides how to report it.
        }

        return $next($request);
    }
}
