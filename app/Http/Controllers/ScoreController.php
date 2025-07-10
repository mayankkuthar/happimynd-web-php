<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreSaveRequest;
use App\Models\Score;
use App\Models\User;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function __construct()
    {
        //
    }

    public function getScoreList(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $scores = Score::latest()->whereHas('user')->with('user')->paginate($perPage)
            ->appends($request->except('page'));

        return view('Backend.score.all')->with('scores', $scores);
    }
}
