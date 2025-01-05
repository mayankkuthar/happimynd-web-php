<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Score;
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

        return response()->json(['status' => 'success', 'message' => 'Score saved successfully.']);
    }
}
