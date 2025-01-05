<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class OurClient extends Model
{
    use HasFactory;
    protected $fillable = [
        'id','name','image','preference'
    ];

    public function getImageWithS3Url()
    {

        if(!$this->image){
            return null;
        }

        return (Storage::url(config('constants.mediaAssets.client.folderName') . $this->image));
    }
}
