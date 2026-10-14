<?php

namespace App\Http\Controllers\Hub;

use Illuminate\Support\Facades\DB;
use Throwable;

class HealthController extends HubController
{
    public function health()
    {
        $dbConnected = false;

        try {
            DB::connection('hub')->select('SELECT 1');
            $dbConnected = true;
        } catch (Throwable $e) {
            $dbConnected = false;
        }

        // API_SPEC §2 defines this exact shape (no "success" key).
        return response()->json([
            'status' => 'ok',
            'service' => 'happimynd-games-hub',
            'timestamp' => $this->utcNow()->toIso8601String(),
            'dbConnected' => $dbConnected,
        ]);
    }
}
