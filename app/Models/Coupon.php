<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['expired_at' => 'datetime'];

    public function couponPlan()
    {
        return $this->hasMany(CouponPlan::class);
    }

    public function couponReceipt()
    {
        return $this->hasMany(CouponReceipt::class);
    }

    public function user()
    {
        return $this->hasMany(CouponUser::class);
    }

    public function scopeActiveCoupon($query)
    {
        return $query->where('status', 1);
    }

    public function getStatus()
    {
        return ($this->status) ? 'Active' : 'In-Active';
    }
}
