<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscribersRequest extends FormRequest
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
            //
            'other_service' => ['required', 'exists:other_services,id'],
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'mobile' => ['required', 'max:10'],
        ];
    }
}