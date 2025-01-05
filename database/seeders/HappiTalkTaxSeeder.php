<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HappitalkTax;


class HappiTalkTaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $data =[
            'tds_percentage' => '10',
        ];

        HappitalkTax::Create($data);
    }
}
