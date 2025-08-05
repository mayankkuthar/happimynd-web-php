<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use ErrorException;
use App\Models\Plan;
use App\Models\ServiceImage;
use App\Models\EditButton;
use App\Models\Offer;
use App\Models\Post;
use App\Models\OurTeam;
use App\Models\OurClient;
use App\Models\User;
use App\Models\StaticSection;
use App\Models\Admin;
use App\Models\Token;
use App\Models\Package;
use App\Models\Duration;
use App\Models\OrganizationPageData;
use App\Models\OrganizationLogo;
use App\Models\DataGroup;
use App\Models\Assessment;
use App\Models\RaiseQuery;
use App\Models\ThriveCode;
use App\Models\DataContent;
use App\Models\ServiceType;
use App\Models\DurationType;
use App\Models\DynamicBundlePlan;
use App\Models\HappibuddyMonthlyReport;


use Illuminate\Support\Str;
use App\Models\Availability;
use App\Models\CarouselSection;
use App\Models\BundleStatus;
use App\Models\Organization;
use App\Models\OtherService;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use App\Models\AvailableDate;
use App\Models\Quotes;
use App\Models\CategoryToken;
use App\Models\TokenCategory;
use App\Models\TokenMetaData;
use App\Services\FileService;
use App\Collections\Constants;
use App\Services\TokenService;
use App\Models\AssessmentScore;
use App\Models\ServiceMetaData;
use App\Services\BitrixService;
use App\Models\AssessmentAnswer;
use App\Models\ServiceTypeGroup;
use App\Http\Requests\IosRequest;
use App\Models\AssessmentApprove;
use App\Models\AvailabilityDates;
use App\Http\Requests\BlogRequest;
use App\Models\EducationalService;
use Spatie\Permission\Models\Role;
use App\Services\AssessmentService;
use Illuminate\Support\Facades\Log;
use App\Models\EducationServiceType;
use App\Services\ApiResponseService;
use App\Services\AppointmentService;
use App\Http\Requests\AndroidRequest;
use App\Models\EducationServiceAuthor;
use App\Models\OtherServiceSubscriber;
use App\Models\Coupon;
use App\Models\CouponPlan;
use App\Models\CouponReceipt;
use App\Http\Requests\AdminBlogRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\File;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\AdminBitrixRequest;
use App\Http\Requests\GenerateTokenRequest;
use App\Http\Requests\AvailableDatesRequest;
use App\Http\Requests\AddOrganizationRequest;
use App\Http\Requests\AdminUserDetailRequest;
use App\Http\Requests\AdminUserUpdateRequest;
use App\Http\Requests\HappySpaceBitrixRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AdminOtherServiceRequest;
use App\Http\Requests\AdminEducationServiceRequest;
use App\Mail\TokenEmail;
use Illuminate\Mail\Markdown;
use App\Models\AssignedPsychologistForChat;
use App\Models\GroupChat;
use App\Models\Feedback;

use App\Models\HappitalkSession;
use App\Models\HappitalkBooking;
use App\Models\UserMood;
use App\Models\RewardPointInstance;
use App\Models\UserRewardPointRecord;


use Maatwebsite\Excel\Facades\Excel;

use App\Exports\UserListDateWiseExport;
use App\Exports\UserPlanDateWiseExport;
use App\Exports\FeedbackExport;

use App\Models\AssignPsyToPlan;
use App\Models\Psychologist;
use App\Models\OfferScreenContent;



use Validator;


class AdminController extends Controller
{
    protected $apiResponseService;

    public function __construct(ApiResponseService $apiResponseService)
    {
        $this->apiResponseService = $apiResponseService;
    }

