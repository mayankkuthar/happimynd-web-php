<?php

namespace App\Http\Controllers;
use Twilio\Rest\Client;


use Mail;
use Session;
use Carbon\Carbon;
use App\Models\Post;
use App\Models\ServiceImage;
use App\Models\EditButton;
use App\Models\User;
use App\Models\Token;
use App\Models\OurClient;
use App\Models\Quotes;
use App\Mail\OtpEmail;
use App\Models\Guardian;
use App\Models\DataGroup;
use App\Models\Assessment;
use App\Models\RaiseQuery;
use App\Models\ThriveCode;
use App\Models\VerifyUser;
use App\Models\ServiceType;
use App\Models\UserProfile;
use Illuminate\Support\Arr;
use App\Models\Availability;
use App\Models\BundleStatus;
use App\Models\StaticSection;
use App\Models\Organization;
use App\Models\OtherService;
use App\Models\PostCategory;
use App\Services\OTPService;
use Illuminate\Http\Request;
use App\Models\AvailableDate;
use App\Models\TokenMetaData;
use App\Models\VerifyGuardian;
use App\Services\TokenService;
use App\Services\BitrixService;
use App\Mail\QueryRaisedToAdmin;
use App\Models\ServiceTypeGroup;
use App\Services\PackageService;
use App\Services\PaymentService;
use App\Models\AssessmentApprove;
use App\Models\AvailabilityDates;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Models\EducationServiceType;
use App\Services\ApiResponseService;
use App\Services\AppointmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\UserNameRequest;
use App\Models\OtherServiceSubscriber;
use App\Models\Coupon;
use App\Models\CouponReceipt;
use Illuminate\Support\Facades\Cookie;
use App\Http\Requests\VerifyOtpRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\RaiseQueryRequest;
use App\Http\Requests\UserSignInRequest;
use App\Http\Requests\UserSignupRequest;
use App\Http\Requests\SubscribersRequest;
use App\Http\Requests\TokenVerifyRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Http\Requests\UpdateMobileRequest;
use App\Models\EducationServiceSubscriber;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\EditUserProfileRequest;
use App\Models\Country;
use Exception;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

use App\Models\DataContent;

use App\Services\FileService;

class UserController extends Controller
{
    public $apiService;


    public function __construct(ApiResponseService $apiService, BitrixService $bitrixService, ApiResponseService $apiResponseService)
    {
        $this->apiService = $apiService;
        $this->bitrix = $bitrixService;
        $this->apiResponseService = $apiResponseService;
    }

    /**
     * this method is for authentication system to specify login using username instead of password
     *
     * @return void
     */
    public function setUsername()
    {
        $this->username = 'username';
    }

    /**
     * check if username ealready exists
     *
     * @param UserNameRequest $request
     * @return void
     */
    public function verifyUserName(UserNameRequest $request)
    {
        return false;
    }

    /**
     * if user signsup using happimyndCode verify that code is valid
     *
     * @param TokenVerifyRequest $request
     * @return void
     */
    public function verifyToken(TokenVerifyRequest $request)
    {
        return $this->apiService->success(true);
    }

