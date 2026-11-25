<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Log of every HappiLIFE "come back and finish" reminder, automatic or sent by
 * an admin. The automatic run reads it to decide which reminder is due next
 * and to avoid repeating itself.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('happilife_reminders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('assessment_id')->index();
            $table->string('source', 10);                 // auto | manual
            $table->unsignedTinyInteger('stage')->nullable(); // auto reminders: 1, 2, 3 ...
            $table->string('channels')->nullable();       // e.g. "inapp,push,email"
            $table->unsignedInteger('answered')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->text('message');
            $table->unsignedBigInteger('sent_by_admin_id')->nullable();
            $table->timestamps();

            $table->index(['assessment_id', 'source', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('happilife_reminders');
    }
};
