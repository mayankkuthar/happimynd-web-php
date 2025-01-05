<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Token;
use App\Models\UserToken;
use Illuminate\Support\Facades\DB;
class ShiftingTokenUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tokens = Token::whereNotNull('user_id')->get();
        foreach($tokens as $token){
            UserToken::insert(['token_id' => $token->id, 'user_id' => $token->user_id, 'created_at' => $token->updated_at, 'updated_at'=> $token->updated_at ]);
        }
        Schema::table('tokens', function(Blueprint  $table){
            $table->dropForeign('tokens_user_id_foreign');
            $table->dropColumn('user_id');
        });

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
