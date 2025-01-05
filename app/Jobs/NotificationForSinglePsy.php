<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Http;


class NotificationForSinglePsy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $deviceToken;
    public $message;



    public function __construct($deviceToken, $message)
    {
        $this->deviceToken = $deviceToken;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $deviceToken = $this->deviceToken;
        $message = $this->message;


        $apiURL = 'https://exp.host/--/api/v2/push/send';
        $postInput = [
            'to' => $deviceToken,
            'title' => 'HappiMynd',
            'body' => $message,
        ];

        $headers = [
            'Content-Type: application/json'
        ];
  
        $response = Http::withHeaders($headers)->post($apiURL, $postInput);

        $statusCode = $response->status();
        $responseBody = json_decode($response->getBody(), true);

    }
}
