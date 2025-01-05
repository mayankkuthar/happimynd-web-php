<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotificationList;
use Auth;
use Validator;

class NotificationApiController extends Controller
{
    

    public function notificationList(Request $request){
        $user = Auth::user();
        $list = NotificationList::where('user_id' , $user->id)->orderBy('id' , 'desc')->get();
        return response()->json(['status' => 'success' , 'message' => 'Notification list get successfully.' , 'list' => $list]);
    }


    public function readSingleNotification(Request $request){

        $message = [
            'notification_id.required'      =>  'Please enter notification ID',
        ];
        $validator = Validator::make($request->all(), [
            'notification_id'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $data = NotificationList::where('id' , $request->notification_id)->first();

        $data->is_read = '1';
        $data->save();

        return response()->json(['status' => 'success' , 'message' => 'Notification read successfully.' , 'data' => $data]);

    }



    public function readAllNotification(Request $request){
        $user = Auth::user();
        NotificationList::where('user_id' , $user->id)->update(['is_read' => 1]);
        return response()->json(['status' => 'success' , 'message' => 'All notifications read successfully.']);
    }


}













