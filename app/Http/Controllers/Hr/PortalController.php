<?php

namespace App\Http\Controllers\Hr;

use Illuminate\Http\JsonResponse;

/**
 * Candidate portal settings (API_SPEC §7). Public: mounted before the session
 * gate. `POST /portal/applications` is intentionally not here yet — it needs
 * object storage for the resume (POST /resumes/{id}/file stays on the intern's
 * side until the GCS service account is available).
 */
class PortalController extends HrController
{
    public function config(): JsonResponse
    {
        return $this->ok([
            'open_roles' => array_values((array) config('hr.portal.open_roles', [])),
            'allowed_extensions' => array_values((array) config('hr.portal.allowed_extensions', [])),
            'max_file_bytes' => (int) config('hr.portal.max_file_bytes', 5 * 1024 * 1024),
        ]);
    }
}
