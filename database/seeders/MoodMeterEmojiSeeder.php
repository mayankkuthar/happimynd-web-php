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
            'calm',
            'scared',
            'anxious',
            'confused',
            'disappointed',
            'happy',
            'angry',
            'sad',
            'frustrated',
            'nervous',
        ];

        $emoji_image = [
            'calm.png',
            'scared.png',
            'anxious.png',
            'confused.png',
            'disappointed.png',
            'happy.png',
            'angry.png',
            'sad.png',
            'frustrated.png',
            'nervous.png',
        ];

        for ($i=0; $i < 10 ; $i++) { 
            
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
