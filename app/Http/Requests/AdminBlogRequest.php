<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminBlogRequest extends FormRequest
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
        if($this->getMethod() != 'POST'){
            $id = $this->request->get('post_id');
            // dd($id);
            return [
                //
                'title' => ['required','unique:posts,title,'.$id],
                'thumbnail' => ['mimes:jpg,bmp,png,svg'],
                'content' => ['required'],
                'media' => ['nullable','mimes:mp3,mp4,m1v,3gp', 'file','max:2000'],
                'accessibility' => ['required'],
                'publish_status' => ['required'],
            ]; 
        };
        return [
            //
            'title' => ['required', 'unique:posts'],
            'thumbnail' => ['nullable','mimes:jpg,bmp,png,svg'],
            'content' => ['required'],
            'media' => ['nullable','mimes:mp3,mp4,m1v,3gp', 'file','max:2000'],
            'accessibility' => ['required'],
            'publish_status' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'accessibility' => 'The Content type is required.',
            'publish_status' => 'The Publish field is required.',
        ];
    }
}
