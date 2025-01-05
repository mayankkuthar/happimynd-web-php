<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->decimal('price',12,2)->unsigned()->default(0);
            $table->float('discount',8,2)->unsigned()->default(0);
            $table->string('buy_link')->nullable();
            $table->string('coupon')->nullable();
            $table->foreignId('service_type_group_id')->constrained()->onDelete('cascade');
            $table->boolean('publish_status')->default(0)->comment('0=>Draft, 1=>Published');
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
        Schema::dropIfExists('other_services');
    }
}