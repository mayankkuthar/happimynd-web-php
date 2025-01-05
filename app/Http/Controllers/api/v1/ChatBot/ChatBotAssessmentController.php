<?php

namespace App\Http\Controllers\api\v1\ChatBot;

use App\Http\Controllers\Controller;
use App\Models\ChatBot\ChatBotAssessment;
use App\Models\ChatBot\ChatBotCategory;
use App\Models\ChatBot\ChatBotOption;
use App\Models\ChatBot\ChatBotQuestion;
use App\Models\ChatBot\ChatBotReportCharacteristic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatBotAssessmentController extends Controller
{
    /**
     * Categories.
     */
    public function categories(Request $request)
    {
        $categories = ChatBotCategory::where($request->only(['id', 'name', 'calculation_step_macro']))->get();

        return [
            'status' => 'Success',
            'message' => 'Chat bot categories retrieved successfully.',
            'categories' => $categories,
        ];
    }

    /**
     * Questions.
     */
    public function questions(Request $request)
    {
        $questions = ChatBotQuestion::with('options')->where($request->only(['id', 'language', 'chat_bot_category_id']))->get();

        return [
            'status' => 'Success',
            'message' => 'Chat bot questions retrieved successfully.',
            'questions' => $questions,
        ];
    }

    /**
     * Questions.
     */
    public function options(Request $request)
    {
        $options = ChatBotOption::with('question')->where($request->only(['id', 'chat_bot_question_id', 'option', 'score']))->get();

        return [
            'status' => 'Success',
            'message' => 'Chat bot options retrieved successfully.',
            'options' => $options,
        ];
    }

    /**
     * Report characteristics
     */
    public function reportCharacteristics(Request $request)
    {
        $reportCharacteristics = ChatBotReportCharacteristic::where($request->only('id', 'chat_bot_category_id'))->get();

        return [
            'status' => 'Success',
            'message' => 'Chat bot report characteristics retrieved successfully.',
            'report_characteristics' => $reportCharacteristics,
        ];
    }

    /**
     * Assessments.
     */
    public function assessments(Request $request)
    {
        $assessments = ChatBotAssessment::where($request->only(['id', 'user_id', 'chat_bot_category_id', 'score']))->get();

        return [
            'status' => 'Success',
            'message' => 'Chat bot assessments retrieved successfully.',
            'assessments' => $assessments,
        ];
    }

    /**
     * Assessments.
     */
    public function addAssessment(Request $request)
    {
        $data = $request->only(['user_id', 'chat_bot_category_id', 'score']);

        $rules = [
            'user_id' => 'required',
            'chat_bot_category_id' => 'required',
            'score' => 'required|integer',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return [
                'status' => 'Error',
                'message' => $validator->errors()->first(),
            ];
        }

        $assessment = ChatBotAssessment::create($validator->validated());

        return [
            'status' => 'Success',
            'message' => 'Chat bot assessment addedd successfully.',
            'assessment' => $assessment,
        ];
    }
}
