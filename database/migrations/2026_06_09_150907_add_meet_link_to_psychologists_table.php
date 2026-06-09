<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('psychologists', function (Blueprint $table) {
            $table->string('meet_link')->nullable()->after('device_token');
        });
    }

    public function down()
    {
        Schema::table('psychologists', function (Blueprint $table) {
            $table->dropColumn('meet_link');
        });
    }
};
