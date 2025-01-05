<?php

use App\Models\Package;
use App\Models\Plan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableCleanup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('packages', 'regular_price')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn('regular_price');
            });
        }
        if (Schema::hasColumn('offers', 'special_inaugral_price')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->renameColumn('special_inaugral_price', 'price');
            });
        }
        if (Schema::hasColumn('packages', 'duration_id')) {
            Schema::disableForeignKeyConstraints();
            Schema::table('packages', function (Blueprint $table) {
                $table->dropForeign(['duration_id']);
                $table->dropColumn('duration_id');
            });
            Schema::enableForeignKeyConstraints();
        }
        if (Schema::hasTable('durations')) {
            Schema::disableForeignKeyConstraints();
            Schema::dropIfExists('durations');
            Schema::enableForeignKeyConstraints();
        }
        Package::where('id', 7)->update(['deleted_at' => '2021-02-02 21:17:31']);
        Package::where('id', 8)->update(['deleted_at' => '2021-02-02 21:17:31']);
        Plan::where('package_id', 7)->update(['package_id' => 6]);
        Plan::where('package_id', 8)->update(['package_id' => 6]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
