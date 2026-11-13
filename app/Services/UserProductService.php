<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

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
     * Labels of products a user (loaded with withUsageCounts) has started.
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
