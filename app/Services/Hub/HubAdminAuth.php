<?php

namespace App\Services\Hub;

use App\Models\SsoAdminNumber;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

/**
 * Single-admin session handling for the Games Hub dashboard (API_SPEC §5).
 *
 * The session cookie `hm_admin` is a self-signed value:
 *   value     = base64url(payload) . "." . base64url(HMAC-SHA256(secret, payload))
 *   payload   = {"m":"<admin mobile>","x":<expiry ms epoch>}
 */
class HubAdminAuth
{
    public function configured(): bool
    {
        // Admins now sign in with HappiMynd SSO (phone list in sso_admin_numbers),
        // so only the cookie-signing secret is needed.
        return !empty(config('hub.admin.session_secret'));
    }

    /**
     * Verify a password against the configured PBKDF2-HMAC-SHA256 hash:
     *   pbkdf2:<iterations>:<saltHex>:<hashHex>
     */
    public function verifyPassword(string $password): bool
    {
        $stored = (string) config('hub.admin.password_hash');
        if ($stored === '') {
            return false;
        }

        $parts = explode(':', $stored);
        if (count($parts) !== 4 || $parts[0] !== 'pbkdf2') {
            return false;
        }

        [, $iterations, $saltHex, $hashHex] = $parts;
        $salt = @hex2bin($saltHex);
        $expected = @hex2bin($hashHex);

        if ($salt === false || $expected === false || $iterations === '') {
            return false;
        }

        $derived = hash_pbkdf2('sha256', $password, $salt, (int) $iterations, strlen($expected), true);

        return hash_equals($expected, $derived);
    }

    public function isValid(Request $request): bool
    {
        if (!$this->configured()) {
            return false;
        }

        $raw = $this->rawSession($request);
        if (!is_string($raw) || strpos($raw, '.') === false) {
            return false;
        }

        [$payload, $signature] = explode('.', $raw, 2);

        if (!hash_equals($this->sign($payload), $signature)) {
            return false;
        }

        $decoded = json_decode($this->base64UrlDecode($payload) ?: '', true);
        if (!is_array($decoded)) {
            return false;
        }

        $mobile = isset($decoded['m']) ? (string) $decoded['m'] : '';
        $expiry = isset($decoded['x']) ? (int) $decoded['x'] : 0;
        if ($mobile === '' || $expiry <= $this->nowMs()) {
            return false;
        }

        // Taking a number off the list (or switching it off) ends its session.
        $admin = SsoAdminNumber::findActiveFor($mobile);

        return $admin !== null && $admin->hub_admin;
    }

    /** The admin's name from a valid cookie, for the dashboard header. */
    public function adminName(Request $request): ?string
    {
        if (!$this->isValid($request)) {
            return null;
        }
        [$payload] = explode('.', (string) $this->rawSession($request), 2);
        $decoded = json_decode($this->base64UrlDecode($payload) ?: '', true);
        $admin = SsoAdminNumber::findActiveFor((string) ($decoded['m'] ?? ''));

        return $admin ? $admin->name : null;
    }

    /**
     * The session as sent: the hm_admin cookie (same-site) or an
     * "Authorization: Bearer" header (the dashboard on happipause.happimynd.com,
     * since the API answers CORS with "*" and no credentials).
     */
    private function rawSession(Request $request): ?string
    {
        $cookie = $request->cookie(config('hub.admin.cookie'));

        return is_string($cookie) && $cookie !== '' ? $cookie : $request->bearerToken();
    }

    /** Signed session value, used both as the cookie and as the bearer token. */
    public function makeToken(string $mobile): string
    {
        $payload = json_encode([
            'm' => SsoAdminNumber::normalize($mobile),
            'x' => $this->nowMs() + ((int) config('hub.admin.cookie_minutes')) * 60 * 1000,
        ], JSON_UNESCAPED_SLASHES);

        $payloadB64 = $this->base64UrlEncode($payload);

        return $payloadB64 . '.' . $this->sign($payloadB64);
    }

    public function makeCookie(Request $request, string $mobile)
    {
        $value = $this->makeToken($mobile);

        return Cookie::make(
            config('hub.admin.cookie'),
            $value,
            (int) config('hub.admin.cookie_minutes'),
            config('hub.admin.cookie_path'),
            null,
            $this->cookieSecure($request),
            true,
            false,
            'strict'
        );
    }

    public function expiredCookie(Request $request)
    {
        return Cookie::make(
            config('hub.admin.cookie'),
            '',
            0,
            config('hub.admin.cookie_path'),
            null,
            $this->cookieSecure($request),
            true,
            false,
            'strict'
        );
    }

    // ── Lockout ──────────────────────────────────────────────────────────

    public function ip(Request $request): string
    {
        $cf = $request->header('cf-connecting-ip');
        if (!empty($cf)) {
            return trim(explode(',', $cf)[0]);
        }

        $xff = $request->header('x-forwarded-for');
        if (!empty($xff)) {
            return trim(explode(',', $xff)[0]);
        }

        return (string) $request->ip();
    }

    public function lockMinutesRemaining(string $ip): int
    {
        $until = (int) Cache::get($this->lockKey($ip), 0);
        if ($until <= time()) {
            return 0;
        }

        return (int) ceil(($until - time()) / 60);
    }

    public function registerFailure(string $ip): void
    {
        $minutes = (int) config('hub.admin.lockout_minutes');
        $key = $this->failKey($ip);
        $count = ((int) Cache::get($key, 0)) + 1;

        Cache::put($key, $count, now()->addMinutes($minutes));

        if ($count >= (int) config('hub.admin.lockout_attempts')) {
            Cache::put($this->lockKey($ip), time() + $minutes * 60, now()->addMinutes($minutes));
            Cache::forget($key);
        }
    }

    public function clearFailures(string $ip): void
    {
        Cache::forget($this->failKey($ip));
        Cache::forget($this->lockKey($ip));
    }

    // ── Internals ────────────────────────────────────────────────────────

    private function cookieSecure(Request $request): bool
    {
        return (bool) config('hub.admin.cookie_secure', true) && $request->isSecure();
    }

    private function sign(string $payload): string
    {
        return $this->base64UrlEncode(
            hash_hmac('sha256', $payload, (string) config('hub.admin.session_secret'), true)
        );
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $value = strtr($value, '-_', '+/');
        $pad = strlen($value) % 4;
        if ($pad) {
            $value .= str_repeat('=', 4 - $pad);
        }

        return (string) base64_decode($value, true);
    }

    private function nowMs(): int
    {
        return (int) floor(microtime(true) * 1000);
    }

    private function failKey(string $ip): string
    {
        return 'hub:admin:fail:' . $ip;
    }

    private function lockKey(string $ip): string
    {
        return 'hub:admin:lock:' . $ip;
    }
}
