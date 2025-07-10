<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Coupon;
use App\Models\CouponReceipt;
use App\Models\User;
use App\Models\Package;
use App\Models\Receipt;
use App\Models\Psychologist;
use App\Models\PsychologistAppointment;
use App\Models\TokenPlan;
use App\Models\ServiceType;
use App\Models\BundleStatus;
use App\Models\OtherService;
use Illuminate\Http\Request;
use App\Services\BitrixService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use App\Models\OtherServiceSubscriber;
use Exception;
// use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\ApiResponseService;
use Session;
use Illuminate\Support\Collection;

class PaymentController extends Controller
{
    public function __construct(PaymentService $paymentService, BitrixService $bitrixService)
    {
        $this->paymentService = $paymentService;
        $this->bitrix = $bitrixService;
    }

    public function buyBundle(Request $request)
    {
        // $packages = null;
        // if (auth('user')->user()->isOrganizationUser()) {
        //     $packages = Package::where('bundle', 0)->with(['plan' => function ($query) {
        //         return $query->withMax('offer', 'discount')->with('duration')->orderBy('offer_max_discount', 'ASC')->with('expertLevel');
        //     }])->get();
        // } else {
        //     $packages = Package::with(['plan' => function ($query) {
        //         return $query->withMax('offer', 'discount')->with('duration')->orderBy('offer_max_discount', 'ASC')->with('expertLevel');
        //     }])->get();
        // }
        $un_sorted_packages = null;
        if (auth('user')->user()->isOrganizationUser()) {
            $un_sorted_packages = Package::where('bundle', 0)->with(['plan' => function ($query) {
                return $query->withMax('offer', 'discount')->with('duration')->orderBy('offer_max_discount', 'ASC')->with('expertLevel');
            }])->get();
        } else {
            $un_sorted_packages = Package::with(['plan' => function ($query) {
                return $query->withMax('offer', 'discount')->with('duration')->orderBy('offer_max_discount', 'ASC')->with('expertLevel');
            }])->get();
        }

        $sortOrder = ["HappiLIFE Screening", "HappiLIFE Summary Reading", "HappiGUIDE", "HappiBUDDY", "HappiSELF", "HappiTALK"  , "HappiSELF + HappiGUIDE" , "HappiLEARN + HappiBUDDY" ,"HappiBUDDY + HappiSELF" ,"HappiLEARN + HappiBUDDY + HappiSELF"];
 
        $packages = Collection::make($un_sorted_packages)->sortBy(function ($item) use ($sortOrder) {
            return array_search(ucfirst($item['name']), $sortOrder);
        })->values()->all();
        
        foreach ($packages as $key => $package) {
            if ($package->name == "HappiTALK") {
                $packages[$key]->setRelation('plan', collect([$package->getMinimumPricePlan()]));
            }
        }
        $user_id = auth('user')->user()->id;
        return view('Frontend/payment/payment_bundle')
            ->with('packages', $packages)
            ->with('user_id', $user_id);
    }

