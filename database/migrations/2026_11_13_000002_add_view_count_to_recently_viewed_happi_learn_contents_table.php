<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('recently_viewed_happi_learn_contents')
            || Schema::hasColumn('recently_viewed_happi_learn_contents', 'view_count')) {
            return;
        }
        Schema::table('recently_viewed_happi_learn_contents', function (Blueprint $table) {
            // Existing rows represent at least one view.
            $table->unsignedInteger('view_count')->default(1)->comment('How many times the user opened this HappiLEARN content');
        });
    }

    public function down()
    {
        if (!Schema::hasTable('recently_viewed_happi_learn_contents')
            || !Schema::hasColumn('recently_viewed_happi_learn_contents', 'view_count')) {
            return;
        }
        Schema::table('recently_viewed_happi_learn_contents', function (Blueprint $table) {
            $table->dropColumn('view_count');
        });
    }
};
