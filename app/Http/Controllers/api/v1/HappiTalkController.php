<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Psychologist;
use App\Models\City;
use App\Models\Specialization;
use App\Models\ExpertLevel;
use App\Models\Language;
use App\Models\PsychologistAvailability;
use App\Models\PsychologistDateTimeSlots;
use App\Models\PsychologistAppointment;
use App\Models\HappitalkSession;
use App\Models\HappitalkBooking;
use App\Models\UserToken;
use App\Models\Token;
use App\Models\Organization;
use App\Models\ApplicationRateEmoji;
use App\Models\HappitalkSessionOpinionUser;
use App\Models\NotificationList;
use App\Models\User;
use App\Models\HappitalkSessionOpinionPsychologist;
use App\Models\AssignPsyToOrgForTalk;
use App\Models\BundleStatus;
use App\Models\HappitalkNotesForUserByPsy;
use App\Models\AssignPsyToPlan;
use App\Models\Plan;
use App\Models\HappitalkSessionComposition;
use App\Models\HappitalkPenaltyClause;
use App\Models\CouponReceipt;

use Carbon\Carbon;
use DateTime;
use Symfony\Component\HttpFoundation\Response;

use Http;
use Auth;
use Illuminate\Support\Facades\Storage;
use Validator;
use DB;

use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Rest\Client;

use App\BusinessModel\PushNotification;

use App\BusinessModel\CreateTwillioRoom;



class HappiTalkController extends Controller
{

    public function pushNotification(){
        return new PushNotification();
    }

