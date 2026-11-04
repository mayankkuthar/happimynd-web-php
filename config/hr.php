<?php

/*
|--------------------------------------------------------------------------
| HR Auto (Resume Extractor) configuration
|--------------------------------------------------------------------------
|
| Intern project. The Laravel API in App\Http\Controllers\Hr implements the
| contract in intern_2/hr-automation/mysql-migration/API_SPEC.md, backed by
| the "hr" connection (hr_auto database). The intern app talks to this API
| instead of receiving MySQL credentials.
|
*/

return [

    // Single admin account (API_SPEC §2). No user table.
    'admin' => [
        'username' => env('HR_ADMIN_USERNAME', 'admin'),
        'password' => env('HR_ADMIN_PASSWORD'),
        'session_secret' => env('HR_SESSION_SECRET'),
        'cookie' => 'hr_session',
        'cookie_path' => '/api/hr',
        'session_ttl_hours' => (int) env('HR_SESSION_TTL_HOURS', 12),
        'cookie_samesite' => env('HR_SESSION_COOKIE_SAMESITE', 'Lax'),
        'cookie_secure' => env('HR_SESSION_COOKIE_SECURE', true),
    ],

    // Sources: one per dashboard tab; data is isolated per source.
    'sources' => ['linkedin', 'indeed', 'naukri', 'email', 'others'],

    // Departments (stored as these display labels).
    'departments' => [
        'Business Development',
        'Digital Marketing',
        'Client Servicing',
        'Psychology',
        'Torchbearer',
        'IT',
        'HR & Finance',
    ],

    // Upload limit (bytes); uploads themselves are handled by the intern app,
    // which stores the file and registers the row through POST /api/hr/records.
    'max_file_bytes' => 25 * 1024 * 1024,

    // Fields that hold a string[] (API_SPEC §4). Edits to these accept an array
    // or `;`-separated text. Extend via HR_LIST_FIELDS without a code change;
    // the canonical list lives in the intern's server/src/domain/schema.ts.
    'list_fields' => array_values(array_filter(array_map('trim', explode(
        ',',
        (string) env('HR_LIST_FIELDS', 'certification_names,project_names,technical_skills')
    )))),

    // The 56 field names (API_SPEC §3). Empty = accept any scalar key; set
    // HR_FIELDS to reject unknown keys with 422 "Unknown field 'x'" once the
    // full list is known.
    'fields' => array_values(array_filter(array_map('trim', explode(
        ',',
        (string) env('HR_FIELDS', '')
    )))),

    // CORS origins allowed with credentials (comma-separated CORS_ORIGINS).
    'cors_origins' => array_values(array_filter(array_map('trim', explode(
        ',',
        (string) env('CORS_ORIGINS', '')
    )))),

    // Candidate pipeline stages (APPLICATION_STATUSES). The first one is the
    // status given to new portal applications. The web app picks badge colours
    // from these exact names.
    'application_statuses' => array_values(array_filter(array_map('trim', explode(
        ',',
        (string) env('HR_APPLICATION_STATUSES', 'New,Under Review,Shortlisted,Interview,Selected,Rejected')
    )))),

    // Careers page (API_SPEC §7). Roles are free text, so there is no list.
    'portal' => [
        'allowed_extensions' => array_values(array_filter(array_map('trim', explode(
            ',',
            (string) env('HR_PORTAL_ALLOWED_EXTENSIONS', env('PORTAL_ALLOWED_EXTENSIONS', 'pdf,doc,docx'))
        )))),
        'max_file_bytes' => (int) env('HR_PORTAL_MAX_FILE_BYTES', env('PORTAL_MAX_FILE_BYTES', 5 * 1024 * 1024)),
        'rate_limit_per_hour' => (int) env('HR_PORTAL_RATE_LIMIT_PER_HOUR', env('PORTAL_RATE_LIMIT_PER_HOUR', 10)),
    ],

    // Object storage for resumes (GCS JSON API, service account).
    'storage' => [
        'project_id' => (string) env('HR_GCS_PROJECT_ID', env('GCS_PROJECT_ID', 'project-ae1396db-f419-4e7b-903')),
        'bucket' => (string) env('HR_GCS_BUCKET', env('GCS_BUCKET_NAME', 'it-team-test')),
        'prefix' => trim((string) env('HR_GCS_RESUME_PREFIX', env('GCS_RESUME_PREFIX', 'resumes')), '/'),
        // Either a path to the service-account JSON, or its contents in env.
        'credentials_path' => (string) env('HR_GCS_CREDENTIALS_PATH', env('GOOGLE_APPLICATION_CREDENTIALS', '')),
        'credentials_json' => (string) env('HR_GCS_CREDENTIALS_JSON', ''),
    ],

    // Application workflow (API_SPEC §8).
    'applications' => [
        'duplicate_match' => (string) env('HR_APPLICATION_DUPLICATE_MATCH', env('APPLICATION_DUPLICATE_MATCH', 'email_or_phone')),
        'duplicate_window_days' => (int) env('HR_APPLICATION_DUPLICATE_WINDOW_DAYS', env('APPLICATION_DUPLICATE_WINDOW_DAYS', 90)),
        'ack_email' => (bool) env('HR_APPLICATION_ACK_EMAIL', env('APPLICATION_ACK_EMAIL', false)),
    ],

    // SMTP for candidate email (API_SPEC §8, POST /applications/{id}/email).
    // Reads the intern's SMTP_* names too, so her .env works unchanged.
    'mail' => [
        'host' => (string) env('HR_SMTP_HOST', env('SMTP_HOST', '')),
        'port' => (int) env('HR_SMTP_PORT', env('SMTP_PORT', 587)),
        'secure' => (bool) env('HR_SMTP_SECURE', env('SMTP_SECURE', false)),
        'username' => (string) env('HR_SMTP_USER', env('SMTP_USER', '')),
        // App passwords are shown with spaces; SMTP wants them without.
        'password' => str_replace(' ', '', (string) env('HR_SMTP_PASSWORD', env('SMTP_PASSWORD', ''))),
        'from' => (string) env('HR_SMTP_FROM', env('SMTP_FROM', '')),
        'team_name' => (string) env('HR_HIRING_TEAM_NAME', env('HIRING_TEAM_NAME', 'Hiring HappiMynd')),

        // Dashboard composer templates. Placeholders are filled with
        // {{candidate_name}}, {{first_name}}, {{applied_role}} and
        // {{application_id}} (single-brace spellings also work).
        'templates' => [
            [
                'id' => 'connect',
                'name' => "We'd like to connect",
                'subject' => "We'd Like to Connect With You",
                'body' => "Hi {{candidate_name}},\n\nWe reviewed your profile and resume and would like to connect with you regarding an opportunity with our team.\n\nPlease let us know a convenient time to connect, or reply to this email and we will coordinate with you.\n\nRegards,\nHiring HappiMynd",
            ],
            [
                'id' => 'interview',
                'name' => 'Interview invitation',
                'subject' => 'Interview for {{applied_role}}',
                'body' => "Hi {{candidate_name}},\n\nThank you for applying for the {{applied_role}} position. We would like to invite you to an interview.\n\nPlease reply with a few times that work for you over the coming week and we will confirm the details.\n\nRegards,\nHiring HappiMynd",
            ],
            [
                'id' => 'regret',
                'name' => 'Not moving forward',
                'subject' => 'Your application for {{applied_role}}',
                'body' => "Hi {{candidate_name}},\n\nThank you for your interest in the {{applied_role}} position and for the time you put into your application. After careful review, we will not be moving forward at this time.\n\nWe will keep your profile on file for future opportunities.\n\nRegards,\nHiring HappiMynd",
            ],
        ],

        // Sent to the candidate after a portal submission when
        // APPLICATION_ACK_EMAIL=1 (logged with sent_by = "system").
        'ack' => [
            'subject' => "We've received your application ({{application_id}})",
            'body' => "Hi {{candidate_name}},\n\nThank you for applying for {{applied_role}}. Your application has been submitted successfully and your reference ID is {{application_id}}.\n\nOur hiring team will review your profile and contact you if there is a suitable opportunity.\n\nRegards,\nHiring HappiMynd",
        ],
    ],

];
