<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetQuestionRequest;
use App\Http\Requests\SaveAssessmentOptionRequest;
use App\Http\Requests\CalltimeRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\OptionQuestion;
use App\Models\ServiceImage;
use App\Models\AssessmentApprove;
use App\Services\ApiResponseService;
use App\Services\AssessmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Ramsey\Collection\Collection;
use App\Models\User;

class AssessmentController extends Controller
{

    public function __construct(ApiResponseService $apiResponse, AssessmentService $assessmentService)
    {
        $this->apiResponse = $apiResponse;
        $this->assessmentService = $assessmentService;
    }
    public function startAssessment(Request $request)
    {
        return $this->apiResponse->success([
            'assessment_id' => $this->assessmentService
                ->forUser(auth('user')->user()->id)
                ->initiateAssessment()->assessmentId
        ]);
    }

    public function getQuestions(GetQuestionRequest $request)
    {
        $requestData = $request->validated();
        $assessmentId = $requestData['assessment_id'];
        return QuestionResource::collection(
            $this->assessmentService
                ->forAssessment($assessmentId)
                ->getRemainingQuestions()
        )->additional(['perPage' => $this->assessmentService->questionsPerPage, 'answered' => $this->assessmentService->answeredQuestionsCount, 'total' => $this->assessmentService->totalQuestionsCount, 'current_page' => $this->assessmentService->getPageNumber()]);
    }

    public function saveAssessmentOption(SaveAssessmentOptionRequest $request)
    {
        $requestData = $request->validated();
        $assessmentId = $requestData['assessment_id'];
        $optionQuestionId = $requestData['option_question_id'];
        return $this->apiResponse->success(
            $this->assessmentService
                ->saveAssessmentOption($assessmentId, $optionQuestionId)
        );
    }

    public function calculateAssessmentScore(Request $request)
    {

        $assessmentId = $request->assessment_id;
        $assessment = Assessment::find($assessmentId);
        $user = User::where('id' , $assessment->user_id)->first();
        // if($user->platform == 'mobile'){
        //     if (is_null($assessment)) {
        //         return response()->json(['status'=>'false' , 'message'=>'Invalid assessment id.']);
        //     }
        //     $this->assessmentService->forAssessment($assessmentId)->calculateScoreApp();
        //     $WOL_object = [];
        //     foreach ($this->assessmentService->scoreArray as $scoreArray) {
        //         // if ($scoreArray['WOL_representation'] != 'none') {

        //             if(array_key_exists('category_in_report' , $scoreArray) && array_key_exists('WOL_fill_color' , $scoreArray) && array_key_exists('WOL_fill_area' , $scoreArray) && array_key_exists('WOL_representation' , $scoreArray)){
        //                 $category_in_report = $scoreArray['category_in_report'];
        //                 $color = $scoreArray['WOL_fill_color'];
        //                 $value = $scoreArray['WOL_fill_area'];
        //                 array_push(
        //                     $WOL_object,
        //                     [
        //                         'range' =>  $category_in_report, 'data' =>  [
        //                             [
        //                                 'category' => $category_in_report,
        //                                 "value" => $value,
        //                                 'color' =>  $color,
        //                             ]
        //                         ]
        //                     ]
        //                 );
        //             }
        //         // }
        //     }
        //     $data=ServiceImage::all();
        //     $WOL_object = json_encode($WOL_object);
        //     return view('Frontend/report/report_app')
        //         ->with('score', $this->assessmentService->report['score'])
        //         ->with('WOL_object', $WOL_object)
        //         ->with('report', $this->assessmentService->report)->with('data',$data);
        // }
        



        // $assessmentId = base64_decode($assessmentId);
        $assessmentId = $request->input('assessment_id');
        $assessment = Assessment::find($assessmentId);
        if (is_null($assessment)) {
            return redirect(route('user.dashboard'));
        }
        $this->assessmentService->forAssessment($assessmentId)->calculateScore();
        $WOL_object = [];
        foreach ($this->assessmentService->scoreArray as $scoreArray) {
            // if ($scoreArray['WOL_representation'] != 'none') {

                if(array_key_exists('category_in_report' , $scoreArray) && array_key_exists('WOL_fill_color' , $scoreArray) && array_key_exists('WOL_fill_area' , $scoreArray) && array_key_exists('WOL_representation' , $scoreArray)){
                    $category_in_report = $scoreArray['category_in_report'];
                    $color = $scoreArray['WOL_fill_color'];
                    $value = $scoreArray['WOL_fill_area'];
                    array_push(
                        $WOL_object,
                        [
                            'range' =>  $category_in_report, 'data' =>  [
                                [
                                    'category' => $category_in_report,
                                    "value" => $value,
                                    'color' =>  $color,
                                ]
                            ]
                        ]
                    );
                }
            // }
        }
        $data=ServiceImage::all();
        $WOL_object = json_encode($WOL_object);
        return view('Frontend/report/report')
            ->with('score', $this->assessmentService->report['score'])
            ->with('WOL_object', $WOL_object)
            ->with('report', $this->assessmentService->report)->with('data',$data);
    }

