<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A mobile number that gets admin rights in Quizzard and/or the Games Hub
 * when its owner signs in there through HappiMynd SSO.
 * Managed in Admin Panel > Admins > App Admin Numbers.
 */
class SsoAdminNumber extends Model
{
    protected $table = 'sso_admin_numbers';

    protected $fillable = ['name', 'mobile', 'quizzard_role', 'quizzard_org_id', 'hub_admin', 'is_active'];

    protected $casts = [
        'hub_admin' => 'boolean',
        'is_active' => 'boolean',
    ];

    const QUIZZARD_ROLES = [
        'none' => 'No admin access',
        'admin' => 'Admin (one organisation)',
        'super_admin' => 'Super Admin',
    ];

    /** Last 10 digits, so "+91 93184 58907", "919318458907" and "9318458907" all match. */
    public static function normalize(?string $mobile): string
    {
        $digits = preg_replace('/\D+/', '', (string) $mobile);

        return strlen($digits) > 10 ? substr($digits, -10) : $digits;
    }

    public static function findActiveFor(?string $mobile): ?self
    {
        $normalized = self::normalize($mobile);
        if ($normalized === '') {
            return null;
        }

        return self::where('mobile', $normalized)->where('is_active', true)->first();
    }
}
