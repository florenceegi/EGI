<?php

return [
    /**
     * Configurazione varianti immagini responsive
     * Questi valori devono corrispondere a quelli nell'Ultra Upload Manager
     */
    'variants' => [
        'egi' => [
            'thumbnail' => ['width' => 150, 'height' => 150, 'quality' => 85],
            'mobile' => ['width' => 400, 'height' => 400, 'quality' => 80],
            'tablet' => ['width' => 600, 'height' => 600, 'quality' => 75],
            'desktop' => ['width' => 800, 'height' => 800, 'quality' => 75],
        ],
        'banner' => [
            'mobile' => ['width' => 800, 'height' => 400, 'quality' => 80],
            'tablet' => ['width' => 1200, 'height' => 600, 'quality' => 75],
            'desktop' => ['width' => 1920, 'height' => 960, 'quality' => 70],
        ],
        'card' => [
            'default' => ['width' => 300, 'height' => 300, 'quality' => 85],
        ],
        'avatar' => [
            'default' => ['width' => 200, 'height' => 200, 'quality' => 90],
        ],
    ],

    /**
     * Media queries per responsive design
     */
    'media_queries' => [
        'mobile' => '(max-width: 767px)',
        'tablet' => '(min-width: 768px) and (max-width: 1023px)',
        'desktop' => '(min-width: 1024px)',
    ],

    /**
     * Formati supportati
     */
    'formats' => [
        'optimized' => 'webp',
        'fallback' => 'jpg',
    ],

    /**
     * Path per le immagini ottimizzate
     */
    'optimized_subdir' => 'optimized',

    /**
     * Storage disk da verificare in ordine di priorità
     */
    'storage_disks' => ['s3', 'public', 'do'],

    /**
     * Fallback behavior
     */
    'fallback_to_original' => true,
    'cache_existence_checks' => true,
    'cache_ttl' => 3600, // 1 hour
];
