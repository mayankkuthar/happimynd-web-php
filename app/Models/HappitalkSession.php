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

    // How many users can book the same psychologist slot. Change it here; the
    // slots API returns it as slot_capacity.
    const SLOT_CAPACITY = 20;

    // Sessions that still hold their slot: not cancelled, not rejected
    public function scopeHoldingSlot($query)
    {
        return $query->where('is_cancel', '!=', '1')->where('is_req_accepted', '!=', '2');
    }

    // Start and end of a slot like "10:00 AM - 10:30 AM" as timestamps, or
    // nulls when it can't be read
    public static function slotBounds($time)
    {
        $parts = explode('-', (string) $time);
        $start = strtotime('1970-01-01 ' . trim($parts[0]));
        $end = isset($parts[1]) ? strtotime('1970-01-01 ' . trim($parts[1])) : false;
        return ($start === false || $end === false) ? [null, null] : [$start, $end];
    }

    // The sessions (of one psychologist and date) that overlap the slot $time
    public static function overlappingSlot($sessions, $time)
    {
        [$start, $end] = self::slotBounds($time);
        return $sessions->filter(function ($session) use ($start, $end, $time) {
            if ($start === null) {
                return $session->time == $time;
            }
            $sessionStart = strtotime('1970-01-01 ' . $session->start_time);
            $sessionEnd = strtotime('1970-01-01 ' . $session->end_time);
            if ($sessionStart === false || $sessionEnd === false) {
                return $session->time == $time;
            }
            return $sessionStart < $end && $sessionEnd > $start;
        });
    }

    // Why $user_id can't book the psychologist's slot, or null when they can.
    // A slot takes up to SLOT_CAPACITY bookings, one per user. Rescheduling
    // passes the session being moved so it doesn't count against itself.
    public static function slotUnavailableReason($psychologist_id, $date, $time, $user_id, $except_session_id = null)
    {
        $sessions = self::where('psychologist_id', $psychologist_id)
            ->where('date', $date)
            ->holdingSlot()
            ->when($except_session_id, function ($query) use ($except_session_id) {
                return $query->where('id', '!=', $except_session_id);
            })
            ->get();
        $inSlot = self::overlappingSlot($sessions, $time);

        if ($inSlot->contains('user_id', $user_id)) {
            return "You have already booked this slot.";
        }
        if ($inSlot->count() >= self::SLOT_CAPACITY) {
            return "This slot is fully booked.";
        }
        return null;
    }

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
        return $this->belongsTo(Psychologist::class , 'psychologist_id')->select('id','first_name','last_name','username','email','profile_picture' , 'device_token', 'meet_link');
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
