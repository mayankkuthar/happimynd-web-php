<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataGroup;
use App\Models\DataContent;

class StaticDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DataGroup::factory()
            ->has(DataContent::factory()->count(6), 'content')
            ->create(['name' => 'terms-and-services']);
    }
}
