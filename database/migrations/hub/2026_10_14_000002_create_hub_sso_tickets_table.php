<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHubSsoTicketsTable extends Migration
{
    /**
     * Single-use login codes for external games, valid 120 s.
     * Existing rows do not need migrating.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('hub')->create('sso_tickets', function (Blueprint $table) {
            $table->char('code', 64)->primary();          // 64 hex chars
            $table->char('user_id', 36);
            $table->string('game_id', 50);
            $table->dateTime('expires_at', 3);
            $table->dateTime('used_at', 3)->nullable();
            $table->dateTime('created_at', 3)->default(\Illuminate\Support\Facades\DB::raw('CURRENT_TIMESTAMP(3)'));

            $table->index('user_id', 'idx_sso_user');
            $table->index('expires_at', 'idx_sso_expires');
            $table->foreign('user_id', 'fk_sso_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::connection('hub')->dropIfExists('sso_tickets');
    }
}
