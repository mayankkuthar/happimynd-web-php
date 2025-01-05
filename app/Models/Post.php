<?php

namespace App\Models;

use App\Models\PostCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory, SoftDeletes;

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $casts =[
        'publish_status' => 'boolean',
        'restricted_content' => 'boolean',
        'featured' => 'boolean',
    ];

    protected $appends = [
        'next',
        'previous'
    ];

    public function category(){
        return $this->belongsTo(PostCategory::class);
    }

    public function getContentWithS3Url($assetName)
    {
        return (Storage::url(config('constants.mediaAssets.' . $assetName . '.folderName') . $this->media));
    }

    public function getThumbnailWithS3Url($assetName)
    {
        if(!$this->thumbnail){
            return null;
        }
        return (Storage::url(config('constants.mediaAssets.' . $assetName . '.folderName') . $this->thumbnail));
    }

    public function getNextAttribute(){
        return $this->where('id' ,'>' ,$this->id)->where('post_category_id', $this->post_category_id)->orderBy('id', 'asc')->first();
    }

    public function getPreviousAttribute(){
        return $this->where('id' ,'<' ,$this->id)->where('post_category_id', $this->post_category_id)->orderBy('id', 'asc')->first();
    }

}