    public function orderBundle(Request $request)
    {
        try {
            $additionalNotes = [];
            $user = User::find($request->input('user_id'));
            $paymentDetails = [];
            $prepareDataForReceipt = [];
            $coupon_code = $request->input('coupon_code');
            $coupon_id = '';
            $coupon = Coupon::where('code', $coupon_code)->first();
            $psychologist_id = $request->input('psychologist_id');
            $psychologist_plan_id = $request->input('psychologist_plan_id');
            if ($coupon) {
                $coupon_id = $coupon->id;
            }
            /* If user has email */
            if ($user) {
                $planIds = $request->input('plan');
                if (empty($planIds)) {
                    $planIds = [];
                }
                if (!empty($request->input('psychologist_plan_id') && !empty($request->input('psychologist_id')))) {
                    array_push($planIds, $request->input('psychologist_plan_id'));
                }
                $plans = Plan::whereIn('id', $planIds)->with(['offer' => function ($query) {
                    $query->where('valid', true)->orderBy('created_at', 'desc');
                }])->with('package')->get();
                //check if plans already subscribed by user
                $planNames = $plans->pluck('package.name')->toArray();
                $subscribedPlans = $user->bundleStatus()->with('plans.package')->get()->pluck('plans.package.name')->toArray();
                //user cant buy same plan more than once else he should create another account
                if (count(array_intersect($planNames, $subscribedPlans)) > 0) {
                    return redirect(route('user.dashboard'));
                }

                //to send data to bitrix for happichat and happitalk avail for B2B users
                $happiCHAT = "";
                $happiTALK = "";


                $happiTALKSessions = "";

                $amount = 0;
                if (auth('user')->user()->isOrganizationUser()) {
                    //get plans subscribed by users organization(signed up using happimyndcode)
                    $free_org_plans = array_intersect($planIds, $user->userToken->token->tokenPlans()->doesntHave('bundleStatus')->get()->pluck('plan_id')->toArray());
                    foreach ($plans as $plan) {
                        //check if plan is not subscribed by organization then add amount
                        $price = 0;
                        if ($plan->isHappiTalkPlan()) {

                            if ($user->organizationHasHappiTalkPlan()) {
                                $price = 0;
                            }
                        } elseif (in_array($plan->id, $free_org_plans)) {

                            $price = 0;
                        } else {
                            $price = $plan->getSellingPriceWithDiscount($coupon_code);
                        }
                        $amount += $price;
                        array_push($prepareDataForReceipt, ['plan_id' => $plan->id, "amount" => $price, 'package_id' => $plan->package_id]);

                        if ($plan->package->name == "HappiCHAT") {
                            $happiCHAT = "1";
                        }
                        if ($plan->package->name == "HappiTALK") {
                            $happiTALK = "1";
                            if ($user->organizationHasHappiTalkPlan()) {
                                $happiTALKSessions = "" . $user->getOrganizationHappiTalkSessions() ?? '';
                            } else {
                                $happiTALKSessions = "" . $plan->duration->frequency ?? '';
                            }
                        }
                    }
                } else {
                    $price = 0;
                    foreach ($plans as $plan) {
                        $price = $plan->getSellingPriceWithDiscount($coupon_code);
                        $amount += $price;
                        array_push($prepareDataForReceipt, ['plan_id' => $plan->id, "amount" => $price, 'package_id' => $plan->package_id]);
                    }
                }
                // dd($amount);
                // if ($amount != 0 && !$user->email) {
                //     $request->session()->flash('warning', 'Please Update your email and mobile number.');
                //     return redirect(route('user.dashboard'));
                // } else {
                    $datas = array();
                    foreach ($prepareDataForReceipt as $receiptData) {
                        array_push($datas, array(
                            'package_id' => $receiptData['package_id'],
                            "price" => $receiptData['amount'],
                            "quantity" => 1,
                        ));
                    }

                    foreach ($plans as $plan) {
                        // prepare data for bitrix
                        if ($plan->package->name == "HappiLIFE Screening") {
                            $paymentDetails +=
                                [
                                    'happiLIFEScreening' => [
                                        'makePaymentLink' => $request->fullUrl(),
                                        'paymentStatus' => "Selected"
                                    ]

                                ];
                        } else if ($plan->package->name == "HappiLIFE Summary Reading") {
                            $paymentDetails +=
                                [
                                    'happiLIFESummaryReading' => [
                                        'makePaymentLink' => $request->fullUrl(),
                                        'paymentStatus' => "Selected"
                                    ]

                                ];
                        } else if ($plan->package->name == "HappiAPP") {
                            $paymentDetails +=
                                [
                                    'happiAPP' => [
                                        'makePaymentLink' => $request->fullUrl(),
                                        'paymentStatus' => "Selected"
                                    ]

                                ];
                        } else if ($plan->package->name == "HappiCHAT") {
                            $paymentDetails +=
                                [
                                    'happiCHAT' => [
                                        'makePaymentLink' => $request->fullUrl(),
                                        'paymentStatus' => "Selected"
                                    ]

                                ];
                        } else if ($plan->package->name == "HappiTALK") {
                            $additionalNotes =
                                [
                                    'psychologist_id' => $psychologist_id,
                                ];
                            $paymentDetails +=
                                [
                                    'happiTALK' => [
                                        'makePaymentLink' => $request->fullUrl(),
                                        'paymentStatus' => "Selected"
                                    ]

                                ];
                        } else if ($plan->package->name == "HappiCHAT + HappiAPP") {
                            $paymentDetails +=
                                [
                                    'happiCHAThappiAPP' => [
                                        'makePaymentLink' => $request->fullUrl(),
                                        'paymentStatus' => "Selected"
                                    ]

                                ];
                        }
                    }
                    /** Update the package/products before payment(Product selected) of the B2C deal */
                    $deal_id = $user->deal_id;
                    // if ($deal_id) {
                    //     foreach ($datas as $planData) {
                    //         $planData['price'] = 0;
                    //         if ($planData['price'] == 0)
                    //             $bitrixResponse = $user->addProductDealToBitrix([$planData]);
                    //     }
                    //     $bitrixResponse = $user->updateBitrixDeal(array("makePayment" => $request->fullUrl(), 'paymentDetails' => $paymentDetails, 'HappiCHAT' => $happiCHAT, "HappiTALK" => $happiTALK, "HappiTALKSessions" => $happiTALKSessions));
                    // }
                    if ($deal_id && config('constants.bitrix')) {
                        foreach ($datas as $planData) {
                            $planData['price'] = 0;
                            if ($planData['price'] == 0)
                                $bitrixResponse = $user->addProductDealToBitrix([$planData]);
                        }
                        $bitrixResponse = $user->updateBitrixDeal(array("makePayment" => $request->fullUrl(), 'paymentDetails' => $paymentDetails, 'HappiCHAT' => $happiCHAT, "HappiTALK" => $happiTALK, "HappiTALKSessions" => $happiTALKSessions));
                    }

                    if ($amount <= 0) {
                        $paymentDetails = [];
                        $happiTALKSessions = '';
                        $happiTALK = '0';
                        $happiCHAT = '0';
                        /* Create Bundle status to track of the bundles/Plans */
                        $bundleStatuses = [];
                        foreach ($plans as $plan) {
                            $bundleStatus = BundleStatus::create([
                                'user_id' => $user->id,
                                'plan_id' => $plan->id
                            ]);
                            if ($plan->isHappiTalkPlan()) {
                                $psychologistAppointment = PsychologistAppointment::create([
                                    'user_id' => $user->id,
                                    'psychologist_id' => $psychologist_id,
                                    'sessions' => $bundleStatus->plans->duration->frequency,
                                ]);
                                $happiTALKSessions = '' . $bundleStatus->plans->duration->frequency;
                                $happiTALK = "1";
                                $request->session()->flash('popup', "show_happitalk_instruction");
                            }
                            array_push(
                                $bundleStatuses,
                                [
                                    "package_id" => $bundleStatus->plans->package->id,
                                    "price" => 0,
                                    "quantity" => 1,
                                ]
                            );
                            if ($plan->package->name == 'HappiCHAT') {
                                $happiCHAT = '1';
                                $request->session()->flash('popup', "show_happichat_instruction");
                            }

                            // prepare data for bitrix
                            if ($plan->package->name == "HappiLIFE Screening") {
                                $paymentDetails +=
                                    [
                                        'happiLIFEScreening' => [
                                            "paymentOrderLink" => 'Not Required',
                                            'paymentStatus' => "Added"
                                        ]

                                    ];
                            } else if ($plan->package->name == "HappiLIFE Summary Reading") {
                                $paymentDetails +=
                                    [
                                        'happiLIFESummaryReading' => [
                                            "paymentOrderLink" => 'Not Required',
                                            'paymentStatus' => "Added"
                                        ]

                                    ];
                            } else if ($plan->package->name == "HappiAPP") {
                                $paymentDetails +=
                                    [
                                        'happiAPP' => [
                                            "paymentOrderLink" => 'Not Required',
                                            'paymentStatus' => "Added"
                                        ]

                                    ];
                            } else if ($plan->package->name == "HappiCHAT") {
                                $paymentDetails +=
                                    [
                                        'happiCHAT' => [
                                            "paymentOrderLink" => 'Not Required',
                                            'paymentStatus' => "Added"
                                        ]

                                    ];
                            } else if ($plan->package->name == "HappiTALK") {
                                $paymentDetails +=
                                    [
                                        'happiTALK' => [
                                            "paymentOrderLink" => 'Not Required',
                                            'paymentStatus' => "Added"
                                        ]

                                    ];
                            } else if ($plan->package->name == "HappiCHAT + HappiAPP") {
                                $paymentDetails +=
                                    [
                                        'happiCHAThappiAPP' => [
                                            "paymentOrderLink" => 'Not Required',
                                            'paymentStatus' => "Added"
                                        ]

                                    ];
                            }

                            if (auth('user')->user()->isOrganizationUser()) {
                                //get plans subscribed by users organization(signed up using happimyndcode)
                                if (in_array($plan->id, $free_org_plans)) {
                                    $tokenPlan = $user->userToken->token->tokenPlans()->where('plan_id', $plan->id)->latest()->first();
                                    $tokenPlan->bundle_status_id = $bundleStatus->id;
                                    $tokenPlan->save();
                                    if ($plan->package->name == "HappiAPP") {
                                        $redirectTo = route('user.exploreServices');
                                    }
                                }
                            } else {
                                foreach ($plans as $plan) {
                                    $amount += $plan->getSellingPriceWithDiscount($coupon_code);
                                }
                            }
                        }
                        if (!empty($coupon_id)) {
                            CouponReceipt::create(['coupon_id' => $coupon_id, 'user_id' => $user->id]);
                        }
                        if (config('constants.bitrix')) {

                            $bitrixResponse = $user->updateBitrixDeal(array("paymentDetails" => $paymentDetails));
                            /** Update the deal after payment of the B2C deal */
                            if ($deal_id) {
                                $data = array(
                                    'paymentStatus' => 'Completed',
                                    'paymentLink' => 'Not required.'
                                );
                                $bitrixResponse = $user->updateBitrixDeal($data);
                            }

                            // copying bitrix deal to respective pipelines if user purchased happichat or happitalk
                            if ($happiCHAT == "1") {
                                $user->copyBitrixDealToPipeline("HappiCHAT");
                            }
                            if ($happiTALK == "1") {
                                $user->copyBitrixDealToPipeline("HappiTALK");
                            }
                        }
                        if (isset($redirectTo) && !is_null($redirectTo)) {
                            return redirect($redirectTo);
                        }
                        $request->session()->flash('success', 'You choose plan ' . implode(',', $planNames));
                        return redirect(route('user.dashboard'));
                    } else {

                        //if amount is not 0 before redirecting user to payment page send data to bitrix with status awaiting payment confirmation
                        $paymentDetails = [];
                        foreach ($plans as $plan) {
                            // prepare data for bitrix
                            if ($plan->package->name == "HappiLIFE Screening") {
                                $paymentDetails +=
                                    [
                                        'happiLIFEScreening' => [
                                            'paymentStatus' => "Payment Confirmation Pending"
                                        ]

                                    ];
                            } else if ($plan->package->name == "HappiLIFE Summary Reading") {
                                $paymentDetails +=
                                    [
                                        'happiLIFESummaryReading' => [
                                            'paymentStatus' => "Payment Confirmation Pending"
                                        ]

                                    ];
                            } else if ($plan->package->name == "HappiAPP") {
                                $paymentDetails +=
                                    [
                                        'happiAPP' => [
                                            'paymentStatus' => "Payment Confirmation Pending"
                                        ]

                                    ];
                            } else if ($plan->package->name == "HappiCHAT") {
                                $paymentDetails +=
                                    [
                                        'happiCHAT' => [
                                            'paymentStatus' => "Payment Confirmation Pending"
                                        ]

                                    ];
                            } else if ($plan->package->name == "HappiTALK") {
                                $paymentDetails +=
                                    [
                                        'happiTALK' => [
                                            'paymentStatus' => "Payment Confirmation Pending"
                                        ]

                                    ];
                            } else if ($plan->package->name == "HappiCHAT + HappiAPP") {
                                $paymentDetails +=
                                    [
                                        'happiCHAThappiAPP' => [
                                            'paymentStatus' => "Payment Confirmation Pending"
                                        ]

                                    ];
                            }
                        }
                        $user->updateBitrixDeal(array(
                            "paymentDetails" => $paymentDetails,
                            'HappiCHAT' => $happiCHAT,
                            "HappiTALK" => $happiTALK,
                            'HappiTALKSessions' => $happiTALKSessions,
                        ));
                    }
                // }
                // dd($prepareDataForReceipt);
                // dd($amount);
                return $this->paymentService->paymentRequest($user, $prepareDataForReceipt, $amount, $coupon_id, 'INR', null, $additionalNotes);

                $request->session()->flash('warning', 'Please Update your email and mobile number.');
            }
        } catch (Exception $e) {
            if (env('APP_DEBUG')) {
                throw $e;
            }
            \Log::error($e);
            $request->session()->flash('warning', 'Some error ocurred, please try again');
        }
        return redirect(route('user.dashboard'));
    }

