<?php

namespace App\Http\Controllers\Hr;

use App\Services\Hr\ApplicationMailer;
use App\Services\Hr\GcsStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Public candidate portal (API_SPEC §7).
 *
 * A submission writes an `applications` row plus a linked `records` row
 * (source `others`), so the resume also appears in the extractor's "Others"
 * tab. Roles are free text; there is no open-roles list.
 */
class PortalController extends HrController
{
    public function config(): JsonResponse
    {
        return $this->ok([
            'allowed_extensions' => array_values((array) config('hr.portal.allowed_extensions', [])),
            'max_file_bytes' => (int) config('hr.portal.max_file_bytes', 5 * 1024 * 1024),
        ]);
    }

    public function store(Request $request, GcsStorage $storage, ApplicationMailer $mailer): JsonResponse
    {
        // Honeypot: pretend it worked, store nothing.
        if (trim((string) $request->input('website', '')) !== '') {
            return $this->ok(['application_id' => $this->newApplicationId()], 201);
        }

        $data = $this->validateSubmission($request);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $resume = $request->file('resume');

        if (!$resume instanceof UploadedFile) {
            return $this->missing('resume', 'body');
        }

        if ($err = $this->validateResume($resume)) {
            return $err;
        }

        // Rate limit after the cheap validation so bad requests don't burn it.
        $bucket = 'hr-portal:' . (string) $request->ip();
        $limit = (int) config('hr.portal.rate_limit_per_hour', 10);

        if (RateLimiter::tooManyAttempts($bucket, $limit)) {
            return $this->detail('Too many applications from this address. Please try again later.', 429);
        }

        RateLimiter::hit($bucket, 3600);

        $duplicateOf = $this->findDuplicate($data);

        if (is_array($duplicateOf)) {
            return $this->detail($duplicateOf['message'], 409);
        }

        if (!$storage->configured()) {
            return $this->detail('Storage is not configured', 503);
        }

        $applicationId = $this->newApplicationId();
        $recordId = bin2hex(random_bytes(6));
        $extension = strtolower($resume->getClientOriginalExtension());
        $contentType = $resume->getClientMimeType() ?: 'application/octet-stream';
        $contents = (string) file_get_contents((string) $resume->getRealPath());
        $applicationKey = $storage->resumeKey('portal/' . $applicationId . '.' . $extension);

        if ($contents === '' || !$storage->upload($applicationKey, $contents, $contentType)) {
            return $this->detail('We could not store the resume. Please try again.', 503);
        }

        try {
            $this->db()->insert(
                'INSERT INTO `applications` '
                . '(`id`,`full_name`,`email`,`phone`,`phone_digits`,`college`,`degree`,`specialization`,'
                . '`graduation_year`,`education_details`,`current_position`,`experience_years`,`previous_experience`,'
                . '`skills`,`applied_role`,`preferred_location`,`notice_period`,`linkedin_url`,`github_url`,'
                . '`portfolio_url`,`other_links`,`resume_storage_key`,`resume_file_name`,`resume_content_type`,'
                . '`resume_size_bytes`,`status`,`duplicate_of`,`record_id`,`created_at`,`updated_at`) '
                . 'VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,CAST(? AS JSON),?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(3),NOW(3))',
                [
                    $applicationId, $data['full_name'], $data['email'], $data['phone'], $data['phone_digits'],
                    $data['college'], $data['degree'], $data['specialization'], $data['graduation_year'],
                    $data['education_details'], $data['current_position'], $data['experience_years'],
                    $data['previous_experience'], json_encode($data['skills']), $data['applied_role'],
                    $data['preferred_location'], $data['notice_period'], $data['linkedin_url'],
                    $data['github_url'], $data['portfolio_url'], $data['other_links'],
                    $applicationKey, $resume->getClientOriginalName(), $contentType,
                    $resume->getSize(), $this->firstStatus(),
                    is_string($duplicateOf) ? $duplicateOf : null, $recordId,
                ]
            );
        } catch (Throwable $e) {
            $storage->delete($applicationKey);

            return $this->detail('We could not save the application. Please try again.', 503);
        }

        // Second copy for the extractor. This never fails the submission.
        $this->attachRecord($storage, $applicationId, $recordId, $extension, $contents, $contentType, $resume);

        // Confirmation email (never fails the submission).
        if ((bool) config('hr.applications.ack_email', false)) {
            $this->sendAcknowledgement($mailer, $data, $applicationId);
        }

        return $this->ok(['application_id' => $applicationId], 201);
    }

