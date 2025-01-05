<?php

namespace App\Jobs;

use Exception;
use Carbon\Carbon;
use App\Models\Plan;
use App\Mail\TokenEmail;
use App\Models\DataGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class TokenEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $email;
    public $tokens;
    public $planIds;
    public $companyName;
    public $file;
    public $emailBody;
    public $emailSubject;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tokens, $planIds, $companyName, $file, $emailBody, $emailSubject)
    {
        //
        $this->tokens = $tokens;
        $this->planIds = $planIds;
        $this->companyName = $companyName;
        $this->file = $file;
        $this->emailBody = $emailBody;
        $this->emailSubject = $emailSubject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            //Getting all the packages that was selected by the admin
            $packages = Plan::with('Package')->whereIn('id', $this->planIds)->get()->pluck('package.name');
            $packageNames = $packages->implode(', ');
            foreach ($this->tokens as $token) {

                //composing the body of the email to be sent to emails from excel

                $body = $this->emailBody;

                $mailDetails =  [
                    'body' => $body,
                    'subject' => $this->emailSubject,
                    'token' => $token['token'],
                    'packages' => $packageNames,
                    'name' => $token['name'],
                    'path' => $this->file,
                    'company' => $this->companyName
                ];
                $body = str_replace('[[name]]', $mailDetails['name'], $body);
                $body = str_replace('[[company]]', $mailDetails['company'], $body);
                $body = str_replace('[[packages]]', $mailDetails['packages'], $body);
                $body = str_replace('[[token]]', $mailDetails['token'], $body);
                $body = str_replace('\n', '<br>', $body);

                $mailDetails['body'] = $body;
                Mail::to($token['email'])->queue(new TokenEmail($mailDetails));
            }
        } catch (Exception $e) {
            Log::alert($e->getMessage());
            Log::alert($e);
        }
    }

    public function failed()
    {
        Log::critical('Email job via emails is failed at' . Carbon::now());
    }
}
