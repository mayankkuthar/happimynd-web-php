<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponPlan extends Model
{
    use HasFactory;
    protected $table = 'coupon_plans';
    protected $guarded = [];
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class)->with('expertLevel');
    }
}
