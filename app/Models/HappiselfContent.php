<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;
use App\Models\HappiselfQuestionOption;
use App\Models\HappiselfUsersLastVisitSubCourseAndContent;

use Auth;

class HappiselfContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'happiself_course_id',
        'happiself_sub_course_id',
        'title',
        'description',
        'content_type',
        'media',
        'is_media_downloadable',
        'content',
        'correct_answer',
        'deleted_at',
    ];



    public function getContentAttribute($value){
        if($this->content_type == 'audio' || $this->content_type == 'video'){
            return Storage::url(config('constants.mediaAssets.happiself_course.folderName').''.$value);
        }else{
            return $value;
        }
    }
    


    public function getMediaAttribute($value){
        if($value){
            return Storage::url(config('constants.mediaAssets.happiself_course_media.folderName').''.$value);
        }
    }



    public function option(){
        return $this->hasMany(HappiselfQuestionOption::class , 'happiself_content_id');
    }




    public function courseName(){
        return $this->belongsTo(HappiselfCourse::class , 'happiself_course_id')->select('id' , 'course_name');
    }


    public function subCourseName(){
        return $this->belongsTo(HappiselfSubCourse::class , 'happiself_sub_course_id')->select('id' , 'sub_course_name');
    }


    
}






