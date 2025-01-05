<?php

use App\Models\AssessmentScore;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UseJsonForScoreInScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::beginTransaction();

            Schema::table('assessment_scores', function (Blueprint $table) {
                $table->json('scores')->after('attempts');
            });
            $existingRecords = AssessmentScore::all();
            foreach ($existingRecords as $record) {
                $record->makeHidden(['assessment_id', 'user_id', 'attempts', 'created_at', 'updated_at', 'deleted_at', 'id', 'scores']);
                $score = $record->toArray();
                $prepareData = [];
                $personality_check = false;
                foreach ($score as $category => $array) {
                    if (str_contains($category, '_score')) {
                        $category = explode('_score', $category)[0];
                        if ($category == "personality" && !$personality_check) {
                            array_push(
                                $prepareData,
                                [
                                    "personality" => [
                                        $record['personality_1'] => [
                                            'score' => $record['personality_score_1']
                                        ],
                                        $record['personality_2'] => [
                                            'score' => $record['personality_score_2']
                                        ]
                                    ]
                                ],
                            );
                            $personality_check = true;
                        } elseif ($personality_check != true) {
                            array_push($prepareData, [$category => ['score' => $record[$category . '_score'], 'level' => $record[$category . '_level']]]);
                        }
                    }
                }
                $record->scores = $prepareData;
                $record->save();
            }

            DB::commit();
            // Schema::table('assessment_scores', function (Blueprint $table) {
            //     $table->dropColumn([
            //         'anxiety_score',
            //         'depression_score',
            //         'stress_score',
            //         'burn_out_score',
            //         'happiness_score',
            //         'internet_addiction_score',
            //         'self_esteem_score',
            //         'resilience_score',
            //         'job_satisfaction_score',
            //         'personality_1',
            //         'personality_2',
            //         'personality_score_1',
            //         'personality_score_2',
            //         'anxiety_level',
            //         'depression_level',
            //         'stress_level',
            //         'burn_out_level',
            //         'happiness_level',
            //         'internet_addiction_level',
            //         'self_esteem_level',
            //         'resilience_level',
            //         'job_satisfaction_level',
            //     ]);
            // });
        } catch (Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('assessment_scores', 'scores')) {


            Schema::table('assessment_scores', function (Blueprint $table) {
                $table->dropColumn('scores');
            });
        }
    }
}