<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminEducationServiceRequest extends FormRequest
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
                'author' => ['required', 'exists:education_service_authors,id', 'string', 'max:20'],
                'thumbnail' => ['nullable','mimes:jpg,bmp,png,svg', 'dimensions:width=200,height=200'],
                'price' => ['required', 'numeric', 'gt:0'],
                'discounted_price' => ['required','numeric', 'gt:0'],
                'rating' => ['required','numeric', 'min:1', 'max:5'],
                'downloads' => ['required','numeric', 'gt:0'],
                'buy_link' => ['nullable', 'url'],
                'service_type' => ['required'],
                'publish_status' => ['required'],
            ];
        };
        return [
            //
            'title' => ['required','unique:other_services,title','string', 'max:50'],
            'author' => ['required', 'exists:education_service_authors,id', 'string'],
            'thumbnail' => ['required','mimes:jpg,bmp,png,svg', 'dimensions:width=200,height=200'],
            'price' => ['required', 'numeric', 'gt:0'],
            'discounted_price' => ['required','numeric', 'gt:0'],
            'rating' => ['required','numeric', 'min:1', 'max:5'],
            'downloads' => ['required','numeric', 'gt:0'],
            'buy_link' => ['nullable', 'url'],
            'service_type' => ['required'],
            'publish_status' => ['required'],
        ];
    }
}