    // --- Validation -------------------------------------------------------

    /** @return array|JsonResponse */
    private function validateSubmission(Request $request)
    {
        $get = function (string $key): string {
            $value = $request->input($key, '');

            return is_scalar($value) ? trim((string) $value) : '';
        };

        $fullName = $get('full_name');
        if ($fullName === '') {
            return $this->missing('full_name', 'body');
        }
        if (mb_strlen($fullName) > 200) {
            return $this->invalid('full_name', 'full_name must be at most 200 characters', 'body');
        }

        $email = mb_strtolower($get('email'));
        if ($email === '') {
            return $this->missing('email', 'body');
        }
        if (mb_strlen($email) > 320 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return $this->invalid('email', 'email must be a valid address', 'body');
        }

        $phone = $get('phone');
        if ($phone === '') {
            return $this->missing('phone', 'body');
        }
        $digits = (string) preg_replace('/\D+/', '', $phone);
        if (strlen($digits) < 10 || strlen($digits) > 15) {
            return $this->invalid('phone', 'phone must contain 10 to 15 digits', 'body');
        }

        $college = $get('college');
        if ($college === '') {
            return $this->missing('college', 'body');
        }
        if (mb_strlen($college) > 255) {
            return $this->invalid('college', 'college must be at most 255 characters', 'body');
        }

        $degree = $get('degree');
        if ($degree === '') {
            return $this->missing('degree', 'body');
        }
        if (mb_strlen($degree) > 255) {
            return $this->invalid('degree', 'degree must be at most 255 characters', 'body');
        }

        foreach (['specialization', 'current_position', 'preferred_location'] as $field) {
            if (mb_strlen($get($field)) > 255) {
                return $this->invalid($field, $field . ' must be at most 255 characters', 'body');
            }
        }

        $graduationYear = $get('graduation_year');
        if ($graduationYear !== '') {
            if (!preg_match('/^\d{4}$/', $graduationYear)
                || (int) $graduationYear < 1950
                || (int) $graduationYear > (int) gmdate('Y') + 6) {
                return $this->invalid('graduation_year', 'graduation_year is out of range', 'body');
            }
            $graduationYear = (int) $graduationYear;
        } else {
            $graduationYear = null;
        }

        if (mb_strlen($get('education_details')) > 2000) {
            return $this->invalid('education_details', 'education_details must be at most 2000 characters', 'body');
        }

        $experience = $get('experience_years');
        if ($experience === '') {
            return $this->missing('experience_years', 'body');
        }
        if (!is_numeric($experience) || (float) $experience < 0 || (float) $experience > 60) {
            return $this->invalid('experience_years', 'experience_years must be between 0 and 60', 'body');
        }

        if (mb_strlen($get('previous_experience')) > 4000) {
            return $this->invalid('previous_experience', 'previous_experience must be at most 4000 characters', 'body');
        }

        $skillsRaw = $get('skills');
        if ($skillsRaw === '') {
            return $this->missing('skills', 'body');
        }
        if (mb_strlen($skillsRaw) > 2000) {
            return $this->invalid('skills', 'skills must be at most 2000 characters', 'body');
        }

        $skills = [];
        foreach (preg_split('/[,;\n]+/', $skillsRaw) as $part) {
            $part = trim($part);
            if ($part !== '' && !in_array($part, $skills, true)) {
                $skills[] = $part;
            }
        }

        if ($skills === []) {
            return $this->invalid('skills', 'skills must contain at least one skill', 'body');
        }
        if (count($skills) > 50) {
            return $this->invalid('skills', 'skills must contain at most 50 items', 'body');
        }

        $role = $get('applied_role');
        if ($role === '') {
            return $this->missing('applied_role', 'body');
        }
        if (mb_strlen($role) > 255) {
            return $this->invalid('applied_role', 'applied_role must be at most 255 characters', 'body');
        }

        if (mb_strlen($get('notice_period')) > 100) {
            return $this->invalid('notice_period', 'notice_period must be at most 100 characters', 'body');
        }

        foreach (['linkedin_url', 'github_url', 'portfolio_url'] as $field) {
            $value = $get($field);
            if ($value === '') {
                continue;
            }
            if (mb_strlen($value) > 500 || filter_var($value, FILTER_VALIDATE_URL) === false
                || !preg_match('#^https?://#i', $value)) {
                return $this->invalid($field, $field . ' must be an http(s) URL of at most 500 characters', 'body');
            }
        }

        if (mb_strlen($get('other_links')) > 2000) {
            return $this->invalid('other_links', 'other_links must be at most 2000 characters', 'body');
        }

        return [
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'phone_digits' => substr($digits, -10),
            'college' => $college,
            'degree' => $degree,
            'specialization' => $this->nullable($get('specialization')),
            'graduation_year' => $graduationYear,
            'education_details' => $this->nullable($get('education_details')),
            'current_position' => $this->nullable($get('current_position')),
            'experience_years' => round((float) $experience, 1),
            'previous_experience' => $this->nullable($get('previous_experience')),
            'skills' => $skills,
            'applied_role' => $role,
            'preferred_location' => $this->nullable($get('preferred_location')),
            'notice_period' => $this->nullable($get('notice_period')),
            'linkedin_url' => $this->nullable($get('linkedin_url')),
            'github_url' => $this->nullable($get('github_url')),
            'portfolio_url' => $this->nullable($get('portfolio_url')),
            'other_links' => $this->nullable($get('other_links')),
        ];
    }

