<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortColumnToBatchCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batch_category', function (Blueprint $table) {
            $table->tinyInteger('sort_order')->after('calculation_step_macro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batch_category', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
}
