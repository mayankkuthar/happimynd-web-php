<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HappiselfUsersLastVisitSubCourseAndContent;
use Auth;

class HappiselfSubCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'happiself_course_id',
        'sub_course_name',
        'count_for_sequence',
        'deleted_at',
    ];

    // protected $appends = ['status'];


    public function happiselfCourse(){
        return $this->belongsTo(HappiselfCourse::class , 'happiself_course_id');
    }


    // public function getStatusAttribute(){
    //     $user = Auth::user();
    //     $users_complete_courses_detail =  HappiselfUsersLastVisitSubCourseAndContent::where('user_id' , $user->id)->where('happiself_sub_course_id' , $this->id)->first();
    //     if($users_complete_courses_detail){
    //         if($users_complete_courses_detail->is_complete_happiself_sub_course == 0){
    //             return 'ongoing';
    //         }
    //         else{
    //             return 'completed';
    //         }
    //     }
    //     else{
    //         return 'locked';
    //     }
    // }

    
}






