<?php

namespace App\Http\Middleware;

use Closure;

class HubNoStore
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-store');

        return $response;
    }
}
