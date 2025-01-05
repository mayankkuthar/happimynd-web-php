<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment;
use Auth;
use App\Models\User;
use App\Models\VerifyUser;
use Mail;
use App\Mail\OtpEmail;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;
use App\Models\NotificationList;
use App\Models\RewardPointInstance;
use App\Models\UserRewardPointRecord;

use App\BusinessModel\RewardPointToUser;
use Exception;
use Illuminate\Support\Facades\Log;

class UserControllerApi extends Controller
{

    public function rewardPointToUser()
    {
        return new RewardPointToUser();
    }

    const MOBILE_OTP_TEMPLATE = "Greeting from HappiMynd,
    The OTP for your mobile verification is <OTP>";

    public function assessmentStatus(Request $request)
    {

        $user = Auth::user();
        $assessment_details = Assessment::where('user_id', $user->id)->first();

        if (!$assessment_details) {
            return response()->json(['status' => 'error', 'message' => 'Assesment is not complete.']);
        }

        if ($assessment_details->ended_at == null) {
            return response()->json(['status' => 'error', 'message' => 'Assesment is not complete.']);
        }

        if ($assessment_details->ended_at != null) {
            return response()->json(['status' => 'success', 'message' => 'Assesment is completed.']);
        }
    }


    public function sendVerificationOtp(Request $request)
    {
        $user = Auth::user();
        $type = $request->type;

        try {

            $otp = rand('111111', '999999');

            if ($type == 'mobile') {

                $check_mobile_already_exist_or_not = User::where('mobile', $request->mobile)->where('id', '!=', $user->id)->first();
                if ($check_mobile_already_exist_or_not) {
                    return response()->json(['status' => 'error', 'message' => 'Mobile number is already exist.'], 400);
                }

                $phone = $request->mobile;
                $country_code = $request->input('country_code', null);
                logger($country_code);

                $this->sendSMS($otp, $user, $phone, $country_code);

                if ($user->mobile == null) {
                    $reward_points = RewardPointInstance::where('action_performed', 'When gives phone number')->first();
                    $points_to_be_added_to_user = $reward_points->points_to_be_given;
                    $user_id = $user->id;
                    $task_performed = 'Gives phone number';
                    $this->rewardPointToUser()->addRewardToUser($user_id, $points_to_be_added_to_user, $task_performed);

                    // $reward_data = [
                    //     'user_id' => $user->id,
                    //     'points_earned' => $points_to_be_added_to_user,
                    //     'task_performed' => 'Gives phone number',
                    // ];
                    // UserRewardPointRecord::create($reward_data);
                }


                User::where('id', $user->id)->update(['mobile' => $request->mobile]);

                $data = [
                    'mobile_otp' => $otp,
                    'user_id' => $user->id,
                ];

                $check_alreday_have_entry = VerifyUser::where('user_id', $user->id)->first();

                if ($check_alreday_have_entry) {
                    VerifyUser::where('user_id', $user->id)->update($data);
                } else {
                    VerifyUser::create($data);
                }

                return response()->json(['status' => 'success', "message" => "OTP has been sent to given mobile number."]);
            } else if ($type == 'email') {

                $check_email_already_exist_or_not = User::where('email', $request->email)->where('id', '!=', $user->id)->first();
                if ($check_email_already_exist_or_not) {
                    return response()->json(['status' => 'error', 'message' => 'Email address is already exist.'], 400);
                }

                $mailDetails = [
                    'username' => $user->username,
                    'email' => $user->email,
                    'nickname' => $user->nickname,
                    'otp' => $otp,
                ];
                Mail::to($request->email)->send(new OtpEmail($mailDetails));

                if ($user->email == null) {
                    $reward_points = RewardPointInstance::where('action_performed', 'When gives email ID')->first();
                    $points_to_be_added_to_user = $reward_points->points_to_be_given;
                    $user_id = $user->id;
                    $task_performed = 'Gives email ID';
                    $this->rewardPointToUser()->addRewardToUser($user_id, $points_to_be_added_to_user, $task_performed);

                    // $reward_data = [
                    //     'user_id' => $user->id,
                    //     'points_earned' => $points_to_be_added_to_user,
                    //     'task_performed' => 'Gives email ID',
                    // ];
                    // UserRewardPointRecord::create($reward_data);
                }


                User::where('id', $user->id)->update(['email' => $request->email]);
                $data = [
                    'email_otp' => $otp,
                    'user_id' => $user->id,
                ];

                $check_alreday_have_entry = VerifyUser::where('user_id', $user->id)->first();

                if ($check_alreday_have_entry) {
                    VerifyUser::where('user_id', $user->id)->update($data);
                } else {
                    VerifyUser::create($data);
                }

                return response()->json(['status' => 'success', "message" => "OTP has been sent to your registered email address."]);
            }
        } catch (\Exception $ex) {
            return $ex->getMessage();
            return response()->json(["message" => "Unable to proceed your request, Please try later."], 400);
        }
    }




    public function sendSMS($otp, $user, $phone, $country_code = null)
    {
        try {
            if (is_null($country_code) || (string) $country_code == '91') {
                $response = Http::get('https://enterprise.smsgupshup.com/GatewayAPI/rest', [
                    'method' => 'SendMessage',
                    'send_to' => $phone,
                    'msg' => str_replace('<OTP>', $otp, self::MOBILE_OTP_TEMPLATE),
                    'msg_type' => 'TEXT',
                    'userid' => env('GUPSUP_USER_ID'),
                    'auth_scheme' => 'plain',
                    'password' => env('GUPSUP_PASSWORD'),
                    'v' => 1.1,
                    'format' => 'text'
                ]);

                logger('DOMESTIC');
                logger($response);
            } else {
                $response = Http::get('https://enterprise.smsgupshup.com/GatewayAPI/rest', [
                    'method' => 'SendMessage',
                    'send_to' => 00 . $country_code . $phone,
                    'msg' => str_replace('<OTP>', $otp, self::MOBILE_OTP_TEMPLATE),
                    'msg_type' => 'TEXT',
                    'userid' => env('GUPSUP_INTL_USER_ID'),
                    'auth_scheme' => 'plain',
                    'password' => env('GUPSUP_INTL_PASSWORD'),
                    'v' => 1.1,
                    'format' => 'text'
                ]);

                logger('INTERNATIONAL');
                logger($response);
            }
        } catch (Exception $e) {
            logger($e->getMessage());
        }
    }
}
