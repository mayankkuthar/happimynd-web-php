<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Psychologist;
use App\Models\User;
use App\Models\HappitalkBooking;
use App\Models\HappitalkSessionOpinionUser;
use App\Models\HappitalkSessionOpinionPsychologist;

use Auth;

class HappitalkSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'happitalk_booking_id',
        'user_id',
        'user_type',
        'psychologist_id',
        'amount_per_session_psy',
        'date',
        'time',
        'start_time',
        'end_time',
        'room_id',
        'is_start',
        'is_end',
        'is_req_accepted',
        'req_rejected_reason',
        'is_cancel',
        'cancel_by',
        'cancel_reason',
        'is_notification_emit',
        'is_notification_emit_30_min_ago',
        'is_notification_emit_24_hour_ago',

        'is_user_join',
        'is_psy_join',

        'psy_joined_time',
        'psy_leave_time',

        'user_recording_permission',

    ];

    protected $appends = [ 
                        'is_give_feedback_by_user',
                        'is_give_feedback_by_psy',
                        'is_save_notes_of_session_by_psy'
                        ];


    public function psychologistDetail(){
        return $this->belongsTo(Psychologist::class , 'psychologist_id')->select('id','first_name','last_name','username','email','profile_picture' , 'device_token');
    }


    public function userDetail(){
        return $this->belongsTo(User::class , 'user_id');
    }


    public function bookingDetails(){
        return $this->belongsTo(HappitalkBooking::class , 'happitalk_booking_id');
    }

    public function userOpinion(){
        return $this->hasOne(HappitalkSessionOpinionUser::class , 'happitalk_session_id')->with('Emoji');
    }

    public function psychologistOpinion(){
        return $this->hasOne(HappitalkSessionOpinionPsychologist::class , 'happitalk_session_id');
    }


    public function getIsGiveFeedbackByUserAttribute(){
            return HappitalkSessionOpinionUser::where('happitalk_session_id' , $this->id)->count() == 0 ? 'no' :'yes';
    }

    public function getIsGiveFeedbackByPsyAttribute(){
            return HappitalkSessionOpinionPsychologist::where('happitalk_session_id' , $this->id)->count() == 0 ? 'no' :'yes';
    }


    public function getIsSaveNotesOfSessionByPsyAttribute(){
            return HappitalkNotesForUserByPsy::where('session_id' , $this->id)->count() == 0 ? 'no' :'yes';
    }



}
