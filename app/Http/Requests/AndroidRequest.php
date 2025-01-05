<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AndroidRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return (auth('admin')->check() && auth('admin')->user()->hasAnyRole(['super-admin', 'admin']));
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
            'androidLink' =>['required','url']
        ];
    }
}
