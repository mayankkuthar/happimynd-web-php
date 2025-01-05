<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HappitalkPenaltyClause extends Model
{
    use HasFactory;

    protected $fillable = [
        'for_b2b_user_for_one_credit',
        'for_b2b_user_for_half_credit',

        'for_b2c_user_for_one_credit',
        'for_b2c_user_for_half_credit',

    ];

}
