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

];
