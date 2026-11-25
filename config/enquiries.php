<?php

/*
|--------------------------------------------------------------------------
| Conscious Growth — Website Enquiries
|--------------------------------------------------------------------------
|
| Contract: intern_2/Consious Growth Setup/Enquiries API Specification.md.
| All database work goes through the stored procedures in
| enquiry_db_procedures.sql on the "enquiries" connection.
|
*/

return [

    // Admin endpoints require `Authorization: Bearer <token>`. Empty = closed.
    'admin_token' => env('ENQUIRIES_ADMIN_TOKEN'),

    // Public form submissions per IP per hour.
    'public_rate_limit_per_hour' => (int) env('ENQUIRIES_RATE_LIMIT_PER_HOUR', 10),

    'interests' => [
        'Individual Coaching & Growth',
        'Leadership Development & LEAP',
        'Organisational Programs',
        'Speaking & Event Invitations',
        'Other Enquiries',
    ],

    'pathways' => [
        'students-career-aspirants',
        'working-professionals',
        'managers-leaders',
        'senior-leaders-cxos',
        'organisations-teams',
    ],

    'timelines' => [
        'Immediate (This month)',
        'Within 1-2 months',
        'Currently exploring',
    ],

    'engagement_types' => [
        'Leadership talk',
        'Corporate workshop',
        'Webinar',
        'Panel discussion',
        'Guest session',
    ],

    'audiences' => [
        'Corporate leaders and teams',
        'HR and L&D professionals',
        'Students and emerging professionals',
        'Industry or public audience',
    ],

    'event_formats' => [
        'In person',
        'Virtual',
        'Hybrid',
    ],

    'types' => ['GENERAL', 'PATHWAY', 'SPEAKING'],

    'statuses' => ['NEW', 'CONTACTED', 'IN_PROGRESS', 'CLOSED', 'SPAM'],

];
