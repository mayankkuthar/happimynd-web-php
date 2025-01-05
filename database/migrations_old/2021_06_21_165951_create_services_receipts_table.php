<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('marchant_name');
            $table->float('amount');
            $table->string('currency')->default('INR');
            $table->boolean('status')->default(false);
            $table->string('order_id')->nullable();
            $table->foreignId('other_service_subscriber_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('other_service_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('services_receipts');
    }
}