    public function bookPsychologist(Request $request)
    {
        try {
            $user = User::find($request->input('user_id'));

            /* If user has email */
            if ($user) {
                $paymentDetails = [];
                $psychologist = Psychologist::find($request->psychologist_id);
                if ($psychologist) {
                    $plan_id = $request->plan_id;
                    $plans = $psychologist->getPsychologistPlans()->keyBy('id');
                    if (isset($plans[$plan_id]) && $plans[$plan_id] != null) {
                        $paymentDetails =
                            [
                                'happiTALK' => [
                                    'makePaymentLink' => $request->fullUrl(),
                                    'paymentStatus' => "Selected"
                                ]

                            ];
                        $user->updateBitrixDeal(array("paymentDetails" => $paymentDetails));
                        $plan  = $plans[$plan_id];
                        $amount = $plan->getSellingPrice();
                        $additionalData = [
                            'psychologist_id' => $psychologist->id,
                        ];
                        $coupon_id = '';
                        if (isset($request['coupon_id'])) {
                            if ($request['coupon_id']) {
                                $coupon = Coupon::find($request['coupon_id']);
                                if ($coupon) {
                                    $valid = $coupon->isValidCoupon($plan_id);
                                    if ($valid) {
                                        $coupon_id = $coupon->id;
                                        $discount_percent = $coupon->discount_percent;
                                        $amount = round($amount * (100 - $discount_percent) / 100, 2);
                                    }
                                }
                            }
                        }
                        return $this->paymentService->paymentRequest($user, [$plan->id], $amount, $coupon_id, 'INR', route('payment.psychologistPaymentResponse'), $additionalData);
                    }
                }
            }
        } catch (Exception $e) {
            if (env('APP_DEBUG') == true) {
                throw $e;
            }
            Log::critical("unable to book psychologist for query" . json_encode($request->toArray()));
            Log::error($e);
        }
    }

