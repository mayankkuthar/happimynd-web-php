<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Plan extends Model
{
    use HasFactory;
    // protected $with = ['offer', 'duration'];
    protected $fillable = [
        'package_id',
        'duration_type_id',
        'price',
    ];

    public function isActive()
    {
        return ($this->attributes['active'] == 1);
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function CouponPlan()
    {
        return $this->hasMany(CouponPlan::class);
    }
    public function duration()
    {
        return $this->belongsTo(DurationType::class, 'duration_type_id', 'id');
    }

    public function offer()
    {
        return $this->hasOne(Offer::class);
    }

    public function bundleStatus()
    {
        return $this->hasMany(BundleStatus::class);
    }
    public function customPrice()
    {
        return $this->hasMany(PsychologistPlan::class, 'plan_id');
    }

    //TODO: remove this
    public function getSellingPriceAttribute()
    {
        //
        if ($this->psychologistCustomPrice) {
            return $this->psychologistCustomPrice->selling_price;
        }
        if ($this->offer) {
            return $this->offer->price;
        }
        return $this->price;
    }

    public function getSellingPrice()
    {
        if ($this->psychologistCustomPrice) {
            return $this->psychologistCustomPrice->selling_price;
        }
        if ($this->offer) {
            return $this->offer->price;
        }
        return $this->price;
    }

    public function getCostPrice()
    {
        if ($this->psychologistCustomPrice) {
            return $this->psychologistCustomPrice->cost_price;
        }
        return $this->price;
    }

    public function hasOffer()
    {
        return $this->offer ? true : false;
    }

    public function getDiscount()
    {
        if ($this->psychologistCustomPrice) {
            return $this->psychologistCustomPrice->discount;
        }
        if ($this->offer) {
            return $this->offer->discount;
        }
        return "";
    }

    public function getPerSessionSellingPrice()
    {
        return (int)($this->selling_price / $this->duration->frequency);
    }

    public function getSessionSellingPrice()
    {
        if ($this->psychologistCustomPrice) {
            return (int)$this->psychologistCustomPrice->selling_price;
        }
        return (int)$this->offer->price;
    }

    public function getSessionCostPrice()
    {
        if ($this->psychologistCustomPrice) {
            return $this->psychologistCustomPrice->cost_price;
        }
        return $this->price;
    }

    public function getSessionDuration()
    {
        if ($this->duration) {
            return $this->duration->frequency;
        }
        return "";
    }

    public function printDuration()
    {
        if ($this->isHappiTalkPlan()) {
            return $this->getSessionDuration() . ' Session';
        }
        return '';
    }

    public function forPsychologist(): bool
    {
        return $this->expertLevel != null;
    }

    public function expertLevel()
    {
        return $this->belongsTo(ExpertLevel::class);
    }

    public function expertLevelPlan()
    {
        return $this->belongsToMany(Plan::class, 'expert_level_plans')->using(ExpertLevelPlan::class);
    }

    public function isHappiTalkPlan()
    {
        return $this->package->name == "HappiTALK";
    }

    public function getCouponDiscountPrice($discount_percentage)
    {
        $price = $this->getSellingPrice();
        return round($price - ($price * ($discount_percentage / 100)), 2);
    }

    public function getSellingPriceWithDiscount($discount_code = '')
    {
        if (!empty($discount_code) && $this->couponPlan->count() > 0) {
            $couponPlan = $this->couponPlan()->whereHas('coupon', function ($query) use ($discount_code) {
                return $query->ActiveCoupon()->where('code', $discount_code);
            })->with('coupon')->first();
            if (!empty($couponPlan)) {
                return $this->getCouponDiscountPrice($couponPlan->coupon->discount_percent);
            }
        }
        return $this->getSellingPrice();
    }

 

    public function mobileOffers(){
        return $this->hasMany(Offer::class , 'plan_id')->select('id','discount','price','plan_id');
    }


    public function mobileExpertLevel()
    {
        return $this->belongsTo(ExpertLevel::class  , 'expert_level_id');
    }


    public function mobileDuration(){
        return $this->belongsTo(DurationType::class, 'duration_type_id')->select('id','type','value','frequency');
    }



}




