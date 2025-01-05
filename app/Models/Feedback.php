<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ApplicationRateEmoji;


class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';


    protected $fillable = [
        'user_id',
        'application_rate_emoji_id',
        'feedback_message',
    ];


    public function user(){
        return $this->belongsTo(User::class)->select('id' , 'username' , 'nickname');
    }


    public function applicationRateEmoji(){
        return $this->belongsTo(ApplicationRateEmoji::class);
    }


}
