<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class ApplicationRateEmoji extends Model
{
    use HasFactory;

    protected $table = 'application_rate_emojis';


    protected $fillable = [
        'name',
        'image',
    ];



    public function getImageAttribute()
    {
        return Storage::url(config('constants.mediaAssets.happimynd_app_rating.folderName').''.$this->attributes['image']);
    }

}
