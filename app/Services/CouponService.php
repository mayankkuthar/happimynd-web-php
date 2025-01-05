<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Plan;
use Carbon\Carbon;

class CouponService
{
    public function verifyCoupon($code, $plan_ids)
    {
        $returnData = ['error' => true, 'msg' => ''];
        $coupon = Coupon::activeCoupon()->where('code', $code)->first();
        if ($coupon) {
            if (!empty($coupon->max_uses) && $coupon->couponReceipt->count() > $coupon->max_uses) { //if coupon uses crosed specified usage limit
                $returnData['error'] = true;
                $returnData['msg'] = 'Coupon Expired';
                return $returnData;
            }
            if (!empty($coupon->expired_at) && $coupon->expired_at < Carbon::now()) {  // if expiry time is crossed
                $returnData['error'] = true;
                $returnData['msg'] = 'Coupon Expired';
                return $returnData;
            }
            $user = auth('user')->user();
            $couponReceipt = $coupon->couponReceipt()->where('user_id', $user->id)->first();
            if ($couponReceipt && $couponReceipt->isPurchased()) {
                $returnData['error'] = true;
                $returnData['msg'] = 'Coupon already used';
                return $returnData;
            }
            $couponPlans = $coupon->couponPlan;
            $coupon_plan_ids = $couponPlans->pluck('plan_id')->toArray();
            $tobeSelectedCouponPlanIds = array_intersect($couponPlans->pluck('plan_id')->toArray(), $plan_ids);
            if (count($tobeSelectedCouponPlanIds) == 0) {
                $plans = Plan::whereIn('id', $coupon_plan_ids)->get();
                $msgs = [];
                $count = 0;
                foreach ($plans as $plan) {
                    $count++;
                    if ($plan->package->name == "HappiTALK") {
                        $happiTalkMsg = 'HappiTALK with ' . $plan->printDuration() . ' of ' . $plan->expertLevel->name;
                        array_push($msgs, '<br>' . $count . ') ' . $happiTalkMsg);
                    } else {
                        array_push($msgs, '<br>' . $count . ') ' . $plan->package->name);
                    }
                }
                $returnData['msg'] = 'Following plans to be selected ' . implode(', ', $msgs);
            } else {
                $returnData['error'] = false;
                $returnData['msg'] = 'Coupon Applied';
                $returnData['discount'] = $coupon->discount_percent;
            }
        } else {
            $returnData['error'] = true;
            $returnData['msg'] = 'Invalid Coupon code';
        }
        return $returnData;
    }
}
