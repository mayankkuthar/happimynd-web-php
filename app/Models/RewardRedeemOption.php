<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Something users can redeem reward points for (see the migration for fields).
 */
class RewardRedeemOption extends Model
{
    protected $table = 'reward_redeem_options';

    protected $fillable = [
        'title', 'description', 'category', 'points_required', 'reward_value', 'image',
        'stock', 'max_per_user', 'valid_from', 'valid_until', 'terms', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'points_required' => 'integer',
        'stock' => 'integer',
        'max_per_user' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'valid_from' => 'date:Y-m-d',
        'valid_until' => 'date:Y-m-d',
    ];

    const CATEGORIES = [
        'discount' => 'Discount / coupon',
        'session'  => 'Free session',
        'service'  => 'Service unlock',
        'voucher'  => 'Gift voucher',
        'merch'    => 'Merchandise',
        'other'    => 'Other',
    ];

    /** Switched on and inside its validity dates today */
    public function scopeAvailable($query)
    {
        $today = Carbon::today()->toDateString();
        return $query->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', $today);
            });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('points_required')->orderBy('id');
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst((string) $this->category);
    }

    /** "Live", "Off", "Scheduled", "Expired" or "Out of stock" for the admin list */
    public function statusLabel(): string
    {
        $today = Carbon::today();
        if (!$this->is_active) {
            return 'Off';
        }
        if ($this->valid_from && $this->valid_from->gt($today)) {
            return 'Scheduled';
        }
        if ($this->valid_until && $this->valid_until->lt($today)) {
            return 'Expired';
        }
        if ($this->stock !== null && $this->stock <= 0) {
            return 'Out of stock';
        }
        return 'Live';
    }
}
