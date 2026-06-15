<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mobile_otps', function (Blueprint $table) {
            $table->id();
            $table->string('mobile');
            $table->string('otp');
            $table->timestamp('expires_at');
            $table->string('verified_token')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('mobile');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mobile_otps');
    }
};
