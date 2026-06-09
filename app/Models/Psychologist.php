<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

// use Exception;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Contracts\Auth\MustVerifyEmail;


// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
// use Illuminate\Support\Facades\Hash;
// use Tymon\JWTAuth\Contracts\JWTSubject;
// use Illuminate\Support\Facades\Http;

use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use App\Models\HappitalkBooking;

class Psychologist extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'psychologists';


    protected $casts = [
        'slot1' => 'array',
        'slot2' => 'array'
    ];



    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'age',
        'gender',
        'internation_cert',
        'highest_qualification',
        'summary',
        'profile_picture',
        'email',
        'password',
        'device_token',
        'meet_link',
        'commission_percentage',
        'price_per_session',
    ];


    protected $appends = [
        'total_earned',
        'to_be_shared',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    public function setSlot1Attribute($data)
    {
        $this->attributes['slot1'] = json_encode($data);
    }

    public function setSlot2Attribute($data)
    {
        $this->attributes['slot2'] = json_encode($data);
    }
    protected $minPricePerSession = "";

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function expertLevel()
    {
        return $this->belongsTo(ExpertLevel::class);
    }

    public function language()
    {
        return $this->belongsToMany(Language::class, 'psychologist_languages')
            ->using(PsychologistLanguage::class);
    }

    public function availability()
    {
        return $this->belongsToMany(PsychologistDateTimeSlots::class, 'psychologist_availabilities', null, 'psychologist_slot_id')
            ->using(PsychologistAvailability::class);
    }

    public function getAvailableSlots()
    {
        return $this->with(['availability' => function ($query) {
            $query->whereNull('user_id');
        }])->first();
    }

    public function specialization()
    {
        return $this->belongsToMany(Specialization::class, 'psychologist_specializations')
            ->using(PsychologistSpecialization::class);
    }

    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

    public function getS3ImageUrlAttribute()
    {
        return Storage::disk('s3')->url(config('constants.mediaAssets.psychologist.profilePicture.folderName') . '/' . $this->attributes['profile_picture']);
    }

    public function printLanguages()
    {
        $languages = $this->language()->pluck('name')->toArray();
        $languageCount = count($languages);
        if ($languageCount == 1) {
            return ucwords($languages[0]);
        }
        if ($languageCount == 2) {
            return ucwords($languages[0] . ' & ' . $languages[1]);
        }
        return ucwords(implode(', ', array_slice($languages, 0, -1)) . ' & ' . $languages[$languageCount - 1]);
    }

    public function printSpecializations()
    {
        $specializations = $this->specialization()->pluck('name')->toArray();
        $specializationCount = count($specializations);
        if ($specializationCount == 1) {
            return ucwords($specializations[0]);
        }
        if ($specializationCount == 2) {
            return ucwords($specializations[0] . ' & ' . $specializations[1]);
        }
        return ucwords(implode(', ', array_slice($specializations, 0, -1)) . ' & ' . $specializations[$specializationCount - 1]);
    }

    public function customPrice()
    {
        return $this->belongsToMany(Plan::class, 'psychologist_plan')->using(PsychologistPlan::class)->withPivot(['selling_price', 'cost_price', 'discount'])->as('psychologistCustomPrice');
    }

    public function hasCustomPrice()
    {
        return $this->customPrice->count() > 0;
    }

    public function getPsychologistPlans()
    {
        $plans = collect();

        $plans = $plans->merge($this->expertLevel->plan()->with('duration')->get());
        if ($this->hasCustomPrice()) {
            $plans = $plans->merge($this->customPrice()->with('duration')->get());
        }
        return $plans->keyBy('duration_type_id');
    }

    public function getMinimumSessionPrice()
    {
        $plans = $this->getPsychologistPlans();
        $min = PHP_INT_MAX;
        foreach ($plans as $plan) {
            $pricePerSession = $plan->getPerSessionSellingPrice();
            if ($min > $pricePerSession) {
                $min = $pricePerSession;
            }
        }
        $this->minPricePerSession = $min;
        return (int)$this->minPricePerSession;
    }
    public function verifyPsychologist()
    {
        return $this->hasOne(VerifyPsychologist::class);
    }



    public function mobileExpertLevel()
    {
        return $this->belongsTo(ExpertLevel::class , 'expert_level_id')->with('mobilePlan');
    }


    public function getTotalEarnedAttribute(){
        return $this->hasMany(HappitalkBooking::class)->sum('amount');
    }

    public function getToBeSharedAttribute(){
        return $this->hasMany(HappitalkBooking::class)->sum('amount_after_deduction');
    }

    

}
