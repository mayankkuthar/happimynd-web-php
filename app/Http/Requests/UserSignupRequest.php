<?php

namespace App\Http\Requests;

use App\Models\UserProfile;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\TokenVerify;

class UserSignupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !auth('user')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // die("====");
        $rules = [
            'signup_type' => 'nullable',
            'nickname' => ['bail', 'required', 'alpha_num', 'min:4', 'max:255'],
            'age' => ['required', 'numeric', 'gt:17', 'digits_between:1,3'],
            'gender' => ['required', Rule::in(config('constants.gender'))],
            'password' => 'bail|required|string|min:6',
            'password_confirmation' => 'bail|required|same:password',
            'organization_id' => 'bail|required_if:signup_type,=,null|numeric|exists:organizations,id',
            'happimyndCode' => ['bail', 'required_if:signup_type,=,null', 'exists:tokens,token', new TokenVerify($this->organization_id)],
            'user_profile_id' => ['nullable', 'required_with:happimyndCode', "exists:user_profiles,id"],
        ];

        // check if request is from an under-age remove the rule element/ if under age condition changes change this condition
        if ($this->request->get('under_age') == 1) {
            $age_rules = $rules['age'];
            $underAgeKey = array_search("gt:17", $age_rules);
            unset($age_rules[$underAgeKey]);
            $rules['age'] = $age_rules;
        }
        // echo $this->signup_type;
        // die();
        if (strtolower($this->signup_type) != 'campaign') {
            //username is already generated for campaign so no need to validate username
            $rules = array_merge($rules, (new UserNameRequest)->rules());
        }
        return $rules;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            '*.required' => ':attribute is required.',
            '*.digits_between' => 'Please enter valid :attribute.',
            'age.gt' => 'Age must be greater than or equal to 18',
            'age.*' => 'Invalid age.',
            'password_confirmation.same' => "Password doesn't match.",
        ];
    }
}
