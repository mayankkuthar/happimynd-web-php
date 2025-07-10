<?php

namespace App\Http\Controllers\ChatBot;

use Exception;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ChatBot\ChatBotAssessment;
use App\Exports\ChatBotAssessmentDataExport;

class ChatBotAssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 100);
        $assessments  = ChatBotAssessment::with(['user'])->paginate($perPage)
            ->appends($request->except('page'));
        return view('Backend.chat-bot.assessments.index', compact('assessments'));
    }

    /**
     * Dowwnload a listing of the resource as an excel document.
     *
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $dates = $request->only(['from', 'to']);

        try {
            // Increase execution time and memory limit.
            ini_set('max_execution_time', 300);
            ini_set('memory_limit', '4096M');

            return Excel::download(new ChatBotAssessmentDataExport($dates), 'Chat Bot Assessments' . Carbon::now()->format('d-M-Y g-i a') . '.xlsx');
        } catch (Exception $e) {
            Log::error($e);

            return redirect()->back()->with('danger', 'Problem ocurred, please contact developer.');
        }
    }
}
