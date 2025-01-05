<?php

namespace App\Models;

use App\Jobs\GenerateScreeningReport;
use App\Services\BitrixService;
use App\Services\OTPService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Http;
use Mail;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'login_token',
        'mobile',
        'nickname',
        'username',
        'age',
        'profession',
        'gender',
        'avatar',
        'lead_id',
        'deal_id',
        'user_profile_id',
        'country_id',
        'happimynd_code',
        'platform',
        'language',
        'device_token',
        'refer_code',
        'from_refer_code',
        'last_reward_noti_emit_number',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Country associated with the user.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getDefaultAvatarAttribute()
    {
        return ($this->avatar) ? $this->avatar : config('constants.mediaAssets.userDefaultProfilePicture');
    }

    public function assessment()
    {
        return $this->hasMany(Assessment::class);
    }

    public function getProfessionAttribute()
    {
        if (gettype($this->profileType) != "object") {
            $userProfile = auth()->user()->profileType->name;
            return $userProfile->name;
        }
        if ($this->profileType == "object") {
            return $this->profileType->name;
        }
        return "";
    }

    public function isAdmin()
    {
        return false;
    }

    public function hasAccessToAdminPanel()
    {
        return false;
    }

    public function isActive()
    {
        return $this->account_status == 'active';
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function verifyUser()
    {
        return $this->hasOne(VerifyUser::class);
    }

    public function userToken()
    {
        return $this->hasOne(UserToken::class)->with('token');
    }


    public function hasPendingAssessment()
    {
        $pendingAssessment = $this->assessment()->whereNull('ended_at')->get();
        return (count($pendingAssessment) > 0) ? true : false;
    }

    public function bundleStatus()
    {
        return $this->hasMany(BundleStatus::class);
    }

    public function isEmailVerified()
    {
        if ($this->verifyUser && $this->verifyUser->email_verify == 1) {
            return true;
        }
        return false;
    }

    public function isMobileVerified()
    {
        if ($this->verifyUser && $this->verifyUser->mobile_verify == 1) {
            return true;
        }
        return false;
    }

    public function generateReportAndSendMail($sendMail = true, $skipChecks = false)
    {

        $user_id =  $this->id;
        $assessment_id = Assessment::where('user_id' , $user_id)->first();

        $assessment = Assessment::where('id' , $assessment_id->id)->first();
        $user = User::where('id' , $user_id)->first();

        // echo 'checking  ==='. $sendMail; die();
        \Log::debug('User.php generateReportAndSendMail() start');
        //generate pdf if GENERATE_PDF set to true in env
        if (is_null(env('GENERATE_PDF')) || env('GENERATE_PDF') == false) {
            \Log::debug('User.php generateReportAndSendMail() exited due to env not set');
            return 0;
        }

        if (!$skipChecks && !is_null($this->bundleStatus)) {
            $generateReportPDF = false;
            foreach ($this->bundleStatus as $subscribedBundle) {
                if (
                    ($subscribedBundle->plans->package_id == 1 || $subscribedBundle->plans->package_id == 2) &&
                    ($subscribedBundle->valid) &&
                    ($subscribedBundle->percentage_covered == 100.00)
                ) {
                    $generateReportPDF = true;
                }
            }
            if ($generateReportPDF) {
                \Log::debug("before dispatch " . Carbon::now()->format("g:i a"));
                // dispatch(new GenerateScreeningReport($this->id))->delay(1);
                // \Log::debug("after dispatch " . Carbon::now()->format('g:i a'));

                //Code from GenerateScreening Report file
                try {
                    if (is_null($assessment->report)) {

                        $response = Http::get(env('NODE_URL') . '/check');
                        if ($response->ok()) {
                            $response = Http::get(env('NODE_URL') . '/pdf?reportUrl=' . env('APP_URL') . '/calculate-score?assessment_id=' . $assessment->id . '&fileName=' . $assessment->id . '_' . $user->nickname.'testing' . '-ScreeningReport.pdf');
                            $res = $response->json();

                            // print_r($res['link']);
                            \Log::info('response body:' . json_encode($res));
                            // $assessment->report = $res['link'];
                            $assessment->update(['report' => $res['link']]);
                        } else {
                            \Log::critical('respone not ok');
                            \Log::critical($response);
                        }
                    }

                    // return [$sendMail , $user , $assessment->report , $user->isEmailVerified()];
                    if ($sendMail && $user && $assessment->report && $user->isEmailVerified()) {


                        Mail::send('mail/report', [], function ($message) use ($assessment, $user) {
                            $message->to($user->email)->subject('Happimynd Screening Report');
                            $message->attach($assessment->report, [
                                'as' => 'ScreeningReport.pdf'
                            ]);
                            $message->from(env('MAIL_FROM_ADDRESS'));

                        });

                            return 33;

                        \Log::debug('Report Mail sent to user:' . json_encode($user));
                    } else {
                        \Log::info('Report mail not sent to user: ' . $user->id . ': email not verified' . json_encode($user));
                    }
                } catch (Exception $e) {
                    \Log::critical('Node server is down at ' . now());
                    \Log::critical($e);
                }

            }
        } else {
            \Log::debug('No bundles for userId:' . $this->id);
            if ($skipChecks) {
                \Log::debug("generating report invoked from admin panel");
                \Log::debug("before dispatch " . Carbon::now()->format("g:i a"));
                // dispatch(new GenerateScreeningReport($this->id, $sendMail))->delay(1);
                // \Log::debug("after dispatch " . Carbon::now()->format('g:i a'));

                //Code from GenerateScreening Report file
                try {
                    if (is_null($assessment->report)) {

                        $response = Http::get(env('NODE_URL') . '/check');
                        if ($response->ok()) {
                            $response = Http::get(env('NODE_URL') . '/pdf?reportUrl=' . env('APP_URL') . '/calculate-score?assessment_id=' . $assessment->id . '&fileName=' . $assessment->id . '_' . $user->nickname . '-ScreeningReport.pdf');
                            $res = $response->json();

                            // print_r($res['link']);
                            \Log::info('response body:' . json_encode($res));
                            // $assessment->report = $res['link'];
                            $assessment->update(['report' => $res['link']]);
                        } else {
                            \Log::critical('respone not ok');
                            \Log::critical($response);
                        }
                    }

                    // return [$sendMail , $user , $assessment->report , $user->isEmailVerified()];
                    if ($sendMail && $user && $assessment->report && $user->isEmailVerified()) {


                        Mail::send('mail/report', [], function ($message) use ($assessment, $user) {
                            $message->to($user->email)->subject('Happimynd Screening Report');
                            $message->attach($assessment->report, [
                                'as' => 'ScreeningReport.pdf'
                            ]);
                            $message->from(env('MAIL_FROM_ADDRESS'));

                        });

                        \Log::debug('Report Mail sent to user:' . json_encode($user));
                    } else {
                        \Log::info('Report mail not sent to user: ' . $user->id . ': email not verified' . json_encode($user));
                    }
                } catch (Exception $e) {
                    \Log::critical('Node server is down at ' . now());
                    \Log::critical($e);
                }


            }
        }
        \Log::debug('User.php generateReportAndSendMail() end for user:' . $this->id);
        return 1;
    }

    /**
     * Checks if user account is organization (B2B) profile type
     *
     * @return boolean
     */
    public function isOrganizationUser(): bool
    {
        //TODO: verify if B2B profile is considered if only signup using token or preferred profile type
        return (bool)($this->userToken);
    }

    public function profileType()
    {
        return $this->belongsTo(UserProfile::class, 'user_profile_id');
    }

    public function couponReceipt()
    {
        return $this->hasMany(CouponReceipt::class);
    }

    public function getUsedCouponCodes()
    {
        $coupon_receipts = $this->couponReceipt;
        $coupon_codes = [];
        foreach($coupon_receipts as $coupon_receipt){
            if($coupon_receipt->receipt){
                if($coupon_receipt->receipt->status == 1){
                    $coupon_codes[] = $coupon_receipt->coupon->code;
                }
            }else{
                $coupon_codes[] = $coupon_receipt->coupon->code;
            }
        }
        $coupon_str = implode(" | ",$coupon_codes);
        return $coupon_str?$coupon_str:null;
    }

    
    public function showReport()
    {
        $bundleStatuses = $this->bundleStatus()->with('plans.package')->latest()->where('valid', 1)->where('percentage_covered', 100.00)->get();
        foreach ($bundleStatuses as $bundleStatus) {
            if ($bundleStatus->plans->package->name == "HappiLIFE Screening") {
                return true;
            }
        }
        return false;
    }

    public function sendMobileOtp()
    {
        return (new OTPService($this))->sendMobileOtp();
    }

    public function sendMailOtp()
    {
        return (new OTPService($this))->sendMailOtp();
    }

    public function verifyMobileOtp($otp)
    {
        return (new OTPService($this))->verifyMobileOtp($otp);
    }

    public function verifyMailOtp($otp)
    {
        return (new OTPService($this))->verifyMailOtp($otp);
    }

    public function scopeRemoveTestUser($query)
    {
        return
            $query->where('username', 'not like', '%test%')
            ->where('nickname', 'not like', '%test%')
            ->where('nickname', 'not like', '%happimynd%')
            ->where('username', 'not like', '%happimynd%');
    }

    public function copyBitrixDealToPipeline($pipeline)
    {
        if (!config('constants.bitrix')) {
            return;
        }
        \Log::info('in user copybitrix method with pipeline' . json_encode($pipeline));
        \Log::info(json_encode($this->load(['bundleStatus', 'verifyUser'])));
        if (!$this->isEmailVerified()) {
            \Log::debug("did't copied deal to pipeline:: email not verified");
            return false;
        }
        $userData = $this->toArray();
        $organizationName = "";
        if ($this->isOrganizationUser()) {
            $organizationName = $this->userToken->token->organization->name;
            $userData['dealType'] = "B2B";
        } else {
            $userData['dealType'] = "B2C";
        }
        $userData['dealCategory'] = $pipeline;
        $bitrixResponse = (new BitrixService)->addDeal($userData, "", $organizationName);
        $newDealId  = $bitrixResponse->result;
        \Log::info("in observer." . json_encode($bitrixResponse));
        $updateContactResponse = (new BitrixService)->addOrUpdateContactForDeal($newDealId, $userData);
        $products = [];
        $happiCHAT = "0";
        $happiTALK = "0";
        $happiTALKSessions = "";
        foreach ($this->bundleStatus as $bundleStatus) {
            $price = 0;
            if ($bundleStatus->receipt) {
                $price = $bundleStatus->receipt->amount;
            }
            array_push($products, [
                'package_id' => $bundleStatus->plans->package_id,
                "price" => $price,
                "quantity" => 1,
            ]);
            if ($bundleStatus->plans->package->name == "HappiTALK") {
                $happiTALK = "1";
                $happiTALKSessions = $bundleStatus->plans->duration->frequency ?? '';
            }
            if ($bundleStatus->plans->package->name == "HappiCHAT") {
                $happiCHAT = "1";
            }
        }
        (new BitrixService)->addProductDeal($newDealId, $products);
        if ((isset($updateContactResponse->result)) && $updateContactResponse->result) {
            $bitrixResponse = (new BitrixService)->updateDeal(
                $newDealId,
                $userData,
                array('contactId' => $updateContactResponse->result, "HappiTALK" => $happiTALK, "HappiCHAT" => $happiCHAT, "HappiTALKSessions" => $happiTALKSessions),
            );
        }
    }

    public function addProductDealToBitrix($bundleStatuses, $deal_id = "")
    {
        if (!config('constants.bitrix')) {
            return;
        }
        if ($deal_id == "") {
            $deal_id = $this->deal_id;
        }

        (new BitrixService)->addProductDeal($deal_id, $bundleStatuses);
    }

    public function updateBitrixDeal($data, $deal_id = "")
    {
        if (!config('constants.bitrix')) {
            return;
        }
        if ($deal_id == "") {
            $deal_id = $this->deal_id;
        }
        \Log::debug('updating bitrix' . json_encode($data));
        (new BitrixService)->updateDeal($deal_id, $this, $data);
    }

    public function psychologistAppointment()
    {
        return $this->hasOne(PsychologistAppointment::class);
    }

    public function addReportReadingToBitrixOld()
    {
        $assessment = $this->assessment()->first();
        if ($assessment) {
            $assessmentApprove = $assessment->approve;
            $timeSlot = $assessmentApprove->slot;
            $preferredDateTime  = Carbon::parse($assessmentApprove->available_date)->toDateTimeString();
            $data['reportReadingSlotDate'] = $preferredDateTime;
            $data['reportReadingTimeSlot'] = $timeSlot;
            $data['detailLink'] = route('downloadAssessmentDetail', [base64_encode('assessment_id') => base64_encode($assessment->id)]);
            $data['reportReadingCommunicationMode'] = $assessmentApprove->callOption();
            $data['dealCategory'] = "ReportReading";
            $addDealBitrixResponse = (new BitrixService)->addDeal($data, $this->lead_id);
            if ($addDealBitrixResponse->result) {
                $deal_id = $addDealBitrixResponse->result;
                if ($deal_id) {
                    /** Update the contact for the deal(B2C) in the bitrix */
                    $updateContactResponse = (new BitrixService)->addOrUpdateContactForDeal(
                        $deal_id,
                        $this
                    );
                    if ((isset($updateContactResponse->result)) && $updateContactResponse->result) {
                        $bitrixResponse = (new BitrixService)->updateDeal(
                            $deal_id,
                            $this,
                            array('contactId' => $updateContactResponse->result)
                        );
                    }
                }
            }
        }
    }

    public function addReportReadingToBitrix()
    {
        $assessment = $this->assessment()->first();
        if ($assessment) {
            $assessmentApprove = $assessment->approve;
            $timeSlot = $assessmentApprove->slot;
            $preferredDateTime  = Carbon::parse($assessmentApprove->available_date)->toDateTimeString();
            $data['reportReadingSlotDate'] = $preferredDateTime;
            $data['reportReadingTimeSlot'] = $timeSlot;
            $data['detailLink'] = route('downloadAssessmentDetail', [base64_encode('assessment_id') => base64_encode($assessment->id)]);
            $data['reportReadingCommunicationMode'] = $assessmentApprove->callOption();
            $data['dealCategory'] = "ReportReading";
            if (config('constants.bitrix')) {
            $addDealBitrixResponse = (new BitrixService)->addDeal($data, $this->lead_id);
                if ($addDealBitrixResponse->result) {
                    $deal_id = $addDealBitrixResponse->result;
                    if ($deal_id) {
                        /** Update the contact for the deal(B2C) in the bitrix */
                        $updateContactResponse = (new BitrixService)->addOrUpdateContactForDeal(
                            $deal_id,
                            $this
                        );
                        if ((isset($updateContactResponse->result)) && $updateContactResponse->result) {
                            $bitrixResponse = (new BitrixService)->updateDeal(
                                $deal_id,
                                $this,
                                array('contactId' => $updateContactResponse->result)
                            );
                        }
                    }
                }
            }
        }
    }

    public function hasOrganizationPlans()
    {
        if (!$this->isOrganizationUser()) {
            return false;
        }

        return $this->userToken->token->tokenPlans->count() > 0;
    }

    public function hasSubscribedPlans()
    {
        return $this->getSubscribedPlans()->count() > 0;
    }

    public function getSubscribedPlans()
    {
        $this->load(['bundleStatus' => function ($query) {
            $query->ActivePlan()->with('plans.package');
        }]);
        return $this->bundleStatus->pluck('plans');
    }

    public function getOrganizationPlans()
    {
        $this->userToken->token->load(['tokenPlans' => function ($query) {
            $query->with('bundleStatus', 'plan.package', 'plan.duration');
        }]);

        return $this->userToken->token->tokenPlans->pluck('plan');
    }

    /**
     * checks if given plan belongs to organization plan(token plan)
     *
     * @param [Plan] $plan
     * @return bool
     */
    public function organizationHasPlan($plan)
    {
        if (!$this->isOrganizationUser()) {
            return false;
        }
        $this->userToken->token->load(['tokenPlans' => function ($query) {
            $query->with('bundleStatus', 'plan.package');
        }]);
        foreach ($this->userToken->token->tokenPlans->pluck('plan') as $organizationalPlan) {
            if ($plan->package->name == $organizationalPlan->package->name) {
                return true;
            }
        }
        return false;
    }

    public function isSubscribedTo($plan)
    {
        $this->load('bundleStatus.plans.package');
        foreach ($this->bundleStatus as $bundleStatus) {
            if ($bundleStatus->plans->package->name == $plan->package->name) {
                return true;
            }
        }
        return false;
    }

    public function hasHappiTalkPlan()
    {
        $package = Package::where('name', 'HappiTALK')->with('plan')->first();
        return $this->isSubscribedTo($package->plan->first());
    }

    public function hasSummaryReadingPlan()
    {
        $package = Package::where('name', 'HappiLIFE Summary Reading')->with('plan')->first();
        return $this->isSubscribedTo($package->plan->first());
    }

    public function hasHappiAPPPlan()
    {
        $package = Package::where('name', 'HappiAPP')->with('plan')->first();
        return $this->isSubscribedTo($package->plan->first());
    }

    public function organizationHasHappiTalkPlan()
    {
        $package = Package::where('name', 'HappiTALK')->with('plan')->first();
        return $this->organizationHasPlan($package->plan->first());
    }

    public function organizationHasSummaryReadingPlan()
    {
        $package = Package::where('name', 'HappiLIFE Summary Reading')->with('plan')->first();
        return $this->organizationHasPlan($package->plan->first());
    }

    public function getOrganizationHappiTalkSessions()
    {
        if ($this->isOrganizationUser() && $this->organizationHasHappiTalkPlan()) {
            return $this->userToken->token->tokenMetaData->meta_data['HappiTALK2'] ?? 0;
        }
        return 0;
    }

    public function usersRating(){
        return $this->hasOne(UsersRating::class)->with('applicationRatingEmoji');
    }

}
