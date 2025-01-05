<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRewardPointRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points_earned',
        'task_performed',
    ];

}
