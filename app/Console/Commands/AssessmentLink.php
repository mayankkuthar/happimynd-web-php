<?php

namespace App\Console\Commands;

use App\Models\Assessment;
use App\Models\Batch;
use App\Models\BatchCategory;
use App\Models\Category;
use App\Models\Question;
use App\Models\ReportCharacteristic;
use App\Models\UserProfile;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssessmentLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assessment:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            $categories = Category::all();
            $batchCategory = [];
            $categories = Category::all();
            Batch::create([
                'name' => 'salaried',
                'user_profile_id' => 1,
            ]);
            foreach ($categories as $category) {
                array_push($batchCategory, [
                    'category_id' => $category->id,
                    'batch_id' => 1
                ]);
            }
            BatchCategory::insert($batchCategory);
            foreach ($categories as $category) {
                $questions = $category->question;
                $bc = BatchCategory::where('category_id', $category->id)->first();
                $bc->questions()->saveMany($questions);
            }
            $rcs = ReportCharacteristic::wherehas('category')->get();
            foreach ($rcs as $rc) {
                $rc->batch_category_id = BatchCategory::where('category_id', $rc->category_id)->first()->id;
                if ($rc->oldEmoji)
                    $rc->rating_picture_id = $rc->oldEmoji->id;
                $rc->save();
            }

            $batchCategories = BatchCategory::with('category')->get();
            foreach ($batchCategories as $batchCategory) {
                if (
                    $batchCategory->category->acronymn == 'Depression' ||
                    $batchCategory->category->acronymn == 'Anxiety' ||
                    $batchCategory->category->acronymn == 'Stress'
                ) {
                    $batchCategory->calculation_step_macro = 'ADDALLSCORE*2';
                    $batchCategory->save();
                } elseif (
                    $batchCategory->category->acronymn == 'Burn out' ||
                    $batchCategory->category->acronymn == 'Internet Addiction' ||
                    $batchCategory->category->acronymn == 'Self Esteem' ||
                    $batchCategory->category->acronymn == 'Job Satisfaction' ||
                    $batchCategory->category->acronymn == 'State Anxiety' ||
                    $batchCategory->category->acronymn == 'Trait Anxiety'
                ) {
                    $batchCategory->calculation_step_macro = 'ADDALLSCORE';
                    $batchCategory->save();
                } elseif ($batchCategory->category->name == 'Happiness') {
                    $batchCategory->calculation_step_macro = '(COUNTOPTION-1*30)+(COUNTOPTION-2*20)+(COUNTOPTION-3*30)';
                    $batchCategory->save();
                } elseif ($batchCategory->category->name == 'Resilience') {
                    $batchCategory->calculation_step_macro = 'ADDALLSCORE/QUESTIONCOUNT';
                    $batchCategory->save();
                }
            }

            //add batch id to assessments
            $assessments = Assessment::all();
            foreach ($assessments as $assessment) {
                $batch = $assessment->user->profileType->batch->first();
                if (is_null($batch)) {
                    $batch = UserProfile::where('name', 'salaried')->first()->batch->first();
                }
                if ($batch) {
                    $assessment->batch_id = $batch->id;
                    $assessment->save();
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            print($e->getMessage());
        }
    }
}
