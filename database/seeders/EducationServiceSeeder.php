<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EducationServiceType;

class EducationServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $mostPopular = new EducationServiceType();
        $mostPopular->id = 1;
        $mostPopular->name = "Most Popular";
        $mostPopular->save();

        $recommended = new EducationServiceType();
        $recommended->id = 2;
        $recommended->name = "Recommended";
        $recommended->save();
    }
}