<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MoodMeterEmoji;

class UserMood extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'emoji_id',
        'text',
        'date',
        'time',
    ];

    public function emojiDetails(){
        return $this->belongsTo(MoodMeterEmoji::class , 'emoji_id');
    }
    
}
