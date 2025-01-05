<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ServiceType::create([
            'id' => 1,
            'name' => "Other Services",
            'status' => 1
        ])->group()->createMany(
            [
                [
                    'id'=> 1,
                    'service_type_id' => 1,
                    'name'=> 'HappiMynd Services',
                    'status' => 1
                ],

                [
                'id'=> 2,
                'service_type_id' => 2,
                'name'=> 'Other Services',
                'status' => 1
                ],
            ]
        );

        ServiceType::create([
            'id' => 2,
            'name' => "Educational Services",
            'status' => 1
        ])->group()->createMany(
            [

                [
                'id'=> 3,
                'service_type_id' => 2,
                'name'=> 'Most Popular',
                'status' => 1
                ],
                [
                    'id'=> 4,
                    'service_type_id' => 2,
                    'name'=> 'Recommended',
                    'status' => 1
                ],
            ]
        );




        // $happiMynd = new ServiceType();
        // $happiMynd->id = 1;
        // $happiMynd->name = "HappiMynd Services";
        // $happiMynd->save();

        // $otherService = new ServiceType();
        // $otherService->id = 2;
        // $otherService->name = "Other Services";
        // $otherService->save();
    }
}