    public function createTwillioRoom(){
        return new CreateTwillioRoom();
    }

    
    public function psychologistListing(Request $request){

        $user = Auth::user();

        $psychologists = new Psychologist();
        $shouldGetAll = true;

        $psychologists = $psychologists->whereNotNull('slot1');
        if ($request->has('search')) {
            $shouldGetAll = false;
            $psychologists = $psychologists
                ->where(function ($query) use ($request) {
                    $query->where('first_name', 'like', '%' . $request->search . '%')
                        ->orWhere('last_name', 'like', '%' . $request->search . '%')
                        ->orWhereHas('language', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        })
                        ->orWhereHas('city', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        })
                        ->orWhereHas('expertLevel', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        })
                        ->orWhereHas('specialization', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        });
                });
        }
        if ($request->has('city')) {
            $explode_city_name = explode(',',$request->city);
            $shouldGetAll = false;
            $psychologists = $psychologists->whereHas('city', function ($query) use ($explode_city_name) {
                $query->whereIn('name', $explode_city_name);
            });
        }
        if ($request->has('expert_category')) {
            $explode_expert_category = explode(',',$request->expert_category);
            $shouldGetAll = false;
            $psychologists = $psychologists->whereHas('expertLevel', function ($query) use ($explode_expert_category) {
                $query->whereIn('name', $explode_expert_category);
            });
        }
        if ($request->has('specialization')) {
            $explode_specialization = explode(',',$request->specialization);
            $shouldGetAll = false;
            $psychologists = $psychologists->whereHas('specialization', function ($query) use ($explode_specialization) {
                $query->wherein('name', $explode_specialization);
            });
        }
        if ($request->has('language')) {
            $explode_language = explode(',',$request->language);
            $shouldGetAll = false;
            $psychologists = $psychologists->whereHas('language', function ($query) use ($explode_language) {
                $query->whereIn('name', $explode_language);
            });
        }


        // if ($shouldGetAll) {
        //     $psychologists = Psychologist::whereNotNull('slot1');
        // }


        $is_user_from_org = UserToken::where('user_id' , $user->id)->first();
        if($is_user_from_org){
            $token_detail = Token::where('id' , $is_user_from_org->token_id)->first();
            $org_detail = Organization::where('id' , $token_detail->organization_id)->first();
            $psy_id_to_be_show_based_on_org = AssignPsyToOrgForTalk::where('organization_id',$token_detail->organization_id)->pluck('psychologist_id');

            $total_sesssions = auth()->user()->getOrganizationHappiTalkSessions();
            if($total_sesssions > 0){

                // return 56;
                // $user_subscribed_plans = BundleStatus::where('user_id' ,$user->id)->pluck('plan_id');
                // $packages_based_on_plan_ids = Plan::whereIn('id', $user_subscribed_plans)->pluck('package_id')->toArray();

                // if(in_array('6' , $packages_based_on_plan_ids)){

                $is_already_buy_talk = HappitalkBooking::where('user_id' , $user->id)->first();
                if($is_already_buy_talk){
                    //user name individual and org null because use already avail service
                    $user_detail = [
                        'user_from' => 'individual',
                        'organization_name' => null,
                    ];
                    $psychologists = $psychologists->whereIn('id', $psy_id_to_be_show_based_on_org )->whereNotNull('slot1');
                }else{
                    $user_detail = [
                        'user_from' => 'organization',
                        'organization_name' => $org_detail->name,
                        'total_sesssions' => $total_sesssions,
                    ];
                    $psychologists = $psychologists->whereIn('id', $psy_id_to_be_show_based_on_org )->whereNotNull('slot1');
                }
            }else{
                //user name individual and org null because org not buy happitalk
                $user_detail = [
                    'user_from' => 'individual',
                    'organization_name' => null,
                ];
                $psychologists = $psychologists->whereIn('id', $psy_id_to_be_show_based_on_org )->whereNotNull('slot1');
            }
        }else{
            $user_detail = [
                'user_from' => 'individual',
                'organization_name' => null,
            ];
            $talk_psy_ids = AssignPsyToPlan::where('plan_name' , 'HappiTalk')->pluck('psychologist_id')->toArray();
            $psychologists = $psychologists->whereIn('id' , $talk_psy_ids)->whereNotNull('slot1');
        }
        

        $psychologists = $psychologists->where('deleted_at' , null)->select('id','first_name','last_name','username','email','summary','profile_picture','gender','city_id','expert_level_id','slot1','slot2')->with('language:id,name', 'mobileExpertLevel:id,name', 'city:id,name', 'specialization:id,name')->get();

        foreach($psychologists as $row){
           $image_with_path = Storage::disk('s3')->url(config('constants.mediaAssets.psychologist.profilePicture.folderName') . '/' . $row['profile_picture']);
           // $row->profile_picture = $image_with_path;
           $row->psy_profile = $image_with_path;

        } 

        return response()->json(['status' => 'success' , 'message' => 'Psychologist list get successfully.' ,'user_detail' => $user_detail, 'list' => $psychologists]);
    }

    public function getSlotsOfPsy(Request $request){
        $message = [
            'psychologist_id.required'   =>  'Please enter psychologist ID.',
        ];
        $validator = Validator::make($request->all(), [
            'psychologist_id'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $current_date = Date('Y-m-d'); 
        $psychologist = Psychologist::where('id' , $request->psychologist_id)->first();

        // return $psychologist->availability;
        $slot_dates =  $psychologist->availability->where('date','>=',$current_date)->pluck('date')->toArray();
        $slot_dates =  array_values(array_unique($slot_dates));


        $availableSlots = $psychologist->availability->where('date','>=',$current_date)->groupBy('date')->map(function ($slots) {
            $times = [];
            foreach ($slots as $k => $slot) {
                $times['time'][$k] = $slot->time;
                // array_push($times, $slot->time);
            }
            return $times;
        });

        return response()->json(['status' => 'success' , 'message' => 'Slots get successfully.' , 'slot_dates' => $slot_dates , 'slot_dates_with_time' => $availableSlots]);

    }

    public function psychologistCity(){
        $city = City::where('deleted_at',null)->get();
        return response()->json(['status' => 'success' , 'message' => 'Language list get successfully.' , 'city' => $city]);
    }

    public function psychologistSpecialization(){
        $specialization = Specialization::where('deleted_at' , null)->get();
        return response()->json(['status' => 'success' , 'message' => 'Language list get successfully.' , 'specialization' => $specialization]);
    }

    public function psychologistExpertCategory(){
        $expert_level = ExpertLevel::where('deleted_at',null)->get();
        return response()->json(['status' => 'success' , 'message' => 'Language list get successfully.' , 'expert_level' => $expert_level]);
    }

    public function psychologistLanguage(){
        $language = Language::where('deleted_at' , null)->get();
        return response()->json(['status' => 'success' , 'message' => 'Language list get successfully.' , 'language' => $language]);
    }




    public function myBookingUser(Request $request){
        $user = Auth::user();
        $current_date = Date('Y-m-d');


        $message = [
            'type.required'  =>  'Please enter type.',
        ];
        $validator = Validator::make($request->all(), [
            'type'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $type = $request->type;

        if($type == 'past'){
            $sessions = HappitalkSession::where('user_id' , $user->id)->where('date', '<' , $current_date)->with('psychologistDetail','bookingDetails')->orderBy('date' , 'asc')->orderBy('time' , 'asc')->get();
        }
        if($type == 'today'){
            $sessions = HappitalkSession::where('user_id' , $user->id)->where('date', '=' , $current_date)->with('psychologistDetail','bookingDetails')->orderBy('date' , 'asc')->orderBy('time' , 'asc')->get();
        }
        if($type == 'future'){
            $sessions = HappitalkSession::where('user_id' , $user->id)->where('date', '>' , $current_date)->with('psychologistDetail','bookingDetails')->orderBy('date' , 'asc')->orderBy('time' , 'asc')->get();
        }

        foreach($sessions as $single_session){
            // $single_session->psychologistDetail['profile_picture'] = Storage::disk('s3')->url(config('constants.mediaAssets.psychologist.profilePicture.folderName') . '/' . $single_session->psychologistDetail['profile_picture']);

            $single_session->psychologistDetail->psy_profile = Storage::disk('s3')->url(config('constants.mediaAssets.psychologist.profilePicture.folderName') . '/' . $single_session->psychologistDetail['profile_picture']);
        }

        return response()->json(['status' => 'success' , 'message' => 'Session get successfully.' , 'session_detail' => $sessions]);
    }

    public function rescheduleBookingUser(Request $request){

        $message = [
            'session_id.required'   =>  'Please enter session ID.',
            // 'psychologist_id.required'   =>  'Please enter psychologist ID.',
            'date.required'   =>  'Please enter date.',
            'time.required'   =>  'Please enter time.',

        ];
        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
            // 'psychologist_id'   => 'required',
            'date'         => 'required',
            'time'         => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $session_id = $request->session_id;
        $date = $request->date;
        $time = $request->time;

        $session_details = HappitalkSession::where('id' , $session_id)->first();
        $psychologist_id = $session_details->psychologist_id;

        if($session_details->is_cancel == '1'){
            return response()->json(['status' => 'error' , 'message' => "This session has already been cancelled."]);
        }
        
        $reschedule_at_same_date_time = HappitalkSession::where('id' ,$session_id)->where('date',$date)->where('time',$time)->first();
        if($reschedule_at_same_date_time){
            return response()->json(['status' => 'error' , 'message' => 'You are rescheduling the booking for the same time again. Please change the time slot.']);
        }
        
        // $is_there_any_session_on_this_time = HappitalkSession::where('psychologist_id' , $psychologist_id)->where('date' , $date)->where('time', $time)->where('is_cancel' , 0)->first();
        // if($is_there_any_session_on_this_time){
        //     return response()->json(['status' => 'error' , 'message' => "This slot is already booked."]);
        // }
        $is_there_any_pending_session_req_at_this_time = HappitalkSession::where('psychologist_id' , $psychologist_id)->where('date' , $request->date)->where('time', $request->time)->where('is_req_accepted','0')->first();
        if($is_there_any_pending_session_req_at_this_time){
            return response()->json(['status' => 'error' , 'message' => "This slot is already booked."]);
        }
        $is_there_any_accepted_session_and_not_cancel_req_at_this_time = HappitalkSession::where('psychologist_id' , $psychologist_id)->where('date' , $request->date)->where('time', $request->time)->where('is_req_accepted','1')->where('is_cancel' , '0')->first();
        if($is_there_any_accepted_session_and_not_cancel_req_at_this_time){
            return response()->json(['status' => 'error' , 'message' => "This slot is already booked."]);
        }

        $explode_start_end_time = explode('-' ,$time ) ;
        $requested_start_time = rtrim($explode_start_end_time[0]);
        $requested_end_time = ltrim($explode_start_end_time[1]);

        $update_data = [
            'date' => $date,
            'time' => $time,
            'start_time' => $requested_start_time,
            'end_time' => $requested_end_time,
            'is_req_accepted' => 0
        ];
        HappitalkSession::where('id' , $request->session_id)->update($update_data);

        $psychologist_details = Psychologist::where('id' , $psychologist_id)->first();
        $message = "Your appointment has been rescheduled by the user. New schedule (".$date." ".$time.")";

        if($psychologist_details->device_token && strlen($psychologist_details->device_token) > 20){
            $device_token = $psychologist_details->device_token;
            $this->sendNotification($device_token,$message);
        }

        return response()->json(['status' => 'success' , 'message' => "The booking was rescheduled successfully."]);

    }

    public function cancelBookingUser(Request $request){

        $message = [
            'session_id.required'   =>  'Please enter session ID.',
            'cancel_reason.required'   =>  'Please enter cancel reason.',

        ];
        $validator = Validator::make($request->all(), [
            'session_id'   => 'required|exists:happitalk_sessions,id',
            'cancel_reason' => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $session_details = HappitalkSession::where('id' , $request->session_id)->first();

        // if($session_details->is_cancel == '1'){
        //     return response()->json(['status' => 'error' , 'message' => "This session has already been cancelled."]);
        // }


        
        //Get penality details
        $penalty_clause_detail = HappitalkPenaltyClause::first();

        if($penalty_clause_detail){

            $session_start_time_with_date = $session_details->date.' ' .$session_details->start_time;

            if($session_details->user_type == 'b2c'){
                $new_time_for_one_credit = date('Y-m-d h:i A', strtotime($session_start_time_with_date. ' -'.$penalty_clause_detail->for_b2c_user_for_one_credit.' hours'));
                $new_time_for_half_credit = date('Y-m-d h:i A', strtotime($session_start_time_with_date. ' -'.$penalty_clause_detail->for_b2c_user_for_half_credit.' hours'));

            }else{
                $new_time_for_one_credit = date('Y-m-d h:i A', strtotime($session_start_time_with_date. ' -'.$penalty_clause_detail->for_b2b_user_for_one_credit.' hours'));
                $new_time_for_half_credit = date('Y-m-d h:i A', strtotime($session_start_time_with_date. ' -'.$penalty_clause_detail->for_b2b_user_for_half_credit.' hours'));

            }   


            $current_time = date('Y-m-d h:i A');

            $new_timeStamp_for_one_credit = Carbon::createFromFormat('Y-m-d h:i A', $new_time_for_one_credit);
            $new_timeStamp_for_half_credit = Carbon::createFromFormat('Y-m-d h:i A', $new_time_for_half_credit);
            $current_timeStamp = Carbon::createFromFormat('Y-m-d h:i A', $current_time);

            // Compare the two times 
            if ($new_timeStamp_for_one_credit > $current_timeStamp) {
                $booking_deatils = HappitalkBooking::where('id' , $session_details->happitalk_booking_id)->first();
                $booking_deatils->remaining_session = $booking_deatils->remaining_session+1;
                $booking_deatils->save();
            }

            if ($new_timeStamp_for_one_credit < $current_timeStamp && $new_timeStamp_for_half_credit > $current_timeStamp) {
                $booking_deatils = HappitalkBooking::where('id' , $session_details->happitalk_booking_id)->first();
                $booking_deatils->remaining_session = $booking_deatils->remaining_session+ 0.5;
                $booking_deatils->save();
            }

        }else{
            // If no data in penalty then give credit
            $booking_deatils = HappitalkBooking::where('id' , $session_details->happitalk_booking_id)->first();
            $booking_deatils->remaining_session = $booking_deatils->remaining_session+1;
            $booking_deatils->save();
        }

        
        //Notfication to psychologist
        $psychologist_details = Psychologist::where('id' , $session_details->psychologist_id)->first();
        $message = 'Your appointment has been canceled by the user.';
        if($psychologist_details->device_token && strlen($psychologist_details->device_token) > 20){
            $device_token = $psychologist_details->device_token;
            $this->sendNotification($device_token,$message);
        }

        //Update cancel status
        HappitalkSession::where('id' , $request->session_id)->update(['is_cancel' => '1' , 'cancel_by' => 'user', 'cancel_reason' => $request->cancel_reason]);


        return response()->json(['status' => 'success' , 'message' => "Booking has been cancelled successfully."]);
    }

    public function listToBookAnotherSessionUser(Request $request){
        $user = Auth::user();
        $data = HappitalkBooking::where('user_id' , $user->id)->where('remaining_session' , '!=' , 0)->with('psychologist')->get();

        foreach($data as $row){
           $image_with_path = Storage::disk('s3')->url(config('constants.mediaAssets.psychologist.profilePicture.folderName') . '/' . $row->psychologist->profile_picture);
           // $row->psychologist->profile_picture = $image_with_path;
           $row->psychologist->psy_profile = $image_with_path;
        } 

        return response()->json(['status' => 'success' , 'message' => 'Session get successfully.' , 'booking_details' => $data]);
    }

    public function bookAnotherSessionUser(Request $request){

        $user = Auth::user();

        $message = [
            'booking_id.required'   =>  'Please enter booking ID.',
            'date.required'   =>  'Please enter date.',
            'time.required'   =>  'Please enter time.',
        ];

        $validator = Validator::make($request->all(), [
            'booking_id'   => 'required',
            'date'   => 'required',
            'time'   => 'required',
            'user_recording_permission'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $booking = HappitalkBooking::where('id' , $request->booking_id)->first();

        $explode_start_end_time = explode('-' ,$request->time ) ;
 
        $requested_start_time = rtrim($explode_start_end_time[0]);
        $check_start_time_exist_in_any_booked_slot =  HappitalkSession::
                                where('psychologist_id' , $booking->psychologist_id)
                                ->where('date' , $request->date)
                                ->whereRaw("STR_TO_DATE(CONCAT('1970-01-01 ', start_time), '%Y-%m-%d %h:%i %p') < STR_TO_DATE(CONCAT('1970-01-01 ', '$requested_start_time'), '%Y-%m-%d %h:%i %p')")
                                ->whereRaw("STR_TO_DATE(CONCAT('1970-01-01 ', end_time), '%Y-%m-%d %h:%i %p') > STR_TO_DATE(CONCAT('1970-01-01 ', '$requested_start_time'), '%Y-%m-%d %h:%i %p')")
                                ->where('is_req_accepted' , '!=' , '2')
                                ->where('is_cancel' , '!=' , '1')
                                ->first();

        if($check_start_time_exist_in_any_booked_slot){
            return response()->json(['status' => 'error' , 'message' => "This slot is not available."]);
        }

        $requested_end_time = ltrim($explode_start_end_time[1]);
        $check_end_time_exist_in_any_booked_slot =  HappitalkSession::
                                where('psychologist_id' , $booking->psychologist_id)
                                ->where('date' , $request->date)
                                ->whereRaw("STR_TO_DATE(CONCAT('1970-01-01 ', start_time), '%Y-%m-%d %h:%i %p') < STR_TO_DATE(CONCAT('1970-01-01 ', '$requested_end_time'), '%Y-%m-%d %h:%i %p')")
                                ->whereRaw("STR_TO_DATE(CONCAT('1970-01-01 ', end_time), '%Y-%m-%d %h:%i %p') > STR_TO_DATE(CONCAT('1970-01-01 ', '$requested_end_time'), '%Y-%m-%d %h:%i %p')")
                                ->where('is_req_accepted' , '!=' , '2')
                                ->where('is_cancel' , '!=' , '1')
                                ->first();

        if($check_end_time_exist_in_any_booked_slot){
            return response()->json(['status' => 'error' , 'message' => "This slot is not available."]);
        }

        $is_there_any_pending_session_req_at_this_time = HappitalkSession::where('psychologist_id' , $booking->psychologist_id)->where('date' , $request->date)->where('time', $request->time)->where('is_req_accepted','0')->first();
        if($is_there_any_pending_session_req_at_this_time){
            return response()->json(['status' => 'error' , 'message' => "This slot is already booked."]);
        }
        $is_there_any_accepted_session_and_not_cancel_req_at_this_time = HappitalkSession::where('psychologist_id' , $booking->psychologist_id)->where('date' , $request->date)->where('time', $request->time)->where('is_req_accepted','1')->where('is_cancel' , '0')->first();
        if($is_there_any_accepted_session_and_not_cancel_req_at_this_time){
            return response()->json(['status' => 'error' , 'message' => "This slot is already booked."]);
        }

        // $unique_room_name = Date('Y-m-d_h:i:s').'_'.rand('0000000','9999999');

        // $sid = env('TWILIO_ACCOUNT_SID');
        // $token = env('TWILIO_ACCOUNT_TOKEN');
        // $twilio = new Client($sid, $token);

        // $room = $twilio->video->v1->rooms
        //                   ->create([
        //                                "recordParticipantsOnConnect" => True,
        //                                "statusCallback" => "www.google.com",
        //                                "type" => "group",
        //                                "uniqueName" => $unique_room_name,
        //                                'ttl' => 0,
        //                            ]
        //                   );

        $time = str_replace(' ' , '',$request->time);
        $explode_time = explode('-',$time);
        $full_start_time = $explode_time[0];
        $split_start_time =  preg_split('#(?<=\d)(?=[a-z])#i', $full_start_time);
        $full_end_time = $explode_time[1];
        $split_end_time =  preg_split('#(?<=\d)(?=[a-z])#i', $full_end_time);
        $exact_time = $split_start_time[0].' '.$split_start_time[1].' '.'-'.' '.$split_end_time[0].' '.$split_end_time[1];

        $psychologist_details = Psychologist::where('id' , $booking->psychologist_id)->first();

        if($booking->user_type == 'b2b'){
            $data = [
                'happitalk_booking_id' => $request->booking_id,
                'user_id' => $user->id,
                'user_type' => 'b2b',
                'psychologist_id' => $booking->psychologist_id,
                'amount_per_session_psy' => $psychologist_details->price_per_session,
                'date' => $request->date,
                'time' => $request->time,
                'start_time' => $split_start_time[0].' '.$split_start_time[1],
                'end_time' => $split_end_time[0].' '.$split_end_time[1],
                // 'room_id' => $room->sid,
                'user_recording_permission' => $request->user_recording_permission,
            ];  
        }else{
            $data = [
                'happitalk_booking_id' => $request->booking_id,
                'user_id' => $user->id,
                'user_type' => 'b2c',
                'psychologist_id' => $booking->psychologist_id,
                'date' => $request->date,
                'time' => $request->time,
                'start_time' => $split_start_time[0].' '.$split_start_time[1],
                'end_time' => $split_end_time[0].' '.$split_end_time[1],
                // 'room_id' => $room->sid,
                'user_recording_permission' => $request->user_recording_permission,
            ]; 
        }

        HappitalkSession::create($data);

        $booking->remaining_session = $booking->remaining_session-1;
        $booking->save();

        $message = 'New appointment has been schedule.';
        if($psychologist_details->device_token && strlen($psychologist_details->device_token) > 20){
            $device_token = $psychologist_details->device_token;
            $this->sendNotification($device_token,$message);
        }

        return response()->json(['status' => 'success' , 'message' => "Session request sent to psychologist. We will let you know once accepted." , 'details' => $data]);
    }

    public function emojiAndReasonList(Request $request){
        $emoji_list = ApplicationRateEmoji::get();
        $reason_list = [ 
                        'Didnot explore concern.',
                        'Didnot feel understood.',
                        'Average.',
                        'Felt understood and happy.',
                        'Felt great. Will book again.'
                        ];
        return response()->json(['status' => 'success' , 'message' => "Data get successfully." , 'emoji_list' => $emoji_list , 'reason_list' =>$reason_list]);

    }

    public function submitOpinionAfterSessionUser(Request $request){
        $user = Auth::user();

        $message = [
            'session_id.required'   =>  'Please enter session ID.',
        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $emoji_id = $request->emoji_id;
        $reason = $request->reason;
        $additional_comment = $request->additional_comment;


        $is_already = HappitalkSessionOpinionUser::where('happitalk_session_id' , $request->session_id)->first();

        if($is_already){
            return response()->json(['status' => 'error' , 'message' => "Opinion already submitted."]);
        }
        
        $data = [
            'user_id' =>$user->id,
            'happitalk_session_id' =>$request->session_id,
            'application_rate_emoji_id' => $request->emoji_id,
            'reason' => $request->reason,
            'additional_comment' => $request->additional_comment,
        ];

        HappitalkSessionOpinionUser::create($data);

        return response()->json(['status' => 'success' , 'message' => "Opinion submit successfully."]);
    }



    public function myBookingPsychologist(Request $request){
        $psy = Auth::guard('psychologist')->user();
        $current_date = Date('Y-m-d');
        $message = [
            'type.required'  =>  'Please enter type.',
        ];
        $validator = Validator::make($request->all(), [
            'type'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $type = $request->type;

        $search = $request->search;

        if($type == 'past'){
            // $sessions =  HappitalkSession::where('psychologist_id' , $psy->id)->where('date' ,'<', $current_date)->orderBy('date' , 'asc')->orderBy('time' , 'asc')->with('userDetail')->paginate('50');

            $sessions =  HappitalkSession::where('psychologist_id' , $psy->id)
                            ->where('date' ,'<', $current_date)
                            // ->orderBy('is_req_accepted', 'asc')
                            // ->orderBy('date' , 'asc')
                            ->orderByRaw("STR_TO_DATE(CONCAT(`date`, ' ', `start_time`), '%Y-%m-%d %h:%i %p') ASC")
                            ->join('users', 'users.id', '=', 'happitalk_sessions.user_id')
                            ->Where('nickname', 'like', '%' . $search . '%')
                            ->with('userDetail')
                            ->select(['happitalk_sessions.*' , 'users.nickname'])
                            ->paginate('20');
        }
        if($type == 'today'){
            $sessions =  HappitalkSession::where('psychologist_id' , $psy->id)
                            ->where('date' , '=', $current_date)
                            // ->orderBy('is_req_accepted', 'asc')
                            ->orderByRaw("STR_TO_DATE(CONCAT(`date`, ' ', `start_time`), '%Y-%m-%d %h:%i %p') ASC")
                            ->join('users', 'users.id', '=', 'happitalk_sessions.user_id')
                            ->Where('nickname', 'like', '%' . $search . '%')
                            ->with('userDetail')
                            ->select(['happitalk_sessions.*' , 'users.nickname'])
                            ->paginate('20');
        }
        if($type == 'future'){
            $sessions =  HappitalkSession::where('psychologist_id' , $psy->id)
                            ->where('date' , '>', $current_date)
                            // ->orderBy('is_req_accepted', 'asc')
                            // ->orderBy('date' , 'asc')
                            ->orderByRaw("STR_TO_DATE(CONCAT(`date`, ' ', `start_time`), '%Y-%m-%d %h:%i %p') ASC")
                            ->join('users', 'users.id', '=', 'happitalk_sessions.user_id')
                            ->Where('nickname', 'like', '%' . $search . '%')
                            ->with('userDetail')
                            ->select(['happitalk_sessions.*' , 'users.nickname'])
                            ->paginate('20');
        }

        foreach($sessions as $single_session){
            $user_id =  $single_session->userDetail->id;
            $is_user_from_org = UserToken::where('user_id' , $user_id)->first();
            if($is_user_from_org){
                $token_detail = Token::where('id' , $is_user_from_org->token_id)->first();
                $organization_detail = Organization::where('id' , $token_detail->organization_id)->first();
                $single_session->userDetail['user_from'] = 'organization';
                $single_session->userDetail['organization_name'] = $organization_detail->name;
            }else{
                $single_session->userDetail['user_from'] = 'individual';
                $single_session->userDetail['organization_name'] = null;
            }
        }
        return response()->json(['status' => 'success' , 'message' => 'Session get successfully.' , 'session_detail' => $sessions]);
    }


    public function myPendingRequestPsychologist(Request $request){
        $psy = Auth::guard('psychologist')->user();
        $sessions =  HappitalkSession::where('psychologist_id' , $psy->id)->where('is_req_accepted' , 0)->orderBy('date' , 'asc')->orderBy('time' , 'asc')->with('userDetail')->get();

        foreach($sessions as $single_session){
            $user_id =  $single_session->userDetail->id;
            $is_user_from_org = UserToken::where('user_id' , $user_id)->first();
            if($is_user_from_org){
                $token_detail = Token::where('id' , $is_user_from_org->token_id)->first();
                $organization_detail = Organization::where('id' , $token_detail->organization_id)->first();
                $single_session->userDetail['user_from'] = 'organization';
                $single_session->userDetail['organization_name'] = $organization_detail->name;
            }else{
                $single_session->userDetail['user_from'] = 'individual';
                $single_session->userDetail['organization_name'] = null;
            }
        }
        return response()->json(['status' => 'success' , 'message' => 'Pending session request get successfully.' , 'session_detail' => $sessions]);
    }



    public function myAllSlotsPsychologist(Request $request){
        $psy = Auth::guard('psychologist')->user();
        $psychologist = Psychologist::where('id' , $psy->id)->first();

        // return $psychologist->availability;
        $slot_dates =  $psychologist->availability->pluck('date')->toArray();
        $slot_dates =  array_values(array_unique($slot_dates));


        $availableSlots = $psychologist->availability->groupBy('date')->map(function ($slots) {
            $times = [];
            foreach ($slots as $k => $slot) {
                $times['time'][$k] = $slot->time;
                // array_push($times, $slot->time);
            }
            return $times;
        });

        return response()->json(['status' => 'success' , 'message' => 'Slots get successfully.' , 'slot_dates' => $slot_dates , 'slot_dates_with_time' => $availableSlots]);
    }


    
    public function getSlotsOfPerticularDatePsy(Request $request){
        $psy = Auth::guard('psychologist')->user();
        $message = [
            'date.required'   =>  'Please enter date.',
            'time.required'   =>  'Please enter time.',
        ];

        $validator = Validator::make($request->all(), [
            'date'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }


        $psychologist = Psychologist::where('id' , $psy->id)->first();

        $availableSlots = $psychologist->availability->where('date' , $request->date)->groupBy('date')->map(function ($slots) {
            $times = [];
            foreach ($slots as $k => $slot) {
                $times['time'][$k] = $slot->time;
                // array_push($times, $slot->time);
            }
            return $times;
        });

        return response()->json(['status' => 'success' , 'message' => 'Slots get successfully.' , 'slot_dates_with_time' => $availableSlots]);

    }


    public function sessionMarkAsCompletePsy(Request $request){

        $psy = Auth::guard('psychologist')->user();

        $message = [
            'session_id.required'   =>  'Please enter session ID.',
        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $current_time = Date('Y-m-d h:i:s A');

        $session_details = HappitalkSession::where('id' , $request->session_id)->with('userDetail','psychologistDetail')->first();
        HappitalkSession::where('id' , $request->session_id)->update(['is_end' => 1 ,'psy_leave_time' => $current_time ]);

        // Notification to user
        $message = "Your session with your expert has ended. Don't forget to tell us about your experience. Also, explore session packages and get great discounts.";

        if($session_details->userDetail->device_token && strlen($session_details->userDetail->device_token) > 20){
            $device_token = $session_details->userDetail->device_token;
            $title = "Session done";

            $this->pushNotification()->sendNotification($device_token, $message, $title);
        }
        $data = [
                    'user_id' => $session_details->userDetail->id,
                    'message' => $message, 
                ];
        NotificationList::create($data);

        //Notification to psy
        if($session_details->psychologistDetail->device_token && strlen($session_details->psychologistDetail->device_token) > 20 ){
            $message = "Your HappiTALK session has now ended. Complete the documentation to keep track of HappiUSER's progress.";
            $device_token = $session_details->psychologistDetail->device_token;

            $this->pushNotification()->sendNotification($device_token,$message);
        }
        

        if($session_details->is_psy_join == "1"){

            try {
                $sid = env('TWILIO_ACCOUNT_SID');
                $token = env('TWILIO_ACCOUNT_TOKEN');
                $twilio = new Client($sid, $token);

                $composition = $twilio->video->v1->compositions
                                     ->create($session_details->room_id, // roomSid
                                              [
                                                  "audioSources" => ["*"],
                                                  "videoLayout" => [
                                                      "grid" => [
                                                          "video_sources" => [
                                                              "*"
                                                          ]
                                                      ]
                                                  ],
                                                 
                                                  "format" => "mp4"
                                              ]
                                     );

                // print($composition->sid);
                $composition_data = [
                    'happitalk_session_id' => $request->session_id,
                    'twillio_composition_id' => $composition->sid,
                ];

                HappitalkSessionComposition::create($composition_data);

            } catch(Exception $e) {
                return $e->getMessage();
            }
            
        }
            

        return response()->json(['status' => 'success' , 'message' => 'Session mark as completed successfully.']);
    }


    public function checkRoomParticipantPsy(Request $request){

        $message = [
            'session_id.required'   =>  'Please enter session ID.',
        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $session_details = HappitalkSession::where('id' , $request->session_id)->first();

        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_ACCOUNT_TOKEN');
        $twilio = new Client($sid, $token);

        $participants = $twilio->video->v1->rooms($session_details->room_id)
                                  ->participants
                                  ->read(["status" => "connected"], 20);

       if(count($participants) > 0){
            return response()->json(['status' => 'error' , 'message' => 'Ask user to end the call first.']);
       }else{
            return response()->json(['status' => 'success' , 'message' => 'Room is empty.']);
       }
    }

    public function getSessionOfPerticularDatePsy(Request $request){

        $psy = Auth::guard('psychologist')->user();

        $message = [
            'date.required'   =>  'Please enter date.',
        ];

        $validator = Validator::make($request->all(), [
            'date'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $sessions = HappitalkSession::where('psychologist_id' , $psy->id)->where('date' , $request->date)->orderBy('time' , 'asc')->with('userDetail')->get();

        return response()->json(['status' => 'success' , 'message' => 'Session of perticular date get successfully.' , 'sessions' => $sessions]);

    }


    public function acceptSessionRequest(Request $request){

        $message = [
            'session_id.required'   =>  'Please enter session ID.',
        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $session_details = HappitalkSession::where('id' , $request->session_id)->first();

        $user_details = User::where('id' , $session_details->user_id)->first();
        $message = 'Your booking has been accepted.';
        if($user_details->device_token && strlen($user_details->device_token) > 20){
            $device_token = $user_details->device_token;
            $this->sendNotification($device_token,$message);
        }
        $data = [
            'user_id' => $user_details->id,
            'message' => $message,
        ];
        NotificationList::create($data);

        $session_details->is_req_accepted = 1;
        $session_details->save();

        return response()->json(['status' => 'success' , 'message' => 'Session request has been accepted successfully.']);

    }


    public function rejectSessionRequest(Request $request){

        $message = [
            'session_id.required'   =>  'Please enter session ID.',
            'reason.required'   =>  'Please enter reason.',

        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
            'reason'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $reason = $request->reason;

        $session_details = HappitalkSession::where('id' , $request->session_id)->first();
        $session_details->is_req_accepted = 2;
        $session_details->req_rejected_reason = $reason;
        $session_details->save();

        $user_details = User::where('id' , $session_details->user_id)->first();
        $message = "Your booking request has not been accepted by a counseling coach expert. (".$reason.")";
        if($user_details->device_token && strlen($user_details->device_token) > 20){
            $device_token = $user_details->device_token;
            $this->sendNotification($device_token,$message);
        }
        $data = [
            'user_id' => $user_details->id,
            'message' => $message,
        ];
        NotificationList::create($data);

        $booking_deatils = HappitalkBooking::where('id',$session_details->happitalk_booking_id)->first();
        $booking_deatils->remaining_session = $booking_deatils->remaining_session+1;
        $booking_deatils->save();

        return response()->json(['status' => 'success' , 'message' => 'Session request has been rejected successfully.']);

    }



    public function getSessionBetweenTwoDatesPsy(Request $request){

        $psy = Auth::guard('psychologist')->user();

        $message = [
            'start_date.required'   =>  'Please enter start date.',
            'end_date.required'   =>  'Please enter end date.',

        ];

        $validator = Validator::make($request->all(), [
            'start_date'   => 'required',
            'end_date'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }


        $sessions = HappitalkSession::where('psychologist_id' , $psy->id)->where('date' , '>=' , $request->start_date)->where('date' , '<=' , $request->end_date)->orderBy('date' , 'asc')->orderBy('time' , 'asc')->with('userDetail')->get();

        return response()->json(['status' => 'success' , 'message' => 'Session between two dates get successfully.' , 'sessions' => $sessions]);
    }



    public function rescheduleBookingPsy(Request $request){

        $psy = Auth::guard('psychologist')->user();

        $message = [
            'session_id.required'   =>  'Please enter session ID.',
            'cancel_reason.required'   =>  'Please enter cancel reason.',
        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
            'cancel_reason'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $session_details = HappitalkSession::where('id' , $request->session_id)->first();
        if($session_details->is_cancel == '1'){
            return response()->json(['status' => 'error' , 'message' => "This session has already been cancelled."]);
        }
        
        $booking_deatils = HappitalkBooking::where('id' , $session_details->happitalk_booking_id)->first();
        $booking_deatils->remaining_session = $booking_deatils->remaining_session+1;
        $booking_deatils->save();

        $user_Detail = User::where('id' , $session_details->user_id)->first();

        $message = 'Your booking has been reschedule, please book again.';
        if($user_Detail->device_token && strlen($user_Detail->device_token) > 20){
            $device_token = $user_Detail->device_token;
            $this->sendNotification($device_token,$message);
        }
        $data = [
            'user_id' => $user_Detail->id,
            'message' => $message,
        ];
        NotificationList::create($data);

        // HappitalkSession::where('id' , $request->session_id)->delete();
        HappitalkSession::where('id' , $request->session_id)->update(['is_cancel' => '1' , 'cancel_by' => 'psychologist', 'cancel_reason' => $request->cancel_reason]);


        return response()->json(['status' => 'success' , 'message' => "Reschedule booking request has been sent to user, and this session has been cancelled."]);

    }




    public function deliveredSessionsPsy(Request $request){
        $psy = Auth::guard('psychologist')->user();
        $current_date = Date('Y-m-d');
        // $sessions =  HappitalkSession::where('psychologist_id' , $psy->id)->where('is_cancel' , 0)->where('date','<',$current_date)->orderBy('date' , 'desc')->orderBy('time' , 'desc')->with('userDetail')->get();
        $sessions =  HappitalkSession::where('psychologist_id' , $psy->id)->where('is_cancel' , 0)->where('is_end', 1)->where('is_req_accepted' , '1')->orderBy('date' , 'desc')->orderBy('time' , 'desc')->with('userDetail')->get();


        foreach($sessions as $single_session){
            $user_id =  $single_session->userDetail->id;
            $is_user_from_org = UserToken::where('user_id' , $user_id)->first();
            if($is_user_from_org){
                $token_detail = Token::where('id' , $is_user_from_org->token_id)->first();
                $organization_detail = Organization::where('id' , $token_detail->organization_id)->first();
                $single_session->userDetail['user_from'] = 'organization';
                $single_session->userDetail['organization_name'] = $organization_detail->name;
            }else{
                $single_session->userDetail['user_from'] = 'individual';
                $single_session->userDetail['organization_name'] = null;
            }
        }
        return response()->json(['status' => 'success' , 'message' => 'Previous Sessions get successfully.' , 'session_detail' => $sessions]);
    }   



    public function deleteSingleSlotPsy(Request $request){

        $psy = Auth::guard('psychologist')->user();

        $message = [
            'date.required'   =>  'Please enter date.',
            'time.required'   =>  'Please enter time.',

        ];

        $validator = Validator::make($request->all(), [
            'date'   => 'required',
            'time'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $date_time_slot = PsychologistDateTimeSlots::where('date',$request->date)->where('time',$request->time)->first();
        if(!$date_time_slot){
            return response()->json(['status' => 'error' , 'message' => 'Invalid data.']);
        }
        PsychologistAvailability::where('psychologist_id',$psy->id)->where('psychologist_slot_id',$date_time_slot->id)->delete();
        return response()->json(['status' => 'success' , 'message' => 'Slots deleted successfully.']);
    }



    public function deleteSlotBetweenTwoDatesPsy(Request $request){

        $psy = Auth::guard('psychologist')->user();

        $message = [
            'first_date.required'  =>  'Please enter first date.',
            'last_date.required'   =>  'Please enter last date.',
        ];

        $validator = Validator::make($request->all(), [
            'first_date'  => 'required',
            'last_date'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $first_date = $request->first_date;
        $last_date = $request->last_date;

        $date_and_time_slot_ids_bt_dates = PsychologistDateTimeSlots::where('date' ,'>=',$first_date)->where('date' ,'<=',$last_date)->pluck('id');

        PsychologistAvailability::where('psychologist_id' , $psy->id)->whereIn('psychologist_slot_id',$date_and_time_slot_ids_bt_dates)->delete();

        return response()->json(['status' => 'success' , 'message' => 'Slots between two dates deleted successfully.']);

    }



    public function addSlotsPsy(Request $request){

        $psy = Auth::guard('psychologist')->user();

        $message = [
            'date.required'   =>  'Please enter date.',
            'time.required'   =>  'Please enter time.',

        ];

        $validator = Validator::make($request->all(), [
            'date'   => 'required',
            'time'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $date = $request->date;
        $explode_date = explode(',' , $date);
        $time = $request->time;
        $explode_time = explode(',' , $time);


        foreach($explode_date as $single_date){
            foreach($explode_time as $simple_time){
                $availability = PsychologistDateTimeSlots::firstOrCreate(['date' => $single_date, 'time' => $simple_time]);

                $is_psy_already_have_same_slot = PsychologistAvailability::where('psychologist_id' , $psy->id)->where('psychologist_slot_id' , $availability->id)->first();
                
                if(!$is_psy_already_have_same_slot){
                    $data = [
                        'psychologist_id' => $psy->id,
                        'psychologist_slot_id' => $availability->id,
                    ];
                    PsychologistAvailability::create($data);
                }
            }   
        }

        return response()->json(['status' => 'success' , 'message' => 'Slots add successfully.']);

    }


    public function submitOpinionAfterSessionPsy(Request $request){
        $psy = Auth::guard('psychologist')->user();

        $message = [
            'session_id.required'   =>  'Please enter session ID.',
            // 'session_status.required'   =>  'Please enter session status.',
            // 'presenting_complaints.required'   =>  'Please enter presenting complaint.',
            // 'session_summary.required'   =>  'Please enter session summary.',
            // 'hardword_asigned.required'   =>  'Please enter hardword asigned.',
            // 'plan_for_next_session.required'   =>  'Please enter plan for next session.',
        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
            // 'session_status'   => 'required',
            // 'presenting_complaints'   => 'required',
            // 'session_summary'   => 'required',
            // 'hardword_asigned'   => 'required',
            // 'plan_for_next_session'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }


        $is_already = HappitalkSessionOpinionPsychologist::where('happitalk_session_id' , $request->session_id)->first();

        if($is_already){
            return response()->json(['status' => 'error' , 'message' => "Opinion already submitted."]);
        }

        $data = [
            'psychologist_id' => $psy->id,
            'happitalk_session_id'   =>  $request->session_id,
            'session_status'   =>  $request->session_status ?? null,
            'presenting_complaints'   =>  $request->presenting_complaints ?? null,
            'session_summary'   =>  $request->session_summary ?? null,
            'hardword_asigned'   =>  $request->hardword_asigned ?? null,
            'plan_for_next_session'   =>  $request->plan_for_next_session ?? null,
        ];
        HappitalkSessionOpinionPsychologist::create($data);

        return response()->json(['status' => 'success' , 'message' => "Opinion submit successfully."]);

    }



    public function submitSessionNotePsy(Request $request){

        $psy = Auth::guard('psychologist')->user();

        $message = [
            'session_id.required'   =>  'Please enter session ID.',
        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $data = [
            'session_id' => $request->session_id?? '',
            'case_history' => $request->case_history ?? '',
            'username' => $request->username ?? '',
            'time' => $request->time ?? '',
            'duration' => $request->duration ?? '',
            'name_of_therapist' => $request->name_of_therapist ?? '',
            'age' => $request->age ?? '',
            'gender' => $request->gender ?? '',
            'occupation' => $request->occupation ?? '',
            'qualification' => $request->qualification ?? '',
            'presenting_complaints' => $request->presenting_complaints ?? '',
            'past_psychology_history' => $request->past_psychology_history ?? '',
            'medical_history' => $request->medical_history ?? '',
            'family_psychological_histroy' => $request->family_psychological_histroy ?? '',
            'session_summary' => $request->session_summary ?? '',
            'diagnosis' => $request->diagnosis ?? '',
            'plan_for_therpy_treatment' => $request->plan_for_therpy_treatment ?? '',
        ];

        HappitalkNotesForUserByPsy::create($data);
        
        return response()->json(['status' => 'success' , 'message' => 'Notes has been submit successfully for this session']);
    }



    public function userPreviousSessionsPsy(Request $request)
    {
        $message = [
            'user_id.required'   =>  'Please enter user ID.',
        ];

        $validator = Validator::make($request->all(), [
            'user_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $current_date = Date('Y-m-d');
        // $session_list_of_user = HappitalkSession::where('user_id' , $request->user_id)->where('date' , '<', $current_date)->where('is_end',1)->with('psychologistDetail')->get();
        $session_list_of_user = HappitalkSession::where('user_id' , $request->user_id)->where('is_end',1)->with('psychologistDetail')->get();

        foreach($session_list_of_user as $single_session){
            // $single_session->psychologistDetail['profile_picture'] = Storage::disk('s3')->url(config('constants.mediaAssets.psychologist.profilePicture.folderName') . '/' . $single_session->psychologistDetail['profile_picture']);
            $single_session->psychologistDetail->psy_profile = Storage::disk('s3')->url(config('constants.mediaAssets.psychologist.profilePicture.folderName') . '/' . $single_session->psychologistDetail['profile_picture']);

        }

        return response()->json(['status' => 'success' , 'message' => 'Users previous sessions get successfully.' , 'list' =>$session_list_of_user]);
    }



    public function getSessionNotePsy(Request $request){
        $message = [
            'session_id.required'   =>  'Please enter session ID.',
        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $data = HappitalkNotesForUserByPsy::where('session_id',$request->session_id)->with('sessionDetail')->first();
        // $data->sessionDetail->psychologistDetail['profile_picture'] = Storage::disk('s3')->url(config('constants.mediaAssets.psychologist.profilePicture.folderName') . '/' . $data->sessionDetail->psychologistDetail['profile_picture']);
        $data->sessionDetail->psychologistDetail->psy_profile = Storage::disk('s3')->url(config('constants.mediaAssets.psychologist.profilePicture.folderName') . '/' . $data->sessionDetail->psychologistDetail['profile_picture']);

        return response()->json(['status' => 'success' , 'message' => 'Logs of session get successfully.' , 'data' =>$data]);
    }   


    public function sendNotification($deviceToken , $message){
        $apiURL = 'https://exp.host/--/api/v2/push/send';
        $postInput = [
            'to' => $deviceToken,
            'title' => 'HappiMynd',
            'body' => $message,
        ];
        $headers = [
            'Content-Type: application/json'
        ];
        $response = Http::withHeaders($headers)->post($apiURL, $postInput);
        $statusCode = $response->status();
        $responseBody = json_decode($response->getBody(), true);
        // dd($responseBody);
        return $responseBody;
    }



    public function joinTalkRoomUser(Request $request){
        $user = Auth::user();

        $message = [
            'session_id.required'   =>  'Please enter session ID.',
        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $twilioAccountSid = getenv('TWILIO_ACCOUNT_SID');
        $twilioApiKey = getenv('TWILIO_API_KEY');
        $twilioApiSecret = getenv('TWILIO_API_KEY_SECRET');

        $session_details = HappitalkSession::where('id' , $request->session_id)->first();
        if($session_details->is_end == '1'){
            return response()->json(['status' => 'error' , 'message' => 'This session has ended.']);
        }


        if(!$session_details->room_id  ){
            $createRoom = $this->createTwillioRoom()->createRoom();

            $session_details->room_id = $createRoom->sid;
            $session_details->save();

            $roomName = $createRoom->sid;
        }else{
            // Required for Video grant
            $roomName = $session_details->room_id;
        }



        
        // An identifier for your app - can be anything you'd like
        $identity =  $user->username;

        // Create access token, which we will serialize and send to the client
        $token = new AccessToken(
            $twilioAccountSid,
            $twilioApiKey,
            $twilioApiSecret,
            3600,
            $identity
        );

        // Create Video grant
        $videoGrant = new VideoGrant();
        $videoGrant->setRoom($roomName);

        // Add grant to token
        $token->addGrant($videoGrant);

        // render token to string
        // echo $token->toJWT();


        $session_details->is_user_join = 1;
        $session_details->save();
        

        return response()->json(['status' => 'success' , 'message' => 'Token get sucessfully.' , 'token' => $token->toJWT()]);

    }

    public function joinTalkRoomPsy(Request $request){



        $psy = Auth::guard('psychologist')->user();

        $message = [
            'session_id.required'   =>  'Please enter session ID.',
        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $twilioAccountSid = getenv('TWILIO_ACCOUNT_SID');
        $twilioApiKey = getenv('TWILIO_API_KEY');
        $twilioApiSecret = getenv('TWILIO_API_KEY_SECRET');

        $session_details = HappitalkSession::where('id' , $request->session_id)->with('userDetail', 'psychologistDetail')->first();
        if($session_details->is_end == '1'){
            return response()->json(['status' => 'error' , 'message' => 'This session has ended.']);
        }

        if(!$session_details->room_id  ){
            $createRoom = $this->createTwillioRoom()->createRoom();

            $session_details->room_id = $createRoom->sid;
            $session_details->save();

            $roomName = $createRoom->sid;
        }else{
            // Required for Video grant
            $roomName = $session_details->room_id;
        }


        
        // An identifier for your app - can be anything you'd like
        $identity =  $psy->username;

        // Create access token, which we will serialize and send to the client
        $token = new AccessToken(
            $twilioAccountSid,
            $twilioApiKey,
            $twilioApiSecret,
            3600,
            $identity
        );

        // Create Video grant
        $videoGrant = new VideoGrant();
        $videoGrant->setRoom($roomName);

        // Add grant to token
        $token->addGrant($videoGrant);

        // render token to string
        // echo $token->toJWT();


        // Notification to user
        if($session_details->is_psy_join == 0){
            
            $message = "I'm waiting for you! Please Join in Quickly. 🤓";

            if($session_details->userDetail->device_token && strlen($session_details->userDetail->device_token) > 20){
                $device_token = $session_details->userDetail->device_token;
                $title = "Session start";

                $this->pushNotification()->sendNotification($device_token, $message, $title);
            }
            $data = [
                        'user_id' => $session_details->userDetail->id,
                        'message' => $message, 
                    ];
            NotificationList::create($data);
        }


        //Notification to psy
        if($session_details->psychologistDetail->device_token && strlen($session_details->psychologistDetail->device_token) > 20 && $session_details->is_psy_join == 0){
            $message = "Your session has started. Ensure proper connectivity so that nothing hinders the path of your duty.";
            $device_token = $session_details->psychologistDetail->device_token;

            $this->pushNotification()->sendNotification($device_token,$message);
        }

        $current_time = Date('Y-m-d h:i:s A');

        $session_details->is_psy_join = 1;
        $session_details->psy_joined_time = $current_time;
        $session_details->save();

        return response()->json(['status' => 'success' , 'message' => 'Token get sucessfully.' , 'token' => $token->toJWT()]);

    }


    public function availHappiTalkUser(Request $request){

        $user = Auth::user();

        $message = [
            'psychologist_id.required'  =>  'Please enter psychologist ID.',
            // 'plan_id.required'          =>  'Please enter plan ID.',
            'date.required'             =>  'Please enter date.',
            'time.required'             =>  'Please enter time.',
            'session.required'          =>  'Please enter session.',
        ];
        $validator = Validator::make($request->all(), [
            'psychologist_id'   => 'required',
            // 'plan_id'           => 'required',
            'date'              => 'required',
            'time'              => 'required',
            'session'           => 'required',
            'user_recording_permission' => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        // $plan_id = $request->plan_id;
        $session = $request->session;
        $psychologist_id = $request->psychologist_id;
        $date = $request->date;
        $time = $request->time;


        $explode_start_end_time = explode('-' ,$request->time ) ;
 
        $requested_start_time = rtrim($explode_start_end_time[0]);
        $check_start_time_exist_in_any_booked_slot =  HappitalkSession::
                                where('psychologist_id' , $psychologist_id)
                                ->where('date' , $request->date)
                                ->whereRaw("STR_TO_DATE(CONCAT('1970-01-01 ', start_time), '%Y-%m-%d %h:%i %p') < STR_TO_DATE(CONCAT('1970-01-01 ', '$requested_start_time'), '%Y-%m-%d %h:%i %p')")
                                ->whereRaw("STR_TO_DATE(CONCAT('1970-01-01 ', end_time), '%Y-%m-%d %h:%i %p') > STR_TO_DATE(CONCAT('1970-01-01 ', '$requested_start_time'), '%Y-%m-%d %h:%i %p')")
                                ->where('is_req_accepted' , '!=' , '2')
                                ->where('is_cancel' , '!=' , '1')
                                ->first();

        if($check_start_time_exist_in_any_booked_slot){
            return response()->json(['status' => 'error' , 'message' => "This slot is not available."]);
        }

        $requested_end_time = ltrim($explode_start_end_time[1]);
        $check_end_time_exist_in_any_booked_slot =  HappitalkSession::
                                where('psychologist_id' , $psychologist_id)
                                ->where('date' , $request->date)
                                ->whereRaw("STR_TO_DATE(CONCAT('1970-01-01 ', start_time), '%Y-%m-%d %h:%i %p') < STR_TO_DATE(CONCAT('1970-01-01 ', '$requested_end_time'), '%Y-%m-%d %h:%i %p')")
                                ->whereRaw("STR_TO_DATE(CONCAT('1970-01-01 ', end_time), '%Y-%m-%d %h:%i %p') > STR_TO_DATE(CONCAT('1970-01-01 ', '$requested_end_time'), '%Y-%m-%d %h:%i %p')")
                                ->where('is_req_accepted' , '!=' , '2')
                                ->where('is_cancel' , '!=' , '1')
                                ->first();

        if($check_end_time_exist_in_any_booked_slot){
            return response()->json(['status' => 'error' , 'message' => "This slot is not available."]);
        }



        $is_there_any_pending_session_req_at_this_time = HappitalkSession::where('psychologist_id' , $psychologist_id)->where('date' , $date)->where('time', $time)->where('is_req_accepted','0')->first();
        if($is_there_any_pending_session_req_at_this_time){
            return response()->json(['status' => 'error' , 'message' => "This slot is already booked."]);
        }
        $is_there_any_accepted_session_and_not_cancel_req_at_this_time = HappitalkSession::where('psychologist_id' , $psychologist_id)->where('date' , $date)->where('time', $time)->where('is_req_accepted','1')->where('is_cancel' , '0')->first();
        if($is_there_any_accepted_session_and_not_cancel_req_at_this_time){
            return response()->json(['status' => 'error' , 'message' => "This slot is already booked."]);
        }
        
       

        // $unique_room_name = Date('Y-m-d_h:i:s').'_'.rand('0000000','9999999');

        // $sid = env('TWILIO_ACCOUNT_SID');
        // $token = env('TWILIO_ACCOUNT_TOKEN');
        // $twilio = new Client($sid, $token);

        // $room = $twilio->video->v1->rooms
        //                   ->create([
        //                                "recordParticipantsOnConnect" => True,
        //                                "statusCallback" => "www.google.com",
        //                                "type" => "group",
        //                                "uniqueName" => $unique_room_name,
        //                                'ttl' => 0,
        //                            ]
        //                   );

        $talk_booking = [
            'user_id' => $user->id,
            'user_type' => 'b2b',
            'psychologist_id' => $psychologist_id,
            // 'plan_id' => $plan_id,
            'total_no_of_session' => $session,
            'remaining_session' => $session - 1,
        ];
        $is_booking = HappitalkBooking::create($talk_booking);

        $time_with_trim_space = str_replace(' ' , '',$request->time);
        $explode_time = explode('-',$time_with_trim_space);
        $full_start_time = $explode_time[0];
        $split_start_time =  preg_split('#(?<=\d)(?=[a-z])#i', $full_start_time);
        $full_end_time = $explode_time[1];
        $split_end_time =  preg_split('#(?<=\d)(?=[a-z])#i', $full_end_time);
        $exact_time = $split_start_time[0].' '.$split_start_time[1].' '.'-'.' '.$split_end_time[0].' '.$split_end_time[1];

        $psy_details = Psychologist::where('id',$psychologist_id)->first();

        $talk_session = [
            'happitalk_booking_id' => $is_booking->id,
            'user_id' => $user->id,
            'user_type' => 'b2b',
            'psychologist_id' => $psychologist_id,
            'amount_per_session_psy' => $psy_details->price_per_session,
            'date' => $date,
            'time' => $time,
            'start_time' => $split_start_time[0].' '.$split_start_time[1],
            'end_time' => $split_end_time[0].' '.$split_end_time[1],
            // 'room_id' => $room->sid,
            'user_recording_permission' => $request->user_recording_permission,
        ];
        HappitalkSession::create($talk_session);


        if($request->coupen_id){
            $coupen_data = [
                'user_id' => $user->id,
                'coupon_id' => $request->coupen_id,
            ];
            CouponReceipt::create($coupen_data);
        }

        $device_token = $psy_details->device_token;
        $message = 'Please check you have a new appointment.';

        if($device_token && strlen($device_token) > 20){
            $this->pushNotification()->sendNotification($device_token,$message);
        }
        
        return response()->json(['status' => 'success' , 'message' => 'Your HappiTALK session has been booked successfully.']);

    }



    public function dashboardPsy(Request $request){
        $psy = Auth::guard('psychologist')->user();


        if($request->type == 'custom'){

            $message = [
                'start_date.required'  =>  'Please enter start date.',
                'end_date.required'  =>  'Please enter end date.',
            ];
            $validator = Validator::make($request->all(), [
                'start_date'   => 'required',
                'end_date'   => 'required',
            ],$message);

            if($validator->fails()) {
                return response()->json(["message" => $validator->errors()->first()],400);
            }

            $first_day = $request->start_date;
            $last_day  = $request->end_date;

            $b2c_booked_sessions = HappitalkSession::whereBetween('date' , [$first_day , $last_day])->where('psychologist_id' , $psy->id)->where('user_type' , 'b2c')->where('is_cancel' , '0')->count();
            $b2c_delivered_sessions = HappitalkSession::whereBetween('date' , [$first_day , $last_day])->where('psychologist_id' , $psy->id)->where('user_type' , 'b2c')->where('is_cancel' , '0')->where('is_end' , '1')->count();
            $b2c_delivered_sessions_booking_ids = HappitalkSession::whereBetween('date' , [$first_day , $last_day])->where('psychologist_id' , $psy->id)->where('user_type' , 'b2c')->where('is_cancel' , '0')->where('is_end' , '1')->pluck('happitalk_booking_id');
            $b2c_amount = HappitalkBooking::where('psychologist_id' , $psy->id)->where('user_type' , 'b2c')->whereIn('id' , $b2c_delivered_sessions_booking_ids)->sum('amount_after_deduction');


            $b2b_booked_sessions = HappitalkSession::whereBetween('date' , [$first_day , $last_day])->where('psychologist_id' , $psy->id)->where('user_type' , 'b2b')->where('is_cancel' , '0')->where('is_req_accepted' , '!=' , '2')->count();
            $b2b_delivered_sessions = HappitalkSession::whereBetween('date' , [$first_day , $last_day])->where('psychologist_id' , $psy->id)->where('user_type' , 'b2b')->where('is_cancel' , '0')->where('is_end' , '1')->count();
            $b2b_amount = HappitalkSession::whereBetween('date' , [$first_day , $last_day])->where('psychologist_id' , $psy->id)->where('user_type' , 'b2b')->where('is_cancel' , '0')->where('is_end' , '1')->sum('amount_per_session_psy'); 
           
        }
        elseif($request->type == 'this_month'){

            $first_day = date('Y-m-01');
            $last_day  = date('Y-m-t');

            $b2c_booked_sessions = HappitalkSession::whereBetween('date' , [$first_day , $last_day])->where('psychologist_id' , $psy->id)->where('user_type' , 'b2c')->where('is_cancel' , '0')->count();
            $b2c_delivered_sessions = HappitalkSession::whereBetween('date' , [$first_day , $last_day])->where('psychologist_id' , $psy->id)->where('user_type' , 'b2c')->where('is_cancel' , '0')->where('is_end' , '1')->count();
            $b2c_delivered_sessions_booking_ids = HappitalkSession::whereBetween('date' , [$first_day , $last_day])->where('psychologist_id' , $psy->id)->where('user_type' , 'b2c')->where('is_cancel' , '0')->where('is_end' , '1')->pluck('happitalk_booking_id');
            $b2c_amount = HappitalkBooking::where('psychologist_id' , $psy->id)->where('user_type' , 'b2c')->whereIn('id' , $b2c_delivered_sessions_booking_ids)->sum('amount_after_deduction');


            $b2b_booked_sessions = HappitalkSession::whereBetween('date' , [$first_day , $last_day])->where('psychologist_id' , $psy->id)->where('user_type' , 'b2b')->where('is_cancel' , '0')->where('is_req_accepted' , '!=' , '2')->count();
            $b2b_delivered_sessions = HappitalkSession::whereBetween('date' , [$first_day , $last_day])->where('psychologist_id' , $psy->id)->where('user_type' , 'b2b')->where('is_cancel' , '0')->where('is_end' , '1')->count();
            $b2b_amount = HappitalkSession::whereBetween('date' , [$first_day , $last_day])->where('psychologist_id' , $psy->id)->where('user_type' , 'b2b')->where('is_cancel' , '0')->where('is_end' , '1')->sum('amount_per_session_psy'); 
        }
        else{
            $b2c_booked_sessions = HappitalkSession::where('psychologist_id' , $psy->id)->where('user_type' , 'b2c')->where('is_cancel' , '0')->count();
            $b2c_delivered_sessions = HappitalkSession::where('psychologist_id' , $psy->id)->where('user_type' , 'b2c')->where('is_cancel' , '0')->where('is_end' , '1')->count();
            $b2c_amount = HappitalkBooking::where('psychologist_id' , $psy->id)->where('user_type' , 'b2c')->sum('amount_after_deduction');


            $b2b_booked_sessions = HappitalkSession::where('psychologist_id' , $psy->id)->where('user_type' , 'b2b')->where('is_cancel' , '0')->where('is_req_accepted' , '!=' , '2')->count();
            $b2b_delivered_sessions = HappitalkSession::where('psychologist_id' , $psy->id)->where('user_type' , 'b2b')->where('is_cancel' , '0')->where('is_end' , '1')->count();
            $b2b_amount = HappitalkSession::where('psychologist_id' , $psy->id)->where('user_type' , 'b2b')->where('is_cancel' , '0')->where('is_end' , '1')->sum('amount_per_session_psy'); 

            
        }
        

        $data = [
            'b2c_booked_sessions' => $b2c_booked_sessions,
            'b2c_delivered_sessions' => $b2c_delivered_sessions,
            'b2c_amount' => $b2c_amount,
            'b2b_booked_sessions' => $b2b_booked_sessions,
            'b2b_delivered_sessions' => $b2b_delivered_sessions,
            'b2b_amount' => $b2b_amount,
        ];

        return response()->json(['status' => 'success' , 'message' => 'Dashboard data get successfully.' , 'data' => $data]);

    }   


    public function getSessionRecordingPsy(Request $request){
        $message = [
            'session_id.required'   =>  'Please enter session ID.',
        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $composition_detail = HappitalkSessionComposition::where('happitalk_session_id' , $request->session_id)->first();
        if(!$composition_detail){
            return response()->json(['status' => 'error' , 'message' => 'Recording not exist']);
        }else{
            $sid = env('TWILIO_ACCOUNT_SID');
            $token = env('TWILIO_ACCOUNT_TOKEN');

            $twilio = new Client($sid, $token);
            $composition = $twilio->video->v1->compositions($composition_detail->twillio_composition_id)->fetch();

            //If composition completed then make url link
            if($composition->status == 'completed'){
                
                $app_key = env('TWILIO_API_KEY');
                $app_secret = env('TWILIO_API_KEY_SECRET');

                $headers = array(
                      'Content-Type: multipart/form-data',
                );
                $ch = curl_init();
                curl_setopt( $ch,CURLOPT_URL, 'https://video.twilio.com/v1/Compositions/'.$composition_detail->twillio_composition_id.'/Media?Ttl=3600' );
                // curl_setopt( $ch,CURLOPT_POST, true );
                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                curl_setopt($ch, CURLOPT_USERPWD, $app_key.':'.$app_secret);
                // curl_setopt( $ch,CURLOPT_POSTFIELDS, $fields );
                $result = curl_exec($ch);

                if($result == FALSE) {
                  die('Curl failed: ' . curl_error($ch));
                }

                curl_close( $ch );

                $data = json_decode($result, true);

                $url = $data['redirect_to'];

                    // $url = url('/download-composition-web'.'/'.$room_id.'/'.$composition_sid);
                return response()->json(["status" => "success" , 'state' =>  $composition->status ,"url" => $url]);

            }
            else{
                return response()->json(["status" => "success" , 'state' =>  $composition->status]);
            }
        }
    }



    public function usersAllTalkSessionNotesByPsy(Request $request){

        $message = [
            'user_id.required'   =>  'Please enter user ID.',
        ];

        $validator = Validator::make($request->all(), [
            'user_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $sessions_ids = HappitalkSession::where('user_id' , $request->user_id)->pluck('id');


        $data =  HappitalkNotesForUserByPsy::whereIn('session_id' ,$sessions_ids )->get();

        return response()->json(["status" => 'success' , 'message' => 'Notes list get successfully.' , 'list' => $data]);

    }



    public function getPenaltyClauseUser(Request $request){
        $data = HappitalkPenaltyClause::first();
        return response()->json(["status" => 'success' , 'message' => 'Penalty clause get successfully.' , 'data' => $data]);
    }


}   












