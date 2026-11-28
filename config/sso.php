<?php

/*
|--------------------------------------------------------------------------
| Single sign-on for Quizzard and the Games Hub (HappiPAUSE)
|--------------------------------------------------------------------------
|
| Flow: the app sends the user to happimynd.com/v2/sso?app=<app>&return_to=<url>.
| The website (already logged in, or after the normal OTP login) calls
| POST /api/v1/sso/code, which checks return_to against `origins` below and
| answers with the URL to send the user back to, carrying ?sso_code=<code>.
| The app's backend swaps that code for the HappiMynd user:
|   - Quizzard: POST /api/v1/sso/exchange with the X-SSO-Secret header
|   - Games Hub: inside this app (/api/hub/auth/sso), no HTTP hop
|
*/

$extraOrigins = array_values(array_filter(array_map('trim', explode(',', (string) env('SSO_DEV_ORIGINS', '')))));

return [

    // One-time codes are only good for this long.
    'code_ttl_seconds' => 120,

    'apps' => [
        'quizzard' => [
            'origins' => array_merge(['https://quizzard.happimynd.com'], $extraOrigins),
            'secret' => env('SSO_QUIZZARD_SECRET'),
        ],
        'hub' => [
            'origins' => array_merge(['https://happipause.happimynd.com'], $extraOrigins),
            'secret' => null, // exchanged in-process
        ],
    ],

    // Signs the Games Hub player token (falls back to APP_KEY).
    'hub_player_token_secret' => env('HUB_PLAYER_TOKEN_SECRET'),
    'hub_player_token_days' => 30,

    // OTP abuse limits (send-login-otp / verify-login-otp).
    'otp_sends_per_hour' => 10,
    'otp_max_wrong_attempts' => 5,
];
