<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'packages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'bundle',
        'duration_id',
        'regular_price',
        'package_id',
        'validity'
    ];

    public function plan()
    {
        return $this->hasMany(Plan::class);
    }

    public function offer()
    {
        return $this->hasOneThrough(Plan::class, Offer::class);
    }

    public function duration()
    {
        return $this->belongsTo(Duration::class);
    }

    public function getMinimumPricePlan()
    {
        $plans = $this->plan()->whereHas('expertLevel')->with('expertLevel')->get();
        $minPrice = null;
        foreach ($plans as $key2 => $plan) {
            if ($plan->expertLevel) {
                if ($minPrice == null) {
                    $minPrice = $plan;
                } else if ($plan->getPerSessionSellingPrice() < $minPrice->getPerSessionSellingPrice()) {
                    $minPrice = $plan;
                }
            }
        }
        return $minPrice;
    }




    public function mobilePlans(){
        return $this->hasMany(Plan::class , 'package_id')->select('id','package_id','duration_type_id','price','expert_level_id')->where('active',1)->with('mobileOffers','mobileExpertLevel','mobileDuration');
    }



    // public function getNameAttribute($value){
    //     if($value == 'HappiLIFE Summary Reading'){
    //         return "HappiLEARN";
    //     }else{
    //         return $value;
    //     }
    // }



}





