<?php

/*
|--------------------------------------------------------------------------
| HappiLIFE — progress & completion reminders
|--------------------------------------------------------------------------
|
| Answers are saved server-side one by one (assessment_answers), so an
| unfinished HappiLIFE attempt can be resumed from the website or the mobile
| app with the same login. The settings below drive the reminders that nudge
| users back to an unfinished attempt (`php artisan happilife:send-reminders`,
| scheduled daily in App\Console\Kernel, plus the manual admin page).
|
*/

return [

    // Master switch for the scheduled (automatic) reminders.
    'reminders_enabled' => filter_var(env('HAPPILIFE_REMINDERS_ENABLED', true), FILTER_VALIDATE_BOOLEAN),

    // Time of day (app timezone) the automatic reminder run happens.
    'reminder_time' => env('HAPPILIFE_REMINDER_TIME', '11:00'),

    // Days of inactivity (since the last saved answer, or since the attempt
    // was started) after which reminder #1, #2, #3 ... is sent. Each period of
    // inactivity restarts the sequence: answering a question resets it.
    'reminder_days' => array_map('intval', explode(',', env('HAPPILIFE_REMINDER_DAYS', '1,3,7,14'))),

    // Never send two automatic reminders closer together than this.
    'min_gap_days' => (int) env('HAPPILIFE_REMINDER_MIN_GAP_DAYS', 2),

    // Attempts idle for longer than this are left alone, so the first run
    // after deployment doesn't message years-old abandoned attempts.
    'max_inactive_days' => (int) env('HAPPILIFE_REMINDER_MAX_INACTIVE_DAYS', 30),

    // Channels used for reminders. In-app (notification list) is always on.
    'channels' => [
        'push'  => filter_var(env('HAPPILIFE_REMINDER_PUSH', true), FILTER_VALIDATE_BOOLEAN),
        'email' => filter_var(env('HAPPILIFE_REMINDER_EMAIL', true), FILTER_VALIDATE_BOOLEAN),
    ],

    // Where the email button sends people (the website's HappiLIFE page).
    'resume_url' => env('HAPPILIFE_RESUME_URL', 'https://happimynd.com/v2/assessment'),

    // Rough time per question, used for "about N minutes left" copy.
    'seconds_per_question' => (int) env('HAPPILIFE_SECONDS_PER_QUESTION', 10),
];
