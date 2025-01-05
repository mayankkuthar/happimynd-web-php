<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDealIdInPsychologistAppointment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('psychologist_appointments', function (Blueprint $table) {
            $table->BigInteger('dealId')->after('appointment_status')->nullable();
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
            $table->dropColumn('dealId');
        });
    }
}
