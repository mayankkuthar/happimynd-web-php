<?php

namespace App\BusinessModel;

use Illuminate\Database\Eloquent\Model;
use Hash;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Http;
use Twilio\Rest\Client;

class CreateTwillioRoom extends Model
{
    

    public function createRoom(){

        $unique_room_name = Date('Y-m-d_h:i:s').'_'.rand('0000000','9999999');
        
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_ACCOUNT_TOKEN');
        $twilio = new Client($sid, $token);


        return $room = $twilio->video->v1->rooms
                          ->create([
                                       "recordParticipantsOnConnect" => True,
                                       "statusCallback" => "www.google.com",
                                       "type" => "group",
                                       "uniqueName" => $unique_room_name,
                                       'ttl' => 0,
                                   ]
                          );

    }


}







