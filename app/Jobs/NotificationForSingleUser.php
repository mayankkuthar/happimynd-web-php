<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Http;
use App\Models\User;
use App\Models\UserToken;
use App\Models\NotificationList;
use App\Models\Package;
use App\Models\BundleStatus;


class NotificationForSingleUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $deviceToken;
    public $message;
    public $user_id;



    public function __construct($deviceToken, $message, $user_id)
    {
        $this->deviceToken = $deviceToken;
        $this->message = $message;
        $this->user_id = $user_id;

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
        $user_id = $this->user_id;


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
     
        // dd($responseBody);
        // return $responseBody;

        // \Log::info(" ========================================================== ");
        // \Log::info(" ////////////////////////////////////////////////////////// ");

        // \Log::info("Notification sent to user_id--------- ".$user_id);


        $data = [
                    'user_id' => $user_id,
                    'message' => $message,
                ];
        NotificationList::create($data);

        // \Log::info("Notification saved to user notification table--------- ".$user_id);

    }
}
