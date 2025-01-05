<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RatingPictures extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'report_characteristic_id',
    ];

    protected $appends = [
        'image'
    ];

    public function getImageAttribute()
    {
        return Storage::url(config('constants.mediaAssets.ratingPicture.folderName') . '' . $this->name);
    }
}
