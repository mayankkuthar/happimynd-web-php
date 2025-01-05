<?php

use App\Models\ChatBot\SuicidalThought;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuicidalThoughtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suicidal_thoughts', function (Blueprint $table) {
            $table->id();
            $table->longText('description')->nullable();
            $table->timestamps();
        });

        SuicidalThought::create(['description' => '']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suicidal_thoughts');
    }
}
