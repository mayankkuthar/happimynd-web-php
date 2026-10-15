<?php

namespace App\Services\Hr;

/**
 * Signed admin session cookie for the HR Auto API (API_SPEC §2).
 *
 * Cookie value: "<expiry ms epoch>.<base64url(HMAC-SHA256(secret, "<expiry>"))>".
 * Valid only when the signature verifies and the expiry is in the future.
 * No user table: the single admin account comes from config.
 */
class HrSession
{
    /** Random secret used when HR_SESSION_SECRET is unset (per boot). */
    private static ?string $fallbackSecret = null;

    public function cookieName(): string
    {
        return (string) config('hr.admin.cookie', 'hr_session');
    }

    public function ttlSeconds(): int
    {
        return max(1, (int) config('hr.admin.session_ttl_hours', 12)) * 3600;
    }

    /**
     * Issue a fresh cookie value. Returns [value, minutes].
     *
     * @return array{0: string, 1: int}
     */
    public function issue(): array
    {
        $expiryMs = (int) floor(microtime(true) * 1000) + $this->ttlSeconds() * 1000;
        $expiry = (string) $expiryMs;
        $value = $expiry . '.' . $this->sign($expiry);

        return [$value, (int) ceil($this->ttlSeconds() / 60)];
    }

    public function verify(?string $cookie): bool
    {
        if (!is_string($cookie) || $cookie === '') {
            return false;
        }

        $parts = explode('.', $cookie, 2);
        if (count($parts) !== 2 || !ctype_digit($parts[0])) {
            return false;
        }

        [$expiry, $signature] = $parts;

        if ((int) $expiry < (int) floor(microtime(true) * 1000)) {
            return false;
        }

        return hash_equals($this->sign($expiry), $signature);
    }

    private function sign(string $expiry): string
    {
        return $this->base64url(hash_hmac('sha256', $expiry, $this->secret(), true));
    }

    private function secret(): string
    {
        $secret = (string) config('hr.admin.session_secret', '');

        if ($secret !== '') {
            return $secret;
        }

        if (self::$fallbackSecret === null) {
            self::$fallbackSecret = bin2hex(random_bytes(32));
        }

        return self::$fallbackSecret;
    }

    private function base64url(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
