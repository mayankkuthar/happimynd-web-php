<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $featured = new Post();
        $featured->title = 'Let’s get to know the trends in market';
        $featured->slug = Str::slug('Let’s get to know the trends in market '. Str::random(3));
        $featured->description = 'We are dreamers, scientists, engineers, writers, 
        artists. Each of us has a personal reason to try to create a more emotionally 
        resilient world. We are dreamers, scientists, engineers, writers, artists. Each 
        of us has a personal reason to try to create a more emotionally resilient world.
        We are dreamers, scientists, engineers, writers, artists. Each of us has a personal 
        reason to try to create a more emotionally resilient world.We are dreamers, scientists, 
        engineers, writers, artists. Each of us has a personal reason to try to create a more 
        emotionally resilient world. We are dreamers, scientists, engineers, writers, artists. 
        Each of us has a personal reason to try to create a more emotionally resilient world.

        We are dreamers, scientists, engineers, writers, artists. Each of us has a personal 
        reason to try to create a more emotionally resilient world. We are dreamers, scientists, 
        engineers, writers, artists. Each of us has a personal reason to try to create a more emotionally resilient world.
        
        We are dreamers, scientists, engineers, writers, artists. Each of us has a personal 
        reason to try to create a more emotionally resilient world. We are dreamers, scientists,
         engineers, writers, artists. Each of us has a personal reason to try to create a more 
         emotionally resilient world. We are dreamers, scientists, engineers, writers, artists. 
         Each of us has a personal reason to try to create a more emotionally resilient world.We 
         are dreamers, scientists, engineers, writers, artists. Each of us has a personal reason 
         to try to create a more emotionally resilient world.';

        $featured->thumbnail = '';
        $featured->media = '';
        $featured->post_category_id = 1;
        $featured->publish_status = 1;
        $featured->restricted_content = 0;
        $featured->featured = 1;
        $featured->save();

        for ($i=0; $i < 4 ; $i++) { 

            $blog1 = new Post();
            $blog1->title = 'Fundraising fraud';
            $blog1->slug = Str::slug('Fundraising fraud '. Str::random(3));
            $blog1->description = 'How to make sure your money supports the right cause';
            $blog1->thumbnail = '';
            $blog1->media = '';
            $blog1->post_category_id = 1;
            $blog1->publish_status = 1;
            $blog1->restricted_content = 0;
            $blog1->featured = 0;
            $blog1->save();
    
            $video = new Post();
            $video->title = 'Fundraising fraud';
            $video->slug = Str::slug('Fundraising fraud '. Str::random(3));
            $video->description = 'How to make sure your money supports the right cause';
            $video->thumbnail = '';
            $video->media = '1616176177-Sample Video 1080x720 1mb.mp4.mp4';
            $video->post_category_id = 2;
            $video->publish_status = 1;
            $video->restricted_content = 0;
            $video->featured = 0;
            $video->save();

    
            $audio = new Post();
            $audio->title = 'Fundraising fraud';
            $audio->slug = Str::slug('Fundraising fraud '. Str::random(3));
            $audio->description = 'How to make sure your money supports the right cause';
            $audio->thumbnail = '';
            $audio->media = '1616891319-file_example_MP3_1MG.mp3';
            $audio->post_category_id = 3;
            $audio->publish_status = 1;
            $audio->restricted_content = 0;
            $audio->featured = 0;
            $audio->save();
        }
    }
}