    public function psychologistPaymentResponse(Request $request)
    {
        // dd($request);
        $order = $this->paymentService->getPsychologistPaymentResponse($request);
        $reciept = Receipt::where('order_id', $order->id)->first();
        $psychologist_id = $order->notes->psychologist_id;
        $receiptPackages = $reciept->plans()->get();
        $user = User::find($reciept->user_id);
        $paymentDetails = [];
        /* If the payment is successful */
        if ($reciept->status) {
            $bundleStatuses = [];
            $happiTALKSessions = '';
            foreach ($receiptPackages as $receiptPackage) {
                $bundleStatus = BundleStatus::create([
                    'user_id' => $reciept->user->id,
                    'plan_id' => $receiptPackage->plan_id,
                    'receipt_id' => $reciept->id
                ]);
                array_push(
                    $bundleStatuses,
                    [
                        "package_id" => $bundleStatus->plans->package->id,
                        "price" => $reciept->amount,
                        "quantity" => 1,
                    ]
                );
                if ($bundleStatus->plans->package->name == 'HappiTALK') {
                    $psychologistAppointment = PsychologistAppointment::create([
                        'user_id' => $user->id,
                        'psychologist_id' => $psychologist_id,
                        'sessions' => $bundleStatus->plans->duration->frequency,
                    ]);
                    $happiTALKSessions = '' . $bundleStatus->plans->duration->frequency;
                    $request->session()->flash('popup', "show_happitalk_instruction");
                }
            }
            if (config('constants.bitrix')) {
                $user = User::find($reciept->user_id);
                $deal_id = $user->deal_id;
                /** Update the deal after payment of the B2C deal */
                if ($deal_id) {
                    $data = array(
                        'paymentStatus' => 'Completed',
                        'paymentLink' => 'https://dashboard.razorpay.com/app/orders/' . $reciept->order_id,
                        'amount' => $reciept->amount,
                        'currency' => $reciept->currency,
                        "HappiTALK" => '1',
                        'stage' => "WON",
                        'HappiTALKSessions' => $happiTALKSessions,
                        "paymentDetails" => [
                            'happiTALK' => [
                                "paymentOrderLink" => 'https://dashboard.razorpay.com/app/orders/' . $reciept->order_id,
                                'paymentStatus' => "Added"
                            ]

                        ],
                    );
                    $user->addProductDealToBitrix($bundleStatuses);
                    $user->updateBitrixDeal($data);
                }
            }

            $request->session()->flash('success', 'You have paid amount ' . $reciept->amount);
            // if (str_contains(User::find($reciept->user_id)->nickname, 'user')) {
            //     //TODO: analyze this logic
            //     return redirect(route('user.assessment'));
            // }
            return redirect(route('user.dashboard'));
        }
        $request->session()->flash('warning', 'Something went wrong!!!. Please try again.');
        return redirect(route('user.dashboard'));
    }

