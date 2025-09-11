<?php

return [
    // Default pagination per type
    'per_page' => 24,

    // Searchable entity types
    'types' => [
        'egi',
        'collection',
        'creator'
    ],

    // User types allowed in filtering creators / users
    'user_types' => [
        'creator',
        'collector',
        'patron',
        'trader'
    ],

    // Max suggestions to return in quick panel
    'suggestions_limit' => 8,

    // Threshold over which we show the "troppi risultati" message in panel
    'too_many_threshold' => 200,
];
