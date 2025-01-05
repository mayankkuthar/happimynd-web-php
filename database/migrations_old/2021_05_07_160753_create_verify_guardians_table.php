<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerifyGuardiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verify_guardians', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('otp')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('verified')->default(0);
            $table->string('session')->nullable();
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
        Schema::dropIfExists('verify_guardians');
    }
}
