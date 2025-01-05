<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PsychologistAppointment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];

    public function psychologist()
    {
        return $this->belongsTo(Psychologist::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function baseSessionCost()
    {
        $psychologist = $this->psychologist;
        $planCost = $psychologist->getPsychologistPlans();
        //$planCost = $planCost->filter(function($query){
        //    return ($query['duration_type_id'] == $this->session );
        //});
        $base_price = 0;
        foreach($planCost as $plan){
            if($plan['duration']['frequency'] == $this->sessions ){
                $base_price = $plan->getPerSessionSellingPrice();
                break;
            }
        }
        return $base_price;
    }
}
