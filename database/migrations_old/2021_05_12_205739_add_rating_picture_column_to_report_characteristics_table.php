<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRatingPictureColumnToReportCharacteristicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_characteristics', function (Blueprint $table) {
            $table->foreignId('rating_picture_id')->nullable()->constrained();
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
            $table->dropConstrainedForeignId('rating_picture_id');
        });
    }
}
