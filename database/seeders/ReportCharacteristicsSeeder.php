<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class ReportCharacteristicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'Depression' => [
                [
                    'minimum_score' => 0,
                    'maximum_score' => 9,
                    'meter_scale_level_name' => 'Normal',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 10,
                    'maximum_score' => 13,
                    'meter_scale_level_name' => 'Mild',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 14,
                    'maximum_score' => 20,
                    'meter_scale_level_name' => 'Moderate',
                    'summary' => 'sumary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 21,
                    'maximum_score' => 27,
                    'meter_scale_level_name' => 'Severe',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 28,
                    'maximum_score' => 200,
                    'meter_scale_level_name' => 'Extremely Severe',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
            ],
            'Anxiety' => [
                [
                    'minimum_score' => '0',
                    'maximum_score' => '7',
                    'meter_scale_level_name' => 'Normal',
                    'summary' => 'sumary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => '1',
                    'show_meter_scale' => '1'
                ],
                [
                    'minimum_score' => '8',
                    'maximum_score' => '9',
                    'meter_scale_level_name' => 'Mild',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => '1',
                    'show_meter_scale' => '1'
                ],
                [
                    'minimum_score' => '10',
                    'maximum_score' => '14',
                    'meter_scale_level_name' => 'Moderate',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => '1',
                    'show_meter_scale' => '1'
                ],
                [
                    'minimum_score' => '15',
                    'maximum_score' => '19',
                    'meter_scale_level_name' => 'Severe',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => '1',
                    'show_meter_scale' => '1'
                ],
                [
                    'minimum_score' => '20',
                    'maximum_score' => '200',
                    'meter_scale_level_name' => 'Extremely Severe',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => '1',
                    'show_meter_scale' => '1'
                ],
            ],
            'Stress' => [
                [
                    'minimum_score' => 0,
                    'maximum_score' => 14,
                    'meter_scale_level_name' => 'Normal',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 15,
                    'maximum_score' => 18,
                    'meter_scale_level_name' => 'Mild',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 19,
                    'maximum_score' => 25,
                    'meter_scale_level_name' => 'Moderate',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 26,
                    'maximum_score' => 33,
                    'meter_scale_level_name' => 'Severe',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 34,
                    'maximum_score' => 200,
                    'meter_scale_level_name' => 'Extremely Severe',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
            ],
            'Burnout' => [
                [
                    'minimum_score' => 6,
                    'maximum_score' => 7,
                    'meter_scale_level_name' => 'Normal',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 8,
                    'maximum_score' => 13,
                    'meter_scale_level_name' => 'Mild',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 14,
                    'maximum_score' => 20,
                    'meter_scale_level_name' => 'Moderate',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 21,
                    'maximum_score' => 24,
                    'meter_scale_level_name' => 'Severe',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 25,
                    'maximum_score' => 30,
                    'meter_scale_level_name' => 'Extremely Severe',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
            ],
            'Happiness' => [
                [
                    'minimum_score' => 280,
                    'maximum_score' => 300,
                    'meter_scale_level_name' => 'Extremely Happy',
                    'summary' => 'summary',
                    'WOL_representation' => 'straight',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 250,
                    'maximum_score' => 280,
                    'meter_scale_level_name' => 'Happy',
                    'summary' => 'summary',
                    'WOL_representation' => 'straight',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 200,
                    'maximum_score' => 250,
                    'meter_scale_level_name' => 'Moderately Happy',
                    'summary' => 'summary',
                    'WOL_representation' => 'straight',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 0,
                    'maximum_score' => 199, 'meter_scale_level_name' => 'Unhappy',
                    'summary' => 'summary',
                    'WOL_representation' => 'straight',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
            ],
            'Internet Addiction' => [
                [
                    'minimum_score' => 0,
                    'maximum_score' => 11,
                    'meter_scale_level_name' => 'Normal',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 12,
                    'maximum_score' => 17,
                    'meter_scale_level_name' => 'Mild',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 18,
                    'maximum_score' => 28,
                    'meter_scale_level_name' => 'Moderate',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 29,
                    'maximum_score' => 35,
                    'meter_scale_level_name' => 'Severe',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
            ],
            'Self Esteem' => [
                [
                    'minimum_score' => 36,
                    'maximum_score' => 40,
                    'meter_scale_level_name' => 'Very High',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 31,
                    'maximum_score' => 35,
                    'meter_scale_level_name' => 'High',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 26,
                    'maximum_score' => 30,
                    'meter_scale_level_name' => 'Average',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 16,
                    'maximum_score' => 25,
                    'meter_scale_level_name' => 'Low',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 10,
                    'maximum_score' => 15,
                    'meter_scale_level_name' => 'VeryLow',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
            ],
            'State Anxiety' => [
                [
                    'minimum_score' => 0,
                    'maximum_score' => 14,
                    'meter_scale_level_name' => 'Normal',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 15,
                    'maximum_score' => 24,
                    'meter_scale_level_name' => 'Has State Anxiety',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
            ],
            'Trait Anxiety' => [
                [
                    'minimum_score' => 0,
                    'maximum_score' => 14,
                    'meter_scale_level_name' => 'Normal',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 15,
                    'maximum_score' => 24,
                    'meter_scale_level_name' => 'Has Trait Anxiety',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
            ],
            'Resilience' => [
                [
                    'minimum_score' => 1.00,
                    'maximum_score' => 2.99,
                    'meter_scale_level_name' => 'Low Resilience',
                    'summary' => 'summary',
                    'WOL_representation' => 'straight',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 3.00,
                    'maximum_score' => 4.30,
                    'meter_scale_level_name' => 'Normal Resilience',
                    'summary' => 'summary',
                    'WOL_representation' => 'straight',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 4.31,
                    'maximum_score' => 5.00,
                    'meter_scale_level_name' => 'High Resilience',
                    'summary' => 'summary',
                    'WOL_representation' => 'straight',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
            ],
            'Job Satisfaction' => [
                [
                    'minimum_score' => 42,
                    'maximum_score' => 50,
                    'meter_scale_level_name' => 'Very High',
                    'summary' => 'summary',
                    'WOL_representation' => 'straight',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 39,
                    'maximum_score' => 41,
                    'meter_scale_level_name' => 'High',
                    'summary' => 'summary',
                    'WOL_representation' => 'straight',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 32,
                    'maximum_score' => 38,
                    'meter_scale_level_name' => 'Average',
                    'summary' => 'summary',
                    'WOL_representation' => 'straight',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 27,
                    'maximum_score' => 31,
                    'meter_scale_level_name' => 'Low',
                    'summary' => 'summary',
                    'WOL_representation' => 'straight',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 10,
                    'maximum_score' => 26,
                    'meter_scale_level_name' => 'Very Low',
                    'summary' => 'summary',
                    'WOL_representation' => 'straight',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
            ],
            'Substance Abuse' => [
                [
                    'minimum_score' => 1,
                    'maximum_score' => 100,
                    'meter_scale_level_name' => 'Substance Abuse Disorder Exists',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
                [
                    'minimum_score' => 0,
                    'maximum_score' => 0,
                    'meter_scale_level_name' => 'Substance Abuse Disorder Exists',
                    'summary' => 'summary',
                    'WOL_representation' => 'inverse',
                    'included_in_report' => 1,
                    'show_meter_scale' => 1
                ],
            ]
        ];

        $categories = Category::all();
        foreach ($categories as $category) {
            if ($category->name != 'Personality') {
                $category->reportCharacteristics()->createMany($data[$category->name]);
            }
        }
    }
}
