<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('user')->check() && auth('user')->user()->id == $this->user_id;
        dd($request);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required',
            'email'   => 'required|unique:users,email,'.$this->user_id,
            'useemail' => '',
            'subscribe' => '',
        ];
    }
}
