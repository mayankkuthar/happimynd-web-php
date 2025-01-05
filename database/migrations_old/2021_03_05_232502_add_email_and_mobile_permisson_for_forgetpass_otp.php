<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailAndMobilePermissonForForgetpassOtp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('verify_users', function (Blueprint $table) {
            //
            $table->boolean('forget_email_permission')->default(true);
            $table->boolean('forget_mobile_permission')->default(true);
            $table->boolean('subscribe_newsletter_blog')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('verify_users', function (Blueprint $table) {
            //
        });
    }
}
