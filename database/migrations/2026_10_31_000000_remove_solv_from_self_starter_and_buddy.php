<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveSolvFromSelfStarterAndBuddy extends Migration
{
    // SELF STARTER and BUDDY were created with HappiGUIDE ticked, so they
    // granted a SOLV session they don't include. The admin can't edit a
    // bundle's services, so take HappiGUIDE out here.
    public function up()
    {
        $bundleIds = DB::table('packages')->where('bundle', 1)->whereIn('name', ['SELF STARTER', 'BUDDY'])->pluck('id');
        $happiguidePlanIds = DB::table('plans')
            ->whereIn('package_id', DB::table('packages')->where('name', 'HappiGUIDE')->pluck('id'))
            ->pluck('id');

        DB::table('dynamic_bundle_plans')
            ->whereIn('package_id', $bundleIds)
            ->whereIn('plan_id', $happiguidePlanIds)
            ->delete();
    }

    public function down()
    {
        $happiguidePlanId = DB::table('plans')
            ->whereIn('package_id', DB::table('packages')->where('name', 'HappiGUIDE')->pluck('id'))
            ->value('id');
        foreach (DB::table('packages')->where('bundle', 1)->whereIn('name', ['SELF STARTER', 'BUDDY'])->pluck('id') as $bundleId) {
            DB::table('dynamic_bundle_plans')->insert([
                'package_id' => $bundleId,
                'plan_id' => $happiguidePlanId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
