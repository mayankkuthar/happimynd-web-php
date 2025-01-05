<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class QuestionSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questions:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // dd(storage_path());
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        DB::table('questions')->truncate();
        DB::table('option_questions')->truncate();
        DB::table('report_characteristics')->truncate();
        DB::table('rating_pictures')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $i = 0;
        Artisan::call("db:seed", ['--class' => 'CategorySeeder']);
        foreach (file('questions.txt') as $line) {
            $i++;
            $data = explode(";", $line);
            if (count($data) == 2) {
                $data[1] = str_replace(PHP_EOL, '', $data[1]);
                // dd('Schizoid\r\n' == $data[1]);
                // dump($data[1]);
                $cat_id = Category::withTrashed()->where('acronymn', 'LIKE', "%{$data[1]}%")->first()->id;

                dump($i . '->' . $data[1]);
                Question::create([
                    'question' => $data[0],
                    'category_id' => $cat_id
                ]);
            }
        }
        Artisan::call("db:seed", ['--class' => 'OptionSeeder']);
        return 0;
    }
}
