<?php

/*
|--------------------------------------------------------------------------
| Games Hub content publish schemas
|--------------------------------------------------------------------------
|
| Source of truth in the original app is src/config/content-schemas.ts. That
| file was NOT part of the migration package, so these rules are a port of
| what API_SPEC.md §5 documents. To reach exact parity, replace/extend the
| "games" map with the real per-game schemas (or share content-schemas.ts).
|
| Schema shape per collection:
|   [
|     'type'     => 'textList' | 'fields',
|     'minItems' => int, 'maxItems' => int,     // list-level
|     'item'     => [...]                        // for type=textList: a field schema
|     'fields'   => ['fieldName' => [...]]       // for type=fields
|   ]
|
| Field schema shape:
|   [
|     'type'        => 'text' | 'emoji' | 'select' | 'textList',
|     'max'         => int,        // text/emoji hard limit
|     'soft'        => int,        // text/emoji soft limit (warning only)
|     'mustInclude' => ['{name}'],  // placeholders that must appear
|     'optional'    => bool,
|     'count'       => int,        // textList: exact item count
|     'min' => int, 'maxItems' => int,  // textList: bounds
|     'options'     => [...],      // select
|     'item'        => [...],      // textList: item field schema
|   ]
|
*/

return [

    // Collections that exist for every game.
    'common' => [
        'end-titles' => [
            'type' => 'textList',
            'minItems' => 1,
            'maxItems' => 50,
            'item' => ['type' => 'text', 'max' => 80, 'soft' => 60],
        ],
        'end-messages' => [
            'type' => 'textList',
            'minItems' => 1,
            'maxItems' => 50,
            'item' => ['type' => 'text', 'max' => 160, 'soft' => 120],
        ],
    ],

    // Per-game collections (game slug => collection => schema).
    // Populate from src/config/content-schemas.ts for exact validation.
    'games' => [
        // 'guess-my-answer' => [ 'questions' => [ ... ] ],
        // 'control'         => [ 'areas' => [ ... ] ],
        // 'thought-traffic' => [ 'thoughts' => [ ... ] ],
    ],

    // When true, a collection that is not defined for the game is rejected.
    // Default false so unknown-but-valid front-end content is not blocked —
    // the dashboard already validates client-side with the real schemas.
    'strict' => false,
];
