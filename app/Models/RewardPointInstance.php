<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A way to earn reward points. The code always finds a reward by its fixed
 * `key`; `action_performed` is only the text shown to users and admins, so it
 * can be edited freely.
 */
class RewardPointInstance extends Model
{
    use HasFactory;

    /** Awarded by backend code at a fixed place (key cannot be changed) */
    const BY_SYSTEM = 'system';
    /** Reported by an app / website / service via POST /api/v1/earn-reward-points */
    const BY_APP = 'app';

    const FREQUENCIES = [
        'every_time' => 'Every time',
        'daily'      => 'Once a day',
        'once'       => 'Only once',
    ];

    /** Services offered in the admin "service" dropdown (any other text can be typed too) */
    const SERVICES = [
        'HappiLIFE', 'HappiSELF', 'HappiGUIDE', 'HappiBUDDY', 'HappiTALK', 'HappiLEARN',
        'HappiVOICE', 'HappiPAUSE', 'Quizzard', 'Mood-O-Meter', 'Profile', 'Referral', 'General',
    ];

    protected $fillable = [
        'key',
        'service',
        'action_performed',
        'points_to_be_given',
        'awarded_by',
        'frequency',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function findByKey($key)
    {
        return static::where('key', $key)->first();
    }

    public function frequencyLabel(): string
    {
        return self::FREQUENCIES[$this->frequency] ?? $this->frequency;
    }
}
