<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWolFillAreaColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_characteristics', function (Blueprint $table) {
            $table->tinyInteger('WOL_fill_area')->nullable()->after('WOL_representation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_characteristics', function (Blueprint $table) {
            $table->dropColumn('WOL_fill_area');
        });
    }
}
