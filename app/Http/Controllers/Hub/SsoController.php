<?php

namespace App\Http\Controllers\Hub;

use App\Models\Hub\HubUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Single-use login codes for external games (API_SPEC §2).
 */
class SsoController extends HubController
{
    private const UNABLE = 'Unable to authenticate with this link. Please try again.';
    private const USED = 'This login link has already been used. Please start again from the hub.';
    private const EXPIRED = 'This login session has expired. Please start again from the hub.';

    public function create(Request $request)
    {
        $body = $this->jsonBody($request);

        $userId = trim((string) ($body['userId'] ?? ''));
        $gameId = trim((string) ($body['gameId'] ?? ''));

        if ($userId === '' || $gameId === '') {
            return $this->fail('User not found.');
        }

        $user = HubUser::find($userId);
        if (!$user) {
            return $this->fail('User not found.');
        }

        $code = bin2hex(random_bytes(32)); // 64 hex chars
        $now = $this->utcNow();

        DB::connection('hub')->table('sso_tickets')->insert([
            'code' => $code,
            'user_id' => $user->id,
            'game_id' => $gameId,
            'expires_at' => $this->utcStr($now->copy()->addSeconds(120)),
            'used_at' => null,
            'created_at' => $this->utcStr($now),
        ]);

        return $this->ok([
            'code' => $code,
            'expiresInSeconds' => 120,
            'gameId' => $gameId,
        ], 201);
    }

    public function exchange(Request $request)
    {
        $body = $this->jsonBody($request);
        $code = trim((string) ($body['code'] ?? ''));

        if ($code === '') {
            return $this->fail(self::UNABLE);
        }

        // Atomic redeem (MIGRATION_GUIDE §5): one statement, single use.
        $affected = DB::connection('hub')->update(
            'UPDATE sso_tickets
                SET used_at = UTC_TIMESTAMP(3)
              WHERE code = ?
                AND used_at IS NULL
                AND expires_at > UTC_TIMESTAMP(3)',
            [$code]
        );

        if ($affected === 1) {
            $ticket = DB::connection('hub')->table('sso_tickets')->where('code', $code)->first();
            $user = $ticket ? HubUser::find($ticket->user_id) : null;

            if (!$user) {
                return $this->fail(self::UNABLE);
            }

            return $this->ok([
                'user' => $this->presentUser($user),
                'gameId' => $ticket->game_id,
            ]);
        }

        // Nothing changed: work out why for a friendly message.
        $ticket = DB::connection('hub')->table('sso_tickets')->where('code', $code)->first();

        if (!$ticket) {
            return $this->fail(self::UNABLE);
        }
        if ($ticket->used_at !== null) {
            return $this->fail(self::USED);
        }

        return $this->fail(self::EXPIRED);
    }
}
