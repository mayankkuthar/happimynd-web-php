<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApplicationRateEmoji;
use DB;

class ApplicationRatingEmojiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $emoji_name = [
            'Very Happy',
            'Happy',
            'Neutral',
            'Somewhat Happy',
            'Not Happy',
        ];

        $emoji_image = [
            'veryhappy.png',
            'happy.png',
            'neutral.png',
            'somewhathappy.png',
            'nothappy.png'
        ];

        for ($i=0; $i <=4 ; $i++) { 
            
            $name = $emoji_name[$i];
            $image = $emoji_image[$i];

            $data = [
                'name'  => $name,
                'image' => $image,
            ];

            ApplicationRateEmoji::create($data);

        }
    }
}
