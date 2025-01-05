<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDurationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duration_types', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->comment('1->onetime 2-> Session 3-> Year');
            $table->integer('value')->comment('1)minutes for session 2)months for year')->default(1);
            $table->tinyInteger('frequency')->comment('example 2 sessions frequency is 2 ')->default(1);
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('duration_types');
        Schema::enableForeignKeyConstraints();
    }
}
