<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bundle_statuses', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->comment('Snapshot of when this purchase expires. NULL = never expires')->after('valid');
        });
    }

    public function down()
    {
        Schema::table('bundle_statuses', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
};
