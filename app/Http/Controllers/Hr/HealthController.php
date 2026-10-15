<?php

namespace App\Http\Controllers\Hr;

use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * HR Auto health endpoints (API_SPEC §3). /health is always 200 (a DB blip
 * must not change it); /health/ready is for the load balancer.
 */
class HealthController extends HrController
{
    public function health(): JsonResponse
    {
        $aiEnabled = (bool) env('HR_AI_ENABLED', false);
        $model = $aiEnabled ? (string) env('HR_AI_MODEL', '') : null;

        return response()->json([
            'status' => 'ok',
            'ai_enabled' => $aiEnabled,
            'model' => ($model === '') ? null : $model,
            'database' => $this->databaseStatus(),
            'storage' => (string) env('HR_STORAGE', 'gcs'),
            'processor' => [
                'running' => (bool) env('HR_PROCESSOR_RUNNING', true),
                'active' => 0,
                'concurrency' => (int) env('HR_PROCESSOR_CONCURRENCY', 2),
            ],
        ], 200);
    }

    public function ready(): JsonResponse
    {
        $database = $this->databaseStatus();
        $ready = $database === 'ok';

        return response()->json([
            'status' => $ready ? 'ready' : 'not ready',
            'database' => $database,
            'storage' => (string) env('HR_STORAGE', 'gcs'),
            'processor' => [
                'running' => (bool) env('HR_PROCESSOR_RUNNING', true),
                'active' => 0,
                'concurrency' => (int) env('HR_PROCESSOR_CONCURRENCY', 2),
            ],
        ], $ready ? 200 : 503);
    }

    private function databaseStatus(): string
    {
        if (config('database.connections.hr.database') === null) {
            return 'not configured';
        }

        try {
            $this->db()->select('SELECT 1');

            return 'ok';
        } catch (Throwable $e) {
            return 'unreachable';
        }
    }
}
