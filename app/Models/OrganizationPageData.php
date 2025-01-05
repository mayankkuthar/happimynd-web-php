<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrganizationPageData extends Model
{
    use HasFactory;
    protected $fillable = [
        'id','name','title','description','image_link'
    ];

    public function getImageWithS3Url($assetName)
    {

        if(!$this->image_link){
            return null;
        }

        return (Storage::url(config('constants.mediaAssets.' . $assetName . '.folderName') . $this->image_link));
    }
}
