<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserProfile;
use App\Models\LikeHappiLearnContent;
use App\Models\BundleStatus;

use Auth;
use Storage;

class HappiLearnContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'language',
        'type',
        'status',
        'title',
        'profile',
        'parameters',
        'keywords',
        'summary',
        'thumbnail',
        'link',
        'date_of_upload',
        'credit',
        'is_deleted',
    ];

    protected $appends = ['is_likes' , 'is_subscribe'];

    public function likes(){
        return $this->hasMany(LikeHappiLearnContent::class);
    }


    public function getIsSubscribeAttribute(){
        $user = Auth::user();
        if($user){
            $is_user_subscribe_learn = BundleStatus::where('user_id' ,$user->id)->where('plan_id' , 2)->NotExpired()->first();
            if($is_user_subscribe_learn){
                return 1;
            }else{
                return 0;
            }
        }
    }


    public function getIsLikesAttribute(){
        $user = Auth::user();
        return LikeHappiLearnContent::where(['user_id' => $user->id , 'happi_learn_content_id'=>$this->id])->count() == 0 ? 'no' : 'yes';
    }



    // If thumbnail is empty then display these 4 static thumbnail based in type else fetch thumbnail from Db and it should also uploaded on aws (happilearn_thumbnail)
    public function getThumbnailAttribute($value){
        if(!$value){
            if($this->type == 'image'){
                // return 'https://happimyndstagingbucket2.s3.ap-south-1.amazonaws.com/happilearn_thumbnail/static_thumbnail_for_image_with_bg.png';
                return Storage::url(config('constants.mediaAssets.happiLearn_thumbnail.folderName').'static_thumbnail_for_image_with_bg.png');
            }
            if($this->type == 'video'){
                // return 'https://happimyndstagingbucket2.s3.ap-south-1.amazonaws.com/happilearn_thumbnail/static_thumbnail_for_video_with_bg.png';
                return Storage::url(config('constants.mediaAssets.happiLearn_thumbnail.folderName').'static_thumbnail_for_video_with_bg.png');
            }
            if($this->type == 'blog'){
                // return 'https://happimyndstagingbucket2.s3.ap-south-1.amazonaws.com/happilearn_thumbnail/static_thumbnail_for_blog_with_bg.png';
                return Storage::url(config('constants.mediaAssets.happiLearn_thumbnail.folderName').'static_thumbnail_for_blog_with_bg.png');
            }
            if($this->type == 'infographics'){
                // return 'https://happimyndstagingbucket2.s3.ap-south-1.amazonaws.com/happilearn_thumbnail/static_thumbnail_for_infographics_with_bg.png';
                return Storage::url(config('constants.mediaAssets.happiLearn_thumbnail.folderName').'static_thumbnail_for_infographics_with_bg.png');
            }
        }else{
            // return 'https://happimyndstagingbucket2.s3.ap-south-1.amazonaws.com/happilearn_thumbnail/'.$value;
            return Storage::url(config('constants.mediaAssets.happiLearn_thumbnail.folderName')).$value;

        }
    }



    //There are four types in Happilearn . if there is video , infographics then directly get value from DB because we directly save Video link in DB. and for image it should be upload on aws(happiLearn_content) and save only name of these in DB and get by concatinate with url.
    public function getLinkAttribute($value){
        if($value){
            if($this->type == 'image' || $this->type == 'infographics'){
                // return 'https://happimyndstagingbucket2.s3.ap-south-1.amazonaws.com/happilearn_images_infographic/'.$value;
                return Storage::url(config('constants.mediaAssets.happiLearn_content.folderName').$value);

            }else{
                return $value;
            }
        }
    }



}
