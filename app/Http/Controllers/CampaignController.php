<?php

namespace App\Http\Controllers;

use App\Models\BundleStatus;
use App\Models\Campaign;
use App\Models\Package;
use App\Models\Plan;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\UserService;
use Database\Seeders\UserSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Models\Receipt;
use App\Models\UserProfile;

use Session;
use App\Services\BitrixService;

class CampaignController extends Controller
{


    public function __construct(BitrixService $bitrixService)
    {
        $this->bitrix = $bitrixService;
    }


    public function addCampaign(Request $request)
    {
        $plans = Plan::with('package')->get()->keyBy('id'); //fetch and use primary id as key in collection object
        $planAndPrice = [];
        foreach ($request->input('plans') as $plan) {
            $planAndPrice += [
                $plans[$plan]['id'] => $plans[$plan]['price']
            ];
        }
        return Campaign::create([
            'name' => $request->input('campaign-name'),
            'url_parameters' => [
                'utm_campaign' => $request->input('campaign-name'),
            ],
            'meta_data' => [
                'total_price' => $request->input('campaign-total-price'),
            ],
            'plan_id' => $planAndPrice,
            'status' => true,
        ]);
    }

    public function checkCampaignName(Request $request)
    {
        return (Campaign::where('name', $request->input('name'))->first()) ? 1 : 0;
    }

    public function updateCampaign(Request $request)
    {
        //TODO: use existing plan price from json

        $plans = Plan::with('package')->get()->keyBy('id'); //fetch and use primary id as key in collection object
        $planAndPrice = [];
        foreach ($request->input('plans') as $plan) {
            $planAndPrice += [
                $plans[$plan]['id'] => $plans[$plan]['price']
            ];
        }
        return Campaign::where('id', $request->input('campaign-id'))->update([
            'name' => $request->input('campaign-name'),
            'url_parameters' =>
            [
                'utm_campaign' => $request->input('campaign-name'),
            ],
            'plan_id' => $planAndPrice,
        ]);
    }

    public function changeStatus(Request $request)
    {
        return Campaign::where('id', $request->input('id'))->update([
            'status' => $request->input('status'),
        ]);
    }

    public function deleteCampaign(Request $request)
    {
        return Campaign::destroy($request->input('id'));
    }
    public function getAllCampaigns(Request $request)
    {
        $campaigns = Campaign::orderBy('id', 'DESC')->get();
        $plans = Plan::with('package')->get()->pluck('package', 'id');
        $packages = Package::with('plan')->where('bundle', 0)->get();
        return view('Backend/campaign/index')
            ->with('campaigns', $campaigns)
            ->with('plans', $plans)
            ->with('packages', $packages);
    }

    public function getPlansPage(Request $request)
    {
        if (auth('user')->check()) {
            return redirect(route('user.dashboard'));
        }
        $packages = Package::with(['plan' => function ($query) {
            return $query->withMax('offer', 'discount')->with('duration')->orderBy('offer_max_discount', 'ASC');
        }])->get();
        $campaign = Campaign::where('name', $request->input('utm_campaign'))->first();
        if ($campaign && $campaign->isActive()) {

            $user = (new UserService)->generateUser();
            if ($user != null) {
                // dd('s');
                $planIds = array_keys($campaign->plan_id);
                $plans = Plan::whereIn('id', $planIds)->with('package')->get();
                $amount = $campaign->meta_data['total_price'];

                if (config('constants.bitrix')) {
                    $datas = array();
                    foreach ($plans as $plan) {
                        $price = 0;
                        if ($plan->offer) {
                            $price += $plan->offer->price;
                        } else {
                            $price += $plan->price;
                        }
                        array_push($datas, array(
                            'package_id' => $plan->package_id,
                            "price" => $price,
                            "quantity" => 1,
                        ));
                    }

                    /** Update the package/products before payment(Product selected) of the B2C deal */
                    $deal_id = $user->deal_id;
                    if ($deal_id) {
                        $bitrixResponse = $user->addProductDealToBitrix($deal_id, $datas);
                        //TODO: bitrix data sync
                        $bitrixResponse = $this->bitrix->updateBitrixDeal(
                            $deal_id,
                            $user,
                            array('stage' => "3", "makePayment" => $request->fullUrl(),)
                        );
                    }
                }

                if ($amount == 0) {
                    /* Create Bundle status to track of the bundles/Plans */
                    foreach ($plans as $plan) {
                        $bundleStatus = BundleStatus::create([
                            'user_id' => $user->id,
                            'plan_id' => $plan->id
                        ]);
                    }

                    if (config('constants.bitrix')) {
                        /** Update the deal after payment of the B2C deal */
                        if ($deal_id) {
                            $data = array(
                                'paymentStatus' => 'Completed',
                                'paymentLink' => 'Not required.'
                            );
                            $bitrixResponse = $this->bitrix->updateDeal($deal_id, $user, $data);
                        }
                    }

                    $request->session()->flash('success', 'You choose plan ' . $plan->package->name);
                    return 'asd';
                }
                



                return (new PaymentService)->paymentRequest($user, $planIds, $amount, null , "INR", route('campaign.payment.responseBundle')."?ids=".implode(',',$planIds));
            }
        }
        // return redirect(route('user.payment.buyBundle'));
    }
    


    public function responseBundle(Request $request)
    {
        $plan_ids =  $request->ids;

        $reciept = (new paymentService)->getPaymentResponse($request);

        // print_r($reciept->id); die();
        $reciept_detail = Receipt::where('order_id' , $reciept->id)->first();
        $user = User::where('id' , $reciept_detail->user_id)->first();

        $explode_plan_ids = explode(',' , $plan_ids);

        // $receiptPackages = $reciept->plans()->get();
        // $user = User::find($reciept->user_id);

        /* If the payment is successful */
        if ($reciept->status) {

            /* Create Bundle status to track of the bundles/Plans */
            // foreach ($receiptPackages as $receiptPackage) {
            //     $bundleStatus = BundleStatus::create([
            //         'user_id' => $reciept_detail->user_id,
            //         'plan_id' => $receiptPackage->plan_id,
            //         'receipt_id' => $reciept_detail->id
            //     ]);
            // }

            foreach ($explode_plan_ids as $row) {
                $bundleStatus = BundleStatus::create([
                    'user_id' => $reciept_detail->user_id,
                    'plan_id' => $row,
                    'receipt_id' => $reciept_detail->id
                ]);
            }


            if (config('constants.bitrix')) {
                $deal_id = $user->deal_id;
                /** Update the deal after payment of the B2C deal */
                if ($deal_id) {
                    $data = array(
                        'paymentStatus' => 'Completed',
                        'paymentLink' => 'https://dashboard.razorpay.com/app/orders/' . $reciept->order_id,
                        'amount' => $reciept->amount,
                        'currency' => $reciept->currency,
                        'stage' => "WON",
                    );
                    $bitrixResponse = $this->bitrix->updateDeal($deal_id, $user, $data);
                }
            }


            $request->session()->flash('success', 'You have paid amount ' . $reciept->amount);
            return redirect(route('user.individualSignupView'))->with('username', $user->username)->with('signupType', 'campaign');
        }
        $request->session()->flash('warning', 'Something went wrong!!!. Please try again.');
        return redirect(route('user.individualSignupView'));
    }




}







