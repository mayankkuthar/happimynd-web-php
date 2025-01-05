<?php

namespace App\Models;

use App\Models\ServiceType;
use App\Models\ServiceMetaData;
use App\Models\ServiceTypeGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OtherService extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function type()
    {
        return $this->belongsTo(ServiceTypeGroup::class, 'service_type_group_id');
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
        return (Storage::url(config('constants.mediaAssets.' . $assetName . '.folderName') . $this->thumbnail));
    }

    public function educationService()
    {
        return $this->hasOne(ServiceMetaData::class);
    }

    public function discountedPrice()
    {
        $discountAmount  = $this->discount;
        $price = $this->price;
        $discountedPrice = $price - (($discountAmount/100) * $price);

        return $discountedPrice;
    }
}