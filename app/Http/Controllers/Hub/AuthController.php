<?php

namespace App\Http\Controllers\Hub;

use App\Models\Hub\HubUser;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Player accounts (API_SPEC §2).
 * No server-side sessions: the API trusts the user id it is given.
 */
class AuthController extends HubController
{
    private const USERNAME_PATTERN = '/^[a-zA-Z0-9_]{3,20}$/';
    private const EMAIL_PATTERN = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';

    public function signup(Request $request)
    {
        $body = $this->jsonBody($request);

        $name = trim((string) ($body['name'] ?? ''));
        $username = trim((string) ($body['username'] ?? ''));
        $email = trim((string) ($body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');
        $avatar = (array_key_exists('avatar', $body) && $body['avatar'] !== null && $body['avatar'] !== '')
            ? (string) $body['avatar']
            : '🧘';

        if (mb_strlen($name) < 2) {
            return $this->fail('Please enter a name of at least 2 characters.');
        }
        if (!preg_match(self::USERNAME_PATTERN, $username)) {
            return $this->fail('Username must be 3-20 characters long and can only contain letters, numbers, and underscores.');
        }
        if (!preg_match(self::EMAIL_PATTERN, $email)) {
            return $this->fail('Please enter a valid email address.');
        }
        if (mb_strlen($password) < 6) {
            return $this->fail('Password must be at least 6 characters long.');
        }

        $usernameLc = mb_strtolower($username);
        $emailLc = mb_strtolower($email);

        if ($this->usernameTaken($usernameLc)) {
            return $this->fail('Username "@' . $username . '" is already taken.');
        }
        if ($this->emailTaken($emailLc)) {
            return $this->fail('This email address is already registered. Please log in instead.');
        }

        $now = $this->utcNow();

        try {
            $user = HubUser::create([
                'id' => (string) Str::uuid(),
                'email' => $emailLc,
                'username' => $usernameLc,
                'name' => $name,
                'password_hash' => Hash::make($password), // bcrypt, 10 rounds
                'avatar' => $avatar,
                'created_at' => $this->utcStr($now),
                'updated_at' => $this->utcStr($now),
            ]);
        } catch (QueryException $e) {
            if ($this->isDuplicateKey($e)) {
                return $this->fail(
                    $this->usernameTaken($usernameLc)
                        ? 'Username "@' . $username . '" is already taken.'
                        : 'This email address is already registered. Please log in instead.'
                );
            }
            throw $e;
        }

        return $this->ok(['user' => $this->presentUser($user)], 201);
    }

    public function login(Request $request)
    {
        $body = $this->jsonBody($request);

        $identifier = $body['loginIdentifier'] ?? $body['email'] ?? $body['username'] ?? null;
        $identifier = trim((string) $identifier);
        $password = (string) ($body['password'] ?? '');

        if ($identifier === '' || $password === '') {
            return $this->fail('Please enter your username or email address, and your password.');
        }

        $identifierLc = mb_strtolower($identifier);

        $user = HubUser::whereRaw('LOWER(username) = ?', [$identifierLc])
            ->orWhereRaw('LOWER(email) = ?', [$identifierLc])
            ->first();

        if (!$user || empty($user->password_hash) || !Hash::check($password, $user->password_hash)) {
            return $this->fail('Invalid email or password', 401);
        }

        return $this->ok(['user' => $this->presentUser($user)]);
    }

    public function me(Request $request)
    {
        $userId = $request->query('userId') ?: $request->header('x-user-id');

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
