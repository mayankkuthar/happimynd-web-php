<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\UserToken;
use App\Models\NotificationList;
use App\Models\Package;
use App\Models\BundleStatus;

use DB;
use Http;
use App\Events\PushNotification;
use App\Jobs\NotificationForSingleUser;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $user_language;
    public $message;
    public $type;

    public function __construct($user_language, $message , $type)
    {
         $this->user_language = $user_language;
         $this->message = $message;
         $this->type = $type;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $deviceToken = 'ExponentPushToken[N-B5WvAhcwGW9GhuizFzK6]';
        $message = $this->message;
        $type = $this->type;
        $user_language = $this->user_language;

        $org_users = UserToken::pluck('user_id');
        if($type == 'all'){
            if($user_language == 'all'){
                $users = User::where('device_token' , '!=' ,null)->orderBy('id' , 'desc')->get();
            }else{
                $users = User::where('device_token' , '!=' ,null)->orderBy('id' , 'desc')->where('language' , $user_language)->get();
            }
        }
        if($type == 'd2c'){
            if($user_language == 'all'){
                $users = User::where('device_token' , '!=' ,null)->orderBy('id' , 'desc')->whereIn('id' , $org_users)->get();
            }else{
                $users = User::where('device_token' , '!=' ,null)->orderBy('id' , 'desc')->whereIn('id' , $org_users)->where('language' , $user_language)->get();
            }
        }
        if($type == 'normal'){
            if($user_language == 'all'){
                $users = User::where('device_token' , '!=' ,null)->orderBy('id' , 'desc')->whereNotIn('id' , $org_users)->get();
            }else{
                $users = User::where('device_token' , '!=' ,null)->orderBy('id' , 'desc')->whereNotIn('id' , $org_users)->where('language' , $user_language)->get();
            }
        }


        if($type == 'happiself_subscribed_serives'){
            $package = Package::where('name' , 'HappiSELF')->with('mobilePlans')->first();
            $plan_id = $package->mobilePlans['0']['id'];
            $user_id_who_buy_happiself = BundleStatus::where('plan_id' , $plan_id)->pluck('user_id');
            $users = User::where('device_token' , '!=' ,null)->orderBy('id' , 'desc')->whereIn('id' , $user_id_who_buy_happiself)->get();
        }



        \Log::info("Notification sending start");

        $count = 0;
        
        foreach($users as $row){
            $length_of_device_token = strlen($row->device_token);
            
            if($length_of_device_token > 20){

                $deviceToken = $row->device_token;
                $user_id = $row->id;

                // $this->sendNotification($deviceToken , $message);

                // $data = [
                //     'user_id' => $row->id,
                //     'message' => $message,
                // ];
                // NotificationList::create($data);

                dispatch(new NotificationForSingleUser($deviceToken, $message, $user_id));


                // event(new PushNotification($deviceToken,$message));

                $count = $count+1;


            }
        }

        \Log::info("******Total No. of user to whom notification has been sent*******".$count);
        \Log::info("Notification has been sent to all users");
        \Log::info("Notification has been send to ".$user_language." users of language type ".$type." __ ".$message);

    }

    // public function sendNotification($deviceToken , $message){

            //with expo

    //     $apiURL = 'https://exp.host/--/api/v2/push/send';
    //     $postInput = [
    //         'to' => $deviceToken,
    //         'title' => 'HappiMynd',
    //         'body' => $message,
    //     ];

    //     $headers = [
    //         'Content-Type: application/json'
    //     ];
  
    //     $response = Http::withHeaders($headers)->post($apiURL, $postInput);

    //     $statusCode = $response->status();
    //     $responseBody = json_decode($response->getBody(), true);
     
    //     // dd($responseBody);
    //     return $responseBody;


            //with firebase
    //     // if (!defined('API_ACCESS_KEY')) {
    //     //       define('API_ACCESS_KEY','AAAAgU10Ie8:APA91bGJe7hHUpDcN-4du-2lJcVgc0-MZhYjuTHuSIrx6kHCcuh1J1A5B0ta1quZLJxnPNBqHoL7z_RsKcDhql1XCYhzylZ2cI7zs3sdUXk0sHgWevBEL5vYu1lvWW-uM94hbZYtys7b');
    //     //     }
    //     //     // print_r($type); die;

    //     //     $not_message = array('sound' =>1,
    //     //                 'message'=>$message,
    //     //                 'notifykey'=>$message,
    //     //                 "body" => $message,
    //     //                 'data'=>$message
    //     //                 );

    //     //     $registrationIds = 'd0TlE1tuRf-yTe-mq8G3p7:APA91bFsq-QBmCI8DOdERFJ5c_GRM-spepXk5HV5_EmizPtNY6b2ystf_-AlMRVcbISKAFVOAVmwr2LwYW2cleMtR7_JBTzDpnV3Q7Q0C3HYgFu3BZQItITuqjh7AZViBOOSzWIMHr2D';

    //     //     $fields = array(
    //     //       'registration_ids' => array($registrationIds),
    //     //       'notification' => $not_message,
    //     //       'alert' => $message,
    //     //       'sound' => 'default',
    //     //       // 'Notifykey' => $notifykey,
    //     //       // 'badgeCount' => $badgeCount,
    //     //       // 'data' => $not_message
                
    //     //     );
    //     //     $headers = array(
    //     //       'Authorization: key=' . API_ACCESS_KEY,
    //     //       'Content-Type: application/json'
    //     //     );

    //     //     //print_r(json_encode($fields)); die;

    //     //     $ch = curl_init();
    //     //     curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    //     //     curl_setopt( $ch,CURLOPT_POST, true );
    //     //     curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    //     //     curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    //     //     curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

    //     //     curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($fields) );
    //     //     $result = curl_exec($ch);

    //     //     if($result == FALSE) {
    //     //       die('Curl failed: ' . curl_error($ch));
    //     //     }

    //     //     curl_close( $ch );
    //     //     // echo "<pre>";print_r($result);exit;
    //     //     //     echo "<br>";

    //     //     return  $result;



    // }

}
