<?php

namespace App\Http\Controllers\Hub;

use App\Models\Hub\HubGameProgress;
use App\Models\Hub\HubUser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Saved game progress for the level-based games (API_SPEC §7).
 *
 * One save per signed-in player per game. The save is an opaque JSON object
 * that only the game reads; the API stores it as-is. Guests never call these
 * endpoints. There are no player sessions: the API trusts the `userId` it is
 * given, like /api/hub/auth/me.
 */
class ProgressController extends HubController
{
    private const UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
    private const MAX_BYTES = 65536; // 64 KB

    /**
     * GET /api/hub/progress/:slug?userId=<uuid>
     */
    public function show(Request $request, $slug)
    {
        $slug = (string) $slug;
        if (!preg_match(config('hub.slug_pattern'), $slug)) {
            return $this->noStore($this->fail('Invalid game.', 404));
        }

        $userId = $request->query('userId');
        if (!$this->isUuid($userId)) {
            return $this->noStore($this->fail('Invalid userId.'));
        }

        $row = HubGameProgress::query()
            ->where('user_id', mb_strtolower((string) $userId))
            ->where('game_slug', $slug)
            ->first();

        if (!$row) {
            return $this->noStore($this->ok(['data' => null, 'updatedAt' => null]));
        }

        // Decode as objects (not associative arrays) so nested JSON objects are
        // returned exactly as stored. A row that no longer parses counts as no
        // save ("the stored text is bad").
        $data = json_decode((string) $row->data);
        if (json_last_error() !== JSON_ERROR_NONE || $data === null) {
            return $this->noStore($this->ok(['data' => null, 'updatedAt' => null]));
        }

        $updatedAt = Carbon::parse((string) $row->updated_at, 'UTC')->toIso8601String();

        return $this->noStore($this->ok(['data' => $data, 'updatedAt' => $updatedAt]));
    }

    /**
     * POST /api/hub/progress/:slug
     * Body: text/plain JSON { userId, data } — parsed from the raw body, like
     * the tracking endpoints.
     */
    public function save(Request $request, $slug)
    {
        $raw = $request->getContent();
        $body = json_decode($raw);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->noStore($this->fail('Invalid JSON.'));
        }

        $slug = (string) $slug;
        if (!preg_match(config('hub.slug_pattern'), $slug)) {
            return $this->noStore($this->fail('Invalid game.', 404));
        }

        $userId = is_object($body) ? ($body->userId ?? null) : null;
        if (!$this->isUuid($userId)) {
            return $this->noStore($this->fail('Invalid userId.'));
        }
        $userId = mb_strtolower((string) $userId);

        $data = is_object($body) ? ($body->data ?? null) : null;
        if (!is_object($data)) {
            return $this->noStore($this->fail('Invalid data.'));
        }

        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (strlen($json) > self::MAX_BYTES) {
            return $this->noStore($this->fail('Save is too large.'));
        }

        if (!HubUser::where('id', $userId)->exists()) {
            return $this->noStore($this->fail('User not found.', 404));
        }

        // Replace the row, last write wins (MySQL 8.0.19+ syntax).
        DB::connection('hub')->update(
            'INSERT INTO game_progress (user_id, game_slug, data, updated_at)
             VALUES (?, ?, ?, UTC_TIMESTAMP(3)) AS new
             ON DUPLICATE KEY UPDATE data = new.data, updated_at = new.updated_at',
            [$userId, $slug, $json]
        );

        return $this->noStore($this->noContent());
    }

    private function isUuid($value): bool
    {
        return is_string($value) && (bool) preg_match(self::UUID_PATTERN, $value);
    }

    private function noStore($response)
    {
        return $response->header('Cache-Control', 'no-store');
    }
}
