<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\UserProfile;
use App\Models\User;
use App\Models\Assessment;
use App\Models\Plan;
use App\Models\Coupon;
use App\Models\CouponReceipt;
use App\Models\Psychologist;
use App\Models\VerifyUser;
use App\Models\UserLanguage;
use App\Models\BundleStatus;
use App\Models\NotificationList;
use App\Models\NotificationMessage;
use App\Models\AssessmentAnswer;
use App\Models\RaiseQuery;
use App\Models\VerifyGuardian;
use App\Models\Feedback;
use App\Models\UserMood;
use App\Models\MoodMeterEmoji;
use App\Models\RewardPointInstance;
use App\Models\UserRewardPointRecord;



use App\Services\OTPService;




use Illuminate\Support\Facades\Http;

use App\Mail\OtpEmail;


use App\Models\Token;

use Mail;
use Validator;
use Hash;
use JWTAuth;
use DB;
use Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Twilio\Rest\Client;

use App\Services\BitrixService;
use Illuminate\Support\Facades\Cookie;
use App\Services\TokenService;
use Illuminate\Support\Arr;
use App\Http\Resources\QuestionResource;

use App\Services\AssessmentService;
use App\Services\ApiResponseService;

use App\Services\PaymentService;

use App\Models\Package;

use Config;

use App\BusinessModel\PushNotification;
use App\BusinessModel\RewardPointToUser;

use Illuminate\Support\Collection;


class UserAuthenticationController extends Controller
{
    
    const MOBILE_OTP_TEMPLATE = "Greeting from HappiMynd,
    The OTP for your mobile verification is <OTP>";
    
    public function pushNotification(){
        return new PushNotification();
    }

    public function rewardPointToUser(){
        return new RewardPointToUser();
    }

    public function __construct(BitrixService $bitrixService ,  AssessmentService $assessmentService , ApiResponseService $apiResponse , PaymentService $paymentService , ApiResponseService $apiService)
    {
        $this->bitrix = $bitrixService;
        $this->assessmentService = $assessmentService;
        $this->apiResponse = $apiResponse;
        $this->paymentService = $paymentService;
        $this->apiService = $apiService;
        

        $this->user = new User;
        $this->psychologist = new Psychologist;

    }   


    public function onBoarding(Request $request){
        return 1;
    }


    public function organizerList(Request $request){
        $organizations = Organization::orderBy('name' ,'asc')->AvaliableOrganization()->get();
        return response()->json(['message' => 'Organizer list get sucessfully.' , 'data' => $organizations]);
    }


    public function userProfile(Request $request){
        $userProfiles = UserProfile::orderBy('status', 'DESC')->get();
        return response()->json(['message' => 'User profile get sucessfully.' , 'data' => $userProfiles]);
    }


    public function languageList(Request $request){
        $data = UserLanguage::get();
        return response()->json(['status' => 'success' , 'message' => 'Language list get successfully.' , 'data'=>$data]);

    }

