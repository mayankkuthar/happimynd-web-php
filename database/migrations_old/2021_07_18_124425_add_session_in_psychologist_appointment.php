<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionInPsychologistAppointment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('psychologist_appointments', function (Blueprint $table) {
            $table->tinyInteger('sessions')->after('appointment_status');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('psychologist_appointments', function (Blueprint $table) {
            $table->dropColumn('sessions');
        });
    }
}
