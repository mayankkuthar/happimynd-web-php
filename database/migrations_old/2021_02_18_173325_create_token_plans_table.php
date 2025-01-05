<?php

use App\Models\Token;
use App\Models\TokenPlan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTokenPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('token_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('token_id')->onDelete('cascade')->onUpdate('cascade')->constrained();
            $table->foreignId('plan_id')->onDelete('cascade')->onUpdate('cascade')->constrained();
            $table->tinyInteger('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
        $tokens = Token::all();
        foreach ($tokens as $token) {
            TokenPlan::create([
                'token_id' => $token->id,
                'plan_id' => $token->package_id,
            ]);
        }
        if (Schema::hasColumn('tokens', 'package_id')) {
            Schema::disableForeignKeyConstraints();
            Schema::table('tokens', function (Blueprint $table) {
                $table->dropColumn('package_id');
            });
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('token_plans');
    }
}
