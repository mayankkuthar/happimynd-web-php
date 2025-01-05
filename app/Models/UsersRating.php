<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ApplicationRateEmoji;

class UsersRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_rate_emoji_id',
        'review',
    ];

    public function applicationRatingEmoji(){
        return $this->belongsTo(ApplicationRateEmoji::class , 'application_rate_emoji_id');
    }

}
