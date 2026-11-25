<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponSaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'bail|required|unique:coupons,code',
            'discount_percent' => 'bail|required|gte:0',
            'max_uses' => 'bail|nullable|numeric|gt:0',
            'description' => 'bail|required',
            'plans' => 'bail|required',
            // 'any_plan' => 'bail|required_if:plans,=,null',
            // Empty = expires after the default validity days set in Code Expiry Settings
            'ends_at' => 'bail|nullable|date',

        ];
    }

    public function messages()
    {
        return [
            'code.unique'  => 'Code already exists',
            'dicount_percent.required' => 'Discount is required',
            'discount_percent.gt' => 'Discoint Should be greater',
        ];
    }
}
