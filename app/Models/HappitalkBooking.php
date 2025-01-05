<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Psychologist;
use App\Models\User;

class HappitalkBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'psychologist_id',
        'amount',
        'amount_after_deduction',
        'plan_id',
        'total_no_of_session',
        'remaining_session',
    ];


    public function psychologist(){
        return $this->belongsTo(Psychologist::class)->select('id','first_name','last_name','username','email','profile_picture','expert_level_id')->with('expertLevel','specialization');
    }


    public function user(){
        return $this->belongsTo(User::class);
    }


}
