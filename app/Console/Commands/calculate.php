<?php

namespace App\Console\Commands;

use App\Models\CalculationStep;
use App\Services\AssessmentService;
use Illuminate\Console\Command;
use App\Services\Calculator;
use App\Services\Stack;

class calculate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:score';

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
        $steps = CalculationStep::all();
        $category_id = 2;
        $assessment_id = 162;
        $assessmentService = (new AssessmentService)->forAssessment($assessment_id)->calculateScore();
        $depressionScore = $assessmentService->calculateDepressionScore();
        $string = "(countop1*30)+(countop2*20)+(countop3*10)";
        $string = str_replace("countop1", '3', $string);
        $string = str_replace("countop2", '1', $string);
        $string = str_replace("countop3", '5', $string);
        dd($this->evaluate($string));
    }
    public function calculate($string)
    {
        $steps = explode('+', $string);
        $finalScore = 0;
        $allsc = 7;

        foreach ($steps as $step) {
        }
    }
}
