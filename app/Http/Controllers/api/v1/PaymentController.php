<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\User;
use App\Models\Package;
use App\Models\Plan;
use App\Models\Receipt;
use App\Models\BundleStatus;
use App\Models\Coupon;
use App\Models\CouponPlan;
use App\Models\UserToken;
use App\Models\TokenPlan;
use App\Models\Psychologist;

use App\Models\DynamicBundlePlan;

use App\Models\NotificationList;
use App\Models\NotificationMessage;

use App\Models\HappitalkBooking;
use App\Models\HappitalkSession;
use App\Models\AssignPsyToPlan;
use App\Models\HappiguideSession;
// use App\Models\HappitalkTax;
use App\Models\ReceiptPackage;

use App\Models\RewardPointInstance;
use App\Models\UserRewardPointRecord;
use App\Models\CouponReceipt;



use DateTime;
use Carbon\Carbon;

use App\BusinessModel\PushNotification;
use App\BusinessModel\RewardPointToUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class PaymentController extends Controller
{

    public function pushNotification()
    {
        return new PushNotification();
    }

    public function rewardPointToUser()
    {
        return new RewardPointToUser();
    }


    private $api;
    private $marchant_name;
    private const RazorPay = 'RazorPay';

    public function __construct($marchant_name = self::RazorPay)
    {
        $this->marchant_name = $marchant_name;
        /* If marchant is RazorPay */
        if ($this->marchant_name == self::RazorPay) {
            $api_key = env('RAZORPAY_KEY');
            $api_secret = env('RAZORPAY_SECRET');
            $this->api = new Api($api_key, $api_secret);
        }
    }



    public function buyPlan(Request $request)
    {
        Log::info('buyPlan');
        $user = Auth::user();

        $user_subscribed_plans = BundleStatus::where('user_id', $user->id)->pluck('plan_id');
        $packages_based_on_plan_ids = Plan::whereIn('id', $user_subscribed_plans)->pluck('package_id')->toArray();

        $packages = Package::where('id', 1)
            ->orwhere('id', 2)
            ->orwhere('id', 3)
            ->orwhere('id', 15)
            ->orwhere('id', 16)
            ->orwhere('name', 'HappiSELF')
            ->orwhere('name', 'HappiTalk')
            ->orWhere('name', 'HappiGuide')
            ->where('deleted_at', null)
            ->with('mobilePlans')
            ->get();

        // $packages = Package::where('deleted_at' , null)->with('mobilePlans')->get();

        foreach ($packages as $key => $package) {
            $package_id = $package->id;
            if (in_array($package_id, $packages_based_on_plan_ids)) {
                $package->is_subscribed = 1;
            } else {
                $is_org_user = UserToken::where('user_id', $user->id)->with('token')->first();
                if ($is_org_user) {
                    $is_plan_assign_with_token = TokenPlan::where('token_id', $is_org_user->token_id)->where('plan_id', $package->mobilePlans[0]->id)->first();
                    if ($is_plan_assign_with_token) {
                        $package->is_subscribed = 2;
                    } else {
                        $package->is_subscribed = 0;
                    }
                } else {
                    $package->is_subscribed = 0;
                }
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Packages get successfully.', 'data' => $packages]);
    }


    public function payment(Request $request)
    {
        Log::info('payment');

        // $refund = $this->api->payment->fetch('pay_KEXHpMRHuWYvHr')->refund(array("amount"=> "100", "speed"=>"normal", "notes"=>array("notes_key_1"=>"Beam me up Scotty.", "notes_key_2"=>"Engage"), "receipt"=>"Receipt No. 31"));
        // print_r($refund);

        if ($request->coupen_id) {
            $coupen_id = $request->coupen_id;
        } else {
            $coupen_id = 0;
        }


        $user = Auth::user();

        $message = [
            'plan_id.required'     =>  'Please enter plan ID.',
            'amount.required'      =>  'Please enter amount.',
        ];
        $validator = Validator::make($request->all(), [
            'plan_id'   => 'required',
            'amount'   => 'required',
        ], $message);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $plan_id = $request->plan_id;
        $amount = $request->amount;

        $net_amount = $amount * 100;
        $currency = 'INR';
        $additionalNotes = [];

        $receipt = Receipt::create([
            'marchant_name' => $this->marchant_name,
            'amount' => $amount,
            'currency' => $currency,
            'user_id' => $user->id
        ]);

        $receipt_package_data = [
            'receipt_id' => $receipt->id,
            'plan_id' => $plan_id,
            'amount' => $receipt->amount,
        ];
        ReceiptPackage::create($receipt_package_data);


        $order = $this->api->order->create(array(
            'receipt' => $receipt->id,
            'amount' => $net_amount,
            'currency' => $currency,
            'notes' => $additionalNotes
        ));

        /* Update the order id */
        $receipt->order_id = $order['id'];
        $receipt->save();


        $payment_link = url('api/v1/payment-link' . '/' . $order->id . '/' . $user->id . '/' . $plan_id . '/' . $coupen_id);
        Log::info('payment', [$payment_link]);

        return response()->json(['status' => 'success', 'message' => 'Payment link get successfully.', 'link' => $payment_link]);
    }


    public function paymentLink(Request $request, $order_id, $user_id, $plan_id, $coupen_id)
    {
        Log::info('paymentLink');

        $callback_url = url('api/v1/success-payment-page' . '/' . $order_id . '/' . $user_id . '/' . $plan_id . '/' . $coupen_id);
        $order = Receipt::where('order_id', $order_id)->first();
        $user = User::where('id', $user_id)->first();

        return view('payment/paymentRequestApp')
            ->with('callback_url', $callback_url)
            ->with('order', $order)
            ->with('user', $user);
    }


    public function successPaymentPage(Request $request, $order_id, $user_id, $plan_id, $coupen_id)
    {
        Log::info('successPaymentPage');

        $data = $request->all();
        $payment_id = $data['razorpay_payment_id'];

        $receipt = Receipt::where('order_id', $order_id)->first();


        $plan_details = Plan::where('id', $plan_id)->first();
        $package_details = Package::where('id', $plan_details->package_id)->first();

        if ($package_details->bundle == 1) {
            $bundle_plans = DynamicBundlePlan::where('package_id', $plan_details->package_id)->get();
            foreach ($bundle_plans as $plan) {
                $bundleStatus = BundleStatus::create([
                    'user_id' => $user_id,
                    'plan_id' => $plan->plan_id,
                    'receipt_id' => $receipt->id,
                    'percentage_covered' => "100.00",
                ]);
            }
        } else {
            $bundleStatus = BundleStatus::create([
                'user_id' => $user_id,
                'plan_id' => $plan_id,
                'receipt_id' => $receipt->id,
                'percentage_covered' => "100.00",
            ]);
        }

        // $receipt->status = 1;
        // $receipt->payment_id = $payment_id;

        $receipt->save();


        if ($coupen_id != 0) {
            $coupen_data = [
                'coupon_id' => $coupen_id,
                'receipt_id' => $receipt->id,
                'user_id' => $user_id,
            ];

            CouponReceipt::create($coupen_data);
        }



        $plan_details = Plan::where('id', $plan_id)->with('package')->first();
        if ($plan_details->package->name === 'HappiLEARN') {
            $reward_points = RewardPointInstance::where('action_performed', 'When HappiLEARN Subscribed')->first();
            $points_to_be_added_to_user = $reward_points->points_to_be_given;
            $task_performed = 'Subscribe HappiLEARN';
            $this->rewardPointToUser()->addRewardToUser($user_id, $points_to_be_added_to_user, $task_performed);

            // $reward_data = [
            //     'user_id' => $user_id,
            //     'points_earned' => $points_to_be_added_to_user,
            //     'task_performed' => 'Subscribe HappiLEARN',
            // ];
            // UserRewardPointRecord::create($reward_data);
        }
        if ($plan_details->package->name === 'HappiBUDDY') {
            $reward_points = RewardPointInstance::where('action_performed', 'When HappiBUDDY Subscribed')->first();
            $points_to_be_added_to_user = $reward_points->points_to_be_given;
            $task_performed = 'Subscribe HappiBUDDY';
            $this->rewardPointToUser()->addRewardToUser($user_id, $points_to_be_added_to_user, $task_performed);
            // $reward_data = [
            //     'user_id' => $user_id,
            //     'points_earned' => $points_to_be_added_to_user,
            //     'task_performed' => 'Subscribe HappiBUDDY',
            // ];
            // UserRewardPointRecord::create($reward_data);
        }
        if ($plan_details->package->name === 'HappiSELF') {
            $reward_points = RewardPointInstance::where('action_performed', 'When HappiSELF subscribed')->first();
            $points_to_be_added_to_user = $reward_points->points_to_be_given;
            $task_performed = 'Subscribe HappiSELF';
            $this->rewardPointToUser()->addRewardToUser($user_id, $points_to_be_added_to_user, $task_performed);
            // $reward_data = [
            //     'user_id' => $user_id,
            //     'points_earned' => $points_to_be_added_to_user,
            //     'task_performed' => 'Subscribe HappiSELF',
            // ];
            // UserRewardPointRecord::create($reward_data);
        }


        $user_detail = User::where('id', $user_id)->first();
        $device_token = $user_detail->device_token;
        $noti_message_detail = NotificationMessage::where('type', 'When a service is purchased')->pluck($user_detail->language);
        $message = $noti_message_detail[0];
        // $message = 'Congratulations On Prioritising Your Health😃🤗';
        // $this->pushNotification()->sendNotification($device_token,$message);
        if ($device_token != null && strlen($device_token) > 20) {
            $this->pushNotification()->sendNotification($device_token, $message);
        }
        $data = [
            'user_id' => $user_detail->id,
            'message' => $message,
        ];
        NotificationList::create($data);


        return view('payment/payment-success-page');
    }


    public function applyCoupon(Request $request)
    {
        Log::info('applyCoupon');

        $message = [
            'plan_id.required'      =>  'Please enter plan ID.',
            'coupon.required'      =>  'Please enter coupon.',
        ];
        $validator = Validator::make($request->all(), [
            'plan_id'   => 'required',
            'coupon'   => 'required',
        ], $message);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $check_coupon_exist = Coupon::where('code', $request->coupon)->where('status', 1)->first();
        if (!$check_coupon_exist) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Coupon'], 400);
        } else {

            if (!empty($check_coupon_exist->expired_at) && $check_coupon_exist->expired_at < Carbon::now()) {  // if expiry time is crossed
                return response()->json(['status' => 'error', 'message' => 'Coupon Expired'], 400);
            }
        }


        $is_coupon_exist_given_plan_id =  CouponPlan::where('plan_id', $request->plan_id)->where('coupon_id', $check_coupon_exist->id)->first();
        if ($is_coupon_exist_given_plan_id) {

            $applied_coupon_details = [
                'coupon_id' => $check_coupon_exist->id,
                'plan_id' => $request->plan_id,
                'discount' => $check_coupon_exist->discount_percent,
            ];
            return response()->json(['status' => 'success', 'message' => 'The coupon was applied successfully.', 'data' => $applied_coupon_details], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'Coupon not belongs to these plan IDs.'], 400);
    }


    public function mySubscribedServices(Request $request)
    {
        Log::info('mySubscribedServices');

        $user = Auth::user();

        $user_subscribed_plans = BundleStatus::where('user_id', $user->id)->pluck('plan_id');
        $packages_based_on_plan_ids = Plan::whereIn('id', $user_subscribed_plans)->pluck('package_id')->toArray();

        $packages = Package::whereIn('id', $packages_based_on_plan_ids)->get();
        return response()->json(['status' => 'success', 'message' => 'My subscribed services get successfully.', 'data' => $packages], 200);
    }


    public function availFreeService(Request $request)
    {
        Log::info('availFreeService');

        $user = Auth::user();

        $message = [
            'plan_id.required'      =>  'Please enter plan ID.',
        ];
        $validator = Validator::make($request->all(), [
            'plan_id'   => 'required',
        ], $message);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        // $plan_ids = $request->plan_id;
        // $explode_plan_ids = explode(',',$plan_ids);
        // foreach($explode_plan_ids as $plan){
        //     $data = [
        //         'user_id' => $user->id,
        //         'plan_id' => $plan,
        //     ];

        //     BundleStatus::create($data);
        // }

        // $plan_ids = $request->plan_id;
        // $explode_plan_ids = explode(',',$plan_ids);
        // foreach($explode_plan_ids as $plan){
        $data = [
            'user_id' => $user->id,
            'plan_id' => $request->plan_id,
        ];

        BundleStatus::create($data);
        // }




        if ($request->coupen_id) {
            $coupen_data = [
                'user_id' => $user->id,
                'coupon_id' => $request->coupen_id,
            ];
            CouponReceipt::create($coupen_data);
        }


        return response()->json(['status' => 'success', 'message' => 'Plan availed successfully.']);
    }


    public function PaymentForIos(Request $request)
    {
        $user = Auth::user();

        $message = [
            'marchant_name.required'      =>  'Please enter marchant name.',
            'plan_id.required'      =>  'Please enter plan ID.',
            'amount.required'      =>  'Please enter amount.',
            'transaction_id.required'      =>  'Please enter transaction ID.',
            'transaction_receipt.required'      =>  'Please enter transaction receipt.',
        ];
        $validator = Validator::make($request->all(), [
            'marchant_name'   => 'required',
            'plan_id'   => 'required',
            'amount'   => 'required',
            'transaction_id'   => 'required',
            'transaction_receipt'   => 'required',

        ], $message);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $reciept_data = [
            'marchant_name' => $request->marchant_name,
            'amount' => $request->amount,
            'currency' => 'INR',
            'status' => 1,
            'order_id' => $request->transaction_id,
            // 'payment_id' => $request->transaction_receipt,
            'user_id' => $user->id,
        ];
        $create_receipt = Receipt::create($reciept_data);

        $receipt_package_data = [
            'receipt_id' => $create_receipt->id,
            'plan_id' => $request->plan_id,
            'amount' => $create_receipt->amount,
        ];
        ReceiptPackage::create($receipt_package_data);


        $bundle_data = [
            'valid' => 1,
            'percentage_covered' => '100.00',
            'plan_id' => $request->plan_id,
            'user_id' => $user->id,
            'receipt_id' => $create_receipt->id,
        ];

        BundleStatus::create($bundle_data);

        return response()->json(['status' => 'success', 'message' => 'Plan has been buy successfully.']);
    }


    public function paymentForHappitalk(Request $request)
    {

        $user = Auth::user();

        $message = [
            'psychologist_id.required'  =>  'Please enter psychologist ID.',
            'plan_id.required'          =>  'Please enter plan ID.',
            'amount.required'           =>  'Please enter amount.',
            'date.required'             =>  'Please enter date.',
            'time.required'             =>  'Please enter session.',
            'session.required'          =>  'Please enter session.',
        ];
        $validator = Validator::make($request->all(), [
            'psychologist_id'   => 'required',
            'plan_id'           => 'required',
            'amount'            => 'required',
            'date'              => 'required',
            'time'              => 'required',
            'session'           => 'required',
            'user_recording_permission' => 'required',
        ], $message);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        if ($request->coupen_id) {
            $coupen_id = $request->coupen_id;
        } else {
            $coupen_id = 0;
        }

        $psychologist_id = $request->psychologist_id;
        $plan_id = $request->plan_id;
        $amount = $request->amount;
        $date = $request->date;
        $time = str_replace(' ', '', $request->time);
        $session = $request->session;

        // $is_slot_already_book = HappitalkSession::where('psychologist_id', $psychologist_id)->where('date' , $date)->where('time' , '=',  $request->time)->where('is_cancel' , 0)->first();
        // if($is_slot_already_book){
        //     return response()->json(['status' => 'error' ,  'message' => 'This slot has been already book.']);
        // }

        $explode_start_end_time = explode('-', $request->time);

        $requested_start_time = rtrim($explode_start_end_time[0]);
        $check_start_time_exist_in_any_booked_slot =  HappitalkSession::where('psychologist_id', $psychologist_id)
            ->where('date', $request->date)
            ->whereRaw("STR_TO_DATE(CONCAT('1970-01-01 ', start_time), '%Y-%m-%d %h:%i %p') < STR_TO_DATE(CONCAT('1970-01-01 ', '$requested_start_time'), '%Y-%m-%d %h:%i %p')")
            ->whereRaw("STR_TO_DATE(CONCAT('1970-01-01 ', end_time), '%Y-%m-%d %h:%i %p') > STR_TO_DATE(CONCAT('1970-01-01 ', '$requested_start_time'), '%Y-%m-%d %h:%i %p')")
            ->where('is_req_accepted', '!=', '2')
            ->where('is_cancel', '!=', '1')
            ->first();

        if ($check_start_time_exist_in_any_booked_slot) {
            return response()->json(['status' => 'error', 'message' => "This slot is not available."]);
        }

        $requested_end_time = ltrim($explode_start_end_time[1]);
        $check_end_time_exist_in_any_booked_slot =  HappitalkSession::where('psychologist_id', $psychologist_id)
            ->where('date', $request->date)
            ->whereRaw("STR_TO_DATE(CONCAT('1970-01-01 ', start_time), '%Y-%m-%d %h:%i %p') < STR_TO_DATE(CONCAT('1970-01-01 ', '$requested_end_time'), '%Y-%m-%d %h:%i %p')")
            ->whereRaw("STR_TO_DATE(CONCAT('1970-01-01 ', end_time), '%Y-%m-%d %h:%i %p') > STR_TO_DATE(CONCAT('1970-01-01 ', '$requested_end_time'), '%Y-%m-%d %h:%i %p')")
            ->where('is_req_accepted', '!=', '2')
            ->where('is_cancel', '!=', '1')
            ->first();

        if ($check_end_time_exist_in_any_booked_slot) {
            return response()->json(['status' => 'error', 'message' => "This slot is not available."]);
        }

        $is_there_any_pending_session_req_at_this_time = HappitalkSession::where('psychologist_id', $psychologist_id)->where('date', $request->date)->where('time', $request->time)->where('is_req_accepted', '0')->first();
        if ($is_there_any_pending_session_req_at_this_time) {
            return response()->json(['status' => 'error', 'message' => "This slot is already booked."]);
        }
        $is_there_any_accepted_session_and_not_cancel_req_at_this_time = HappitalkSession::where('psychologist_id', $psychologist_id)->where('date', $request->date)->where('time', $request->time)->where('is_req_accepted', '1')->where('is_cancel', '0')->first();
        if ($is_there_any_accepted_session_and_not_cancel_req_at_this_time) {
            return response()->json(['status' => 'error', 'message' => "This slot is already booked."]);
        }

        $net_amount = $amount * 100;
        $currency = 'INR';
        $additionalNotes = [];

        $receipt = Receipt::create([
            'marchant_name' => $this->marchant_name,
            'amount' => $amount,
            'currency' => $currency,
            'user_id' => $user->id
        ]);

        $receipt_package_data = [
            'receipt_id' => $receipt->id,
            'plan_id' => $plan_id,
            'amount' => $receipt->amount,
        ];
        ReceiptPackage::create($receipt_package_data);


        $order = $this->api->order->create(array(
            'receipt' => $receipt->id,
            'amount' => $net_amount,
            'currency' => $currency,
            'notes' => $additionalNotes
        ));

        /* Update the order id */
        $receipt->order_id = $order['id'];
        $receipt->save();


        $payment_link = url('api/v1/payment-link-for-happitalk' . '/' . $order->id . '/' . $user->id . '/' . $plan_id . '/' . $psychologist_id . '/' . $date . '/' . $time . '/' . $session . '/' . $request->user_recording_permission . '/' . $coupen_id);

        return response()->json(['status' => 'success', 'message' => 'Payment link get successfully.', 'link' => $payment_link]);
    }


    public function paymentLinkForHappitalk(Request $request, $order_id, $user_id, $plan_id, $psychologist_id, $date, $time, $session, $user_recording_permission, $coupen_id)
    {
        $callback_url = url('api/v1/success-payment-page-for-happitalk' . '/' . $order_id . '/' . $user_id . '/' . $plan_id . '/' . $psychologist_id . '/' . $date . '/' . $time . '/' . $session . '/' . $user_recording_permission . '/' . $coupen_id);
        $order = Receipt::where('order_id', $order_id)->first();
        $user = User::where('id', $user_id)->first();

        return view('payment/paymentRequestApp')
            ->with('callback_url', $callback_url)
            ->with('order', $order)
            ->with('user', $user);
    }


    public function successPaymentPageForHappitalk(Request $request, $order_id, $user_id, $plan_id, $psychologist_id, $date, $time, $session, $user_recording_permission, $coupen_id)
    {

        $data = $request->all();
        $payment_id = $data['razorpay_payment_id'];

        $receipt = Receipt::where('order_id', $order_id)->first();

        $explode_plan_ids = explode(',', $plan_id);

        foreach ($explode_plan_ids as $plan_id) {
            $bundleStatus = BundleStatus::create([
                'user_id' => $user_id,
                'plan_id' => $plan_id,
                'receipt_id' => $receipt->id,
                'percentage_covered' => "100.00",
            ]);
        }

        $receipt->status = 1;
        // $receipt->payment_id = $payment_id;

        $receipt->save();


        if ($coupen_id != 0) {
            $coupen_data = [
                'coupon_id' => $coupen_id,
                'receipt_id' => $receipt->id,
                'user_id' => $user_id,
            ];

            CouponReceipt::create($coupen_data);
        }


        $reward_points = RewardPointInstance::where('action_performed', 'When HappiTALK is booked')->first();
        $points_to_be_added_to_user = $reward_points->points_to_be_given;
        $task_performed = 'Book HappiTALK';
        $this->rewardPointToUser()->addRewardToUser($user_id, $points_to_be_added_to_user, $task_performed);

        // $reward_data = [
        //     'user_id' => $user_id,
        //     'points_earned' => $points_to_be_added_to_user,
        //     'task_performed' => 'Book HappiTALK',
        // ];
        // UserRewardPointRecord::create($reward_data);


        // $user_detail = User::where('id' , $user_id)->first();
        // $device_token = $user_detail->device_token;

        // $noti_message_detail = NotificationMessage::where('type' , 'When a service is purchased')->pluck($user_detail->language);
        // $message = $noti_message_detail[0];

        // $this->pushNotification()->sendNotification($device_token,$message);

        // $data = [
        //     'user_id' => $user_detail->id,
        //     'message' => $message,
        // ];
        // NotificationList::create($data);

        $psy_details = Psychologist::where('id', $psychologist_id)->first();
        // $tds_Detail = HappitalkTax::first();

        $commission_percentage = $psy_details->commission_percentage;
        // $tds_percentage = $tds_Detail->tds_percentage;
        $tds_percentage = $psy_details->tds_percentage;


        $amount_with_commission = $receipt->amount / 100 * $commission_percentage;
        $amount_after_tds_deduction = $amount_with_commission - $amount_with_commission / 100 * $tds_percentage;

        $booking_details = [
            'user_id' => $user_id,
            'user_type' => 'b2c',
            'psychologist_id' => $psychologist_id,
            'amount' => $receipt->amount,
            'amount_after_deduction' => $amount_after_tds_deduction,
            'plan_id' => $plan_id,
            'total_no_of_session' => $session,
            'remaining_session' => $session - 1,
        ];
        $is_created = HappitalkBooking::create($booking_details);

        $explode_time = explode('-', $time);
        $full_start_time = $explode_time[0];
        $split_start_time =  preg_split('#(?<=\d)(?=[a-z])#i', $full_start_time);
        $full_end_time = $explode_time[1];
        $split_end_time =  preg_split('#(?<=\d)(?=[a-z])#i', $full_end_time);
        $exact_time = $split_start_time[0] . ' ' . $split_start_time[1] . ' ' . '-' . ' ' . $split_end_time[0] . ' ' . $split_end_time[1];


        // $unique_room_name = Date('Y-m-d_h:i:s').'_'.rand('0000000','9999999');

        // $sid = env('TWILIO_ACCOUNT_SID');
        // $token = env('TWILIO_ACCOUNT_TOKEN');
        // $twilio = new Client($sid, $token);


        // $room = $twilio->video->v1->rooms
        //                   ->create([
        //                                "recordParticipantsOnConnect" => True,
        //                                "statusCallback" => "www.google.com",
        //                                "type" => "group",
        //                                "uniqueName" => $unique_room_name,
        //                                'ttl' => 0,
        //                            ]
        //                   );


        $booking_details_with_date_time = [
            'happitalk_booking_id' => $is_created->id,
            'user_id' => $user_id,
            'user_type' => 'b2c',
            'psychologist_id' => $psychologist_id,
            'date' => $date,
            'time' => $exact_time,
            'start_time' => $split_start_time[0] . ' ' . $split_start_time[1],
            'end_time' => $split_end_time[0] . ' ' . $split_end_time[1],
            // 'room_id' => $room->sid,
            'user_recording_permission' => $user_recording_permission,
        ];
        HappitalkSession::create($booking_details_with_date_time);

        $user_detail = User::where('id', $user_id)->first();
        $device_token = $user_detail->device_token;
        // $noti_message_detail = NotificationMessage::where('type' , 'When a service is purchased')->pluck($user_detail->language);
        $message = "You're On The Right Path!🛤️😀. Your HappiTALK session has been proposed with your expert psychologist (" . $psy_details->first_name . ") for (" . $date . " " . $exact_time . ")";

        if ($device_token != null && strlen($device_token) > 20) {
            $this->pushNotification()->sendNotification($device_token, $message);
        }
        $data = [
            'user_id' => $user_detail->id,
            'message' => $message,
        ];
        NotificationList::create($data);

        $device_token = $psy_details->device_token;
        $message = "Someone has chosen you to help out! Your HappiTALK session has been scheduled for (" . $date . " " . $exact_time . " ).";
        if ($device_token != null && strlen($device_token) > 20) {
            $this->pushNotification()->sendNotification($device_token, $message);
        }

        \Mail::to($psy_details->email)->send(new \App\Mail\NewBookingMail($psy_details, $user_detail->name, $date, $exact_time));

        return view('payment/payment-success-page');
    }



    public function paymentForHappiguide(Request $request)
    {

        $user = Auth::user();

        $message = [
            'plan_id.required'          =>  'Please enter plan ID.',
            'amount.required'           =>  'Please enter amount.',
            'date.required'             =>  'Please enter date.',
            'time.required'             =>  'Please enter time.',
        ];
        $validator = Validator::make($request->all(), [
            'plan_id'           => 'required',
            'amount'            => 'required',
            'date'              => 'required',
            'time'              => 'required',
        ], $message);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $is_any_psy_map_to_guide = AssignPsyToPlan::where('plan_name', 'HappiGuide')->first();
        if (!$is_any_psy_map_to_guide) {
            return response()->json(['status' => 'error', 'message' => 'No psychologist map with HappiGuide'], 400);
        }


        if ($request->coupen_id) {
            $coupen_id = $request->coupen_id;
        } else {
            $coupen_id = 0;
        }

        $plan_id = $request->plan_id;
        $amount = $request->amount;
        $date = $request->date;
        $time = str_replace(' ', '', $request->time);


        $net_amount = $amount * 100;
        $currency = 'INR';
        $additionalNotes = [];

        $receipt = Receipt::create([
            'marchant_name' => $this->marchant_name,
            'amount' => $amount,
            'currency' => $currency,
            'user_id' => $user->id
        ]);

        $receipt_package_data = [
            'receipt_id' => $receipt->id,
            'plan_id' => $plan_id,
            'amount' => $receipt->amount,
        ];
        ReceiptPackage::create($receipt_package_data);


        $order = $this->api->order->create(array(
            'receipt' => $receipt->id,
            'amount' => $net_amount,
            'currency' => $currency,
            'notes' => $additionalNotes
        ));

        /* Update the order id */
        $receipt->order_id = $order['id'];
        $receipt->save();

        $payment_link = url('api/v1/payment-link-for-happiguide' . '/' . $order->id . '/' . $user->id . '/' . $plan_id . '/' . $date . '/' . $time . '/' . $coupen_id);

        return response()->json(['status' => 'success', 'message' => 'Payment link get successfully.', 'link' => $payment_link]);
    }


    public function paymentLinkForHappiguide(Request $request, $order_id, $user_id, $plan_id, $date, $time, $coupen_id)
    {
        $callback_url = url('api/v1/success-payment-page-for-happiguide' . '/' . $order_id . '/' . $user_id . '/' . $plan_id . '/' . $date . '/' . $time . '/' . $coupen_id);
        $order = Receipt::where('order_id', $order_id)->first();
        $user = User::where('id', $user_id)->first();

        return view('payment/paymentRequestApp')
            ->with('callback_url', $callback_url)
            ->with('order', $order)
            ->with('user', $user);
    }


    public function successPaymentPageForHappiguide(Request $request, $order_id, $user_id, $plan_id, $date, $time, $coupen_id)
    {
        $data = $request->all();
        $payment_id = $data['razorpay_payment_id'];

        $receipt = Receipt::where('order_id', $order_id)->first();

        // return $user_id.'/'.$plan_id.'/'.$receipt->id;

        $bundleStatus = BundleStatus::create([
            'user_id' => $user_id,
            'plan_id' => $plan_id,
            'receipt_id' => $receipt->id,
            'percentage_covered' => "100.00",
        ]);

        $receipt->status = 1;
        // $receipt->payment_id = $payment_id;

        $receipt->save();

        if ($coupen_id != 0) {
            $coupen_data = [
                'coupon_id' => $coupen_id,
                'receipt_id' => $receipt->id,
                'user_id' => $user_id,
            ];

            CouponReceipt::create($coupen_data);
        }

        $explode_time = explode('-', $time);
        $full_start_time = $explode_time[0];
        $split_start_time =  preg_split('#(?<=\d)(?=[a-z])#i', $full_start_time);
        $full_end_time = $explode_time[1];
        $split_end_time =  preg_split('#(?<=\d)(?=[a-z])#i', $full_end_time);
        $exact_time = $split_start_time[0] . ' ' . $split_start_time[1] . ' ' . '-' . ' ' . $split_end_time[0] . ' ' . $split_end_time[1];

        $unique_room_name = Date('Y-m-d_h:i:s') . '_' . rand('0000000', '9999999');

        // $sid = env('TWILIO_ACCOUNT_SID');
        // $token = env('TWILIO_ACCOUNT_TOKEN');
        // $twilio = new Client($sid, $token);

        // $room = $twilio->video->v1->rooms
        //                   ->create([
        //                                "recordParticipantsOnConnect" => false,
        //                                "statusCallback" => "www.google.com",
        //                                "type" => "group",
        //                                "uniqueName" => $unique_room_name,
        //                               'ttl' => 0,
        //                            ]
        //                   );

        // $room_id = $room->sid;

        $last_assign_psy = AssignPsyToPlan::where('plan_name', 'HappiGuide')->where('last_psy_assign_for_guide', 1)->first();
        if ($last_assign_psy == null) {
            $first_guide_psychologist = AssignPsyToPlan::where('plan_name', 'HappiGuide')->first();
            $first_guide_psychologist->last_psy_assign_for_guide = 1;
            $first_guide_psychologist->save();
            $psychologist_id = $first_guide_psychologist->psychologist_id;
        } else {
            $last_assign_psy->last_psy_assign_for_guide = 0;
            $last_assign_psy->save();

            $next_psy_to_be_assigned = AssignPsyToPlan::where('plan_name', 'HappiGuide')->where('id', '>', $last_assign_psy->id)->first();
            if ($next_psy_to_be_assigned) {
                $next_psy_to_be_assigned->last_psy_assign_for_guide = 1;
                $next_psy_to_be_assigned->save();
                $psychologist_id = $next_psy_to_be_assigned->psychologist_id;
            } else {
                $first_guide_psychologist = AssignPsyToPlan::where('plan_name', 'HappiGuide')->first();
                $first_guide_psychologist->last_psy_assign_for_guide = 1;
                $first_guide_psychologist->save();
                $psychologist_id = $first_guide_psychologist->psychologist_id;
            }
        }
        // return $psychologist_id;

        $data = [
            'user_id' => $user_id,
            'psychologist_id' => $psychologist_id,
            'date' => $date,
            'time' => $exact_time,
            'start_time' => $split_start_time[0] . ' ' . $split_start_time[1],
            'end_time' => $split_end_time[0] . ' ' . $split_end_time[1],
            // 'room_id' => $room_id,
            'is_start' => '0',
            'is_end' => '0',
        ];

        HappiguideSession::create($data);

        //Reward Points
        $reward_points = RewardPointInstance::where('action_performed', 'When HappiGUIDE Subscribed')->first();
        $points_to_be_added_to_user = $reward_points->points_to_be_given;
        $task_performed = 'Book HappiGUIDE';
        $this->rewardPointToUser()->addRewardToUser($user_id, $points_to_be_added_to_user, $task_performed);


        //Notifications
        $user_detail = User::where('id', $user_id)->first();
        $psy_details = Psychologist::where('id', $psychologist_id)->first();

        $users_device_token = $user_detail->device_token;
        $message = "You're On The Right Path!🛤️😀. Your HappiGUIDE session has been proposed with your expert psychologist (" . $psy_details->first_name . ") for (" . $date . " " . $exact_time . ")";
        $title = "HappiGUIDE session";

        if ($users_device_token != null && strlen($users_device_token) > 20) {
            $this->pushNotification()->sendNotification($users_device_token, $message, $title);
        }
        $data = [
            'user_id' => $user_detail->id,
            'message' => $message,
        ];
        NotificationList::create($data);

        $psy_device_token = $psy_details->device_token;
        $message = "Your HappiGUIDE session has been scheduled for (" . $date . " " . $exact_time . " ).";
        $title = "HappiGUIDE session";
        if ($psy_device_token != null && strlen($psy_device_token) > 20) {
            $this->pushNotification()->sendNotification($psy_device_token, $message, $title);
        }

        return view('payment/payment-success-page');
    }



    public function handleWebhook(Request $request)
    {
        // Verify the webhook authenticity
        $secret = env('WEBHOOK_SECRET');
        $signature = $request->header('x-razorpay-signature');
        $payload = file_get_contents('php://input');

        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if ($signature !== $expectedSignature) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Process the webhook event
        $event = json_decode($payload, true);

        if ($event['event'] === 'payment.captured') {

            // Handle successful payment event
            $paymentId = $event['payload']['payment']['entity']['id'];
            Log::info("paymentId===========>" . $paymentId);

            $order_id = $event['payload']['payment']['entity']['order_id'];
            Log::info("order_id===========>" . $order_id);

            Receipt::where('order_id', $order_id)->update(['status' => 1]);


            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Unhandled event'], 400);
    }
}
