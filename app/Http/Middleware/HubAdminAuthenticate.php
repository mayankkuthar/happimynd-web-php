<?php

namespace App\Http\Middleware;

use App\Services\Hub\HubAdminAuth;
use Closure;

class HubAdminAuthenticate
{
    /** @var HubAdminAuth */
    private $auth;

    public function __construct(HubAdminAuth $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        if (!$this->auth->isValid($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Please sign in to the admin dashboard.',
                'error' => 'Please sign in to the admin dashboard.',
            ], 401);
        }

        return $next($request);
    }
}