    public function botAssessment(Request $request)
    {
        $this->assessmentService->forUser($request->input('user_id'))->completeBotAssessment();
        return "done";
    }

    public function reportPreview(Request $request)
    {
        if ($request->input('assessment_id') != "") {
            $this->assessmentService->forAssessment($request->input('assessment_id'))->calculateScore();
            return view('Frontend/report/reportPreview')
                ->with('score', $this->assessmentService->report['score'])
                ->with('report', $this->assessmentService->report);
        }
        return "Report Preview";
    }

    public function updateCalltime(CalltimeRequest $request)
    {
        $formdata = $request->validated();
        $slot = $formdata['slot'];
        $date = Carbon::createFromFormat('m-d-Y', $formdata['date'])->format('Y-m-d');
        $assessmentId = $formdata['assessment_id'];
        $assesmentApprove = AssessmentApprove::updateOrCreate(['assessment_id' => $assessmentId], [
            'slot' => $slot,
            'available_date' => $date,
            'call_option' => $formdata['call_option'],
        ]);
        $user = auth('user')->user();
        $user->addReportReadingToBitrix();
        return $this->apiResponse->success("Successfully, call time updated.!");
    }



    // public function calculateAssessmentScoreApp(Request $request , $assessment_id)
    // {
    //     // $assessmentId = base64_decode($assessmentId);
    //     $assessmentId = $assessment_id;
    //     $assessment = Assessment::find($assessmentId);
    //     if (is_null($assessment)) {
    //         return redirect(route('user.dashboard'));
    //     }
    //     $this->assessmentService->forAssessment($assessmentId)->calculateScoreApp();
    //     $WOL_object = [];
    //     foreach ($this->assessmentService->scoreArray as $scoreArray) {
    //         // if ($scoreArray['WOL_representation'] != 'none') {

    //             if(array_key_exists('category_in_report' , $scoreArray) && array_key_exists('WOL_fill_color' , $scoreArray) && array_key_exists('WOL_fill_area' , $scoreArray) && array_key_exists('WOL_representation' , $scoreArray)){
    //                 $category_in_report = $scoreArray['category_in_report'];
    //                 $color = $scoreArray['WOL_fill_color'];
    //                 $value = $scoreArray['WOL_fill_area'];
    //                 array_push(
    //                     $WOL_object,
    //                     [
    //                         'range' =>  $category_in_report, 'data' =>  [
    //                             [
    //                                 'category' => $category_in_report,
    //                                 "value" => $value,
    //                                 'color' =>  $color,
    //                             ]
    //                         ]
    //                     ]
    //                 );
    //             }
    //         // }
    //     }
    //     $data=ServiceImage::all();
    //     $WOL_object = json_encode($WOL_object);
    //     return view('Frontend/report/report_app')
    //         ->with('score', $this->assessmentService->report['score'])
    //         ->with('WOL_object', $WOL_object)
    //         ->with('report', $this->assessmentService->report)->with('data',$data);
    // }

}
