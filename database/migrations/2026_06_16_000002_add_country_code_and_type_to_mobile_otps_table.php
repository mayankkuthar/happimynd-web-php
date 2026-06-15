<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mobile_otps', function (Blueprint $table) {
            $table->string('country_code')->nullable()->after('otp');
            $table->string('type')->nullable()->after('country_code');
        });
    }

    public function down()
    {
        Schema::table('mobile_otps', function (Blueprint $table) {
            $table->dropColumn(['country_code', 'type']);
        });
    }
};
