<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Exception;

class SubscriptionController extends Controller
{
    protected $keyId;
    protected $keySecret;
    protected $api;

    public function __construct()
    {
        $this->keyId = env('RAZORPAY_KEY');
        $this->keySecret = env('RAZORPAY_SECRET');
        $this->api = new Api($this->keyId, $this->keySecret);
    }

    public function showForm()
    {
        $plans = [
            [
                'id' => 'plan_PzqLTsbZHUwaG2', // Replace with actual Monthly Plan ID
                'name' => 'Monthly Plan',
                'price' => '₹99/month',
                'description' => 'Monthly access to HappiSELF'
            ],
        ];

        return view('subscription.form', compact('plans'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'plan' => 'required|string'
        ]);

        try {
            // Create subscription in Razorpay
            $subscription = $this->api->subscription->create([
                'plan_id' => $request->plan,
                'customer_notify' => 1,
                'total_count' => 359,
                'notes' => [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone
                ]
            ]);
            
            // Redirect user to the Razorpay hosted payment page
            return redirect()->away($subscription->short_url);
            
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e);
        }
    }
}