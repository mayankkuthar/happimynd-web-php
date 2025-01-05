<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\TokenVerify;

class TokenVerifyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'happimyndCode' => ['bail', 'required', 'string', 'exists:tokens,token', new TokenVerify($this->organization_id)],
            'organization_id' => 'bail|required|numeric|exists:organizations,id'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return[
            'happimyndCode.required' => 'Please enter Happimynd Code',
            'happimyndCode.exists' => 'Please enter a valid code',

            'organization_id.required' => 'Select your organization',
            'organization_id.numeric' => 'Select your organization',
            'organization_id.exists' => 'Organization doesn\'t exist',
        ];
    }
}
