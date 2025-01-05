<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Plan;
use App\Models\Offer;


class HappiselfPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $package = [
            'name' => 'HappiSELF',
            'description' => 'Self-manage your emotional wellbeing with globally validated, interactive self help tools that include mind soothing content and gamified exercises.',
        ];

        $create_package = Package::create($package);


        $plan = [
            'package_id' => $create_package->id,
            'duration_type_id' => '5',
            'price'     => '1199',
        ];

        $create_plan = Plan::create($plan);


        $offer = [
            'name' => 'starting',
            'discount' => '10',
            'price' => '1079',
            'valid' => '1',
            'plan_id' => $create_plan->id,
        ];

        Offer::create($offer);

    }
    
}
