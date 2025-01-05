<?php

namespace App\BusinessModel;

use Illuminate\Database\Eloquent\Model;
use Hash;
use DB;
use Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Http;

class PushNotification extends Model
{
    

    public function sendNotification($deviceToken , $message , $title = "HappiMynd"){

        $apiURL = 'https://exp.host/--/api/v2/push/send';
        $postInput = [
            'to' => $deviceToken,
            'title' => $title,
            'body' => $message,
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







