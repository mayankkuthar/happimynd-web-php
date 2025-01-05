<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Validator;
use App\Models\Assessment;
use App\Models\User;


class PsychologistControllerApi extends Controller
{
    public function getUserReportByPsy(Request $request){

        $message = [
            'user_id.required'      =>  'Please enter user ID',
        ];
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $user_id = $request->user_id;
        $user = User::where('id' , $user_id)->first();

        $assessment_details = Assessment::where('user_id' , $user_id)->first();

        if(!$assessment_details){
            return response()->json(['status' => 'error' , 'message' => 'Assesment is not complete.']);
        }

        if($assessment_details->ended_at == null){
            return response()->json(['status' => 'error' , 'message' => 'Assesment is not complete.']);
        }

        if($assessment_details->ended_at != null && $assessment_details->report != null){
            return response()->json(['status' => 'success' , 'message' => 'Assesment is completed.' , 'report_link' => $assessment_details->report]);
        }

        if($assessment_details->ended_at != null && $assessment_details->report == null){

            // $response = Http::get(env('NODE_URL') . '/check');
            // if ($response->ok()) {
            //     $response = Http::get(env('NODE_URL') . '/pdf?reportUrl=' . env('APP_URL') . '/calculate-score?assessment_id=' . $assessment_details->id . '&fileName=' . $assessment_details->id . '_' . $user->nickname.'testing' . '-ScreeningReport.pdf');
            //     $res = $response->json();

            //     // print_r($res['link']);
            //     \Log::info('response body:' . json_encode($res));
            //     // $assessment->report = $res['link'];
            //     $assessment_details->update(['report' => $res['link']]);
            // } else {
            //     \Log::critical('respone not ok');
            //     \Log::critical($response);
            // }

            return response()->json(['status' => 'error' , 'message' => 'User does not have HappiLIFE Awareness Tool.']);
        }

    }
}



