<?php

namespace Database\Seeders;

use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('offers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Offer::create([
            'name' => 'starting',
            'discount' => 100,
            'special_inaugral_price' => 0,
            'valid' => true,
            'start' => Carbon::now(),
            'package_id' => 1
        ]);
        Offer::create([
            'name' => 'starting',
            'discount' => 40,
            'special_inaugral_price' => 299,
            'valid' => true,
            'start' => Carbon::now(),
            'package_id' => 2
        ]);
        Offer::create([
            'name' => 'starting',
            'discount' => 40,
            'special_inaugral_price' => 1200,
            'valid' => true,
            'start' => Carbon::now(),
            'package_id' => 6
        ]);
        Offer::create([
            'name' => 'starting',
            'discount' => 50,
            'special_inaugral_price' => 2000,
            'valid' => true,
            'start' => Carbon::now(),
            'package_id' => 7
        ]);
        Offer::create([
            'name' => 'starting',
            'discount' => 55,
            'special_inaugral_price' => 3600,
            'valid' => true,
            'start' => Carbon::now(),
            'package_id' => 8
        ]);
        Offer::create([
            'name' => 'starting',
            'discount' => 0,
            'special_inaugral_price' => 1099,
            'valid' => true,
            'start' => Carbon::now(),
            'package_id' => 5
        ]);
        Offer::create([
            'name' => 'starting',
            'discount' => 0,
            'special_inaugral_price' => 1199,
            'valid' => true,
            'start' => Carbon::now(),
            'package_id' => 4
        ]);
    }
}
