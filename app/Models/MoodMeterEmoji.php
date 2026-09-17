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
        $url = Storage::url(config('constants.mediaAssets.mood_o_meter_emojies.folderName').''.$this->attributes['image']);
        return $url.'?v='.strtotime($this->attributes['updated_at']);
    }
    

}
