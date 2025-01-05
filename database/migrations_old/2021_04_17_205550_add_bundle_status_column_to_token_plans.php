<?php

use App\Models\BundleStatus;
use App\Models\Token;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddBundleStatusColumnToTokenPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('token_plans', function (Blueprint $table) {
            $table->foreignId('bundle_status_id')
                ->nullable()
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->constrained('bundle_statuses');
        });

        $tokens = Token::with('plans')->whereNotNull('user_id')->whereNotNull('expired_at')->get();
        foreach ($tokens as $token) {
            // dump($token->user_id);
            foreach ($token->plans as $plan) {
                $bundleStatus = BundleStatus::where('user_id', $token->user_id)->where('plan_id', $plan->plan_id)->first();
                if ($bundleStatus) {
                    // dump($token->user_id);
                    $plan->bundle_status_id = $bundleStatus->id;
                    $plan->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('token_plans', function (Blueprint $table) {
            $table->dropForeign(['bundle_status_id']);
            $table->dropColumn(['bundle_status_id']);
        });
    }
}
