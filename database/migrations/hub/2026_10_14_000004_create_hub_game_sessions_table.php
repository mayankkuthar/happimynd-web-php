<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHubGameSessionsTable extends Migration
{
    /**
     * One row per play of a built-in game (analytics).
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('hub')->create('game_sessions', function (Blueprint $table) {
            $table->char('id', 36)->primary();            // UUID generated in the browser
            $table->string('game_slug', 60);
            $table->string('user_type', 12);              // 'guest' | 'registered'
            $table->char('user_id', 36)->nullable();
            $table->dateTime('started_at', 3);            // UTC
            $table->dateTime('ended_at', 3)->nullable();  // NULL while the play is open
            $table->integer('duration_sec')->nullable();  // visible-tab seconds, 0..21600
            $table->string('ended_by', 10)->nullable();   // 'done' | 'left'
            $table->text('stats')->nullable();            // JSON: [{"label": "...", "value": 12}]

            $table->index('started_at', 'idx_sessions_time');
            $table->index(['game_slug', 'started_at'], 'idx_sessions_game_time');
            $table->index('user_id', 'idx_sessions_user');
        });
    }

    public function down()
    {
        Schema::connection('hub')->dropIfExists('game_sessions');
    }
}
