<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataContent extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'data_contents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'data_group_id',
        'image',
        'preference'
    ];

    public function group()
    {
        return $this->belongsTo(DataGroup::class);
    }

    public function getContentWithS3Url($assetName)
    {
        return (Storage::url(config('constants.mediaAssets.' . $assetName . '.folderName') . $this->content));
    }

    public function getImagewithS3Url($assetName) {
        return (Storage::url(config('constants.mediaAssets.' . $assetName . '.folderName') . $this->image));
    }
}
