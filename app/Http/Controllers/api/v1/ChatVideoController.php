<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Auth;
use App\Models\User;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;
use Validator;
use App\Models\ChatChannel;
use App\Models\Psychologist;
use Guard;


class ChatVideoController extends Controller
{

    public function chatFromUser(Request $request){
        $user = Auth::user();
        $assigned_psychologist = Psychologist::orderBy('id' , 'desc')->first();

        $psychologist_id = $assigned_psychologist->id;

        $check_channel_exit = ChatChannel::where('user_id' , $user->id)->where('psychologist_id' , $psychologist_id)->first();

        if($check_channel_exit){
            return response()->json(['message' => 'Channel get sucessfully.' , 'channel_id' => $check_channel_exit->channel_id ]);
        }else{
            $channel_id = $this->getChannelID($user);

            $data = [
                'channel_id' => $channel_id,
                'user_id' => $user->id,
                'psychologist_id' => $psychologist_id,
            ];
            ChatChannel::create($data);

            return response()->json(['message' => 'Channel get sucessfully.' , 'channel_id' => $channel_id ]);
        }

    }


    public function getChannelID($user){
        $sid = getenv("TWILIO_AUTH_SID");
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio = new Client($sid, $token);

        $unique = rand('1111','9999');

        $conversation = $twilio->conversations->v1->conversations
                                                    ->create([
                                                               "friendlyName" => $user->username.'_'.$unique,
                                                           ]
                                                    );
        $channel_id = $conversation->sid;
        return $channel_id;

    }

    

    public function chatFromPsychologist(Request $request){
        $psychologist = Auth::guard('psychologist')->user();
        
        $message = [
            'user_id.required'      =>  'Please enter user ID.',
        ];
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $user_id = $request->user_id;

        $check_channel_exit = ChatChannel::where('user_id' , $user_id)->where('psychologist_id' , $psychologist->id)->first();

        if($check_channel_exit){
            return response()->json(['message' => 'Channel get sucessfully.' , 'channel_id' => $check_channel_exit->channel_id ]);
        }else{

            return response()->json(['message' => 'Channel does not exist.']);
        }

    }




    public function generateToken(Request $request)
    {
         $user = Auth::user();

            // $twilioAccountSid = 'AC5d4615017e291c7ed2198089849b30a1';
            // $twilioApiKey = 'SKdb5849a0073b4098383f0d70f7a9b49c';
            // $twilioApiSecret = '3gpDIeM1nxYfs4LsSjRr6u4vh2Hb0ldU';
            // // Required for Chat grant
            // $serviceSid = 'IS0d1914f3857d4d1db5109eb1fc9611d5';

            $twilioAccountSid = getenv("TWILIO_AUTH_SID");
            $twilioApiKey = getenv("TWILIO_API_SID");
            $twilioApiSecret = getenv("TWILIO_API_SECRET");
            // Required for Chat grant
            $serviceSid = getenv("TWILIO_SERVICE_SID");
            // choose a random username for the connecting user
            $identity = $user->username;

            // Create access token, which we will serialize and send to the client
            $token = new AccessToken(
                $twilioAccountSid,
                $twilioApiKey,
                $twilioApiSecret,
                3600,
                $identity
            );

            // Create Chat grant
            $chatGrant = new ChatGrant();
            $chatGrant->setServiceSid($serviceSid);

            // Add grant to token
            $token->addGrant($chatGrant);

            // render token to string
            return response()->json(['message' => 'Token get sucessfully.' , 'token' => $token->toJWT()]);

            // echo $token->toJWT();
    }




    // public function videoRoom(){
    //     $sid = getenv("TWILIO_AUTH_SID");
    //     $token = getenv("TWILIO_AUTH_TOKEN");
    //     $twilio = new Client($sid, $token);

    //     $room = $twilio->video->v1->rooms
    //                               ->create(["uniqueName" => "DailyStandup"]);

    //     print($room->sid);
    // }



}
