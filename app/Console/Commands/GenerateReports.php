<?php

namespace App\Console\Commands;

use App\Mail\ScreeningReport;
use App\Models\Assessment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Mail;

class GenerateReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generateReports {--id=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return voidp
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
        $assessments = null;
        if (count($this->option('id')) > 0) {
            $userIds = $this->option('id');

            $assessments = Assessment::whereHas('user', function ($query) use ($userIds) {
                $query->whereIn('id', $userIds);
            })->whereNull('report')->completedAssessment()->get();
        } else {
            $assessments = Assessment::with('user')->whereNull('report')->completedAssessment()->get();
        }
        $bar = $this->output->createProgressBar(count($assessments));
        $bar->start();
        foreach ($assessments as $assessment) {
            $assessment->user->generateReportAndSendMail();
            $bar->advance();
        }
        $bar->finish();
        return 0;
    }
}
