<?php

namespace App\Http\Controllers\Enquiries;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

/**
 * Public form submissions (API_SPEC §"Public submit APIs").
 * Rate limited per IP; a filled `website` honeypot is silently accepted.
 */
class EnquiryController extends EnquiriesController
{
    public function general(Request $request): JsonResponse
    {
        if ($limited = $this->rateLimited($request)) {
            return $limited;
        }

        $body = $this->body($request);

        if ($this->str($request, 'website', $body) !== '') {
            return $this->success(0);
        }

        $interest = $this->str($request, 'interest', $body);
        $fullName = $this->str($request, 'fullName', $body);
        $email = mb_strtolower($this->str($request, 'email', $body));
        $phone = $this->str($request, 'phone', $body);
        $organization = $this->str($request, 'organization', $body);
        $message = $this->str($request, 'message', $body);

        if ($interest !== '' && !in_array($interest, (array) config('enquiries.interests'), true)) {
            return $this->validation('interest', 'interest must be one of: ' . $this->list('interests'));
        }

        if ($err = $this->check($fullName, 'fullName', 150, true)) {
            return $err;
        }

        if ($err = $this->checkEmail($email)) {
            return $err;
        }

        if ($err = $this->check($phone, 'phone', 30, false)) {
            return $err;
        }

        if ($err = $this->check($organization, 'organization', 200, false)) {
            return $err;
        }

        if ($message === '') {
            return $this->validation('message', 'Message is required');
        }

        if (mb_strlen($message) > 60000) {
            return $this->validation('message', 'message is too long');
        }

        RateLimiter::hit($this->bucket($request), 3600);

        try {
            $id = $this->callWithId(
                'CALL sp_create_general_enquiry(?,?,?,?,?,?,@enquiry_id)',
                [$interest, $fullName, $email, $phone, $organization, $message]
            );
        } catch (Throwable $e) {
            return $this->dbFailure($e);
        }

        return $this->success($id);
    }

    public function pathway(Request $request): JsonResponse
    {
        if ($limited = $this->rateLimited($request)) {
            return $limited;
        }

        $body = $this->body($request);

        if ($this->str($request, 'website', $body) !== '') {
            return $this->success(0);
        }

        $pathwayId = $this->str($request, 'pathwayId', $body);
        $fullName = $this->str($request, 'fullName', $body);
        $email = mb_strtolower($this->str($request, 'email', $body));
        $phone = $this->str($request, 'phone', $body);
        $currentRole = $this->str($request, 'currentRole', $body);
        $organization = $this->str($request, 'organization', $body);
        $primaryGoal = $this->str($request, 'primaryGoal', $body);
        $timeline = $this->str($request, 'preferredTimeline', $body);

        if ($pathwayId === '') {
            return $this->validation('pathwayId', 'Pathway is required');
        }

        if (mb_strlen($pathwayId) > 100 || !in_array($pathwayId, (array) config('enquiries.pathways'), true)) {
            return $this->validation('pathwayId', 'pathwayId must be one of: ' . $this->list('pathways'));
        }

        if ($err = $this->check($fullName, 'fullName', 150, true)) {
            return $err;
        }

        if ($err = $this->checkEmail($email)) {
            return $err;
        }

        if ($err = $this->check($phone, 'phone', 30, false)) {
            return $err;
        }

        if ($err = $this->check($currentRole, 'currentRole', 200, false)) {
            return $err;
        }

        if ($err = $this->check($organization, 'organization', 200, false)) {
            return $err;
        }

        if (mb_strlen($primaryGoal) > 60000) {
            return $this->validation('primaryGoal', 'primaryGoal is too long');
        }

        if ($timeline !== '' && !in_array($timeline, (array) config('enquiries.timelines'), true)) {
            return $this->validation('preferredTimeline', 'preferredTimeline must be one of: ' . $this->list('timelines'));
        }

        RateLimiter::hit($this->bucket($request), 3600);

        try {
            $id = $this->callWithId(
                'CALL sp_create_pathway_enquiry(?,?,?,?,?,?,?,?,@enquiry_id)',
                [$pathwayId, $fullName, $email, $phone, $currentRole, $organization, $primaryGoal, $timeline]
            );
        } catch (Throwable $e) {
            return $this->dbFailure($e);
        }

        return $this->success($id);
    }

