<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminUserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($user = auth('admin')->user()) {
            return $user->hasAnyRole(['admin', 'super-admin']);
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "user_id" => 'required|exists:admins,id',
            "first_name" => "required|max:255|alpha",
            "last_name" => "max:255|nullable|alpha",
            "email" => "required|email",
            "gender" => ['required', Rule::in(['male', 'female', 'other'])],
            'password' => 'nullable|string|min:4',
            "mobile" => "nullable|size:10",
            "account_status" => [Rule::in(['active', 'blocked'])],
            "roles" => "required",
        ];
    }
}
