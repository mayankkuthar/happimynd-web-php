<?php

namespace App\Http\Requests;

use App\Rules\MatchOldPassword;
use App\Rules\VerifyCurrentPassword;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('user')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password' => ['required', 'string', new VerifyCurrentPassword],
            'password' => ['bail', 'required', 'string', 'min:6', new MatchOldPassword],
            'password_confirmation' => 'bail|required|same:password',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'Current password is required',
            'password.confirmed' => 'passwords does not match',
            '*.required' => 'This field is required',
            'password_confirmation.same' => "Password doesn't match.",
        ];
    }
}
