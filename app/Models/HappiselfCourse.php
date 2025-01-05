<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Models\HappiselfCourseLike;

class HappiselfCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'language',
        'course_name',
        'deleted_at',
    ];


    protected $appends = ['is_like'];

    public function likes(){
        return $this->hasMany(HappiselfCourseLike::class , 'happiself_course_id');
    }

    public function getIsLikeAttribute(){
        $user = Auth::user();
        return HappiselfCourseLike::where('happiself_course_id' , $this->id)->where('user_id', $user->id)->count() == 0 ? 'no' : 'yes'; 
    }

}
