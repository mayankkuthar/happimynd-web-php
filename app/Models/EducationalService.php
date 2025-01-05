<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use App\Models\EducationServiceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EducationalService extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function type()
    {
        return $this->belongsTo(EducationServiceType::class, 'service_type_id');
    }

    public function author()
    {
        return $this->belongsTo(EducationServiceAuthor::class, 'education_service_author_id');
    }

    public function status()
    {
        return $this->publish_status == 1 ? 'Published':'Draft';
    }

    public function getThumbnailWithS3Url($assetName)
    {
        if(!$this->thumbnail){
            return null;
        }
        Log::info($assetName);
        Log::info(Storage::url(config('constants.mediaAssets.' . $assetName . '.folderName') . $this->thumbnail));
        return (Storage::url(config('constants.mediaAssets.' . $assetName . '.folderName') . $this->thumbnail));
    }
}