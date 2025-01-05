<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardPointInstance extends Model
{
    use HasFactory;

    protected $fillable = [

        'action_performed',
        'points_to_be_given',

    ];  
    


}
