<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertLevel extends Model
{
    use HasFactory;

    public function psychologist()
    {
        return $this->hasMany(Psychologist::class);
    }

    public function plan()
    {
        return $this->hasMany(Plan::class);
    }


    public function mobilePlan()
    {
        return $this->hasMany(Plan::class)->select('id','package_id','duration_type_id','price','expert_level_id')->with('mobileOffers' , 'mobileDuration');
    }

}
