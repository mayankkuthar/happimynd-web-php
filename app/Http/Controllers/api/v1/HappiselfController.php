<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HappiselfCourse;
use App\Models\HappiselfSubCourse;
use App\Models\HappiselfContent;
use App\Models\HappiselfUsersLastVisitSubCourseAndContent;
use App\Models\HappiselfCourseLike;
use App\Models\HappiselfLibrary;
use App\Models\HappiselfLibraryContent;
use App\Models\HappiselfUserNote;
use App\Models\HappiselfContentAnswer;
use App\Models\NotificationList;

use Http;
use Auth;
use Validator;

use App\Models\RewardPointInstance;
use App\Models\UserRewardPointRecord;

use App\BusinessModel\PushNotification;
use App\BusinessModel\RewardPointToUser;


class HappiselfController extends Controller
{
        

    public function pushNotification(){
        return new PushNotification();
    }

    public function rewardPointToUser(){
        return new RewardPointToUser();
    }
    
    public function courseList(Request $request){
        $user = Auth::user();

        if($request->isMethod('GET')){
            $course_list = HappiselfCourse::where('deleted_at' , null)->where('language' , $user->language)->withCount('likes')->get();
        }
        if($request->isMethod('POST')){
            $course_list = HappiselfCourse::where('deleted_at' , null)->where('language' , $user->language)->where('course_name', 'like', '%'. $request->course_name . '%')->withCount('likes')->get();
        }
        return response()->json(['status' => 'success' , 'message' => 'Course list get successfully.' , 'list' => $course_list]);
    }


