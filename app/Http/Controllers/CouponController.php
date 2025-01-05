<?php

namespace App\Http\Controllers;

use App\Http\Requests\CouponSaveRequest;
use App\Models\Coupon;
use App\Models\CouponPlan;
use App\Models\CouponReceipt;
use App\Models\Plan;
use App\Services\CouponService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    private $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function verifyCoupon(Request $request)
    {
        if (isset($request['code']) && isset($request['plan_id'])) {
            $response = $this->couponService->verifyCoupon($request['code'], $request['plan_id']);
            if (!$response['error']) {
                $couponPlans = Coupon::where('code', $request['code'])->with('couponPlan.plan')->first()->couponPlan->pluck('plan.id')->toArray();
                $plans = Plan::whereIn('id', $request['plan_id'])->get();
                $coupon_plans = Plan::whereIn('id', $couponPlans)->with('package')->get();
                $plan_to_be_applied = [];
                $plan_to_be_applied_ids = [];

                foreach ($coupon_plans as $coupon_plan) {
                    array_push($plan_to_be_applied, [
                        'plan_id' => $coupon_plan->id, 'package_name' => $coupon_plan->package->name, 'price' => $coupon_plan->getSellingPrice(),
                        'discounted_price' => $coupon_plan->getCouponDiscountPrice($response['discount'])
                    ]);
                    array_push($plan_to_be_applied_ids, $coupon_plan->id);
                }
                $data = [];
                foreach ($plans as $plan) {
                    array_push(
                        $data,
                        [
                            'plan_id' => $plan->id,
                            'price' => $plan->getSellingPrice(),
                            'discounted_price' => (in_array($plan->id, $couponPlans)) ? $plan->getCouponDiscountPrice($response['discount']) : $plan->getSellingPrice(),
                            'discount_applied' => in_array($plan->id, $couponPlans),
                            'is_psychologist' => $plan->package->name == "HappiTALK",
                        ]
                    );
                }
                $response['msg'] = ['plans' => $data, 'coupon_plan' => $plan_to_be_applied, 'coupon_plan_ids' => $plan_to_be_applied_ids];
            }
            return $response;
        }
    }

    public function showCoupons()
    {
        // $coupons = Coupon::with('couponPlan' , 'couponReceipt')->get();
        $coupons = Coupon::with('couponPlan', 'couponReceipt')->latest()->paginate(2000);
        
        // $coupons = Coupon::with('couponPlan')->with(['couponReceipt' => function ($query) {
        //     return $query->whereHas('receipt', function ($query) {
        //         return $query->where('status', 1);
        //     })->with('receipt');
        // }])->get();
        return view('Backend.coupon.all')
            ->with('coupons', $coupons);
    }

    public function showCouponsForm()
    {
        $plans = Plan::with('expertLevel', 'duration')->where('active', '=', '1')->get();
        $categorical_plans = array();
        $categorical_plans['uncategorized'] = array();
        foreach ($plans as $plan) {
            if ($plan->expertLevel) {
                $expert_level_name = $plan->expertLevel->name;
                if (isset($categorical_plans[$expert_level_name])) {
                    array_push($categorical_plans[$expert_level_name], $plan);
                } else {
                    $categorical_plans[$expert_level_name] = array();
                    array_push($categorical_plans[$expert_level_name], $plan);
                }
            } else {
                array_push($categorical_plans['uncategorized'], $plan);
            }
        }
        return view("Backend.coupon.add")->with('categorical_plans', $categorical_plans);
    }

    public function storeCoupons(CouponSaveRequest $request)
    {
        $request->validated();
        $coupon = new Coupon();
        if (isset($request['code']) && isset($request['discount_percent'])) {
            $coupon->code = $request['code'];
            $coupon->discount_percent =  $request['discount_percent'];
            if (isset($request['description'])) {
                $coupon->description = $request['description'];
            }
            if (isset($request['max_uses']) && $request['max_uses'] >= 0) {
                $coupon->max_uses = $request['max_uses'];
            }
            if (isset($request['status'])) {
                $coupon->status = $request['status'];
            }
            if (isset($request['ends_at'])) {
                $coupon->expired_at = $request['ends_at'];
            }

            $coupon->save();
            $applied_plan_ids = $request['plans'];
            foreach ($applied_plan_ids as $plan_id) {
                $active_plan = new CouponPlan();
                $active_plan->plan_id = $plan_id;
                $active_plan->coupon_id = $coupon->id;
                $active_plan->save();
            }
            return redirect()->back()->with('success', 'Coupon created Successfully to view <a href="' . route('admin.coupon.show') . '"> click here</a>');
        } else {
            return redirect()->back()->with('error', 'unable to create');
        }
    }

    public function editCoupons($id)
    {
        $coupon = Coupon::findOrFail($id);
        if ($coupon->is_all == '0') {
            $applied_plans = [];
            foreach ($coupon->couponPlan as $plan) {
                array_push($applied_plans, $plan->plan_id);
            }
            $coupon->applied_plans = $applied_plans;
        }
        $plans = Plan::with('expertLevel', 'duration')->where('active', '=', '1')->get();
        $categorical_plans = array();
        $categorical_plans['uncategorized'] = array();
        foreach ($plans as $plan) {
            if ($plan->expertLevel) {
                $expert_level_name = $plan->expertLevel->name;
                if (isset($categorical_plans[$expert_level_name])) {
                    array_push($categorical_plans[$expert_level_name], $plan);
                } else {
                    $categorical_plans[$expert_level_name] = array();
                    array_push($categorical_plans[$expert_level_name], $plan);
                }
            } else {
                array_push($categorical_plans['uncategorized'], $plan);
            }
        }
        if ($coupon->expired_at) {
            $coupon->expired_at = Carbon::parse($coupon->expired_at)->toDateString();
        }
        return view("Backend.coupon.edit")
            ->with('coupon', $coupon)
            ->with('categorical_plans', $categorical_plans);
    }

    public function updateCoupons(Request $request, $id)
    {
        $coupon = Coupon::find($id);
        if ($coupon && isset($request['code']) && isset($request['discount_percent'])) {
            $coupon->code = $request['code'];
            $coupon->discount_percent =  $request['discount_percent'];
            if (isset($request['description'])) {
                $coupon->description = $request['description'];
            }
            if (isset($request['max_uses']) && $request['max_uses'] >= 0) {
                $coupon->max_uses = $request['max_uses'];
            }
            if (isset($request['status'])) {
                $coupon->status = $request['status'];
            }
            if (isset($request['ends_at'])) {
                $coupon->expired_at = $request['ends_at'];
            }

            if (!isset($request['plans'])) {
                $coupon->is_all = '1';
                $coupon->save();
            } else {
                $coupon->save();
                CouponPlan::where('coupon_id', $coupon->id)->delete();
                $applied_plan_ids = $request['plans'];
                foreach ($applied_plan_ids as $plan_id) {
                    $active_plan = new CouponPlan();
                    $active_plan->plan_id = $plan_id;
                    $active_plan->coupon_id = $coupon->id;
                    $active_plan->save();
                }
            }
            return redirect()->back()->with('status', 'updated Successfully');
        } else {
            return redirect()->back()->with('error', 'unable to update');
        }
    }
    public function deleteCoupons(Request $request)
    {
        if (isset($request['id'])) {
            $result = Coupon::destroy($request['id']);
            if ($result) {
                return redirect()->back()->with('status', 'deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'unable to delete');
            }
        } else {
            return redirect()->back()->with('error', 'unable to delete');
        }
    }

    public function viewCouponUser(Request $request)
    {
        $coupon_receipts = CouponReceipt::whereHas('receipt', function ($query) {
            $query->where('status', '=', '1');
        })->orWhere('receipt_id', '=', null)->with('user', 'coupon', 'receipt')->get();
        return view('Backend.coupon.coupon_user')
            ->with('coupon_receipts', $coupon_receipts);
    }

    public function couponExists(Request $request)
    {

        $result = Coupon::where('code', '=', $request['code'])->first();

        if ($result) {
            return response()->json(['error' => true, 'msg' => 'code already present',]);
        }
        return response()->json(['error' => false, 'msg' => 'code is valid']);
    }
}
