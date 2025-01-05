<?php

namespace App\Http\Requests;

use App\Rules\UserNameRule;
use Illuminate\Foundation\Http\FormRequest;

class UserNameRequest extends FormRequest
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
            'username' => ['bail', 'required', 'alpha_num', 'min:4', new UserNameRule],
        ];
    }
}