    public function signup(Request $request){

        $message = [
            'signup_type.required'      =>  'Please enter signup type',
        ];
        $validator = Validator::make($request->all(), [
            'signup_type'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $signup_type = $request->signup_type;


        if($signup_type == 'organization'){
            $message = [
                'happimyndCode.required' =>  'Please enter happimynd code.',
            ];
            $validator = Validator::make($request->all(), [
                'happimyndCode'    => 'required',
            ],$message);
            if($validator->fails()) {
                return response()->json(["message" => $validator->errors()->first()],400);
            }
        }

        $message = [
            'nickname.required'         =>  'Please enter nick name.',
            'user_profile_id.required'  =>  'Please select profile type.',
            'age.required'              =>  'Please enter age.',
            'gender.required'           =>  'Please select gender.',
            'username.required'        =>  'Please enter user name.',
            'username.unique'          =>  'Username is already taken',
            'password.required'         =>  'Please enter password.',
            'confirm_password.required' =>  'Please enter confirm password.',
            'language.required' =>  'Please select language.',

        ];
        $validator = Validator::make($request->all(), [
            'nickname'      => 'required|min:2|max:200',
            'user_profile_id'  => 'required',
            // 'age'           => 'required', 
            // 'gender'        => 'required',
            'username'     => 'required|unique:users,username',
            'password'      => 'required|min:6',
            'confirm_password'      => 'required|min:6',
            'language'      => 'required|exists:user_languages,id',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }


        try {
            DB::beginTransaction();
            $formData = $request->all();
            if (config('constants.bitrix')) {

                

                /** If user signup using happimyndCode.! B2B*/
                // if ($request->input('happimyndCode')) {
                if ($signup_type == 'organization') {

                    $formData['happimynd_code'] = $request->input('happimyndCode');
                    
                    /** Fetch the organization name of the corresponding happimyndCode. */
                    $organizationName = Token::where('token', $request->input('happimyndCode'))
                        ->with('organization')
                        ->first()
                        ->organization->name;

                    $formData['dealCategory'] = 'B2B_Journey';

                    /** Add user as a lead. */
                    $addDealBitrixResponse  = $this->bitrix->addDeal($formData, "", $organizationName);
                    if ($addDealBitrixResponse->result) {
                        $formData['deal_id'] = $addDealBitrixResponse->result;

                        

                    }
                }
                /** if user is not signup using happimyndCode.!! B2C*/
                else {

                    /** Add user as a lead. */
                    $addLeadBitrixResponse  = $this->bitrix->addLead($formData, true);
                    if ($addLeadBitrixResponse->result) {
                        $formData['lead_id'] = $addLeadBitrixResponse->result;
                        /** Add user for the deal. */
                        $addDealBitrixResponse = $this->bitrix->addDeal($formData, $addLeadBitrixResponse->result, "");
                        if ($addDealBitrixResponse->result) {
                            $formData['deal_id'] = $addDealBitrixResponse->result;
                        }
                    }
                }
            }


            $selected_language_details = UserLanguage::where('id' , $request->language)->first();
            $language = strtolower($selected_language_details->name);
            
            $formData['language'] =  $language;
            $formData['platform'] =  'mobile';

            $formData['device_token'] =  $request->device_token;

            if($request->gender){
                $formData['avatar'] = ($formData['gender'] != 'other') ? $formData['gender'] . '1.svg' : 'female1.svg';
            }


            $referral_code = $request->referral_code;
            if($referral_code != ""){
                $check_referral_code_valid = User::where('refer_code',$referral_code)->first();
                if($check_referral_code_valid){
                    $formData['from_refer_code'] =  $referral_code;
                    $reward_points = RewardPointInstance::where('action_performed' , 'When share app')->first();
                    $points_to_be_added_to_user = $reward_points->points_to_be_given;
                    $user_id = $check_referral_code_valid->id;
                    $task_performed = 'Friend download from referral';
                    $this->rewardPointToUser()->addRewardToUser($user_id , $points_to_be_added_to_user , $task_performed);
                    
                    // $reward_data = [
                    //     'user_id' => $check_referral_code_valid->id,
                    //     'points_earned' => $points_to_be_added_to_user,
                    //     'task_performed' => 'Friend download from referral',
                    // ];
                    // UserRewardPointRecord::create($reward_data);
                }else{
                    return response()->json(['status' => 'error' , 'message' => 'Invalid referral code']);
                }
            }

            
            if ($formData['signup_type'] == 'individual') {
                $user = User::create(Arr::except($formData, ['profession']));
            } 
            else {
                $username = $request->input('username');
                $user = User::updateOrCreate(
                    [
                        'username' => $username
                    ],
                    Arr::except($formData, ['profession'])
                );
            }
            if (isset($formData['signup_type']) && $formData['signup_type'] == 'organization' && !(new TokenService)->assignToken($request->input('happimyndCode'), $user->id)) {
                return response()->json(['message' => 'error expiring Token for user_id=' . $user->id . ' with code=' . $request->input('happimyndCode') . ';']);
            }
            $token = auth('user')->login($user);
            $cookie = Cookie::make('user-access-token', $token);
            Cookie::queue($cookie);
            DB::commit();

            $user->id;

            $user_details = User::where('id' , $user->id)->first();

            $data = [
                'user_id' => $user->id,
                'message' => 'Self awareness is now trending! Want to boast your strengths and become a stronger you? Take the HappiLIFE screening now!',
            ];
            NotificationList::create($data);

            if (! $data = JWTAuth::FromUser($user_details)) {
                return response()->json(['status' => 'failed', 'message' => 'Try try after sometime.', 'error' => 'Unauthorized'], 401);
            }

            return $this->createNewToken($data);

            // return response()->json(['message' => 'Register sucessfully.' , 'data' => $formData]);

        } catch (Exception $e) {
            throw $e;
            DB::rollBack();
        }


    }


    public function entryViaOrg(Request $request){
        $message = [
            'organization_id.required'  =>  'Please enter organization ID.',
            'happimynd_code.required'            =>  'Please enter Happimynd code.',
        ];
        $validator = Validator::make($request->all(), [
            'organization_id'      => 'required|exists:organizations,id',
            'happimynd_code'                => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $organization_id = $request->organization_id;
        $happimynd_code = $request->happimynd_code;

        $check_data = Token::where('organization_id' , $organization_id)->where('token' , $happimynd_code)->first();
        if($check_data){
            // $check_code_already_used  = User::where('happimynd_code' , $happimynd_code)->first();
            // if($check_code_already_used){
            //     return response()->json(['status'=>'error' ,'message' => 'Code is already used.'],400 );
            // }
            if($check_data->use_count < $check_data->use_limit){
                return response()->json(['status'=>'success' ,'message' => 'Code verified. Now you can signup now.'],200 );
            }else{
                return response()->json(['status'=>'error' ,'message' => 'Max user limit for this code is over.'],400 );
            }
        }
        else{
            return response()->json(['status'=>'error' ,'message' => 'Invalid happimynd code.' ],400 );
        }
    }



    



    public function login(Request $request){

        // return $this->pushNotification()->sendNotification("ExponentPushToken[9BAQHqI4m5L1ndnQE4g5Dt]","ss" , 'sdaasdas');

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $data = JWTAuth::attempt($validator->validated())) {
            return response()->json(['status' => 'failed', 'message' => 'Invalid username and password.', 'error' => 'Unauthorized'], 401);
        }

        // User::where('username' , $request->username)->update(['device_token' => $request->device_token]);
        $username = $request->username;
        $device_token = $request->device_token;

        return $this->createNewTokenLogin($data, $username, $device_token);

    }


    protected function createNewTokenLogin($data ,$username = "", $device_token = ""){

        $user_Details = Auth::user();

        if($user_Details->is_account_deleted == '1'){
            return response()->json(['status' => 'failed' , 'message' => 'Invalid username and password.' , 'error' => 'Unauthorized'], 401);
        }

        if($user_Details->device_token && $user_Details->device_token != $device_token && $device_token && strlen($device_token) > 20){
            $message = "Is that you? 
Help us keep you safe. Tell us if you signed in from another device😵🤯😨 Check Here NOW!";
            $title = "Sign in from another device";
            $this->pushNotification()->sendNotification($user_Details->device_token,$message , $title);
        }

        User::where('username' , $username)->update(['device_token' => $device_token]);

        return response()->json([
            'access_token' => $data,
            'token_type' => 'bearer',
            // 'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => Auth::user()
        ]);
    }




    protected function createNewToken($data){

        $user_Details = Auth::user();
        if($user_Details->is_account_deleted == '1'){
            return response()->json(['status' => 'failed' , 'message' => 'Invalid username and password.' , 'error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'access_token' => $data,
            'token_type' => 'bearer',
            // 'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => Auth::user()
        ]);
    }



    public function loginWithCode(Request $request){
        $message = [
            'happimynd_code.required'    => 'Please enter happimynd code.',
        ];

        $validator = Validator::make($request->all(), [
            'happimynd_code' => 'required',   
        ], $message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400); 
        }

        $happimynd_code = $request->happimynd_code;

        $user_details = User::where('happimynd_code' , $happimynd_code)->first();
        if($user_details){

            if (! $data = JWTAuth::FromUser($user_details)) {
                return response()->json(['status' => 'failed', 'message' => 'Invalid username and password.', 'error' => 'Unauthorized'], 401);
            }

            User::where('username' , $user_details->username)->update(['device_token' => $request->device_token]);


            return $this->createNewToken($data);

        }else{
            return response()->json(['status' => 'error', 'message' => 'Invalid happimynd code.']);
        }

    }



    public function logout() {
        $user = Auth::user();
        User::where('id' , $user->id)->update(['device_token' => null]);
        auth()->logout();
        return response()->json(['status' => 'success', 'message' => 'User logged out successfully']);
    }



    public function changePassword(Request $request) {
        $user = Auth::user();

        $message = [
            'old_password.required'    => 'Please enter old password.',
            'new_password.required'    => 'Please enter new password.',
            'confirm_password.required'=> 'Please enter comfirm password.',
            'confirm_password.same'    => "Password and confirm password does not match."
        ];

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',   
            'new_password' => 'required',   
            'confirm_password'  => 'required|required_with:new_password|same:new_password'
        ], $message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400); 
        } 

        $where = [
            'id'    => $user->id,
        ];

        $data = [
            'password'    => Hash::make($request->new_password),
        ];
        
        $old_password = $request->old_password;

        if(Hash::check($old_password,$user->password)) {
            User::where($where)->update($data);

            $device_token = $user->device_token;
            // $message = 'YO! No One Can Lock You Out NOW!';
            $noti_message_detail = NotificationMessage::where('type' , 'After password is changed')->pluck($user->language);
            $message = $noti_message_detail[0];

            $this->pushNotification()->sendNotification($device_token,$message);

            $data = [
                'user_id' => $user->id,
                'message' => $message,
            ];
            NotificationList::create($data);


            return response()->json(['status'=> 'success' ,"message" => "Password has been changed successfully."]);
        } else {
            return response()->json(['status'=> 'error' ,"message" => "Please enter valid old password."],400);
        }
    }



    public function getProfile(Request $request){
        $user_id = Auth::user()->id;
        $user_details = User::where('id' , $user_id)->with('profileType' , 'VerifyUser','userToken')->first();
        return response()->json(['status'=> 'success' ,"message" => "User detials get successfully." , 'data' => $user_details]);
    }



    public function editProfile(Request $request){
        $user = Auth::user();

        $message = [
            'nickname.required'         =>  'Please enter nick name.',
            'age.required'              =>  'Please enter age.',
            'gender.required'           =>  'Please select gender.',
            'username.required'        =>  'Please enter user name.',
            'username.unique'          =>  'Username is already taken',
        ];
        $validator = Validator::make($request->all(), [
            'nickname'      => 'required|min:2|max:200',
            'age'           => 'required', 
            'gender'        => 'required',
            'username'     => 'required|unique:users,username,'.$user->id,

            // 'email' => 'sometimes|required|email|unique:users',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $data = [
            'nickname' => $request->nickname,
            // 'user_profile_id' => $request->user_profile_id,
            'age' => $request->age,
            'gender' => $request->gender,
            'username' => $request->username,
        ];

        if($request->email != ''){
            $check_email_already_exist = User::where('email' , $request->email)->where('id', '!=' , $user->id)->first();
            if($check_email_already_exist){
                return response()->json(['status' => 'error' , 'message' => 'Email already taken']);
            }else{

                if($user->email == null){
                    $reward_points = RewardPointInstance::where('action_performed' , 'When gives email ID')->first();
                    $points_to_be_added_to_user = $reward_points->points_to_be_given;
                    $user_id = $user->id;
                    $task_performed = 'Gives email ID';
                    $this->rewardPointToUser()->addRewardToUser($user_id , $points_to_be_added_to_user , $task_performed);

                    // $reward_data = [
                    //     'user_id' => $user->id,
                    //     'points_earned' => $points_to_be_added_to_user,
                    //     'task_performed' => 'Gives email ID',
                    // ];
                    // UserRewardPointRecord::create($reward_data);
                }

                $data['email'] = $request->email;
            }
        }

        if($request->mobile != ''){
            $check_email_already_exist = User::where('mobile' , $request->mobile)->where('id', '!=' , $user->id)->first();
            if($check_email_already_exist){
                return response()->json(['status' => 'error' , 'message' => 'Mobile number already taken']);
            }else{

                if($user->mobile == null){
                    $reward_points = RewardPointInstance::where('action_performed' , 'When gives phone number')->first();
                    $points_to_be_added_to_user = $reward_points->points_to_be_given;
                    $user_id = $user->id;
                    $task_performed = 'Gives phone number';
                    $this->rewardPointToUser()->addRewardToUser($user_id , $points_to_be_added_to_user , $task_performed);

                    // $reward_data = [
                    //     'user_id' => $user->id,
                    //     'points_earned' => $points_to_be_added_to_user,
                    //     'task_performed' => 'Gives phone number',
                    // ];
                    // UserRewardPointRecord::create($reward_data);
                }

                $data['mobile'] = $request->mobile;
            }
        }

        $data['avatar'] = ($data['gender'] != 'other') ? $data['gender'] . '1.svg' : 'female1.svg';

        $is_updated = User::where('id' , $user->id)->update($data);
        if($is_updated){
            
            $device_token = $user->device_token;
            $noti_message_detail = NotificationMessage::where('type' , 'Any update in the profile')->pluck($user->language);
            $message = $noti_message_detail[0];

            // $message = 'Thank you for telling us more about you! Your profile has been successfully updated.';
            $this->pushNotification()->sendNotification($device_token,$message);

            $notification_data = [
                'user_id' => $user->id,
                'message' => $message,
            ];
            NotificationList::create($notification_data);

            return response()->json(['status' => 'success' , 'message' => "Profile has been updated sucessfully. " ,'data' => $data]);
        }else{
            return response()->json(['status' => 'error' , 'message' => "Unable to update profile, try after sometime"], 400);
        }

    }




    public function saveEmail(Request $request){
        $user = Auth::user();

        $message = [
            'email.required' =>  'Please enter email.',
        ];
        $validator = Validator::make($request->all(), [
            'email'      => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }
 

        $check_email_already_exist = User::where('email' , $request->email)->where('id', '!=' , $user->id)->first();
        if($check_email_already_exist){
            return response()->json(['status' => 'error' , 'message' => 'Email already taken']);
        }else{

            if($user->email == null){
                $reward_points = RewardPointInstance::where('action_performed' , 'When gives email ID')->first();
                $points_to_be_added_to_user = $reward_points->points_to_be_given;
                $user_id = $user->id;
                $task_performed = 'Gives email ID';
                $this->rewardPointToUser()->addRewardToUser($user_id , $points_to_be_added_to_user , $task_performed);

            }

            User::where('id' , $user_id)->update(["email" => $request->email]);

            return response()->json(['status' => 'success' , 'message' => "Email save successfully"], 200);
        }

    }




    public function startAssessment(Request $request){
        // return 4;

        $user_id =  auth('user')->user()->id;

        $platform = $request->platform;
        if($platform == null){
            $platform = 'website';
        }
        Assessment::where('user_id' , $user_id)->update(['platform' => $platform]);


        $is_already_assesment = Assessment::where('user_id',$user_id)->first();

        if($is_already_assesment && $is_already_assesment->ended_at != null){
            return response()->json(['status' => 'true' , 'message' => 'Your assessment is already completed.']);
        }

        if($is_already_assesment && $is_already_assesment->ended_at == null){
            $assessment_number = $is_already_assesment->id;
        }else{
            $assessment_number =  $this->apiResponse->success([
                'assessment_id' => $this->assessmentService
                    ->forUser(auth('user')->user()->id)
                    ->initiateAssessment()->assessmentId
            ]);
        }
        

        if($assessment_number){

            $assessment = Assessment::where('user_id' , $user_id)->first();
            $assessmentId = $assessment->id;

            $data = QuestionResource::collection(
                $this->assessmentService
                    ->forAssessmentApp($assessmentId)
                    ->getRemainingQuestionsApp()
            );

            $additional_data = ['perPage' => $this->assessmentService->questionsPerPage, 'answered' => $this->assessmentService->answeredQuestionsCount, 'total' => $this->assessmentService->totalQuestionsCount, 'current_page' => $this->assessmentService->getPageNumber()];
            
            return response()->json(['status' => 'success' , 'message' => 'Questions get sucessfully.' , 'questions' => $data , 'overview' => $additional_data]);
        }
        return response()->json(['status' => 'success' , 'message' => 'Invalid assessment']);


    }



    public function saveOption(Request $request){
        // return auth::user();
        // return 33;
        $user_id = Auth::user()->id;

        $message = [
            'option_question_id.required'   =>  'Please enter question id.',
        ];
        $validator = Validator::make($request->all(), [
            'option_question_id'  => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }


        $assessment_data = Assessment::where('user_id' , $user_id)->first();
        $assessmentId = $assessment_data->id;

        $optionQuestionId = $request->option_question_id;
        $data =  $this->apiResponse->success(
            $this->assessmentService
                ->saveAssessmentOption($assessmentId, $optionQuestionId)
                // ->saveAssessmentOptionApp($assessmentId, $optionQuestionId)
        );

        return $data; 

    }


    public function completeAssessment(Request $request){
        $user = Auth::user();
        $reward_points = RewardPointInstance::where('action_performed' , 'When HappiLIFE Assessment is taken up')->first();
        $points_to_be_added_to_user = $reward_points->points_to_be_given;
        $user_id = $user->id;
        $task_performed = 'Complete HappiLIFE screening';
        $this->rewardPointToUser()->addRewardToUser($user_id , $points_to_be_added_to_user , $task_performed);
        // $reward_data = [
        //     'user_id' => $user->id,
        //     'points_earned' => $points_to_be_added_to_user,
        //     'task_performed' => 'Complete HappiLIFE screening',
        // ];
        // UserRewardPointRecord::create($reward_data);

        return response()->json(['status' => 'success' , "message" => "Screening has been completed successfully."]);
    }




    public function check(Request $request){
        return 34;
    }



    public function forgotPassword(Request $request){
        
        $type = $request->type;

        if($type == 'email'){
            $message = [
                'email.required'    => 'Please enter email address.',
                'email.email'       => 'Please enter valid email address.',
                'email.exists'      => 'Please enter registered email address.'
            ];

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users',   
            ], $message);

            if($validator->fails()) {
                return response()->json(["message" => $validator->errors()->first()],400);
            }
        }


        if($type == 'mobile'){
            $message = [
                'mobile.required'    => 'Please enter mobile number.',
                'mobile.exists'      => 'Mobile number doesnot exist.'
            ];

            $validator = Validator::make($request->all(), [
                'mobile' => 'required|exists:users',   
            ], $message);

            if($validator->fails()) {
                return response()->json(["message" => $validator->errors()->first()],400);
            }
        }


        try{
            
            $otp = rand('111111','999999');

            if ($type == 'mobile') {

                $check_user = User::where('mobile', $request->mobile)->with('verifyUser')->first();

                // VerifyUser::where('user_id' , $check_user->id)->delete();

                $this->sendSMS($otp , $check_user);

                $data = [
                    'mobile_otp' => $otp,
                    'user_id' => $check_user->id,
                ];
                
                $check_alreday_have_entry = VerifyUser::where('user_id' ,$check_user->id)->first();

                if($check_alreday_have_entry){
                    VerifyUser::where('user_id' , $check_user->id)->update($data);
                }else{
                    VerifyUser::create($data);
                }

                return response()->json(['status' => 'success' , "message" => "OTP has been sent to your registered mobile number."]);

            } 
            else if ($type == 'email') {

                $check_user = User::where('email', $request->email)->with('verifyUser')->first();

                // VerifyUser::where('user_id' , $check_user->id)->delete();
                
                $mailDetails = [
                    'username' => $check_user->username,
                    'email' => $check_user->email,
                    'nickname' => $check_user->nickname,
                    'otp' => $otp,
                ];
                Mail::to($request->email)->send(new OtpEmail($mailDetails));

                $data = [
                    'email_otp' => $otp,
                    'user_id' => $check_user->id,
                ];
                
                $check_alreday_have_entry = VerifyUser::where('user_id' ,$check_user->id)->first();
                
                if($check_alreday_have_entry){
                    VerifyUser::where('user_id' , $check_user->id)->update($data);
                }else{
                    VerifyUser::create($data);
                }

                return response()->json(['status' => 'success' , "message" => "OTP has been sent to your registered email address."]);

            }

        }catch(\Exception $ex) {
            return $ex->getMessage();
            return response()->json(["message" => "Unable to proceed your request, Please try later."],400);
        }

        return response()->json(["message" => "A reset password link has been sent to your registered email address."]);



    }





    public function sendSMS($otp , $check_user)
    {
        // $country_code = '+91';
        // $phone_number = $check_user->mobile;

        // $receiverNumber = $country_code.''.$phone_number;

        // $message = 'Your OTP is '.$otp.' for Happimynd account.';

        try {

            // $account_sid = getenv("TWILIO_SID");
            // $auth_token = getenv("TWILIO_TOKEN");
            // $twilio_number = getenv("TWILIO_FROM");

            // $client = new Client($account_sid, $auth_token);
            // $client->messages->create($receiverNumber, [
            //     'from' => $twilio_number,
            //     'body' => $message]);
            $response = Http::get('https://enterprise.smsgupshup.com/GatewayAPI/rest', [
                'method' => 'SendMessage',
                'send_to' => $check_user->mobile,
                'msg' => str_replace('<OTP>', $otp, self::MOBILE_OTP_TEMPLATE),
                'msg_type' => 'TEXT',
                'userid' => env('GUPSUP_USER_ID'),
                'auth_scheme' => 'plain',
                'password' => env('GUPSUP_PASSWORD'),
                'v' => 1.1,
                'format' => 'text'
            ]);


        } catch (Exception $e) {
            dd("Error: ". $e->getMessage());
        }

    }



    public function verifyOtp(Request $request)
    {
        $message = [
            'otp.required'    => 'Please enter otp.',
        ];

        $validator = Validator::make($request->all(), [
            'otp' => 'required',   
        ], $message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }


        if($request->email){
            $user_Detail = User::where('email', $request->email)->with('verifyUser')->first();
            if($user_Detail->verifyUser->email_otp == $request->otp){
                VerifyUser::where('user_id' , $user_Detail->id)->update(['email_verify' => 1]);
                return response()->json(['status' => 'success' , 'message' => 'OTP is verified']);
            }else{
                return response()->json(['status' => 'error' , 'message' => 'Invalid OTP']);
            }

        }else{
            $user_Detail = User::where('mobile' , $request->mobile)->with('verifyUser')->first();
            if($user_Detail->verifyUser->mobile_otp == $request->otp){
                VerifyUser::where('user_id' , $user_Detail->id)->update(['mobile_verify' => 1]);
                return response()->json(['status' => 'success' , 'message' => 'OTP is verified']);
            }else{
                return response()->json(['status' => 'error' , 'message' => 'Invalid OTP']);
            }
        }

    }



    public function resetPassword(Request $request){

        $message = [
            'password.required'    => 'Please enter password.',
            'confirm_password.required'    => 'Please enter confirm password.',

        ];

        $validator = Validator::make($request->all(), [
            'password' => 'required',   
            'confirm_password' => 'required',   

        ], $message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $password = Hash::make($request->password);

        if($request->email != ''){

            $user_Detail = User::where('email' , $request->email)->with('verifyUser')->first();

            User::where('email', $request->email)->update(['password' => $password]);

            VerifyUser::where('user_id' , $user_Detail->id)->delete();

            return response()->json(['status' => 'success' , 'message' => 'Password has been reset successfully.']);

        }

        if($request->mobile != ''){
            $user_Detail = User::where('mobile' , $request->mobile)->with('verifyUser')->first();

            User::where('mobile', $request->mobile)->update(['password' => $password]);

            VerifyUser::where('user_id' , $user_Detail->id)->delete();

            return response()->json(['status' => 'success' , 'message' => 'Password has been reset successfully.']);
        }

        else{
            return response()->json(['status' => 'error' , 'message' => 'Plesae enter email or mobile number.']);
        }


    }




    
    public function viewReport(Request $request){
        $user = Auth::user();
        $assessmentId = Assessment::where('user_id' , $user->id)->first();
        $link = url('calculate-score')."?assessment_id=".$assessmentId->id;
        return response()->json(['status' => 'success' , 'link' => $link]);
    }



    public function getReport(Request $request){
        $user = Auth::user();
        $assessment_details = Assessment::where('user_id' , $user->id)->first();

        // $is_payment_done = BundleStatus::where('user_id',$user->id)->where('plan_id' , 1)->first();

        if(!$assessment_details){
            return response()->json(['status' => 'error' , 'message' => 'Assesment is  not completed yet.']);
        }

        if($assessment_details->ended_at == null){
            return response()->json(['status' => 'error' , 'message' => 'Assesment is  not completed yet.']);
        }

        if($assessment_details->ended_at == null){
            return response()->json(['status' => 'error' , 'message' => 'Assesment is  not completed yet.']);
        }

        // if(!$is_payment_done && $assessment_details->ended_at != null){
        //     return response()->json(['status' => 'error' , 'message' => 'Please make payment first to get report.']);
        // }

        if (is_null($assessment_details->report)) {

            $response = Http::get(env('NODE_URL') . '/check');
            if ($response->ok()) {
                $response = Http::get(env('NODE_URL') . '/pdf?reportUrl=' . env('APP_URL') . '/calculate-score?assessment_id=' . $assessment_details->id . '&fileName=' . $assessment_details->id . '_' . $user->nickname.'testing' . '-ScreeningReport.pdf');
                $res = $response->json();

                // print_r($res['link']);
                \Log::info('response body:' . json_encode($res));
                // $assessment->report = $res['link'];
                $assessment_details->update(['report' => $res['link']]);
            } else {
                \Log::critical('respone not ok');
                \Log::critical($response);
            }
        }
 
        return response()->json(['status' => 'success' , 'message' => 'Report get successfully.' , 'url' => $assessment_details->report]);
    }


    public function updateLastAnswer(Request $request){

        $user = Auth::user();

        $message = [
            'option_question_id.required'   =>  'Please enter question id.',
        ];
        $validator = Validator::make($request->all(), [
            'option_question_id'  => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $users_assessment = Assessment::where('user_id' , $user->id)->first();

        $get_last_submit_question = AssessmentAnswer::where('assessment_id' , $users_assessment->id)->orderBy('id' , 'desc')->first();

        $get_last_submit_question->option_question_id = $request->option_question_id;
        $get_last_submit_question->update();

        return response()->json(['status' => 'success' , 'message' => 'Answer has been update successfully.']);


    }




    public function deleteAccount(Request $request){
        $user_details = Auth::user();
        User::where('id' , $user_details->id)->update(['is_account_deleted' => '1']);
        return response()->json(['status' => 'success' , 'message' => 'Account has been deleted Successfully.']);
    }



    public function raiseQueryApp(Request $request){

        $user_Detail = Auth::user();

        $message = [
            'category.required'      =>  'Please enter category.',
            'description.required'      =>  'Please enter description.',
        ];
        $validator = Validator::make($request->all(), [
            'category'   => 'required',
            'description'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $data = [
            'category' => $request->category,
            'query' => $request->description,
            'user_id' => $user_Detail->id,
            'platform' => "application",
            "status" => 0,
        ];

        $is_created_query = RaiseQuery::create($data);

        return response()->json(['status' => "success" , "message" => "Quey has been raised successfully."]);

    }



    public function gurdianVerification(Request $request){
        $message = [
            'type.required'      =>  'Please enter type.',
            'random_unique_id.required'      =>  'Please enter unique ID.',
        ];
        $validator = Validator::make($request->all(), [
            'type'   => 'required',
            'random_unique_id'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $type = $request->type;
        $otp = rand(100000, 999999);
        $random_unique_id = $request->random_unique_id;

        if($type == "email"){
            $message = [
                'email.required'      =>  'Please enter email.',
            ];
            $validator = Validator::make($request->all(), [
                'email'   => 'required',
            ],$message);

            if($validator->fails()) {
                return response()->json(["message" => $validator->errors()->first()],400);
            }

            $email = $request->email;

             $mailDetails = [
                'username' => 'Guardian',
                'email' => $email,
                'nickname' => 'Guardian',
                'otp' => $otp,
            ];

            // using mailable class to send markdown email
            Mail::to($email)->queue(new OtpEmail($mailDetails));
            $data = [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(60),
                'email' => $email,
                'session' => $random_unique_id,
             ];
             VerifyGuardian::create($data);

            return response()->json(['status' => "success" , "message" => "Verification OTP has been sent to provided email address."]);

        }

        if($type == "mobile"){
            $message = [
                'mobile.required'      =>  'Please enter mobile.',
            ];
            $validator = Validator::make($request->all(), [
                'mobile'   => 'required',
            ],$message);

            if($validator->fails()) {
                return response()->json(["message" => $validator->errors()->first()],400);
            }

            $mobile = $request->mobile;

            $response = OTPService::sendAnonymousMobileOtp($otp, $mobile);
            // Send SMS
            if ($response == true) {
                $data = [
                    'otp' => $otp,
                    'expires_at' => now()->addMinutes(60),
                    'mobile' => $mobile,
                    'session' => $random_unique_id,
                 ];
                 VerifyGuardian::create($data);
            return response()->json(['status' => "success" , "message" => "Verification OTP has been sent to provided phone number."]);
            }  
        }

    }


    public function verifyGuardianOtp(Request $request){
        $message = [
            'otp.required'      =>  'Please enter otp.',
            'unique_id.required'      =>  'Please enter unique ID.',
        ];
        $validator = Validator::make($request->all(), [
            'otp'   => 'required',
            'unique_id'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $gurdain_verification_detail = VerifyGuardian::where('session' , $request->unique_id)->orderBy('id' , 'desc')->first();
        if(!$gurdain_verification_detail){
            return response()->json(['status' => "error" , "message" => "Invalid ID."]);
        }else{
            if($request->otp == $gurdain_verification_detail->otp){
                $gurdain_verification_detail->verified = 1;
                $gurdain_verification_detail->save();
                return response()->json(['status' => "success" , "message" => "Otp verified successfully."]);
            }else{
                return response()->json(['status' => "error" , "message" => "Invalid OTP."]);
            }
        }

    }


    public function feedback(Request $request){

        $user = Auth::user();

        $message = [
            'application_rate_emoji_id.required'      =>  'Please enter emoji ID.',
            'feedback_message.required'      =>  'Please enter message.',
        ];
        $validator = Validator::make($request->all(), [
            'application_rate_emoji_id'   => 'required',
            'feedback_message'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }



        $is_already_gave_feedback = Feedback::where('user_id' , $user->id)->first();
        if(!$is_already_gave_feedback){
            $reward_points = RewardPointInstance::where('action_performed' , 'When feedback is shared')->first();
            $points_to_be_added_to_user = $reward_points->points_to_be_given;
            $user_id = $user->id;
            $task_performed = 'feedback is shared';
            $this->rewardPointToUser()->addRewardToUser($user_id , $points_to_be_added_to_user , $task_performed);

            // $reward_data = [
            //     'user_id' => $user->id,
            //     'points_earned' => $points_to_be_added_to_user,
            //     'task_performed' => 'feedback is shared',
            // ];
            // UserRewardPointRecord::create($reward_data);
        }


        $data = [
            'user_id' => $user->id,
            'application_rate_emoji_id' => $request->application_rate_emoji_id,
            'feedback_message' => $request->feedback_message,
        ];
        $is_created = Feedback::create($data);

        return response()->json(['status' => 'success' , 'message' => 'Feedback has been submit successfully.' , 'data' => $is_created]);
    }



    public function moddEmojiList(Request $request){
        $data = MoodMeterEmoji::get();

        $sortOrder = ["Delighted", "Happy", "Confused", "Disappointed", "Sad", "Angry", "Crying", "Scared", "Anxious"];
 
        $sortedData = Collection::make($data)->sortBy(function ($item) use ($sortOrder) {
            return array_search(ucfirst($item['name']), $sortOrder);
        })->values()->all();

        return response()->json(['status' => 'success' , 'message' => "Mood emoji list get successfully." , 'data' => $sortedData]);
    }

    public function userMood(Request $request){
        $user = Auth::user();
        $date = date('Y-m-d');
        $time = date('h:i:s');

        $message = [
            'emoji_id.required'  =>  'Please enter emoji ID.',
            'text.required'      =>  'Please enter text.',
        ];
        $validator = Validator::make($request->all(), [
            'emoji_id'   => 'required',
            'text'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }


        $noti_emit_status = true;

        $last_6_mood = UserMood::where('user_id' , $user->id)->orderBy('id' , 'desc')->limit(6)->get();
        
        if(count($last_6_mood) == 6){
            foreach($last_6_mood as $row){
                if($row->emoji_id == 1 || $row->emoji_id == 5){
                    $noti_emit_status = false;
                }
            }

            if($noti_emit_status){
                if($request->emoji_id != 1 && $request->emoji_id != 5){
                    if($user->device_token && strlen($user->device_token) > 20){
                        $message = "Hey! It's seems you're feeling dull 😟 these days. Don't forget you have a BUDDY waiting for you to share and care. 🤗 Login to HappiBUDDY now!";
                        $title ="HappiBuddy";
                        $this->pushNotification()->sendNotification($user->device_token,$message , $title);
                    }
                }
                
            }
        }
        

        

        $data = [
            'user_id' => $user->id,
            'emoji_id' => $request->emoji_id,
            'text' => $request->text,
            'date' => $date,
            'time' => $time,
        ];

        UserMood::create($data);


        $reward_points = RewardPointInstance::where('action_performed' , 'When punch in Mood in Mood O meter')->first();
        $points_to_be_added_to_user = $reward_points->points_to_be_given;
        $user_id = $user->id;
        $task_performed = 'Punch in mood O meter';
        $this->rewardPointToUser()->addRewardToUser($user_id , $points_to_be_added_to_user , $task_performed);

        // $reward_data = [
        //     'user_id' => $user->id,
        //     'points_earned' => $points_to_be_added_to_user,
        //     'task_performed' => 'Punch in O meter',
        // ];
        // UserRewardPointRecord::create($reward_data);

        return response()->json(['status' => "success" , "message" => "Mood has been recorded."]);

    }



    public function totalRewardPointsUser(Request $request){
        $user = Auth::user();
        $total_reward_points = UserRewardPointRecord::where('user_id' , $user->id)->sum('points_earned');
        return response()->json(['status' => 'success' , 'message' => 'Reward points get successfully.' , 'total_reward_points' => $total_reward_points]);
    }


    public function myReferralCode(Request $request){
        $user = Auth::user();
        if($user->refer_code == null){

            $date =  Date('d');
            $month =  Date('m');

            $seed = str_split('abcdefghijklmnopqrstuvwxyz'); 
            $index_start = array_rand($seed);
            $random_letter_start = $seed[$index_start];

            $index_mid = array_rand($seed);
            $random_letter_mid = $seed[$index_mid];

            $index_end = array_rand($seed);
            $random_letter_end = $seed[$index_end];

            $code = $random_letter_start.$date.$random_letter_mid.$month.$random_letter_end;
            User::where('id' , $user->id)->update(['refer_code' => $code]);
        }else{
            $code = $user->refer_code;
        }
        return response()->json(['status' => 'success' , 'message' => 'Referral code get successfully.' , 'code' => $code]);
    }



    public function rewardInstancesList(Request $request){
        $list = RewardPointInstance::get();
        return response()->json(['status' => 'success' , 'message' => 'Reward instance get successfully.' , 'list' => $list]);
    }


    public function onOffStatus(){
        return response()->json(['status' => 'success' , 'is_open' => 1]);
    }


}










