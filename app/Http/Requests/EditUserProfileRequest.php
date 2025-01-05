<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\CheckIfNullInDB;
use Illuminate\Foundation\Http\FormRequest;

class EditUserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('user')->check() && auth('user')->user()->id == $this->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'nickname' => 'required',
            'user_id' => 'required|exists:users,id',
            'email' => 'nullable|email|unique:users,email,'.$this->user_id,
            // "mobile" => "nullable|digits_between:9,11",
            "mobile" => "nullable",
            "country_id" => "nullable|required_with:mobile",
            'age' => 'required|numeric|gt:17|digits_between:1,3',
            'avatar' => ['bail', new CheckIfNullInDB(new User, 'avatar', 'Please select avatar image')]
        ];
        return array_merge($rules, (new UserNameRequest)->rules());
    }

    public function messages()
    {
        return [
            'mobile.*' => 'Invalid mobile number',
            'age.gt' => 'Age must be greater than or equal to 18',
            'age.*' => 'Invalid age',
            'avatar.*' => 'please select avatar',
        ];
    }
}