    public function responseBundle(Request $request)
    {
        // dd($request);
        $order = $this->paymentService->getPaymentResponse($request);
        $receipt = Receipt::where('order_id', $order->id)->first();
        $receiptPackages = $receipt->plans()->get();
        $user = User::find($receipt->user_id);
        /* If the payment is successful */
        $happiCHAT = "";
        $happiTALK = "";
        $happiTALKSessions = '';
        $paymentDetails = [];
        if ($receipt->status) {
            /* Create Bundle status to track of the bundles/Plans */
            $bundleStatuses = [];
            foreach ($receiptPackages as $receiptPackage) {
                $bundleStatus = BundleStatus::create([
                    'user_id' => $receipt->user->id,
                    'plan_id' => $receiptPackage->plan_id,
                    'receipt_id' => $receipt->id
                ]);
                array_push(
                    $bundleStatuses,
                    [
                        "package_id" => $bundleStatus->plans->package->id,
                        "price" => $bundleStatus->receipt->plans()->where('plan_id', $bundleStatus->plan_id)->first()->amount,
                        "quantity" => 1,
                    ]
                );
                if ($bundleStatus->plans->package->name == 'HappiTALK') {
                    $happiTALK = "1";
                    $happiTALKSessions = "" . $bundleStatus->plans->duration->frequency ?? '';
                    PsychologistAppointment::create([
                        'user_id' => $user->id,
                        'sessions' => $happiTALKSessions,
                        'psychologist_id' => $order->notes->psychologist_id,
                    ]);
                    $request->session()->flash('popup', "show_happitalk_instruction");
                }
                if ($bundleStatus->plans->package->name == 'HappiCHAT') {
                    $happiCHAT = "1";
                    $request->session()->flash('popup', "show_happichat_instruction");
                }
                // prepare data for bitrix
                if ($bundleStatus->plans->package->name == "HappiLIFE Screening") {
                    $paymentDetails +=
                        [
                            'happiLIFEScreening' => [
                                "paymentOrderLink" => 'https://dashboard.razorpay.com/app/orders/' . $receipt->order_id,
                                'paymentStatus' => "Added"
                            ]

                        ];
                } else if ($bundleStatus->plans->package->name == "HappiLIFE Summary Reading") {
                    $paymentDetails +=
                        [
                            'happiLIFESummaryReading' => [
                                "paymentOrderLink" => 'https://dashboard.razorpay.com/app/orders/' . $receipt->order_id,
                                'paymentStatus' => "Added"
                            ]

                        ];
                } else if ($bundleStatus->plans->package->name == "HappiAPP") {
                    $paymentDetails +=
                        [
                            'happiAPP' => [
                                "paymentOrderLink" => 'https://dashboard.razorpay.com/app/orders/' . $receipt->order_id,
                                'paymentStatus' => "Added"
                            ]

                        ];
                } else if ($bundleStatus->plans->package->name == "HappiCHAT") {
                    $paymentDetails +=
                        [
                            'happiCHAT' => [
                                "paymentOrderLink" => 'https://dashboard.razorpay.com/app/orders/' . $receipt->order_id,
                                'paymentStatus' => "Added"
                            ]

                        ];
                } else if ($bundleStatus->plans->package->name == "HappiTALK") {
                    $paymentDetails +=
                        [
                            'happiTALK' => [
                                "paymentOrderLink" => 'https://dashboard.razorpay.com/app/orders/' . $receipt->order_id,
                                'paymentStatus' => "Added"
                            ]

                        ];
                } else if ($bundleStatus->plans->package->name == "HappiCHAT + HappiAPP") {
                    $paymentDetails +=
                        [
                            'happiCHAThappiAPP' => [
                                "paymentOrderLink" => 'https://dashboard.razorpay.com/app/orders/' . $receipt->order_id,
                                'paymentStatus' => "Added"
                            ]

                        ];
                }
            }

            if (config('constants.bitrix')) {
                $user = User::find($receipt->user_id);
                $deal_id = $user->deal_id;
                /** Update the deal after payment of the B2C deal */
                if ($deal_id) {

                    $data = array(
                        'paymentStatus' => 'Completed',
                        'paymentLink' => 'https://dashboard.razorpay.com/app/orders/' . $receipt->order_id,
                        // 'amount' => $receipt->amount,
                        'currency' => $receipt->currency,
                        'HappiCHAT' => $happiCHAT,
                        "HappiTALK" => $happiTALK,
                        'HappiTALKSessions' => $happiTALKSessions,
                    );
                    $user->addProductDealToBitrix($bundleStatuses);
                    $bitrixResponse = $user->updateBitrixDeal($data);
                    $user->updateBitrixDeal(array("paymentDetails" => $paymentDetails));

                    // copying bitrix deal to respective pipelines if user purchased happichat or happitalk
                    if ($happiCHAT == "1") {
                        $user->copyBitrixDealToPipeline("HappiCHAT");
                    }
                    if ($happiTALK == "1") {
                        $user->copyBitrixDealToPipeline("HappiTALK");
                    }
                }
            }

            $request->session()->flash('success', 'You have paid amount ' . $receipt->amount);

            Session::put('app_popup' , 1);

            // if (str_contains(User::find($reciept->user_id)->nickname, 'user')) {
            //     //TODO: analyze this logic
            //     return redirect(route('user.assessment'));
            // }

            return redirect('subscribedservices');
            // return redirect(route('user.dashboard'));
        }
        $request->session()->flash('warning', 'Something went wrong!!!. Please try again.');
        return redirect(route('user.dashboard'));
    }

