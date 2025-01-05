<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaticSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('static_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_group_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('section');
            $table->foreignId('data_content_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('data_contents', function (Blueprint $table) {
            $table->string('image')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('static_sections');
        Schema::table('data_contents', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}
