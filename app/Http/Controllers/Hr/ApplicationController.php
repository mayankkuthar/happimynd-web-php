<?php

namespace App\Http\Controllers\Hr;

use App\Services\Hr\ApplicationMailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Hiring dashboard applications (API_SPEC §8).
 *
 * Every write appends an audit_log row with actor = the admin username.
 * Timestamps are returned as epoch seconds, like records.
 */
class ApplicationController extends HrController
{
    // --- Reads ------------------------------------------------------------

    public function index(Request $request): JsonResponse
    {
        $query = $this->db()->table('applications');

        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $role = $request->query('role');
        $degree = $request->query('degree');
        $minExp = $request->query('min_exp');
        $maxExp = $request->query('max_exp');
        $from = $request->query('from');
        $to = $request->query('to');

        if ($minExp !== null && $minExp !== '' && !is_numeric($minExp)) {
            return $this->detail('min_exp must be a number', 400);
        }

        if ($maxExp !== null && $maxExp !== '' && !is_numeric($maxExp)) {
            return $this->detail('max_exp must be a number', 400);
        }

        if ($from !== null && $from !== '' && !$this->isDate($from)) {
            return $this->detail('from must be a date in YYYY-MM-DD format', 400);
        }

        if ($to !== null && $to !== '' && !$this->isDate($to)) {
            return $this->detail('to must be a date in YYYY-MM-DD format', 400);
        }

        if ($q !== '') {
            $like = '%' . $this->escapeLike($q) . '%';

            $query->where(function ($w) use ($q, $like) {
                $w->where('full_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('id', 'like', $like)
                    ->orWhere('applied_role', 'like', $like)
                    ->orWhere('college', 'like', $like);

                $digits = (string) preg_replace('/\D+/', '', $q);

                if (strlen($digits) >= 4) {
                    $w->orWhere('phone_digits', 'like', '%' . $digits . '%');
                }
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        if ($role !== null && $role !== '') {
            $query->where('applied_role', $role);
        }

        if ($degree !== null && $degree !== '') {
            $like = '%' . $this->escapeLike($degree) . '%';
            $query->where(function ($w) use ($like) {
                $w->where('degree', 'like', $like)->orWhere('specialization', 'like', $like);
            });
        }

        if ($minExp !== null && $minExp !== '') {
            $query->where('experience_years', '>=', (float) $minExp);
        }

        if ($maxExp !== null && $maxExp !== '') {
            $query->where('experience_years', '<=', (float) $maxExp);
        }

        if ($from !== null && $from !== '') {
            $query->where('created_at', '>=', $from . ' 00:00:00');
        }

        if ($to !== null && $to !== '') {
            $query->where('created_at', '<', gmdate('Y-m-d', strtotime($to . ' +1 day')) . ' 00:00:00');
        }

        $rows = $query->orderByDesc('created_at')->orderByDesc('id')->get();

        $counts = new \stdClass();

        foreach ($this->db()->table('applications')->select('status', DB::raw('COUNT(*) as c'))->groupBy('status')->get() as $row) {
            $counts->{$row->status} = (int) $row->c;
        }

        return $this->ok([
            'applications' => $rows->map(function ($row) {
                return $this->presentApplication($row);
            })->all(),
            'counts' => $counts,
        ]);
    }

    public function meta(ApplicationMailer $mailer): JsonResponse
    {
        return $this->ok([
            'statuses' => array_values((array) config('hr.application_statuses', [])),
            'roles' => $this->db()->table('applications')->distinct()->orderBy('applied_role')->pluck('applied_role')->all(),
            'templates' => array_values((array) config('hr.mail.templates', [])),
            'mail_configured' => $mailer->configured(),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $row = $this->find($id);

        if ($row === null) {
            return $this->detail('Application not found', 404);
        }

        $notes = $this->db()->table('application_notes')
            ->where('application_id', $id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(function ($note) {
                return [
                    'id' => (int) $note->id,
                    'author' => (string) $note->author,
                    'body' => (string) $note->body,
                    'created_at' => $this->epochSeconds($note->created_at),
                ];
            })->all();

        $emails = $this->db()->table('application_emails')
            ->where('application_id', $id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(function ($email) {
                return $this->presentEmail($email);
            })->all();

        $audit = $this->db()->table('audit_log')
            ->where('entity_type', 'application')
            ->where('entity_id', $id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => (int) $entry->id,
                    'actor' => (string) $entry->actor,
                    'action' => (string) $entry->action,
                    'detail' => $entry->detail !== null ? $this->jsonObject($entry->detail) : null,
                    'created_at' => $this->epochSeconds($entry->created_at),
                ];
            })->all();

        return $this->ok(array_merge($this->presentApplication($row), [
            'notes' => $notes,
            'emails' => $emails,
            'audit' => $audit,
        ]));
    }

    // --- Writes -----------------------------------------------------------

    public function status(Request $request, string $id): JsonResponse
    {
        $row = $this->find($id);

        if ($row === null) {
            return $this->detail('Application not found', 404);
        }

        $body = $this->jsonBody($request);
        $status = $body['status'] ?? null;

        if ($status === null || $status === '') {
            return $this->missing('status', 'body');
        }

        $statuses = array_values((array) config('hr.application_statuses', []));

        if (!in_array($status, $statuses, true)) {
            return $this->detail(
                "Unknown status '" . $status . "'. Expected one of: " . implode(', ', $statuses) . '.',
                400
            );
        }

        if ((string) $row->status === (string) $status) {
            return $this->ok($this->presentApplication($row));
        }

        $this->db()->table('applications')->where('id', $id)->update([
            'status' => $status,
            'status_changed_at' => DB::raw('UTC_TIMESTAMP(3)'),
        ]);

        $this->audit('application.status', $id, ['from' => (string) $row->status, 'to' => (string) $status]);

        return $this->ok($this->presentApplication($this->find($id)));
    }

    public function notes(Request $request, string $id): JsonResponse
    {
        if ($this->find($id) === null) {
            return $this->detail('Application not found', 404);
        }

        $body = $this->jsonBody($request);
        $text = $body['body'] ?? null;

        if ($text === null) {
            return $this->missing('body', 'body');
        }

        $text = trim((string) $text);

        if ($text === '' || mb_strlen($text) > 5000) {
            return $this->invalid('body', 'Note must be between 1 and 5000 characters');
        }

        $this->db()->insert(
            'INSERT INTO `application_notes` (`application_id`,`author`,`body`,`created_at`) VALUES (?,?,?,NOW(3))',
            [$id, $this->actor(), $text]
        );

        $noteId = (int) $this->db()->getPdo()->lastInsertId();
        $note = $this->db()->table('application_notes')->where('id', $noteId)->first();

        $this->audit('application.note', $id, ['note_id' => $noteId]);

        return $this->ok([
            'id' => (int) $note->id,
            'author' => (string) $note->author,
            'body' => (string) $note->body,
            'created_at' => $this->epochSeconds($note->created_at),
        ], 201);
    }

    public function email(Request $request, string $id, ApplicationMailer $mailer): JsonResponse
    {
        $row = $this->find($id);

        if ($row === null) {
            return $this->detail('Application not found', 404);
        }

        $body = $this->jsonBody($request);
        $subject = $body['subject'] ?? null;
        $text = $body['body'] ?? null;
        $templateId = isset($body['template_id']) && $body['template_id'] !== ''
            ? (string) $body['template_id']
            : null;

        if ($subject === null || trim((string) $subject) === '') {
            return $this->missing('subject', 'body');
        }

        $subject = trim((string) $subject);

        if (mb_strlen($subject) > 255 || preg_match('/[\r\n]/', $subject)) {
            return $this->invalid('subject', 'Subject must be a single line of at most 255 characters');
        }

        if ($text === null || trim((string) $text) === '') {
            return $this->missing('body', 'body');
        }

        $text = (string) $text;

        if (mb_strlen($text) > 20000) {
            return $this->invalid('body', 'Email body must be at most 20000 characters');
        }

        if (!$mailer->configured()) {
            return $this->detail('Email is not configured', 503);
        }

        $replace = [
            '{candidate_name}' => (string) $row->full_name,
            '{applied_role}' => (string) $row->applied_role,
            '{application_id}' => (string) $row->id,
        ];

        $subject = strtr($subject, $replace);
        $text = strtr($text, $replace);

        [$sent, $messageId, $error] = $mailer->send((string) $row->email, $subject, $text);

        $status = $sent ? 'sent' : 'failed';

        $this->db()->insert(
            'INSERT INTO `application_emails` '
            . '(`application_id`,`recipient`,`subject`,`body`,`sent_by`,`status`,`error`,`message_id`,`created_at`) '
            . 'VALUES (?,?,?,?,?,?,?,?,NOW(3))',
            [$id, (string) $row->email, $subject, $text, $this->actor(), $status, $error, $messageId]
        );

        $emailId = (int) $this->db()->getPdo()->lastInsertId();

        if ($sent) {
            $this->db()->table('applications')->where('id', $id)->update([
                'last_contacted_at' => DB::raw('UTC_TIMESTAMP(3)'),
            ]);
        }

        $this->audit('application.email', $id, [
            'email_id' => $emailId,
            'subject' => $subject,
            'template_id' => $templateId,
            'status' => $status,
        ]);

        $email = $this->db()->table('application_emails')->where('id', $emailId)->first();

        if (!$sent) {
            return $this->detail('The email could not be sent: ' . (string) $error, 502);
        }

        return $this->ok($this->presentEmail($email), 201);
    }

    // --- Helpers ----------------------------------------------------------

    private function find(string $id)
    {
        return $this->db()->table('applications')->where('id', $id)->first();
    }

    private function audit(string $action, string $entityId, array $detail): void
    {
        $this->db()->insert(
            'INSERT INTO `audit_log` (`actor`,`action`,`entity_type`,`entity_id`,`detail`,`created_at`) '
            . 'VALUES (?,?,?,?,CAST(? AS JSON),NOW(3))',
            [$this->actor(), $action, 'application', $entityId, json_encode($detail)]
        );
    }

    private function presentApplication($row): array
    {
        return [
            'id' => (string) $row->id,
            'full_name' => (string) $row->full_name,
            'email' => (string) $row->email,
            'phone' => (string) $row->phone,
            'college' => (string) $row->college,
            'degree' => (string) $row->degree,
            'specialization' => $row->specialization !== null ? (string) $row->specialization : null,
            'graduation_year' => $row->graduation_year !== null ? (int) $row->graduation_year : null,
            'education_details' => $row->education_details !== null ? (string) $row->education_details : null,
            'current_position' => $row->current_position !== null ? (string) $row->current_position : null,
            'experience_years' => (float) $row->experience_years,
            'previous_experience' => $row->previous_experience !== null ? (string) $row->previous_experience : null,
            'skills' => $this->jsonArray($row->skills),
            'applied_role' => (string) $row->applied_role,
            'preferred_location' => $row->preferred_location !== null ? (string) $row->preferred_location : null,
            'notice_period' => $row->notice_period !== null ? (string) $row->notice_period : null,
            'linkedin_url' => $row->linkedin_url !== null ? (string) $row->linkedin_url : null,
            'github_url' => $row->github_url !== null ? (string) $row->github_url : null,
            'portfolio_url' => $row->portfolio_url !== null ? (string) $row->portfolio_url : null,
            'other_links' => $row->other_links !== null ? (string) $row->other_links : null,
            'has_resume' => $row->resume_storage_key !== null && $row->resume_storage_key !== '',
            'resume_file_name' => $row->resume_file_name !== null ? (string) $row->resume_file_name : null,
            'resume_content_type' => $row->resume_content_type !== null ? (string) $row->resume_content_type : null,
            'resume_size_bytes' => $row->resume_size_bytes !== null ? (int) $row->resume_size_bytes : null,
            'status' => (string) $row->status,
            'duplicate_of' => $row->duplicate_of !== null ? (string) $row->duplicate_of : null,
            'created_at' => $this->epochSeconds($row->created_at),
            'updated_at' => $this->epochSeconds($row->updated_at),
            'status_changed_at' => $row->status_changed_at !== null ? $this->epochSeconds($row->status_changed_at) : null,
            'last_contacted_at' => $row->last_contacted_at !== null ? $this->epochSeconds($row->last_contacted_at) : null,
        ];
    }

    private function presentEmail($row): array
    {
        return [
            'id' => (int) $row->id,
            'recipient' => (string) $row->recipient,
            'subject' => (string) $row->subject,
            'body' => (string) $row->body,
            'sent_by' => (string) $row->sent_by,
            'status' => (string) $row->status,
            'error' => $row->error !== null ? (string) $row->error : null,
            'created_at' => $this->epochSeconds($row->created_at),
        ];
    }

    private function isDate($value): bool
    {
        if (!is_string($value) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m)) {
            return false;
        }

        return checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
