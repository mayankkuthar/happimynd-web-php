<?php

namespace App\Services\Sso;

use App\Models\SsoAdminNumber;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * One-time login codes that carry a signed-in HappiMynd user over to
 * Quizzard or the Games Hub. See config/sso.php for the flow.
 */
class SsoService
{
    public function apps(): array
    {
        return array_keys(config('sso.apps'));
    }

    public function isApp(?string $app): bool
    {
        return is_string($app) && array_key_exists($app, config('sso.apps'));
    }

    /**
     * The return_to URL if it points at one of the app's allowed origins, or null.
     * Only the scheme + host (+ port) are compared, so any path on the app is fine.
     */
    public function allowedReturnTo(string $app, ?string $returnTo): ?string
    {
        if (!$this->isApp($app) || !is_string($returnTo) || $returnTo === '') {
            return null;
        }

        $parts = parse_url($returnTo);
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        $origin = strtolower($parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : ''));
        foreach (config("sso.apps.$app.origins") as $allowed) {
            if (strtolower(rtrim($allowed, '/')) === $origin) {
                return $returnTo;
            }
        }

        return null;
    }

    /** return_to with sso_code (or sso_error) added to its query string. */
    public function withParam(string $url, string $key, string $value): string
    {
        $fragment = '';
        if (($hash = strpos($url, '#')) !== false) {
            $fragment = substr($url, $hash);
            $url = substr($url, 0, $hash);
        }

        return $url . (strpos($url, '?') === false ? '?' : '&') . $key . '=' . rawurlencode($value) . $fragment;
    }

    public function createCode(User $user, string $app): string
    {
        $code = Str::random(48);

        // Old codes are never needed again; keep the table small.
        DB::table('sso_codes')->where('expires_at', '<', Carbon::now()->subDay())->delete();

        DB::table('sso_codes')->insert([
            'code_hash' => hash('sha256', $code),
            'user_id' => $user->id,
            'app' => $app,
            'expires_at' => Carbon::now()->addSeconds((int) config('sso.code_ttl_seconds')),
            'created_at' => Carbon::now(),
        ]);

        return $code;
    }

    /** Use up a code. Returns its user, or null if the code is unknown, used, expired or for another app. */
    public function consume(?string $code, string $app): ?User
    {
        if (!is_string($code) || strlen($code) < 20 || strlen($code) > 128) {
            return null;
        }

        $hash = hash('sha256', $code);
        $now = Carbon::now();

        // Single statement, so two requests racing with the same code can't both win.
        $claimed = DB::table('sso_codes')
            ->where('code_hash', $hash)
            ->where('app', $app)
            ->whereNull('used_at')
            ->where('expires_at', '>=', $now)
            ->update(['used_at' => $now]);

        if ($claimed !== 1) {
            return null;
        }

        $userId = DB::table('sso_codes')->where('code_hash', $hash)->value('user_id');
        $user = User::find($userId);

        if (!$user || $user->account_status === 'blocked' || (string) $user->is_account_deleted === '1') {
            return null;
        }

        return $user;
    }

    /** What an app is told about the user. */
    public function identity(User $user): array
    {
        $admin = SsoAdminNumber::findActiveFor($user->mobile);

        $name = trim((string) ($user->nickname ?: $user->username));

        return [
            'id' => (string) $user->id,
            'name' => $name !== '' ? $name : 'HappiMynd user',
            'username' => (string) $user->username,
            'mobile' => SsoAdminNumber::normalize($user->mobile),
            'email' => $user->email ? mb_strtolower(trim((string) $user->email)) : null,
            'age' => $user->age !== null ? (int) $user->age : null,
            'gender' => $user->gender ?: null,
            'language' => $user->language ?: null,
            'admin' => [
                'quizzard_role' => $admin ? $admin->quizzard_role : 'none',
                'quizzard_org_id' => $admin ? $admin->quizzard_org_id : null,
                'hub_admin' => $admin ? (bool) $admin->hub_admin : false,
                'name' => $admin ? $admin->name : null,
            ],
        ];
    }
}
