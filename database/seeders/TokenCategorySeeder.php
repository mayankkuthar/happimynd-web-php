<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryToken;
use App\Models\TokenCategory;
use Illuminate\Database\Seeder;

class TokenCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TokenCategory::create([
            'name' => 'Educative Series'
        ]);
        TokenCategory::create([
            'name' => 'Awareness Drive'
        ]);
        TokenCategory::create([
            'name' => 'Marketing Collaterals'
        ]);
        TokenCategory::create([
            'name' => 'Branding of App'
        ]);
        TokenCategory::create([
            'name' => 'Management Report'
        ]);
        TokenCategory::create([
            'name' => 'Tele Helpline'
        ]);
    }
}
