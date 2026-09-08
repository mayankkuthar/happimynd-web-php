<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('psychologists', function (Blueprint $table) {
            $table->string('expert_category')->default('None')->after('expert_level_id');
        });
    }

    public function down()
    {
        Schema::table('psychologists', function (Blueprint $table) {
            $table->dropColumn('expert_category');
        });
    }
};
