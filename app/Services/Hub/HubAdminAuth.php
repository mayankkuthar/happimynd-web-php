<?php

namespace App\Services\Hub;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

/**
 * Single-admin session handling for the Games Hub dashboard (API_SPEC §5).
 *
 * The session cookie `hm_admin` is a self-signed value:
 *   value     = base64url(payload) . "." . base64url(HMAC-SHA256(secret, payload))
 *   payload   = {"e":"<admin email>","x":<expiry ms epoch>}
 */
class HubAdminAuth
{
    public function configured(): bool
    {
        return !empty(config('hub.admin.email'))
            && !empty(config('hub.admin.password_hash'))
            && !empty(config('hub.admin.session_secret'));
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

        $raw = $request->cookie(config('hub.admin.cookie'));
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

        $email = isset($decoded['e']) ? (string) $decoded['e'] : '';
        $expiry = isset($decoded['x']) ? (int) $decoded['x'] : 0;

        return mb_strtolower(trim($email)) === mb_strtolower(trim((string) config('hub.admin.email')))
            && $expiry > $this->nowMs();
    }

    public function makeCookie(Request $request)
    {
        $payload = json_encode([
            'e' => (string) config('hub.admin.email'),
            'x' => $this->nowMs() + ((int) config('hub.admin.cookie_minutes')) * 60 * 1000,
        ], JSON_UNESCAPED_SLASHES);

        $payloadB64 = $this->base64UrlEncode($payload);
        $value = $payloadB64 . '.' . $this->sign($payloadB64);

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
