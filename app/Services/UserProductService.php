<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Classifies users by the products they have actually started.
 *
 * Every registered user is in the User List. As soon as a user starts a
 * product (takes the HappiLIFE screening, opens a HappiSELF course, books a
 * HappiTALK session, ...) they are also classified under that product.
 * A user who has used several products is classified under each of them.
 */
class UserProductService
{
    /** Cache lifetime (seconds) for the counts shown in the sidebar / dashboard */
    const COUNT_CACHE_TTL = 600;

    const COUNT_CACHE_KEY = 'admin.user_product_counts';

    /** Filter value for users who have not started any product yet */
    const NONE = 'none';

    /**
     * product key => [label, User relation that records usage]
     */
    const PRODUCTS = [
        'happilife'  => ['label' => 'HappiLIFE',  'relation' => 'assessment'],
        'happiself'  => ['label' => 'HappiSELF',  'relation' => 'happiselfProgress'],
        'happiguide' => ['label' => 'HappiGUIDE', 'relation' => 'happiguideSessions'],
        'happibuddy' => ['label' => 'HappiBUDDY', 'relation' => 'happibuddyChats'],
        'happitalk'  => ['label' => 'HappiTALK',  'relation' => 'happitalkSessions'],
        'happilearn' => ['label' => 'HappiLEARN', 'relation' => 'happilearnViews'],
        'happivoice' => ['label' => 'HappiVOICE', 'relation' => 'happivoiceScores'],
    ];

    /**
     * Column that shows when a user last used each product.
     * Tables that add a row per use keep created_at; tables that keep one row
     * per user/item and update it on each visit use updated_at.
     */
    const ACTIVITY_COLUMNS = [
        'happilife'  => 'created_at',
        'happiself'  => 'updated_at',
        'happiguide' => 'created_at',
        'happibuddy' => 'updated_at',
        'happitalk'  => 'created_at',
        'happilearn' => 'updated_at',
        'happivoice' => 'created_at',
    ];

    /** Rolling windows for the active-user counts: name => days */
    const ACTIVITY_WINDOWS = [
        'daily'   => 1,
        'weekly'  => 7,
        'monthly' => 30,
        'yearly'  => 365,
    ];

    public static function isValidFilter($product): bool
    {
        return $product === self::NONE || isset(self::PRODUCTS[$product]);
    }

    public static function label($product): string
    {
        if ($product === self::NONE) {
            return 'Not started any product';
        }
        return self::PRODUCTS[$product]['label'] ?? '';
    }

    /**
     * Restrict a User query to users of one product (or to users with no product).
     */
    public static function applyFilter($query, $product)
    {
        if ($product === self::NONE) {
            foreach (self::PRODUCTS as $config) {
                $query->doesntHave($config['relation']);
            }
            return $query;
        }
        if (isset(self::PRODUCTS[$product])) {
            $query->has(self::PRODUCTS[$product]['relation']);
        }
        return $query;
    }

    /**
     * Adds "{key}_usage_count" columns so the list can show which products each user started.
     */
    public static function withUsageCounts($query)
    {
        $counts = [];
        foreach (self::PRODUCTS as $key => $config) {
            $counts[] = $config['relation'] . ' as ' . $key . '_usage_count';
        }
        return $query->withCount($counts);
    }

    /**
     * Same "{key}_usage_count" columns as withUsageCounts(), loaded for an
     * already fetched page of users. Counting only the rows on the page keeps
     * the list fast: withUsageCounts() on the paginated query made MySQL count
     * every product for all ~215k HappiLIFE users before it could sort and
     * take 10, which took minutes.
     */
    public static function loadUsageCounts($users)
    {
        $counts = [];
        foreach (self::PRODUCTS as $key => $config) {
            $counts[] = $config['relation'] . ' as ' . $key . '_usage_count';
        }
        return $users->loadCount($counts);
    }

    /**
     * Labels of products a user (loaded with withUsageCounts / loadUsageCounts) has started.
     */
    public static function usedProductLabels($user): array
    {
        $labels = [];
        foreach (self::PRODUCTS as $key => $config) {
            if ((int)($user->{$key . '_usage_count'} ?? 0) > 0) {
                $labels[] = $config['label'];
            }
        }
        return $labels;
    }

