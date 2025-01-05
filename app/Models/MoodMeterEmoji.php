<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class MoodMeterEmoji extends Model
{
    use HasFactory;

    protected $table = 'mood_meter_emojies';


    protected $fillable = [
        'name',
        'image',
    ];


    public function getImageAttribute()
    {
        // return url('public/assets/Mood-O-Meter').'/'.$this->attributes['image'];
        return Storage::url(config('constants.mediaAssets.mood_o_meter_emojies.folderName').''.$this->attributes['image']);
    }
    

}
