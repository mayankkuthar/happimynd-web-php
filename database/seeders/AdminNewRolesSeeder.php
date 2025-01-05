<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


class AdminNewRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'user', 'guard_name' => 'admin']);
        Role::create(['name' => 'coupen', 'guard_name' => 'admin']);
        Role::create(['name' => 'plans', 'guard_name' => 'admin']);
        Role::create(['name' => 'happimynd-code', 'guard_name' => 'admin']);
        Role::create(['name' => 'organizations', 'guard_name' => 'admin']);
        Role::create(['name' => 'static-data', 'guard_name' => 'admin']);
        Role::create(['name' => 'campaigns', 'guard_name' => 'admin']);
        Role::create(['name' => 'assessments', 'guard_name' => 'admin']);
        Role::create(['name' => 'psychologists', 'guard_name' => 'admin']);
        Role::create(['name' => 'HappiBUDDY', 'guard_name' => 'admin']);
        Role::create(['name' => 'HappiLEARN', 'guard_name' => 'admin']);
        Role::create(['name' => 'HappiSELF', 'guard_name' => 'admin']);
        Role::create(['name' => 'HappiTALK', 'guard_name' => 'admin']);
        Role::create(['name' => 'HappiGUIDE', 'guard_name' => 'admin']);
    }
}
