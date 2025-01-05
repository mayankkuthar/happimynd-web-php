<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('option_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('option_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->integer('weightage');
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
        Schema::dropIfExists('option_questions');
    }
}
