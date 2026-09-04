<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\DataGroup;
use App\Models\DataContent;
use App\Models\Quotes;
use App\Models\EditButton;
use App\Models\StaticSection;
use App\Models\OurClient;
use App\Models\ServiceImage;
use App\Models\OurTeam;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\OrganizationPageData;
use App\Models\OrganizationLogo;
use App\Models\ServiceTypeGroup;
use App\Models\OtherService;
use App\Models\OtherServiceSubscriber;
use App\Models\ServicesReceipt;
use App\Models\ServiceType;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Validator;

use App\Models\UserToken;
use App\Models\Token;
use App\Models\Organization;
use App\Models\AssignPsyToOrgForTalk;
use App\Models\AssignPsyToPlan;

class WebsiteController extends Controller
{
    public function landingPage()
    {
        $dataGroup = DataGroup::with('content', 'carouselSection')->where('name', 'landing_page')->first();

        $dataContents = $dataGroup ? $dataGroup->content : collect([]);

        $introVideoLink = '';
        $introVideoThumbnail = '';
        foreach ($dataContents as $content) {
            if ($content->title == 'landing_page_video') {
                $introVideoLink = $content->getContentWithS3Url('landing_page');
            }
            if ($content->title == 'landing_page_video_thumbnail') {
                $introVideoThumbnail = $content->getContentWithS3Url('landing_page');
            }
        }

        $data_sections = StaticSection::whereHas('dataGroup', function ($query) {
            $query->where('name', 'landing_page');
        })->get();

        $sections = [];
        foreach ($data_sections as $dt) {
            $dataContent = $dt->dataContent;
            if ($dataContent) {
                $dataContent->image_url = $dataContent->image ? $dataContent->getImagewithS3Url('landing_page') : null;
            }
            if (!array_key_exists($dt->section, $sections)) {
                $sections[$dt->section] = $dataContent;
            } else {
                if (is_array($sections[$dt->section])) {
                    $sections[$dt->section][] = $dataContent;
                } else {
                    $ar = $sections[$dt->section];
                    unset($sections[$dt->section]);
                    $sections[$dt->section][] = $ar;
                    $sections[$dt->section][] = $dataContent;
                }
            }
        }

        $quotes = Quotes::all();
        if ($quotes->count()) {
            $quotes[0]->image_url = $quotes[0]->getImageWithS3Url('quotes');
        }

        $clients = OurClient::orderBy('preference')->get();
        foreach ($clients as $client) {
            $client->image_url = $client->getImageWithS3Url();
        }

        $carousel = $dataGroup ? $dataGroup->carouselSection : [];
        foreach ($carousel as $section) {
            foreach ($section->dataContents as $dataContent) {
                $dataContent->image_url = $dataContent->image ? $dataContent->getImagewithS3Url('landing_page') : null;
            }
        }

        $generalFaqs = DataGroup::with('content')->where('name', 'faqs-general')->first();

        $androidLink = DataContent::where('title', 'android_hyperlink')->first();
        $iosLink = DataContent::where('title', 'ios_hyperlink')->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Landing page content.',
            'data' => [
                'intro_video_link' => $introVideoLink,
                'intro_video_thumbnail' => $introVideoThumbnail,
                'quotes' => $quotes->count() ? $quotes[0] : null,
                'sections' => $sections,
                'carousel' => $carousel,
                'clients' => $clients,
                'general_faqs' => $generalFaqs ? $generalFaqs->content : [],
                'landing_buttons' => EditButton::where('page_name', 'landing')->get(),
                'quotes_button' => EditButton::where('page_name', 'quotes')->first(),
                'android_hyperlink' => $androidLink ? $androidLink->content : null,
                'ios_hyperlink' => $iosLink ? $iosLink->content : null,
            ],
        ]);
    }

    public function serviceButtons()
    {
        $data = ServiceImage::all();
        foreach ($data as $item) {
            $item->image_url = $item->getImageWithS3Url('services');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Service buttons.',
            'data' => $data,
        ]);
    }

    public function services()
    {
        $data = ServiceImage::all();
        foreach ($data as $item) {
            $item->image_url = $item->getImageWithS3Url('services');
        }
        $exploreServiceContent = DataGroup::with('content')->where('name', 'explore-services')->first();
        $button_contents = EditButton::where('page_name', 'services')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Services page content.',
            'data' => [
                'service_images' => $data,
                'explore_service_content' => $exploreServiceContent,
                'button_contents' => $button_contents,
            ],
        ]);
    }

    public function exploreServices()
    {
        $data = ServiceImage::all();
        foreach ($data as $item) {
            $item->image_url = $item->getImageWithS3Url('services');
        }
        $button_contents = EditButton::where('page_name', 'services')->get();
        $dataContent = DataGroup::with('content')->where('name', 'explore-services')->first();

        $happiApp = null;
        $happiTALK = null;
        $happiSPACE = null;
        $happiCHAT = null;

        if ($dataContent && count($dataContent->content) >= 4) {
            $happiApp = $dataContent->content[0];
            $happiTALK = $dataContent->content[1];
            $happiSPACE = $dataContent->content[2];
            $happiCHAT = $dataContent->content[3];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Explore services content.',
            'data' => [
                'happi_app' => $happiApp,
                'happi_talk' => $happiTALK,
                'happi_space' => $happiSPACE,
                'happi_chat' => $happiCHAT,
                'service_images' => $data,
                'button_contents' => $button_contents,
            ],
        ]);
    }

    public function educationalServices()
    {
        $serviceTypeGroup = ServiceTypeGroup::whereIn('name', ['Recommended', 'Most Popular'])
            ->with('service')
            ->get();

        $recommended = $serviceTypeGroup->firstWhere('name', 'Recommended');
        $mostPopular = $serviceTypeGroup->firstWhere('name', 'Most Popular');

        $allCourses = collect();
        if ($recommended && $mostPopular) {
            $allCourses = $recommended->service->merge($mostPopular->service);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Educational services content.',
            'data' => [
                'recommended' => $recommended,
                'most_popular' => $mostPopular,
                'all_courses' => $allCourses->values(),
            ],
        ]);
    }

    public function otherServices()
    {
        $serviceTypeGroup = ServiceTypeGroup::whereIn('name', ['Other Services', 'HappiMynd Services'])
            ->with('service')
            ->get();

        $happimynd = $serviceTypeGroup->firstWhere('name', 'HappiMynd Services');
        $otherServices = $serviceTypeGroup->firstWhere('name', 'Other Services');

        return response()->json([
            'status' => 'success',
            'message' => 'Other services content.',
            'data' => [
                'happimynd' => $happimynd,
                'other_services' => $otherServices,
            ],
        ]);
    }

    public function otherServiceDetail($id)
    {
        $other_service = OtherService::find($id);
        if (!$other_service) {
            return response()->json(['status' => 'error', 'message' => 'Service not found.', 'data' => null], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Service detail.',
            'data' => $other_service,
        ]);
    }

    public function blogs()
    {
        $posts = PostCategory::with(['post' => function ($query) {
            $query->where([
                'restricted_content' => 0,
                'publish_status' => 1,
            ]);
        }])->latest()->get();

        $blogs = collect([]);
        $videos = collect([]);
        $audios = collect([]);

        foreach ($posts as $postItem) {
            if ($postItem->id == 1) {
                $blogs = $postItem->post;
            } else if ($postItem->id == 2) {
                $videos = $postItem->post;
            } else {
                $audios = $postItem->post;
            }
        }

        $featured = Post::where('featured', 1)->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Blogs.',
            'data' => [
                'blogs' => $blogs,
                'videos' => $videos,
                'audios' => $audios,
                'featured' => $featured,
            ],
        ]);
    }

    public function blog($slug)
    {
        $post = Post::where('slug', $slug)->first();
        if (!$post) {
            return response()->json(['status' => 'error', 'message' => 'Post not found.', 'data' => null], 404);
        }

        $relatedPosts = PostCategory::with(['post' => function ($query) use ($post) {
            $query->where([
                'restricted_content' => 0,
                'publish_status' => 1,
                'post_category_id' => $post->post_category_id,
            ])->where('id', '!=', $post->id);
        }])->where('id', $post->post_category_id)->get();

        $relatedArticle = collect([]);
        if ($relatedPosts->count() && count($relatedPosts[0]->post) >= 1) {
            $relatedArticle = $relatedPosts[0]->post->splice(0, 3);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Blog detail.',
            'data' => [
                'post' => $post,
                'related_articles' => $relatedArticle,
            ],
        ]);
    }

    public function allBlog($slug)
    {
        $posts = PostCategory::with(['post' => function ($query) {
            $query->where([
                'restricted_content' => 0,
                'publish_status' => 1,
            ]);
        }])->where('name', $slug)->first();

        if (!$posts) {
            return response()->json(['status' => 'error', 'message' => 'Category not found.', 'data' => null], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Posts by category.',
            'data' => $posts,
        ]);
    }

    public function ourTeam()
    {
        $founders = OurTeam::where('category', 'founders')->orderBy('preference')->get();
        $experts = OurTeam::where('category', 'experts')->orderBy('preference')->get();
        $psychologists = OurTeam::where('category', 'psychologists')->orderBy('preference')->get();

        $teams = $founders->merge($experts)->merge($psychologists);
        foreach ($teams as $member) {
            $member->image_url = $member->getImageWithS3Url('teams');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Our team.',
            'data' => [
                'founders' => $founders,
                'experts' => $experts,
                'psychologists' => $psychologists,
            ],
        ]);
    }

    public function organisation()
    {
        $organizationFaqs = DataGroup::with('content')->where('name', 'faqs-organization')->first();
        $organizations = OrganizationPageData::all();
        $logos = OrganizationLogo::all();
        $organisation_buttons = EditButton::where('page_name', 'organisation')->get();

        foreach ($organizations as $organization) {
            $desc = html_entity_decode(strip_tags($organization->description));
            if ($organization->id != 1) {
                $organization['lines'] = explode('*', $desc);
            } else {
                $organization['description'] = $desc;
            }
            $organization->image_url = $organization->getImageWithS3Url('org');
        }
        foreach ($logos as $logo) {
            $logo->image_url = $logo->getImageWithS3Url('organization_logo');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Organisation page content.',
            'data' => [
                'organization_faqs' => $organizationFaqs,
                'organizations' => $organizations,
                'logos' => $logos,
                'organisation_buttons' => $organisation_buttons,
            ],
        ]);
    }

    public function happispaceForm()
    {
        $dataContents = DataGroup::with(['content' => function ($query) {
            $query->where('title', 'happySpace_cdnlink');
        }])->where('name', 'landing_page')->first();

        $happySpace_cdnlink = ($dataContents && count($dataContents->content) >= 1)
            ? $dataContents->content[0]->content
            : '';

        return response()->json([
            'status' => 'success',
            'message' => 'Happispace form.',
            'data' => ['happy_space_cdnlink' => $happySpace_cdnlink],
        ]);
    }

    public function faq()
    {
        $generalFaqs = null;
        $organizationFaqs = null;

        $faqs = DataGroup::with('content')->whereIn('name', ['faqs-general', 'faqs-organization'])->get();

        $general = $faqs->firstWhere('name', 'faqs-general');
        $organization = $faqs->firstWhere('name', 'faqs-organization');

        if ($general) {
            $generalFaqs = $general->content;
        }
        if ($organization) {
            $organizationFaqs = $organization->content;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'FAQs.',
            'data' => [
                'general_faqs' => $generalFaqs,
                'organization_faqs' => $organizationFaqs,
            ],
        ]);
    }

    public function otherServicesSignup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'other_service' => ['required', 'exists:other_services,id'],
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'mobile' => ['required', 'max:10'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first(), 'data' => null], 422);
        }

        $subscriber = OtherServiceSubscriber::create([
            'other_service_id' => $request->other_service,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'name' => $request->name,
            'paid' => 0,
        ]);

        $serviceType = OtherService::find($request->other_service)->load('type.type');

        if ($serviceType->type->type->name == "Other Services") {
            $amount = $subscriber->load('otherService')->otherService->discountedPrice();
        } else {
            $amount = $subscriber->load('otherService.educationService')->otherService->educationService->discounted_price;
        }

        $callback = route('payment.responseOtherServices');

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $receipt = ServicesReceipt::create([
            'marchant_name' => 'RazorPay',
            'amount' => $amount,
            'currency' => 'INR',
            'other_service_subscriber_id' => $subscriber->id,
            'other_service_id' => $request->other_service,
        ]);

        $order = $api->order->create([
            'receipt' => $receipt->id,
            'amount' => $amount * 100,
            'currency' => 'INR',
        ]);

        $receipt->order_id = $order['id'];
        $receipt->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Order created.',
            'data' => [
                'subscriber' => $subscriber,
                'amount' => $amount,
                'currency' => 'INR',
                'order_id' => $order['id'],
                'razorpay_key' => env('RAZORPAY_KEY'),
                'callback_url' => $callback,
            ],
        ]);
    }

    public function dashboard(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated.', 'data' => null], 401);
        }

        $dataGroup = DataGroup::with('content')->where('name', 'dashboard')->first();
        $dataContents = $dataGroup ? $dataGroup->content()->get() : collect([]);
        $dashboardPic = '';
        $hyperlink = '';
        foreach ($dataContents as $content) {
            if ($content->title == 'dashboard_cover_pic') {
                $dashboardPic = $content->getContentWithS3Url('dashboard');
            } elseif ($content->title == 'hyperlink') {
                $hyperlink = $content->content;
            }
        }

        $assessment = $user->assessment()->with('approve')->orderBy('started_at', 'desc')->get();
        $assessment_id = (count($assessment) > 0) ? $assessment[0]->id : 0;
        $assessment_complete_status = (count($assessment) > 0 && $assessment[0]->ended_at == null) ? false : true;
        $appointment_status = (count($assessment) > 0 && $assessment[0]->approve && $assessment[0]->approve->slot != '') ? true : false;

        $booked_dates = \App\Models\AssessmentApprove::select('available_date')->whereNotNull('available_date')->get();
        $disableDates = \App\Models\Availability::select('date')->get();

        $bundleStatus = \App\Models\BundleStatus::where('user_id', $user->id)->Valid()->orderBy('plan_id', 'DESC')->first();
        $plan_id = ($bundleStatus) ? $bundleStatus->plan_id : 0;

        $summaryReadingPlanStatus = (\App\Models\BundleStatus::where('user_id', $user->id)->where('plan_id', 2)->NotExpired()->first()) ? false : true;
        $happiAPPPlanStatus = (\App\Models\BundleStatus::where('user_id', $user->id)->where('plan_id', 5)->NotExpired()->first()) ? false : true;

        $showBlinkingText = false;
        $blinkingText = '';
        if (!$assessment_complete_status) {
            $showBlinkingText = true;
            $blinkingText = 'screening';
        } else if ($summaryReadingPlanStatus) {
            $showBlinkingText = true;
            $blinkingText = 'summary_reading';
        } else if ($happiAPPPlanStatus) {
            $showBlinkingText = true;
            $blinkingText = 'happiapp';
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Dashboard.',
            'data' => [
                'dashboard_cover_pic' => $dashboardPic,
                'hyperlink' => $hyperlink,
                'user' => $user,
                'assessment_id' => $assessment_id,
                'assessment_complete_status' => $assessment_complete_status,
                'appointment_status' => $appointment_status,
                'plan_id' => $plan_id,
                'slot_booked' => \App\Services\AppointmentService::getBookedAppointmentDates(),
                'booked_dates' => $booked_dates,
                'disable_dates' => $disableDates,
                'show_blinking_text' => $showBlinkingText,
                'blinking_text' => $blinkingText,
            ],
        ]);
    }

    public function psychologists(Request $request)
    {
        $query = (new \App\Models\Psychologist())->newQuery()->whereNotNull('slot1');

        if ($request->has('search')) {
            $query->where(function ($query) use ($request) {
                $query->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('language', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('city', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('expertLevel', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('specialization', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }
        if ($request->has('city')) {
            $query->whereHas('city', function ($query) use ($request) {
                $query->where('name', $request->city);
            });
        }
        if ($request->has('expert_category')) {
            $query->whereHas('expertLevel', function ($query) use ($request) {
                $query->where('name', $request->expert_category);
            });
        }
        if ($request->has('specialization')) {
            $query->whereHas('specialization', function ($query) use ($request) {
                $query->where('name', $request->specialization);
            });
        }
        if ($request->has('language')) {
            $query->whereHas('language', function ($query) use ($request) {
                $query->where('name', $request->language);
            });
        }

        $user = Auth::guard('api')->user();
        $userType = 'individual';
        $organizationName = null;

        if ($user) {
            $userToken = UserToken::where('user_id', $user->id)->first();
            if ($userToken) {
                $token = Token::where('id', $userToken->token_id)->first();
                $organization = $token ? Organization::where('id', $token->organization_id)->first() : null;

                if ($organization) {
                    $orgPsyIds = AssignPsyToOrgForTalk::where('organization_id', $token->organization_id)
                        ->pluck('psychologist_id');
                    $query->whereIn('id', $orgPsyIds)->whereNotNull('slot1');

                    $totalSessions = $user->getOrganizationHappiTalkSessions();
                    if ($totalSessions > 0 && !\App\Models\HappitalkBooking::where('user_id', $user->id)->exists()) {
                        $userType = 'organization';
                        $organizationName = $organization->name;
                    }
                }
            } else {
                $talkPsyIds = AssignPsyToPlan::where('plan_name', 'HappiTalk')->pluck('psychologist_id')->toArray();
                $query->whereIn('id', $talkPsyIds)->whereNotNull('slot1');
            }
        }

        $psychologists = $query->with('customPrice')->where('deleted_at', null)->take($request->get('limit', 10))->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Psychologists.',
            'data' => \App\Http\Resources\PsychoLogistResource::collection($psychologists),
            'user_detail' => [
                'user_from' => $userType,
                'organization_name' => $organizationName,
            ],
            'filters' => [
                'specializations' => \App\Models\Specialization::all(),
                'expert_levels' => \App\Models\ExpertLevel::all(),
                'languages' => \App\Models\Language::all(),
                'cities' => \App\Models\City::all(),
            ],
        ]);
    }

    public function raiseQuery(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated.', 'data' => null], 401);
        }

        $validator = Validator::make($request->all(), [
            'category' => 'required|string',
            'query' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first(), 'data' => null], 400);
        }

        $raisedQuery = \App\Models\RaiseQuery::create([
            'category' => $request->input('category'),
            'query' => $request->input('query'),
            'user_id' => $user->id,
            'status' => 0,
            'platform' => 'website',
        ]);

        $query = \App\Models\RaiseQuery::with('user')->find($raisedQuery->id);
        $mailDetails = [
            'username' => $query->user->username,
            'email' => $query->user->email,
            'query' => [
                'description' => $query->query,
                'category' => $query->category,
            ],
        ];

        try {
            \Illuminate\Support\Facades\Mail::to(env('SUPPORT_MAIL'))->queue(new \App\Mail\QueryRaisedToAdmin($mailDetails));
        } catch (\Throwable $e) {
        }

        return response()->json(['status' => 'success', 'message' => 'Query has been raised successfully.', 'data' => null]);
    }

    public function subscribedServices(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated.', 'data' => null], 401);
        }

        $un_sorted_packages = \App\Models\Package::with(['plan' => function ($query) {
            return $query->withMax('offer', 'discount')->with('duration')->orderBy('offer_max_discount', 'ASC');
        }])->get();

        $sortOrder = ["HappiLIFE Screening", "HappiLIFE Summary Reading", "HappiGUIDE", "HappiBUDDY", "HappiSELF", "HappiTALK", "HappiSELF + HappiGUIDE", "HappiLEARN + HappiBUDDY", "HappiBUDDY + HappiSELF", "HappiLEARN + HappiBUDDY + HappiSELF"];

        $packages = \Illuminate\Support\Collection::make($un_sorted_packages)->sortBy(function ($item) use ($sortOrder) {
            $index = array_search(ucfirst($item['name']), $sortOrder);
            return $index === false ? count($sortOrder) : $index;
        })->values()->all();

        foreach ($packages as $key => $package) {
            if ($package->name == "HappiTALK") {
                $packages[$key]->setRelation('plan', collect([$package->getMinimumPricePlan()]));
            }
        }

        $assessment = $user->assessment()->completedAssessment()->latest('ended_at')->first();

        $subscribedPlans = $user->bundleStatus()->NotExpired()->get();
        $subscribedPlanIds = $subscribedPlans->pluck('plan_id')->toArray() ?? [];

        $organizationPackages = null;
        $organizationPlanIds = [];
        if ($user->isOrganizationUser()) {
            $organizationPackages = $user->userToken->token->tokenPlans()->with('bundleStatus')->get();
        }
        if ($organizationPackages != null) {
            $organizationPlanIds = $organizationPackages->pluck('plan_id')->toArray();
        }

        foreach ($packages as $packageKey => $package) {
            if ($package->bundle == 1 && $user->isOrganizationUser()) {
                unset($packages[$packageKey]);
                continue;
            }

            if ($package->plan->count() > 1) {
                $intersectUser = array_intersect($subscribedPlanIds, $package->plan->pluck('id')->toArray());
                $intersectOrg = ($organizationPackages != null) ? array_intersect($organizationPlanIds, $package->plan->pluck('id')->toArray()) : [];
                $plan_id = array_merge($intersectUser, $intersectOrg);
                if (count($plan_id) > 0) {
                    foreach ($package->plan as $planKey => $plan) {
                        if (!in_array($plan->id, $plan_id)) {
                            $packages[$packageKey]->plan->forget($planKey);
                        }
                    }
                }
            }
        }

        $response = [];
        foreach ($packages as $package) {
            $plans = [];
            foreach ($package->plan as $plan) {
                if (!$plan) {
                    continue;
                }
                $sellingPrice = $plan->getSellingPrice();
                $plans[] = [
                    'id' => $plan->id,
                    'package_id' => $plan->package_id,
                    'price' => (float)$plan->price,
                    'selling_price' => (float)$sellingPrice,
                    'per_session_selling_price' => ($plan->duration && $plan->duration->frequency) ? (int)($sellingPrice / $plan->duration->frequency) : null,
                    'offer' => $plan->offer ? [
                        'price' => (float)$plan->offer->price,
                        'discount' => $plan->offer->discount,
                    ] : null,
                    'offer_max_discount' => $plan->offer_max_discount,
                    'duration' => $plan->duration ? [
                        'id' => $plan->duration->id,
                        'name' => $plan->duration->name,
                        'type' => $plan->duration->type,
                        'value' => $plan->duration->value,
                        'frequency' => $plan->duration->frequency,
                    ] : null,
                    'is_subscribed' => in_array($plan->id, $subscribedPlanIds) || in_array($plan->id, $organizationPlanIds),
                ];
            }
            $response[] = [
                'id' => $package->id,
                'name' => $package->name,
                'description' => $package->description,
                'bundle' => $package->bundle,
                'is_subscribed' => collect($plans)->contains('is_subscribed', true),
                'plans' => $plans,
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Subscribed services.',
            'data' => [
                'packages' => array_values($response),
                'assessment' => $assessment,
                'user_id' => $user->id,
                'subscribed_plan_ids' => $subscribedPlanIds,
                'organization_plan_ids' => $organizationPlanIds,
            ],
        ]);
    }
}
