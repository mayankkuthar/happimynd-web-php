<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BundleStatusesTableAlter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bundle_statuses', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->renameColumn('package_id', 'plan_id');
            $table->foreign('plan_id')->references('id')->on('plans')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $doctrineTable = $sm->listTableDetails('offers');
        if ($doctrineTable->hasIndex('offers_package_id_foreign')) {
            DB::statement("ALTER TABLE `" . ENV('DB_DATABASE') . "`.`bundle_statuses` DROP INDEX `bundle_statuses_package_id_foreign`, ADD INDEX `bundle_statuses_plan_id_foreign` (`plan_id`) USING BTREE;");
        } else {
            DB::statement("ALTER TABLE `" . ENV('DB_DATABASE') . "`.`bundle_statuses`  ADD INDEX `bundle_statuses_plan_id_foreign` (`plan_id`) USING BTREE;");
        }
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
