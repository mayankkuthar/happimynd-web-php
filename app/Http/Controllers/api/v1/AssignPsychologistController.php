<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Psychologist;
// use App\Models\AssignedPsychologistForChat;
use App\Models\Language;
use App\Models\PsychologistLanguage;
use Guard;
use DB;
use App\Models\User;
use App\Models\GroupChat;
use App\Models\RewardPointInstance;
use App\Models\UserRewardPointRecord;
use App\Models\AssignPsyToPlan;
use App\Models\HappibuddyMonthlyReport;

use App\BusinessModel\RewardPointToUser;



use Validator;
use Http;

class AssignPsychologistController extends Controller
{

    public function rewardPointToUser(){
        return new RewardPointToUser();
    }

    // public function assignpsychologist(Request $request){
    //     $user = Auth::user();
    //     // $user_language = strtolower($user->language);
    //     $message = [
    //         'language.required'      =>  'Please enter language',
    //     ];
    //     $validator = Validator::make($request->all(), [
    //         'language'   => 'required',

    //     ],$message);

    //     if($validator->fails()) {
    //         return response()->json(["message" => $validator->errors()->first()],400);
    //     }

    //     $user_language = strtolower($request->language);

    //     $lang_detail = Language::where('name' , ucfirst($user_language))->first();
    //     if(!$lang_detail){
    //         return response()->json(['status' =>'false' , 'message' => 'Invalid language.']);
    //     }

    //     $psy_ids_based_on_lang = DB::table('psychologist_languages')->where('language_id',$lang_detail->id)->pluck('psychologist_id');

    //     $all_psychologist = Psychologist::select('id' , 'username','email','total_user_for_chat')->whereIn('id' , $psy_ids_based_on_lang)->get();
        
    //     $count_psy = count($all_psychologist);

    //     if($count_psy == 0){
    //         return response()->json(['status' =>'false' , 'message' => 'No psychologist available corosponding to your language.']);
    //     }

    //     $assign_user_count = null;
    //     $assigned_psychologist_id = null;

    //     for ($i=0; $i < $count_psy; $i++) { 
    //         if($i==0){
    //             $assign_user_count = $all_psychologist[$i]->total_user_for_chat;
    //             $assigned_psychologist_id = $all_psychologist[$i]->id;
    //         }else{
    //             if($all_psychologist[$i]->total_user_for_chat < $assign_user_count){
    //                 $assign_user_count = $all_psychologist[$i]->total_user_for_chat;
    //                 $assigned_psychologist_id = $all_psychologist[$i]->id; 
    //             }
    //         }
    //     }

    //     // $track_of_assigned_psychologist = [
    //     //     'user_id' => $user->id,
    //     //     'psychologist_id'   => $assigned_psychologist_id,
    //     //     'date_time' => Date('d-m-Y h:i:s'),
    //     //     'language' => $user_language,
    //     // ];

    //     $group_detail_of_user = GroupChat::where('user_id' , $user->id)->first();
    //     if($group_detail_of_user){
    //         $group_id = $group_detail_of_user->group_id;
    //     }else{
    //         $group_id = rand('1','999999');
    //     }

    //     $group_chat = [
    //         'user_id'           => $user->id,
    //         'psychologist_id'   => $assigned_psychologist_id,
    //         'group_id'          => $group_id,
    //         'assigned_date_time' => Date('d-m-Y h:i:s'),
    //         'language' => $user_language,
    //     ];

    //     GroupChat::create($group_chat);
    //     // AssignedPsychologistForChat::create($track_of_assigned_psychologist);

    //     $psychologist_detail = Psychologist::where('id' , $assigned_psychologist_id)->first();

    //     $psychologist_detail->total_user_for_chat = $psychologist_detail->total_user_for_chat + 1;
    //     $psychologist_detail->save();

    //     return response()->json(['status' =>'success' , 'message' => 'Psychologist assign successfully.' , 'psychologist_detail' => $psychologist_detail, 'group_id'=> $group_id]);

    // }





