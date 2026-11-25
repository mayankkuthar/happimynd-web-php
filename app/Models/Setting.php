<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Admin-editable key/value settings.
 */
class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['key', 'value'];

    /** Days a HappiMynd (B2B) code stays valid when created without a "valid till" date */
    const TOKEN_DEFAULT_VALIDITY_DAYS = 'token_default_validity_days';

    /** Days a coupon stays valid when created without an expiry date */
    const COUPON_DEFAULT_VALIDITY_DAYS = 'coupon_default_validity_days';

    const DEFAULT_VALIDITY_DAYS = 60;

    public static function getValue(string $key, $default = null)
    {
        // Keeps code creation working if the settings migration has not been run yet
        if (!Schema::hasTable('settings')) {
            return $default;
        }
        $value = static::where('key', $key)->value('value');
        return $value === null ? $default : $value;
    }

    public static function setValue(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function tokenDefaultValidityDays(): int
    {
        return max(1, (int) static::getValue(self::TOKEN_DEFAULT_VALIDITY_DAYS, self::DEFAULT_VALIDITY_DAYS));
    }

    public static function couponDefaultValidityDays(): int
    {
        return max(1, (int) static::getValue(self::COUPON_DEFAULT_VALIDITY_DAYS, self::DEFAULT_VALIDITY_DAYS));
    }
}
