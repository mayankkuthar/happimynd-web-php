<?php

namespace App\Http\Controllers\Hub;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Shared helpers for the Games Hub API controllers.
 *
 * Response contract (API_SPEC §1):
 *   success: { "success": true, ...data }
 *   error:   { "success": false, "message": "...", "error": "..." }
 */
abstract class HubController extends Controller
{
    protected function ok(array $data = [], int $status = 200)
    {
        return response()->json(array_merge(['success' => true], $data), $status);
    }

    protected function fail(string $message, int $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => $message,
        ], $status);
    }

    protected function noContent()
    {
        return response()->noContent();
    }

    /**
     * Read a JSON body regardless of Content-Type. Tracking endpoints send
     * `text/plain` (to avoid a CORS preflight) so $request->all() is empty for
     * them and we must parse the raw body ourselves.
     */
    protected function jsonBody(Request $request): array
    {
        $raw = $request->getContent();

        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $request->all();
    }

    /** Current time in UTC (the app timezone is not UTC). */
    protected function utcNow(): Carbon
    {
        return Carbon::now('UTC');
    }

    /** UTC timestamp formatted for a MySQL DATETIME(3) column. */
    protected function utcStr(?Carbon $dt = null): string
    {
        return ($dt ?: $this->utcNow())->format('Y-m-d H:i:s.v');
    }

    /** Present a hub user in the shape the front end expects (API_SPEC §2). */
    protected function presentUser($user): array
    {
        $created = null;
        if (!empty($user->created_at)) {
            $created = Carbon::parse((string) $user->created_at, 'UTC')->toIso8601String();
        }

        return [
            'id' => (string) $user->id,
            'name' => (string) $user->name,
            'username' => (string) $user->username,
            'email' => (string) $user->email,
            'avatar' => ($user->avatar !== null && $user->avatar !== '') ? $user->avatar : '🧘',
            'created_at' => $created,
        ];
    }

    /** A "No store" header for admin responses (API_SPEC §5). */
    protected function noStoreHeaders(): array
    {
        return ['Cache-Control' => 'no-store'];
    }
}
