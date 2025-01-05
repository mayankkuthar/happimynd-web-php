<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduleNotification;
use App\Models\User;
use Http;
use App\Jobs\SendNotificationJob;
use Carbon\Carbon;
use App\Models\UserToken;
use App\Models\NotificationMessage;
use App\Models\UserLanguage;




class Notification extends Controller
{
    

    public function pushNotification(Request $request){
        if($request->isMethod('GET')){

            $schedule_notification_list = ScheduleNotification::where('is_notification_delivered' , 0)
                                            ->orderByRaw("STR_TO_DATE(scheduled_date_time, '%Y-%m-%d %H:%i') ASC")
                                            ->get();

            $user_language =  UserLanguage::get();
            return view('Backend/push_notification/push_notification')
                        ->with('user_language',$user_language)
                        ->with('schedule_notification_list',$schedule_notification_list);
        }
        if($request->isMethod('POST')){ 

            $message = [
                'message.required' => 'Please enter message.',
                'type.required' => 'Please enter type.',
            ];

            $request->validate([
                'message' => 'required',
                'type' => 'required'
            ],$message);

            $user_language = $request->user_language;
            $message = $request->message;
            $type = $request->type;
 
            if($request->date_time){

                $current_date_time =  date('Y-m-d H:i');
                $strtotime_current_date_time = strtotime($current_date_time);
                $strtotime_selected_date_time = strtotime($request->date_time);

                if($strtotime_current_date_time > $strtotime_selected_date_time){
                    return back()->with('error' , 'Please select future date and time.');
                }else{
                    //Save the schedule table
                    $data = [
                        'user_language' => $request->user_language,
                        'message' => $request->message,
                        'user_type' => $type,
                        'scheduled_date_time' =>  str_replace('T' , ' ' , $request->date_time),
                    ];
                    ScheduleNotification::create($data);
                    return back()->with('success' , 'Notification will send to users according to date and time.');
                }
            }else{
                dispatch(new SendNotificationJob($user_language, $message , $type));
            }

            return back()->with('success' , 'Notification send to users successfully.');
        }
    }




    public function deleteScheduledNotification(Request $request , $id){
        ScheduleNotification::where('id' , $id)->delete();
        return back()->with('success' , 'Notification delete successfully.');
    }


    public function notificationMessages(Request $request){
        $message_list = NotificationMessage::get();
        return view('Backend/push_notification/notification-messages')->with('message_list' , $message_list);
    }


    public function updateNotificationMessage(Request $request){

        $id = $request->id;
        $language = $request->language;
        $message = $request->message;

        if($language == 'english'){
            NotificationMessage::where('id' , $id)->update(['english' => $message]);
        }
        if($language == 'hindi'){
            NotificationMessage::where('id' , $id)->update(['hindi' => $message]);
        }
        if($language == 'punjabi'){
            NotificationMessage::where('id' , $id)->update(['punjabi' => $message]);
        }
        if($language == 'marathi'){
            NotificationMessage::where('id' , $id)->update(['marathi' => $message]);
        }
        if($language == 'telugu'){
            NotificationMessage::where('id' , $id)->update(['telugu' => $message]);
        }
        if($language == 'bangali'){
            NotificationMessage::where('id' , $id)->update(['bangali' => $message]);
        }


        return back()->with('success' , 'Notification message have been updated successfully.');

    }


}







