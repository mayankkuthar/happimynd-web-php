<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Plan;
use App\Models\Offer;



class BundlesPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $package = [
            'name' => 'HappiSELF + HappiGUIDE',
            'description' => 'Expert help for continuous support throughout the year.',
            'bundle' => 1,
        ];
        $create_package = Package::create($package);
        $plan = [
            'package_id' => $create_package->id,
            'duration_type_id' => '5',
            'price'     => '1798',
        ];
        $create_plan = Plan::create($plan);
        $offer = [
            'name' => 'starting',
            'discount' => '20',
            'price' => '1439',
            'valid' => '1',
            'plan_id' => $create_plan->id,
        ];
        Offer::create($offer);




        $package = [
            'name' => 'HappiLEARN + HappiBUDDY',
            'description' => 'Safeguard your mental health by being aware.',
            'bundle' => 1,
        ];
        $create_package = Package::create($package);
        $plan = [
            'package_id' => $create_package->id,
            'duration_type_id' => '5',
            'price'     => '2898',
        ];
        $create_plan = Plan::create($plan);
        $offer = [
            'name' => 'starting',
            'discount' => '20',
            'price' => '2319',
            'valid' => '1',
            'plan_id' => $create_plan->id,
        ];
        Offer::create($offer);





        $package = [
            'name' => 'HappiBUDDY + HappiSELF',
            'description' => 'Get expert help access 365 days a year completely confidential.',
            'bundle' => 1,
        ];
        $create_package = Package::create($package);
        $plan = [
            'package_id' => $create_package->id,
            'duration_type_id' => '5',
            'price'     => '3598',
        ];
        $create_plan = Plan::create($plan);
        $offer = [
            'name' => 'starting',
            'discount' => '20',
            'price' => '2879',
            'valid' => '1',
            'plan_id' => $create_plan->id,
        ];
        Offer::create($offer);





        $package = [
            'name' => 'HappiLEARN + HappiBUDDY + HappiSELF',
            'description' => 'Complete Self Help Package for taking best care of inner self.',
            'bundle' => 1,
        ];
        $create_package = Package::create($package);
        $plan = [
            'package_id' => $create_package->id,
            'duration_type_id' => '5',
            'price'     => '4097',
        ];
        $create_plan = Plan::create($plan);
        $offer = [
            'name' => 'starting',
            'discount' => '25',
            'price' => '3079',
            'valid' => '1',
            'plan_id' => $create_plan->id,
        ];
        Offer::create($offer);



    }
}
