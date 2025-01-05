<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Storage;
use Validator;
use App\Models\HappiguideSession;
use App\Models\Psychologist;
use App\Models\User;
use App\Models\HappiguideNotesForUserByPsy;
use App\Models\HappiguideSessionComposition;
use App\Models\AssignPsyToPlan;
use App\Models\BundleStatus;
use App\Models\RewardPointInstance;
use App\Models\NotificationList;
use App\Models\HappiguideSessionOpinionUser;
use App\Models\HappiguideSessionOpinionPsychologist;
use App\Models\CouponReceipt;


use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Rest\Client;


use App\BusinessModel\PushNotification;
use App\BusinessModel\RewardPointToUser;
use App\BusinessModel\CreateTwillioRoom;


class HappiguideController extends Controller
{

    public function pushNotification(){
        return new PushNotification();
    }


    public function rewardPointToUser(){
        return new RewardPointToUser();
    }

    
    public function createTwillioRoom(){
        return new CreateTwillioRoom();
    }


    
    public function happiguideSessionUser(Request $request){
        $user = Auth::user();
        $guide_booking = HappiguideSession::where('user_id' , $user->id)->with('psychologistDetail')->first();

        if($guide_booking){
            // $guide_booking->psychologistDetail['profile_picture'] = Storage::disk('s3')->url(config('constants.mediaAssets.psychologist.profilePicture.folderName') . '/' . $guide_booking->psychologistDetail['profile_picture']);
            $guide_booking->psychologistDetail->psy_profile = Storage::disk('s3')->url(config('constants.mediaAssets.psychologist.profilePicture.folderName') . '/' . $guide_booking->psychologistDetail['profile_picture']);
            
            return response()->json(['status' => 'success' , 'message' => 'Happiguide session get successfully.' , 'list' => $guide_booking]);
        }
        else{
            return response()->json(['status' => 'error' , 'message' => 'No Session available.']);
        }
        
    }



