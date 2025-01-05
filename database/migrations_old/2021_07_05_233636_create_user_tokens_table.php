<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('token_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::table('tokens', function(Blueprint $table){
            $table->integer('use_limit')->default(1);
            $table->integer('use_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_tokens');
        Schema::table('tokens', function(Blueprint $table){
            $table->dropColumn([
                'use_limit',
                'use_count'
            ]);
           }
        );
    }
}
