<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->integer('attempts')->nullable();
            $table->string('anxiety_score')->nullable();
            $table->string('depression_score')->nullable();
            $table->string('stress_score')->nullable();
            $table->string('burn_out_score')->nullable();
            $table->string('happiness_score')->nullable();
            $table->string('internet_addiction_score')->nullable();
            $table->string('self_esteem_score')->nullable();
            $table->string('resilience_score')->nullable();
            $table->string('job_satisfaction_score')->nullable();
            $table->string('personality_1')->nullable();
            $table->string('personality_2')->nullable();
            $table->string('personality_score_1')->nullable();
            $table->string('personality_score_2')->nullable();
            $table->string('anxiety_level')->nullable();
            $table->string('depression_level')->nullable();
            $table->string('stress_level')->nullable();
            $table->string('burn_out_level')->nullable();
            $table->string('happiness_level')->nullable();
            $table->string('internet_addiction_level')->nullable();
            $table->string('self_esteem_level')->nullable();
            $table->string('resilience_level')->nullable();
            $table->string('job_satisfaction_level')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessment_scores');
    }
}
