<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatBotQuestionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_bot_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_bot_category_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('language')->default('english');
            $table->text('question');
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
        Schema::dropIfExists('chat_bot_questions');
    }
}
