<?php

/*
|--------------------------------------------------------------------------
| HappiMynd Games Hub configuration
|--------------------------------------------------------------------------
|
| Settings for the Games Hub API (intern project) that runs inside this
| Laravel app but on its own MySQL database (connection "hub").
|
*/

return [

    // Admin dashboard (single admin account), see API_SPEC §5.
    'admin' => [
        'email' => env('HUB_ADMIN_EMAIL'),
        'password_hash' => env('HUB_ADMIN_PASSWORD_HASH'),
        'session_secret' => env('HUB_ADMIN_SESSION_SECRET'),
        'cookie' => 'hm_admin',
        'cookie_path' => '/api/hub/admin',
        'cookie_minutes' => 720, // 12 hours
        'cookie_secure' => env('HUB_ADMIN_COOKIE_SECURE', true),
        'lockout_attempts' => 5,
        'lockout_minutes' => 15,
    ],

    // Public caches (seconds), cleared on publish (API_SPEC §6).
    'cache' => [
        'ttl' => 30,
        'catalog_key' => 'hub:catalog',
        'content_key' => 'hub:content',
    ],

    // Game slug pattern used by the front end / tracking / content routes.
    'slug_pattern' => '/^[a-z0-9][a-z0-9-]{0,59}$/',

    // Content publish limits (API_SPEC §5, catalog-limits.ts).
    'limits' => [
        'name' => 40,
        'tagline' => 60,
        'description' => 160,
        'catalog_entries' => 200,
        'content_entries' => 200,
    ],

    // CORS origins allowed with credentials (comma-separated CORS_ORIGINS).
    'cors_origins' => array_values(array_filter(array_map('trim', explode(
        ',',
        (string) env('CORS_ORIGINS', '')
    )))),

];
