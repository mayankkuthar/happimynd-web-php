<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('dynamic_bundle_plans', function (Blueprint $table) {
            $table->unsignedInteger('sessions')->nullable()->comment('Number of sessions granted for session-based plans (e.g. HappiTALK). NULL = not applicable')->after('plan_id');
        });
    }

    public function down()
    {
        Schema::table('dynamic_bundle_plans', function (Blueprint $table) {
            $table->dropColumn('sessions');
        });
    }
};
