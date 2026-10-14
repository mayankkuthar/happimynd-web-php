<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateHubUsersTable extends Migration
{
    /**
     * Games Hub player accounts (own table on the "hub" connection).
     * Not shared with the main Laravel "users" table.
     *
     * Run with:  php artisan migrate --path=database/migrations/hub --database=hub
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('hub')->create('users', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('email', 255);
            $table->string('username', 100);
            $table->string('name', 255);
            $table->string('password_hash', 255)->nullable();
            $table->string('avatar', 50)->nullable()->default('🧘');
            $table->dateTime('created_at', 3)->default(DB::raw('CURRENT_TIMESTAMP(3)'));
            $table->dateTime('updated_at', 3)->default(DB::raw('CURRENT_TIMESTAMP(3)'));

            // utf8mb4_0900_ai_ci is case-insensitive, so these replace the
            // Postgres LOWER(email)/LOWER(username) indexes.
            $table->unique('email', 'uq_users_email');
            $table->unique('username', 'uq_users_username');
            $table->index('created_at', 'idx_users_created');
        });

        // Match the Postgres/schema.mysql.sql ON UPDATE behaviour.
        DB::connection('hub')->statement(
            'ALTER TABLE `users` MODIFY `updated_at` DATETIME(3) NOT NULL '
            . 'DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3)'
        );
    }

    public function down()
    {
        Schema::connection('hub')->dropIfExists('users');
    }
}
