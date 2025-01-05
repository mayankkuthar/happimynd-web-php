<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared("INSERT INTO `quotes` (`id`, `quote`, `image_link`, `author`, `created_at`, `updated_at`) VALUES
        (1, '<p>Life is like riding a bicycle,</p><p><strong>To keep your Balance,</strong></p><p>You must be keep moving.</p>', '1637005041-quote.jfif', '', '2021-10-13 19:34:47', '2021-10-15 09:43:04');"
        
        );
    }
}
