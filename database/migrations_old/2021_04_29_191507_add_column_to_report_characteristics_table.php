<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToReportCharacteristicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_characteristics', function (Blueprint $table) {
            $table->foreignId('batch_category_id')->after('category_id')->nullable()->constrained('batch_category')->onDelete('cascade')->onUpdate('cascade');
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
            $table->dropConstrainedForeignId('batch_category_id');
        });
    }
}
