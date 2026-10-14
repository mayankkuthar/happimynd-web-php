<?php

/*
 * CORS configuration.
 *
 * The Games Hub admin dashboard authenticates with a cookie, so when it runs
 * on a separate origin the browser needs credentialed CORS:
 *   - set CORS_ORIGINS to the web app origin(s) (comma-separated), and
 *   - set CORS_ALLOW_CREDENTIALS=true.
 *
 * NOTE: setting CORS_ORIGINS narrows CORS for the whole app (it replaces the
 * default "*"). Include every origin that calls this API.
 */

$origins = array_values(array_filter(array_map('trim', explode(
    ',',
    (string) env('CORS_ORIGINS', '')
))));

return [

    'paths' => ['api/*', 'auth/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => !empty($origins) ? $origins : ['*'],

    'allowed_origins_patterns' => [
        '#^https://.*\.vercel\.app$#',
        '#^http://localhost(:\d+)?$#',
        '#^http://127\.0\.0\.1(:\d+)?$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => (bool) env('CORS_ALLOW_CREDENTIALS', false),

];
