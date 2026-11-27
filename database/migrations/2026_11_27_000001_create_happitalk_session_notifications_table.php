<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Log of HappiTALK booking intimations / session reminders sent to users and
 * psychologists. Also stops the every-minute cron sending the same reminder twice.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('happitalk_session_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('happitalk_session_id');
            $table->string('kind', 20);        // booking | 24h | 1h | 5m | start | reminder
            // start time the message was about; a rescheduled session gets its reminders again
            $table->dateTime('session_starts_at')->nullable();
            $table->string('recipient', 20);   // user | psychologist
            $table->string('channel', 10);     // push | email
            $table->string('status', 10);      // queued | skipped
            $table->string('detail')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable(); // set when sent from the admin panel
            $table->timestamps();
            $table->index(['happitalk_session_id', 'kind', 'session_starts_at'], 'htsn_session_kind_start_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('happitalk_session_notifications');
    }
};