    /**
     * Number of users per product, matching exactly what the filtered User List shows.
     * ['all' => n, 'none' => n, 'happilife' => n, ...]
     */
    public static function counts(): array
    {
        return Cache::remember(self::COUNT_CACHE_KEY, self::COUNT_CACHE_TTL, function () {
            $counts = ['all' => User::count()];
            foreach (self::PRODUCTS as $key => $config) {
                $counts[$key] = User::has($config['relation'])->count();
            }
            $counts[self::NONE] = self::applyFilter(User::query(), self::NONE)->count();
            return $counts;
        });
    }

    /**
     * Reads ?from_date=YYYY-MM-DD&to_date=YYYY-MM-DD (either may be empty).
     * Returns [Carbon|null $from (start of day), Carbon|null $to (end of day)], swapped if given backwards.
     */
    public static function dateRange($request): array
    {
        $from = self::parseDate($request->input('from_date'));
        $to   = self::parseDate($request->input('to_date'));

        if ($from && $to && $from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        return [
            $from ? $from->copy()->startOfDay() : null,
            $to ? $to->copy()->endOfDay() : null,
        ];
    }

    private static function parseDate($value)
    {
        if (!is_string($value) || $value === '') {
            return null;
        }
        try {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Restrict a User query to a date range.
     *  - No product / "none": by registration date (users.created_at).
     *  - A product: by the date the user STARTED that product (their first usage record),
     *    so a range shows the users that were newly added to that service in it.
     */
    public static function applyDateRange($query, $product, $from, $to)
    {
        if (!$from && !$to) {
            return $query;
        }

        if ($product && isset(self::PRODUCTS[$product])) {
            return self::startedProductBetween($query, self::PRODUCTS[$product]['relation'], $from, $to);
        }

        if ($from) {
            $query->where('users.created_at', '>=', $from);
        }
        if ($to) {
            $query->where('users.created_at', '<=', $to);
        }
        return $query;
    }

    /**
     * Users whose first record in $relation falls inside [$from, $to].
     */
    public static function startedProductBetween($query, $relation, $from, $to)
    {
        $query->whereHas($relation, function ($q) use ($from, $to) {
            $column = $q->getModel()->qualifyColumn('created_at');
            if ($from) {
                $q->where($column, '>=', $from);
            }
            if ($to) {
                $q->where($column, '<=', $to);
            }
        });
        if ($from) {
            // used it before the range => not a new user of this product in the range
            $query->whereDoesntHave($relation, function ($q) use ($from) {
                $q->where($q->getModel()->qualifyColumn('created_at'), '<', $from);
            });
        }
        return $query;
    }

    /**
     * New users in the last 24 hours / 7 days, overall (registrations) and per product
     * (users who started the product). Test users excluded, like the other dashboard tiles.
     * ['overall' => ['24h' => n, '7d' => n], 'products' => [key => ['label', '24h', '7d']]]
     */
    public static function newUserStats(): array
    {
        return Cache::remember(self::COUNT_CACHE_KEY . '.new_users', self::COUNT_CACHE_TTL, function () {
            $now = Carbon::now();
            $windows = [
                '24h' => $now->copy()->subDay(),
                '7d'  => $now->copy()->subDays(7),
            ];

            $stats = ['overall' => [], 'products' => []];
            foreach ($windows as $name => $since) {
                $stats['overall'][$name] = User::RemoveTestUser()->where('users.created_at', '>=', $since)->count();
            }
            foreach (self::PRODUCTS as $key => $config) {
                $stats['products'][$key] = ['label' => $config['label']];
                foreach ($windows as $name => $since) {
                    $stats['products'][$key][$name] = self::startedProductBetween(
                        User::RemoveTestUser(), $config['relation'], $since, null
                    )->count();
                }
            }
            return $stats;
        });
    }

    /**
     * Distinct users who used each product in the last day / week / month / year
     * (rolling windows, test users excluded). 'all' = users who used any product.
     * ['products' => [key => ['label', 'daily', 'weekly', 'monthly', 'yearly']], 'all' => [window => n]]
     */
    public static function activeUserStats(): array
    {
        return Cache::remember(self::COUNT_CACHE_KEY . '.active_users', self::COUNT_CACHE_TTL, function () {
            $now = Carbon::now();
            $since = [];
            foreach (self::ACTIVITY_WINDOWS as $name => $days) {
                $since[$name] = $now->copy()->subDays($days);
            }
            // Only the widest window is read from the tables; the narrower
            // ones are counted from the same rows in one pass
            $oldest = $now->copy()->subDays(max(self::ACTIVITY_WINDOWS));

            // Counting from each product's (small) table and joining users by
            // primary key keeps this to one query per product plus one for
            // "any product". The earlier whereHas() version checked every
            // user against every product table for every window (32 scans of
            // ~230k users, most product tables have no user_id index) and
            // took minutes on production.
            $stats = ['products' => [], 'all' => []];
            $all = null;
            foreach (self::PRODUCTS as $key => $config) {
                $activity = self::activityRows($key, $oldest);
                $stats['products'][$key] = ['label' => $config['label']] + self::countActiveUsers($activity, $since);
                $all = $all ? $all->unionAll($activity) : $activity;
            }
            $stats['all'] = self::countActiveUsers($all, $since);
            return $stats;
        });
    }

    /**
     * (user_id, activity_at) rows for one product since $from. Goes through
     * the related model so its scopes (e.g. soft deletes) still apply.
     */
    private static function activityRows($product, $from)
    {
        $relation = (new User)->{self::PRODUCTS[$product]['relation']}();
        $related = $relation->getRelated();
        $column = $related->qualifyColumn(self::ACTIVITY_COLUMNS[$product] ?? 'created_at');

        return $related->newQuery()
            ->select([$relation->getQualifiedForeignKeyName() . ' as user_id', $column . ' as activity_at'])
            ->where($column, '>=', $from)
            ->toBase();
    }

    /**
     * Distinct non-test users in $activity per window: [window => n]
     */
    private static function countActiveUsers($activity, array $since): array
    {
        $selects = [];
        $bindings = [];
        foreach ($since as $name => $from) {
            $selects[] = "COUNT(DISTINCT CASE WHEN a.activity_at >= ? THEN a.user_id END) AS `{$name}`";
            $bindings[] = $from;
        }

        $row = DB::query()
            ->selectRaw(implode(', ', $selects), $bindings)
            ->fromSub($activity, 'a')
            ->joinSub(User::RemoveTestUser()->select('id')->toBase(), 'u', 'u.id', '=', 'a.user_id')
            ->first();

        return array_map('intval', (array) $row);
    }

    /**
     * New users in a custom range (not cached): overall registrations + users who started each product.
     * ['overall' => n, key => n, ...]
     */
    public static function newUserCountsBetween($from, $to): array
    {
        $counts = ['overall' => self::applyDateRange(User::RemoveTestUser(), null, $from, $to)->count()];
        foreach (self::PRODUCTS as $key => $config) {
            $counts[$key] = self::startedProductBetween(User::RemoveTestUser(), $config['relation'], $from, $to)->count();
        }
        return $counts;
    }

    /**
     * Per-product B2C / B2B split for the dashboard (test users excluded, like the other dashboard tiles).
     * [key => ['label' => .., 'b2c' => n, 'b2b' => n, 'total' => n]]
     */
    public static function dashboardBreakdown(): array
    {
        return Cache::remember(self::COUNT_CACHE_KEY . '.dashboard', self::COUNT_CACHE_TTL, function () {
            $rows = [];
            foreach (self::PRODUCTS as $key => $config) {
                $b2b = User::RemoveTestUser()->has('userToken')->has($config['relation'])->count();
                $b2c = User::RemoveTestUser()->doesntHave('userToken')->has($config['relation'])->count();
                $rows[$key] = [
                    'label' => $config['label'],
                    'b2c'   => $b2c,
                    'b2b'   => $b2b,
                    'total' => $b2c + $b2b,
                ];
            }
            return $rows;
        });
    }
}
