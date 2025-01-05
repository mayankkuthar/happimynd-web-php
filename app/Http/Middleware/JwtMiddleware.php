<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use JWTAuth;
use Exception;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    public $tokenName;

    public $redirectTo;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard)
    {
        $header = $request->header('Authorization');
        if (!is_null($header)) {
            try {
                // $this->user = auth($guard)->user();
                $this->tokenName = $guard . '-access-token';
                if (request()->is('admin/*')) {
                    $this->redirectTo = route('admin.getLogin');
                } else {
                    $this->redirectTo = route('user.loginView');
                }
                $user = JWTAuth::parseToken()->authenticate();
                //if authenticated user(not admin) trying to access admin access routes then redirect to user login.
                if (!$user || (request()->is('admin/*') && !$user->hasAccessToAdminPanel())) {
                    $this->tokenName = 'admin-access-token';
                    return redirect(route('user.loginView'));
                }

                //if authenticated user(admin) is trying to access user access route redirect to landing page
                if (!$user || !request()->is('admin/*') && $user->hasAccessToAdminPanel()) {
                    $this->tokenName = 'user-access-token';
                    // return redirect(route('landingPage'));
                    return redirect('login');
                }
            } catch (Exception $e) {
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                    if (Cookie::has($this->tokenName)) {
                        Cookie::queue(Cookie::forget($this->tokenName));
                    }
                    return redirect($this->redirectTo);
                } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                    $request->session()->invalidate();
                    if (Cookie::has($this->tokenName)) {
                        Cookie::queue(Cookie::forget($this->tokenName));
                    }
                    $request->session()->regenerateToken();
                    return redirect($this->redirectTo);
                } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException) {
                    $request->session()->invalidate();
                    if (Cookie::has($this->tokenName)) {
                        Cookie::queue(Cookie::forget($this->tokenName));
                    }
                    $request->session()->regenerateToken();
                    return redirect($this->redirectTo);
                } else {
                    //Authorization token not found or other exceptions
                    return redirect()->route($this->redirectTo);
                }
            }
        } else {
            if (request()->is('admin/*')) {
                return redirect()->route('admin.getLogin');
            } else {
                return redirect()->route('user.loginView');
            }
        }
        return $next($request);
    }
}
