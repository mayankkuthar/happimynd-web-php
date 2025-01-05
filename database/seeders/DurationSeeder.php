<?php

namespace Database\Seeders;

use App\Models\Duration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('durations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Duration::create(['name' => 'Onetime pay']);
        Duration::create(['name' => '1 Year access']);
        Duration::create(['name' => '1 session']);
        Duration::create(['name' => '2 session']);
        Duration::create(['name' => '4 session']);
    }
}
