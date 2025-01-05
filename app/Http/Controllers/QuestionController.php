<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Category;
use App\Models\OptionQuestion;
use App\Models\Language;

use App\Models\UserLanguage;




use App\Models\BatchCategory;
use App\Models\Option;
use App\Models\Question;
use App\Models\ReportCharacteristic;
use App\Services\ApiResponseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\ImportQuestions;
use Excel;

class QuestionController extends Controller
{

    private $apiResponseService;

    public function __construct(ApiResponseService $apiResponseService)
    {
        $this->apiResponseService = $apiResponseService;
    }
    public function modifyQuestions(Request $request)
    {
        $batches = Batch::whereHas('batchCategory.category')->withCount('questions')->get();
        return view("Backend.assessment.questions")
            ->with('batches', $batches);
    }

    public function getBatchCategoryQuestions(Request $request)
    {
        $questions = BatchCategory::with('questions.option')
            ->where('batch_id', $request->input('batch_id'))
            ->where('category_id', $request->input('category_id'))
            ->first();
        return $this->apiResponseService->success($questions);
    }

    public function updateQuestion(Request $request)
    {
        try {
            DB::beginTransaction();
            $responseData = [];
            // $formData['options'] = array();
            if (str_contains($request->input("check_new_question"), "new")) {
                //new question is being added
                // dd($request->all());
                $formData = $request->all();
                $indexes = array_keys($formData);
                foreach ($indexes as $index) {
                    $optionId = "";
                    if (str_contains($index, 'question-new')) {
                        $formData['question'] = $formData[$index];
                    }
                    if (str_contains($index, 'option-new')) {
                        $optionId = explode('-', $index)[2];
                        if (isset($formData['options'])) {
                            array_push($formData['options'], ['option' => $formData['option-new-' . $optionId], 'weightage' => $formData['score-new-' . $optionId]]);
                        } else {
                            $formData['options'] = [
                                [
                                    'option' => $formData['option-new-' . $optionId], 'weightage' => $formData['score-new-' . $optionId]
                                ]
                            ];
                        }
                    }
                }
                $batchCategoryId = BatchCategory::where('category_id', $formData['category_id'])->where('batch_id', $formData['batch_id'])->first(['id'])->id;
                $question = new Question([
                    "question" => $formData['question'],
                    'batch_category_id' => $batchCategoryId,
                    'category_id' => $formData['category_id']
                ]);
                $optionIds = [];
                foreach ($formData['options'] as $option) {
                    $optionId = Option::where('option', $option)->first(['id']);
                    if ($optionId) {
                        $optionId = $optionId->id;
                    } else {
                        $optionId = Option::create([
                            'option' => $option['option'],
                        ])->id;
                    }
                    array_push($optionIds, ['option_id' => $optionId, 'weightage' => intval($option['weightage'])]);
                }
                $question->save();
                $question->option()->sync($optionIds);
                $responseData['notify'] = [
                    'message' => "Question Added",
                    'type' => 'success'
                ];
            } else {
                //existing question is being modified
                if (!str_contains($request->input("check_new_question"), "new")) {
                    //new question is being added
                    // dd($request->all());
                    $formData = $request->all();
                    $indexes = array_keys($formData);
                    $question_id = null;
                    foreach ($indexes as $index) {
                        $optionId = "";
                        if (str_contains($index, 'question-')) {
                            $formData['question'] = $formData[$index];
                            $question_id = explode('-', $index)[1];
                        }
                        if (str_contains($index, 'option-new')) {
                            $optionId = explode('-', $index)[2];
                            if (isset($formData['options'])) {
                                array_push($formData['options'], ['option' => $formData['option-new-' . $optionId], 'weightage' => $formData['score-new-' . $optionId]]);
                            } else {
                                $formData['options'] = [
                                    [
                                        'option' => $formData['option-new-' . $optionId], 'weightage' => $formData['score-new-' . $optionId]
                                    ]
                                ];
                            }
                        } else if (str_contains($index, 'option-')) {
                            $optionId = explode('-', $index)[1];
                            if (isset($formData['options'])) {
                                array_push($formData['options'], ['option' => $formData['option-' . $optionId], 'weightage' => $formData['score-' . $optionId]]);
                            } else {
                                $formData['options'] = [
                                    [
                                        'option' => $formData['option-' . $optionId], 'weightage' => $formData['score-' . $optionId]
                                    ]
                                ];
                            }
                        }
                    }
                    $batchCategoryId = BatchCategory::where(
                        'category_id',
                        $formData['category_id']
                    )->where('batch_id', $formData['batch_id'])->first(['id'])->id;
                    $question = Question::find($question_id);
                    $question->question = $formData['question'];
                    $optionIds = [];
                    foreach ($formData['options'] as $option) {
                        $optionId = Option::where('option', $option)->first(['id']);
                        if ($optionId) {
                            $optionId = $optionId->id;
                        } else {
                            $optionId = Option::create([
                                'option' => $option['option'],
                            ])->id;
                        }
                        $optionIds += [$optionId => ['weightage' => intval($option['weightage'])]];
                    }
                    $question->save();
                    $question->option()->sync($optionIds);
                    $responseData['notify'] = [
                        'message' => "Question Updated",
                        'type' => 'success'
                    ];
                }
            }
            DB::commit();
            return $this->apiResponseService->success($responseData);
        } catch (Exception $e) {
            // Woopsy
            \Log::critical($e->getMessage());
            DB::rollBack();
            return $this->apiResponseService->error(['notify' => [
                'type' => 'danger',
                'message' => 'Problem occurred, please contact developer'
            ]]);
        }
    }

