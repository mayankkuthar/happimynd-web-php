<?php

namespace App\Services;

use App\Services\Calculator;
use App\Http\Resources\QuestionCollection;
use App\Http\Resources\QuestionResource;
use App\Jobs\GenerateScreeningReport;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentApprove;
use App\Models\Category;
use App\Models\OptionQuestion;
use App\Models\Question;
use App\Models\AssessmentScore;
use App\Models\Batch;
use App\Models\BatchCategory;
use App\Models\BundleStatus;
use App\Models\ReportCharacteristic;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Auth;

class AssessmentService
{
    public $assessmentId, $assessmentAnswers, $assessment;
    public $profileType;
    public $assessmentQuestions;
    public $depressionScore;
    public $userId;
    public $user;
    public $scoreArray = array();
    public $answeredQuestionsCount;
    public $totalQuestionsCount; //excluding personality questions(59)
    public $questionsPerPage; //questionscout/5
    public $batch;

    public function setDefaultBatch()
    {
        $this->batch = $this->getDefaultBatch();
    }

    public function getDefaultBatch()
    {
        return Batch::where('name', 'salaried')->first();
    }
    /**
     * return user type of logged in user or started assessment
     *
     * @return Model
     */
    public function getProfileType()
    {
        if ($this->assessment) {
            if ($this->profileType == null && $this->assessment->started_at <= "2021-05-12 22:05:33") {
                $this->profileType = UserProfile::where('name', 'salaried')->first();
                if ($this->profileType != null) $this->setBatch();
            }
        }
        if (is_null($this->profileType)) {
            $this->profileType = $this->user->profileType;
            if ($this->profileType != null) $this->setBatch();
        }
        if (is_null($this->user->profileType)) {
            $this->profileType = UserProfile::where('name', 'salaried')->first();
            if ($this->profileType != null) $this->setBatch();
        }
        return $this->profileType;
    }

    public function setbatch()
    {
        if (!$this->profileType) {
            $this->getProfileType();
        }
        //if user has already given half assessment of old batch then continue with same if that batch exists
        if ($this->assessment && $this->assessment->batch) {
            $this->batch = $this->assessment->batch;
        } else {
            //if assessment isn't started use latest batch
            $this->batch = $this->user->profileType->batch()->latest()->first();
        }
        if (is_null($this->batch)) {
            //if batch is deleted
            if ($this->profileType->batch && $this->profileType->batch->first()) {
                // if same profile has batch created by admin use that

                $this->batch = $this->profileType->batch()->latest('created_at')->first();
            } else {
                //else use default batch
                $this->setDefaultBatch();
            }
        }

        $this->setBatchCategory();
        return $this->batch;
    }

    public function setBatchCategory()
    {
        if ($this->batch->batchCategory->count() == 0) {
            $this->batchCategory = $this->getDefaultBatch()->batchCategory;
        } else {
            $this->batchCategory = $this->batch->batchCategory;
        }
        return $this;
    }

    public function getBatchCategoryInstance()
    {
        if ($this->batch->batchCategory->count() == 0) {
            return $this->batchCategory = $this->getDefaultBatch()->batchCategory();
        } else {
            return $this->batchCategory = $this->batch->batchCategory();
        }
    }

    public function __construct()
    {

        // $this->totalQuestionsCount = Question::whereHas('category', function ($query) {
        //     $query->whereIn('id', $this->profileType->batchCategory->pluck('category_id')->toArray());
        // })->get()->count();
        // // $this->answeredQuestionsCount = AssessmentAnswer::where('assessment_id', $this->assessmentId)->get()->count();
        // $this->questionsPerPage = ceil($this->totalQuestionsCount / 5);
    }
    public function getPageNumber()
    {
        //tobe removed after including remaining questions(this if conditions is just a hack)
        if ($this->answeredQuestionsCount == $this->totalQuestionsCount) return 5;

        $page_no = $this->answeredQuestionsCount / $this->questionsPerPage;
        if ($this->answeredQuestionsCount % $this->questionsPerPage === 0) {
            return $page_no + 1;
        }
        return ceil($page_no);
    }

    public function forAssessment($assessmentId)
    {
        $this->assessmentId = $assessmentId;
        $this->assessment = Assessment::find($assessmentId);
        $this->user = $this->assessment->user;
        $this->assessmentAnswers = AssessmentAnswer::with('optionQuestion.question.category', 'optionQuestion.option')->where('assessment_id', $this->assessmentId)->get();
        $this->getProfileType();
        $categoryIds = $this->batchCategory->pluck('category_id')->toArray();
        $this->totalQuestionsCount = $this->batch->batchCategory()->whereHas('category')->withCount('questions_english')->get();
        $this->totalQuestionsCount = $this->totalQuestionsCount->sum('questions_english_count');
        $this->answeredQuestionsCount = AssessmentAnswer::where('assessment_id', $this->assessmentId)->get()->count();
        $this->questionsPerPage = ceil($this->totalQuestionsCount / 5);
        return $this;
    }

    public function forUser($userId)
    {
        $this->userId = $userId;
        $this->assessmentId = $this->startAssessment($userId);
        $this->user = User::find($userId);
        return $this;
    }

    public function completeBotAssessment()
    {
        $this->startAssessment($this->userId);
        $this->questionsPerPage = null;
        $this->botAlgo($this->assessmentId);
        $this->endAssessment($this->assessmentId);
        $this->forAssessment($this->assessment->id);
        $this->calculateScore();
        $this->createOrUpdateScore($this->assessment, $this->answeredQuestionsCount);
        // dump('assesment_id=' . $this->assessmentId);
        return $this;
    }

    public function isAssessmentCompleted()
    {
        if ($this->assessment->ended_at == null && $this->totalQuestionsCount == $this->answeredQuestionsCount) {
            $this->endAssessment($this->assessmentId);
            $this->assessment->refresh();
        }
        return $this->assessment->ended_at != null;
    }

    public function calculateScore()
    {
        if ($this->isAssessmentCompleted()) {
            $this->scoreAlgo($this->assessmentId);
        }
        return $this;
    }

    public function initiateAssessment()
    {
        $this->startAssessment($this->userId);
        return $this;
    }

    public function startAssessment($user_id)
    {
        $assessment = Assessment::where('user_id', $user_id)->whereNull('ended_at')->first();
        if ($assessment) {
            $this->forAssessment($assessment->id);
            $this->assessment = $assessment;
            return $assessment->id;
        }
        $this->user = User::find($user_id);
        $this->getProfileType();
        $this->setBatch();
        $assessment = Assessment::create([
            'user_id' => $user_id,
            'started_at' => Carbon::now(),
            'batch_id' => $this->batch->id
        ]);
        $this->assessmentId = $assessment->id;
        $this->assessment = $assessment;
        return $assessment->id;
    }

