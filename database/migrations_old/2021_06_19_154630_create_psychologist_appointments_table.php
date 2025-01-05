<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePsychologistAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psychologist_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('psychologist_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('date')->nullable();
            $table->string('time_slot')->nullable();
            $table->string('appointment_status')->default('pending');
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
        Schema::dropIfExists('psychologist_appointments');
    }
}
