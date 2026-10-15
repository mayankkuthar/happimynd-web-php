<?php

namespace App\Http\Controllers\Invoice;

use App\Services\Invoice\Jwt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Invoice Platform authentication (API_SPEC §1 Auth).
 *
 * POST /auth/login verifies email+password against the User table (active
 * users only) and returns an HS256 JWT. GET /auth/me returns the caller.
 */
class AuthController extends InvoiceApiController
{
    public function login(Request $request): JsonResponse
    {
        $body = $this->jsonBody($request);

        $email = isset($body['email']) ? trim((string) $body['email']) : '';
        $password = isset($body['password']) ? (string) $body['password'] : '';

        $details = [];
        if ($email === '') {
            $details['email'] = 'Required';
        }
        if ($password === '') {
            $details['password'] = 'Required';
        }
        if ($details !== []) {
            return $this->error('Invalid request', 400, $details);
        }

        $user = $this->db()->table('User')
            ->where('email', $email)
            ->where('active', 1)
            ->first(['id', 'email', 'name', 'role', 'passwordHash']);

        if ($user === null || !password_verify($password, (string) $user->passwordHash)) {
            return $this->error('Invalid credentials', 401);
        }

        $secret = (string) config('invoice.auth.jwt_secret', '');
        if ($secret === '') {
            return $this->error('Server auth is not configured', 500);
        }

        $now = time();
        $ttl = max(1, (int) config('invoice.auth.jwt_ttl_minutes', 720)) * 60;

        $token = Jwt::encode([
            'sub' => (string) $user->id,
            'email' => (string) $user->email,
            'name' => (string) $user->name,
            'role' => (string) $user->role,
            'iss' => (string) config('invoice.auth.issuer', 'happimynd-invoice'),
            'iat' => $now,
            'exp' => $now + $ttl,
        ], $secret);

        return $this->ok([
            'token' => $token,
            'user' => [
                'id' => (string) $user->id,
                'email' => (string) $user->email,
                'name' => (string) $user->name,
                'role' => (string) $user->role,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $claims = $this->user($request);
        $id = (string) ($claims['sub'] ?? '');

        $user = $id === '' ? null : $this->db()->table('User')
            ->where('id', $id)
            ->first(['id', 'email', 'name', 'role']);

        if ($user === null) {
            return $this->error('User not found', 404);
        }

        return $this->ok([
            'id' => (string) $user->id,
            'email' => (string) $user->email,
            'name' => (string) $user->name,
            'role' => (string) $user->role,
        ]);
    }

    /** Read a JSON body regardless of Content-Type. */
    private function jsonBody(Request $request): array
    {
        $raw = $request->getContent();
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $request->all();
    }
}
