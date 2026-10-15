<?php

namespace App\Services\Hub;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Admin dashboard aggregates (API_SPEC §5, GET /api/admin/overview).
 *
 * All stored timestamps are UTC. The viewer's timezone offset (`tz`, minutes)
 * is applied only when bucketing by local day, using MySQL:
 *   DATE_FORMAT(DATE_ADD(col, INTERVAL <tz> MINUTE), '%Y-%m-%d')
 */
class HubAdminStats
{
    /** @var string */
    private $conn = 'hub';

    public function overview(int $days, int $tz): array
    {
        $days = in_array($days, [7, 30, 90], true) ? $days : 30;
        $tz = ($tz >= -720 && $tz <= 840) ? $tz : 0;

        $nowTs = Carbon::now('UTC')->getTimestamp();
        $offsetSeconds = $tz * 60;

        // Local midnight today, expressed as a UTC instant.
        $todayStartTs = ((int) floor(($nowTs + $offsetSeconds) / 86400)) * 86400 - $offsetSeconds;

        $sinceTs = $todayStartTs - ($days - 1) * 86400;
        $weekStartTs = $todayStartTs - 6 * 86400;
        $monthStartTs = $todayStartTs - 29 * 86400;

        $now = $this->ts($nowTs);
        $todayStart = $this->ts($todayStartTs);
        $since = $this->ts($sinceTs);
        $weekStart = $this->ts($weekStartTs);
        $monthStart = $this->ts($monthStartTs);

        $events = DB::connection($this->conn)->table('activity_events');
        $sessions = DB::connection($this->conn)->table('game_sessions');

        // ── Totals ───────────────────────────────────────────────────────
        $registeredActive = $this->distinctActive($since);
        $activeToday = $this->distinctActive($todayStart);
        $activeWeek = $this->distinctActive($weekStart);
        $activeMonth = $this->distinctActive($monthStart);

        $registeredVisits = (clone $events)->where('event_type', 'hub_open')
            ->where('user_type', 'registered')->where('created_at', '>=', $since)->count();

        $guestVisits = (clone $events)->where('event_type', 'hub_open')
            ->where('user_type', 'guest')->where('created_at', '>=', $since)->count();

        $signups = DB::connection($this->conn)->table('users')
            ->where('created_at', '>=', $since)->where('created_at', '<=', $now)->count();

        $totalUsers = DB::connection($this->conn)->table('users')->count();

        $registeredPlays = (clone $sessions)->where('user_type', 'registered')
            ->where('started_at', '>=', $since)->count();

        $guestPlays = (clone $sessions)->where('user_type', 'guest')
            ->where('started_at', '>=', $since)->count();

        $registeredPlayers = (clone $sessions)->where('user_type', 'registered')
            ->where('started_at', '>=', $since)->distinct()->count('user_id');

        $playSeconds = (int) ((clone $sessions)->where('started_at', '>=', $since)
            ->whereNotNull('duration_sec')->sum('duration_sec'));

        $sessionsWithDuration = (clone $sessions)->where('started_at', '>=', $since)
            ->whereNotNull('duration_sec')->count();

        $done = (clone $sessions)->where('started_at', '>=', $since)
            ->where('ended_by', 'done')->count();

        $left = (clone $sessions)->where('started_at', '>=', $since)
            ->where('ended_by', 'left')->count();

        $totals = [
            'registeredActive' => $registeredActive,
            'activeToday' => $activeToday,
            'activeWeek' => $activeWeek,
            'activeMonth' => $activeMonth,
            'registeredVisits' => $registeredVisits,
            'guestVisits' => $guestVisits,
            'signups' => $signups,
            'totalUsers' => $totalUsers,
            'registeredPlays' => $registeredPlays,
            'guestPlays' => $guestPlays,
            'registeredPlayers' => $registeredPlayers,
            'playSeconds' => $playSeconds,
            'avgSessionSec' => $sessionsWithDuration > 0
                ? round($playSeconds / $sessionsWithDuration, 2)
                : null,
            'completionRate' => ($done + $left) > 0
                ? round($done / ($done + $left), 4)
                : null,
        ];

        return [
            'range' => [
                'days' => $days,
                'from' => $this->localDate($sinceTs, $offsetSeconds),
                'to' => $this->localDate($nowTs, $offsetSeconds),
            ],
            'totals' => $totals,
            'daily' => $this->daily($days, $sinceTs, $since, $now, $tz, $offsetSeconds),
            'games' => $this->games($since),
        ];
    }

    private function distinctActive(string $since): int
    {
        return DB::connection($this->conn)->table('activity_events')
            ->where('event_type', 'hub_open')
            ->where('user_type', 'registered')
            ->where('created_at', '>=', $since)
            ->distinct()
            ->count('user_id');
    }

