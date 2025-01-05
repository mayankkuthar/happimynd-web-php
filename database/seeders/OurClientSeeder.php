<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OurClient;

class OurClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OurClient::create([
            'name' => 'Art House',
            'image' => '1636529249-Art Housing Finance.png',
            'preference' => 1
        ]);
        OurClient::create([
            'name' => '	BigFM',
            'image' => '1636529264-BIGFM.png',
            'preference' => 2
        ]);
        OurClient::create([
            'name' => 'BP',
            'image' => '1636529271-BP.png',
            'preference' => 3
        ]);
        OurClient::create([
            'name' => 'City',
            'image' => '1636529285-CityFlo.png',
            'preference' => 4
        ]);
        OurClient::create([
            'name' => 'Emaar',
            'image' => '1636529293-Emaar India.png',
            'preference' => 5
        ]);
        OurClient::create([
            'name' => 'Niyo',
            'image' => '1636529301-Niyo.png',
            'preference' => 6
        ]);
        OurClient::create([
            'name' => 'SBI',
            'image' => '1636529311-SBI.png',
            'preference' => 7
        ]);
        OurClient::create([
            'name' => 'TUV',
            'image' => '1636529320-TUV SUD.png',
            'preference' => 8
        ]);
        OurClient::create([
            'name' => 'Wework',
            'image' => '1636529330-Wework.png',
            'preference' => 9
        ]);
    }
}
