<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssessmentDataExport;
use App\Exports\ScoreDataExport;
use App\Models\Assessment;
use App\Models\Category;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\ApiResponseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;
use Facade\FlareClient\Http\Response;
use Illuminate\Support\Collection;

class DataExportController extends Controller
{
    public function __construct(ApiResponseService $apiResponseService)
    {
        $this->apiResponseService = $apiResponseService;
    }

    public function downloadScoreXL(Request $request)
    {
        $data = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        try {
            ini_set('max_execution_time', 300); //increase execution time to 5 min(300 sec) as excel generation takes time
            ini_set('memory_limit', '4096M');
            return Excel::download(new ScoreDataExport($data), 'Score List ' . Carbon::now()->format('d-M-Y g-i a') . '.xlsx');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('danger', 'Problem ocurred, please contact developer');
        }
    }
    
    public function downloadXL(Request $request)
    {
        $data = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'organization_id' => is_numeric($request->input('organization_id')) ? intval($request->input('organization_id')) : $request->input('organization_id'),
        ];
        if ($data['organization_id'] == 'b2c') {
            $data['organization_id'] = null;
            $data['b2c'] = true;
        }
        try {
            ini_set('max_execution_time', 300); //increase execution time to 5 min(300 sec) as excel generation takes time
            ini_set('memory_limit', '4096M');
            return Excel::download(new AssessmentDataExport($data), 'Assessment List ' . Carbon::now()->format('d-M-Y g-i a') . '.xlsx');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('danger', 'Problem ocurred, please contact developer');
        }
    }

    function findInCollection(Collection $collection, $key, $value)
    {
        foreach ($collection as $item) {
            if (isset($item->$key) && strcasecmp($item->$key, $value)) {
                return $item;
            }
        }
        return FALSE;
    }

    function findInCollectionWithInRange(Collection $collection, $min, $max)
    {
        foreach ($collection as $item) {
            if ($item->mimimum_score <= $min && $item->maximum_score <= $max) {
                return $item;
            }
        }
        return FALSE;
    }
}