    /**
     * One row per local day in the range, oldest first, zeros filled.
     */
    private function daily(int $days, int $sinceTs, string $since, string $now, int $tz, int $offsetSeconds): array
    {
        $events = DB::connection($this->conn)->table('activity_events');
        $sessions = DB::connection($this->conn)->table('game_sessions');
        $users = DB::connection($this->conn)->table('users');

        $dayExpr = $this->tzExpr('created_at', $tz);
        $sessionDayExpr = $this->tzExpr('started_at', $tz);

        $registeredActive = $this->grouped($events, $dayExpr, $since, $now, [
            ['event_type', 'hub_open'],
            ['user_type', 'registered'],
        ], 'user_id', true);

        $registeredVisits = $this->grouped($events, $dayExpr, $since, $now, [
            ['event_type', 'hub_open'],
            ['user_type', 'registered'],
        ]);

        $guestVisits = $this->grouped($events, $dayExpr, $since, $now, [
            ['event_type', 'hub_open'],
            ['user_type', 'guest'],
        ]);

        $registeredPlays = $this->grouped($sessions, $sessionDayExpr, $since, $now, [
            ['user_type', 'registered'],
        ], 'id', false, 'started_at');

        $guestPlays = $this->grouped($sessions, $sessionDayExpr, $since, $now, [
            ['user_type', 'guest'],
        ], 'id', false, 'started_at');

        $signups = $this->grouped($users, $dayExpr, $since, $now, []);

        $out = [];
        $startLocalTs = $sinceTs + $offsetSeconds;

        for ($i = 0; $i < $days; $i++) {
            $date = gmdate('Y-m-d', $startLocalTs + $i * 86400);
            $out[] = [
                'date' => $date,
                'registeredActive' => (int) ($registeredActive[$date] ?? 0),
                'registeredVisits' => (int) ($registeredVisits[$date] ?? 0),
                'guestVisits' => (int) ($guestVisits[$date] ?? 0),
                'registeredPlays' => (int) ($registeredPlays[$date] ?? 0),
                'guestPlays' => (int) ($guestPlays[$date] ?? 0),
                'signups' => (int) ($signups[$date] ?? 0),
            ];
        }

        return $out;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param array<int, array{0:string,1:mixed}> $wheres
     * @return array<string, int>
     */
    private function grouped($query, string $dayExpr, string $since, string $now, array $wheres, string $countTarget = '*', bool $distinct = false, string $timeCol = 'created_at'): array
    {
        $select = $distinct && $countTarget !== '*'
            ? "COUNT(DISTINCT {$countTarget}) as v"
            : 'COUNT(*) as v';

        $q = $query->selectRaw("{$dayExpr} as d, {$select}")
            ->where($timeCol, '>=', $since)
            ->where($timeCol, '<=', $now);

        foreach ($wheres as $w) {
            $q->where($w[0], $w[1]);
        }

        $rows = $q->groupBy('d')->pluck('v', 'd')->all();

        $out = [];
        foreach ($rows as $k => $v) {
            $out[(string) $k] = (int) $v;
        }

        return $out;
    }

    private function games(string $since): array
    {
        $rows = DB::connection($this->conn)->table('game_sessions')
            ->selectRaw("game_slug,
                SUM(user_type = 'registered') as registeredPlays,
                SUM(user_type = 'guest') as guestPlays,
                COUNT(DISTINCT CASE WHEN user_type = 'registered' THEN user_id END) as registeredPlayers,
                SUM(COALESCE(duration_sec, 0)) as totalSec,
                SUM(CASE WHEN duration_sec IS NOT NULL THEN 1 ELSE 0 END) as durCount,
                SUM(ended_by = 'done') as doneCount,
                SUM(ended_by = 'left') as leftCount")
            ->where('started_at', '>=', $since)
            ->groupBy('game_slug')
            ->orderBy('game_slug')
            ->get();

        $out = [];
        foreach ($rows as $r) {
            $totalSec = (int) $r->totalSec;
            $durCount = (int) $r->durCount;
            $done = (int) $r->doneCount;
            $left = (int) $r->leftCount;

            $out[] = [
                'slug' => (string) $r->game_slug,
                'registeredPlays' => (int) $r->registeredPlays,
                'guestPlays' => (int) $r->guestPlays,
                'registeredPlayers' => (int) $r->registeredPlayers,
                'totalSec' => $totalSec,
                'avgSec' => $durCount > 0 ? round($totalSec / $durCount, 2) : null,
                'completionRate' => ($done + $left) > 0 ? round($done / ($done + $left), 4) : null,
            ];
        }

        return $out;
    }

    private function tzExpr(string $col, int $tz): string
    {
        // $tz is a validated integer, safe to inline.
        return "DATE_FORMAT(DATE_ADD({$col}, INTERVAL {$tz} MINUTE), '%Y-%m-%d')";
    }

    private function ts(int $epoch): string
    {
        return gmdate('Y-m-d H:i:s', $epoch);
    }

    private function localDate(int $epoch, int $offsetSeconds): string
    {
        return gmdate('Y-m-d', $epoch + $offsetSeconds);
    }
}
