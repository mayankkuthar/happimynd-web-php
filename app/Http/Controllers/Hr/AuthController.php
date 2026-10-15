<?php

namespace App\Http\Controllers\Hr;

use App\Services\Hr\HrSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * HR Auto sign-in (API_SPEC §2). Single admin account from env vars; no user
 * table. Sets a signed `hr_session` cookie.
 */
class AuthController extends HrController
{
    public function login(Request $request, HrSession $session): JsonResponse
    {
        $body = $this->jsonBody($request);

        $username = $body['username'] ?? null;
        $password = $body['password'] ?? null;

        if (!is_string($username) || !is_string($password) || $username === '' || $password === '') {
            return $this->missing($username === null ? 'username' : 'password', 'body');
        }

        $expectedUser = (string) config('hr.admin.username', '');
        $expectedPass = (string) config('hr.admin.password', '');

        // Always run both comparisons (constant time), regardless of outcome.
        $userOk = hash_equals($expectedUser, $username);
        $passOk = $expectedPass !== '' && hash_equals($expectedPass, $password);

        if (!$userOk || !$passOk) {
            return $this->detail('Incorrect username or password', 401);
        }

        [$value, $minutes] = $session->issue();
        $secure = (bool) config('hr.admin.cookie_secure', true);
        $sameSite = strtolower((string) config('hr.admin.cookie_samesite', 'Lax'));
        if ($sameSite === 'none') {
            $secure = true;
        }

        return response()
            ->json(['authenticated' => true], 200)
            ->cookie(
                $session->cookieName(),
                $value,
                $minutes,
                (string) config('hr.admin.cookie_path', '/api/hr'),
                null,
                $secure,
                true,
                false,
                $sameSite
            );
    }

    public function logout(HrSession $session): JsonResponse
    {
        $secure = (bool) config('hr.admin.cookie_secure', true);

        return response()
            ->json(['authenticated' => false], 200)
            ->cookie(
                $session->cookieName(),
                '',
                0,
                (string) config('hr.admin.cookie_path', '/api/hr'),
                null,
                $secure,
                true,
                false,
                strtolower((string) config('hr.admin.cookie_samesite', 'Lax'))
            );
    }

    public function session(Request $request, HrSession $session): JsonResponse
    {
        $cookie = $request->cookie($session->cookieName());

        return response()->json([
            'authenticated' => $session->verify(is_string($cookie) ? $cookie : null),
        ], 200);
    }
}
