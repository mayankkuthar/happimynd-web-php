<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ApplicationRateEmoji;


class HappitalkSessionOpinionUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'happitalk_session_id',
        'application_rate_emoji_id',
        'reason',
        'additional_comment',
    ];


    public function Emoji()
    {
        return $this->belongsTo(ApplicationRateEmoji::class , 'application_rate_emoji_id');
    }


}
