<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ApiResponseService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\BusinessModel\PushNotification;
use App\Models\NotificationList;


class NotificationController extends Controller
{
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    // public function sendNotification(Request $request)
    // {
    //     // dd($request->input('user_id'));
    //     $userIds = explode(',', $request->input('user_id'));
    //     $users = User::whereIn('id', $userIds)->get();
    //     $response = $this->notificationService->toUser($users)
    //         ->sendDirectMessage($request->input('message'));
    //     $request->session()->flash('notification_sent', true);
    //     $request->session()->flash('count', count($userIds));
    //     return redirect()->back();
    // }


    public function pushNotification(){
        return new PushNotification();
    }

    public function sendNotification(Request $request)
    {
        // dd($request->input('user_id'));
        $userIds = explode(',', $request->input('user_id'));
        $users = User::whereIn('id', $userIds)->get();
        $message = $request->input('message');
        $device_token = $users[0]->device_token;
        if($device_token != null && strlen($device_token) > 20 ){ 
            $this->pushNotification()->sendNotification($device_token,$message);
        }
        $data = [
            'user_id' => $users[0]->id,
            'message' => $message,
        ];
        NotificationList::create($data);
        $response = $this->notificationService->toUser($users)
            ->sendDirectMessage($request->input('message'));
        $request->session()->flash('notification_sent', true);
        $request->session()->flash('count', count($userIds));
        return redirect()->back();
    }


    public function markAsRead(Request $request)
    {
        $user = auth('user')->user();
        if ($request->input('id')) {
            $user->unreadNotifications()->where('id', $request->input('notification_id'))->markAsRead();
            return (new ApiResponseService)->success('true');
        } else {
            $user->unreadNotifications->markAsRead();
        }
        return (new ApiResponseService)->success('true');
    }
}
