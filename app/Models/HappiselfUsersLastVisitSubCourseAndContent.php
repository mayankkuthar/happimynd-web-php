<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HappiselfUsersLastVisitSubCourseAndContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'happiself_course_id',
        'happiself_sub_course_id',
        'is_complete_happiself_sub_course',
        'happiself_content_id',
        'is_complete_happiself_content',
    ];

    public function courseDetails(){
        return $this->belongsTo(HappiselfCourse::class , 'happiself_course_id');
    }

    public function subCourseDetails(){
        return $this->belongsTo(HappiselfSubCourse::class , 'happiself_sub_course_id');
    }

    public function userDetails(){
        return $this->belongsTo(User::class , 'user_id');
    }


}
