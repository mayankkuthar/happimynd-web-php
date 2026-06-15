<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileOtp extends Model
{
    protected $table = 'mobile_otps';

    protected $fillable = [
        'mobile',
        'otp',
        'country_code',
        'type',
        'expires_at',
        'verified_token',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function scopeValid($query)
    {
        return $query->where('expires_at', '>=', now())
            ->whereNull('verified_at');
    }

    public function scopeValidToken($query, $token)
    {
        return $query->where('verified_token', $token)
            ->whereNotNull('verified_at')
            ->where('expires_at', '>=', now());
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isVerified()
    {
        return !is_null($this->verified_at);
    }
}
