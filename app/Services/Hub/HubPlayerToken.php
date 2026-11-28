<?php

namespace App\Services\Hub;

/**
 * Signed player token for the Games Hub, issued after HappiMynd SSO.
 *
 *   token   = base64url(payload) . "." . base64url(HMAC-SHA256(secret, payload))
 *   payload = {"u":"<hub user uuid>","x":<expiry, seconds epoch>}
 *
 * Replaces the old model where the API trusted any userId it was sent.
 */
class HubPlayerToken
{
    public function issue(string $userId): string
    {
        $payload = $this->b64(json_encode([
            'u' => $userId,
            'x' => time() + ((int) config('sso.hub_player_token_days', 30)) * 86400,
        ], JSON_UNESCAPED_SLASHES));

        return $payload . '.' . $this->sign($payload);
    }

    /** The hub user id inside a valid, unexpired token, else null. */
    public function userId(?string $token): ?string
    {
        if (!is_string($token) || substr_count($token, '.') !== 1 || strlen($token) > 512) {
            return null;
        }

        [$payload, $signature] = explode('.', $token, 2);
        if (!hash_equals($this->sign($payload), $signature)) {
            return null;
        }

        $decoded = json_decode((string) $this->unb64($payload), true);
        if (!is_array($decoded) || empty($decoded['u']) || (int) ($decoded['x'] ?? 0) < time()) {
            return null;
        }

        return mb_strtolower((string) $decoded['u']);
    }

    private function sign(string $payload): string
    {
        return $this->b64(hash_hmac('sha256', 'hub-player|' . $payload, $this->secret(), true));
    }

    private function secret(): string
    {
        $secret = (string) config('sso.hub_player_token_secret');

        return $secret !== '' ? $secret : (string) config('app.key');
    }

    private function b64(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    private function unb64(string $value)
    {
        $pad = strlen($value) % 4;
        if ($pad) {
            $value .= str_repeat('=', 4 - $pad);
        }

        return base64_decode(strtr($value, '-_', '+/'), true);
    }
}
