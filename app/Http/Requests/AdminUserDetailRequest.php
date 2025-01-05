<?php

namespace App\Http\Requests;

use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($user = auth('admin')->user()) {
            return $user->hasAnyRole(['super-admin', 'admin']);
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
        //TODO:: rule:: if admin user exists then password is not required
        return [
            "first_name" => "required|alpha|max:255",
            "last_name" => "max:255|nullable|alpha",
            "email" => "required|email|unique:admins",
            "gender" => ["nullable", Rule::in(['male', 'female', 'other'])],
            'password' => 'required|string|min:4',
            "mobile" => "nullable|size:10",
            "account_status" => ["nullable", Rule::in(['active', 'blocked'])],
            "roles" => "required",
        ];
    }
}
