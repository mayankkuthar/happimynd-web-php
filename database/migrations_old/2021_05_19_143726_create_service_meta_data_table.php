<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceMetaDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_meta_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('other_service_id')->constrained()->onDelete('cascade');
            $table->foreignId('education_service_author_id')->constrained()->onDelete('cascade');
            $table->decimal('discounted_price',12,2)->unsigned()->default(0.00);
            $table->integer('rating')->unsigned()->default(0);
            $table->integer('downloads')->unsigned()->default(0);
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
        Schema::dropIfExists('service_meta_data');
    }
}