    private function validateResume(UploadedFile $file): ?JsonResponse
    {
        if (!$file->isValid()) {
            return $this->invalid('resume', 'The resume could not be read', 'body');
        }

        $allowed = array_values((array) config('hr.portal.allowed_extensions', []));
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowed, true)) {
            return $this->invalid('resume', 'Resume must be one of: ' . implode(', ', $allowed), 'body');
        }

        if ($file->getSize() > (int) config('hr.portal.max_file_bytes', 5 * 1024 * 1024)) {
            return $this->invalid('resume', 'Resume is larger than the upload limit', 'body');
        }

        $handle = fopen((string) $file->getRealPath(), 'rb');
        $magic = $handle !== false ? (string) fread($handle, 8) : '';
        if ($handle !== false) {
            fclose($handle);
        }

        $matches = [
            'pdf' => strncmp($magic, '%PDF', 4) === 0,
            'docx' => strncmp($magic, "PK\x03\x04", 4) === 0,
            'doc' => strncmp($magic, "\xD0\xCF\x11\xE0", 4) === 0,
        ];

        if (isset($matches[$extension]) && !$matches[$extension]) {
            return $this->invalid('resume', 'Resume contents do not match the file extension', 'body');
        }

        return null;
    }

    // --- Helpers ----------------------------------------------------------

    /** @return string|array|null duplicate_of, or an error to return */
    private function findDuplicate(array $data)
    {
        $query = $this->db()->table('applications')
            ->where(function ($w) use ($data) {
                $w->where('email', $data['email'])->orWhere('phone_digits', $data['phone_digits']);
            });

        $days = (int) config('hr.applications.duplicate_window_days', 90);

        if ($days > 0) {
            $query->where('created_at', '>=', gmdate('Y-m-d H:i:s', time() - $days * 86400));
        }

        $match = $query->orderByDesc('created_at')->first(['id', 'applied_role']);

        if ($match === null) {
            return null;
        }

        if (mb_strtolower((string) $match->applied_role) === mb_strtolower($data['applied_role'])) {
            return [
                'message' => 'You have already applied for ' . $data['applied_role']
                    . ' (reference ' . $match->id . '). Our hiring team will be in touch.',
            ];
        }

        return (string) $match->id;
    }

    private function attachRecord(
        GcsStorage $storage,
        string $applicationId,
        string $recordId,
        string $extension,
        string $contents,
        string $contentType,
        UploadedFile $resume
    ): void {
        try {
            $recordKey = $storage->resumeKey($recordId . '.' . $extension);

            if (!$storage->upload($recordKey, $contents, $contentType)) {
                throw new \RuntimeException('upload failed');
            }

            $this->db()->insert(
                'INSERT INTO `records` '
                . '(`id`,`file_name`,`size_bytes`,`checksum`,`source`,`department`,`status`,`viewed`,'
                . '`storage_key`,`content_type`,`fields`,`confidence`,`text_content`,`log`,`error`,'
                . '`used_ocr`,`page_count`,`ai_status`,`duplicate_of`,`attempts`,`created_at`) '
                . 'VALUES (?,?,?,?,?,?,?,?,?,?,JSON_OBJECT(),JSON_OBJECT(),?,CAST(? AS JSON),NULL,0,0,?,?,0,NOW(3))',
                [
                    $recordId, $resume->getClientOriginalName(), $resume->getSize(),
                    hash('sha256', $contents), 'others', null, 'queued', 0,
                    $recordKey, $contentType, '', json_encode([]), 'disabled', null,
                ]
            );
        } catch (Throwable $e) {
            Log::warning('Portal record link failed', [
                'application_id' => $applicationId,
                'message' => $e->getMessage(),
            ]);

            $this->db()->table('applications')->where('id', $applicationId)->update(['record_id' => null]);
        }
    }

    private function sendAcknowledgement(ApplicationMailer $mailer, array $data, string $applicationId): void
    {
        if (!$mailer->configured()) {
            return;
        }

        $subject = $this->fillPlaceholders(
            (string) config('hr.mail.ack.subject', ''),
            $data['full_name'],
            $data['applied_role'],
            $applicationId
        );
        $body = $this->fillPlaceholders(
            (string) config('hr.mail.ack.body', ''),
            $data['full_name'],
            $data['applied_role'],
            $applicationId
        );

        if ($subject === '' || $body === '') {
            return;
        }

        [$sent, $messageId, $error] = $mailer->send($data['email'], $subject, $body);

        try {
            $this->db()->insert(
                'INSERT INTO `application_emails` '
                . '(`application_id`,`recipient`,`subject`,`body`,`sent_by`,`status`,`error`,`message_id`,`created_at`) '
                . 'VALUES (?,?,?,?,?,?,?,?,NOW(3))',
                [$applicationId, $data['email'], $subject, $body, 'system', $sent ? 'sent' : 'failed', $error, $messageId]
            );
        } catch (Throwable $e) {
            Log::warning('Portal acknowledgement not logged', [
                'application_id' => $applicationId,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function firstStatus(): string
    {
        $statuses = array_values((array) config('hr.application_statuses', []));

        return $statuses[0] ?? 'New';
    }

    private function newApplicationId(): string
    {
        $alphabet = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $id = 'APP-';

            for ($i = 0; $i < 10; $i++) {
                $id .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }

            $exists = $this->db()->table('applications')->where('id', $id)->exists();

            if (!$exists) {
                return $id;
            }
        }

        return 'APP-' . strtoupper(bin2hex(random_bytes(5)));
    }

    private function nullable(string $value): ?string
    {
        return $value === '' ? null : $value;
    }
}
