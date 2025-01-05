<?php

namespace App\Listeners;

use App\Events\PushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\User;
use App\Models\UserToken;
use App\Models\NotificationList;
use App\Models\Package;
use App\Models\BundleStatus;
use Http;

class SendPushNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct($device_token,$message)
    {
        $this->device_token = $device_token;
        $this->message = $message;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\PushNotification  $event
     * @return void
     */
    public function handle(PushNotification $event)
    {
        \Log::debug('  ');
        \Log::debug('=========================== listner start ================================');

        $apiURL = 'https://exp.host/--/api/v2/push/send';
        $postInput = [
            'to' => $this->device_token,
            'title' => 'HappiMynd',
            'body' => $this->message,
        ];

        $headers = [
            'Content-Type: application/json'
        ];
  
        $response = Http::withHeaders($headers)->post($apiURL, $postInput);

        $statusCode = $response->status();
        $responseBody = json_decode($response->getBody(), true);
     
        // dd($responseBody);
        return $responseBody;

    }
}
