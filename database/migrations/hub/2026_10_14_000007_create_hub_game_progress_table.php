<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHubGameProgressTable extends Migration
{
    /**
     * Saved progress for games with levels and scores (card-memory-game,
     * one-line-draw, emotional-tetris). One row per signed-in player per
     * game; `data` is the game's whole save as JSON. Replaces the old
     * per-game tables (schema.mysql.sql).
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('hub')->create('game_progress', function (Blueprint $table) {
            $table->char('user_id', 36);
            $table->string('game_slug', 60);
            $table->mediumText('data');     // JSON object, capped at 64 KB by the API
            $table->dateTime('updated_at', 3); // UTC, set on every save

            $table->primary(['user_id', 'game_slug']);

            // Drop this constraint if `users` lives in another database; the
            // API already checks that the user exists before saving.
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::connection('hub')->dropIfExists('game_progress');
    }
}