    /**
     * user signup
     *
     * @param UserSignupRequest $request
     * @return json redirect link if signup successful
     */
    public function signup(UserSignupRequest $request)
    {
        try {
            DB::beginTransaction();
            $formData = $request->validated();
            if (config('constants.bitrix')) {
                /** If user signup using happimyndCode.! B2B*/
                if ($request->input('happimyndCode')) {
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
            $formData['avatar'] = ($formData['gender'] != 'other') ? $formData['gender'] . '1.svg' : 'female1.svg';
            if ($formData['signup_type'] == 'individual') {
                $user = User::create(Arr::except($formData, ['profession']));
            } else {
                $username = $request->input('username');
                $user = User::updateOrCreate(
                    [
                        'username' => $username
                    ],
                    Arr::except($formData, ['profession'])
                );
            }
            if (isset($formData['signup_type']) && $formData['signup_type'] == 'organization' && !(new TokenService)->assignToken($request->input('happimyndCode'), $user->id)) {
                Log::alert('error expiring Token for user_id=' . $user->id . ' with code=' . $request->input('happimyndCode') . ';');
            }
            $token = auth('user')->login($user);
            $cookie = Cookie::make('user-access-token', $token);
            Cookie::queue($cookie);
            DB::commit();
            return $this->apiService->success(['route' => route('user.assessment')]);
        } catch (Exception $e) {
            throw $e;
            DB::rollBack();
        }
    }

    /**
     * user signin validation
     *
     * @param UserSignInRequest $request
     * @return void
     */
    public function signin(UserSignInRequest $request)
    {
        $formData = $request->validated();
        $this->setUsername('username');
        if (!$token = auth('user')->attempt(['username' => $formData['username'], 'password' => $formData['password']])) {
            return response()->json(['errors' => ['password' => 'invalid Password']], 401);
        }
        // return response('true')->withCookie('access-token', $token);
        $cookie = Cookie::make('user-access-token', $token);
        Cookie::queue($cookie);
        return $this->apiService->success(['route' => route('user.dashboard')]);
        // return $this->apiService->success(['route' => url('subscribedservices')]);
        
    }

    public function landingPageView(Request $request)
    {
        $allcontents = DataGroup::with('content', 'carouselSection')->where('name', 'landing_page')->first();
        $dataContents = $allcontents->content;
        $generalFaqs = DataGroup::with('content')->where('name', 'faqs-general')->first();
        $quotes=Quotes::all();
        $landing_buttons = EditButton::where('page_name', 'landing')->get();
        $button_contents=EditButton::where('page_name','quotes')->first();
        $introVideoLink = '';
        $introVideoThumbnail = '';
        $ht = [];
        $data_section =StaticSection::whereHas('dataGroup', function($query) {
            $query->where('name', 'landing_page');
        })->get();

        $sections = [];
        foreach($data_section as $dt){
            if(!array_key_exists($dt->section, $sections)) {
                $sections[$dt->section] = $dt->dataContent;
            } else {
                if(is_array($sections[$dt->section])) {
                    $sections[$dt->section][] = $dt->dataContent;
                } else {
                    $ar = $sections[$dt->section];
                    unset($sections[$dt->section]);
                    $sections[$dt->section][] = $ar;
                    $sections[$dt->section][] = $dt->dataContent;
                }
            }
        }
        foreach ($dataContents as $content) {
            if ($content->title == 'landing_page_video') {
                $introVideoLink = $content->getContentWithS3Url('landing_page');
            }
            if ($content->title == 'landing_page_video_thumbnail') {
                $introVideoThumbnail = $content->getContentWithS3Url('landing_page');
            }
        }
        $carousel = $allcontents->carouselSection;
        $clients = OurClient::orderBy('preference')->get();

        $androidLink = DataContent::where('title' , 'android_hyperlink')->first();
        if($androidLink){
            Session::put('play_store_link' , $androidLink->content);
        }else{
            Session::put('play_store_link' , 'javascript:void(0)');
        }

        $iosLink = DataContent::where('title' , 'ios_hyperlink')->first();
        if($iosLink){
            Session::put('app_store_link' , $iosLink->content);
        }else{
            Session::put('app_store_link' , 'javascript:void(0)');
        }

        return view('Frontend/landing/landingpage')->with('introVideoLink', $introVideoLink)->with('introVideoThumbnail', $introVideoThumbnail)->with('generalFaqs', $generalFaqs)->with('quotes',$quotes[0])->with('clients', $clients)->with('sections', $sections)->with('carousel', $carousel)->with('button_contents',$button_contents)->with('landing_buttons', $landing_buttons);
    }

    public function getServiceButtonData()
    {
        $data=ServiceImage::all();
        return response()->json([
            "success" => 1,
            "error" => 0,
            "data" => $data
        ]);

    }

    public function sponserSignupView(Request $request)
    {
        $organizations = Organization::orderBy('name' , 'asc')->AvaliableOrganization()->get();
        $userProfiles = UserProfile::orderBy('status', 'DESC')->get();
        return view('Frontend/signup/signup1')->with('organizations', $organizations)->with('userProfiles', $userProfiles);
    }

    public function individualSignupView(Request $request)
    {
        $userProfiles = UserProfile::orderBy('status', 'DESC')->get();
        return view('Frontend/signup/signup2')->with('userProfiles', $userProfiles);
    }

    public function getPrivacy(Request $request)
    {
        $dataContent = DataGroup::where('name', 'terms-and-services')->with('content')->first();
        return view('Frontend/terms/privacy')->with('dataContent', $dataContent);
    }

    public function dashboard(Request $request)
    {
        $dataContents = DataGroup::with('content')->where('name', 'dashboard')->first()->content()->get();
        $dashboardPic = '';
        $hyperlink = '';
        foreach ($dataContents as $content) {
            if ($content->title == 'dashboard_cover_pic') {
                $dashboardPic = $content->getContentWithS3Url('dashboard');
            } elseif ($content->title == 'hyperlink') {
                $hyperlink = $content->content;
            }
        }
        $user = auth('user')->user();
        $assessment = $user->assessment()->with('approve')->orderBy('started_at', 'desc')->get();
        $slotBooked = json_encode(AppointmentService::getBookedAppointmentDates());
        $assessment_id = (count($assessment) > 0) ? $assessment[0]->id : 0;  // fetch latest assessment id.
        $assessment_complete_status = (count($assessment) > 0 && $assessment[0]->ended_at == NULL) ? false : true;  // Assessment complete status
        $appointment_status = (count($assessment) > 0 &&  $assessment[0]->approve && $assessment[0]->approve->slot != "") ? true : false;

        $booked_dates = AssessmentApprove::select('available_date')->whereNotNull('available_date')->get();

        $disableDates = Availability::select('date')->get();

        $bundleStatus = BundleStatus::where('user_id', auth('user')->user()->id)->where('valid', true)->orderBy('plan_id', 'DESC')->first(); //TODO : remove if another packages are activated // Hardcoded...
        $plan_id = ($bundleStatus) ? $bundleStatus->plan_id : 0;

        //check if user bought summary reading plan or not
        //TODO: check logic if multiple assessments are enabled
        $summaryReadingPlanStatus = (BundleStatus::where('user_id', auth('user')->user()->id)
            ->where('plan_id', 2)->first()) ? false : true;
        $happiAPPPlanStatus = (BundleStatus::where('user_id', auth('user')->user()->id)
            ->where('plan_id', 5)->first()) ? false : true;
        $showBlinkingText = false;
        $blinkingText = "";
        if (!$assessment_complete_status) {
            $showBlinkingText = true;
            $blinkingText = "screening";
        } else if ($summaryReadingPlanStatus) {
            $showBlinkingText = true;
            $blinkingText = "summary_reading";
        } else if ($happiAPPPlanStatus) {
            $showBlinkingText = true;
            $blinkingText = "happiapp";
        }
        $confirmation_assesment = ($plan_id != 0) ? "1" : "0"; // Update the confirmation_assesment if pagekage id !=0
        if ($confirmation_assesment == "1") {
            if (\Session::get('_previous')) {
                $previous_url = \Session::get('_previous')['url'];
                if ($previous_url == route('payment.responseBundle') || $previous_url == route('user.assessment') || $previous_url == route('user.reportPreview') || $previous_url == route('payment.orderBundle') || $previous_url == route('user.payment.buyBundle') || Cookie::get('orderId') != null) {
                    $confirmation_assesment = "1";
                } else {
                    $confirmation_assesment = "0";
                }
            } else {
                $confirmation_assesment = "0";
            }
        }


        // $user_deep_details =  User::where('id',$user->id)->with('userToken','verifyUser')->first();
        // if($user_deep_details->userToken != ''){
        //     $org_id_of_user =  $user_deep_details->userToken->token->organization_id;
        //     if($org_id_of_user == '102'){
        //         if($user_deep_details->verifyUser == ''){
        //             Session::put('is_block_verification_popup' , 1);
        //         }   
        //         else if($user_deep_details->verifyUser->mobile_verify == 0 || $user_deep_details->verifyUser->email_verify == 0){
        //             Session::put('is_block_verification_popup' , 1);
        //         }else{
        //             Session::put('is_block_verification_popup' , 0);
        //         }
        //     }
        //     else{
        //         Session::put('is_block_verification_popup' , 0);
        //     }
        // }else{
        //     Session::put('is_block_verification_popup' , 0);
        // }

        $is_block_verification_popup = Session::get('is_block_verification_popup');

        return view('Frontend/dashboard/dashboard')
            ->with('dashboardPic', $dashboardPic)
            ->with('hyperlink', $hyperlink)
            ->with('user', $user)
            ->with('assessment_id', $assessment_id)
            ->with('plan_id', $plan_id)
            ->with('appointment_status', $appointment_status)
            ->with('confirmation_assesment', $confirmation_assesment)
            ->with('slotBooked', $slotBooked)
            ->with('booked_dates', $booked_dates)
            ->with('disableDates', $disableDates)
            ->with('showBlinkingText', $showBlinkingText)
            ->with('blinkingText', $blinkingText)
            ->with('assessment_complete_status', $assessment_complete_status);
    }

    public function downloadReport(Request $request)
    {

        $slotBooked = json_encode(AppointmentService::getBookedAppointmentDates());
        $user = User::where('id', auth('user')->user()->id)
            ->with('userToken')
            ->with('assessment')
            ->with('assessment.approve')
            ->with('verifyUser')
            ->first();
        $assessment_id = (count($user->assessment) > 0) ? $user->assessment[0]->id : 0;  // fetch latest assessment id.
        $bundleStatus = $user->bundleStatus()->where('valid', true)->orderBy('plan_id', 'DESC')->first(); // Hardcoded...
        $plan_id = ($bundleStatus) ? $bundleStatus->plan_id : 0;

        return view('Frontend/profilesetting/downloadreport')->with('user', $user)->with('plan_id', $plan_id)->with('slotBooked', $slotBooked)->with('assessment_id', $assessment_id);
    }


    public function thriveCode(Request $request)
    {
        $code = $request->input('code');
        $user_id = auth('user')->user()->id;
        $happiAPPLimit = 0;
        $user = User::with('userToken')->where('id', $user_id)->first();
        if ($user->userToken) {
            $thriveCode = ThriveCode::select('code')
                ->where('organization_id', $user->userToken->token->organization_id)
                ->where('user_id', $user->id)
                ->get();
            $howManyThriveCodesUsed = count($thriveCode);
            $token_id = $user->userToken->token->id;
            $token_meta_datas = TokenMetaData::all();
            foreach ($token_meta_datas as $token_meta_data) {
                if (in_array($token_id, $token_meta_data->meta_data['token_ids'])) {
                    $happiAPPLimit = $token_meta_data->meta_data['HappiAPP'];
                }
            }
            if ($howManyThriveCodesUsed < $happiAPPLimit) {
                // Allowed to take a new token

                $newThriveCode = ThriveCode::where('organization_id', $user->userToken->token->organization_id)->AvaliableCode()->first();
                if ($newThriveCode) {
                    // No thrieve code left
                    return view('Frontend/includes/codegeneration')->with('getNewCodeBtn', True)->with('codes', $thriveCode)->with('code_left', ($happiAPPLimit - $howManyThriveCodesUsed))->with('no_code_available', '');
                } else {
                    return view('Frontend/includes/codegeneration')->with('getNewCodeBtn', False)->with('codes', $thriveCode)->with('no_code_available', 'For more HappiApp Code please contact organisation.');
                }
            } else {
                // All token occupied by user
                return view('Frontend/includes/codegeneration')->with('getNewCodeBtn', False)->with('codes', $thriveCode)->with('no_code_available', '');
            }
        } elseif (BundleStatus::where('user_id', $user->id)->ValidHappimyndApp()->first()) {
            $bundleStatus = BundleStatus::where('user_id', $user->id)->ValidHappimyndApp()->first();
            if ($bundleStatus->isCompleted()) {
                $thriveCode = ThriveCode::select('code')->where('user_id', $user->id)->where('status', 'active')->get();
                // All token occupied by user
                return view('Frontend/includes/codegeneration')->with('getNewCodeBtn', False)->with('codes', $thriveCode)->with('no_code_available', '');
            } else {
                dd('s');
            }
        }
        return redirect(route('user.dashboard'));
    }

    public function assessment(Request $request)
    {
        $user = User::where('id', auth('user')->user()->id)->with('userToken')->first();
        $assessmentCompletedCount = Assessment::where('user_id', $user->id)->completedAssessment()->get()->count();
        if ($assessmentCompletedCount > 6) {
            return redirect(route('user.dashboard'));
        }
        $token = $user->userToken?$user->userToken->token:null;
        if ($token) {
            $plan_id = $token->plans()->where('plan_id', '<=', '2')->orderBy('plan_id', 'DESC')->first(); //TODO : remove if another packages are activated
            if ($plan_id) {
                $plan_id = $plan_id->plan_id;
            } else {
                $plan_id = 0;
            }
        } else $plan_id = 0;
        $slotBooked = json_encode(AppointmentService::getBookedAppointmentDates());
        // if multiple package_id then check if package_id 2 exists => package_id=2

        return view('Frontend/assessment/assessment')->with('user', $user)->with('plan_id', $plan_id)->with('slotBooked', $slotBooked);
    }

    public function exploreServices()
    {
        $data=ServiceImage::all();
        $button_contents=EditButton::where('page_name','services')->get();
        $dataContent = DataGroup::with('content')->where('name', 'explore-services')->first();
        $happiApp = '';
        $happiTALK = '';
        $happiSPACE = '';
        $happiCHAT = '';
        $values = '';
        if ($dataContent) {
            $values = [
                'HappiAPP',
                'HappiTALK',
                'HappiSPACE',
                'HappiCHAT',
            ];

            $happiApp = $dataContent->content[0];
            $happiTALK = $dataContent->content[1];
            $happiSPACE = $dataContent->content[2];
            $happiCHAT = $dataContent->content[3];
        }

        return view('Frontend/services/exploreservices')->with([
            'happiApp' => $happiApp,
            'happiTALK' => $happiTALK,
            'happiSPACE' => $happiSPACE,
            'happiCHAT' => $happiCHAT,
            'values' => $values,
        ])->with('data',$data)->with('button_contents',$button_contents);
    }

    public function changePasswordView(Request $request)
    {
        return view('Frontend/profilesetting/changepassword');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $formData = $request->validated();
        $queryResult = User::find(auth('user')->user()->id)->update([
            'password' => $request['password'],
        ]);
        if ($queryResult) {
            JWTAuth::invalidate(JWTAuth::getToken());
            $user = User::find(auth('user')->user()->id);
            $token = auth('user')->login($user);
            $cookie = Cookie::make('user-access-token', $token);
            Cookie::queue($cookie);
            return $this->apiService->success('Password Changed Successfully.');
        }
        Log::alert('issue changing password');
        Log::alert($request->toArray());
    }

    public function editProfileView(Request $request)
    {
        $countries = Country::all();
        return view('Frontend/profilesetting/editprofile', compact('countries'));
    }

    public function editProfile(EditUserProfileRequest $request)
    {
        $formData  = $request->validated();
        //if user doesn't change avatar then value of avatar in request is null, removing avatar key below
        if (is_null($formData['avatar'])) {
            unset($formData['avatar']);
        }
        $queryResult = User::find(auth('user')->user()->id)->update($formData);

        if ($queryResult) {
            if (config('constants.bitrix')) {
                /** Get the lead id from auth user */
                $lead_id = auth('user')->user()->lead_id;
                $deal_id = auth('user')->user()->deal_id;
                if ($lead_id) {
                    /** Update the lead in the bitrix */
                    $bitrixResponse = $this->bitrix->updateLead($lead_id, User::find(auth('user')->user()->id));
                }
                if ($deal_id) {
                    /** Update the contact for the deal(B2C) in the bitrix */
                    $updateContactResponse = $this->bitrix->addOrUpdateContactForDeal(
                        $deal_id,
                        User::find(auth('user')->user()->id)
                    );
                    if ((isset($updateContactResponse->result)) && $updateContactResponse->result) {
                        $bitrixResponse = $this->bitrix->updateDeal(
                            $deal_id,
                            User::find(auth('user')->user()->id),
                            array('contactId' => $updateContactResponse->result)
                        );
                    }
                }
            }

            return $this->apiService->success(['route' => route('user.editProfileView'), 'status' => 'Profile updated successfully']);
        }
        Log::alert('error while updating user details for user_id:' . auth('user')->user()->id . 'details=>' . $request->toArray());
        return $this->apiService->success('Error occured while saving details');
    }

    public function getAuthenticatedUser(Request $request)
    {
        return JWTAuth::user();
    }

    public function postRaiseQuery(RaiseQueryRequest $request)
    {
        $formData  = $request->validated();
        $raisedQuery = RaiseQuery::create($formData);
        $query = RaiseQuery::with('user')->find($raisedQuery->id);
        $mailDetails = [
            'username' => $query->user->username,
            'email' => $query->user->email,
            'query' => [
                'description' => $query->query,
                'category' => $query->category
            ],
        ];
        // using mailable class to send markdown email
        Mail::to(env('SUPPORT_MAIL'))->queue(new QueryRaisedToAdmin($mailDetails));
        return $this->apiService->success('Successfully, Query raised.!');
    }

    public function checkVerify(Request $request)
    {
        $user = User::where('id', auth('user')->user()->id)->with('verifyUser')->first();
        return $this->apiService->response([
            'error' => false,
            'email' => $user->email,
            'email_verify' => ($user->verifyUser) ? $user->verifyUser->email_verify : false,
            'mobile' => $user->mobile,
            'mobile_verify' => ($user->verifyUser) ? $user->verifyUser->mobile_verify : false
        ]);
    }

    public function updateEmail(UpdateEmailRequest $request)
    {
        $formData  = $request->validated();
        $useemail = true;
        $subscribe = true;
        $coupon = true;

        if ($request->input('useemail') == false) {
            $useemail = false;
        }

        if ($request->input('subscribe') == false) {
            $subscribe = false;
        }

        if ($request->input('coupon') == false) {
            $coupon = false;
        }

        $user = auth('user')->user();
        $email = $formData['email'];
        if ($user->email != $email) {


            // if($request->mobile){
            //     $user->email = $email;
            //     $user->mobile = $request->mobile;

            //     $user->save();
            //     $user->verifyUser()->updateOrCreate(
            //         ['user_id' => $user->id],
            //         [
            //             'email' => $email,
            //             'mobile' => $request->mobile,

            //             'email_verify' => false,
            //             'forget_email_permission' => $useemail,
            //             'subscribe_newsletter_blog' => $subscribe
            //         ]
            //     );
            // }else{
                $user->email = $email;
                $user->save();
                $user->verifyUser()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'email' => $email,
                        'email_verify' => false,
                        'forget_email_permission' => $useemail,
                        'subscribe_newsletter_blog' => $subscribe
                    ]
                );
            // }
            
            if (config('constants.bitrix')) {
                /** Update the lead on bitrix */
                $lead_id = $user->lead_id;
                $deal_id = $user->deal_id;
                if ($lead_id) {
                    /** Update the lead in the bitrix */
                    $bitrixResponse = $this->bitrix->updateLead($lead_id, User::find(auth('user')->user()->id));
                }
                if ($deal_id) {
                    /** Update the contact for the deal(B2C) in the bitrix */
                    $updateContactResponse = $this->bitrix->addOrUpdateContactForDeal(
                        $deal_id,
                        User::find(auth('user')->user()->id)
                    );
                    if ((isset($updateContactResponse->result)) && $updateContactResponse->result) {
                        $bitrixResponse = $this->bitrix->updateDeal(
                            $deal_id,
                            User::find(auth('user')->user()->id),
                            array('contactId' => $updateContactResponse->result)
                        );
                    }
                }
            }
        }
        return $this->apiService->success('Successfully, Email updated.!');
    }

