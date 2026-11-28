<?php

namespace App\Http\Controllers\Hub;

use App\Models\Hub\HubUser;
use App\Services\Hub\HubPlayerToken;
use App\Services\Sso\SsoService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Player accounts (API_SPEC §2).
 * Players sign in with HappiMynd SSO and get a signed token (HubPlayerToken);
 * user-specific endpoints only trust that token, never a sent user id.
 */
class AuthController extends HubController
{
    private const EMAIL_PATTERN = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';

    /** Retired: players sign in with HappiMynd (OTP) through /sso. */
    public function signup(Request $request)
    {
        return $this->fail('Sign-up now happens with HappiMynd. Please use "Continue with HappiMynd".', 410);
    }

    /** Retired: players sign in with HappiMynd (OTP) through /sso. */
    public function login(Request $request)
    {
        return $this->fail('Password sign-in has been replaced. Please use "Continue with HappiMynd".', 410);
    }

    /**
     * POST /api/hub/auth/sso  Body: { code } (one-time code from happimynd.com/v2/sso)
     * 200 { success, user, token } — the player, created on first visit.
     */
    public function sso(Request $request, SsoService $sso, HubPlayerToken $tokens)
    {
        $body = $this->jsonBody($request);
        $main = $sso->consume(isset($body['code']) ? (string) $body['code'] : null, 'hub');
        if (!$main) {
            return $this->fail('This sign-in link has expired. Please try again.', 401);
        }

        $identity = $sso->identity($main);
        $user = HubUser::where('happimynd_user_id', $main->id)->first();
        $now = $this->utcStr();

        if (!$user) {
            $user = $this->createFromHappimynd($identity, $now);
        } else {
            // Keep the name in step with the HappiMynd profile.
            $user->name = $identity['name'];
            $user->mobile = $identity['mobile'];
            $user->updated_at = $now;
            $user->save();
        }

        return $this->ok([
            'user' => $this->presentUser($user),
            'token' => $tokens->issue((string) $user->id),
        ]);
    }

    /** GET /api/hub/auth/me  (Authorization: Bearer <player token>) */
    public function me(Request $request)
    {
        $userId = $this->playerId($request);

        if (empty($userId)) {
            return $this->fail('Not authenticated.', 401);
        }

        $user = HubUser::find($userId);
        if (!$user) {
            return $this->fail('User not found.', 404);
        }

        return $this->ok(['user' => $this->presentUser($user)]);
    }

    public function logout(Request $request)
    {
        // Nothing is stored server-side.
        return $this->ok();
    }

    private function createFromHappimynd(array $identity, string $now): HubUser
    {
        // Usernames are shown as @handle; make one from the HappiMynd name.
        $base = mb_strtolower(preg_replace('/[^a-zA-Z0-9_]+/', '', (string) ($identity['username'] ?: $identity['name'])));
        $base = substr($base !== '' ? $base : 'player', 0, 14);
        $username = $base;
        for ($i = 0; $this->usernameTaken($username); $i++) {
            $username = substr($base, 0, 14) . '_' . ($i < 3 ? random_int(10, 99) : random_int(1000, 99999));
        }

        // Email is optional now (many HappiMynd users sign up with a mobile only),
        // and only kept when no other player already has it.
        $email = $identity['email'] && preg_match(self::EMAIL_PATTERN, $identity['email']) && !$this->emailTaken($identity['email'])
            ? $identity['email'] : null;

        try {
            return HubUser::create([
                'id' => (string) Str::uuid(),
                'happimynd_user_id' => (int) $identity['id'],
                'mobile' => $identity['mobile'],
                'email' => $email,
                'username' => $username,
                'name' => $identity['name'],
                'password_hash' => null,
                'avatar' => '🧘',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } catch (QueryException $e) {
            // Two tabs finishing SSO at once: the other one created the row.
            if ($this->isDuplicateKey($e)) {
                $existing = HubUser::where('happimynd_user_id', (int) $identity['id'])->first();
                if ($existing) {
                    return $existing;
                }
            }
            throw $e;
        }
    }

    private function usernameTaken(string $usernameLc): bool
    {
        return HubUser::whereRaw('LOWER(username) = ?', [$usernameLc])->exists();
    }

    private function emailTaken(string $emailLc): bool
    {
        return HubUser::whereRaw('LOWER(email) = ?', [$emailLc])->exists();
    }

    private function isDuplicateKey(QueryException $e): bool
    {
        // MySQL: error number 1062 (ER_DUP_ENTRY); SQLSTATE 23000.
        return $e->getCode() === '23000' || (int) ($e->errorInfo[1] ?? 0) === 1062;
    }
}