    public function dashboard(Request $request)
    { 

        $userCount = User::RemoveTestUser()->count();
        $testUserCount = User::count() - $userCount;
        $organizationUsersCount = User::whereHas('userToken')->RemoveTestUser()->count();
        $userCount -= $organizationUsersCount;
        $organizationCount = Organization::all()->count();
        // $assessment = Assessment::whereHas('user', function ($query) {
        //     $query->removeTestUser();
        // })->get();
        // $totalAssessmentCount = $assessment->count() ?? 0;
        // $totalReportsGenerated = 0;
        // $pendingAssessmentCount = 0;
        // $completedAssessmentCount = 0;
        // foreach ($assessment as $assessment) {
        //     if (!empty($assessment->report)) {
        //         $totalReportsGenerated++;
        //     }
        //     if (!empty($assessment->ended_at)) {
        //         $completedAssessmentCount++;
        //     } else {
        //         $pendingAssessmentCount++;
        //     }
        // }

        $totalAssessmentCount = Assessment::count();
        $totalReportsGenerated = Assessment::where('report' , '!=' , null)->count();
        $completedAssessmentCount = Assessment::where('ended_at' , '!=' , null)->count();
        $pendingAssessmentCount = Assessment::where('ended_at' , null)->count();


        $report_generated_on_website = Assessment::where('ended_at' , '!=' , null)->where('platform' , 'website')->count();
        $report_generated_on_android = Assessment::where('ended_at' , '!=' , null)->where('platform' , 'android')->count();
        $report_generated_on_ios = Assessment::where('ended_at' , '!=' , null)->where('platform' , 'ios')->count();

        
        $organizations = Organization::removeTestOrganization()->withCount('token')
            ->withCount(['token as token_used_count' => function ($query) {
                $query->ExpiredTokens();
            }])
            ->withCount(['token as token_unused_count' => function ($query) {
                $query->ValidTokens();
            }])
            ->orderBy('name')
            ->get();

        $products = Plan::withCount(['bundleStatus' => function ($query) {
            $query->where('valid', 1);
        }])
            ->withCount(['bundleStatus as B2C_user_count' => function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->RemoveTestUser()->doesntHave('userToken');
                })->with('user');
            }])
            ->withCount(['bundleStatus as B2B_user_count' => function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->RemoveTestUser();
                })->with('user');
            }])->get();
        // $tokenCount = Token::whereHas('organization', function ($query) {
        //     $query->removeTestOrganization();
        // })->get()->count();
        $tokenCount = Token::whereHas('organization', function ($query) {
            $query->removeTestOrganization();
        })->count();


        $session_done_count = HappitalkSession::where('is_end' , 1)->count();
        $total_earned = HappitalkBooking::sum('amount');
        $be_to_shared = HappitalkBooking::sum('amount_after_deduction');
        $b2c_talk_session_done = HappitalkSession::where('is_end' , '1')->where('user_type' , 'b2c')->count();
        $b2b_talk_session_done = HappitalkSession::where('is_end' , '1')->where('user_type' , 'b2b')->count();

        $session_Details = [
            'session_done_count' => $session_done_count,
            'total_earned' => $total_earned,
            'be_to_shared' => $be_to_shared,
            'b2b_talk_session_done' => $b2b_talk_session_done,
            'b2c_talk_session_done' => $b2c_talk_session_done,
        ];

        return view('Backend/dashboard')
            ->with('userCount', $userCount)
            ->with('testUserCount', $testUserCount)
            ->with('organizationUsersCount', $organizationUsersCount)
            ->with('organizationCount', $organizationCount)
            ->with('organizations', $organizations)
            ->with('products', $products)
            ->with('totalAssessmentCount', $totalAssessmentCount)
            ->with('pendingAssessmentCount', $pendingAssessmentCount)
            ->with('completedAssessmentCount', $completedAssessmentCount)
            ->with('totalReportsGenerated', $totalReportsGenerated)
            ->with('report_generated_on_website', $report_generated_on_website)
            ->with('report_generated_on_android', $report_generated_on_android)
            ->with('report_generated_on_ios', $report_generated_on_ios)
            ->with('tokenCount', $tokenCount)
            ->with('session_Details' , $session_Details);
    }
    public function addAdminUser(AdminUserDetailRequest $request)
    {
        $formData  = $request->validated();
        $roles = $formData['roles'];
        unset($formData['roles']);
        $admin  = Admin::create($formData);
        if ($admin) {
            $admin->syncRoles($roles);
            $request->session()->flash('success', 'Admin added!');
            return Redirect::to(route('admin.addAdminView'));
        }
        Log::error('error creating admin user');
    }

    public function editAdminView($id)
    {
        if (!auth('admin')->user()->hasAnyRole(['super-admin', 'admin'])) {
            return redirect(route('admin.dashboard'));
        }

        $admin = Admin::find($id);
        if ($admin) {
            $roles = Role::where('guard_name', 'admin')->get();
            return view('Backend/editAdmin')->with('roles', $roles)->with('admin', $admin);
        }
    }

    public function updateAdmin(AdminUserUpdateRequest $request)
    {
        $formData  = $request->validated();
        $updateData = [
            'first_name' => $formData['first_name'],
            'last_name' => $formData['last_name'],
            'gender' => $formData['gender'],
            'account_status' => $formData['account_status'] ?? 'active',
            'mobile' => $formData['mobile'],
            'email' => $formData['email'],
        ];

        if (!is_null($request->input('password'))) {
            $updateData['password'] = $formData['password'];
        }
        $user =  Admin::find($formData['user_id'])->update($updateData); //updated details
        //update roles
        if ($user) {
            $user =  Admin::find($formData['user_id']);
            $user->syncRoles($formData['roles']);
            return redirect(route('admin.editAdminView', ['id' => $user->id]))->with('success', 'Details updated');
        }
        return redirect()->back()->with('danger', 'Error updating details');
    }

    public function deleteUser($id)
    {
        if (auth('admin')->user()->hasAnyRole(['super-admin', 'admin']) && auth('admin')->user()->id != $id) {
            return Admin::find($id)->delete();
        }
    }

    public function addRemoveRoles($user_id, $roles)
    {
        if (auth('admin')->user()->hasRole('super-admin')) {
            $user = Admin::find($user_id);
            if (is_array($roles)) {
                return $user->syncRoles($roles);
            } else {
                return $user->syncRoles([$roles]);
            }
        }
    }

    public function getAddAdminView(Request $request)
    {
        if (!auth('admin')->user()->hasAnyRole(['super-admin', 'admin'])) {
            return redirect(route('admin.dashboard'));
        }
        $roles = Role::where('guard_name', 'admin')->get();
        return view('Backend/addAdmin')->with('roles', $roles);
    }

    public function getCustomerList(Request $request)
    {
        $users = null;
        if (!auth('admin')->user()->hasAnyRole(['super-admin', 'admin'])) {
            return redirect(route('admin.dashboard'));
        }
        // $users = User::latest()->with('profileType' , 'usersRating')->get();
        $perPage = $request->get('per_page', 10);
        $users = User::latest()->with('profileType' , 'usersRating')->paginate($perPage)
            ->appends($request->except('page'));

        return view('Backend/customerList')->with('users', $users);
    }



    public function downloadUserListXL(Request $request)
    {
        $users = null;
        if (!auth('admin')->user()->hasAnyRole(['super-admin', 'admin'])) {
            return redirect(route('admin.dashboard'));
        }
        // $users = User::latest()->with('profileType' , 'usersRating')->get();

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $data = [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];

        return Excel::download(new UserListDateWiseExport($data), 'User List ' . Carbon::now()->format('d-M-Y g-i a') . '.xlsx');

    }



    public function getAdminList(Request $request)
    {
        $admins = null;
        if (!auth('admin')->user()->hasAnyRole(['super-admin', 'admin'])) {
            return redirect(route('admin.dashboard'));
        }
        $admins = Admin::with('roles')->get();
        return view('Backend/adminList')->with('admins', $admins);
    }

    public function deleteAdmin($id)
    {
        if (!auth('admin')->user()->hasAnyRole(['super-admin', 'admin',])) {
            return redirect(route('admin.dashboard'));
        }

        $user = Admin::find($id);
        if ($user) {
            $user->delete();
        }
        return redirect(route('admin.adminListView'));
    }

    public function generateTokenView(Request $request)
    {
        if (!auth('admin')->user()->hasAnyRole(['super-admin', 'admin', 'happimynd-code'])) {
            return redirect(route('admin.dashboard'));
        }
        $organizations = Organization::AvaliableOrganization()->get();

        $orientationMailText = DataGroup::where('name', 'email-orientation')->first()->content()->where('title', 'body')->first()->content ?? '';

        $package = Package::with('plan')->where('bundle', 0)->get();
        $tokenCategory = TokenCategory::all();
        return view('Backend/generateToken')
            ->with('organizations', $organizations)
            ->with('tokenCategory', $tokenCategory)
            ->with('orientationMailText', $orientationMailText)
            ->with('packages', $package);
    }

    public function generateTokens(GenerateTokenRequest $request)
    {
        $request->validated();
        $tokenService = new TokenService;
        $organizations = Organization::AvaliableOrganization()->get();
        $thriveCodes = array();
        $emails = array();
        $emailBody = '';
        $attachmentPath = '';
        if ($request->file('email_file')) {
            try {

                $emails = $tokenService->getEmailsFromExcel($request->file('email_file'));
                $emailBody = $request->input('orientation-email-body');
            } catch (Exception $e) {

                return back()->with('error', $e->getMessage());
            }
        }

        if ($request->file('pdf_file')) {
            try {
                $fileService = new FileService();
                $attachmentPath = $fileService->getFilePath($request->file('pdf_file'));
            } catch (Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }
        if ($request->input('thrive_code') && $request->file('thrive_file')->isValid()) {
            $thriveCodes = $tokenService->generateThriveTokenForOrganization($request->input('organization_id'), $request->thrive_file);
        }
        $tokens = $tokenService->generateTokenForOrganization($request->input('organization_id'), $request->input('token_count'), $request->input('plans'), $request['use_limit'], $emails, $attachmentPath, $emailBody);
        $token_ids = $tokens->pluck('id');
        if (!is_null($request->input('HappiTALK-input')) || !is_null($request->input('HappiAPP-input'))) {
            $metaData = TokenMetaData::create([
                'organization_id' => $request->input('organization_id'),
                'meta_data' => array(
                    'token_ids' => $token_ids,
                    'HappiAPP'  => (int)$request->input('HappiAPP-input'),
                    'HappiTALK' => (int)$request->input('HappiTALK-input'),  // Total hours for organization
                    'HappiTALK2' => (int)$request->input('HappiTALK-input2'), // Session Limit per token
                )
            ]);
            Token::whereIn('id', $token_ids)->update(['token_meta_data_id' => $metaData->id]);
            $tokens = Token::where('token_meta_data_id', $metaData->id)->get();
        }

        if (!is_null($request->input('tokenCategories'))) {
            foreach ($token_ids as $tokenId) {
                foreach ($request->input('tokenCategories') as $tokenCategory) {
                    CategoryToken::create([
                        'token_id' => $tokenId,
                        'token_category_id' => $tokenCategory
                    ]);
                }
            }
        }

        $plans = [];
        $catgories = '';
        if (count($token_ids) > 0) {
            $token = Token::find($token_ids[0]);
            foreach ($token->plans as $plan) {
                array_push($plans, $plan->plan->package->name);
            }
            $catgories = implode(' || ', $token->category->pluck('category')->pluck('name')->toArray());
            $plans = implode(' || ', $plans);
        }

        /** If deal ID exists push data to bitrix */
        if ($request->input('deal_id') && config('constants.bitrix')) {
            $bitrix = new BitrixService();
            $bitrixResponse  = $bitrix->updateDeal(
                $request->input('deal_id'),
                '',
                array(
                    "plans" => $plans,
                    "category" => $catgories,
                    "token" => route('downloadHappimyndToken', [base64_encode('organization_id') => base64_encode($request->input('organization_id'))]),
                    "thriveCode" => route('downloadThriveCode', [base64_encode('organization_id') => base64_encode($request->input('organization_id'))]),
                )
            );
        }

        $organization_name = $tokens[0]->organization->name;
        $package = Plan::with('duration')->whereIn('id', $request->input('plans'))->first();
        // $plans = Plan::with('duration', 'offer', 'package')->get();
        $packages = Package::with('plan')->where('bundle', 0)->get();
        $tokenCategory = TokenCategory::all();
        $orientationMailText = DataGroup::where('name', 'email-orientation')->first()->content[0]->content;
        return view('Backend/generateToken')
            ->with('tokens', $tokens)
            ->with('organizations', $organizations)
            ->with('token_count', $request->input('token_count'))
            ->with('organization_id', $request->input('organization_id'))
            ->with('packages', $packages)
            ->with('tokenCategory', $tokenCategory)
            ->with('organization_name', $organization_name)
            ->with('selected_package', $package)
            ->with('orientationMailText', $orientationMailText)
            ->with('thriveCodes', $thriveCodes);
    }

    public function tokenList(Request $request)
    {
        $organizations = Organization::AvaliableOrganization()->get();
        if ($request->has('organization_id')) {

            // $start_date = $request->start_date;
            // $end_date = $request->end_date;
            // $tokens = Token::whereBetween('created_at', [$start_date, $end_date])->where('organization_id', $request->organization_id)
            //     ->with('organization', 'tokenMetaData', 'plans', 'userToken.user');

            $tokens = Token::where('organization_id', $request->organization_id)
                ->with('organization', 'tokenMetaData', 'plans', 'userToken.user');

            if ($request->input('token_status') == 'active') {
                $tokens->ValidTokens();
            } elseif ($request->input('token_status') == 'expired') {
                $tokens->ExpiredTokens();
            } elseif ($request->input('token_status') == 'disabled') {
                $tokens->DisabledTokens();
            } elseif ($request->input('token_status') == 'all') {
                //do nothing
            }
            
            // Add pagination to prevent memory issues with large datasets
            $perPage = $request->get('per_page', 50);
            $tokens = $tokens->with('tokenMetaData')
                ->paginate($perPage)
                ->appends($request->except('page'));
                
            $metaData = TokenMetaData::where('organization_id', $request->organization_id)->get();
            $happiAppCount = 0;
            foreach ($metaData as $data) {
                $happiAppCount += $data->meta_data['HappiAPP'];
            }
            session()->flashInput($request->input());
            return view('Backend/tokenList')
                ->with('tokens', $tokens)
                ->with('happiAppCount', $happiAppCount)
                ->with('organizations', $organizations);
        }
        return view('Backend/tokenList')
            ->with('organizations', $organizations);
    }

    public function thriveCodeList(Request $request)
    {
        $organizations = Organization::all();
        $happimynd = Organization::Happimynd()->first();
        if ($request->has('organization_id')) {
            $thriveCodes = ThriveCode::where('organization_id', $request->organization_id)
                ->with('organization')->with('user');

            if ($request->input('token_status') == 'active') {
                $thriveCodes->ValidTokens();
            } elseif ($request->input('token_status') == 'expired') {
                $thriveCodes->ExpiredTokens();
            } elseif ($request->input('token_status') == 'disabled') {
                $thriveCodes->DisabledTokens();
            } elseif ($request->input('token_status') == 'all') {
                //do nothing
            }
            $thriveCodes = $thriveCodes->get();
            session()->flashInput($request->input());
            return view('Backend/thriveCodeList')
                ->with('thriveCodes', $thriveCodes)
                ->with('organizations', $organizations)
                ->with('happimynd', $happimynd);
        } else if ($request->file('thrive_file') && $request->file('thrive_file')->isValid() && $request->has('happimynd')) {
            $tokenService = new TokenService;
            $thriveCodes = $tokenService->generateThriveTokenForOrganization($request->input('happimynd'), $request->thrive_file);
            session()->flashInput($request->input());
            if ($thriveCodes) {
                return view('Backend/thriveCodeList')
                    ->with('thriveCodes', $thriveCodes)
                    ->with('organizations', $organizations)
                    ->with('happimynd', $happimynd);
            }
        }
        return view('Backend/thriveCodeList')
            ->with('organizations', $organizations)
            ->with('happimynd', $happimynd);
    }

    public function OrganizationView(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        $organizations = Organization::orderBy('name' , 'asc')->with('token.plans.plan.package')->withCount([
            'token',
            'token as active_token_count' => function ($query) {
                $query->ValidTokens();
            },
            'token as disabled_token_count' => function ($query) {
                $query->DisabledTokens();
            },
            'token as used_token_count' => function ($query) {
                $query->ExpiredTokens();
            },
        ])->with('thriveCode')->withCount([
            'thriveCode',
            'thriveCode as active_thriveCode_count' => function ($query) {
                $query->ValidTokens();
            },
            'thriveCode as disabled_thriveCode_count' => function ($query) {
                $query->DisabledTokens();
            },
            'thriveCode as used_thriveCode_count' => function ($query) {
                $query->ExpiredTokens();
            },
        ])->paginate($perPage);
        return view('Backend/addOrganization')->with('organizations', $organizations);
    }

    public function OrganizationDetail(Request $request)
    {
        $organizations = Organization::orderBy('name' , 'asc')->get();
        $detailedOrganization = null;
        $tokens = null;
        $thriveCodes = null;
        
        if ($request->input('organization_id')) {
            $detailedOrganization = Organization::where('id', $request->input('organization_id'))->first();
        } else {
            $detailedOrganization = Organization::orderBy('name' , 'asc')->first();
        }
        
        if ($detailedOrganization) {
            // Load tokens with pagination to avoid memory issues
            $perPage = $request->get('per_page', 50);
            $tokens = Token::where('organization_id', $detailedOrganization->id)
                ->TokenUsed()
                ->with(['userToken.user:id,username,email', 'tokenMetaData:id,organization_id,meta_data', 'plans.plan.package:id,name'])
                ->paginate($perPage)
                ->appends(['organization_id' => $detailedOrganization->id]);
            
            // Load thrive codes with pagination to avoid memory issues
            $thriveCodes = ThriveCode::where('organization_id', $detailedOrganization->id)
                ->ThriveUsed()
                ->with(['user:id,username,email'])
                ->paginate($perPage)
                ->appends(['organization_id' => $detailedOrganization->id]);
        }
        
        return view('Backend/organizationDetail')
            ->with('organizations', $organizations)
            ->with('detailedOrganization', $detailedOrganization)
            ->with('tokens', $tokens)
            ->with('thriveCodes', $thriveCodes);
    }

    public function addOrganization(AddOrganizationRequest $request)
    {
        $name = $request->input('name');
        Organization::create(['name' => $name]);
        return redirect(route('admin.OrganizationView'))->with('success', 'Organization Added!');
    }

    public function deleteOrganization($organization_id)
    {
        if ($organization_id) {
            Organization::where('id', $organization_id)->delete();
            return redirect(route('admin.OrganizationView'))->with('success', 'Organization Deleted!');
        }
    }

    //TODO: expireTokens, reactivateTokens, expireToken, reactivateToken =>merge into one

    public function expireTokens($organization_id, $type)
    {
        if ($type == 'token') {
            Token::where('organization_id', $organization_id)->RevokeValidTokens();
        } else if ($type == 'thriveCode') {
            ThriveCode::where('organization_id', $organization_id)->RevokeValidTokens();
        }
        return redirect(route('admin.OrganizationView'));
    }

    public function reactivateTokens($organization_id, $type)
    {
        if ($type == 'token') {
            Token::where('organization_id', $organization_id)->ActivateDisabledTokens();
        } else if ($type == 'thriveCode') {
            ThriveCode::where('organization_id', $organization_id)->ActivateDisabledTokens();
        }
        return redirect(route('admin.OrganizationView'));
    }

    public function expireToken($id, $type)
    {
        if ($type == 'token') {
            Token::where('id', $id)->DeactivateToken();
        } else if ($type == 'thriveCode') {
            ThriveCode::where('id', $id)->DeactivateToken();
        }
        return redirect()->back();
    }

    public function reactivateToken($id, $type)
    {
        if ($type == 'token') {
            Token::where('id', $id)->ActivateToken();
        } else if ($type == 'thriveCode') {
            ThriveCode::where('id', $id)->ActivateToken();
        }
        return redirect()->back();
    }

    public function termsServices(Request $request)
    {
        $termServicesContent = DataGroup::with('content')->where('name', 'terms-and-services')->first();
        return view('Backend/terms-and-services')->with('termServicesContents', $termServicesContent);
    }

    public function saveStaticContent(Request $request)
    {
        $dataConentId = $request->input('id');
        if ($request->input('id') == 'new') {
            if (!is_null($request->input('title')) && !is_null($request->input('content'))) {
                DataContent::create([
                    'title' => $request->input('title'),
                    'content' => $request->input('content'),
                    'data_group_id' => DataGroup::where('name', 'terms-and-services')->first()->id,
                ]);
            }
        } else {
            $dataContent = DataContent::find($dataConentId);
            if (is_null($request->input('content')) || is_null($request->input('title'))) {
                $dataContent->delete();
            } else {
                $dataContent->content = $request->input('content');
                $dataContent->title = $request->input('title');
                $dataContent->save();
            }
        }
        return redirect()->route('admin.staticData.termServices');
    }

    public function sendNotificationToUserView(Request $request)
    {
        $userId = $request->input('user_id');
        return view('Backend.sendNotification')->with('user_id', $userId);
    }

    public function notifyUserView(Request $request)
    {
        $perPage = $request->get('per_page', 50);
        $users = User::orderBy('created_at')->latest()->paginate($perPage)
            ->appends($request->except('page'));
        return view('Backend.notifyUserView')->with('users', $users);
    }

    public function deleteStaticContent(Request $request)
    {
        $dataConentId = $request->input('id');
        $dataContent = DataContent::find($dataConentId);
        if ($dataContent) {
            $dataContent->delete();
            return $this->apiResponseService->success('true');
        }
        return $this->apiResponseService->error('doesn\'t exists');
    }

    public function dashboardPictureUploadView(Request $request)
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
        return view('Backend/dashboardPictureUpload')->with('dashboardPic', $dashboardPic)->with('hyperlink', $hyperlink);
    }

    public function uploadDashboardCoverPic(Request $request)
    {
        $responseData = [];
        $dataGroup = DataGroup::with('content')->where('name', 'dashboard')->first();
        $dataGroupId = $dataGroup->id;
        if ($request->hasFile('cover_pic')) {
            $fileService = new FileService;
            $filename = $fileService->saveAsAsset('dashboard', 'cover_pic');
            foreach ($dataGroup->content as $content) {
                if (!empty($content->content)) {
                    $fileService->deleteAssetFile('dashboard', $content->content);
                }
            }
            DataContent::updateOrCreate(
                [
                    'data_group_id' => $dataGroupId,
                    'title' => 'dashboard_cover_pic'
                ],
                [
                    'content' => $filename,
                ]
            );
            $responseData['cover_pic'] = Storage::url(config('constants.mediaAssets.dashboard.folderName') . $filename);
        }
        DataContent::updateOrCreate(
            [
                'data_group_id' => $dataGroupId,
                'title' => 'hyperlink',
            ],
            [
                'content' => $request->input('hyperlink')
            ]
        );
        $responseData['hyperlink'] = $request->input('hyperlink');
        return $this->apiResponseService->success($responseData);
    }

    function landingPageVideoUploadView(Request $request)
    {
        $dataContents = DataGroup::with('content')->where('name', 'landing_page')->first()->content()->get();
        $landingPageVideo = '';
        $landingPageVideoThumbnail = '';
        foreach ($dataContents as $content) {
            if ($content->title == 'landing_page_video') {
                $landingPageVideo = $content->getContentWithS3Url('landing_page');
            }
            if ($content->title == 'landing_page_video_thumbnail') {
                $landingPageVideoThumbnail = $content->getContentWithS3Url('landing_page');
            }
        }
        return view('Backend/landingPageVideoUpload')->with('landingPageVideo', $landingPageVideo)->with('landingPageVideoThumbnail', $landingPageVideoThumbnail);
    }

    public function uploadLandingPageVideo(Request $request)
    {
        $fileService = new FileService;
        $responseData = [];
        $dataGroup = DataGroup::with('content')->where('name', 'landing_page')->first();
        $dataGroupId = $dataGroup->id;
        //delete existing files
        foreach ($dataGroup->content as $content) {
            if ($request->hasFile('video')) {
                if ($content->title == 'landing_page_video' && !empty($content->content)) {
                    $fileService->deleteAssetFile('landing_page', $content->content);
                }
            }
            if ($request->hasFile('thumbnail')) {
                if ($content->title == 'landing_page_video_thumbnail' && !empty($content->content)) {
                    $fileService->deleteAssetFile('thumbnail', $content->content);
                }
            }
        }
        //save new files
        if ($request->hasFile('video')) {
            $fileName = $fileService->saveAsAsset('landing_page', 'video');
            DataContent::updateOrCreate(
                [
                    'data_group_id' => $dataGroupId,
                    'title' => 'landing_page_video'
                ],
                [
                    'content' => $fileName,
                ]
            );
            $responseData['video_link'] = Storage::url(config('constants.mediaAssets.landing_page.folderName') . $fileName);
        }
        if ($request->hasFile('thumbnail')) {
            $fileName = $fileService->saveAsAsset('landing_page', 'thumbnail');
            DataContent::updateOrCreate(
                [
                    'data_group_id' => $dataGroupId,
                    'title' => 'landing_page_video_thumbnail'
                ],
                [
                    'content' => $fileName,
                ]
            );
            $responseData['thumbnail_link'] = Storage::url(config('constants.mediaAssets.landing_page.folderName') . $fileName);
        }
        return $this->apiResponseService->success($responseData);
    }

    public function raisedQuery(Request $request)
    {
        $raisedQueries = RaiseQuery::with(['user:id,username,email'])->orderBy('status')->latest()->get();
        $openQueryCount = RaiseQuery::where('status', 0)->count();
        $closedQueryCount = $raisedQueries->count() - $openQueryCount;
        return view('Backend/raisedQuery')
            ->with('raisedQueries', $raisedQueries)
            ->with('openQueryCount', $openQueryCount)
            ->with('closedQueryCount', $closedQueryCount);
    }


    public function createBundle(Request $request)
    {
        
        if($request->isMethod('GET')){
            $single_plans = Package::where('bundle' , '!=' , 1)->where('deleted_at' , null)->where('name' , '!=' , 'happiTALK')->where('name' , '!=' , 'happiGUIDE')->with('plan')->get();

            return view('Backend/create_bundle')
            ->with('single_plans', $single_plans);
        }

        if($request->isMethod('POST')){

            //Create package
            $package_data = [
                'name' => $request->name,
                'description' => $request->description,
                'bundle' => 1,
            ];
            $create_package = Package::create($package_data);

            
            //Map plan with package
            $plan_ids = $request->plans;
            foreach($plan_ids as $plan) {
                $data = [
                    'package_id' => $create_package->id,
                    'plan_id' => $plan,

                ];
                DynamicBundlePlan::create($data);   
            }


            //Create package plan
            $duration = DurationType::where('type' , 1)->first();
            $plan_data = [
                'package_id' => $create_package->id,
                'duration_type_id' => $duration->id,
                'price' => $request->price,
                'active' => 1,
            ];
            $create_plan = Plan::create($plan_data);


            //Create offer
            $offer_data = [
                'name' => 'starting',
                'discount' => $request->discount_percentage,
                'price' => $request->discounted_price,
                'valid' => 1,
                'plan_id' => $create_plan->id,
            ];
            $create_offer = Offer::create($offer_data);

            return redirect('admin/bundle-detail')->with('success' , 'Bundle created Successfully.');

        }
        
    }



    public function bundleDetail(Request $request)
    {
        $packages = Plan::with('duration')->with(['offer' => function ($query) {
            $query->where('valid', true)->orderBy('created_at', 'desc');
        }])->get();
        return view('Backend/bundle')
            ->with('packages', $packages);
    }

    public function updateBundlePrice(Request $request)
    {
        $id = $request->id;
        $price = $request->regular_price;
        $discount = $request->discount;
        $inoffer_price = $request->inoffer_price;
        $plan = Plan::where('id', $id)->update(['price' => $price]);
        $offer = Offer::where('plan_id', $id)->update(['discount' => $discount, 'price' => $inoffer_price]);
        if ($plan == 1 && $offer == 1) {
            $notifyMsg = 'Price Updated Successfully';
            $responseMsg = 'Price for the selected bundle has been updated successfully';
            return $this->apiResponseService->successNotify($notifyMsg, $responseMsg);
        } else if ($plan == 0 || offer == 0) {
            return $this->apiResponseService->contactDeveloperError();
        }
    }

    public function manageBundle(Request $request)
    {
        $bundleStatus = BundleStatus::with('user')->get();
        return view('Backend/manageBundle')
            ->with('bundleStatus', $bundleStatus);
    }

    public function assesmentList(Request $request)
    {
        $query = $request->get('query');
        $perPage = $request->get('per_page', 10);
        ini_set('memory_limit', '4096M');

        if($query){
            $user_ids = User::where('username' , $query)->orWhere('nickname' ,$query )->pluck('id');

            $assessments = Assessment::whereIn('user_id' , $user_ids)->with(['user', 'score', 'batch.batchCategory' => function ($query) {
                $query->withCount('questions');
            }])->latest()->paginate($perPage);
        }else{
           $assessments = Assessment::with(['user', 'score', 'batch.batchCategory' => function ($query) {
                $query->withCount('questions');
            }])->latest()->paginate($perPage); 
        }
        

        $assessments->appends($request->except('page'));

        $organizations = Organization::orderBy('name')->withCount(['token as assessmentCount' => function ($query) {
            $query->whereHas('userToken.user.assessment');
        }])->get();
        $b2cAssessmentCount = count($assessments) - $organizations->sum('assessmentCount');
        foreach ($assessments as $assessment) {
            if ($assessment->completedAssessment()) {
                if (!$assessment->score) {
                    $assessmentService = new AssessmentService();
                    $assessmentService->forAssessment($assessment->id);
                    // dd($assessmentService);
                    // $assessment->score = $assessmentService->createOrUpdateScore($assessment, AssessmentAnswer::where('assessment_id', $assessment->id)->count());
                }
            }
            if ($assessment->user->userToken) {
                $token = Token::where('id', $assessment->user->userToken->token->id)
                    ->with('organization')
                    ->first();
                $assessment->token = $token->token;
                if ($token->organization)
                    $assessment->organization = $token->organization->name;
            }
        }
        return view('Backend/assessmentList')
            ->with('organizations', $organizations)
            ->with('b2cAssessmentCount', $b2cAssessmentCount)
            ->with('query', $query)
            ->with('assessments', $assessments);
    }



    public function assesmentListByUsername(Request $request)
    {
        if($request->isMethod('GET')){
            return view('Backend/assessmentListByUsername');
        }
        if($request->isMethod('POST')){
             
            return redirect('admin/assesmentList?query='.$request->username);
        }
    }


    public function deleteAssessment($assessment_id)
    {
        $assessment_id = base64_decode($assessment_id);
        if ($assessment_id) {
            Assessment::where('id', $assessment_id)->forceDelete();
            return redirect(route('admin.assesmentList'))->with('success', 'Assessment Deleted!');
        }
    }

    public function assesmentApprove($assessment_id, $status, Request $request)
    {
        $assessment_id = base64_decode($assessment_id);
        if ($status == 0 || $status == 1) {
            $status = ($status == "0") ? false : true;
            $assessment = Assessment::find($assessment_id);
            if ($assessment) {
                $assessment->approve()->updateOrCreate(
                    ['assessment_id' => $assessment->id],
                    ['status' => $status]
                );
                $request->session()->flash('Successfully', 'Assessment Report status updated.!');
                return redirect(route('admin.assesmentList'));
            }
        }
        $request->session()->flash('warning', 'Something went wrong.!');
        return redirect(route('admin.assesmentList'));
    }

    public function addUnavailableDates(Request $request)
    {
        $slotBooked = AppointmentService::getBookedAppointmentDates();
        $slotsBooked = [];
        foreach ($slotBooked as $date => $slots) {
            array_push($slotsBooked, ['date' => $date, 'slots' => implode(' || ', $slots)]);
        }
        $slotBooked = json_encode($slotBooked);

        return view('Backend.addUnavailableDate')->with('slotBooked', $slotBooked)->with('slotsBooked', $slotsBooked);
    }

    public function postAddUnavailableDates(Request $request)
    {
        if ((null != $request->input('date')) && null != ($request->input('slots'))) {
            $date = $request->input('date');
            foreach ($request->input('slots') as $slot) {
                if (Availability::where('date', '=', $date)->where('time', '=', $slot)->count() == 0)
                    Availability::create(['date' => $date, 'time' => $slot]);
            }
        }
        $slotBooked = AppointmentService::getBookedAppointmentDates();
        $slotsBooked = [];
        foreach ($slotBooked as $date => $slots) {
            array_push($slotsBooked, ['date' => $date, 'slots' => implode(' || ', $slots)]);
        }
        // dd($slotsBooked);
        $slotBooked = json_encode($slotBooked);
        return view('Backend.addUnavailableDate')->with('slotBooked', $slotBooked)->with('slotsBooked', $slotsBooked);
    }

    function landingPageBitrixFormView(Request $request)
    {
        $dataContents = DataGroup::with(['content' => function ($query) {
            $query->whereIn('title', ['cdnlink', 'happySpace_cdnlink'],);
        }])->where('name', 'landing_page')->first();
        $cdnLink = '';
        $happySpace_cdnlink = '';
        if (count($dataContents->content) >= 1) {

            foreach ($dataContents->content as $content) {
                if ($content->title == 'cdnlink') {
                    $cdnLink = $content->content;
                }
                if ($content->title == 'happySpace_cdnlink') {
                    $happySpace_cdnlink = $content->content;
                }
            }
        }
        return view('Backend/landingPageBtrixForm')->with('cdnlink', $cdnLink)->with('happySpace_cdnlink', $happySpace_cdnlink);
    }

    function saveLandingPageBitrixFormView(AdminBitrixRequest $request)
    {
        $validatedData = $request->validated();

        $response = DataContent::updateOrCreate(
            [
                'title' => 'cdnlink'
            ],
            [
                'content' => $validatedData['cdnlink'],
                'data_group_id' => DataGroup::where('name', 'landing_page')->first()->id,
            ]
        );

        $cdnLink = $response->content;

        return redirect(route('admin.staticData.landingPageBitrixFormView'));
    }
    function saveHappySpaceBitrixForm(HappySpaceBitrixRequest $request)
    {
        $validatedData = $request->validated();

        $response = DataContent::updateOrCreate(
            [
                'title' => 'happySpace_cdnlink'
            ],
            [
                'content' => $validatedData['cdnlink_happyspace'],
                'data_group_id' => DataGroup::where('name', 'landing_page')->first()->id,
            ]
        );

        $responseData['happySpace_cdnlink'] = $response->content;

        return $this->apiResponseService->success($responseData);
    }

    function dashboardAppDownloadView(Request $request)
    {

        $dataContents = DataGroup::with(['content' => function ($query) {
            $query->whereIn(
                'title',
                ['android_hyperlink', 'ios_hyperlink'],
            );
        }])->where('name', 'dashboard')->first();
        $androidLink = '';
        $iosLink = '';
        if (count($dataContents->content) >= 1) {

            foreach ($dataContents->content as $content) {
                if ($content->title == 'android_hyperlink') {
                    $androidLink = $content->content;
                }
                if ($content->title == 'ios_hyperlink') {
                    $iosLink = $content->content;
                }
            }
        }

        return view('Backend/dashboardAppDownloadForm')->with('androidLink', $androidLink)->with('iosLink', $iosLink);
    }

    function dashboardAppDownload(AndroidRequest $request)
    {
        $validatedData = $request->validated();

        $response = DataContent::updateOrCreate(
            [
                'title' => 'android_hyperlink'
            ],
            [
                'content' => $validatedData['androidLink'],
                'data_group_id' => DataGroup::where('name', 'dashboard')->first()->id,
            ]
        );

        $androidLink = $response->content;

        return redirect(route('admin.staticData.dashboardAppDownloadView'));
    }
    function saveDashboardIosLink(IosRequest $request)
    {
        $validatedData = $request->validated();

        $response = DataContent::updateOrCreate(
            [
                'title' => 'ios_hyperlink'
            ],
            [
                'content' => $validatedData['iosLink'],
                'data_group_id' => DataGroup::where('name', 'dashboard')->first()->id,
            ]
        );

        $responseData['ios_hyperlink'] = $response->content;

        return $this->apiResponseService->success($responseData);
    }

    public function organization()
    {
        $data = OrganizationPageData::all();
        $logos=OrganizationLogo::all();
        $organisation_buttons = EditButton::where('page_name', 'organisation')->get();
        return view('Backend/organization')->with('data', $data)->with('logos', $logos)->with('organisation_buttons', $organisation_buttons);
    }

    public function saveOrganization(Request $request)
    {
        $fileService = new FileService;
        $responseData = [];
        $fileNameImage = '';
        $file_type = 1;
        if ($request->hasFile('image') && isset($request->image)) {
            $image = OrganizationPageData::where('id', $request->id)->get('image_link');
            $fileService->deleteAssetFile('teams', $image[0]['image_link']);
            $fileNameImage = $fileService->saveAsAsset('org', 'image');
        }
        $data=[
            'title'=>$request->title,
            'description'=>$request->description,
        ];
        if(isset($request->image)){
            $data['image_link']=$fileNameImage;
        }
        $org = OrganizationPageData::where('id', $request->id)->update(
            $data
        );
        return redirect(route('admin.staticData.organization'))->with('status',' Updated sucessfully');

    }

    public function saveOrganizationLogo(Request $request){
        $fileService = new FileService;
        $responseData = [];
        $fileNameImage = '';
        $file_type = 1;
        if ($request->hasFile('image') && isset($request->image)){
            $image = OrganizationLogo::where('id', $request->id)->get('image_link');
            $fileService->deleteAssetFile('org', $image[0]['image_link']);
            $fileNameImage = $fileService->saveAsAsset('org', 'image');
        }
        $data=[
            'image_link'=> $fileNameImage
        ];
        $org = OrganizationLogo::where('id', $request->id)->update(
            $data
        );
        return redirect(route('admin.staticData.organization'))->with('status',' Updated sucessfully');
    }

    public function quotes()
    {
        $quotes=Quotes::all();
        $button_contents=EditButton::where('page_name','quotes')->first();
        return view('Backend/quotes')->with('quotes',$quotes[0])->with('button_contents',$button_contents);
    }

    public function saveQuotes(Request $request){
        $fileService = new FileService;
        $responseData = [];
        $fileNameImage = '';
        $file_type = 1;
        if ($request->hasFile('image') && isset($request->image)) {
             $image=Quotes::where('id',$request->id)->get('image_link');
            $fileService->deleteAssetFile('quotes', $image[0]['image_link']);
            $fileNameImage = $fileService->saveAsAsset('quotes', 'image');
        }
        $data=[
            'quote'=>$request->quote,
            'author'=>$request->author??''
        ];
        if (isset($request->image)) {
            $data['image_link'] = $fileNameImage;
        }
        $quotes = Quotes::where('id', $request->id)->update(
            $data
        );
        return redirect(route('admin.staticData.quotes'))->with('status',' Quote updated sucessfully');
        
    }

    public function saveEditableQuoteButton(Request $request)
    {
        $button_contents=EditButton::where('id',$request->id)->update(['button_content'=>$request->button_content]);
        return redirect(route('admin.staticData.quotes'))->with('status', ' Updated Successfully');

    }
   
    public function ourteam()
    {
        $founders = OurTeam::where('category', 'founders')->orderBy('preference')->get();
        $experts = OurTeam::where('category', 'experts')->orderBy('preference')->get();
        $psychologists = OurTeam::where('category', 'psychologists')->orderBy('preference')->get();

        return view('Backend/ourteam')->with('founders', $founders)->with('experts', $experts)->with('psychologists', $psychologists);
    }

    public function saveOurTeam(Request $request)
    {
        $fileService = new FileService;
        $responseData = [];
        $fileNameImage = '';
        $file_type = 1;
        if ($request->hasFile('image')) {
            if (isset($request->id)) {
                $image = OurTeam::where('id', $request->id)->get('image_link');
                // $fileService->deleteAssetFile('teams', $image['image_link']);
                $fileService->deleteAssetFile('teams', $image['0']['image_link']);                
            }
            $fileNameImage = $fileService->saveAsAsset('teams', 'image');
        }
        $team = OurTeam::where('category', $request->category)->get();
        $count = $team->count();
        $data = [
            'name' => $request->name,
            'designation' => $request->designation,
            'description' => $request->description,
            'category' => $request->category,
            'preference' => $count + 1
        ];

        if (isset($request->linkedin)) {
            $data['linkedin'] = $request->linkedin;
        }
        if ($fileNameImage != '') {
            $data['image_link'] = $fileNameImage;
        }
        if (isset($request->id)) {
            $post = OurTeam::where('id', $request->id)->update(
                $data
            );
            return redirect(route('admin.staticData.ourteam'))->with('status', $request->name . ' updated successfully');
        } else {
            $data['image_link'] = $fileNameImage;
            $post = OurTeam::Create(
                $data
            );
            return redirect(route('admin.staticData.ourteam'))->with('status', $request->name . ' added sucessfully to Our Team');
        }
    }

    public function updateOurTeamPriority(Request $request)
    {
        $datas = $request['data'];
        foreach ($datas as $id => $preference) {
            $updated = OurTeam::where('id', $id)->update(['preference' => $preference]);
        }
        if ($updated == 1) {
            $notifyMsg = 'Preference Updated Successfully';
            $responseMsg = 'Preference for OurTeam has been updated successfully';
            return $this->apiResponseService->successNotify($notifyMsg, $responseMsg);
        } else {
            return $this->apiResponseService->error([
                'notify' => [
                    'type' => 'error',
                    'message' => 'Preference Cannot be Updated, Please try again'
                ]
            ]);
        }
    }

    public function updateOurClientPriority(Request $request)
    {
        $data = $request['data'];
        foreach ($data as $id => $preference) {
            $updated = OurClient::where('id', $id)->update(['preference' => $preference]);
        }
        if ($updated == 1) {
            $notifyMsg = 'Preference Updated Successfully';
            $responseMsg = 'Preference for OurTeam has been updated successfully';
            return $this->apiResponseService->successNotify($notifyMsg, $responseMsg);
        } else {
            return $this->apiResponseService->error([
                'notify' => [
                    'type' => 'error',
                    'message' => 'Preference Cannot be Updated, Please try again'
                ]
            ]);
        }
    }

    public function editOurteam($id)
    {
        $founders = OurTeam::where('category', 'founders')->orderBy('preference')->get();
        $experts = OurTeam::where('category', 'experts')->orderBy('preference')->get();
        $psychologists = OurTeam::where('category', 'psychologists')->orderBy('preference')->get();
        $member = OurTeam::where('id', $id)->get()->first();

        return view('Backend/ourteam')->with('founders', $founders)->with('experts', $experts)->with('psychologists', $psychologists)->with('member', $member);
    }

    public function deleteOurteam($id)
    {
        OurTeam::where('id', $id)->first()->delete();
        return redirect(route('admin.staticData.ourteam'))->with('status', ' Deleted Successfully');
    }

    public function blog(Request $request)
    {
        // $posts = Post::latest()->get();
        $perPage = $request->get('per_page', 10);
        $posts = Post::latest()->paginate($perPage)
            ->appends($request->except('page'));

        // $users = User::all();
        // dd($users);
        return view('Backend/blog')->with('posts', $posts);
    }
    public function editBlog($slug)
    {
        $post = Post::where('slug', $slug)->first();
        // dd($users);
        return view('Backend/blogEdit')->with('post', $post);
    }

    public function saveBlog(AdminBlogRequest $request)
    {
        // $validatedData = $request->validated();
        $fileService = new FileService;
        $responseData = [];
        $fileNameThumbNail = '';
        $fileNameMedia = '';
        $file_type = 1;
        $thumbnailSize = Constants::THUMBNAILSiZE['WIDTH'] . ' X ' . Constants::THUMBNAILSiZE['HEIGHT'];
        $size = Constants::THUMBNAILSiZE['SIZE'] / 1000 . "KB";
        if ($request->hasFile('thumbnail')) {
            if (!$fileService->checkSize('thumbnail', Constants::THUMBNAILSiZE['WIDTH'], Constants::THUMBNAILSiZE['HEIGHT'])) {
                return redirect()->back()->with('error', "Thumbnail width X height must be equal to $thumbnailSize ");
            }
            $fileService->deleteAssetFile('blog', 'thumbnail');
            $fileNameThumbNail = $fileService->saveAsAsset('blog', 'thumbnail');

            Storage::url(config('constants.mediaAssets.blog.folderName') . $fileNameThumbNail);
        }

        if ($request->hasFile('media')) {
            $fileService->deleteAssetFile('blog', 'media');
            $fileNameMedia = $fileService->saveAsAsset('blog', 'media');
            $fileTypeExt = $request->media->getMimeType();
            $file_types = PostCategory::all();
            $file_type = '';
            foreach ($file_types as $key => $values) {
                if (in_array($fileTypeExt, $values->file_type)) {
                    $file_type = $values->id;
                }
            }


            Storage::url(config('constants.mediaAssets.blog.folderName') . $fileNameMedia);
        }

        $post = POST::updateOrCreate(
            [
                'slug' => Str::slug($request['title']),
            ],
            [
                'title' => $request['title'],
                'slug' => Str::slug($request['title']),
                'description' => $request['content'],
                'thumbnail' => $fileNameThumbNail,
                'media' => $fileNameMedia,
                'publish_status' => $request['publish_status'],
                'restricted_content' => $request['accessibility'],
                'post_category_id' => $file_type,

            ]
        );

        if ($request['featured']) {
            Post::where('featured', 1)->update(['featured' => 0]);
            Post::where('id', $post->id)->update(['featured' => 1]);
        };

        return redirect(route('admin.staticData.blogFormView'))->with('status', 'Blog Posted Successfully');
    }
    public function deleteBlog(Request $request, $id)
    {
        $post = Post::destroy($id);
        return redirect(route('admin.staticData.blogFormView'))->with('status', 'Blog Deleted Successfully');
    }

    public function landingFaqView(Request $request)
    {
        $faqs = DataGroup::with('content')->where('name', 'faqs-general')->first();
        return view('Backend/faq')->with('faqs', $faqs);
    }

    public function landingFaqPost(Request $request)
    {
        $dataConentId = $request->input('id');
        if ($request->input('id') == 'new') {
            if (!is_null($request->input('title')) && !is_null($request->input('content'))) {
                DataContent::create([
                    'title' => $request->input('title'),
                    'content' => $request->input('content'),
                    'data_group_id' => DataGroup::where('name', 'faqs-general')->first()->id,
                ]);
            }
        } else {
            $dataContent = DataContent::find($dataConentId);
            if (is_null($request->input('content')) || is_null($request->input('title'))) {
                $dataContent->delete();
            } else {
                $dataContent->content = $request->input('content');
                $dataContent->title = $request->input('title');
                $dataContent->save();
            }
        }
        return redirect()->route('admin.staticData.landingFaqView');
    }

    public function faqOrganizationView(Request $request)
    {
        $faqs = DataGroup::with('content')->where('name', 'faqs-organization')->first();
        return view('Backend/faqOrganization')->with('faqs', $faqs);
    }

    public function faqOrganizationPost(Request $request)
    {
        $dataConentId = $request->input('id');
        if ($request->input('id') == 'new') {
            if (!is_null($request->input('title')) && !is_null($request->input('content'))) {
                DataContent::create([
                    'title' => $request->input('title'),
                    'content' => $request->input('content'),
                    'data_group_id' => DataGroup::where('name', 'faqs-organization')->first()->id,
                ]);
            }
        } else {
            $dataContent = DataContent::find($dataConentId);
            if (is_null($request->input('content')) || is_null($request->input('title'))) {
                $dataContent->delete();
            } else {
                $dataContent->content = $request->input('content');
                $dataContent->title = $request->input('title');
                $dataContent->save();
            }
        }
        return redirect()->route('admin.staticData.faqOrganizationView');
    }


    public function terms(Request $request)
    {
        $termServicesContent = DataGroup::with('content')->where('name', 'termsandservices')->first();
        return view('Backend/termsandservices')->with('termServicesContents', $termServicesContent);
    }

    public function saveTerms(Request $request)
    {
        $dataConentId = $request->input('id');
        if ($request->input('id') == 'new') {
            if (!is_null($request->input('title')) && !is_null($request->input('content'))) {
                DataContent::create([
                    'title' => $request->input('title'),
                    'content' => $request->input('content'),
                    'data_group_id' => DataGroup::where('name', 'termsandservices')->first()->id,
                ]);
            }
        } else {
            $dataContent = DataContent::find($dataConentId);
            if (is_null($request->input('content')) || is_null($request->input('title'))) {
                $dataContent->delete();
            } else {
                $dataContent->content = $request->input('content');
                $dataContent->title = $request->input('title');
                $dataContent->save();
            }
        }
        return redirect()->route('admin.staticData.terms');
    }

    public function exploreServices(Request $request)
    {
        $values = [
            'HappiAPP',
            'HappiTALK',
            'HappiSPACE',
            'HappiCHAT',
            'HappiLife',
        ];
        $data=ServiceImage::all();
        $packages=Package::all();
        $button_contents=EditButton::where('page_name','services')->get();
        $exploreServiceContent = DataGroup::with('content')->where('name', 'explore-services')->first();
        return view('Backend/explore_services')->with('exploreServiceContent', $exploreServiceContent)->with('values', $values)->with('button_contents',$button_contents)->with('data',$data)->with('packages',$packages);
    }

    public function savePackageName(Request $request){
        $data=[
            'name'=>$request->package_name,
        ];
        Package::where('id',$request->id)->update($data);
        return redirect()->route('admin.staticData.exploreServices');
    }

    public function saveExploreServices(Request $request)
    {
        $dataConentId = $request->input('id');
        if ($request->input('id') == 'new') {
            if (!is_null($request->input('title')) && !is_null($request->input('content'))) {
                DataContent::create([
                    'title' => $request->input('title'),
                    'content' => $request->input('content'),
                    'data_group_id' => DataGroup::where('name', 'explore-services')->first()->id,
                ]);
            }
        } else {
            $dataContent = DataContent::find($dataConentId);

            if (is_null($request->input('content')) || is_null($request->input('title'))) {
                $dataContent->delete();
            } else {
                $dataContent->content = $request->input('content');
                $dataContent->title = $request->input('title');
                $dataContent->save();
            }
        }
        $fileService = new FileService;
        $responseData = [];
        $fileNameImage = '';
        $file_type = 1;
        if ($request->hasFile('image') && isset($request->image)) {
            $image=ServiceImage::where('id',$request->id1)->get('image_link');
            $fileService->deleteAssetFile('services', $image[0]['image_link']);
            $fileNameImage = $fileService->saveAsAsset('services', 'image');
        }
        $data=[
            'title'=>$request->title1,
            'overview'=>$request->overview,
        ];
        if (isset($request->image)) {
            $data['image_link'] = $fileNameImage;
        }
        ServiceImage::where('id',$request->id1)->update($data);
        return redirect()->route('admin.staticData.exploreServices');
    }

    public function editLandingButtons(Request $request) 
    {
        $landing_buttons = EditButton::where('page_name', 'landing')->get();
        return view('Backend.edit_landing_buttons')->with('landing_buttons', $landing_buttons);
    }

    public function saveEditableServicesButton(Request $request)
    {
        $button_contents=EditButton::where('id',$request->id)->update(['button_content'=>$request->button_content]);
        return redirect(route('admin.staticData.exploreServices'))->with('status', ' Updated Successfully');

    }

    public function saveEditableLandingButton(Request $request)
    {
        EditButton::find($request->id)->update(['button_content' => $request->button_content]); 
        return redirect(route('admin.staticData.editLandingButtons'))->with('status', ' Updated Successfully');
    }

    public function saveEditableOrganisationButton(Request $request)
    {
        EditButton::find($request->id)->update(['button_content' => $request->button_content]); 
        return redirect(route('admin.staticData.organization'))->with('status', ' Updated Successfully');
    }

    public function addAvailableDates(Request $request)
    {
        $slotBooked = AppointmentService::getBookedAppointmentDates();
        $slotsBooked = [];
        foreach ($slotBooked as $date => $slots) {
            array_push($slotsBooked, ['date' => $date, 'slots' => implode(' || ', $slots)]);
        }
        $slotBooked = json_encode($slotBooked);

        return view('Backend.addAvailableDate')->with('slotBooked', $slotBooked)->with('slotsBooked', $slotsBooked);
    }

    public function postAddAvailableDates(AvailableDatesRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['date'] = Carbon::createFromFormat('m/d/Y', $validatedData['date'])->format('Y-m-d');
        $validatedData['from'] = date("H:i", strtotime($validatedData['from']));
        $validatedData['to'] = date("H:i", strtotime($validatedData['to']));
        $slot  = AvailableDate::firstOrCreate($validatedData);
        $slotBooked = AppointmentService::getAvailableAppointmentDates();

        $slotsBooked = [];
        foreach ($slotBooked as $date => $slots) {
            array_push($slotsBooked, ['date' => $date, 'slots' => implode(' || ', $slots)]);
        }

        $slotBooked = json_encode($slotBooked);
        return view('Backend.addAvailableDate')->with('slotBooked', $slotBooked)->with('slotsBooked', $slotsBooked)->with('consultant', $slot->consultant);
    }
    public function addAllAvailableDates(Request $request)
    {
        $slotBooked = AppointmentService::getAllAppointmentDates();
        $slotsBooked = [];
        foreach ($slotBooked as $date => $slots) {
            $date = Carbon::createFromFormat('Y-m-d', $date)->format('d-F-Y');
            array_push($slotsBooked, ['date' => $date, 'slots' => implode(' || ', $slots)]);
        }
        $slotBooked = json_encode($slotBooked);

        return view('Backend.addAllAvailableDate')->with('slotBooked', $slotBooked)->with('slotsBooked', $slotsBooked);
    }

    public function postAllAddAvailableDates(Request $request)
    {
        $validator = $request->validate([
            'date' => ['required'],
            'slots' => ['required'],
            'input-default' => ['required_with:slot-default'],
            'slot-1month' => ['required_with:months_no']

        ], [
            'input-default.required_with' => 'Enter number of consultants if you check all'
        ]);

        $inputArray = $request->input('input-slot');
        $results = array_filter($inputArray, function ($inputArray) {
            return $inputArray > 0;
        });
        $result = array_values($results);
        // dd($result);
        if ((null != $request->input('date')) && null != ($request->input('slots')) && !empty($result) && null == $request->input('slot-1month')) {
            $date = Carbon::createFromFormat('m/d/Y', $request->input('date'))->format('Y-m-d');
            $i = 0;
            foreach ($request->input('slots') as $slot) {
                AvailabilityDates::updateOrCreate(['date' => $date, 'time' => $slot], ['time' => $slot, 'consultant' => $result[$i]]);
                $i++;
            }
        }
        if ((null != $request->input('date')) && null != ($request->input('slots')) && empty($result) && null != ($request->input('input-default')) && null == $request->input('slot-1month')) {
            $date = Carbon::createFromFormat('m/d/Y', $request->input('date'))->format('Y-m-d');
            $consultant = $request->input('input-default');
            foreach ($request->input('slots') as $slot) {
                AvailabilityDates::updateOrCreate(['date' => $date, 'time' => $slot], ['time' => $slot, 'consultant' => $consultant]);
            }
        }

        if ((null != $request->input('date')) && null != ($request->input('slots')) && !empty($result) && null != ($request->input('slot-1month'))) {
            $date = Carbon::createFromFormat('m/d/Y', $request->input('date'));
            $endDate = Carbon::createFromFormat('m/d/Y', $request->input('date'));;
            $finalDate = $endDate->addMonths($request->months_no);
            for ($i = $date; $i <= $finalDate; $i->modify('+1 day')) {
                $j = 0;
                foreach ($request->input('slots') as $slot) {
                    AvailabilityDates::updateOrCreate(['date' => $i->format('Y-m-d'), 'time' => $slot], ['time' => $slot, 'consultant' => $result[$j]]);
                    $j++;
                }
            }
        }

        if ((null != $request->input('date')) && null != ($request->input('slots')) && empty($result) && null != ($request->input('input-default')) && null != ($request->input('slot-1month'))) {
            $date = Carbon::createFromFormat('m/d/Y', $request->input('date'));
            $endDate = Carbon::createFromFormat('m/d/Y', $request->input('date'));;
            $finalDate = $endDate->addMonths($request->months_no);
            $consultant = $request->input('input-default');
            for ($i = $date; $i <= $finalDate; $i->modify('+1 day')) {
                foreach ($request->input('slots') as $slot) {
                    AvailabilityDates::updateOrCreate(['date' => $i->format('Y-m-d'), 'time' => $slot], ['time' => $slot, 'consultant' => $consultant]);
                }
            }
        }

        $slotBooked = AppointmentService::getAllAppointmentDates();
        $slotsBooked = [];
        foreach ($slotBooked as $date => $slots) {
            array_push($slotsBooked, ['date' => $date, 'slots' => implode(' || ', $slots)]);
        }

        $slotBooked = json_encode($slotBooked);
        return redirect()->back()->with('slotBooked', $slotBooked)->with('slotsBooked', $slotsBooked)->with('status', 'Slot saved successfully');
    }

    public function deleteAvailableDates($date)
    {
        $dateConvert = Carbon::createFromFormat('d-M-Y', $date)->format('Y-m-d');
        $dateModel = AvailabilityDates::where('date', $dateConvert)->delete();
        return redirect()->route('admin.addAllAvailableDates.get');
    }

    public function showAllBookings()
    {
        $slots = AssessmentApprove::where('status', 0)->where('available_date', '!=', null)->with('assessment', function ($query) {
            $query->with('user');
        })->orderBy('available_date', 'asc')->get();

        // getting all the future appointments already booked by customers
        $slotsBooked = $slots->filter(function ($value, $key) {
            return $value->available_date >= Carbon::now()->format('Y-m-d');
        });

        //getting all the past booking appointments made my customers
        $slotsBookedPast = $slots->filter(function ($value, $key) {
            return $value->available_date < Carbon::now()->format('Y-m-d');
        });

        return view('Backend.allBookedDate')->with('slotsBooked', $slotsBooked)->with('slotsBookedPast', $slotsBookedPast);
    }


    public function showAllBookingsPost(Request $request)
    {

        $validator = $request->validate([
            'start_date' => 'required',
            'end_date' => ['required', 'date', 'after_or_equal:start_date']
        ]);

        $from = Carbon::createFromFormat('m/d/Y', $request->input('start_date'))->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->input('end_date'))->format('Y-m-d');
        $slotsBooked = AssessmentApprove::whereBetween('available_date', [$from, $to])->with('assessment', function ($query) {
            $query->with('user');
        })
            ->orderBy('available_date', 'asc')->get();

        return view('Backend.allBookedDate')->with('slotsBooked', $slotsBooked)->with('start_date', $request->start_date);
    }

    public function updateDateFormat()
    {

        try {
            $ass = AssessmentApprove::where('available_date', '!=', null)->get();
            foreach ($ass as $a) {
                $final = explode('/', $a->available_date);
                if (checkDate($final[0], $final[1], $final[2])) {
                    $from = Carbon::createFromFormat('m/d/Y', $a->available_date)->format('Y-m-d');
                    AssessmentApprove::where('available_date', $a->available_date)->update(['available_date' => $from]);
                }
            }
            return redirect()->back()->with(['status' => 'update completed']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => 'update completed']);
        }
    }

    public function usersPlans(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $users = User::with('profileType', 'bundleStatus.plans.package')
            ->latest()
            // ->get();
            ->paginate($perPage)
            ->appends($request->except('page'));
        return view('Backend/plans/usersPlans')
            ->with('users', $users);
    }


    public function downloadUserPlanXL(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $data = [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];

        return Excel::download(new UserPlanDateWiseExport($data), 'User Plan ' . Carbon::now()->format('d-M-Y g-i a') . '.xlsx');
    }


    public function RaiseQueryStatusChange(Request $request)
    {
        $query = RaiseQuery::find($request->query_id);
        if ($query) {
            $query->status = $request->status;
            $query->save();
            return $this->apiResponseService->success([
                'notify' => [
                    'type' => 'success',
                    'message' => 'status updated'
                ]
            ]);
        }
        return $this->apiResponseService->success([
            'notify' => [
                'type' => 'error',
                'message' => 'some problem occured. try reloading page'
            ]
        ]);
    }

    public function usersAdditionalPlans(Request $request)
    {
        $users = User::whereHas('bundleStatus.plans.package', function ($query) {
            $query->where('name', "HappiCHAT")->orWhere('name', 'HappiTALK');
        })
            ->with('userToken.token.organization')
            ->with(['bundleStatus' => function ($query) {
                $query->whereHas('plans.package', function ($query) {
                    $query->where('name', "HappiCHAT")->orWhere('name', 'HappiTALK');
                })->with('plans.package');
            }])
            ->get();
        // dd($users);
        $bundles = BundleStatus::with('user')->whereHas('plans.package', function ($query) {
            $query->where('name', "HappiCHAT")->orWhere('name', 'HappiTALK');
        })->with(['plans.package' => function ($query) {
            $query->where('name', 'HappiCHAT')->orWhere("name", "HappiTALK");
        }])->latest()->get()->groupBy('user.id');
        // dd($bundles);
        return view('Backend.plans.additionalPlans')
            ->with('bundles', $bundles);
    }

    public function regenerateReprot(Request $request)
    {
        try {
            $assessment  = Assessment::find($request->assessment_id);
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

    public function otherServices(Request $request)
    {
        $services = OtherService::with('type')->get();
        $servicesType = ServiceType::whereIn('name', ['HappiMynd Services', 'Other Services'])->with('services')->get();
        if (count($servicesType) > 0) {
            $services = $servicesType[0]->services;
        }
        $serviceTypes = ServiceTypeGroup::whereIn('name', ['HappiMynd Services', 'Other Services'])->get();
        return view('Backend/other_services')
            ->with([
                'services' => $services,
                'serviceTypes' => $serviceTypes
            ]);
    }

    public function editOtherServices($slug)
    {
        $service = OtherService::where('slug', $slug)->first();
        $serviceTypes = ServiceTypeGroup::whereIn('name', ['HappiMynd Services', 'Other Services'])->get();
        if (!$service) {
            abort(404);
        }
        return view('Backend/other_services_edit')
            ->with([
                'service' => $service,
                'serviceTypes' => $serviceTypes,
            ]);
    }

    public function saveOtherService(AdminOtherServiceRequest $request)
    {
        $validatedData = $request->validated();
        $fileService = new FileService;
        $fileNameThumbNail = '';
        if ($request->hasFile('thumbnail')) {
            $fileService->deleteAssetFile('services', 'thumbnail');
            $fileNameThumbNail = $fileService->saveAsAsset('services', 'thumbnail');
        }



        $post = OtherService::updateOrCreate(
            [
                'id' => $request['id']
            ],
            [
                'title' => $request['title'],
                'slug' => Str::slug($request['title']),
                'description' => $request['description'],
                'thumbnail' => $fileNameThumbNail,
                'price' => $request['price'],
                'discount' => $request['discount'],
                'buy_link' => $request['buy_link'],
                'coupon' => $request['coupon'],
                'service_type_group_id' => $request['service_type'],
                'publish_status' => $request['publish_status'],

            ]
        );



        return redirect()->back()->with('status', 'Service Posted Successfully');
    }
    public function deleteOtherServices(int $id)
    {
        OtherService::destroy($id);
        return redirect()->back()->with('status', 'Record Deleted Successfully');
    }

    public function educationalServices(Request $request)
    {

        $services = array();
        $servicesType = ServiceType::where('name', 'Educational Services')->with('services.educationService.author')->get();
        if (count($servicesType) > 0) {
            $services = $servicesType[0]->services;
        }
        $serviceTypes = ServiceTypeGroup::whereIn('name', ['Most Popular', 'Recommended'])->get();
        $authors = EducationServiceAuthor::all();
        return view('Backend/education_services')
            ->with([
                'services' => $services,
                'serviceTypes' => $serviceTypes,
                'authors' => $authors
            ]);
    }

    public function editEducationalServices($slug)
    {
        $service = OtherService::where('slug', $slug)->with('educationService')->first();
        $serviceTypes = ServiceTypeGroup::whereIn('name', ['Most Popular', 'Recommended'])->get();
        $authors = EducationServiceAuthor::all();
        if (!$service) {
            abort(404);
        }
        return view('Backend/education_services_edit')
            ->with([
                'service' => $service,
                'serviceTypes' => $serviceTypes,
                'authors' => $authors
            ]);
    }

    public function saveEducationalService(AdminEducationServiceRequest $request)
    {
        $validatedData = $request->validated();
        $fileService = new FileService;
        $fileNameThumbNail = '';
        if ($request->hasFile('thumbnail')) {
            $fileService->deleteAssetFile('services', 'thumbnail');
            $fileNameThumbNail = $fileService->saveAsAsset('services', 'thumbnail');
        } else {
            $fileNameThumbNail = $request->image;
        }


        $post = OtherService::updateOrCreate(
            [
                'id' => $request['id']
            ],
            [
                'title' => $request['title'],
                'slug' => Str::slug($request['title']),
                'description' => '',
                'thumbnail' => $fileNameThumbNail,
                'price' => $request['price'],
                'discount' => 0,
                'buy_link' => $request['buy_link'],
                'service_type_group_id' => $request['service_type'],
                'publish_status' => $request['publish_status'],

            ]
        );

        ServiceMetaData::updateOrCreate(
            [
                'other_service_id' => $post->id
            ],
            [
                'education_service_author_id' => $request['author'],
                'discounted_price' => $request['discounted_price'],
                'rating' => $request['rating'],
                'downloads' => $request['downloads'],

            ]
        );



        return redirect(route('admin.educationalServices.get'))->with('status', 'Service Posted Successfully');
    }
    public function deleteEducationalServices(int $id)
    {
        OtherService::destroy($id);
        return redirect()->back()->with('status', 'Record Deleted Successfully');
    }

    public function purchasedServices()
    {
        $purchasedServices = OtherServiceSubscriber::with('otherService.type.type', 'receipt')->where('paid', true)->latest()->get();
        return view('Backend/purchased_services')->with('purchasedServices', $purchasedServices);
    }

    public function editOrientationMail(Request $request)
    {
        $mail = DataGroup::where('name', 'email-orientation')->first();
        $mailBody = $mail->content()->where('title', 'body')->first()->content ?? '';
        $mailSubject = $mail->content()->where('title', 'subject')->first()->content ?? '';
        return view('Backend.orientationMailEdit')
            ->with('mailSubject', $mailSubject)
            ->with('mailBody', $mailBody);
    }

    public function saveOrientationMail(Request $request)
    {
        try {
            DB::beginTransaction();
            $mail = DataGroup::where('name', 'email-orientation')->first();
            $mailBody = $mail->content()->where('title', 'body')->first();
            $mailBody->content = $request->body;
            $mailBody->save();
            $mailSubject = $mail->content()->where('title', 'subject')->first();
            $mailSubject->content = $request->subject;
            $mailSubject->save();
            DB::commit();
        } catch (Exception $e) {
            \Log::error($e);
            DB::rollBack();
            $request->session()->flash('danger', 'error occurred while saving, please contact developer');
            return redirect()->back();
        }
        $request->session()->flash('success', 'Updated');
        return redirect(route('admin.staticData.editOrientationEmail.get'));
    }

    public function previewOrientationMail(Request $request)
    {
        $body = $request->input("body");
        $mailDetails =  [
            'body' => $body,
            'subject' => 'Subject',
            'token' => 'token',
            'packages' => 'package1, packag2 ',
            'name' => 'name',
            'path' => 'asd',
            'company' => 'company'
        ];
        $body = str_replace('[[name]]', $mailDetails['name'], $body);
        $body = str_replace('[[company]]', $mailDetails['company'], $body);
        $body = str_replace('[[packages]]', $mailDetails['packages'], $body);
        $body = str_replace('[[token]]', $mailDetails['token'], $body);
        $body = str_replace('\n', '<br>', $body);

        $mailDetails['body'] = $body;
        return new TokenEmail($mailDetails);
    }

    public function ourClientsGet(Request $request) {
        $all_clients = OurClient::orderBy('preference', 'ASC')->get();
        return view('Backend.ourclient')->with('all_clients', $all_clients);
    }

    public function saveOurClients(Request $request)
    {
        $fileService = new FileService;
        $responseData = [];
        $fileNameImage = '';
        $file_type = 1;
        if ($request->hasFile('image')) {
            if (isset($request->id)) {
                $client = OurClient::find($request->id);
                $fileService->deleteAssetFile('client', $client['image']);
            }
            $fileNameImage = $fileService->saveAsAsset('client', 'image');
        }
        $count = OurClient::all()->count();
        $data = [
            'name' => $request->name,
            'preference' => $count + 1
        ];
        if ($fileNameImage != '') {
            $data['image'] = $fileNameImage;
        }
        if (isset($request->id)) {
            $post = OurClient::where('id', $request->id)->update(
                $data
            );
            return redirect(route('admin.staticData.ourClientsGet'))->with('status', $request->name . ' updated successfully');
        } else {
            $data['image'] = $fileNameImage;
            $post = OurClient::Create(
                $data
            );
            return redirect(route('admin.staticData.ourClientsGet'))->with('status', $request->name . ' added sucessfully to Our Team');
        }
    }

    public function ourClientsEdit($id, Request $request) {
        $member = OurClient::find($id);
        return view('Backend.ourclient')->with('member', $member);
    }

    public function ourClientsDelete($id, Request $request) {
        $fileService = new FileService;
        $client = OurClient::find($id);
        $name  = $client->name;
        $fileService->deleteAssetFile('client', $client->image);
        $client->delete();
        return redirect(route('admin.staticData.ourClientsGet'))->with('status', $name . ' deleted sucessfully from our client');
    }

    public function landingPageSection(Request $request) {
        $section = $request['section'];
        $data = StaticSection::whereHas( 'dataGroup', function($query) {
            $query->where('name', 'landing_page');
        })->where('section', $section)->with('dataContent')->first();
        $data = $data->dataContent;
        Log::info($data);
        $folder = 'landing_page';
        return view('Backend.landing_statics.section_edit')->with('section', $data)->with('content_section', $request['section'])->with('folder', $folder);
    }

    public function saveContent( Request $request ) {
        $fileService = new FileService;
        $fileNameImage = '';
        $data = [
            'title' => $request['title'],
            'content' => $request['content']?$request['content']:'',
        ];
        $datacontent = '';
        if (isset($request->id)) {
            $datacontent = DataContent::find($request->id);
        }
        if ($request->hasFile('image')) {
            if ($datacontent && $datacontent['image']) {
                $fileService->deleteAssetFile('landing_page', $datacontent['image']);
            }
            $fileNameImage = $fileService->saveAsAsset('landing_page', 'image');
            if($fileNameImage) {
                $data['image'] = $fileNameImage;
            }
        }
        Log::info($data);
        $datacontent->update($data);
        return redirect()->back();
    }

    public function updateCarouselPriority(Request $request)
    {
        $datas = $request['data'];
        foreach ($datas as $id => $preference) {
            $updated = DataContent::where('id', $id)->update(['preference' => $preference]);
        }
        if ($updated == 1) {
            $notifyMsg = 'Preference Updated Successfully';
            $responseMsg = 'Preference for OurTeam has been updated successfully';
            return $this->apiResponseService->successNotify($notifyMsg, $responseMsg);
        } else {
            return $this->apiResponseService->error([
                'notify' => [
                    'type' => 'error',
                    'message' => 'Preference Cannot be Updated, Please try again'
                ]
            ]);
        }
    }


    public function offerScreen(Request $request){

        if($request->isMethod('GET')){
            $data = OfferScreenContent::first();

            return view('Backend/offer_screen')
                    ->with('data', $data);
        }
        if($request->isMethod('POST')){
            $data = OfferScreenContent::first();
            if($data){
                $data->content = $request->content;
                $data->save();
            }else{
                OfferScreenContent::create(['content' => $request->content ]);
            }
            return redirect()->back()->with('success' , 'Data update successfully.');
        }
    }


    // public function addPointsToOfferScreen(Request $request){
    //     if($request->isMethod('GET')){
    //         return view('Backend/offer_screen_points');
    //     }
    //     if($request->isMethod('POST')){
    //         OfferScreenContent::create(['type' => 'points' , 'content' => $request->point ]);
    //         return redirect('admin/staticdata/offer-screen')->with('success' , 'Added successfully.');
    //     }

    // }

    // public function deletePointsToOfferScreen(Request $request , $id){
    //     OfferScreenContent::where('id' , $id)->delete();
    //     return redirect('admin/staticdata/offer-screen')->with('success' , 'Delete successfully.');
    // }



    public function showCarouselContent(Request $request) {
        $carousel = CarouselSection::with('dataContents')->where('name', $request->carousel)->first();
        $carousel_data = $carousel->dataContents;
        return view('Backend.carousel_show')->with('carousel_data', $carousel_data); 
    }

    public function editCarouselContent(Request $request) {
        $data = DataContent::find($request->id);
        $folder = 'landing_page';

        return view('Backend.edit_carousel_content')->with('section', $data)->with('folder', $folder);
    }

    public function saveCarousel(Request $request)
    {
        $fileService = new FileService;
        $responseData = [];
        $fileNameImage = '';
        $file_type = 1;
        $asset_file = $request['asset_file'];
        if ($request->hasFile('image')) {
            if (isset($request->id)) {
                $data = DataContent::find($request->id);
                $fileService->deleteAssetFile($asset_file,  $data->image);
            }
            $fileNameImage = $fileService->saveAsAsset($asset_file, 'image');
        }
        $data = [
            'title' => $request->title,
            'content' => $request->content,   
        ];
        if ($fileNameImage != '') {
            $data['image'] = $fileNameImage;
        }
        $post = DataContent::find($request->id)->update(
            $data
        );
        return redirect()->back()->with('status', $request->name . ' updated successfully');
        
    }

    public function priorityCarouselUpdate(Request $request) {
        $data = $request['data'];
        foreach ($data as $id => $preference) {
            $updated = DataContent::where('id', $id)->update(['preference' => $preference]);
        }
        if ($updated == 1) {
            $notifyMsg = 'Preference Updated Successfully';
            $responseMsg = 'Preference for OurTeam has been updated successfully';
            return $this->apiResponseService->successNotify($notifyMsg, $responseMsg);
        } else {
            return $this->apiResponseService->error([
                'notify' => [
                    'type' => 'error',
                    'message' => 'Preference Cannot be Updated, Please try again'
                ]
            ]);
        }
    }


    public function userListToWhomPsyAssigned(Request $request){


        $username = $request->get('username');
        $perPage = $request->get('per_page', 10);

        if($username){
            // $users = User::where('username' ,$username)->orderBy('id','desc')->with('profileType')->paginate('10');

            $users = User::where('username' ,$username)->first();

            $data = GroupChat::where('user_id' , $users->id)
                ->select('user_id', DB::raw('MAX(id) as id') )
                ->groupBy('user_id')
                ->orderBy('id' , 'desc')
                ->with('user')
                ->paginate($perPage)
                ->appends($request->except('page'));
        }else{
            // $chat_users_ids = GroupChat::distinct('user_id')->pluck('user_id');
            // $users = User::whereIn('id' ,$chat_users_ids)->orderBy('id','desc')->with('profileType')->paginate('10');

            $data = GroupChat::select('user_id', DB::raw('MAX(id) as id') )
                ->groupBy('user_id')
                ->orderBy('id' , 'desc')
                ->with('user')
                ->paginate($perPage)
                ->appends($request->except('page'));

        }
        
        return view('assigned_psychologist/userlist_with_assigned_psy')->with('data' , $data);
    }


    public function userListToWhomPsyAssignedByUsername(Request $request){
        
        if($request->isMethod('GET')){
            return view('happibuddy/buddy_by_username');
        }
        if($request->isMethod('POST')){
            return redirect('admin/user-list-to-whom-psychologist-assign?username='.$request->username);
        }
    }


    public function monthlyReportOfBuddyUser(Request $request , $user_id){
        $list = HappibuddyMonthlyReport::where('user_id' , $user_id)->get();
        return view('happibuddy/users_monthly_report')->with('list' , $list);
    }



    public function psyListBasedOnUser(Request $request,$user_id){
        $psychologists = GroupChat::where('user_id' , $user_id)->orderBy('id','desc')->with('psychologist')->get();
        return view('assigned_psychologist/psychologist_list_of_user')->with('psychologists' , $psychologists);
    }


    public function changeBuddyPsy(Request $request,$user_id){
        $buddy_psy_ids = AssignPsyToPlan::where('plan_name' , 'HappiBuddy')->pluck('psychologist_id')->toArray();
        $psychologist = Psychologist::whereIn('id' , $buddy_psy_ids)->where('deleted_at' , null)->orderBy('first_name' , 'asc')->get();
        return view('happibuddy/change_psy')->with('user_id',$user_id)->with('psychologist',$psychologist);
    }


    public function actionSwitchBuddyPsy(Request $request,$user_id , $psy_id){

        $group_detail_of_user = GroupChat::where('user_id' , $user_id)->first();


        $group_chat = [
            'user_id'           => $user_id,
            'psychologist_id'   => $psy_id,
            'group_id'          => $group_detail_of_user->group_id,
            'assigned_date_time' => Date('d-m-Y h:i:s'),
            'language' => $group_detail_of_user->language,
        ];

        GroupChat::create($group_chat);

        $group_detail_of_user->group_active_for_chat = 0;
        $group_detail_of_user->save();

        return redirect('admin/user-list-to-whom-psychologist-assign')->with('success' , 'Psychologist switch successfully.');
    }





    public function organizationDetailsWithLogo(Request $request){
        $organizations_list = Organization::where('deleted_at' , null)->get();
        return view('organization_logo/organization_logo')->with('organizations_list',$organizations_list);
    }


    public function editOrgLogo(Request $request , $id){

        if($request->isMethod('GET')){
            $org_detail = Organization::where('id' , $id)->first();
            return view('organization_logo/edit-org-logo')->with('org_detail',$org_detail);
        }
        if($request->isMethod('POST')){

            $validator = Validator::make($request->all(), [
                'image' => 'sometimes|image|mimes:jpg,png,jpeg',
               
            ],);

            if($validator->fails()) {
                return back()->with('error' , 'Please Upload valid image');
            }


            $logo =  $request->image;

            if($logo != null){
                $imageName = time().'.'.$logo->extension();  
     
                $path = Storage::disk('s3')->put(config('constants.mediaAssets.organization_logo.folderName'), $logo);
                $path = Storage::disk('s3')->url($path); 

                $for_save_image = explode('//' ,$path);


                $data = [
                    'organization_logo' => $for_save_image[2],
                    'main_logo' => $request->main_logo,
                    'powered_by' => $request->powered_by,
                ];

                Organization::where('id',$id)->update($data);

                return redirect('admin/organization-details-with-logo')->with('success' , 'Update successfully.');

            }else{

                $data = [
                    'main_logo' => $request->main_logo,
                    'powered_by' => $request->powered_by,
                ];

                Organization::where('id',$id)->update($data);
                return redirect('admin/organization-details-with-logo')->with('success' , 'Update successfully.');
            }
            

        }
       
    }




    public function getFeedbackList(Request $request){
        $list = Feedback::orderBy('id' , 'desc')->with('user' , 'applicationRateEmoji')->get();
        return view('Backend/feedback_list')->with('list' , $list);
    }


    public function downloadFeedbackListxl(Request $request)
    { 
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $data = [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];

        return Excel::download(new FeedbackExport($data), 'FeedbackList ' . Carbon::now()->format('d-M-Y g-i a') . '.xlsx');

    }



    public function viewUserMood(Request $request , $user_id){
        $user_mood_list = UserMood::where('user_id' , $user_id)->orderBy('date' , 'desc')->orderBy('time' , 'desc')->with('emojiDetails')->get();
        return view('Backend/user_mood_list')->with('user_mood_list' , $user_mood_list);
    }



    public function viewUserRewards(Request $request , $user_id){
        $user_reward_list = UserRewardPointRecord::where('user_id' , $user_id)->orderBy('id' , 'desc')->get();
        $total_earned_points = UserRewardPointRecord::where('user_id' , $user_id)->sum('points_earned');
        return view('Backend/user_reward_list')->with('user_reward_list' , $user_reward_list)->with('total_earned_points', $total_earned_points);
    }



    public function getRewardPointsInstanceList(Request $request){
        $perPage = $request->get('per_page', 50);
        $list = RewardPointInstance::paginate($perPage)
            ->appends($request->except('page'));
        return view('Backend/reward_points')->with('list' , $list);
    }



    public function getEditRewardPointsList(Request $request , $id){

        if($request->isMethod('GET')){
            $data = RewardPointInstance::where('id', $id)->first();
            return view('Backend/reward_points_edit')->with('data' , $data);
        }

        if($request->isMethod('POST')){

            RewardPointInstance::where('id' , $id)->update(['points_to_be_given' => $request->points_to_be_given]);
            return redirect('admin/list/reward-points-instance')->with('success' , 'Points has been updated successfully.');

        }
        
    }   


}