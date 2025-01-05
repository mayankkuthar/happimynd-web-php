<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Admin::create([
            'first_name' => 'super',
            'last_name' => 'admin',
            'email' => 'superadmin@happimynd.com',
            'password' => 'password',
        ]);

        $admin->assignRole('super-admin');

        $admin = Admin::create([
            'first_name' => 'admin',
            'last_name' => 'admin',
            'email' => 'admin@happimynd.com',
            'password' => 'password',
        ]);

        $admin->assignRole('admin');

        $admin = Admin::create([
            'first_name' => 'content',
            'last_name' => 'writer',
            'email' => 'content-writer@happimynd.com',
            'password' => 'password',
        ]);

        $admin->assignRole('content-writer');

        $admin = Admin::create([
            'first_name' => 'MR.',
            'last_name' => 'psychologist',
            'email' => 'psychologist@happimynd.com',
            'password' => 'password',
        ]);

        $admin->assignRole('psychologist');
    }
}
