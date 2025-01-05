<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OurServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared("
        INSERT INTO `data_contents` ( `title`, `content`, `image`, `preference`, `data_group_id`, `deleted_at`, `created_at`, `updated_at`, `carousel_section_id`) VALUES
        ('A Simple test that will give you information on your health.', ' Our virtues help us in thriving in life, while our vices are the opportunities to improve. This is where HappiLIFE can assist you in knowing yourself better with a simple exercise based on your life and experiences. You will get to learn about your strengths, weaknesses and various other dimensions of your personality by completing the excercise. When you are aware of the parameters of your emotions and intellect, you can start your journey towards holistic wellness. Consider HappiLIFE as your companion supporting you in becoming a better version of yourself.', NULL, NULL, 7, NULL, '2021-10-19 16:22:58', '2021-10-19 16:22:58', NULL);
        ");
    }
}
