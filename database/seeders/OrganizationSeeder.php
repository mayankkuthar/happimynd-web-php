<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Token;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Organization::factory()
            ->count(30)
            ->create();
        // Organization::factory()
        //     // ->times(30)
        //     // ->hasToken(3)
        //     ->create();
    }
}
