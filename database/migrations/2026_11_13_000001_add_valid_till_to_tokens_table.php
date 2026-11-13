<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('tokens', 'valid_till')) {
            return;
        }
        Schema::table('tokens', function (Blueprint $table) {
            $table->date('valid_till')->nullable()->comment('Last date (inclusive) the B2B code can be redeemed. NULL = no expiry')->after('use_count');
        });
    }

    public function down()
    {
        if (!Schema::hasColumn('tokens', 'valid_till')) {
            return;
        }
        Schema::table('tokens', function (Blueprint $table) {
            $table->dropColumn('valid_till');
        });
    }
};
