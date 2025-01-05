<?php

namespace App\Jobs;

use App\Models\Assessment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Mail;

class GenerateScreeningReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $userId;
    protected $sendMail;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $sendMail = true)
    {
        $this->userId = $userId;
        $this->sendMail = $sendMail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!env('GENERATE_PDF')) {
            return 0;
        }
        $start_time = microtime(true);
        $user = User::with(['assessment' => function ($query) {
            return $query->completedAssessment();
        }])->find($this->userId);
        \Log::debug('started GenerateScreeningJob for user:' . $user->id . ' at ' . Carbon::now()->format('d/m/y g:i a'));
        if (!$user || ($user->email == null)) {
            \Log::debug('email not available for user: ' . $user->id);
        }
        \Log::debug(json_encode($user->toArray()));
        foreach ($user->assessment as $assessment) {
            try {
                if (is_null($assessment->report)) {

                    $response = Http::get(env('NODE_URL') . '/check');
                    if ($response->ok()) {
                        $response = Http::get(env('NODE_URL') . '/pdf?reportUrl=' . env('APP_URL') . '/calculate-score?assessment_id=' . $assessment->id . '&fileName=' . $assessment->id . '_' . $user->nickname . '-ScreeningReport.pdf');
                        $res = $response->json();
                        \Log::info('response body:' . json_encode($res));
                        $assessment->report = $res['link'];
                        $assessment->save();
                    } else {
                        \Log::critical('respone not ok');
                        \Log::critical($response);
                    }
                }
                if ($this->sendMail && $user && $assessment->report && $user->isEmailVerified()) {
                    Mail::send('mail/report', [], function ($message) use ($assessment, $user) {
                        $message->to($user->email)->subject('Happimynd Screening Report');
                        $message->attach($assessment->report, [
                            'as' => 'ScreeningReport.pdf'
                        ]);
                        $message->from(env('MAIL_FROM_ADDRESS'));
                    });
                    \Log::debug('Report Mail sent to user:' . json_encode($user));
                } else {
                    \Log::info('Report mail not sent to user: ' . $user->id . ': email not verified' . json_encode($user));
                }
            } catch (Exception $e) {
                \Log::critical('Node server is down at ' . now());
                \Log::critical($e);
            }
        }
        $end_time = microtime(true);
        \Log::debug('Exit GenerateScreeningJob for user ' . $user->id . ' at ' . Carbon::now()->format('d/m/y g:i a') . ' Executed in ' . ($end_time - $start_time) . ' Sec\'s');
    }
}
