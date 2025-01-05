<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ThriveCode;
use App\Models\Token;
use App\Models\UserToken;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentScore;
use App\Services\AssessmentService;

class DownloadController extends Controller
{
    public function __construct()
    {
    }

    public function downloadHappimyndToken(Request $request)
    {
        /**
         * URL Logic: prefix . base64_encode('organization') = base64_encode('id')
         */
        if (base64_decode($request->input(base64_encode('organization_id')))) {
            $tokens = Token::with(['organization', 'userToken.user', 'tokenMetaData'])
                ->where('organization_id', base64_decode($request->input(base64_encode('organization_id'))))
                ->get();
            if ($tokens) {
                $userCount = UserToken::whereHas( 'token', function($query) {
                    $query->where('organization_id', $request->organization_id);
                })->count();
                return view('Download/token')->with('tokens', $tokens)->with('userCount', $userCount);
            }
        }
        return view('Download/token');
    }

    public function downloadThriveCode(Request $request)
    {
        /**
         * URL Logic: prefix . base64_encode('organization') = base64_encode('id')
         */
        if (base64_decode($request->input(base64_encode('organization_id')))) {
            $thriveCodes = ThriveCode::with(['organization', 'user'])
                ->where('organization_id', base64_decode($request->input(base64_encode('organization_id'))))
                ->get();
            if ($thriveCodes)
                return view('Download/thriveCode')->with('thriveCodes', $thriveCodes);
        }
        return view('Download/thriveCode');
    }

    public function downloadAssessmentDetail(Request $request)
    {
        /**
         * URL Logic: prefix . base64_encode('assessment_id') = base64_encode('id')
         * eg: route('downloadAssessmentDetail', [base64_encode('assessment_id')=>base64_encode('128')])
         */
        if (base64_decode($request->input(base64_encode('assessment_id')))) {
            $assessment = Assessment::with(['user', 'user.userToken.token', 'score'])
                ->where('id', base64_decode($request->input(base64_encode('assessment_id'))))
                ->first();
            if ($assessment) {
                if ($assessment->completedAssessment()) {
                    if (!$assessment->score) {
                        $assessmentService = new AssessmentService();
                        $assessment->score = $assessmentService->createOrUpdateScore($assessment, AssessmentAnswer::where('assessment_id', $assessment->id)->count());
                    }
                }
                if ($assessment->user->userToken) {
                    $token = Token::where('id', $assessment->user->userToken->token->id)
                        ->with('organization')
                        ->first();
                    $assessment->token = $token->token;
                    $assessment->organization = $token->organization->name;
                }
                return view('Download/assessmentDetail')->with('assessment', $assessment);
            }
        }
        return view('Download/assessmentDetail');
    }
}
