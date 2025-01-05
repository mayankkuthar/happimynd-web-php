<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EducationServiceAuthor;

class EducationServiceAuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $author = new EducationServiceAuthor();
        $author->id = 1;
        $author->name = "Ravikant Suman, Jeff Bezoz";
        $author->save();
    }
}