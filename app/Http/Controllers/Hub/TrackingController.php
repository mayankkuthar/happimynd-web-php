<?php

namespace App\Http\Controllers\Hub;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Usage tracking (API_SPEC §3).
 *
 * The browser posts with Content-Type text/plain and keepalive so requests
 * avoid a CORS preflight and can finish as the page closes. Success = 204 (no
 * body); failure = 400. The client ignores the response either way.
 */
class TrackingController extends HubController
{
    private const UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
    private const MAX_DURATION = 21600; // 6 hours
    private const MAX_STATS = 8;

    public function event(Request $request)
    {
        $body = $this->jsonBody($request);

        $common = $this->validateCommon($body);
        if (isset($common['error'])) {
            return $this->fail($common['error']);
        }

        // The only event the app sends now; the old 'external_launch' event was
        // removed when the games moved into the hub (API_SPEC §3).
        if (($body['type'] ?? null) !== 'hub_open') {
            return $this->fail('Invalid event type.');
        }

        DB::connection('hub')->table('activity_events')->insert([
            'id' => (string) Str::uuid(),
            'event_type' => 'hub_open',
            'game_slug' => null,
            'user_type' => $common['userType'],
            'user_id' => $common['userId'],
            'created_at' => $this->utcStr(),
        ]);

        return $this->noContent();
    }

    public function sessionStart(Request $request)
    {
        $body = $this->jsonBody($request);

        $common = $this->validateCommon($body);
        if (isset($common['error'])) {
            return $this->fail($common['error']);
        }

        $sessionId = $this->uuidOrNull($body['sessionId'] ?? null);
        if ($sessionId === null) {
            return $this->fail('Invalid session id.');
        }

        $gameSlug = isset($body['gameSlug']) ? (string) $body['gameSlug'] : '';
        if (!preg_match(config('hub.slug_pattern'), $gameSlug)) {
            return $this->fail('Invalid game slug.');
        }

        // A retry of the same start is ignored.
        DB::connection('hub')->table('game_sessions')->insertOrIgnore([
            'id' => $sessionId,
            'game_slug' => $gameSlug,
            'user_type' => $common['userType'],
            'user_id' => $common['userId'],
            'started_at' => $this->utcStr(),
            'ended_at' => null,
            'duration_sec' => null,
            'ended_by' => null,
            'stats' => null,
        ]);

        return $this->noContent();
    }

    public function sessionEnd(Request $request)
    {
        $body = $this->jsonBody($request);

        $common = $this->validateCommon($body);
        if (isset($common['error'])) {
            return $this->fail($common['error']);
        }

        $sessionId = $this->uuidOrNull($body['sessionId'] ?? null);
        if ($sessionId === null) {
            return $this->fail('Invalid session id.');
        }

        $gameSlug = isset($body['gameSlug']) ? (string) $body['gameSlug'] : '';
        if (!preg_match(config('hub.slug_pattern'), $gameSlug)) {
            return $this->fail('Invalid game slug.');
        }

        $duration = $this->cleanDuration($body['durationSec'] ?? null);
        $endedBy = (($body['endedBy'] ?? null) === 'done') ? 'done' : 'left';
        $stats = $this->cleanStats($body['stats'] ?? null);

        $now = $this->utcStr();

        $affected = DB::connection('hub')->update(
            "UPDATE game_sessions
                SET ended_at = ?, duration_sec = ?, ended_by = ?, stats = ?
              WHERE id = ?
                AND (ended_by IS NULL OR ended_by <> 'done' OR ? = 'done')",
            [$now, $duration, $endedBy, $stats, $sessionId, $endedBy]
        );

        if ($affected === 0) {
            $exists = DB::connection('hub')->table('game_sessions')->where('id', $sessionId)->exists();

            if (!$exists) {
                // The start was lost: write the full row.
                $started = $this->utcStr($this->utcNow()->subSeconds($duration));

                DB::connection('hub')->table('game_sessions')->insert([
                    'id' => $sessionId,
                    'game_slug' => $gameSlug,
                    'user_type' => $common['userType'],
                    'user_id' => $common['userId'],
                    'started_at' => $started,
                    'ended_at' => $now,
                    'duration_sec' => $duration,
                    'ended_by' => $endedBy,
                    'stats' => $stats,
                ]);
            }
        }

        return $this->noContent();
    }

    /**
     * Shared validation (API_SPEC §3).
     *
     * @return array{error?: string, userType?: string, userId?: ?string}
     */
    private function validateCommon(array $body): array
    {
        $userType = $body['userType'] ?? null;
        if (!in_array($userType, ['guest', 'registered'], true)) {
            return ['error' => 'Invalid user type.'];
        }

        $userId = null;
        if ($userType === 'registered') {
            $userId = $this->uuidOrNull($body['userId'] ?? null);
            if ($userId === null) {
                return ['error' => 'A valid user id is required for registered users.'];
            }
        }
        // Guests: ignore any id they send (user_id stays NULL).

        return ['userType' => $userType, 'userId' => $userId];
    }

    private function uuidOrNull($value): ?string
    {
        if (!is_string($value) || !preg_match(self::UUID_PATTERN, $value)) {
            return null;
        }

        return strtolower($value);
    }

    private function cleanDuration($value): int
    {
        if (!is_numeric($value)) {
            return 0;
        }

        return max(0, min(self::MAX_DURATION, (int) round((float) $value)));
    }

    private function cleanStats($stats): ?string
    {
        if (!is_array($stats)) {
            return null;
        }

        $out = [];
        foreach ($stats as $item) {
            if (count($out) >= self::MAX_STATS) {
                break;
            }
            if (!is_array($item)) {
                continue;
            }

            $label = isset($item['label']) && is_string($item['label']) ? trim($item['label']) : '';
            if ($label === '') {
                continue;
            }
            $label = mb_substr($label, 0, 40);

            $value = $item['value'] ?? null;
            if (is_int($value) || is_float($value)) {
                if (!is_finite((float) $value)) {
                    continue;
                }
                $cleanValue = $value;
            } elseif (is_string($value)) {
                $cleanValue = mb_substr($value, 0, 24);
            } else {
                continue;
            }

            $out[] = ['label' => $label, 'value' => $cleanValue];
        }

        if (empty($out)) {
            return null;
        }

        return json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
