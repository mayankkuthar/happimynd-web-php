<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeFkOfferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            if (Schema::hasColumn('offers', 'package_id')) {
                $table->dropForeign(['package_id']);
                $table->renameColumn('package_id', 'plan_id');
                $table->foreign('plan_id')->references('id')->on('plans')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            }
        });
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $doctrineTable = $sm->listTableDetails('offers');
        if ($doctrineTable->hasIndex('offers_package_id_foreign')) {
            DB::statement("ALTER TABLE `" . ENV('DB_DATABASE') . "`.`offers` DROP INDEX `offers_package_id_foreign`, ADD INDEX `offers_plan_id_foreign` (`plan_id`) USING BTREE;");
        } else {
            DB::statement("ALTER TABLE `" . ENV('DB_DATABASE') . "`.`offers` ADD INDEX `offers_plan_id_foreign` (`plan_id`) USING BTREE;");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
