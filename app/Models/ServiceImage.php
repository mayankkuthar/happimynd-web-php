<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ServiceImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'id','overview','title','image_link'
    ];

    public function getImageWithS3Url($assetName)
    {

        if(!$this->image_link){
            return null;
        }

        return (Storage::url(config('constants.mediaAssets.' . $assetName . '.folderName') . $this->image_link));
    }
}
