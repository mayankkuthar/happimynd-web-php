<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Psychologist;
use App\Models\VerifyPsychologist;

use Validator,Hash;
use JWTAuth;
use DB,Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpEmail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PsychologistAuthenticationController extends Controller
{
    public function psychologistLogin(Request $request){

        $formData = $request->all();

        $credentials = ['email' => $formData['email'], 'password' => $formData['password']];
        if (!$token = auth('psychologist')->attempt($credentials)) {
                        return response()->json(['status' => 'failed', 'message' => 'Invalid email address and password.', 'error' => 'Unauthorized'], 401);
        }

        if($request->device_token){
            Psychologist::where('email' , $formData['email'])->update(['device_token' => $formData['device_token']]);
        }

        return $this->createNewTokenPsychologist($token);

    }

    public function psychologistLogout() {
        $psychologist = Auth::guard('psychologist')->user();
        Psychologist::where('id' , $psychologist->id)->update(['device_token' => null]);
        Auth::guard('psychologist')->logout();
        return response()->json(['status' => 'success', 'message' => 'Psychologist logged out successfully']);
    }

    protected function createNewTokenPsychologist($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth::guard('psychologist')->user()
        ]);
    }

    public function psychologistCheck(Request $request){
        return auth::guard('psychologist')->user();
    }

    public function forgotPassword(Request $request){
        $message = [
            'email.required'    => 'Please enter email address.',
            'email.email'       => 'Please enter valid email address.',
            'email.exists'      => 'Please enter registered email address.'
        ];
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:psychologists',
        ], $message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }
        try{
            $otp = rand('111111','999999');
                $check_user = Psychologist::where('email', $request->email)->with('verifyPsychologist')->first();
                // VerifyUser::where('user_id' , $check_user->id)->delete();
                $mailDetails = [
                    'nickname' => $check_user->username,
                    'email' => $check_user->email,
                    'first_name' => $check_user->first_name,
                    'otp' => $otp,
                ];
                Mail::to($request->email)->send(new OtpEmail($mailDetails));
                $data = [
                    'email_otp' => $otp,
                    'psychologist_id' => $check_user->id,
                ];
                $check_alreday_have_entry = VerifyPsychologist::where('psychologist_id' ,$check_user->id)->first();
                if($check_alreday_have_entry){
                    VerifyPsychologist::where('psychologist_id' , $check_user->id)->update($data);
                }else{
                    VerifyPsychologist::create($data);
                }
                return response()->json(['status' => 'success' , "message" => "OTP has been sent to ".$check_user->email."."]);
        }catch(\Exception $ex) {
            return $ex->getMessage();
            return response()->json(["message" => "Unable to proceed your request, Please try later."],400);
        }
        // return response()->json(["message" => "A reset password link has been sent to your registered email address."]);
    }



    public function psychologistVerifyOtp(Request $request){
        
        $message = [
            'email.required'              =>  'Please enter email.',
            'otp.required'              =>  'Please enter otp.',
        ];
        $validator = Validator::make($request->all(), [
            'email'           => 'required',
            'otp'           => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }


        $email = $request->email;

        $psy_details = Psychologist::where('email' , $email)->first();

        $otp_details = VerifyPsychologist::where('psychologist_id' , $psy_details->id)->first();
        $entered_otp = $request->otp;

        if($entered_otp == $otp_details->email_otp){
            $otp_details->email_verify = 1;
            $otp_details->save();
            return response()->json(['status'=> 'success' ,"message" => "OTP verify."]);
        }else{
            return response()->json(['status'=> 'error' ,"message" => "Invalid OTP."]);
        }

    }



    public function psySetPassword(Request $request){

        $message = [
            'email.required'              =>  'Please enter email.',
            'password.required'              =>  'Please enter password.',
        ];
        $validator = Validator::make($request->all(), [
            'email'           => 'required',
            'password'           => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $psy_detail = Psychologist::where('email' , $request->email)->first();
        $hash_password = Hash::make($request->password);

        $psy_detail->password = $hash_password;
        $psy_detail->save();
        
        return response()->json(['status'=> 'success' ,"message" => "Password has been reset successfully."]);



    }



    public function getProfile(Request $request){
        $psychologist_id = Auth::guard('psychologist')->user()->id;
        $user_details = Psychologist::where('id' , $psychologist_id)->with(['specialization','language','availability','expertLevel'])->first();
        return response()->json(['status'=> 'success' ,"message" => "User detials get successfully." , 'data' => $user_details]);
    }

    public function editProfile(Request $request){
        $user = Auth::guard('psychologist')->user();

        $message = [
            'age.required'              =>  'Please enter age.',
            'gender.required'           =>  'Please select gender.',
            'highest_qualification.required'        =>  'Please enter Highest Qualification.',
        ];
        $validator = Validator::make($request->all(), [
            'age'           => 'required',
            'gender'        => 'required',
            'highest_qualification'     => 'required|unique:users,username,'.$user->id,
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $data = [
            'age' => $request->age,
            'gender' => $request->gender,
            'highest_qualification' => $request->highest_qualification,
            'meet_link' => $request->meet_link,
        ];

        if($request->internation_cert){
                $data['internation_cert'] = $request->internation_cert;
        }
        // if($request->email != ''){
        //     $check_email_already_exist = User::where('email' , $request->email)->first();
        //     if($check_email_already_exist){
        //         return response()->json(['status' => 'error' , 'message' => 'Email already taken']);
        //     }else{
        //         $data['email'] = $request->email;
        //     }
        // }
        //
        // if($request->mobile != ''){
        //     $check_email_already_exist = User::where('mobile' , $request->mobile)->first();
        //     if($check_email_already_exist){
        //         return response()->json(['status' => 'error' , 'message' => 'Mobbile number already taken']);
        //     }else{
        //         $data['mobile'] = $request->mobile;
        //     }
        // }

        // $data['avatar'] = ($data['gender'] != 'other') ? $data['gender'] . '1.svg' : 'female1.svg';

        $is_updated = Psychologist::where('id' , $user->id)->update($data);
        if($is_updated){
            return response()->json(['status' => 'success' , 'message' => "Profile has been updated sucessfully. " ,'data' => $data]);
        }else{
            return response()->json(['status' => 'error' , 'message' => "Unable to update profile, try after sometime"], 400);
        }

    }

    public function changePassword(Request $request) {
        $user = Auth::guard('psychologist')->user();

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
            Psychologist::where($where)->update($data);
            return response()->json(['status'=> 'success' ,"message" => "Password has been changed successfully."]);
        } else {
            return response()->json(['status'=> 'error' ,"message" => "Please enter valid old password."],400);
        }
    }



    public function assignEmailPassToExistingPsy(Request $request){
        $psychologist = Psychologist::where('email' , null)->get();
        $password = 'happimynd@12345';
        $hash_password = Hash::make($password);
        foreach($psychologist as $row){
            $random_number = rand('111','999');
            $username = $row->first_name.$random_number;
            $email = $row->first_name.$random_number.'@yopmail.com';
            $data = [
                'username' => $username,
                'email' => $email,
                'password' => $hash_password,
            ];
            Psychologist::where('id' , $row->id)->update($data);
        }
        return response()->json(['status' => 'success' , 'message' => 'Email and password assign to existing psychologists' , 'password' => $password]);

    }





}








