<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHubGameContentTable extends Migration
{
    /**
     * Admin-edited in-game text, one row per customised list.
     * items = the whole list as a JSON array, in the game's own item shape.
     * No row = the game uses its built-in list.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('hub')->create('game_content', function (Blueprint $table) {
            $table->string('game_slug', 60);
            $table->string('collection', 40);   // e.g. 'thoughts', 'questions', 'end-titles'
            $table->mediumText('items');        // largest list today is ~25 KB
            $table->dateTime('updated_at', 3);

            $table->primary(['game_slug', 'collection']);
        });
    }

    public function down()
    {
        Schema::connection('hub')->dropIfExists('game_content');
    }
}