    public function assignpsychologist(Request $request){
        $user = Auth::user();
        // $user_language = strtolower($user->language);
        $message = [
            'language.required'      =>  'Please enter language',
        ];
        $validator = Validator::make($request->all(), [
            'language'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $user_language = strtolower($request->language);

        $lang_detail = Language::where('name' , ucfirst($user_language))->first();
        if(!$lang_detail){
            return response()->json(['status' =>'false' , 'message' => 'Invalid language.']);
        }
 

        $group_detail_of_user = GroupChat::where('user_id' , $user->id)->first();
        if($group_detail_of_user){
            $group_id = $group_detail_of_user->group_id;
        }else{
            $group_id = rand('1','999999');
        }

        $is_any_psy_map_with_buddy = AssignPsyToPlan::where('plan_name' , 'HappiBuddy')->first();
        if(!$is_any_psy_map_with_buddy){
            return response()->json(['status' =>'error' , 'message' => 'No psychologist map with buddy rightnow.']);
        }

        $last_assign_psy = AssignPsyToPlan::where('plan_name' , 'HappiBuddy')->where('last_psy_assign_for_buddy' , 1)-> first();
        if($last_assign_psy == null){
            $first_guide_psychologist = AssignPsyToPlan::where('plan_name' , 'HappiBuddy')->first();
            $first_guide_psychologist->last_psy_assign_for_buddy = 1;
            $first_guide_psychologist->save();
            $psychologist_id = $first_guide_psychologist->psychologist_id;
        }
        else{
            $last_assign_psy->last_psy_assign_for_buddy = 0;
            $last_assign_psy->save();

            $next_psy_to_be_assigned = AssignPsyToPlan::where('plan_name' , 'HappiBuddy')->where('id' , '>' , $last_assign_psy->id)->first();
            if($next_psy_to_be_assigned){
                $next_psy_to_be_assigned->last_psy_assign_for_buddy = 1;
                $next_psy_to_be_assigned->save();
                $psychologist_id = $next_psy_to_be_assigned->psychologist_id;
            }else{
                $first_guide_psychologist = AssignPsyToPlan::where('plan_name' , 'HappiBuddy')->first();
                $first_guide_psychologist->last_psy_assign_for_buddy = 1;
                $first_guide_psychologist->save();
                $psychologist_id = $first_guide_psychologist->psychologist_id;
            }
        }


        $group_chat = [
            'user_id'           => $user->id,
            // 'psychologist_id'   => '47',
            'psychologist_id'   => $psychologist_id,
            'group_id'          => $group_id,
            'assigned_date_time' => Date('d-m-Y h:i:s'),
            'language' => $user_language,
        ];

        GroupChat::create($group_chat);

        // $psychologist_detail = Psychologist::where('id' , '47')->first();
        $psychologist_detail = Psychologist::where('id' , $psychologist_id)->first();


        // $psychologist_detail->total_user_for_chat = $psychologist_detail->total_user_for_chat + 1;
        // $psychologist_detail->save();

        return response()->json(['status' =>'success' , 'message' => 'Psychologist assign successfully.' , 'psychologist_detail' => $psychologist_detail, 'group_id'=> $group_id]);

    }




    // public function switchLanguage(Request $request){
    //     $user = Auth::user();

    //     $message = [
    //         'language.required'      =>  'Please enter language',
    //     ];
    //     $validator = Validator::make($request->all(), [
    //         'language'   => 'required',

    //     ],$message);

    //     if($validator->fails()) {
    //         return response()->json(["message" => $validator->errors()->first()],400);
    //     }

    //     $user_language = strtolower($request->language);
    //     $lang_detail = Language::where('name' , ucfirst($user_language))->first();
    //     if(!$lang_detail){
    //         return response()->json(['status' =>'false' , 'message' => 'Invalid language.']);
    //     }

    //     $psy_ids_based_on_lang = DB::table('psychologist_languages')->where('language_id',$lang_detail->id)->pluck('psychologist_id');

    //     $all_psychologist = Psychologist::select('id' , 'username','email','total_user_for_chat')->whereIn('id' , $psy_ids_based_on_lang)->get();
        
    //     $count_psy = count($all_psychologist);

    //     if($count_psy == 0){
    //         return response()->json(['status' =>'false' , 'message' => 'No psychologist available corosponding to your language.']);
    //     }

    //     $assign_user_count = null;
    //     $assigned_psychologist_id = null;

    //     for ($i=0; $i < $count_psy; $i++) { 
    //         if($i==0){
    //             $assign_user_count = $all_psychologist[$i]->total_user_for_chat;
    //             $assigned_psychologist_id = $all_psychologist[$i]->id;
    //         }else{
    //             if($all_psychologist[$i]->total_user_for_chat < $assign_user_count){
    //                 $assign_user_count = $all_psychologist[$i]->total_user_for_chat;
    //                 $assigned_psychologist_id = $all_psychologist[$i]->id; 
    //             }
    //         }
    //     }

    //     // $last_assigned_psy_id = AssignedPsychologistForChat::where('user_id',$user->id)->orderBy('id' , 'desc')->first();
    //     $last_assigned_psy_id = GroupChat::where('user_id',$user->id)->orderBy('id' , 'desc')->first();
    //     $last_assigned_psy_id->group_active_for_chat = 0;
    //     $last_assigned_psy_id->save();

    //     $last_psy_assigned_for_dec_count = Psychologist::where('id' , $last_assigned_psy_id->psychologist_id)->first();

    //     $last_psy_assigned_for_dec_count->total_user_for_chat = $last_psy_assigned_for_dec_count->total_user_for_chat-1;
    //     $last_psy_assigned_for_dec_count->save();

    //     // $track_of_assigned_psychologist = [
    //     //     'user_id' => $user->id,
    //     //     'psychologist_id'   => $assigned_psychologist_id,
    //     //     'date_time' => Date('d-m-Y h:i:s'),
    //     //     'language' => $user_language,
    //     // ];

    //     $group_detail_of_user = GroupChat::where('user_id' , $user->id)->first();
    //     $group_chat = [
    //         'user_id'           => $user->id,
    //         'psychologist_id'   => $assigned_psychologist_id,
    //         'group_id'          => $group_detail_of_user->group_id,
    //         'assigned_date_time' => Date('d-m-Y h:i:s'),
    //         'language' => $user_language,
    //     ];

    //     GroupChat::create($group_chat);

    //     // AssignedPsychologistForChat::create($track_of_assigned_psychologist);
    //     $psychologist_detail = Psychologist::where('id' , $assigned_psychologist_id)->first();

    //     $psychologist_detail->total_user_for_chat = $psychologist_detail->total_user_for_chat + 1;
    //     $psychologist_detail->save();

    //     return response()->json(['status' =>'success' , 'message' => 'Psychologist changed successfully.' , 'psychologist_detail' => $psychologist_detail, 'group_id'=> $group_detail_of_user->group_id]);
    // }

    public function switchLanguage(Request $request){
        $user = Auth::user();

        $message = [
            'language.required'      =>  'Please enter language',
        ];
        $validator = Validator::make($request->all(), [
            'language'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $user_language = strtolower($request->language);
        $lang_detail = Language::where('name' , ucfirst($user_language))->first();
        if(!$lang_detail){
            return response()->json(['status' =>'false' , 'message' => 'Invalid language.']);
        }



        $group_detail_of_user = GroupChat::where('user_id' , $user->id)->orderBy('id','desc')->first();


        $is_any_psy_map_with_buddy = AssignPsyToPlan::where('plan_name' , 'HappiBuddy')->first();
        if(!$is_any_psy_map_with_buddy){
            return response()->json(['status' =>'error' , 'message' => 'No psychologist map with buddy rightnow.']);
        }
        

        $last_assign_psy = AssignPsyToPlan::where('plan_name' , 'HappiBuddy')->where('last_psy_assign_for_buddy' , 1)-> first();
        if($last_assign_psy == null){
            $first_guide_psychologist = AssignPsyToPlan::where('plan_name' , 'HappiBuddy')->first();
            $first_guide_psychologist->last_psy_assign_for_buddy = 1;
            $first_guide_psychologist->save();
            $psychologist_id = $first_guide_psychologist->psychologist_id;
        }
        else{
            $last_assign_psy->last_psy_assign_for_buddy = 0;
            $last_assign_psy->save();

            $next_psy_to_be_assigned = AssignPsyToPlan::where('plan_name' , 'HappiBuddy')->where('id' , '>' , $last_assign_psy->id)->first();
            if($next_psy_to_be_assigned){
                $next_psy_to_be_assigned->last_psy_assign_for_buddy = 1;
                $next_psy_to_be_assigned->save();
                $psychologist_id = $next_psy_to_be_assigned->psychologist_id;
            }else{
                $first_guide_psychologist = AssignPsyToPlan::where('plan_name' , 'HappiBuddy')->first();
                $first_guide_psychologist->last_psy_assign_for_buddy = 1;
                $first_guide_psychologist->save();
                $psychologist_id = $first_guide_psychologist->psychologist_id;
            }
        }


        $group_detail_of_user->group_active_for_chat = 0;
        $group_detail_of_user->save();


        $group_chat = [
            'user_id'           => $user->id,
            'psychologist_id'   => $psychologist_id,
            'group_id'          => $group_detail_of_user->group_id,
            'assigned_date_time' => Date('d-m-Y h:i:s'),
            'language' => $user_language,
        ];

        GroupChat::create($group_chat);

        $psychologist_detail = Psychologist::where('id' , $psychologist_id)->first();

        // $psychologist_detail->total_user_for_chat = $psychologist_detail->total_user_for_chat + 1;
        // $psychologist_detail->save();

        

        return response()->json(['status' =>'success' , 'message' => 'Psychologist changed successfully.' , 'psychologist_detail' => $psychologist_detail, 'group_id'=> $group_detail_of_user->group_id]);
    }





    public function psyWhomUserCurrentlyChatting(Request $request){
        $user = Auth::user();
        // $assiged_psy = AssignedPsychologistForChat::where('user_id' , $user->id)->orderBy('id' , 'desc')->first();
        $group_details = GroupChat::where('user_id' , $user->id)->orderBy('id' , 'desc')->first();

        if(!$group_details){
            return response()->json(['status' =>'error' , 'message' => 'No psychologist assigned']);
        }
        $psy_detail = Psychologist::where('id' , $group_details->psychologist_id)->first();

        // $group_detail_of_user = GroupChat::where('user_id' , $user->id)->first();

        return response()->json(['status' =>'success' , 'message' => 'Psychologist get successfully.' , 'psychologist_detail' => $psy_detail , 'language' => $group_details->language , 'group_id' => $group_details->group_id , 'user_unread_message' => $group_details->user_unread_message]);
    }





    public function psyChatListing(Request $request){
        $psychologist = Auth::guard('psychologist')->user(); 

        // $assigned_users = AssignedPsychologistForChat::where('psychologist_id' , $psychologist->id)
        //     ->distinct('user_id');
        //     // ->pluck('user_id');

        // $assigned_users = GroupChat::where('psychologist_id' , $psychologist->id)
        //     ->orderBy('last_message_deliver_at' , 'desc');
        //     // ->distinct('user_id');
        //     // ->pluck('user_id');

        // $assigned_users_ids = $assigned_users->pluck('user_id');
        // // $assigned_users_data = $assigned_users->orderBy('id','desc')->get();

        // $user_array = [];
        // for ($i=0; $i <count($assigned_users_ids) ; $i++) { 
        //     $user_detail = User::where('id' , $assigned_users_ids[$i])->select('id','username','email')->first();
        //     // $user_detail['current_lang_of_chatting'] = $assigned_users_data[$i][0]->language;
        //     array_push($user_array , $user_detail);
        // }
        //  // $user_array;
        // return response()->json(['status' =>'success' , 'message' => 'Chat list get successfully.' , 'list' => $user_array]);
        $assigned_users = GroupChat::select('id','user_id','user_unread_message','psychologist_id','psychologist_unread_message','group_id')->where('psychologist_id' , $psychologist->id)->where('group_active_for_chat',1)->orderBy('last_message_deliver_at' , 'desc')->with('user')->get(); 


        return response()->json(['status' =>'success' , 'message' => 'Chat list get successfully.' , 'list' => $assigned_users]);

    }





    public function allPsyToWhomUserChat(Request $request){
        $user = Auth::user();
        // $data = AssignedPsychologistForChat::where('user_id' , $user->id)->select('id' , 'user_id' , 'psychologist_id' , 'date_time')->get();
        $data = GroupChat::where('user_id' , $user->id)->select('id' , 'user_id' , 'psychologist_id' , 'assigned_date_time')->get();

        if(!$data){
            return response()->json(['status' =>'error' , 'message' => 'No chat available.']);
        }
        return response()->json(['status' =>'success' , 'message' => 'All psychologist to whom user chat.' , 'list' => $data]);
    }





    public function getGroupIdByPsychologist(Request $request){

        $psychologist = Auth::guard('psychologist')->user();
        $message = [
            'user_id.required'      =>  'Please enter user ID',
        ];
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $group_detail = GroupChat::where('psychologist_id' ,$psychologist->id)->where('user_id',$request->user_id)->first();

        // $assigned_psychologist_to_given_user = AssignedPsychologistForChat::where('user_id' ,$request->user_id)->orderBy('id' , 'desc')->first();

        if($group_detail){
            return response()->json(['status' =>'success' , 'message' => 'Group ID get successfully.' , 'group_id' => $group_detail->group_id , 'current_psychologist_of_user' => $group_detail->psychologist_id]);
        }else{
            return response()->json(['status' =>'success' , 'message' => 'No group found with this user.']);
        }
    }







    public function sendMessageByUserToPsy(Request $request){

        $user  = Auth::guard()->user();
        $message = [
            'psychologist_id.required'      =>  'Please enter psychologist ID',
            'group_id.required'      =>  'Please enter group ID',
        ];
        $validator = Validator::make($request->all(), [
            'psychologist_id'   => 'required',
            'group_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $where = [
            'user_id' => $user->id,
            'psychologist_id' => $request->psychologist_id,
            'group_id' => $request->group_id,
        ];

        $psychologist_detail = Psychologist::where('id' , $request->psychologist_id)->first();

        $check_group_detail = GroupChat::where($where)->orderBy('id' , 'desc')->first();

        $current_date_time = Date('d-m-Y h:i:s');

        if($check_group_detail){
            $check_group_detail->last_message_deliver_at = $current_date_time;
            $check_group_detail->psychologist_unread_message = $check_group_detail->psychologist_unread_message+1;

            $check_group_detail->Save();

            $from = $user->username;
            $device_token = $psychologist_detail->device_token;
            $message = $request->message;

            $this->sendNotification($from , $device_token , $message);


            $reward_points = RewardPointInstance::where('action_performed' , 'When message is shared in HappiBUDDY')->first();
            $points_to_be_added_to_user = $reward_points->points_to_be_given;
            $user_id = $user->id;
            $task_performed = 'Message sent in HappiBUDDY';
            $this->rewardPointToUser()->addRewardToUser($user_id , $points_to_be_added_to_user , $task_performed);

            // $reward_data = [
            //     'user_id' => $user->id,
            //     'points_earned' => $points_to_be_added_to_user,
            //     'task_performed' => 'Message sent in HappiBUDDY',
            // ];
            // UserRewardPointRecord::create($reward_data);

            return response()->json(['status' => 'success' , 'message' => 'Message has been sent successfully to psychologist.']);
        }   else{
            return response()->json(['status' => 'error' , 'message' => 'Invalid Details.']);
        }
        
    }



    public function sendMessageByPsyToUser(Request $request){
        $psychologist  = Auth::guard('psychologist')->user();
        $message = [
            'user_id.required'      =>  'Please enter user ID',
            'group_id.required'      =>  'Please enter group ID',
        ];
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required',
            'group_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $where = [
            'user_id' => $request->user_id,
            'psychologist_id' => $psychologist->id,
            'group_id' => $request->group_id,
        ];

        $user_details = User::where('id' , $request->user_id)->first();

        $check_group_detail = GroupChat::where($where)->orderBy('id' , 'desc')->first();

        $current_date_time = Date('d-m-Y h:i:s');

        if($check_group_detail){
            $check_group_detail->last_message_deliver_at = $current_date_time;
            $check_group_detail->user_unread_message = $check_group_detail->user_unread_message+1;

            $check_group_detail->Save();

            $from = 'Buddy';
            $device_token = $user_details->device_token;
            $message = $request->message;

            $this->sendNotification($from , $device_token,$message);

            return response()->json(['status' => 'success' , 'message' => 'Message has been sent successfully to user.']);
        }   else{
            return response()->json(['status' => 'error' , 'message' => 'Invalid Details.']);
        }
    }



    public function clearMessageBatchOfUser(Request $request){
        $user  = Auth::guard()->user();
    
        $where = [
            'user_id' => $user->id, 
        ];

        $check_group_detail = GroupChat::where($where)->update(['user_unread_message' => 0]);
        return response()->json(['status' => 'success' , 'message' => 'Message batch cleared successfully of user.']);

    }



    public function clearMessageBatchOfPsy(Request $request){
        $psychologist  = Auth::guard('psychologist')->user();
        $message = [
            'user_id.required'      =>  'Please enter user ID',
            'group_id.required'      =>  'Please enter group ID',
        ];
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required',
            'group_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $where = [
            'user_id' => $request->user_id,
            'psychologist_id' => $psychologist->id,
            'group_id' => $request->group_id,
        ];

        $check_group_detail = GroupChat::where($where)->update(['psychologist_unread_message' => 0]);
        return response()->json(['status' => 'success' , 'message' => 'Message batch cleared successfully of psychologist.']);

    }




    public function sendNotification($from , $deviceToken , $message){

        $apiURL = 'https://exp.host/--/api/v2/push/send';
        $postInput = [
            'to' => $deviceToken,
            'title' => $from,
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


 

    public function usersBuddyReportPsy(Request $request){
        $psy = Auth::guard('psychologist')->user();

        $message = [
            'user_id.required'   =>  'Please enter user ID.',
            'session_status.required'   =>  'Please enter session status.',
            'presenting_complaints.required'   =>  'Please enter presenting complaint.',
            'session_summary.required'   =>  'Please enter session summary.',
            'hardword_asigned.required'   =>  'Please enter hardword asigned.',
            'plan_for_next_session.required'   =>  'Please enter plan for next session.',
        ];

        $validator = Validator::make($request->all(), [
            'user_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }
 

        $data = [
            'user_id' => $request->user_id,
            'psychologist_id' => $psy->id,
            'session_status'   =>  $request->session_status,
            'presenting_complaints'   =>  $request->presenting_complaints ?? "",
            'session_summary'   =>  $request->session_summary ?? "",
            'hardword_asigned'   =>  $request->hardword_asigned ?? "",
            'plan_for_next_session'   =>  $request->plan_for_next_session ?? "",
        ];
        HappibuddyMonthlyReport::create($data);

        return response()->json(['status' => 'success' , 'message' => "HappiBUDDY Opinion submit successfully."]);

    }



    public function getUsersBuddyReportPsy(Request $request){
        $psy = Auth::guard('psychologist')->user();

        $message = [
            'user_id.required'   =>  'Please enter user ID.',
        ];

        $validator = Validator::make($request->all(), [
            'user_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }
  
       
        $data =  HappibuddyMonthlyReport::where('user_id' , $request->user_id)->orderBy('id' , 'desc')->limit('3')->get();

        return response()->json(['status' => 'success' , 'message' => "Users HappiBUDDY report get successfully." , 'data' => $data]);
    }



}