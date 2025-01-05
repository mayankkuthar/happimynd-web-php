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
use App\Models\UserRewardPointRecord;
use App\Models\User;
use App\Models\NotificationList;


use App\BusinessModel\PushNotification;


class RewardPointToUser extends Model
{
    
    public function pushNotification(){
        return new PushNotification();
    }

    public function addRewardToUser($user_id , $points_to_be_added_to_user , $task_performed){

        $reward_data = [
            'user_id' => $user_id,
            'points_earned' => $points_to_be_added_to_user,
            'task_performed' => $task_performed,
        ];
        
        UserRewardPointRecord::create($reward_data);


        $user_details = User::where('id' , $user_id)->first();
        $users_total_reward_point = UserRewardPointRecord::where('user_id' , $user_id)->sum('points_earned');

        $pt_diff = $users_total_reward_point - $user_details->last_reward_noti_emit_number;

        // 1000 pts
        if($pt_diff >= 1000){

            $user_details->last_reward_noti_emit_number = $user_details->last_reward_noti_emit_number+1000;
            $user_details->save();
            
            $user_details_after_update_points = User::where('id' , $user_id)->first();

            $device_token = $user_details_after_update_points->device_token;
            $message = "Doesn’t it feel great to be rewarded for self work? Congratulations 🎉 👏 You Earned ".$user_details_after_update_points->last_reward_noti_emit_number." Reward Points 🙆";
            $title = "Reward Points Milestone";

            if($device_token && strlen($device_token) > 20){
                $this->pushNotification()->sendNotification($device_token,$message , $title);
            }

            $noti_data = [
                'user_id' => $user_id,
                'message' => $message,
            ];

            NotificationList::create($noti_data);
        }





    }


}







