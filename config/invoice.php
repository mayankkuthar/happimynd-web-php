<?php

/*
|--------------------------------------------------------------------------
| Invoice Platform configuration
|--------------------------------------------------------------------------
|
| Intern project. The Laravel API in App\Http\Controllers\Invoice implements
| the contract in intern_2/Invoice automation platform/Invoice Project -
| API Specification.md, backed by the "invoice" connection (invoice_platform
| database). Read endpoints and DB edits only; the intern worker keeps the
| async parts (PDF, email, sheets sync, Razorpay parsing).
|
*/

return [

    // JWT auth (API_SPEC §1: POST /api/auth/login returns an HS256 token).
    'auth' => [
        'jwt_secret' => env('INVOICE_JWT_SECRET'),
        'jwt_ttl_minutes' => (int) env('INVOICE_JWT_TTL_MINUTES', 720),
        'issuer' => env('INVOICE_JWT_ISSUER', 'happimynd-invoice'),
    ],

    // Paging (list endpoints).
    'page' => [
        'default_size' => 25,
        'max_size' => 100,
    ],

    // Role ordering: VIEWER < OPERATOR < ADMIN.
    'roles' => [
        'VIEWER' => 1,
        'OPERATOR' => 2,
        'ADMIN' => 3,
    ],

    // CORS origins allowed with credentials (comma-separated CORS_ORIGINS).
    'cors_origins' => array_values(array_filter(array_map('trim', explode(
        ',',
        (string) env('CORS_ORIGINS', '')
    )))),

];
