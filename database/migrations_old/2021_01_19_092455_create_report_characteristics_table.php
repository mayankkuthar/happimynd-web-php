<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportCharacteristicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_characteristics', function (Blueprint $table) {
            $table->id();
            $table->float('minimum_score');
            $table->float('maximum_score')->nullable();
            $table->string('meter_scale_level_name');
            $table->foreignId('category_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->text('summary');
            $table->enum('WOL_representation', ['straight', 'inverse', 'none']);
            $table->boolean('included_in_report');
            $table->boolean('show_meter_scale');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_characteristics');
    }
}