    public function happiguideRescheduleSessionUser(Request $request){
        $user = Auth::user();

        $message = [
            'session_id.required'   =>  'Please enter session ID.',
            'date.required'   =>  'Please select date.',
            'time.required'   =>  'Please select time.',
        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
            'date'   => 'required',
            'time'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $guide_session_id = $request->session_id;
        $date = $request->date;
        $time = $request->time;


        $guide_session_Details = HappiguideSession::where('id' , $guide_session_id)->first();

        $is_already_seesion_on_this_time = HappiguideSession::where('id','!=',$guide_session_id)->where('date' , $date)->where('time' , $time)->where('psychologist_id' , $guide_session_Details->psychologist_id)->first();

        if($is_already_seesion_on_this_time){
            return response()->json(['status' => 'error' , 'message' => 'This time slot is already booked.']);
        }else{
            $data = [
                'date' => $date,
                'time' => $time,
            ];
            HappiguideSession::where('id' , $guide_session_id)->update($data);

            $psy_details = Psychologist::where('id' , $guide_session_Details->psychologist_id)->first();
            $device_token = $psy_details->device_token;
            $message = 'Your HappiGUIDE session has been rescheduled';
            if($device_token != null && strlen($device_token) > 20){
                $this->pushNotification()->sendNotification($device_token,$message);
            }

            return response()->json(['status' => 'success' , 'message' => 'HappiGUIDE session has been Rescheduled successfully.']);
        }

    }



    public function joinGuideRoomUser(Request $request){
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

        $session_details = HappiguideSession::where('id' , $request->session_id)->first();
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

        return response()->json(['status' => 'success' , 'message' => 'Token get sucessfully.' , 'token' => $token->toJWT()]);
    }



    public function happiguideSessionPsy(Request $request){
        $psy = Auth::guard('psychologist')->user();
        $search = $request->search;
        $guide_booking = HappiguideSession::where('psychologist_id' , $psy->id)
                            // ->with('userDetail')->paginate('50');
                        ->join('users', 'users.id', '=', 'happiguide_sessions.user_id')
                        ->Where('nickname', 'like', '%' . $search . '%')
                        ->with('userDetail')
                        ->select(['happiguide_sessions.*' , 'users.nickname'])
                        ->paginate('20');
        return response()->json(['status' => 'success' , 'message' => 'Psychologist Guide session get successfully.' , 'session_detail' => $guide_booking]);
    }



    public function joinGuideRoomPsy(Request $request){
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

        $session_details = HappiguideSession::where('id' , $request->session_id)->first();
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

        return response()->json(['status' => 'success' , 'message' => 'Token get sucessfully.' , 'token' => $token->toJWT()]);
    }



    public function happiguideSessionMarkAsCompletedPsy(Request $request){

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

        $session_id = $request->session_id;

        $session_details = HappiguideSession::where('id' , $request->session_id)->first();
        HappiguideSession::where('id' , $session_id)->update(['is_end' => 1]);


        // $sid = env('TWILIO_ACCOUNT_SID');
        // $token = env('TWILIO_ACCOUNT_TOKEN');
        // $twilio = new Client($sid, $token);

        // $composition = $twilio->video->v1->compositions
        //                          ->create($session_details->room_id, // roomSid
        //                                   [
        //                                       "audioSources" => ["*"],
        //                                       "videoLayout" => [
        //                                           "grid" => [
        //                                               "video_sources" => [
        //                                                   "*"
        //                                               ]
        //                                           ]
        //                                       ],
                                             
        //                                       "format" => "mp4"
        //                                   ]
        //                          );

        // // print($composition->sid);
        // $composition_data = [
        //     'happiguide_session_id' => $request->session_id,
        //     'twillio_composition_id' => $composition->sid,
        // ];

        // HappiguideSessionComposition::create($composition_data);

        return response()->json(['status' => 'success' , 'message' => 'Session has been mark completed successfully.']);

    }



    public function submitGuideSessionNotePsy(Request $request){

        $psy = Auth::guard('psychologist')->user();

        $message = [
            'guide_session_id.required'   =>  'Please enter guide session ID.',
        ];

        $validator = Validator::make($request->all(), [
            'guide_session_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $data = [
            'guide_session_id' => $request->guide_session_id ?? '',
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

        HappiguideNotesForUserByPsy::create($data);
        
        return response()->json(['status' => 'success' , 'message' => 'Notes has been submit successfully for this HappiGUIDE session.']);
    }



    public function getHappiguideSessionRecordingPsy(Request $request){
        $message = [
            'session_id.required'   =>  'Please enter session ID.',
        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $composition_detail = HappiguideSessionComposition::where('happiguide_session_id' , $request->session_id)->first();
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



    public function checkGuideRoomParticipantPsy(Request $request){

        $message = [
            'session_id.required'   =>  'Please enter session ID.',
        ];

        $validator = Validator::make($request->all(), [
            'session_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $session_details = HappiguideSession::where('id' , $request->session_id)->first();

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


    public function availHappiguideUser(Request $request) {

        $user = Auth::user();

        $message = [
            'plan_id.required'   =>  'Please enter plan ID.',
            'date.required'   =>  'Please enter date.',
            'time.required'   =>  'Please enter time.',
        ];

        $validator = Validator::make($request->all(), [
            'plan_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }


        $bundleStatus = BundleStatus::create([
            'user_id' => $user->id,
            'plan_id' => $request->plan_id, 
            'percentage_covered' => "0.00",
        ]);
        
        

        $is_any_psy_map_to_guide = AssignPsyToPlan::where('plan_name' , 'HappiGuide')->first();
        if(!$is_any_psy_map_to_guide){
            return response()->json(['status' => 'error' , 'message' => 'No psychologist map with HappiGuide'], 400);
        }

        $last_assign_psy = AssignPsyToPlan::where('plan_name' , 'HappiGuide')->where('last_psy_assign_for_guide' , 1)-> first();
        if($last_assign_psy == null){
            $first_guide_psychologist = AssignPsyToPlan::where('plan_name' , 'HappiGuide')->first();
            $first_guide_psychologist->last_psy_assign_for_guide = 1;
            $first_guide_psychologist->save();
            $psychologist_id = $first_guide_psychologist->psychologist_id;
        }
        else{
            $last_assign_psy->last_psy_assign_for_guide = 0;
            $last_assign_psy->save();

            $next_psy_to_be_assigned = AssignPsyToPlan::where('plan_name' , 'HappiGuide')->where('id' , '>' , $last_assign_psy->id)->first();
            if($next_psy_to_be_assigned){
                $next_psy_to_be_assigned->last_psy_assign_for_guide = 1;
                $next_psy_to_be_assigned->save();
                $psychologist_id = $next_psy_to_be_assigned->psychologist_id;
            }else{
                $first_guide_psychologist = AssignPsyToPlan::where('plan_name' , 'HappiGuide')->first();
                $first_guide_psychologist->last_psy_assign_for_guide = 1;
                $first_guide_psychologist->save();
                $psychologist_id = $first_guide_psychologist->psychologist_id;
            }
        }
        // return $psychologist_id;

        $explode_start_end_time = explode('-' ,$request->time ) ;
        $requested_start_time = rtrim($explode_start_end_time[0]);
        $requested_end_time = ltrim($explode_start_end_time[1]);

        //Twilio room
        // $unique_room_name = Date('Y-m-d_h:i:s').'_'.rand('0000000','9999999');
        
        // $sid = env('TWILIO_ACCOUNT_SID');
        // $token = env('TWILIO_ACCOUNT_TOKEN');
        // $twilio = new Client($sid, $token);
 
        // $room = $twilio->video->v1->rooms
        //                   ->create([
        //                                "recordParticipantsOnConnect" => false,
        //                                "statusCallback" => "www.google.com",
        //                                "type" => "group",
        //                                "uniqueName" => $unique_room_name,
        //                               'ttl' => 0,
        //                            ]
        //                   );

        // $room_id = $room->sid;

        $data = [
            'user_id' => $user->id,
            'psychologist_id' => $psychologist_id,
            'date' => $request->date,
            'time' => $request->time,
            'start_time' => $requested_start_time,
            'end_time' => $requested_end_time,
            // 'room_id' => $room_id,
            'is_start' => '0',
            'is_end' => '0',
        ];

        HappiguideSession::create($data);


        if($request->coupen_id){
            $coupen_data = [
                'user_id' => $user->id,
                'coupon_id' => $request->coupen_id,
            ];
            CouponReceipt::create($coupen_data);
        } 
        

        //Reward Points
        $reward_points = RewardPointInstance::where('action_performed' , 'When HappiGUIDE Subscribed')->first();
        $points_to_be_added_to_user = $reward_points->points_to_be_given;
        $task_performed = 'Book HappiGUIDE';
        $this->rewardPointToUser()->addRewardToUser($user->id , $points_to_be_added_to_user , $task_performed);
                    


        // Notifications
        $user_detail = User::where('id' , $user->id)->first();
        $psy_details = Psychologist::where('id',$psychologist_id)->first();

        $users_device_token = $user_detail->device_token;
        $message = "You're On The Right Path!🛤️😀. Your HappiGUIDE session has been proposed with your Counseling Coach expert (".$psy_details->first_name.") for (".$request->date." ".$request->time.")";
        $title = "HappiGUIDE session";

        if($users_device_token != null && strlen($users_device_token) > 20){
            $this->pushNotification()->sendNotification($users_device_token,$message, $title);
        }
        $data = [
            'user_id' => $user->id,
            'message' => $message,
        ];
        NotificationList::create($data);

        $psy_device_token = $psy_details->device_token;
        $message = "Your HappiGUIDE session has been scheduled for (".$request->date." ".$request->time." ).";
        $title = "HappiGUIDE session";

        if($psy_device_token != null && strlen($psy_device_token) > 20){
            $this->pushNotification()->sendNotification($psy_device_token,$message, $title);
        }

        return response()->json(['status' => 'success' , 'message' => "HappiGUIDE session avail successfully."]);

    }




    public function submitOpinionAfterGuideSessionUser(Request $request){
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


        $is_already = HappiguideSessionOpinionUser::where('happiguide_session_id' , $request->session_id)->first();

        if($is_already){
            return response()->json(['status' => 'error' , 'message' => "Opinion already submitted."]);
        }
        
        $data = [
            'user_id' =>$user->id,
            'happiguide_session_id' =>$request->session_id,
            'application_rate_emoji_id' => $request->emoji_id,
            'reason' => $request->reason,
            'additional_comment' => $request->additional_comment,
        ];

        HappiguideSessionOpinionUser::create($data);

        return response()->json(['status' => 'success' , 'message' => "Opinion submit successfully."]);


    }





    public function submitOpinionAfterGuideSessionPsy(Request $request){
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


        $is_already = HappiguideSessionOpinionPsychologist::where('happiguide_session_id' , $request->session_id)->first();

        if($is_already){
            return response()->json(['status' => 'error' , 'message' => "Opinion already submitted."]);
        }

        $data = [
            'psychologist_id' => $psy->id,
            'happiguide_session_id'   =>  $request->session_id,
            'session_status'   =>  $request->session_status ?? null,
            'presenting_complaints'   =>  $request->presenting_complaints ?? null,
            'session_summary'   =>  $request->session_summary ?? null,
            'hardword_asigned'   =>  $request->hardword_asigned ?? null,
            'plan_for_next_session'   =>  $request->plan_for_next_session ?? null,
        ];
        HappiguideSessionOpinionPsychologist::create($data);

        return response()->json(['status' => 'success' , 'message' => "Opinion submit successfully."]);

    }





}











