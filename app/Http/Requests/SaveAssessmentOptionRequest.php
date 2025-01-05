<?php

namespace App\Http\Requests;

use App\Models\Assessment;
use Illuminate\Foundation\Http\FormRequest;

class SaveAssessmentOptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $assessment = Assessment::find($this->assessment_id);
        if($assessment){
            return auth('user')->check() && auth('user')->user()->id == $assessment->user_id;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'assessment_id' => ['required'],
            'option_question_id' => ['required', 'exists:option_questions,id'],
        ];
    }
}
