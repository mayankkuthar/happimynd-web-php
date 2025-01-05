<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('packages')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Package::create([
            'name' => 'HappiLIFE Screening',
            'description' => 'Globally validated 10 Parameter Screening Summary.',
            'duration_id' => 1,
            'regular_price' => 499
        ]);
        Package::create([
            'name' => 'HappiLIFE Summary Reading',
            'description' => 'Assisted Summary Reading by experienced emotional wellbeing expert.',
            'duration_id' => 1,
            'regular_price' => 499
        ]);
        Package::create([
            'name' => 'HappiCHAT',
            'description' => 'Self Help Content Library with personalized chat support by expert psychologists.',
            'duration_id' => 2,
            'regular_price' => 599
        ]);
        Package::create([
            'name' => 'HappiCHAT + HappiAPP',
            'description' => 'Enjoy the benifits of HappiCHAT and HappiAPP at at an unbelievable price.',
            'duration_id' => 2,
            'regular_price' => 2098,
            'bundle' => true
        ]);
        Package::create([
            'name' => 'HappiAPP',
            'description' => '100% Confidential, Anonymous, Secure, Research & Evidence based Self Help App for Emotional Well-being.',
            'duration_id' => 2,
            'regular_price' => 1499
        ]);
        Package::create([
            'name' => 'HappiTALK',
            'description' => '100% Confidential, Anonymous and Reliable therapeutic counselling through certified psychologists. Each session of max 45 mins each.',
            'duration_id' => 3,
            'regular_price' => 2000,
        ]);
        Package::create([
            'name' => 'HappiTALK',
            'description' => '100% Confidential, Anonymous and Reliable therapeutic counselling through certified psychologists. Each session of max 45 mins each.',
            'duration_id' => 4,
            'regular_price' => 2000
        ]);
        Package::create([
            'name' => 'HappiTALK',
            'description' => '100% Confidential, Anonymous and Reliable therapeutic counselling through certified psychologists. Each session of max 45 mins each.',
            'duration_id' => 5,
            'regular_price' => 2000
        ]);
    }
}
