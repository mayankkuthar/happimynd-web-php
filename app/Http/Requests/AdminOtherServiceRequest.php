<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminOtherServiceRequest extends FormRequest
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
        if($this->getMethod() == 'POST' && is_null($this->request->get('id')) == false ){
            $id = $this->request->get('id');
            return [
                //
                'title' => ['required','unique:other_services,title,'.$id, 'string', 'max:20'],
                'description' => ['required', 'string', 'max:50'],
                'thumbnail' => ['nullable','mimes:jpg,bmp,png,svg','dimensions:width=200,height=200'],
                'price' => ['required','numeric', 'gt:0'],
                'discount' => ['required','numeric', 'gt:0', 'max:100'],
                'buy_link' => ['nullable', 'url'],
                'coupon' => ['nullable', 'string'],
                'service_type' => ['required'],
                'publish_status' => ['required'],
            ];
        };
        return [
            //
            'title' => ['required','unique:other_services,title', 'string', 'max:20'],
            'description' => ['required','max:50', 'string'],
            'thumbnail' => ['required','mimes:jpg,bmp,png,svg', 'dimensions:width=200,height=200'],
            'price' => ['required','numeric', 'gt:0'],
            'discount' => ['required','numeric', 'gt:0','max:100'],
            'buy_link' => ['nullable', 'url'],
            'coupon' => ['nullable', 'string'],
            'service_type' => ['required'],
            'publish_status' => ['required'],
        ];
    }
}