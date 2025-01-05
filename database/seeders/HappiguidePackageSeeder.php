<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Plan;
use App\Models\Offer;



class HappiguidePackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $package = [
            'name' => 'HappiGUIDE',
            'description' => 'Understand the intricacies of your HappiLIFE summary and implement the right steps for improvement with a summary reading session by our emotional wellbeing expert.',
        ];

        $create_package = Package::create($package);


        $plan = [
            'package_id' => $create_package->id,
            'duration_type_id' => '5',
            'price'     => '599',
        ];

        $create_plan = Plan::create($plan);


        $offer = [
            'name' => 'starting',
            'discount' => '10',
            'price' => '539',
            'valid' => '1',
            'plan_id' => $create_plan->id,
        ];

        Offer::create($offer);


    }
}
