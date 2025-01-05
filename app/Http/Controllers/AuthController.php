<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Services\ApiResponseService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $apiService;
    protected $tokenName;

    public function __construct(ApiResponseService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * valdates admin panel login
     *
     * @param AdminLoginRequest $request
     */
    public function adminLogin(AdminLoginRequest $request)
    {
        $formData = $request->validated();

        $credentials = ['email' => $formData['email'], 'password' => $formData['password']];
        if (!$token = auth('admin')->attempt($credentials)) {
            return redirect()->back()->with('invalid', 'Invalid credentials');
        }
        if (!auth('admin')->user()->isActive()) {
            // $this->logout($request, 'admin');
            return redirect(route('admin.getLogin'))->with('invalid', 'Your account is blocked');
        }
        $cookie = Cookie::make('admin-access-token', $token);
        Cookie::queue($cookie);
        return redirect(route('admin.dashboard'));
    }

    /**
     * logout code for user/admin using respictive guard
     *
     * @param Request $request
     * @param string $guard
     * @return void
     */
    public function logout(Request $request, $guard = 'user')
    {
        try {
            Auth::guard($guard)->logout();
            $request->session()->invalidate();
            if (Cookie::has($this->tokenName)) {
                Cookie::queue(Cookie::forget($this->tokenName));
            }
            $request->session()->regenerateToken();
        } catch (Exception $exception) {
            Log::error($exception);
            return false;
        }

        return $this->apiService->success('logged out');
    }

    /**
     * perform logout for user
     *
     * @param Request $request
     * @return void
     */
    public function userLogout(Request $request)
    {
        $this->tokenName = 'user-access-token';
        $this->logout($request, 'user');
        return redirect(route('user.loginView'));
    }

    /**
     * perform logout for admin user
     *
     * @param Request $request
     * @return void
     */
    public function adminLogout(Request $request)
    {
        $this->tokenName = 'admin-access-token';
        $this->logout($request, 'admin');
        return redirect()->route('admin.getLogin');
    }
}
