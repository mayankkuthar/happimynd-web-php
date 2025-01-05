<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MoodMeterEmoji;

class MoodMeterEmojiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $emoji_name = [
            'delighted',
            'disappointed',
            'crying',
            'sad',
            'happy',
            'angry',
            'confused',
            'anxious',
            'scared',
        ];

        $emoji_image = [
            'delighted.png',
            'disappointed.png',
            'crying.png',
            'sad.png',
            'happy.png',
            'angry.png',
            'confused.png',
            'anxious.png',
            'scared.png',
        ];

        for ($i=0; $i < 9 ; $i++) { 
            
            $name = $emoji_name[$i];
            $image = $emoji_image[$i];

            $data = [
                'name'  => $name,
                'image' => $image,
            ];

            MoodMeterEmoji::create($data);

        }

    }
}
