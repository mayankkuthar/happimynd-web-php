<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('user')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'assessment_id' => ['required', 'exists:assessments,id'],
        ];
    }
}