    public function speaking(Request $request): JsonResponse
    {
        if ($limited = $this->rateLimited($request)) {
            return $limited;
        }

        $body = $this->body($request);

        if ($this->str($request, 'website', $body) !== '') {
            return $this->success(0);
        }

        $engagementType = $this->str($request, 'engagementType', $body);
        $audience = $this->str($request, 'audience', $body);
        $proposedDate = $this->str($request, 'proposedDate', $body);
        $eventFormat = $this->str($request, 'eventFormat', $body);
        $notes = $this->str($request, 'notes', $body);
        $fullName = $this->str($request, 'fullName', $body);
        $email = mb_strtolower($this->str($request, 'email', $body));
        $phone = $this->str($request, 'phone', $body);
        $organization = $this->str($request, 'organization', $body);

        if ($engagementType !== '' && !in_array($engagementType, (array) config('enquiries.engagement_types'), true)) {
            return $this->validation('engagementType', 'engagementType must be one of: ' . $this->list('engagement_types'));
        }

        if ($audience !== '' && !in_array($audience, (array) config('enquiries.audiences'), true)) {
            return $this->validation('audience', 'audience must be one of: ' . $this->list('audiences'));
        }

        if ($proposedDate !== '') {
            if (!$this->isDate($proposedDate)) {
                return $this->validation('proposedDate', 'proposedDate must be a date in YYYY-MM-DD format');
            }

            if ($proposedDate < gmdate('Y-m-d')) {
                return $this->validation('proposedDate', 'Proposed date cannot be in the past');
            }
        }

        if ($eventFormat !== '' && !in_array($eventFormat, (array) config('enquiries.event_formats'), true)) {
            return $this->validation('eventFormat', 'eventFormat must be one of: ' . $this->list('event_formats'));
        }

        if (mb_strlen($notes) > 60000) {
            return $this->validation('notes', 'notes is too long');
        }

        if ($err = $this->check($fullName, 'fullName', 150, true)) {
            return $err;
        }

        if ($err = $this->checkEmail($email)) {
            return $err;
        }

        if ($err = $this->check($phone, 'phone', 30, false)) {
            return $err;
        }

        if ($err = $this->check($organization, 'organization', 200, false)) {
            return $err;
        }

        RateLimiter::hit($this->bucket($request), 3600);

        try {
            $id = $this->callWithId(
                'CALL sp_create_speaking_enquiry(?,?,?,?,?,?,?,?,?,@enquiry_id)',
                [
                    $engagementType, $audience, $proposedDate !== '' ? $proposedDate : null,
                    $eventFormat, $notes, $fullName, $email, $phone, $organization,
                ]
            );
        } catch (Throwable $e) {
            return $this->dbFailure($e);
        }

        return $this->success($id);
    }

    // --- Helpers ----------------------------------------------------------

    private function success(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'enquiryId' => $id,
            'message' => 'Enquiry received',
        ], 201);
    }

    private function bucket(Request $request): string
    {
        return 'enquiries:' . (string) $request->ip();
    }

    private function rateLimited(Request $request): ?JsonResponse
    {
        $limit = (int) config('enquiries.public_rate_limit_per_hour', 10);

        if (RateLimiter::tooManyAttempts($this->bucket($request), $limit)) {
            return $this->error('RATE_LIMITED', 'Too many requests. Please try again later.', 429);
        }

        return null;
    }

    private function check(string $value, string $field, int $max, bool $required): ?JsonResponse
    {
        $label = $field === 'fullName' ? 'Full name' : $field;

        if ($value === '') {
            return $required ? $this->validation($field, $label . ' is required') : null;
        }

        if (mb_strlen($value) > $max) {
            return $this->validation($field, $label . ' must be at most ' . $max . ' characters');
        }

        return null;
    }

    private function checkEmail(string $email): ?JsonResponse
    {
        if ($email === '') {
            return $this->validation('email', 'A valid email address is required');
        }

        if (mb_strlen($email) > 255 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return $this->validation('email', 'A valid email address is required');
        }

        return null;
    }

    private function isDate(string $value): bool
    {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m)) {
            return false;
        }

        return checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
    }

    private function list(string $key): string
    {
        return implode(', ', (array) config('enquiries.' . $key, []));
    }
}
