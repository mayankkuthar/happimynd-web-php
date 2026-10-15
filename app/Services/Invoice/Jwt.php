<?php

namespace App\Services\Invoice;

/**
 * Minimal HS256 JWT encode/decode for the Invoice Platform API (API_SPEC §1).
 * No external dependency: the app already ships no JWT library.
 */
class Jwt
{
    public static function encode(array $payload, string $secret): string
    {
        $header = self::b64url(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $body = self::b64url(json_encode($payload));
        $signature = self::b64url(hash_hmac('sha256', $header . '.' . $body, $secret, true));

        return $header . '.' . $body . '.' . $signature;
    }

    /**
     * Decode and verify a token. Returns the claims, or null if invalid/expired.
     */
    public static function decode(string $token, string $secret): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $body, $signature] = $parts;

        $expected = self::b64url(hash_hmac('sha256', $header . '.' . $body, $secret, true));
        if (!hash_equals($expected, $signature)) {
            return null;
        }

        $decoded = base64_decode(strtr($body, '-_', '+/'), true);
        if ($decoded === false) {
            return null;
        }

        $claims = json_decode($decoded, true);
        if (!is_array($claims)) {
            return null;
        }

        if (isset($claims['exp']) && (int) $claims['exp'] < time()) {
            return null;
        }

        return $claims;
    }

    private static function b64url(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
