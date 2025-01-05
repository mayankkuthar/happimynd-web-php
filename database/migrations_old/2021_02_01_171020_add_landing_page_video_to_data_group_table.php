<?php

use App\Models\DataContent;
use App\Models\DataGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLandingPageVideoToDataGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DataGroup::create(
            [
                'name' => 'landing_page'
            ],
        );
        Schema::table('data_group', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_group', function (Blueprint $table) {
            //
        });
    }
}
