<?php

namespace App\Services;

use Session;

use Exception;
use Razorpay\Api\Api;
use App\Models\Receipt;
use App\Models\CouponReceipt;
use Illuminate\Http\Request;
use App\Models\ServicesReceipt;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     *
     * @param $api => Razorpay\Api\Api;
     * @param $marchant_name => name of the marchant used for Payment
     */
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

    /**
     * User should verfiy email and mobile number
     *
     * @param $user : User Model (Required)
     * @param $package : array of pakageIds
     * @param $amount : Float (Required)
     * @param $currency : String
     */
    public function paymentRequest($user, $packageIds, $amount, $coupon_id = null, $currency = "INR", $callback_url = null, $additionalNotes = [])
    {
        /* If marchant is RazorPay */
        if ($this->marchant_name == self::RazorPay) {

            /* Calculate net amount(default is paisa in RazorPay) */
            $net_amount = $amount * 100;
            /* Create a Receipt for the above order */
            $receipt = Receipt::create([
                'marchant_name' => $this->marchant_name,
                'amount' => $amount,
                'currency' => $currency,
                'user_id' => $user->id
            ]);

            /* Add the packages to receipt */
            $toPackages = array();
            if (isset($packageIds[0]['plan_id'])) {
                foreach ($packageIds as $plan) {
                    array_push($toPackages, ['plan_id' => $plan['plan_id'], 'amount' => $plan['amount']]);
                }
            } else {
                foreach ($packageIds as $pId) {
                    array_push($toPackages, ['plan_id' => $pId]);
                }
            }
            $receipt->plans()->createMany($toPackages);
            if ($coupon_id) {
                CouponReceipt::create(['coupon_id' => $coupon_id, 'receipt_id' => $receipt->id, 'user_id' => $user->id]);
            }

            /* Create order againt the $amount */
            $order = $this->api->order->create(array(
                'receipt' => $receipt->id,
                'amount' => $net_amount,
                'currency' => $currency,
                'notes' => $additionalNotes
            ));

            /* Update the order id */
            $receipt->order_id = $order['id'];
            $receipt->save();

            /* Put order id in session */
            // \Session::put('order_id', $order['id']);
            if (is_null($callback_url)) {
                $callback_url = route('payment.responseBundle');
            }

            /* Request payment */
            return view('payment/paymentRequest')
                ->with('callback_url', $callback_url)
                ->with('order', $order)
                ->with('user', $user);
        }
    }

    /**
     * Return the Receipt: Model
     */
    public function getPaymentResponse(Request $request)
    {
        // dd($request);
        $order_id = '';
        try {
            if ($this->marchant_name == self::RazorPay) {
                if ($request->has('error')) {
                    $order_id = json_decode($request->error['metadata'])->order_id;
                } else {
                    $order_id = ($request->input('razorpay_order_id') != '') ? $request->input('razorpay_order_id') : \Session::get('order_id');
                }

                /* Fetch the order detail */
                $order = $this->api->order->fetch($order_id);

                /* Fetch the payment detail */
                $payment = $this->api->order->fetch($order_id)->payments();

                /* Fetch the Reciept detail */
                $receipt = Receipt::where('order_id', $order_id)->first();
                if ($payment['count'] > 0) {
                    /* Update the status only if payment is captured. */
                    if ($payment['items'][0]['status'] === 'captured') {
                        $receipt->status = true;
                        $receipt->save();
                    }
                }


                /* Return the reciept */
                Log::info("payment resposne" . json_encode($payment->toArray()));
                return $order;
            }
        } catch (Exception $e) {
            Log::critical('Payment Response Exception');
            Log::critical($request->toArray());
            Log::critical($order_id);
            Log::critical($e);
        }
    }
    public function getServicePaymentResponse(Request $request)
    {
        if ($this->marchant_name == self::RazorPay) {
            $order_id = ($request->input('razorpay_order_id') != '') ? $request->input('razorpay_order_id') : \Session::get('order_id');

            /* Fetch the order detail */
            $order = $this->api->order->fetch($order_id);

            /* Fetch the payment detail */
            $payment = $this->api->order->fetch($order_id)->payments();
            /* Fetch the Reciept detail */
            $receipt = ServicesReceipt::where('order_id', $order_id)->first();
            if ($payment['count'] > 0) {
                /* Update the status only if payment is captured. */
                if ($payment['items'][0]['status'] === 'captured') {
                    $receipt->status = true;
                    $receipt->save();
                }
            }

            /* Return the reciept */
            return $receipt;
        }
    }

    public function paymentServiceRequest($amount, $data, $callback_url = null, $currency = "INR")
    {
        /* If marchant is RazorPay */
        if ($this->marchant_name == self::RazorPay) {

            /* Calculate net amount(default is paisa in RazorPay) */
            $net_amount = $amount * 100;

            /* Create a Receipt for the above order */
            $receipt = ServicesReceipt::create([
                'marchant_name' => $this->marchant_name,
                'amount' => $amount,
                'currency' => $currency,
                'other_service_subscriber_id' => $data['id'],
                'other_service_id' => $data['other_service_id']
            ]);


            /* Create order againt the $amount */
            $order = $this->api->order->create(array(
                'receipt' => $receipt->id,
                'amount' => $net_amount,
                'currency' => $currency
            ));

            /* Update the order id */
            $receipt->order_id = $order['id'];
            $receipt->save();

            /* Put order id in session */
            \Session::put('order_id', $order['id']);
            if (is_null($callback_url)) {
                $callback_url = route('payment.responseOtherServices');
            }

            /* Request payment */
            return view('payment/paymentRequest')
                ->with('callback_url', $callback_url)
                ->with('order', $order)
                ->with('user', $data);
        }
    }
    /**
     * Return the Receipt: Model
     */
    public function getPsychologistPaymentResponse(Request $request)
    {
        // dd($request);
        if ($this->marchant_name == self::RazorPay) {
            $order_id = ($request->input('razorpay_order_id') != '') ? $request->input('razorpay_order_id') : \Session::get('order_id');

            /* Fetch the order detail */
            $order = $this->api->order->fetch($order_id);
            // dd($order->notes['psychologist_id']);
            /* Fetch the payment detail */
            $payment = $this->api->order->fetch($order_id)->payments();
            /* Fetch the Reciept detail */
            $receipt = Receipt::where('order_id', $order_id)->first();
            if ($payment['count'] > 0) {
                /* Update the status only if payment is captured. */
                if ($payment['items'][0]['status'] === 'captured') {
                    $receipt->status = true;
                    $receipt->save();
                }
            }

            /* Return the reciept */
            return $order;
        }
    }


    // public function paymentRequestApp($user, $packageIds, $amount, $coupon_id = null, $currency = "INR", $callback_url = null, $additionalNotes = [])
    // {
    //     /* If marchant is RazorPay */
    //     if ($this->marchant_name == self::RazorPay) {

    //         /* Calculate net amount(default is paisa in RazorPay) */
    //         $net_amount = $amount * 100;
    //         /* Create a Receipt for the above order */
    //         $receipt = Receipt::create([
    //             'marchant_name' => $this->marchant_name,
    //             'amount' => $amount,
    //             'currency' => $currency,
    //             'user_id' => $user->id
    //         ]);

    //         /* Add the packages to receipt */
    //         $toPackages = array();
    //         if (isset($packageIds[0]['plan_id'])) {
    //             foreach ($packageIds as $plan) {
    //                 array_push($toPackages, ['plan_id' => $plan['plan_id'], 'amount' => $plan['amount']]);
    //             }
    //         } else {
    //             foreach ($packageIds as $pId) {
    //                 array_push($toPackages, ['plan_id' => $pId]);
    //             }
    //         }
    //         $receipt->plans()->createMany($toPackages);
    //         if ($coupon_id) {
    //             CouponReceipt::create(['coupon_id' => $coupon_id, 'receipt_id' => $receipt->id, 'user_id' => $user->id]);
    //         }

    //         /* Create order againt the $amount */
    //         $order = $this->api->order->create(array(
    //             'receipt' => $receipt->id,
    //             'amount' => $net_amount,
    //             'currency' => $currency,
    //             'notes' => $additionalNotes
    //         ));

    //         /* Update the order id */
    //         $receipt->order_id = $order['id'];
    //         $receipt->save();

    //         /* Put order id in session */
    //         // \Session::put('order_id', $order['id']);
    //         if (is_null($callback_url)) {
    //             $callback_url = url('payment-successfull');
    //         }

    //         /* Request payment */
    //         // return view('payment/paymentRequest')
    //         //     ->with('callback_url', $callback_url)
    //         //     ->with('order', $order)
    //         //     ->with('user', $user);
    //         $order = $order['id'];
    //         $user_id = $user->id;

    //         $route_name =  url('payment-link');

    //         $payment_url_link =  $route_name.'/'.$order.'/'.$user_id;
    //         return response()->json(['status' => 'success' , 'message' => "Click on below link" , 'link' => $payment_url_link]);
    //     }
    // }
}
