<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\BatchCategory;
use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $user = User::first();
        // $categories = [
        //     'Anxiety',
        //     'Depression',
        //     'Stress',
        //     'Burnout',
        //     'Happiness',
        //     'Internet Addiction',
        //     'Personality',
        //     'Self Esteem',
        //     'State Anxiety',
        //     'Trait Anxiety',
        //     'Resilience',
        //     'Job Satisfaction',
        //     'Substance Abuse',
        //     'Personality',
        //     'Personality',
        //     'Personality',
        //     'Personality',
        //     'Personality',
        //     'Personality',
        //     'Personality',
        //     'Personality',
        // ];
        // $batch = Batch::create(['name' => 'batch1']);
        // foreach ($categories as $category) {
        //     $c = Category::factory(["name" => $category, "acronymn" => $category])->create();
        //     BatchCategory::create([
        //         'batch_id' => $batch->id,
        //         'category_id' => $c->id,
        //     ]);
        // }
        DB::unprepared(
            "
            INSERT INTO `categories` VALUES(1, 'Anxiety', 'Anxiety', 'Your Calmness', NULL, '2021-02-03 06:03:53', '2021-02-03 06:03:53');
INSERT INTO `categories` VALUES(2, 'Depression', 'Depression', 'Your Mood', NULL, '2021-02-03 06:03:53', '2021-02-03 06:03:53');
INSERT INTO `categories` VALUES(3, 'Stress', 'Stress', 'Stress Level',NULL, '2021-02-03 06:03:53', '2021-02-03 06:03:53');
INSERT INTO `categories` VALUES(4, 'Burn out', 'Burn out', 'Fatigue Levels',NULL, '2021-02-03 06:03:53', '2021-02-03 06:03:53');
INSERT INTO `categories` VALUES(5, 'Happiness', 'Happiness', 'Your Happiness',NULL, '2021-02-03 06:03:53', '2021-02-03 06:03:53');
INSERT INTO `categories` VALUES(6, 'Internet Addiction', 'Internet Addiction', 'Digital usage',NULL, '2021-02-03 06:03:53', '2021-02-03 06:03:53');
INSERT INTO `categories` VALUES(7, 'Personality', 'Paranoid', 'Your personal style',NULL, '2021-02-03 06:03:53', '2021-02-03 06:03:53');
INSERT INTO `categories` VALUES(8, 'Self Esteem', 'Self Esteem', 'Self Esteem',NULL, '2021-02-03 06:03:53', '2021-02-03 06:03:53');
INSERT INTO `categories` VALUES(9, 'Anxiety', 'State Anxiety', 'Your Calmness',NULL, '2021-02-03 06:03:53', '2021-02-03 06:03:53');
INSERT INTO `categories` VALUES(10, 'Anxiety', 'Trait Anxiety', 'Your Calmness',NULL, '2021-02-03 06:03:53', '2021-02-03 06:03:53');
INSERT INTO `categories` VALUES(11, 'Resilience', 'Resilience', 'Your Resilience',NULL, '2021-02-03 06:03:53', '2021-02-03 06:03:53');
INSERT INTO `categories` VALUES(12, 'Job Satisfaction', 'Job Satisfaction', 'Work Satisfaction',NULL, '2021-02-03 06:03:54', '2021-02-03 06:03:54');
INSERT INTO `categories` VALUES(13, 'Substance Abuse', 'Substance Abuse', 'SA','2021-02-03 06:03:54', '2021-02-03 06:03:54', '2021-02-03 06:03:54');
INSERT INTO `categories` VALUES(14, 'Personality', 'Dissocial', 'Your personal style',NULL, '2021-02-03 06:03:54', '2021-02-03 06:03:54');
INSERT INTO `categories` VALUES(15, 'Personality', 'Impulsive', 'Your personal style',NULL, '2021-02-03 06:03:54', '2021-02-03 06:03:54');
INSERT INTO `categories` VALUES(16, 'Personality', 'Borderline', 'Your personal style',NULL, '2021-02-03 06:03:54', '2021-02-03 06:03:54');
INSERT INTO `categories` VALUES(17, 'Personality', 'Histrionic', 'Your personal style',NULL, '2021-02-03 06:03:54', '2021-02-03 06:03:54');
INSERT INTO `categories` VALUES(18, 'Personality', 'Anankastic', 'Your personal style',NULL, '2021-02-03 06:03:54', '2021-02-03 06:03:54');
INSERT INTO `categories` VALUES(19, 'Personality', 'Anxious', 'Your personal style',NULL, '2021-02-03 06:03:54', '2021-02-03 06:03:54');
INSERT INTO `categories` VALUES(20, 'Personality', 'Dependent', 'Your personal style',NULL, '2021-02-03 06:03:54', '2021-02-03 06:03:54');
INSERT INTO `categories` VALUES(21, 'Personality', 'Schizoid', 'Your personal style','2021-02-03 06:03:54', '2021-02-03 06:03:54', '2021-02-03 06:03:54');
INSERT INTO `categories` VALUES(22, 'Personality', 'Personality', 'Your personal style',NULL, '2021-02-03 06:03:54', '2021-02-03 06:03:54');
            "
        );
    }
}
