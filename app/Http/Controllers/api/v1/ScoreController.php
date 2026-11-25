<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Score;
use App\BusinessModel\RewardPointToUser;
use Illuminate\Support\Facades\Validator;

class ScoreController extends Controller
{
    public function saveScore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'result' => 'required',
            'score' => 'required',
            'smoothness' => 'required',
            'liveliness' => 'required',
            'control' => 'required',
            'energy_range' => 'required',
            'clarity' => 'required',
            'crispness' => 'required',
            'speech_rate' => 'required',
            'pause_duration' => 'required',
            'inferred_at' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        $score = Score::create($request->all());

        // Reward points for a HappiVOICE check (points / how often are set in admin > Reward Points)
        (new RewardPointToUser())->awardByKey($score->user_id, 'happivoice_check', 'Completed a HappiVOICE check');

        return response()->json(['status' => 'success', 'message' => 'Score saved successfully.']);
    }
}
