<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOrganizationRequest extends FormRequest
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
            'name' => 'required|unique:organizations,name,NULL,id,deleted_at,NULL',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'Organization already exists'
        ];
    }
}
