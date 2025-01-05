<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileAndFeaturedColumnToPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('posts');
        Schema::create('posts', function (Blueprint $table) {
            //
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->text('description');
            $table->string('thumbnail');
            $table->string('media',255)->nullable();
            $table->foreignId('post_category_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('publish_status')->default(0)->comment('0=>Draft, 1=>Published');
            $table->boolean('restricted_content')->default(0)->comment('0=>Free, 1=>Paid');
            $table->boolean('featured')->default(0);
            $table->engine = 'InnoDB';
            $table->softDeletes();
            $table->timestamps();
                        
        });
        Schema::enableForeignKeyConstraints();
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('posts');
        Schema::enableForeignKeyConstraints();
    }
}
