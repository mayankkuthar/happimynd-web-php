<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatBotReportCharacteristicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_bot_report_characteristics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_bot_category_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->float('minimum');
            $table->float('maximum')->nullable();
            $table->text('interpretation')->nullable(true);
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
        Schema::dropIfExists('chat_bot_report_characteristics');
    }
}
