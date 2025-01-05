<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use Illuminate\Database\Seeder;

class PostCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $blog = new PostCategory();
        $blog->id = 1;
        $blog->name = 'blog';
        $blog->file_type = ['image/jpeg', 'image/png'];
        $blog->save();

        $video = new PostCategory();
        $video->id = 2;
        $video->name = 'video';
        $video->file_type = ['video/mp4'];
        $video->save();

        $audio = new PostCategory();
        $audio->id = 3;
        $audio->name = 'audio';
        $audio->file_type = ['audio/mpeg'];
        $audio->save();
    }
}
