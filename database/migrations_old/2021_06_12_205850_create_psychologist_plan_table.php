<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePsychologistPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psychologist_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('psychologist_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->float('selling_price')->default(0);
            $table->float('discount')->default(0);
            $table->float('cost_price')->default(0);
            $table->string('day');
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
        Schema::dropIfExists('psychologist_plan');
    }
}