    public function deleteQuestion(Request $request)
    {
        $isDeleted = Question::destroy($request->input('question_id'));
        if ($isDeleted) {
            return $this->apiResponseService->success(
                [
                    'notify' => [
                        'type' => 'success',
                        'message' => 'Question deleted'
                    ]
                ]
            );
        } else {
            return $this->apiResponseService->error(['notify failed deleting question']);
        }
    }


    public function importQuestions(Request $request){
        if($request->isMethod('GET')){
            return view('import-questions');
        }
        if($request->isMethod('POST')){

            $message = [
                    'import_question.required' => 'Please select file to import.',
                    'import_question.mimes' => 'Only .xls and .xlsx format file allowed.',
                ];

            $request->validate([
                'import_question' => 'required|mimes:xls,xlsx'
            ],$message);

            $array = Excel::toArray(new ImportQuestions, request()->file('import_question'));
            $data_file = $array[0];
            if(count($data_file) <= 0){
                  return back()->with("error" , "File is empty.");
            }

            $my_key_references = ['language','question','batch_id' , 'category_id','option1','score1','option2','score2','option3','score3','option4','score4','option5','score5'];
            $file_key_only = array_keys($data_file[0]);
            $array_difference = array_diff($my_key_references, $file_key_only);
            $array_difference = count($array_difference);
            if($array_difference > 0){
                return back()->with("error" , "Mismatch key in excel file.");
                // return "keys missmatch";
            }

            $count_file = count($data_file);
            
            $line=2;

            for($i=0 ; $i < $count_file; $i++){

                if($data_file[$i]["language"] == null){
                    return back()->with('error' ,'Please insert language at line '.$line);
                }

                $is_valid_language = UserLanguage::where('name' , strtolower($data_file[$i]["language"]))->first();
                $all_user_langauges  = UserLanguage::pluck('name');
                if(!$is_valid_language){
                    return back()->with('error' ,"Please select valid language at line ".$line.'. Language should be in '.$all_user_langauges);
                }

                if($data_file[$i]["question"] == null){
                    return back()->with('error' ,'Please insert question at line '.$line);
                }

                if($data_file[$i]["batch_id"] == null){
                    return back()->with('error' ,'Please insert Batch ID at line '.$line);
                }

                if($data_file[$i]["category_id"] == null){
                    return back()->with('error' ,'Please insert category ID at line '.$line);
                }

                if($data_file[$i]["option1"]){
                    if($data_file[$i]["score1"] == null){
                        return back()->with('error' ,'Score1 is mendatory at line '.$line);
                    }
                }else{
                    return back()->with('error' ,'Option1 is mendatory at line '.$line);
                }

                for ($j=1; $j <= 5; $j++) { 

                    $option = $data_file[$i]["option".$j];
                    $score =  $data_file[$i]["score".$j];

                    if($option != null){
                        if($score == null){
                            return back()->with('error' ,'Please define the score of option at line '.$line);
                        }
                        if($score != null && $score > 5){
                            return back()->with('error' ,'Score should be less than 5 at line '.$line);
                        }
                        if($score != null && $score < 0){
                            return back()->with('error' ,'Score should be greater than 0 at line '.$line);
                        }
                    }
                }

                $check_batch = Batch::where("id" , $data_file[$i]["batch_id"])->where('deleted_at' , null)->first();
                if($check_batch == false){
                    return back()->with('error' ,"Invalid Batch ID at line".$line);
                }

                $check_category = Category::where("id" , $data_file[$i]["category_id"])->where('deleted_at' , null)->first();
                if($check_category == false){
                    return back()->with('error' ,"Invalid Category ID at line".$line);
                }

                $category_allocate_to_batch = BatchCategory::where("category_id" , $data_file[$i]["category_id"])->where("batch_id" , $data_file[$i]["batch_id"])->where('deleted_at' , null)->first();
                if($category_allocate_to_batch == false){
                    return back()->with('error' ,"Category not allocated to batch at line".$line);
                }

            $line = $line+1;

            }

            // return 5;

            for($i=0 ; $i < $count_file; $i++){

                $batch_category_id = BatchCategory::where("category_id" , $data_file[$i]["category_id"])->where("batch_id" , $data_file[$i]["batch_id"])->first();
                $category_id = $data_file[$i]["category_id"];
                $question = $data_file[$i]["question"];
                $language = strtolower($data_file[$i]["language"]);

                $data =[ 
                    'language' => $language,
                    'question' => $question,
                    'category_id' => $category_id,
                    'batch_category_id' => $batch_category_id->id,
                ];
                $create_question = Question::create($data);


                for ($j=1; $j <= 5; $j++) { 

                    $option = $data_file[$i]["option".$j];
                    $score =  $data_file[$i]["score".$j];
                    
                    if($option != null){

                        $is_option_already_exist = Option::where('option' , $option)->first();
                        if($is_option_already_exist){
                            $option_id = $is_option_already_exist->id;
                        }else{
                            $create_option = Option::create(['option' => $option]);
                            $option_id = $create_option->id;
                        }

                        $question_option_data = [
                            'question_id' => $create_question->id,
                            'option_id' => $option_id,
                            'weightage' => $score,
                        ];
                        OptionQuestion::create($question_option_data);
                    }

                }

            }

            return back()->with('success' ,'Question import successfully');
        }
    }


    public function batchCategoryIds(Request $request){
        $batches = Batch::where('deleted_at' , null)->get();
        return view('batch_ids')->with('batches' , $batches);
    }

    public function viewCategoryIds(Request $request ,$id){

        $batch_category_ids = BatchCategory::where('batch_id' , $id)->pluck('category_id');
        $categories = Category::whereIn('id' , $batch_category_ids)->where('deleted_at' , null)->get();

        return view('category_ids')->with('categories' , $categories);

    }


}





















