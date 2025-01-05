<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;
use Twilio\Jwt\Grants\VideoGrant;
use Auth;
use Twilio\Rest\Client;


class VideoChatController extends Controller
{
    
    public function grantRoomAccess(Request $request){
 
            $user = Auth::user(); 

            // // Add grant to token
            // $token->addGrant($videoGrant);
            $twilioAccountSid = "AC5d4615017e291c7ed2198089849b30a1";
            // $twilioApiKey = "SK20ae9aaa40d2e9b96a6a6caae4604c42";
            // $twilioApiSecret = "F1gjqEXAYTuvWr5IFNGj13OOZKbbHmB7";

            $twilioApiKey = env('TWILIO_API_KEY');
            $twilioApiSecret = env('TWILIO_API_KEY_SECRET');

            // Required for Video grant
            $roomName = $request->room_name;
            // An identifier for your app - can be anything you'd like
            $identity =  $user->username;

            // Create access token, which we will serialize and send to the client
            $token = new AccessToken(
                $twilioAccountSid,
                $twilioApiKey,
                $twilioApiSecret,
                3600,
                $identity
            );

            // Create Video grant
            $videoGrant = new VideoGrant();
            $videoGrant->setRoom($roomName);

            // Add grant to token
            $token->addGrant($videoGrant);

            // render token to string
            // echo $token->toJWT();
 
            return response()->json(['message' => 'Token get sucessfully.' , 'token' => $token->toJWT()]);

    }



    public function createVideoRoom(Request $request){

        $unique_room_name = rand('11111' , '99999').'testRoom';
 
        // $sid = "AC5d4615017e291c7ed2198089849b30a1";
        // $token = "b3241dac126bc8e8c8535e05c1bfbae1";
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_ACCOUNT_TOKEN');
        $twilio = new Client($sid, $token);

        // $room = $twilio->video->v1->rooms
                                  // ->create(["uniqueName" => $unique_room_name]);
        // $account = $twilio->api->v2010->accounts->create();
        // return $account;

        $room = $twilio->video->v1->rooms
                          ->create([
                                       "recordParticipantsOnConnect" => True,
                                       "statusCallback" => "www.google.com",
                                       "type" => "group",
                                       "uniqueName" => $unique_room_name,
                                       'ttl' => 0,
                                   ]
                          );


        // $recording_rules = $twilio->video->v1->rooms($room->sid)
        //          ->recordingRules
        //          ->update(["rules" => [["type" => "include", "all" => true]]]);

        // print($room->sid);
        return response()->json(['message' => 'Room created sucessfully.' , 'room_id' => $room->sid , 'room_name' => $unique_room_name]);

    }



    public function makeCompositionOfRoom(Request $request){

        // $sid = "AC5d4615017e291c7ed2198089849b30a1";
        // $token = "b3241dac126bc8e8c8535e05c1bfbae1";

        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_ACCOUNT_TOKEN');
        $twilio = new Client($sid, $token);

        // return $recording = $twilio->video->v1->recordings($request->recordingSID)
        //                        ->fetch();

        // print($recording->trackName);

        $composition = $twilio->video->v1->compositions
                                 ->create($request->roomID, // roomSid
                                          [
                                              "audioSources" => ["*"],
                                              "videoLayout" => [
                                                  "grid" => [
                                                      "video_sources" => [
                                                          "*"
                                                      ]
                                                  ]
                                              ],
                                             
                                              "format" => "mp4"
                                          ]
                                 );

        print($composition->sid);

    }


    public function downloadComposition(Request $request){
        $composition_sid = $request->composition_sid;
        $room_id = $request->room_sid;


        //Check composition status that composition is completed , pending or failed
        // $sid = "AC5d4615017e291c7ed2198089849b30a1";
        // $token = "b3241dac126bc8e8c8535e05c1bfbae1";
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_ACCOUNT_TOKEN');

        $twilio = new Client($sid, $token);
        $composition = $twilio->video->v1->compositions($composition_sid)->fetch();

        //If composition completed then make url link
        if($composition->status == 'completed'){
            
            $app_key = env('TWILIO_API_KEY');
            $app_secret = env('TWILIO_API_KEY_SECRET');

            $headers = array(
                  'Content-Type: multipart/form-data',
            );
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://video.twilio.com/v1/Compositions/'.$composition_sid.'/Media?Ttl=3600' );
            // curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt($ch, CURLOPT_USERPWD, $app_key.':'.$app_secret);
            // curl_setopt( $ch,CURLOPT_POSTFIELDS, $fields );
            $result = curl_exec($ch);

            if($result == FALSE) {
              die('Curl failed: ' . curl_error($ch));
            }

            curl_close( $ch );

            $data = json_decode($result, true);

            $url = $data['redirect_to'];

                // $url = url('/download-composition-web'.'/'.$room_id.'/'.$composition_sid);
            return response()->json(["status" => "success" , 'state' =>  $composition->status ,"url" => $url]);

        }
        else{
            return response()->json(["status" => "error" , 'state' =>  $composition->status]);
        }

        
    }



    public function checkparticipantInRoom(Request $request){
        $room_id = $request->room_sid;

        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_ACCOUNT_TOKEN');

        $twilio = new Client($sid, $token);
        $participants = $twilio->video->v1->rooms($room_id)
                                  ->participants
                                  ->read(["status" => "connected"], 20);

        // foreach ($participants as $record) {
        //     print($record->sid);
        // }


        return count($participants);


    }   



    public function disconnectAllFromRoom(Request $request){
        $room_id = $request->room_sid;

        // $sid = env('TWILIO_ACCOUNT_SID');
        // $token = env('TWILIO_ACCOUNT_TOKEN');

        // $twilio = new Client($sid, $token);

        // // Get a reference to the room
        // $room = $twilio->video->v1->rooms($room_id)->fetch();

        // // Get a list of all participants in the room
        // return $participants = $room->participants->read();

        // // Loop through the list of participants and disconnect each one
        // foreach ($participants as $participant) {
        //     $participant->disconnect();
        // }

        // Your Account SID and Auth Token from twilio.com/console
        $accountSid = env('TWILIO_ACCOUNT_SID');;
        $authToken = env('TWILIO_ACCOUNT_TOKEN');
        $twilioApiBaseUrl = 'https://video.twilio.com/v1/Rooms/';

        // The room SID to disconnect participants from
        $roomSid = $room_id;

        $app_key = env('TWILIO_API_KEY');
        $app_secret = env('TWILIO_API_KEY_SECRET');

        // Get a list of all participants in the room
        $participantsUrl = $twilioApiBaseUrl . $roomSid . '/Participants';
        $ch = curl_init($participantsUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_USERPWD, "$accountSid:$authToken");
        curl_setopt($ch, CURLOPT_USERPWD, $app_key.':'.$app_secret);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $response = curl_exec($ch);
        $participants = json_decode($response, true)['participants'];

        // Loop through the list of participants and disconnect each one
        foreach ($participants as $participant) {
            $participantSid = $participant['sid'];
            $disconnectUrl = $participantsUrl . '/' . $participantSid;

            $ch = curl_init($disconnectUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            // curl_setopt($ch, CURLOPT_USERPWD, "$accountSid:$authToken");
            curl_setopt($ch, CURLOPT_USERPWD, $app_key.':'.$app_secret);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

            $response = curl_exec($ch);
        }

    }


}







