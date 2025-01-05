<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\OtpEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class OTPService
{
    const MOBILE_OTP_TEMPLATE = "Greeting from HappiMynd,
The OTP for your mobile verification is <OTP>";

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function generateOtp()
    {
        srand(time());
        $otp = rand(100000, 999999);
        return $otp;
    }

    public function sendMobileOtp()
    {
        if ($this->user && is_null($this->user->mobile)) {
            return false;
        }
        
        $otp = $this->generateOtp();

        // logger($this->user->country->code);

        if (is_null($this->user->country) || (string) $this->user->country->code == '0091') {
            $response = Http::get('https://enterprise.smsgupshup.com/GatewayAPI/rest', [
                'method' => 'SendMessage',
                'send_to' => $this->user->mobile,
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
                'send_to' => $this->user->country->code . $this->user->mobile,
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

        $responseDestructured = explode("|", $response->body());
        logger($responseDestructured);
        
        $this->user->verifyUser()->updateOrCreate(
            ['user_id' => $this->user->id],
            [
                'mobile_otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(60)
            ]
        );

        if (!str_contains($responseDestructured[0], "success")) {
            \Log::critical("Unable to send Mobile OTP to user:" . json_encode($this->user) . ' Response: ' . json_encode($response->body()));
        }

        return true;
    }

    public function sendMailOtp()
    {
        if ($this->user && is_null($this->user->email)) {
            return false;
        }
        $otp = $this->generateOtp();
        $mailDetails = [
            'username' => $this->user->username,
            'email' => $this->user->email,
            'nickname' => $this->user->nickname,
            'otp' => $otp,
        ];
        // using mailable class to send markdown email
        Mail::to($this->user->email)->send(new OtpEmail($mailDetails));
        $this->user->verifyUser()->updateOrCreate(
            ['user_id' => $this->user->id],
            [
                'email_otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(60)
            ],
        );
        return true;
    }

    public function verifyMobileOtp($otp)
    {
        if ($this->user->verifyUser && $this->user->verifyUser->mobile_otp == $otp) {
            if ($this->user->verifyUser->expires_at >= Carbon::now()) {
                $this->user->verifyUser->mobile_verify = 1;
                $this->user->verifyUser->save();
                return true;
            }
        }
        return false;
    }

    public function verifyMailOtp($otp)
    {
        if ($this->user->verifyUser && $this->user->verifyUser->email_otp == $otp) {
            if ($this->user->verifyUser->expires_at >= Carbon::now()) {
                $this->user->verifyUser->email_verify = 1;
                $this->user->verifyUser->save();
                return true;
            }
        }
        return false;
    }

    public static function sendAnonymousMobileOtp($otp, $mobileNo)
    {
        try {
            $response = Http::get('https://enterprise.smsgupshup.com/GatewayAPI/rest', [
                'method' => 'SendMessage',
                'send_to' => $mobileNo,
                'msg' => str_replace('<OTP>', $otp, self::MOBILE_OTP_TEMPLATE),
                'msg_type' => 'TEXT',
                'userid' => env('GUPSUP_USER_ID'),
                'auth_scheme' => 'plain',
                'password' => env('GUPSUP_PASSWORD'),
                'v' => 1.1,
                'format' => 'text'
            ]);

            if (explode("|", $response->body())[0] == "success ") {
                return true;
            }
            return false;
        } catch (Exception $e) {
            Log::critical($e->getMessage());
            Log::critical("SMS Not Sent for " . $mobileNo);
        }
    }
}
