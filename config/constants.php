<?php

return [
    'professions' => [
        ['salaried', false],
        ['self employed', false],
        ['home maker', false],
        ['jobseeker', false],
        ['entrepreneur', false],
        ['frontline warrior', true],
        ['senior citizen', true],
        ['student(School)', true],
        ['student(college/university)', true],
    ],
    'professions_for_rules' => [ //TODO:remove
        'salaried',
        'self employed',
        'home maker',
        'jobseeker',
        'entrepreneur',
    ],
    'gender' => [
        'male',
        'female',
        'other',
    ],
    'mediaAssets' => [
        'dashboard' => [
            'folderName' => 'assets/dashboard/',
        ],
        'landing_page' => [
            'folderName' => 'assets/landingpage/'
        ],
        'userProfilePicture' => 'assets/Frontend/images/profile/',
        'userDefaultProfilePicture' => 'male1.svg',
        'ratingPicture' => [
            'folderName' => 'assets/assessment/rating_pictures/'
        ],

        'happimynd_app_rating' => [
            'folderName' => 'application_rating_emojies/'
        ],
        'mood_o_meter_emojies' => [
            'folderName' => 'mood_o_meter/'
        ],

        'assessmentReports' => [
            'folderName' => 'assessmentReports/',
        ],
        'services' => [
            'folderName' => 'assets/services/'
        ],

        'admin' => [
            'folderName' => 'admin/dashboard-picture/',
        ],
        'psychologist' => [
            'profilePicture' => [
                'folderName' => 'assets/psychologist/profile_pictures'
            ]
        ],
        'teams' => [
            'folderName' => 'assets/ourteam/'
        ],
        'client' => [
            'folderName' => 'assets/ourclient/'
        ],
        'org' => [
            'folderName' => 'assets/organisation/'
        ],
        'quotes'=> [
            'folderName' => 'assets/quotes/'
        ],
        'organization_logo' => [
            'folderName' => 'organization_logo/'
        ],
        'happiLearn_thumbnail' => [
            'folderName' => 'happilearn_thumbnail/'
        ],
        'happiLearn_content' => [
            'folderName' => 'happilearn_images_infographic/'
        ],
        'happiself_course' => [
            'folderName' => 'happiself_course/'
        ],
        'happiself_course_media' => [
            'folderName' => 'happiself_course_media/'
        ],
        'happiself_library' => [
            'folderName' => 'happiself_library/'
        ],
    ],
    'bitrix' => env('BITRIX'),
    'blinkingText' => [
        'screening' => [
            "text" => "Your HappiLIFE Awareness Tool is not complete, complete it from your profile icon dropdown",
            "link" => 'user.assessment'
        ],
        'summary_reading' => [
            "text" => "Avail assisted Summary Reading with a mental wellness expert for discovering your hidden potentials and opportunities to grow",
            "link" => "user.payment.buyBundle"
        ],
        'happiapp' => [
            "text" => "Avail HappiAPP, a smartphone app for self-management of your emotional wellness",
            "link" => "user.payment.buyBundle"
        ]
    ]
];
