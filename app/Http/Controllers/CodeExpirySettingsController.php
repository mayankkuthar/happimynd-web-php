<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

/**
 * Admin Panel: default validity (in days) for HappiMynd codes and coupons created without an expiry date.
 * Changing a value only affects codes created afterwards.
 */
class CodeExpirySettingsController extends Controller
{
    private const TOKEN_ROLES = ['admin', 'super-admin', 'happimynd-code'];
    private const COUPON_ROLES = ['admin', 'super-admin', 'coupen'];

    private function permissions(): array
    {
        $admin = auth('admin')->user();
        $canEditToken = $admin && $admin->hasAnyRole(self::TOKEN_ROLES);
        $canEditCoupon = $admin && $admin->hasAnyRole(self::COUPON_ROLES);
        if (!$canEditToken && !$canEditCoupon) {
            abort(403);
        }
        return [$canEditToken, $canEditCoupon];
    }

    public function edit()
    {
        [$canEditToken, $canEditCoupon] = $this->permissions();
        return view('Backend.codeExpirySettings', [
            'canEditToken' => $canEditToken,
            'canEditCoupon' => $canEditCoupon,
            'tokenDays' => Setting::tokenDefaultValidityDays(),
            'couponDays' => Setting::couponDefaultValidityDays(),
        ]);
    }

    public function update(Request $request)
    {
        [$canEditToken, $canEditCoupon] = $this->permissions();
        $rules = [];
        if ($canEditToken) {
            $rules['token_days'] = 'required|integer|min:1|max:3650';
        }
        if ($canEditCoupon) {
            $rules['coupon_days'] = 'required|integer|min:1|max:3650';
        }
        $data = $request->validate($rules, [], [
            'token_days' => 'HappiMynd code validity',
            'coupon_days' => 'coupon validity',
        ]);

        if ($canEditToken) {
            Setting::setValue(Setting::TOKEN_DEFAULT_VALIDITY_DAYS, (int) $data['token_days']);
        }
        if ($canEditCoupon) {
            Setting::setValue(Setting::COUPON_DEFAULT_VALIDITY_DAYS, (int) $data['coupon_days']);
        }

        return redirect()->route('admin.codeExpirySettings')->with('success', 'Default expiry updated. It applies to codes created from now on.');
    }
}