    public function updateMobile(UpdateMobileRequest $request)
    {
        $formData  = $request->validated();
        $user = auth('user')->user();
        $mobile = $formData['mobile'];
        $country_id = $formData['country_id'];

        // if ($user->mobile != $mobile) {

        $user->mobile = $mobile;
        $user->country_id = $country_id;
        $user->save();

        $user->verifyUser()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'mobile' => $mobile,
                'mobile_verify' => false,
            ]
        );

        if (config('constants.bitrix')) {
            /** Update the lead on bitrix */
            $lead_id = $user->lead_id;
            $deal_id = $user->deal_id;
            if ($lead_id) {
                /** Update the lead in the bitrix */
                $bitrixResponse = $this->bitrix->updateLead($lead_id, User::find(auth('user')->user()->id));
            }
            if ($deal_id) {
                /** Update the contact for the deal(B2C) in the bitrix */
                $updateContactResponse = $this->bitrix->addOrUpdateContactForDeal(
                    $deal_id,
                    User::find(auth('user')->user()->id)
                );
                if ((isset($updateContactResponse->result)) && $updateContactResponse->result) {
                    $bitrixResponse = $this->bitrix->updateDeal(
                        $deal_id,
                        User::find(auth('user')->user()->id),
                        array('contactId' => $updateContactResponse->result)
                    );
                }
            }
        }
        // }

        return $this->apiService->success('Successfully, Mobile No updated.!');
    }

    public function generateOTP($type, Request $request)
    {
        srand(time());
        $otp = rand(100000, 999999);

        $username = $request->input('username');

        // Logged in user
        if (Auth::check()) {
            $user = User::where('id', auth('user')->user()->id)->with('verifyUser')->first();
        } else {
            if ($username != null) {
                $user = User::where('username', $username)->with('verifyUser')->first();
            } else {
                return redirect(route('user.loginView'));
            }
        }
        if ($type == 'mobile') {
            if ($user->sendMobileOtp()) {
                return $this->apiService->success('Successfully, Mobile OTP is sent.!');
            }
            return $this->apiService->error('Something went wrong, please try again');
        } else if ($type == 'email') {
            if ($user->sendMailOtp()) {
                return $this->apiService->success('Successfully, Email OTP is send.!');
            }
            return $this->apiService->error('Something went wrong, please try again');
        }
    }

    public function verifyOtpByCode($type, Request $request)
    {
        // $formData = $request->validated();
        if (Auth::check()) {
            $user = User::where('id', auth('user')->user()->id)->with('verifyUser')->first();
        } else {
            $user = User::where('username', $request->input('username'))->with('verifyUser')->first();
        }

        $assessment= Assessment::where('user_id' , $user->id)->first();

        $otp = $request['otp'];
        if ($type == 'mobile') {
            if ($user->verifyMobileOtp($otp)) {

                // $this->regenerateReprot($assessment->id);
                
                // $bundleStatus = [
                //     'valid'         =>  '1',
                //     'percentage_covered'   => '0.00',
                //     'plan_id'       => '1',
                //     'user_id'       => $user->id,
                // ];

                // BundleStatus::create($bundleStatus);

                return $this->apiService->success('Successfully, Mobile OTP is verified.!');
            }
            return $this->apiService->error('Invalid Mobile OTP/OTP has Expired!');
        } else if ($type == 'email') {
            if ($user->verifyMailOtp($otp)) {

                // $this->regenerateReprot($assessment->id);

                // $receipt = [
                //     'marchant_name' => 'RazorPay',
                //     'amount'        => '299.00',
                //     'currency'      => 'INR',
                //     'status'        => '1',
                //     'user_id'       => $user->id,
                // ];
                // $create_receipt = Receipt::create($receipt);

                // $bundleStatus = [
                //     'valid'         =>  '1',
                //     'percentage_covered'   => '100.00',
                //     'plan_id'       => '1',
                //     'user_id'       => $user->id,
                //     'receipt_id'    => $create_receipt->create_receipt,
                // ];

                // BundleStatus::create($bundleStatus);
                

                return $this->apiService->success('Successfully, Email OTP is verified.!');
            }
            return $this->apiService->success('Invalid Email OTP/OTP has Expired!');
        }
    }



    public function regenerateReprot($assessment_id)
    {
        try {
            $assessment  = Assessment::find($assessment_id);
            if ($assessment) {
                if (!$assessment->isCompleted()) {
                    return $this->apiResponseService->error([
                        'notify' => [
                            'type' => 'error',
                            'message' => 'Assessment not completed'
                        ]
                    ]);
                }
                if ($assessment->report) {
                    $reportLink = $assessment->report;
                    $fileName = explode(config('constants.mediaAssets.assessmentReports.folderName'), $reportLink)[1];
                    $fileService = new FileService();
                    $fileService->deleteAssetFile('assessmentReports', $fileName);
                    $assessment->report = null;
                    $assessment->save();
                }
                $user = $assessment->user;
                \Log::info('generating report from regenerateReport method');
                $user->generateReportAndSendMail(false, true);
                return $this->apiResponseService->success([
                    'notify' => [
                        'type' => 'success',
                        'message' => 'Deleted existing report if any and report will be generated'
                    ]
                ]);
            }
        } catch (Exception $e) {
            \Log::error($e);
            return $this->apiResponseService->error([
                'notify' => [
                    'type' => 'error',
                    'message' => 'some problem occurred please contact developer'
                ]
            ]);
        }
    }



    public function verifyOtpByLink($type, $user_id, $otp)
    {
        $user = User::find(base64_decode($user_id));
        if ($user) {
            $verifyUser = $user->verifyUser->first();
            if ($type == "email") {
                if ($user->verifyMailOtp($otp)) {
                    return $this->apiService->success('Email Verified Successfully.');
                }
            }
        }
        return $this->apiService->error('Invalid Otp/Otp has expired');
    }

    public function checkForThriveCode(Request $request)
    {
        /**
         * Status: 1 => 'Your HappiApp Code',
         * Status: 2 => 'Are you sure to avail the HappiApp Code'
         * Status: 3 => 'Please Contact your organization'
         * Status: 4 => 'You will get the HappiApp Code very soon !!'
         * Status: 5 => 'You will get the HappiApp Code in 36 hours.'
         * Status: 6 => 'Please Buy the Happimynd App plan'
         */

        $response = array(
            "1" => "Your HappiApp Code",
            "2" => "Are you sure to avail the HappiApp Code",
            "3" => "Please Contact your organization",
            "4" => "You will get the HappiApp Code very soon !!",
            "5" => "You will get the HappiApp Code in 36 hours.",
            "6" => "Please Buy the Happimynd App plan",
        );

        $user_id = auth('user')->user()->id;
        $avail = $request->input('avail');
        $user = User::with('userToken')->where('id', $user_id)->first();
        if ($user) {
            /** If user has happimynd token */
            if ($user->userToken) {

                /** If user already have thrive code. */
                $thriveCode = ThriveCode::where('organization_id', $user->userToken->token->organization_id)
                    ->where('user_id', $user->id)
                    ->first();

                if ($thriveCode) {
                    return $this->apiService->responseWithStatus("1", $response["1"], $thriveCode->code);
                }

                /** If user want to avail the thrive code. */
                $thriveCode = ThriveCode::where('organization_id', $user->userToken->token->organization_id)->AvaliableCode()->first();

                /** If organization has available HappiApp Code. */
                if ($thriveCode) {
                    if ($avail) {
                        $tokeService = new TokenService();
                        $tokeService->assignThriveCode($thriveCode->id, $user->id);

                        $happiAPPLimit = 0;

                        $allThriveCode = ThriveCode::select('code')
                            ->where('organization_id', $user->userToken->token->organization_id)
                            ->where('user_id', $user->id)
                            ->get();
                        $howManyThriveCodesUsed = count($allThriveCode);

                        $token_id = $user->userToken->token->id;
                        $token_meta_datas = TokenMetaData::all();

                        foreach ($token_meta_datas as $token_meta_data) {
                            if (in_array($token_id, $token_meta_data->meta_data['token_ids'])) {
                                $happiAPPLimit = $token_meta_data->meta_data['HappiAPP'];
                            }
                        }
                        (new PackageService)->bundlePlanCompleted(
                            BundleStatus::where('plan_id', 5)
                                ->where('user_id', auth('user')->user()->id)
                                ->where('percentage_covered', '!=', 100.00)
                                ->latest()
                                ->first()->id,
                            ($howManyThriveCodesUsed / $happiAPPLimit) * 100
                        );
                        return $this->apiService->responseWithStatus("1", $response["1"], $thriveCode->code);
                    }
                    return $this->apiService->responseWithStatus("2", $response["2"]);
                } else {
                    // TODO:: send notification to the organization about you running out of HappiApp Code.
                    return $this->apiService->responseWithStatus("3", $response["3"]);
                }
            } else {
                $happimyndOrg = Organization::Happimynd()->first();
                if ($happimyndOrg) {
                    $organization_id = $happimyndOrg->id;
                    /** if User buied happimyndApp */
                    $bundleStatus = BundleStatus::where('user_id', $user->id)->ValidHappimyndApp()->first();
                    if ($bundleStatus) {
                        /** If user already have HappiApp Code. */
                        $thriveCode = ThriveCode::where('organization_id', $organization_id)
                            ->where('user_id', $user->id)
                            ->first();
                        if ($thriveCode) {
                            return $this->apiService->responseWithStatus("1", $response["1"], $thriveCode->code);
                        }

                        $thriveCode = ThriveCode::where('organization_id', $organization_id)->AvaliableCode()->first();
                        if ($thriveCode) {
                            if ($avail) {
                                $tokeService = new TokenService();
                                $tokeService->assignThriveCode($thriveCode->id, $user->id);
                                $happiAPPLimit = 0;

                                $thriveCode = ThriveCode::select('code')
                                    ->where('organization_id', $organization_id)
                                    ->where('user_id', $user->id)
                                    ->first();
                                (new PackageService)->bundlePlanCompleted(
                                    BundleStatus::where('plan_id', 5)
                                        ->where('user_id', auth('user')->user()->id)
                                        ->where('percentage_covered', '!=', 100.00)
                                        ->latest()
                                        ->first()->id
                                );
                                return $this->apiService->responseWithStatus("1", $response["1"], $thriveCode->code);
                            }
                            return $this->apiService->responseWithStatus("2", $response["2"]);
                        } else {
                            // TODO:: send notification to the happimynd about you running out of HappiApp Code.
                            return $this->apiService->responseWithStatus("5", $response["5"]);
                        }
                    } else {
                        return $this->apiService->responseWithStatus("6", $response["6"]);
                    }
                }
            }
        }
        return $this->apiService->responseWithStatus("4", $response["4"]);
    }

    public function getThriveCode(Request $request)
    {
        $user_id = auth('user')->user()->id;
        $happiAPPLimit = 0;
        $user = User::with('userToken')->where('id', $user_id)->first();

        $thriveCode = ThriveCode::select('code')
            ->where('organization_id', $user->userToken->token->organization_id)
            ->where('user_id', $user->id)
            ->get();
        $howManyThriveCodesUsed = count($thriveCode);

        $token_id = $user->userToken->token->id;
        $token_meta_datas = TokenMetaData::all();

        foreach ($token_meta_datas as $token_meta_data) {
            if (in_array($token_id, $token_meta_data->meta_data['token_ids'])) {
                $happiAPPLimit = $token_meta_data->meta_data['HappiAPP'];
            }
        }

        if ($howManyThriveCodesUsed < $happiAPPLimit) {
            $thriveCode = ThriveCode::where('organization_id', $user->userToken->token->organization_id)->AvaliableCode()->first();
            if ($thriveCode) {
                $tokeService = new TokenService();
                $tokeService->assignThriveCode($thriveCode->id, $user->id);
                (new PackageService)->bundlePlanCompleted(
                    BundleStatus::where('plan_id', 5)
                        ->where('user_id', auth('user')->user()->id)
                        ->where('percentage_covered', '!=', 100.00)
                        ->latest()
                        ->first()->id,
                    (($howManyThriveCodesUsed + 1) / $happiAPPLimit) * 100
                );
            }
            return redirect(route('user.thrivecode'));
        } else {
            return redirect(route('user.thrivecode'));
        }
    }


    public function usernameExistOrNot()
    {

        $data = User::where('username', request('username'))->orWhere('email', request('username'))->get();
        // dd($data[0]);
        if (count($data) == 1) {
            $permissions = VerifyUser::select('forget_email_permission', 'forget_mobile_permission')->where('user_id', $data[0]['id'])->get();
            if (empty($permissions[0])) {
                return response()->json([
                    "flag" => false,
                    "status" => "The entered username/e-mail has not been verified",
                ]);
            }
            return response()->json([
                "flag" => true,
                "email_permission" => $permissions[0]['forget_email_permission'],
                "mobile_permission" => $permissions[0]['forget_mobile_permission'],
                "username" => $data[0]['username']
            ]);
        } else {
            return response()->json([
                "flag" => false,
                "status" => "The entered username/e-mail is invalid.",
            ]);
        }
    }

    public function forgetPasswordReset(Request $request)
    {
        $queryResult = User::where('username', $request->username)->update(['password' => Hash::make($request->password1)]);
        return redirect(route('user.loginView'));
    }

    public function getTerms(Request $request)
    {
        $dataContent = DataGroup::with('content')->where('name', 'termsandservices')->first();
        return view('Frontend/terms/terms')->with('dataContent', $dataContent);
    }

    public function paidBlog()
    {
        $posts = PostCategory::with(['post' => function ($query) {
            $query->where([
                'restricted_content' => 1,
                'publish_status' => 1
            ]);
        }])->get();
        $blogs = '';
        $videos = '';
        $audios = '';
        $featured = Post::where('featured', 1)->first();
        foreach ($posts as $postItem) {
            if ($postItem->id == 1) {
                if (count($postItem->post) > 0) {
                    $blogs = $postItem->post;
                }
            } else if ($postItem->id == 2) {
                if (count($postItem->post) > 0) {
                    $videos = $postItem->post;
                }
            } else {
                if (count($postItem->post) > 0) {
                    $audios = $postItem->post;
                }
            }
        }

        return view('Frontend/blog/blog')
            ->with([
                'blogs' => $blogs,
                'videos' => $videos,
                'audios' => $audios,
                'featured' => $featured
            ]);
    }

    public function readPaidBlog($slug)
    {
        $relatedArticle = '';
        $post = Post::where('slug', $slug)->first();
        $relatedPosts = PostCategory::with([
            'post' => function ($query) use ($slug, $post) {
                $query->where([
                    'restricted_content' => 0,
                    'publish_status' => 1,
                    'post_category_id' => $post->category_id
                ])->where('slug', '!=', $slug);
            }
        ])->where('id', 1)->get();
        if (count($relatedPosts[0]->post) >= 1) {

            $relatedArticle = ($relatedPosts[0]->post)->splice(0, 3);
        }
        return view('Frontend/blog/read_blog')->with('post', $post)->with('relatedArticle', $relatedArticle);
    }


    public function allBlog(Request $request, $slug)
    {

        $posts = PostCategory::with([
            'post' => function ($query) {
                $query->where([
                    'restricted_content' => 0,
                    'publish_status' => 1,
                ]);
            }
        ])->where('name', $slug)->first();


        return view('Frontend/blog/all_blog')->with('posts', $posts);
    }

    public function getAvailableDates(Request $request)
    {
        $validatedData = $request->date;

        $dateFormatted = Carbon::createFromFormat('m-d-Y', $validatedData)->format('Y-m-d');

        $slots  = AvailabilityDates::where('date', $dateFormatted)->get();
        if ($slots) {
            $availableSlots = [];
            foreach ($slots as $slot) {
                $assessmentCount = AssessmentApprove::where('available_date', $dateFormatted)->where('slot', $slot->time)->count();

                $response = [];
                $response['time'] = $slot->time;
                $response['id'] = $slot->id;

                if (intval($assessmentCount) < intval($slot->consultant)) {
                    array_push($availableSlots, $response);
                }
            }

            return response()->json($availableSlots);
        }
        return $this->apiResponse->error("No Available dates found.!");
    }

    public function getFaq()
    {
        $generalFaqs = '';
        $organizationFaqs = '';
        //get all the dataGroup
        $faqs = DataGroup::with('content')->whereIn('name', ['faqs-general', 'faqs-organization'])->get();

        $generalFaqs = $faqs->filter(function ($value, $key) {
            return $value->name == 'faqs-general';
        });
        $organizationFaqs = $faqs->filter(function ($value, $key) {
            return $value->name == 'faqs-organization';
        });

        $generalFaqsAll = $generalFaqs->all();
        $organizationFaqsAll = $organizationFaqs->all();

        //check if the records contain records
        if (count($generalFaqsAll) > 0) {
            $generalFaqs = $generalFaqsAll[0]->content;
        }
        if (count($organizationFaqsAll) > 0) {
            $organizationFaqs = $organizationFaqsAll[1]->content;
        }

        return view('Frontend/faq/faq')->with([
            'generalFaqs' => $generalFaqs,
            'organizationFaqs' => $organizationFaqs,
        ]);
    }

    public function generateSendGuardianOTP($type, Request $request)
    {
        $otp = rand(100000, 999999);
        Session::put('under_age_session_id', time());
        $session_id = Session::get('under_age_session_id');
        switch ($type) {
            case 'email':
                VerifyGuardian::updateOrCreate(['session' => $session_id], [
                    'email' => $request->input,
                    'mobile' => null,
                    'otp' => $otp,
                    'expires_at' => now()->addMinutes(60)
                ]);

                $mailDetails = [
                    'username' => 'Guardian',
                    'email' => $request->input,
                    'nickname' => 'Guardian',
                    'otp' => $otp,
                ];

                // using mailable class to send markdown email
                Mail::to($request->input)->queue(new OtpEmail($mailDetails));
                return $this->apiService->success(['session_id' => $session_id]);
            case 'mobile':
                VerifyGuardian::updateOrCreate(['session' => $session_id], [
                    'email' => null,
                    'mobile' => $request->input,
                    'otp' => $otp,
                    'expires_at' => now()->addMinutes(60)
                ]);

                $phoneDetails = [
                    'username' => 'Guardian',
                    'email' => $request->input,
                    'nickname' => 'Guardian',
                    'otp' => $otp,
                ];
                // Send Mobile OTP to user
                $response = OTPService::sendAnonymousMobileOtp($otp, $request->input);

                if ($response == true) {

                    return $this->apiService->success(['session_id' => $session_id]);
                } else {

                    return $this->apiService->error('Something went wrong.!');
                }
            default:
                # code...
                break;
        }
    }

    public function verifyGuardianOtpByCode(Request $request)
    {
        $guardian = VerifyGuardian::where([
            'otp' => $request->otp,
            'session' => $request->session_id
        ])->first();
        if (!empty($guardian) and $guardian->expires_at > Carbon::now()) {
            $guardian->verified = 1;
            $guardian->save();
            return $this->apiService->success('Successfully, Email OTP is verifered.!');
        } else {
            return $this->apiService->error('Invalid Email OTP/OTP has Expired!');
        }
    }

    public function verificationData(Request $request)
    {
        $responseData = [];
        $user = auth('user')->user();
        $responseData['user'] = $user->toArray();
        $assessment = Assessment::select('id', 'ended_at', 'report')->where('user_id', $user->id)->latest('started_at')->first();
        $appointment_status = (!is_null($assessment) &&  $assessment->approve && $assessment->approve->slot != "") ? true : false;
        $responseData['assessment'] = null;
        if ($assessment) {
            $responseData['assessment'] = $assessment->toArray();
            $responseData['assessment']['isCompleted'] = $assessment->ended_at != null;
        }
        $bundleStatuses = $user->bundleStatus()
            ->latest()
            ->select('percentage_covered', 'plan_id', 'valid')
            ->with(['plans' => function ($query) {
                return $query->with('package');
            }])
            ->get();
        $responseData['plans'] = [];
        foreach ($bundleStatuses as $bundleStatus) {
            $plan = $bundleStatus->plans;
            // dd($bundleStatus);
            $responseData['plans'][$plan->package->name] = [
                'percentage_covered' => $bundleStatus->percentage_covered,
            ];
        }
        $psychologistAppointment = $user->psychologistAppointment;
        $responseData['user']['psychologist_appointment_status'] = null;
        if ($psychologistAppointment != null) {
            $responseData['user']['psychologist_appointment_status'] = $psychologistAppointment->appointment_status;
        }
        $responseData['user']['verify']['email_verified'] = $user->isEmailVerified();
        $responseData['user']['verify']['mobile_verified'] = $user->isMobileVerified();
        $responseData['user']['appointment_status'] = $appointment_status;
        return $this->apiResponseService->success($responseData);
    }

    public function getBookedDates(Request $request)
    {
        $user = auth('user')->user();
        $assessment = $user->assessment()->with('approve')->orderBy('started_at', 'desc')->first();
        $slotBooked = json_encode(AppointmentService::getBookedAppointmentDates());
        $appointment_status = (!is_null($assessment) &&  $assessment->approve && $assessment->approve->slot != "") ? true : false;

        $booked_dates = AssessmentApprove::select('available_date')->whereNotNull('available_date')->get();

        $disableDates = Availability::select('date')->get();
        $responseData = [
            'booked_slots' => $slotBooked,
            'user_appointment_status' => $appointment_status,
            'booked_dates' => $booked_dates,
            'disabled_dates' => $disableDates
        ];
        return $this->apiResponseService->success($responseData);
    }

    public function generateGuardianOTP($type, Request $request)
    {
        srand(time());
        $otp = rand(100000, 999999);
        //getting the session id of the user
        $session_id = Session::get('under_age_session_id');
        $guardian = VerifyGuardian::where('session', $session_id)->first();

        // if the user session was not found
        if (!$guardian) {
            return $this->apiService->error('OTP could not be generated. Kindly try again.!');
        }

        switch ($type) {
            case 'mobile':
                $guardian->otp = $otp;
                $guardian->expires_at = now()->addMinutes(60);
                $guardian->save();

                // Send SMS
                $response = OTPService::sendAnonymousMobileOtp($otp, $guardian->mobile);
                if ($response == true) {

                    return $this->apiService->success('Successfully, Mobile OTP is send.!');
                } else {

                    return $this->apiService->error('Something went wrong.!');
                }

            case 'email':
                $guardian->otp = $otp;
                $guardian->expires_at = now()->addMinutes(60);
                $guardian->save();

                $mailDetails = [
                    'username' => 'Guardian',
                    'email' => $guardian->email,
                    'nickname' => 'Guardian',
                    'otp' => $otp,
                ];

                // using mailable class to send markdown email
                Mail::to($guardian->email)->queue(new OtpEmail($mailDetails));
                return $this->apiService->success('Successfully, Email OTP is send.!');
        }
    }
    public function otherServices()
    {
        $happimynd = null;
        $otherServices = null;

        $serviceTypeGroup = ServiceTypeGroup::whereIn('name',  ['Other Services', 'HappiMynd Services'])
            ->with('service')
            ->get();

        $filteredHappimynd = $serviceTypeGroup->filter(function ($value, $key) {
            return $value->name == "HappiMynd Services";
        })->values()->all();
        $filterOtherServices = $serviceTypeGroup->filter(function ($value, $key) {
            return $value->name == "Other Services";
        })->values()->all();

        if (count($filteredHappimynd) > 0) {
            $happimynd = $filteredHappimynd[0];
        }
        if (count($filterOtherServices) > 0) {
            $otherServices = $filterOtherServices[0];
        }
        return view('Frontend/services/otherservices')->with([
            'happimynd' => $happimynd,
            'otherServices' => $otherServices,
        ]);
    }

    public function saveOtherServicesMailList(SubscribersRequest $request)
    {

        $validatedData = $request->validated();

        $data = [
            'other_service_id' => $request->other_service,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'name' => $request->name,
            'paid' => 0
        ];
        $details = OtherServiceSubscriber::Create($data);
        $serviceType = OtherService::find($request->other_service)->load('type.type');

        if ($serviceType->type->type->name == "Other Services") {
            $amount = $details->load('otherService')->otherService->discountedPrice();
        } else {
            $amount = $details->load('otherService.educationService')->otherService->educationService->discounted_price;
        }
        $callback = route('payment.responseOtherServices');
        $result = new PaymentService();
        return $result->paymentServiceRequest($amount, $details, $callback);
    }
    public function Services(){
        $data=ServiceImage::all();
        $exploreServiceContent = DataGroup::with('content')->where('name', 'explore-services')->first();
        $button_contents=EditButton::where('page_name','services')->get();
        return view('Frontend/services/services')->with('exploreServiceContent', $exploreServiceContent)->with('button_contents',$button_contents)->with('data',$data);
    }

    public function educationServices()
    {

        $recommended = null;
        $mostPopular = null;

        $serviceTypeGroup = ServiceTypeGroup::whereIn('name',  ['Recommended', 'Most Popular'])
            ->with('service')
            ->get();


        $filteredRecommended = $serviceTypeGroup->filter(function ($value, $key) {
            return $value->name == "Recommended";
        })->values()->all();

        $filterMostPopular = $serviceTypeGroup->filter(function ($value, $key) {
            return $value->name == "Most Popular";
        })->values()->all();

        if (count($filteredRecommended) > 0) {
            $recommended = $filteredRecommended[0];
        }
        if (count($filterMostPopular) > 0) {
            $mostPopular = $filterMostPopular[0];
        }
        $mergedCourses = $recommended->service->merge($mostPopular->service);
        return view('Frontend/services/educationalservices')->with([
            'recommended' => $recommended,
            'mostPopular' => $mostPopular,
            'allCourses' => $mergedCourses,
        ]);
    }

    public function showOtherServices($id)
    {
        $other_service  =  OtherService::find($id);
        if (!$other_service) {
            return [];
        }
        return $other_service;
    }

    

    public function paymentLink($order_id , $user_id){

        $data = User::where('id' , $user_id)->first();
        $callback_url = url('payment-successfull');

        return view('payment/paymentRequestApp')
                ->with('callback_url', $callback_url)
                ->with('order', $order_id)
                ->with('user', $data);
    }

    public function paymentSuccessfull(Request $request){
        return 'successfully';
    }




    public function generateOTPEmail(Request $request)
    {

        return $request->all();
        srand(time());
        $otp = rand(100000, 999999);

        return response()->json(['data' => $otp]);

        // $username = $request->input('username');

        // Logged in user
        // if (Auth::check()) {
        //     $user = User::where('id', auth('user')->user()->id)->with('verifyUser')->first();
        // } else {
        //     if ($username != null) {
        //         $user = User::where('username', $username)->with('verifyUser')->first();
        //     } else {
        //         return redirect(route('user.loginView'));
        //     }
        // }
        // if ($type == 'mobile') {
        //     if ($user->sendMobileOtp()) {
        //         return $this->apiService->success('Successfully, Mobile OTP is sent.!');
        //     }
        //     return $this->apiService->error('Something went wrong, please try again');
        // } else if ($type == 'email') {

        //     return 'sfsdfsemail';
        //     if ($user->sendMailOtp()) {
        //         return $this->apiService->success('Successfully, Email OTP is send.!');
        //     }
        //     return $this->apiService->error('Something went wrong, please try again');
        // }
    }



    // public function downloadCompositionWeb(Request $request , $room_id , $composition_id){
    //     // return $composition_id;
    //     $accountSid = 'AC5d4615017e291c7ed2198089849b30a1';
    //     $authToken = 'b3241dac126bc8e8c8535e05c1bfbae1';
    //     $compositionSid = $composition_id;

     

    //     $app_key = "SKd5572e6fcb053580b02e198f1e6d21a4";
    //     $app_secret = "Lo44iumVX9Bh6yjCPnOIvONTRN7TJfCF";

    //   $headers = array(
    //       'Content-Type: multipart/form-data',
    //   );

    //   $ch = curl_init();
    //     curl_setopt( $ch,CURLOPT_URL, 'https://video.twilio.com/v1/Compositions/CJb8a0d4f2161395af484ffef181922ded/Media?Ttl=3600' );
    //     // curl_setopt( $ch,CURLOPT_POST, true );
    //     curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    //     curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    //     curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    //     curl_setopt($ch, CURLOPT_USERPWD, $app_key.':'.$app_secret);
    //     // curl_setopt( $ch,CURLOPT_POSTFIELDS, $fields );
    //     $result = curl_exec($ch);

    //     if($result == FALSE) {
    //       die('Curl failed: ' . curl_error($ch));
    //     }

    //     curl_close( $ch );

    //     $data = json_decode($result, true);

    //     // return $data['redirect_to'];
    //     return redirect($data['redirect_to']);

    // }



}




// curl -X GET 'https://video.twilio.com/v1/Compositions/CJb8a0d4f2161395af484ffef181922ded/Media?Ttl=3600' \
//      -u 'SKd5572e6fcb053580b02e198f1e6d21a4:Lo44iumVX9Bh6yjCPnOIvONTRN7TJfCF'







