<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AddJWTTokenToHeaderMiddleware
{
    /**
     * if request has jwt token in cookie this middleware adds that cookie in header and based on route/token define guard name to be used throughout execution.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');
        if (is_null($header)) {
            if (Cookie::has('admin-access-token') && Cookie::has('user-access-token')) {
                if (request()->is('admin/*')) {
                    $request->headers->set('Authorization', 'Bearer ' . Cookie::get('admin-access-token'));
                    auth()->shouldUse('admin');
                } else {
                    $request->headers->set('Authorization', 'Bearer ' . Cookie::get('user-access-token'));
                    auth()->shouldUse('user');
                }
            } else {
                $token = Cookie::get('admin-access-token');
                if (Cookie::has('admin-access-token') && $token = Cookie::get('admin-access-token')) {
                    $request->headers->set('Authorization', 'Bearer ' . $token);
                    auth()->shouldUse('admin');
                } elseif (Cookie::has('user-access-token') && $token = Cookie::get('user-access-token')) {
                    $request->headers->set('Authorization', 'Bearer ' . $token);
                    auth()->shouldUse('user');
                }
            }
        }
        return $next($request);
    }
}
