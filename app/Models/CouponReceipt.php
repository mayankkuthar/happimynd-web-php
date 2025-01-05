<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponReceipt extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function isPurchased()
    {
        //for 100% off coupon receipt is not generated
        //this check skips receipt fetch
        if ($this->coupon->discount_percent == 100) {
            return true;
        }
        return $this->receipt->status == 1;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
