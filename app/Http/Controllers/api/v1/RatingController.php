<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ApplicationRateEmoji;
use Validator;
use Auth;
use App\Models\UsersRating;


class RatingController extends Controller
{
    

    public function EmojiList(Request $request){
        $emoji_list = ApplicationRateEmoji::get();
        return response()->json(['status' => 'success' , 'message' => 'Emoji get successfully.' , 'list' => $emoji_list]);
    }


    public function submitRating(Request $request){

        $user = Auth::user();

        $message = [
            'review.required'      =>  'Please enter review',
            'application_rate_emoji_id.required'      =>  'Please enter rating ID',
        ];
        $validator = Validator::make($request->all(), [
            'review'   => 'required',
            'application_rate_emoji_id'   => 'required',
        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $is_already_reviewed = UsersRating::where('user_id' , $user->id)->first();
        if($is_already_reviewed){
            return response()->json(['status' => 'error' , 'message' => 'Your reviewed has been already submitted.']);
        }

        $data = [
            'user_id' => $user->id,
            'application_rate_emoji_id' => $request->application_rate_emoji_id,
            'review' => $request->review,
        ];

        UsersRating::create($data);

        return response()->json(['status' => 'success' , 'message' => 'Review has been submit successfully.']);

    }


}