    public function subCourseList(Request $request){

        $user = Auth::user();
        
        $message = [
            'happiself_course_id.required'    =>  'Please enter happiself course ID',
            'happiself_course_id.exists'      =>  'Please enter valid happiself course ID',
        ];
        $validator = Validator::make($request->all(), [
            'happiself_course_id'   => 'required|exists:happiself_courses,id',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $sub_course_list = HappiselfSubCourse::where('deleted_at' , null)->where('happiself_course_id' , $request->happiself_course_id)->orderBy('count_for_sequence' , 'asc')->get();

        $for_first_open_status = 1;
        foreach($sub_course_list as $rows){

            $users_complete_courses_detail =  HappiselfUsersLastVisitSubCourseAndContent::where('user_id' , $user->id)->where('happiself_sub_course_id' , $rows->id)->first();
            if($users_complete_courses_detail){
                if($users_complete_courses_detail->is_complete_happiself_sub_course == 0){
                    $rows['status'] = 'ongoing';
                    $for_first_open_status = $for_first_open_status+1;
                }
                else{
                    $rows['status'] = 'completed';
                }
            }
            else{
                if($for_first_open_status == 1){
                    $rows['status'] = 'open';
                    $for_first_open_status = $for_first_open_status+1;
                }else{
                    $rows['status'] = 'locked';                
                }
            }
        }

        return response()->json(['status' => 'success' , 'message' => 'Sub course list get successfully.' , 'list' => $sub_course_list]);
    }


    public function getSubCourseContent(Request $request){

        $message = [
            'happiself_sub_course_id.required'    =>  'Please enter happiself sub course ID',
            'happiself_sub_course_id.exists'      =>  'Please enter valid happiself sub course ID',
        ];
        $validator = Validator::make($request->all(), [
            'happiself_sub_course_id'   => 'required|exists:happiself_sub_courses,id',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $content = HappiselfContent::where('happiself_sub_course_id' , $request->happiself_sub_course_id)->where('deleted_at' , null)->with('option')->get();

        return response()->json(['status' => 'success' , 'message' => 'Content get successfully.' , 'data' => $content]);
    }



    public function startSubCourse(Request $request){

        $user = Auth::user();

        $message = [
            'happiself_sub_course_id.required'    =>  'Please enter happiself sub course ID',
            'happiself_sub_course_id.exists'      =>  'Please enter valid happiself sub course ID',
        ];
        $validator = Validator::make($request->all(), [
            'happiself_sub_course_id'   => 'required|exists:happiself_sub_courses,id',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $happiself_sub_course_id = $request->happiself_sub_course_id;

        $is_sub_course_already_start = HappiselfUsersLastVisitSubCourseAndContent::where('happiself_sub_course_id' , $happiself_sub_course_id)->where('user_id' , $user->id)->where('is_complete_happiself_sub_course' , 0)->first();
        if($is_sub_course_already_start){
            return response()->json(['status' => 'success' , 'message' => 'This course is already started.']);
        }

        $happiself_sub_course_detils = HappiselfSubCourse::where('id' , $happiself_sub_course_id)->first();
        $data = [
            'user_id' => $user->id,
            'happiself_course_id' => $happiself_sub_course_detils->happiself_course_id,
            'happiself_sub_course_id' => $happiself_sub_course_id,
            'is_complete_happiself_sub_course' => 0,
        ];
        HappiselfUsersLastVisitSubCourseAndContent::create($data);

        return response()->json(['status' => 'success' , 'message' => 'Sub course has been start successfully.']);

    }


    public function endSubCourse(Request $request){

        $user = Auth::user();

        $message = [
            'happiself_sub_course_id.required'    =>  'Please enter happiself sub course ID',
            'happiself_sub_course_id.exists'      =>  'Please enter valid happiself sub course ID',
        ];
        $validator = Validator::make($request->all(), [
            'happiself_sub_course_id'   => 'required|exists:happiself_sub_courses,id',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $happiself_sub_course_id = $request->happiself_sub_course_id;

        $is_already_complete_sub_course = HappiselfUsersLastVisitSubCourseAndContent::where('happiself_sub_course_id' , $happiself_sub_course_id)->where('user_id' , $user->id)->first();

        if($is_already_complete_sub_course->is_complete_happiself_sub_course == 1){
            return response()->json(['status' => 'success' , 'message' => 'This sub course is already completed.']);
        }else{
            $is_already_complete_sub_course->is_complete_happiself_sub_course = 1;
            $is_already_complete_sub_course->save();
            

            


            //Send Reward points on complete sub course
            $reward_points = RewardPointInstance::where('action_performed' , 'When sub module is completed in HappiSELF')->first();
            $points_to_be_added_to_user = $reward_points->points_to_be_given;
            $user_id = $user->id;
            $task_performed = 'Sub module is completed in HappiSELF';
            $this->rewardPointToUser()->addRewardToUser($user_id , $points_to_be_added_to_user , $task_performed);

            // $reward_data = [
            //     'user_id' => $user->id,
            //     'points_earned' => $points_to_be_added_to_user,
            //     'task_performed' => 'Sub module is completed in HappiSELF',
            // ];
            // UserRewardPointRecord::create($reward_data);
            //



            //For course compelete reward
            $all_sub_courses_of_this_course = HappiselfSubCourse::where('happiself_course_id' , $is_already_complete_sub_course->happiself_course_id)->where('deleted_at' , null)->pluck('id')->toArray();
            $all_completed_sub_couse_of_this_course = HappiselfUsersLastVisitSubCourseAndContent::where('user_id' , $user->id)->where('happiself_course_id' , $is_already_complete_sub_course->happiself_course_id)->where('is_complete_happiself_sub_course' , 1)->pluck('happiself_sub_course_id')->toArray();

            $course_complete_status = 'true';
            foreach($all_sub_courses_of_this_course as $row){
                if(!in_array($row , $all_completed_sub_couse_of_this_course)){
                    $course_complete_status = 'false';
                    break;
                }
            }

            if($course_complete_status == 'true'){

                //Reward Point
                $reward_points = RewardPointInstance::where('action_performed' , 'When module is completed in HappiSELF')->first();
                $points_to_be_added_to_user = $reward_points->points_to_be_given;
                $user_id = $user->id;
                $task_performed = 'Module is completed in HappiSELF';
                $this->rewardPointToUser()->addRewardToUser($user_id , $points_to_be_added_to_user , $task_performed);

                // $reward_data = [
                //     'user_id' => $user->id,
                //     'points_earned' => $points_to_be_added_to_user,
                //     'task_performed' => 'Module is completed in HappiSELF',
                // ];
                // UserRewardPointRecord::create($reward_data);
                // ENd reward Point

                //Trigger Notification
                $title = 'Module completion';
                $device_token = $user->device_token;
                $message = "That's the Winning SPIRIT 💪 Keep the VIBE ON!";
                if($device_token && strlen($device_token) > 20){
                    $this->pushNotification()->sendNotification($device_token, $message, $title);
                }
                //
                $data = [
                    'user_id' => $user->id,
                    'message' => $message,
                ];
                $create_notification = NotificationList::create($data);

            }
            //
            

            return response()->json(['status' => 'success' , 'message' => 'Happiself sub course complete successfully.']);
        }
    }





    public function likeHappiselfCourse(Request $request){

        $user = Auth::user();

        $message = [
            'happiself_course_id.required'      =>  'Please enter happiself course ID',
            'happiself_course_id.exists'      =>  'Please enter valid happiself course ID',
        ];
        $validator = Validator::make($request->all(), [
            'happiself_course_id'   => 'required|exists:happiself_courses,id',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $data = [
            'user_id' => $user->id,
            'happiself_course_id' => $request->happiself_course_id,
        ];

        $is_already_like = HappiselfCourseLike::where('user_id',$user->id)
                                ->where('happiself_course_id',$request->happiself_course_id)
                                ->first();

        if($is_already_like){
            return response()->json(['status' => 'success' , 'message' => 'Happiself course is already liked.']);
        }else{
            HappiselfCourseLike::create($data);
            return response()->json(['status' => 'success' , 'message' => 'Happiself course like successfully.']);
        }
        

    }


    public function unLikeHappiselfCourse(Request $request){
        
        $user = Auth::user();

        $message = [
            'happiself_course_id.required'      =>  'Please enter happiself course ID',
            'happiself_course_id.exists'      =>  'Please enter valid happiself course ID',
        ];
        $validator = Validator::make($request->all(), [
            'happiself_course_id'   => 'required|exists:happiself_courses,id',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $is_liked = HappiselfCourseLike::where('user_id',$user->id)
                             ->where('happiself_course_id',$request->happiself_course_id)
                             ->first();
        if($is_liked){
            $is_liked->delete();
            return response()->json(['status' => 'success' , 'message' => 'Post unlike successfully.']);
        }else{
            return response()->json(['status' => 'success' , 'message' => 'Post already unlike.']);
        }

    }



    public function happiselfLibraryList(Request $request){
        $user = Auth::user();

        if($request->isMethod('GET')){
            $library_list = HappiselfLibrary::where('deleted_at' , null)->where('language' , $user->language)->get();
        }
        if($request->isMethod('POST')){
            $library_list = HappiselfLibrary::where('deleted_at' , null)->where('language' , $user->language)->where('library_name', 'like', '%'. $request->library_name . '%')->get();
        }
        return response()->json(['status' => 'success' , 'message' => 'Library list get successfully.' , 'list' => $library_list]);   
    }


    public function happiselfLibraryContent(Request $request){

        $message = [
            'happiself_library_id.required'    =>  'Please enter happiself library ID',
            'happiself_library_id.exists'      =>  'Please enter valid happiself library ID',
        ];
        $validator = Validator::make($request->all(), [
            'happiself_library_id'   => 'required|exists:happiself_libraries,id',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $content = HappiselfLibraryContent::where('happiself_library_id' , $request->happiself_library_id)->where('deleted_at',null)->get();

        return response()->json(['status' => 'success' , 'message' => 'Content get successfully.' , 'data' => $content]);

    }




    public function addNotes(Request $request){

        $user = Auth::user();

        $message = [
            'notes_id.required'    =>  'Please enter notes ID',
        ];
        $validator = Validator::make($request->all(), [
            'notes'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $data = [
            'user_id' => $user->id,
            'notes' => $request->notes,
        ];
        HappiselfUserNote::create($data);
        
        return response()->json(['status' => 'success' , 'message' => 'Notes added successfully.']);

    }




    public function happiselfUpdateNotes(Request $request){

        $user = Auth::user();

        $message = [
            'notes.required'    =>  'Please enter happiself notes',
            'notes_id.required'    =>  'Please enter notes ID',
        ];
        $validator = Validator::make($request->all(), [
            'notes'   => 'required',
            'notes_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $data = [
            'notes' => $request->notes,
        ];
        HappiselfUserNote::where('id' , $request->notes_id)->update($data);
        

        return response()->json(['status' => 'success' , 'message' => 'Notes Update successfully.']);

    }



    public function happiselfGetNotesList(Request $request){
        $user = Auth::user();
        $data = HappiselfUserNote::where('user_id' , $user->id)->get();
        return response()->json(['status' => 'success' , 'message' => 'Notes list get successfully.' , 'data' => $data]);
    }


    public function happiselfGetNotesByID(Request $request){

        $message = [
            'notes_id.required'    =>  'Please enter notes ID',
        ];
        $validator = Validator::make($request->all(), [
            'notes_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $data = HappiselfUserNote::where('id' , $request->notes_id)->first();

        return response()->json(['status' => 'success' , 'message' => 'Notes get successfully.' , 'data' => $data]);

    }



    public function happiselfDeleteNotesById(Request $request){

        $message = [
            'notes_id.required'    =>  'Please enter notes ID',
        ];
        $validator = Validator::make($request->all(), [
            'notes_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $data = HappiselfUserNote::where('id' , $request->notes_id)->delete();

        return response()->json(['status' => 'success' , 'message' => 'Notes deleted successfully.']);

    }






    public function saveHappiselfContentAnswer(Request $request){
        $user = Auth::user();

        $message = [
            'content_id.required'    =>  'Please enter happiself content ID.',
            'content_type.required'    =>  'Please enter content type.',
            'answer.required'    =>  'Please enter answer.',
        ];
        $validator = Validator::make($request->all(), [
            'content_id'   => 'required',
            'content_type'   => 'required',
            'answer'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $content_details = HappiselfContent::where('id' , $request->content_id)->first();

        // return $request->answer;

        if($request->content_type == 'short_answer' || $request->content_type == 'linear_scale'){
                $data = [
                    'user_id' => $user->id,
                    'happiself_content_id' => $request->content_id,
                    'question_type' => $request->content_type,
                    'answer' => $request->answer,
                ];

                HappiselfContentAnswer::create($data);
        }
        if($request->content_type == 'question_checkbox'){

            $explode_answer = explode(',' , $request->answer);
            foreach($explode_answer as $row){
                $data = [
                    'user_id' => $user->id,
                    'happiself_content_id' => $request->content_id,
                    'question_type' => $request->content_type,
                    'answer' => $row,
                ];

                HappiselfContentAnswer::create($data);
            }
        }

        return response()->json(['status' => 'success' , 'message' => 'Answer has been submitted successfully.']);

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




}















