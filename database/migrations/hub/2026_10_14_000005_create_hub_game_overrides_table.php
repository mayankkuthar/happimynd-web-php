<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHubGameOverridesTable extends Migration
{
    /**
     * Admin edits to a game's library card.
     * NULL text column = use the text coded in the app. No row = fully default.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('hub')->create('game_overrides', function (Blueprint $table) {
            $table->string('slug', 60)->primary();
            $table->string('name', 60)->nullable();         // app limit 40
            $table->string('tagline', 100)->nullable();     // app limit 60
            $table->string('description', 300)->nullable(); // app limit 160
            $table->tinyInteger('hidden')->default(0);      // 1 = hidden from the library
            $table->dateTime('updated_at', 3);
        });
    }

    public function down()
    {
        Schema::connection('hub')->dropIfExists('game_overrides');
    }
}
