<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherServiceSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_service_subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('other_service_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('mobile')->nullable();
            $table->boolean('paid');
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
        Schema::dropIfExists('other_service_subscribers');
    }
}