    public function paymentDetail(Request $request)
    {
        // $reciepts = Receipt::latest()->with('plans.plan.package')->with('user')->get();
        $perPage = $request->get('per_page', 10);
        $reciepts = Receipt::where('marchant_name' , 'RazorPay')->latest()->with('plans.plan.package')->with('user')->paginate($perPage)
            ->appends($request->except('page'));

        // dd(DB::getQueryLog());
        return view('Backend/paymentDetail')
            ->with('reciepts', $reciepts);
    }


    public function paymentDetailIos(Request $request)
    {
        // $reciepts = Receipt::latest()->with('plans.plan.package')->with('user')->get();
        $reciepts = Receipt::where('marchant_name' , 'apple_pay')->latest()->with('plans.plan.package')->with('user')->get();

        // dd(DB::getQueryLog());
        return view('Backend/paymentDetailIos')
            ->with('reciepts', $reciepts);
    }


    /**
     * Return subscribed services(signup using token) of organization user
     *
     * @param Request $request
     */
    public function subscribedServices(Request $request)
    {

        $un_sorted_packages = Package::with(['plan' => function ($query) {
            return $query->withMax('offer', 'discount')->with('duration')->orderBy('offer_max_discount', 'ASC');
        }])->get();


        $sortOrder = ["HappiLIFE Screening", "HappiLIFE Summary Reading", "HappiGUIDE", "HappiBUDDY", "HappiSELF", "HappiTALK"  , "HappiSELF + HappiGUIDE" , "HappiLEARN + HappiBUDDY" ,"HappiBUDDY + HappiSELF" ,"HappiLEARN + HappiBUDDY + HappiSELF"];
 
        $packages = Collection::make($un_sorted_packages)->sortBy(function ($item) use ($sortOrder) {
            return array_search(ucfirst($item['name']), $sortOrder);
        })->values()->all();


        foreach ($packages as $key => $package) {
            if ($package->name == "HappiTALK") {
                $packages[$key]->setRelation('plan', collect([$package->getMinimumPricePlan()]));
            }
        }

        $user_id = auth('user')->user()->id;
        $user = auth('user')->user();
        $assessment = auth('user')->user()->assessment()->completedAssessment()->latest('ended_at')->first();
        $tokenPlans = null;
        // $subscribedPlans = User::where('id', auth('user')->user()->id)->with(['bundleStatus.tokenPlan'])->get()->pluck('bundleStatus')[0] ?? null;
        $subscribedPlans = $user->bundleStatus;
        $subscribedPlanIds = $subscribedPlans->pluck('plan_id')->toArray() ?? null;
        $subscribedPlans = $user->bundleStatus->keyBy('plan_id');
        $organizationPackages = null;
        $organizationPlanIds = [];
        if (auth('user')->user()->isOrganizationUser()) {
            $organizationPackages = $user->userToken->token->tokenPlans()->with('bundleStatus')->get();
            // $organizationalPackages = $subscribedPlans->filter(function ($subscribedPlan) {
            //     if ($subscribedPlan->tokenPlan) {
            //         return true;
            //     }
            //     return false;
            // });
        }
        if ($organizationPackages != null) {
            $organizationPlanIds = $organizationPackages->pluck('plan_id')->toArray();
        }
        foreach ($packages as $packageKey => $package) {

            //bundle deals to be shown to only individual users
            if ($package->bundle == 1 && auth('user')->user()->isOrganizationUser()) {
                // $packages->forget($packageKey);

                if (is_object($packages)) {
                    $packages->forget($packageKey);
                }
            }

            //if package has multiple plans and user is subscribed to anyone plan among them then unset other plans under same package
            if (
                $package->plan->count() > 1 &&
                ($subscribedPlans != null &&
                    count($plan_id = array_intersect($subscribedPlanIds, $package->plan->pluck('id')->toArray())) > 0) ||
                ($organizationPackages != null &&
                    count($plan_id = array_intersect($organizationPlanIds, $package->plan->pluck('id')->toArray())) > 0)

            ) {
                foreach ($package->plan as $planKey => $plan) {
                    if (!in_array($plan->id, $plan_id)) {
                        $packages[$packageKey]->plan->forget($planKey);
                    }
                }
            }
        }
        if ($organizationPackages != null) {
            $organizationPackages = $organizationPackages->pluck('bundleStatus', 'plan_id');
        }
 
        // dd($subscribedPlanIds);
        // dd($organizationPackages);
        // dd($organizationPlanIds);

        $app_popup = Session::get('app_popup');
        if ($app_popup == 1) {
            Session::put('app_popup' , 0);
            $popup_status = 1;
        }else{
            $popup_status = 0;
        }

        return view('Frontend/payment/subscribed_services')
            ->with('packages', $packages)
            ->with('assessment', $assessment)
            ->with('user_id', $user_id)
            ->with('organizationPackages', $organizationPackages)
            ->with('subscribedPlans', $subscribedPlans)
            ->with('subscribedPlanIds', $subscribedPlanIds)
            ->with('organizationPlanIds', $organizationPlanIds)
            ->with('tokenPlans', $tokenPlans)
            ->with('popup_status', $popup_status);
    }
    public function responseOtherServices(Request $request)
    {
        $reciept = $this->paymentService->getServicePaymentResponse($request);
        /* If the payment is successful */
        $sub = OtherService::find($reciept->other_service_id)->load('type');
        $result = ServiceType::find($sub->type->service_type_id);
        if ($result->name == "Other Services") {
            $route = 'otherservices';
        } else {
            $route = 'educationalservices';
        }

        if ($reciept->status) {
            /* Update mailing list with payment */
            OtherServiceSubscriber::where('id', $reciept->other_service_subscriber_id)->update(['paid' => true]);
            $request->session()->flash('success', 'You have paid amount ' . $reciept->amount . ' we will email you your details in 48hrs');

            return redirect(route($route));
        }
        $request->session()->flash('warning', 'Something went wrong!!!. Please try again.');
        return redirect(route($route));
    }
}
