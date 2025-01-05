<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('nickname');
            $table->string('email')->nullable();
            $table->enum('profession', ['salaried', 'self employed', 'home maker', 'senior citizen', 'student(school)', 'entrepreneur', 'student(college/university)', 'jobseeker', 'frontline warrior']);
            $table->integer('age')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('password')->nullable();
            $table->string('mobile')->nullable();
            $table->enum('account_status', ['active', 'blocked'])->default('active');
            $table->string('avatar')->nullable();
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
        Schema::dropIfExists('users');
    }
}
