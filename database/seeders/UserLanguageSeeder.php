<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserLanguage;

class UserLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $language = [
            'english',
            'hindi',
            'punjabi',
            'marathi',
            'telugu',
            'bangali',
        ];



        for ($i=0; $i <6 ; $i++) { 
            UserLanguage::create(['name' => $language[$i]]);
        }

    }
}