    public function endAssessment($assessmentId)
    {
        $assessment = Assessment::find($assessmentId);
        if ($assessmentId) {
            $assessment->ended_at = Carbon::now();
            if ($assessment->save()) {
                AssessmentApprove::updateOrCreate(['assessment_id' => $assessmentId], [
                    'slot' => null,
                ]);
                $bundleStatus = BundleStatus::where('user_id', $this->userId)->where('percentage_covered', '!=', 100.00)->whereHas('plans.package', function ($query) {
                    $query->where('name', 'HappiLIFE Screening');
                })->first();
                $this->user->generateReportAndSendMail();
                if ($bundleStatus) {
                    (new PackageService)->bundlePlanCompleted($bundleStatus->id);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * assessment will be completed for user
     *
     * @param [int] $assessmentId
     * @return void
     */
    protected function botAlgo($assessmentId)
    {
        $questions = $this->getQuestions($assessmentId)->toArray();
        $options = [];
        $now = Carbon::now()->toDateTimeString();
        // dd($questions);
        foreach ($questions as $question) {
            $options[] = [
                'option_question_id' => $question['option'][mt_rand(0, (count($question['option']) - 1))]['pivot']['id'],
                'created_at' => $now,
                'updated_at' => $now,
                'assessment_id' => $this->assessmentId,
            ];
        }
        // dd($options);
        AssessmentAnswer::insert($options);
    }

    /**
     * save option selected by user in assessment and end assessment if all are answered and update bundle status
     *
     * @param [int] $assessmentId
     * @param [int] $optionQuestionId
     * @return void
     */
    public function saveAssessmentOption($assessmentId, $optionQuestionId)
    {
        $this->forAssessment($assessmentId);
        $optionQuestion = OptionQuestion::with('question.option')->find($optionQuestionId);
        $questionOptions = $optionQuestion->question->option->pluck('pivot')->pluck('id')->toArray();
        $answeredOptions = AssessmentAnswer::where('assessment_id', $assessmentId)->get()->pluck('option_question_id')->toArray();
        if (count(array_intersect($answeredOptions, $questionOptions)) == 0) {
            $result = AssessmentAnswer::create([
                'assessment_id' => $assessmentId,
                'option_question_id' => $optionQuestionId,
            ]);
            $count = AssessmentAnswer::where('assessment_id', $assessmentId)->count();
            if ($count == $this->totalQuestionsCount) {
                $assessment = Assessment::find($assessmentId);
                $assessment->ended_at = Carbon::now();
                $assessment->save();
                AssessmentApprove::updateOrCreate(['assessment_id' => $assessmentId], [
                    'slot' => null,
                ]);
                $this->createOrUpdateScore($assessment, $count);
                \Log::debug('invoking report job from assessmentService');
                $bundleStatus = BundleStatus::where('user_id', auth('user')->user()->id)->where('percentage_covered', '!=', 100.00)->whereHas('plans.package', function ($query) {
                    $query->where('name', 'HappiLIFE Screening');
                })->first();
                if ($bundleStatus) {
                    (new PackageService)->bundlePlanCompleted($bundleStatus->id);
                }
                $assessment->user->generateReportAndSendMail();
                return 'completed';
            }
            if ($result) {
                return true;
            }
        }
        return false;
    }

    /**
     * get remaining questions of assessmnet which need to be answered
     *
     * @return array
     */
    public function getRemainingQuestions()
    {
        return $this->getQuestions($this->assessmentId);
    }

    /**
     * get answered question of assessment
     *
     * @return Model
     */
    public function getAnsweredQuestions()
    {
        return AssessmentAnswer::where('assessment_id', $this->assessmentId)
            ->with('optionQuestion.question')
            ->get()
            ->pluck('optionQuestion.question');
    }

    /**
     * check and return remaining personality questions
     *
     * @return void
     */
    public function checkAllPersonalityQuestionsAttempted()
    {
        $answeredQuestionIds = $this->getAnsweredQuestions()->pluck('id')->toArray();

        $personalityQuestions = $this->getPersonalityQuestions(false);

        return $personalityQuestions->except($answeredQuestionIds)->count() == 0;
    }

    /**
     * return all personality questions of batch related to user profile
     *
     * @param [bool] $limit
     *
     * @return Model
     */
    public function getPersonalityQuestions($limit = true)
    {
        $batchCategoryIds = $this->batchCategory->pluck('id')->toArray();
        $personalityQuestions = Question::where('language' , 'english')->whereHas('category', function ($query) {
            $query->where('name', 'like', 'Personality');
        })
            ->whereHas('option')
            ->whereIn('batch_category_id', $batchCategoryIds)
            ->with('option')
            ->orderBy('id', 'ASC');
        if ($limit) {
            $personalityQuestions->limit($this->questionsPerPage);
        }
        return $personalityQuestions->get();
    }

    /**
     * check if user profile batch has personality questions
     *
     * @return boolean
     */
    public function checkIfProfileHasPersonalityQuestions()
    {
        return $this->batch->batchCategory()->whereHas('category', function ($query) {
            $query->where('name', 'Personality');
        })->get()->count() > 1;
    }

    /**
     * gives remaining personality question for initialized assessment
     *
     * @return array
     */
    public function getRemainingPersonalityQuestions()
    {
        $answeredQuestionIds = $this->getAnsweredQuestions()->pluck('id')->toArray();
        $batchCategoryIds = $this->batchCategory->pluck('id')->toArray();
        $remainingQuestions = Question::where('language' , 'english')->whereHas('category', function ($query) {
            $query->where('name', 'like', 'personality');
        })
            ->whereHas('option')
            ->whereIn('batch_category_id', $batchCategoryIds)
            ->whereNotIn('id', $answeredQuestionIds)
            ->with('option')
            ->orderBy('id', 'ASC')
            ->limit($this->questionsPerPage)
            ->get();


        // $remainingQuestions = $personalityQuestions->except($answeredQuestionIds);
        return $remainingQuestions;
    }

    /**
     * return questions with option for assessment id
     *
     * @param [int] $assessmentId
     * @return Model
     */
    public function getQuestions($assessmentId)
    {
        //limit no. of questions per page
        $limit = $this->questionsPerPage;


        $personalityQuestions = null;

        $questions = collect();
        if ($this->questionsPerPage == null) {
            //incase to get all questions without pagination ex: bot assessment
            $questions = Question::where('language' , 'english')->whereHas('category')
                ->whereIn('batch_category_id', $this->batchCategory->pluck('id')->toArray())
                ->with('option')
                ->get();
        } else {
            //has id of questions which are already answered by user in current assessment
            $answered_question_ids = $this->getAnsweredQuestions()->pluck('id')->toArray();
            if (empty($answered_question_ids) && $this->checkIfProfileHasPersonalityQuestions()) {
                $batchCategoryIds = $this->batchCategory->pluck('id')->toArray();
                $questions = $this->getPersonalityQuestions();
            } elseif (!$this->checkAllPersonalityQuestionsAttempted()) {
                sort($answered_question_ids);
                $personalityQuestions = $this->getRemainingPersonalityQuestions();
                if (count($personalityQuestions) <= $this->questionsPerPage) {
                    $limit = $this->questionsPerPage - count($personalityQuestions);
                    $questions = $personalityQuestions;
                } else {
                    $limit = 0;
                }
                if ($limit > 0) {
                    $remainingQuestions = Question::where('language' , 'english')->whereHas('category')->whereIn('batch_category_id', $this->batchCategory->pluck('id')->toArray())->with('option')->whereNotIn('id', $answered_question_ids)->inRandomOrder()->limit($limit)->get();
                    $questions = $questions->merge($remainingQuestions);
                }
            } else {
                $questions = Question::where('language' , 'english')->whereHas('category')->whereIn('batch_category_id', $this->batchCategory->pluck('id')->toArray())->whereNotIn('id', $answered_question_ids)->inRandomOrder()->limit($limit)->get();
            }
            $this->answeredQuestionsCount = count($answered_question_ids);
            if ($this->answeredQuestionsCount == $this->totalQuestionsCount) {
                $this->endAssessment($this->assessmentId);
            }
        }
        return $questions;
    }

    public function getQuestionsCatWise($assessmentId)
    {

        $user = Auth::user();
        //limit no. of questions per page
        $limit = $this->questionsPerPage;


        $personalityQuestions = null;

        $questions = collect();
        if ($this->questionsPerPage == null) {
            //incase to get all questions without pagination ex: bot assessment
            $questions = Question::where('language' , 'english')->whereHas('category')
                ->whereIn('batch_category_id', $this->batchCategory->pluck('id')->toArray())
                ->with('option')
                ->get();
        } else {
            //has id of questions which are already answered by user in current assessment
            $answered_question_ids = $this->getAnsweredQuestions()->pluck('id')->toArray();
            if (empty($answered_question_ids) && $this->checkIfProfileHasPersonalityQuestions()) {
                $batchCategoryIds = $this->batchCategory->pluck('id')->toArray();
                $questions = $this->getPersonalityQuestions();
            } elseif (!$this->checkAllPersonalityQuestionsAttempted()) {
                sort($answered_question_ids);
                $personalityQuestions = $this->getRemainingPersonalityQuestions();
                if (count($personalityQuestions) <= $this->questionsPerPage) {
                    $limit = $this->questionsPerPage - count($personalityQuestions);
                    $questions = $personalityQuestions;
                } else {
                    $limit = 0;
                }
                if ($limit > 0) {
                    // $remainingQuestions = Question::where('language' , 'english')->whereHas('category')->whereIn('batch_category_id', $this->batchCategory->pluck('id')->toArray())->with('option')->whereNotIn('id', $answered_question_ids)->inRandomOrder()->limit($limit)->get();
                    // $questions = $questions->merge($remainingQuestions);
                    $user_assessment_detail = Assessment::where('user_id' , $user->id)->first();
                    $cat_ids_based_on_batch = BatchCategory::where('batch_id' , $user_assessment_detail->batch_id)->pluck('category_id');
                    $cat_name = Category::whereIn('id' , $cat_ids_based_on_batch)->pluck('name')->toArray();

                    $answeredQuestionIds = $this->getAnsweredQuestions()->pluck('id')->toArray();
                    $batchCategoryIds = $this->batchCategory->pluck('id')->toArray();

                    foreach($cat_name as $single_cat_name){

                        if($limit <= $this->questionsPerPage && $limit != 0){

                            if($single_cat_name != 'Personality'){


                                $remainingQuestions = Question::where('language' , 'english')->whereHas('category', function ($query) use ($single_cat_name) {
                                    $query->where('name', 'like', $single_cat_name);
                                })
                                ->whereHas('option')
                                ->whereIn('batch_category_id', $batchCategoryIds)
                                ->whereNotIn('id', $answeredQuestionIds)
                                ->with('option')
                                ->orderBy('id', 'ASC');
                                // ->limit($limit)
                                // ->get();

                                $count_que = count($remainingQuestions->get());

                                if($count_que <= $limit){
                                    $questions = $questions->merge($remainingQuestions->get());
                                    $limit = $limit - $count_que;
                                }else{
                                    $questions = $questions->merge($remainingQuestions->limit($limit)->get());
                                    // $limit = $limit - $limit;
                                    $limit = 0;
                                }



                                // $questions = $questions->merge($remainingQuestions);
                            }

                        }
                    }
                }
            } else {
                $questions = collect();
                // $questions = Question::where('language' , 'english')->whereHas('category')->whereIn('batch_category_id', $this->batchCategory->pluck('id')->toArray())->whereNotIn('id', $answered_question_ids)->inRandomOrder()->limit($limit)->get();
                $user_assessment_detail = Assessment::where('user_id' , $user->id)->first();
                $cat_ids_based_on_batch = BatchCategory::where('batch_id' , $user_assessment_detail->batch_id)->pluck('category_id');
                $cat_name = Category::whereIn('id' , $cat_ids_based_on_batch)->pluck('name')->toArray();
                $answeredQuestionIds = $this->getAnsweredQuestions()->pluck('id')->toArray();
                $batchCategoryIds = $this->batchCategory->pluck('id')->toArray();
               
                foreach($cat_name as $single_cat_name){

                    if($limit <= $this->questionsPerPage && $limit != 0){

                        if($single_cat_name != 'Personality'){

                            $remainingQuestions = Question::where('language' , 'english')->whereHas('category', function ($query) use ($single_cat_name) {
                                $query->where('name', 'like', $single_cat_name);
                            })
                            ->whereHas('option')
                            ->whereIn('batch_category_id', $batchCategoryIds)
                            ->whereNotIn('id', $answeredQuestionIds)
                            ->with('option')
                            ->orderBy('id', 'ASC');
                            // ->limit($limit)
                            // ->get();

                            $count_que = count($remainingQuestions->get());

                            if($count_que <= $limit){
                                $questions = $questions->merge($remainingQuestions->get());
                                $limit = $limit - $count_que;
                            }else{
                                $questions = $questions->merge($remainingQuestions->limit($limit)->get());
                                // $limit = $limit - $limit;
                                $limit = 0;
                            }
                        }

                    }
                }
            }
            $this->answeredQuestionsCount = count($answered_question_ids);
            if ($this->answeredQuestionsCount == $this->totalQuestionsCount) {
                $this->endAssessment($this->assessmentId);
            }
        }
        return $questions;
    }

    /**
     * initialize user and its assessment details
     *
     * @param [int] $assessmentId
     * @return void
     */
    public function setAssessment($assessmentId)
    {
        $this->assessmentId = $assessmentId;
        $this->assessmentAnswers = AssessmentAnswer::with('optionQuestion.question.category', 'optionQuestion.option')->where('assessment_id', $this->assessmentId)->get();
        $this->assessment = Assessment::find($assessmentId);
        $this->user = $this->assessment->user;
        return $this;
    }

    /**
     * This method invokes all required methods for score calculation -> evaluate expression, make report daya for category
     *
     * @param [int] $assessmentId
     * @return void
     */
    protected function scoreAlgo($assessmentId)
    {
        $this->assessmentId = $assessmentId;
        $categories = $this->batch->batchCategory()->whereHas('category', function ($query) {
            $query->whereColumn('name', 'acronymn')->with('category');
        })->get()->pluck('category');
        $this->assessmentAnswers = AssessmentAnswer::with('optionQuestion.question.category', 'optionQuestion.option')->where('assessment_id', $this->assessmentId)->get();
        // dd($assessmentId);
        $totalScore = 0;
        $this->reportCharacteristics = ReportCharacteristic::whereIn('batch_category_id', $this->batchCategory->pluck('id')->toArray())->with('emoji')->get();
        // dd($this->reportCharacteristics);
        foreach ($categories as $category) {
            $this->{$category->name . 'Score'} = 0;
            // print_r(call_user_func(array($this, 'calculate' . str_replace(' ', '', ucfirst($category->name)) . 'Score')));
            $batchCategory = $category->batchCategory;
            $score = $this->calculateScoreForCategory($batchCategory);

            if ($category->name != 'Personality') {
                $this->{str_replace(' ', '', lcfirst($category->name)) . 'Score'} = $score;
                $totalScore += $score;
            }
            // array_push($this->scoreArray, [$category->name]);
            $this->scoreArray[$category->name] = ['score' => $score];
            $this->reportScale($category->name, $category->id);

            // dump($category->name);
            // dump($score);
        }
        $assessment = Assessment::find($assessmentId);
        $this->report = [
            'score' => $this->scoreArray,
            'assessment_date' => $assessment->started_at->format('M d,Y'),
            'assessment_time' => $assessment->started_at->format('h:i'),
            'nickname' => $assessment->user->nickname,
        ];
        return $totalScore;
    }

    /**
     * This invokes any method which needs different type of calculation and evaluate Score expression
     *
     * @param [int] $batchCategory
     * @return int
     */
    public function calculateScoreForCategory($batchCategory)
    {
        if ($batchCategory->category->name == "Personality") {
            return $this->calculatePersonalityScore();
        } elseif ($batchCategory->category->name == 'Anxiety') {
            return $this->calculateAnxietyScore($batchCategory);
        } else {
            return $this->evaluateExpression($batchCategory->calculation_step_macro, $batchCategory->category_id);
        }
        return $this->evaluateExpression($batchCategory->calculation_step_macro, $batchCategory->category_id);
    }

    /**
     * Add respective scores of options selected by user
     *
     * @param [int] $categoryId
     * @return int
     */
    protected function addAllScores($categoryId)
    {
        // $this->assessmentAnswers->dump();
        $answers = $this->assessmentAnswers->filter(function ($value) use ($categoryId) {
            return $value->optionQuestion->question->batchCategory->category_id == $categoryId;
        });
        $sum = $answers->pluck('optionQuestion')->sum('weightage');
        return $sum;
    }

    /**
     * Count options-number selected by user in category
     *
     * @param [int] $categoryId
     * @return int
     */
    public function countOptions($categoryId)
    {
        $answers = $this->assessmentAnswers->filter(function ($value) use ($categoryId) {
            return $value->optionQuestion->question->batchCategory->category_id == $categoryId;
        });

        $counts = [];
        foreach ($answers as $answer) {
            if (!isset($counts['option-' . $answer->optionQuestion->weightage])) {
                $counts['option-' . $answer->optionQuestion->weightage] = 1;
            } else {
                $counts['option-' . $answer->optionQuestion->weightage] += 1;
            }
        }
        return $counts;
    }

    /**
     * Count Questions in category
     *
     * @param [int] $categoryId
     * @return int
     */
    public function countQuestionInCategory($categoryId)
    {
        $batchId = $this->batch->id;
        $batchCategory = BatchCategory::where('batch_id', $batchId)
            ->where('category_id', $categoryId)
            ->with('questions_english')
            ->first();
        return $batchCategory->questions->count();
    }

    /**
     * Does preprocess on keyword templates defined and evaluate expression for final score
     *
     * KEYWORDS:-
     *  1) ADDALLSCORE: add all score of options selected by user
     *  2) COUNTOPTION-<OPTION_NUMER>: count how many options of option nummber aka option with same score is selected
     *  3) QuestionCount: count questions in category
     *
     * @param [string] $expression
     * @param [int] $categoryId
     * @return int
     */
    public function evaluateExpression($expression, $categoryId)
    {
        $calculator = new Calculator;
        if (str_contains($expression, "ADDALLSCORE")) {
            $sum = $this->addAllScores($categoryId);
            $expression = str_replace('ADDALLSCORE', $sum, $expression);
        }
        if (str_contains($expression, "COUNTOPTION-")) {
            $pattern = "/(COUNTOPTION-[0-9]*)/i";
            preg_match_all($pattern, $expression, $matches);
            $counts = $this->countOptions($categoryId);

            foreach ($matches[0] as $match) {
                $option = explode("-", $match);
                if (!isset($counts['option-' . $option[1]])) {
                    $counts['option-' . $option[1]] = 0;
                }
                $expression = str_replace("COUNTOPTION-" . $option[1], $counts['option-' . $option[1]], $expression);
            }
        }
        if (str_contains($expression, "QUESTIONCOUNT")) {
            $count = $this->countQuestionInCategory($categoryId);
            $expression = str_replace("QUESTIONCOUNT", $count, $expression);
        }
        return $calculator->evaluate($expression);
    }

    /**
     * Creates associative array of categories as indices and values as all data to be inserted in report
     *
     * @return void
     */
    protected function reportScale($categoryName, $categoryId)
    {

        if ($categoryName == 'Personality') {
            $catNames = $this->calculatePersonalityScore();
            $summary = '';
            foreach ($catNames as $category => $key) {
                $summary = $summary .  Category::with('reportCharacteristics')->where('acronymn', ucfirst($category))->first()->reportCharacteristics->first()->summary;
            }
            $this->scoreArray[$categoryName] += [
                'meterScaleLevelName' => '',
                'summary' => $summary,
                'WOL_representation' => 'none',
                'included_in_report' => '1',
                'category_in_report' => Category::find($categoryId)->name_in_report,
            ];
        } elseif (isset($this->{str_replace(' ', '', lcfirst($categoryName)) . 'Score'})) {
            $score = $this->{str_replace(' ', '', lcfirst($categoryName)) . 'Score'};
            $scale = $this->reportCharacteristics->filter(function ($value) use ($categoryId, $score) {
                if ($value->category_id == $categoryId && $value->minimum_score <= $score && $value->maximum_score >= $score) {
                    return true;
                }
            });
            if (count($scale) == 1) {
                $scale = $scale->first();
                if (isset($this->scoreArray[$categoryName])) {
                    $cat = Category::find($categoryId);
                    $fill_area_percentage = $scale->WOL_fill_area;
                    $this->scoreArray[$categoryName] += [
                        'meterScaleLevelName' => $scale->meter_scale_level_name,
                        'WOL_representation' => $scale->WOL_representation,
                        'summary' => $scale->summary,
                        'included_in_report' => $scale->included_in_report,
                        'picture' => ($scale->emoji->image) ?? '',
                        'category_in_report' => $cat->name_in_report,
                        'WOL_fill_area' => $fill_area_percentage,
                        'WOL_fill_color' => $cat->color,
                    ];
                } else {
                    $this->scoreArray[$categoryName] = [
                        'summary' => $scale->summary,
                    ];
                }
                if ($categoryName == 'Anxiety') {
                    if ($this->stateAnxietyScore > $this->traitAnxietyScore) {
                        $this->reportScale('State Anxiety', $this->getBatchCategoryInstance()->whereHas('category', function ($query) {
                            $query->where('acronymn', 'like', 'State Anxiety');
                        })->first()->category_id);
                        $this->scoreArray['Anxiety']['summary'] = $this->scoreArray['Anxiety']['summary'] . $this->scoreArray['State Anxiety']['summary'];
                        unset($this->scoreArray['State Anxiety']);
                    } else {
                        $this->reportScale('Trait Anxiety', $this->getBatchCategoryInstance()->whereHas('category', function ($query) {
                            $query->where('acronymn', 'like', 'Trait Anxiety');
                        })->first()->category_id);
                        $this->scoreArray['Anxiety']['summary'] = $this->scoreArray['Anxiety']['summary'] . $this->scoreArray['Trait Anxiety']['summary'];
                        unset($this->scoreArray['Trait Anxiety']);
                    }
                }
            }
        }
    }

    /**
     * calculate score based on formula defined
     *
     * @return int
     */
    protected function calculateAnxietyScore($batchCategory)
    {
        $totalScore = $this->evaluateExpression($batchCategory->calculation_step_macro, $batchCategory->category_id);
        $this->anxietyScore = $totalScore;
        $category = Category::where('acronymn', 'Trait Anxiety')->get()->pluck('id')->toArray();
        $batchCategory = BatchCategory::whereIn('category_id', $category)->where('batch_id', $batchCategory->batch_id)->first();

        $this->calculateTraitAnxietyScore($batchCategory);
        $category = Category::where('acronymn', 'State Anxiety')->get()->pluck('id')->toArray();
        $batchCategory = BatchCategory::whereIn('category_id', $category)->where('batch_id', $batchCategory->batch_id)->first();
        $this->calculateStateAnxietyScore($batchCategory);
        // dd($totalScore);
        return $totalScore;
    }

    /**
     * calculate score based on formula defined
     *
     * @return int
     */
    protected function calculateStateAnxietyScore($batchCategory)
    {
        $this->stateAnxietyScore = $this->evaluateExpression($batchCategory->calculation_step_macro, $batchCategory->category_id);
        $this->scoreArray['Anxiety'][$batchCategory->category->acronymn] = ['score' => $this->stateAnxietyScore];
        return $this->stateAnxietyScore;
    }

    /**
     * sum up respective score
     *
     * @return int
     */
    protected function calculateTraitAnxietyScore($batchCategory)
    {
        $this->traitAnxietyScore = $this->evaluateExpression($batchCategory->calculation_step_macro, $batchCategory->category_id);
        $this->scoreArray['Anxiety'][$batchCategory->category->acronymn] = ['score' => $this->traitAnxietyScore];
        return $this->traitAnxietyScore;
    }

    /**
     * personality score is calculated as the percentage of true options selected by user in
     * each subcategory and return top two percentage category
     *
     * @return array
     */
    public function calculatePersonalityScore()
    {
        $answers = $this->assessmentAnswers->filter(function ($value) {
            return $value->optionQuestion->question->category->name == 'Personality';
        });
        $paranoid = 0;
        $paranoidCount = 0;
        $dissocial = 0;
        $dissocialCount = 0;
        $impulsive = 0;
        $impulsiveCount = 0;
        $borderline = 0;
        $borderlineCount = 0;
        $histrionic = 0;
        $histrionicCount = 0;
        $anankastic = 0;
        $anankasticCount = 0;
        $anxious = 0;
        $anxiousCount = 0;
        $dependent = 0;
        $dependentCount = 0;
        foreach ($answers as $answer) {
            if ($answer->optionQuestion->question->category->acronymn == 'Paranoid') {
                if ($answer->optionQuestion->weightage == 1) {
                    $paranoid++;
                }
                $paranoidCount++;
            }
            if ($answer->optionQuestion->question->category->acronymn == 'Dissocial') {
                if ($answer->optionQuestion->weightage == 1) {
                    $dissocial++;
                }
                $dissocialCount++;
            }
            if ($answer->optionQuestion->question->category->acronymn == 'Impulsive') {
                if ($answer->optionQuestion->weightage == 1) {
                    $impulsive++;
                }
                $impulsiveCount++;
            }
            if ($answer->optionQuestion->question->category->acronymn == 'Borderline') {
                if ($answer->optionQuestion->weightage == 1) {
                    $borderline++;
                }
                $borderlineCount++;
            }
            if ($answer->optionQuestion->question->category->acronymn == 'Histrionic') {
                if ($answer->optionQuestion->weightage == 1) {
                    $histrionic++;
                }
                $histrionicCount++;
            }
            if ($answer->optionQuestion->question->category->acronymn == 'Anankastic') {
                if ($answer->optionQuestion->weightage == 1) {
                    $anankastic++;
                }
                $anankasticCount++;
            }
            if ($answer->optionQuestion->question->category->acronymn == 'Anxious') {
                if ($answer->optionQuestion->weightage == 1) {
                    $anxious++;
                }
                $anxiousCount++;
            }
            if ($answer->optionQuestion->question->category->acronymn == 'Dependent') {
                if ($answer->optionQuestion->weightage == 1) {
                    $dependent++;
                }
                $dependentCount++;
            }
        }
        $percentages = [
            'paranoid' => round(($paranoid / $paranoidCount) * 100, 2),
            'dissocial' => round(($dissocial / $dissocialCount) * 100, 2),
            'impulsive' => round(($impulsive / $impulsiveCount) * 100, 2),
            'borderline' => round(($borderline / $borderlineCount) * 100, 2),
            'histrionic' => round(($histrionic / $histrionicCount) * 100, 2),
            'anankastic' => round(($anankastic / $anankasticCount) * 100, 2),
            'Anxious' => round(($anxious / $anxiousCount) * 100, 2),
            'dependent' => round(($dependent / $dependentCount) * 100, 2),
        ];
        ksort($percentages);
        arsort($percentages);
        return array_slice($percentages, 0, 2, true);
    }

    public function createOrUpdateScore($assessment, $count = 0)
    {

        $preventLoop = false;
        if (empty($this->scoreArray) && $preventLoop == false) {
            //if score array is empty this condition prevents for infinite loop
            //Note: this methods shouldn't be called from calculateScore methods
            $preventLoop = true;
            $this->forAssessment($assessment->id);
            $this->calculateScore();
        }
        if ($count == 0) {
            $count = $this->totalQuestionsCount;
        }
        if ($count == $this->totalQuestionsCount) {
            $score = $this->scoreArray;
            $prepareData = [];
            foreach ($score as $category => $array) {
                if ($category == "Personality") {
                    array_push(
                        $prepareData,
                        [
                            "personality" => [
                                array_keys($array['score'])[0] => [
                                    'score' => $array['score'][array_keys($array['score'])[0]]
                                ],
                                array_keys($array['score'])[1] => [
                                    'score' => $array['score'][array_keys($array['score'])[0]]
                                ]
                            ]
                        ],
                    );
                } else {
                    array_push($prepareData, [strtolower($category)  => ['score' => $array['score'], 'level' => $array['WOL_fill_area']]]);
                }
            }

            $assessmentScore = AssessmentScore::updateOrCreate(
                ['assessment_id' => $assessment->id],
                [
                    'user_id' => $assessment->user_id,
                    'attempts' => $count,
                    'scores' => $prepareData
                ]
            );
            return $assessmentScore;
        }

        return NULL;
    }


    //Application



    //Application



    public function forAssessmentApp($assessmentId)
    {
        $this->assessmentId = $assessmentId;
        $this->assessment = Assessment::find($assessmentId);
        $this->user = $this->assessment->user;
        $this->assessmentAnswers = AssessmentAnswer::with('optionQuestion.question.category', 'optionQuestion.option')->where('assessment_id', $this->assessmentId)->get();
        $this->getProfileType();
        $categoryIds = $this->batchCategory->pluck('category_id')->toArray();
        $this->totalQuestionsCount = $this->batch->batchCategory()->whereHas('category')->withCount('questions_app')->get();
        
        $this->totalQuestionsCount = $this->totalQuestionsCount->sum('questions_app_count');
        $this->answeredQuestionsCount = AssessmentAnswer::where('assessment_id', $this->assessmentId)->get()->count();
        $this->questionsPerPage = $this->totalQuestionsCount != 0 ? ceil($this->totalQuestionsCount / 5) : 0;
        
        return $this;
    }



    public function getRemainingQuestionsApp()
    {
        return $this->getQuestionsApp($this->assessmentId);
    }


    public function getQuestionsApp($assessmentId)
    {

        $user_language = Auth::user()->language;

        //limit no. of questions per page
        $limit = $this->questionsPerPage;


        $personalityQuestions = null;

        $questions = collect();
        if ($this->questionsPerPage == null) {
            //incase to get all questions without pagination ex: bot assessment
            $questions = Question::where('language',$user_language)->whereHas('category')
                ->whereIn('batch_category_id', $this->batchCategory->pluck('id')->toArray())
                ->with('option')
                ->get();
        } else {
            //has id of questions which are already answered by user in current assessment
            $answered_question_ids = $this->getAnsweredQuestions()->pluck('id')->toArray();
            if (empty($answered_question_ids) && $this->checkIfProfileHasPersonalityQuestions()) {
                $batchCategoryIds = $this->batchCategory->pluck('id')->toArray();
                $questions = $this->getPersonalityQuestionsApp($user_language);
                // if($questions == null){
                    // $questions = Question::where('language',$user_language)->whereHas('category')->whereIn('batch_category_id', $this->batchCategory->pluck('id')->toArray())->whereNotIn('id', $answered_question_ids)->inRandomOrder()->limit($limit)->get();
                // }
            } elseif (!$this->checkAllPersonalityQuestionsAttempted()) {
                sort($answered_question_ids);
                $personalityQuestions = $this->getRemainingPersonalityQuestionsApp($user_language);
                // if($personalityQuestions == null){
                    // $personalityQuestions = Question::where('language',$user_language)->whereHas('category')->whereIn('batch_category_id', $this->batchCategory->pluck('id')->toArray())->with('option')->whereNotIn('id', $answered_question_ids)->inRandomOrder()->limit($limit)->get();
                // }

                if (count($personalityQuestions) <= $this->questionsPerPage) {
                    $limit = $this->questionsPerPage - count($personalityQuestions);
                    $questions = $personalityQuestions;
                } else {
                    $limit = 0;
                }
                if ($limit > 0) {
                    $remainingQuestions = Question::where('language',$user_language)->whereHas('category')->whereIn('batch_category_id', $this->batchCategory->pluck('id')->toArray())->with('option')->whereNotIn('id', $answered_question_ids)->inRandomOrder()->limit($limit)->get();
                    $questions = $questions->merge($remainingQuestions);
                }
            } else {
                $questions = Question::where('language',$user_language)->whereHas('category')->whereIn('batch_category_id', $this->batchCategory->pluck('id')->toArray())->whereNotIn('id', $answered_question_ids)->inRandomOrder()->limit($limit)->get();
            }
            $this->answeredQuestionsCount = count($answered_question_ids);
            if ($this->answeredQuestionsCount == $this->totalQuestionsCount) {
                $this->endAssessment($this->assessmentId);
            }
        }
        return $questions;
    }



    public function getPersonalityQuestionsApp($user_language , $limit = true)
    {
        $batchCategoryIds = $this->batchCategory->pluck('id')->toArray();
        $personalityQuestions = Question::where('language' , $user_language)->whereHas('category', function ($query) {
            $query->where('name', 'like', 'Personality');
        })
            ->whereHas('option')
            ->whereIn('batch_category_id', $batchCategoryIds)
            ->with('option')
            ->orderBy('id', 'ASC');
        if ($limit) {
            $personalityQuestions->limit($this->questionsPerPage);
        }
        return $personalityQuestions->get();
    }


    public function getRemainingPersonalityQuestionsApp($user_language)
    {
        $answeredQuestionIds = $this->getAnsweredQuestions()->pluck('id')->toArray();
        $batchCategoryIds = $this->batchCategory->pluck('id')->toArray();
        $remainingQuestions = Question::where('language' , $user_language)->whereHas('category', function ($query) {
            $query->where('name', 'like', 'personality');
        })
            ->whereHas('option')
            ->whereIn('batch_category_id', $batchCategoryIds)
            ->whereNotIn('id', $answeredQuestionIds)
            ->with('option')
            ->orderBy('id', 'ASC')
            ->limit($this->questionsPerPage)
            ->get();


        // $remainingQuestions = $personalityQuestions->except($answeredQuestionIds);
        return $remainingQuestions;
    }


    public function saveAssessmentOptionApp($assessmentId, $optionQuestionId)
    {
        $this->forAssessmentApp($assessmentId);
        $optionQuestion = OptionQuestion::with('question.option')->find($optionQuestionId);
        $questionOptions = $optionQuestion->question->option->pluck('pivot')->pluck('id')->toArray();
        $answeredOptions = AssessmentAnswer::where('assessment_id', $assessmentId)->get()->pluck('option_question_id')->toArray();
        if (count(array_intersect($answeredOptions, $questionOptions)) == 0) {
            $result = AssessmentAnswer::create([
                'assessment_id' => $assessmentId,
                'option_question_id' => $optionQuestionId,
            ]);
            $count = AssessmentAnswer::where('assessment_id', $assessmentId)->count();

            // 
            $this->totalQuestionsCountApp = $this->batch->batchCategory()->whereHas('category')->withCount('questions_app')->get();
            $totalQuestionsCountApp = $this->totalQuestionsCountApp->sum('questions_app_count');
            // 

            if ($count == $totalQuestionsCountApp) {
                $assessment = Assessment::find($assessmentId);
                $assessment->ended_at = Carbon::now();
                $assessment->save();
                AssessmentApprove::updateOrCreate(['assessment_id' => $assessmentId], [
                    'slot' => null,
                ]);
                $this->createOrUpdateScoreApp($assessment, $count);
                \Log::debug('invoking report job from assessmentService');
                $bundleStatus = BundleStatus::where('user_id', auth('user')->user()->id)->where('percentage_covered', '!=', 100.00)->whereHas('plans.package', function ($query) {
                    $query->where('name', 'HappiLIFE Screening');
                })->first();
                if ($bundleStatus) {
                    (new PackageService)->bundlePlanCompleted($bundleStatus->id);
                }
                $assessment->user->generateReportAndSendMail();
                return 'completed';
            }
            if ($result) {
                return true;
            }
        }
        return false;
    }


    public function createOrUpdateScoreApp($assessment, $count = 0)
    {

        $preventLoop = false;
        if (empty($this->scoreArray) && $preventLoop == false) {
            //if score array is empty this condition prevents for infinite loop
            //Note: this methods shouldn't be called from calculateScore methods
            $preventLoop = true;
            $this->forAssessmentApp($assessment->id);
            $this->calculateScoreApp();
        }
        if ($count == 0) {
            $count = $this->totalQuestionsCount;
        }
        if ($count == $this->totalQuestionsCount) {
            $score = $this->scoreArray;
            $prepareData = [];
            foreach ($score as $category => $array) {
                if(array_key_exists('WOL_fill_area' , $array)){
                    if ($category == "Personality") {
                        array_push(
                            $prepareData,
                            [
                                "personality" => [
                                    array_keys($array['score'])[0] => [
                                        'score' => $array['score'][array_keys($array['score'])[0]]
                                    ],
                                    array_keys($array['score'])[1] => [
                                        'score' => $array['score'][array_keys($array['score'])[0]]
                                    ]
                                ]
                            ],
                        );
                    } else {
                        array_push($prepareData, [strtolower($category)  => ['score' => $array['score'], 'level' => $array['WOL_fill_area']]]);
                    }
                }
                
            }

            $assessmentScore = AssessmentScore::updateOrCreate(
                ['assessment_id' => $assessment->id],
                [
                    'user_id' => $assessment->user_id,
                    'attempts' => $count,
                    'scores' => $prepareData
                ]
            );
            return $assessmentScore;
        }

        return NULL;
    }


    public function calculateScoreApp()
    {
        if ($this->isAssessmentCompleted()) {
            $this->scoreAlgoApp($this->assessmentId);
        }
        return $this;
    }


    protected function scoreAlgoApp($assessmentId)
    {
        $this->assessmentId = $assessmentId;
        $categories = $this->batch->batchCategory()->whereHas('category', function ($query) {
            $query->whereColumn('name', 'acronymn')->with('category');
        })->get()->pluck('category');
        $this->assessmentAnswers = AssessmentAnswer::with('optionQuestion.question.category', 'optionQuestion.option')->where('assessment_id', $this->assessmentId)->get();
        // dd($assessmentId);
        $totalScore = 0;
        $this->reportCharacteristics = ReportCharacteristic::whereIn('batch_category_id', $this->batchCategory->pluck('id')->toArray())->with('emoji')->get();
        // dd($this->reportCharacteristics);
        foreach ($categories as $category) {
            $this->{$category->name . 'Score'} = 0;
            // print_r(call_user_func(array($this, 'calculate' . str_replace(' ', '', ucfirst($category->name)) . 'Score')));
            $batchCategory = $category->batchCategory;
            $score = $this->calculateScoreForCategoryApp($batchCategory);

            if ($category->name != 'Personality') {
                $this->{str_replace(' ', '', lcfirst($category->name)) . 'Score'} = $score;
                $totalScore += $score;
            }
            // array_push($this->scoreArray, [$category->name]);
            $this->scoreArray[$category->name] = ['score' => $score];
            $this->reportScaleApp($category->name, $category->id);

            // dump($category->name);
            // dump($score);
        }
        $assessment = Assessment::find($assessmentId);
        $this->report = [
            'score' => $this->scoreArray,
            'assessment_date' => $assessment->started_at->format('M d,Y'),
            'assessment_time' => $assessment->started_at->format('h:i'),
            'nickname' => $assessment->user->nickname,
        ];
        return $totalScore;
    }



    public function calculateScoreForCategoryApp($batchCategory)
    {
        if ($batchCategory->category->name == "Personality") {
            return $this->calculatePersonalityScoreApp();
        } 
        elseif ($batchCategory->category->name == 'Anxiety') {
            return $this->calculateAnxietyScoreApp($batchCategory);
        } 
        else {
            return $this->evaluateExpressionApp($batchCategory->calculation_step_macro, $batchCategory->category_id);
        }
        return $this->evaluateExpressionApp($batchCategory->calculation_step_macro, $batchCategory->category_id);
    }



    public function calculatePersonalityScoreApp()
    {
        $answers = $this->assessmentAnswers->filter(function ($value) {
            return $value->optionQuestion->question->category->name == 'Personality';
        });
        $paranoid = 1;
        $paranoidCount = 1;
        $dissocial = 1;
        $dissocialCount = 1;
        $impulsive = 1;
        $impulsiveCount = 1;
        $borderline = 1;
        $borderlineCount = 1;
        $histrionic = 1;
        $histrionicCount = 1;
        $anankastic = 1;
        $anankasticCount = 1;
        $anxious = 1;
        $anxiousCount = 1;
        $dependent = 1;
        $dependentCount = 1;
        foreach ($answers as $answer) {
            if ($answer->optionQuestion->question->category->acronymn == 'Paranoid') {
                if ($answer->optionQuestion->weightage == 1) {
                    $paranoid++;
                }
                $paranoidCount++;
            }
            if ($answer->optionQuestion->question->category->acronymn == 'Dissocial') {
                if ($answer->optionQuestion->weightage == 1) {
                    $dissocial++;
                }
                $dissocialCount++;
            }
            if ($answer->optionQuestion->question->category->acronymn == 'Impulsive') {
                if ($answer->optionQuestion->weightage == 1) {
                    $impulsive++;
                }
                $impulsiveCount++;
            }
            if ($answer->optionQuestion->question->category->acronymn == 'Borderline') {
                if ($answer->optionQuestion->weightage == 1) {
                    $borderline++;
                }
                $borderlineCount++;
            }
            if ($answer->optionQuestion->question->category->acronymn == 'Histrionic') {
                if ($answer->optionQuestion->weightage == 1) {
                    $histrionic++;
                }
                $histrionicCount++;
            }
            if ($answer->optionQuestion->question->category->acronymn == 'Anankastic') {
                if ($answer->optionQuestion->weightage == 1) {
                    $anankastic++;
                }
                $anankasticCount++;
            }
            if ($answer->optionQuestion->question->category->acronymn == 'Anxious') {
                if ($answer->optionQuestion->weightage == 1) {
                    $anxious++;
                }
                $anxiousCount++;
            }
            if ($answer->optionQuestion->question->category->acronymn == 'Dependent') {
                if ($answer->optionQuestion->weightage == 1) {
                    $dependent++;
                }
                $dependentCount++;
            }
        }
        $percentages = [
            'paranoid' => round(($paranoid / $paranoidCount) * 100, 2),
            'dissocial' => round(($dissocial / $dissocialCount) * 100, 2),
            'impulsive' => round(($impulsive / $impulsiveCount) * 100, 2),
            'borderline' => round(($borderline / $borderlineCount) * 100, 2),
            'histrionic' => round(($histrionic / $histrionicCount) * 100, 2),
            'anankastic' => round(($anankastic / $anankasticCount) * 100, 2),
            'Anxious' => round(($anxious / $anxiousCount) * 100, 2),
            'dependent' => round(($dependent / $dependentCount) * 100, 2),
        ];
        ksort($percentages);
        arsort($percentages);
        return array_slice($percentages, 0, 2, true);
    }



    protected function calculateAnxietyScoreApp($batchCategory)
    {
        $totalScore = $this->evaluateExpressionApp($batchCategory->calculation_step_macro, $batchCategory->category_id);
        $this->anxietyScore = $totalScore;
        $category = Category::where('acronymn', 'Trait Anxiety')->get()->pluck('id')->toArray();
        $batchCategory_trait = BatchCategory::whereIn('category_id', $category)->where('batch_id', $batchCategory->batch_id)->first();
        if($batchCategory != null){
            $this->calculateTraitAnxietyScoreApp($batchCategory);
        }

        $category = Category::where('acronymn', 'State Anxiety')->get()->pluck('id')->toArray();
        $batchCategory_state = BatchCategory::whereIn('category_id', $category)->where('batch_id', $batchCategory->batch_id)->first();
        if($batchCategory != null){
            $this->calculateStateAnxietyScoreApp($batchCategory);
        }
        // dd($totalScore);
        return $totalScore;
    }



    protected function calculateTraitAnxietyScoreApp($batchCategory)
    {
        $this->traitAnxietyScore = $this->evaluateExpressionApp($batchCategory->calculation_step_macro, $batchCategory->category_id);
        $this->scoreArray['Anxiety'][$batchCategory->category->acronymn] = ['score' => $this->traitAnxietyScore];
        return $this->traitAnxietyScore;
    }

    protected function calculateStateAnxietyScoreApp($batchCategory)
    {
        $this->stateAnxietyScore = $this->evaluateExpressionApp($batchCategory->calculation_step_macro, $batchCategory->category_id);
        $this->scoreArray['Anxiety'][$batchCategory->category->acronymn] = ['score' => $this->stateAnxietyScore];
        return $this->stateAnxietyScore;
    }


    public function evaluateExpressionApp($expression, $categoryId)
    {
        $calculator = new Calculator;
        if (str_contains($expression, "ADDALLSCORE")) {
            $sum = $this->addAllScores($categoryId);
            $expression = str_replace('ADDALLSCORE', $sum, $expression);
        }
        if (str_contains($expression, "COUNTOPTION-")) {
            $pattern = "/(COUNTOPTION-[0-9]*)/i";
            preg_match_all($pattern, $expression, $matches);
            $counts = $this->countOptions($categoryId);

            foreach ($matches[0] as $match) {
                $option = explode("-", $match);
                if (!isset($counts['option-' . $option[1]])) {
                    $counts['option-' . $option[1]] = 0;
                }
                $expression = str_replace("COUNTOPTION-" . $option[1], $counts['option-' . $option[1]], $expression);
            }
        }
        if (str_contains($expression, "QUESTIONCOUNT")) {
            $count = $this->countQuestionInCategoryApp($categoryId);
            $expression = str_replace("QUESTIONCOUNT", $count, $expression);
        }
        return $calculator->evaluate($expression);
    }


    public function countQuestionInCategoryApp($categoryId)
    {
        $batchId = $this->batch->id;
        $batchCategory = BatchCategory::where('batch_id', $batchId)
            ->where('category_id', $categoryId)
            ->with('questions')
            ->first();
        return $batchCategory->questions->count();
    }


    protected function reportScaleApp($categoryName, $categoryId)
    {

        if ($categoryName == 'Personality') {
            $catNames = $this->calculatePersonalityScoreApp();
            $summary = '';
            foreach ($catNames as $category => $key) {
                $summary = $summary .  Category::with('reportCharacteristics')->where('acronymn', ucfirst($category))->first()->reportCharacteristics->first()->summary;
            }
            $this->scoreArray[$categoryName] += [
                'meterScaleLevelName' => '',
                'summary' => $summary,
                'WOL_representation' => 'none',
                'included_in_report' => '1',
                'category_in_report' => Category::find($categoryId)->name_in_report,
            ];
        } elseif (isset($this->{str_replace(' ', '', lcfirst($categoryName)) . 'Score'})) {
            $score = $this->{str_replace(' ', '', lcfirst($categoryName)) . 'Score'};
            $scale = $this->reportCharacteristics->filter(function ($value) use ($categoryId, $score) {
                if ($value->category_id == $categoryId && $value->minimum_score <= $score && $value->maximum_score >= $score) {
                    return true;
                }
            });
            if (count($scale) == 1) {
                $scale = $scale->first();
                if (isset($this->scoreArray[$categoryName])) {
                    $cat = Category::find($categoryId);
                    $fill_area_percentage = $scale->WOL_fill_area;
                    $this->scoreArray[$categoryName] += [
                        'meterScaleLevelName' => $scale->meter_scale_level_name,
                        'WOL_representation' => $scale->WOL_representation,
                        'summary' => $scale->summary,
                        'included_in_report' => $scale->included_in_report,
                        'picture' => ($scale->emoji->image) ?? '',
                        'category_in_report' => $cat->name_in_report,
                        'WOL_fill_area' => $fill_area_percentage,
                        'WOL_fill_color' => $cat->color,
                    ];
                } else {
                    $this->scoreArray[$categoryName] = [
                        'summary' => $scale->summary,
                    ];
                }
                if ($categoryName == 'Anxiety') {

                    if ($this->stateAnxietyScore >= $this->traitAnxietyScore) {
                        $this->reportScaleApp('State Anxiety', $this->getBatchCategoryInstanceApp()->whereHas('category', function ($query) {
                            $query->where('acronymn', 'like', 'State Anxiety');
                        })->first()->category_id);
                        $this->scoreArray['Anxiety']['summary'] = $this->scoreArray['Anxiety']['summary'] . $this->scoreArray['State Anxiety']['summary'];
                        unset($this->scoreArray['State Anxiety']);
                    }
                    else{
                        $this->reportScaleApp('Trait Anxiety', $this->getBatchCategoryInstanceApp()->whereHas('category', function ($query) {
                            $query->where('acronymn', 'like', 'Trait Anxiety');
                        })->first()->category_id);
                        $this->scoreArray['Anxiety']['summary'] = $this->scoreArray['Anxiety']['summary'] . $this->scoreArray['Trait Anxiety']['summary'];
                        unset($this->scoreArray['Trait Anxiety']);
                    }
                    
                }

            }
        }
    }


    public function getBatchCategoryInstanceApp()
    {
        if ($this->batch->batchCategory->count() == 0) {
            return $this->batchCategory = $this->getDefaultBatch()->batchCategory();
        } else {
            return $this->batchCategory = $this->batch->batchCategory();
        }
    }



    
}
