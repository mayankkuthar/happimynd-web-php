<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Psychologist;
use App\Models\User;

use App\Models\HappiguideNotesForUserByPsy;

use App\Models\HappiguideSessionOpinionUser;
use App\Models\HappiguideSessionOpinionPsychologist;

class HappiguideSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'psychologist_id',
        'date',
        'time',
        'room_id',
        'is_start',
        'is_end', 

        'start_time',
        'end_time',
        'is_notification_emit',
    ];

    protected $appends = [
        'start_time',
        'end_time',
        'is_save_notes_of_session_by_psy'
    ];

    public function getstartTimeAttribute(){
        $explode_start_end_time = explode('-' , $this->time);
        return trim($explode_start_end_time[0]);
    }

    public function getendTimeAttribute(){
        $explode_start_end_time = explode('-' , $this->time);
        return trim($explode_start_end_time[1]);
    }

    public function getIsSaveNotesOfSessionByPsyAttribute(){
            return HappiguideNotesForUserByPsy::where('guide_session_id' , $this->id)->count() == 0 ? 'no' :'yes';
    }
    

    public function userDetail(){
        return $this->belongsTo(User::class , 'user_id')->select('id','username','email');
    }

    
    public function psychologistDetail(){
        return $this->belongsTo(Psychologist::class , 'psychologist_id')->select('id','first_name','last_name','username','email','profile_picture');
    }


    public function userOpinion(){
        return $this->hasOne(HappiguideSessionOpinionUser::class , 'happiguide_session_id')->with('Emoji');
    }

    public function psychologistOpinion(){
        return $this->hasOne(HappiguideSessionOpinionPsychologist::class , 'happiguide_session_id');
    }


}
