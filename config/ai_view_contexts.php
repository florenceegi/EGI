<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI View Contexts Configuration
    |--------------------------------------------------------------------------
    |
    | Maps each view identifier to its translation key and metadata.
    | The context is injected into AI prompts to provide accurate,
    | view-specific guidance based on actual codebase analysis.
    |
    | @see resources/lang/{locale}/ai_contexts.php for translated content
    | @author FlorenceEGI Dev Team
    | @version 1.0.0
    | @date 2026-02-09
    */

    'views' => [
        /*
        |--------------------------------------------------------------------------
        | Company Views
        |--------------------------------------------------------------------------
        */

        'company.portfolio' => [
            'translation_key' => 'ai_contexts.company.portfolio',
            'archetype' => 'company',
            'controller' => 'App\Http\Controllers\CompanyHomeController',
            'route_name' => 'company.portfolio',
            'rag_boost_terms' => 'company portfolio collections EGI created owned stats reservations',
            'priority' => 'high',
            'languages' => ['it', 'en', 'de', 'es', 'fr', 'pt'],
        ],

        'company.collections' => [
            'translation_key' => 'ai_contexts.company.collections',
            'archetype' => 'company',
            'controller' => 'App\Http\Controllers\CompanyHomeController',
            'route_name' => 'company.collections',
            'rag_boost_terms' => 'company collections brand products catalog organization',
            'priority' => 'high',
            'languages' => ['it', 'en', 'de', 'es', 'fr', 'pt'],
        ],

        /*
        |--------------------------------------------------------------------------
        | Creator Views
        |--------------------------------------------------------------------------
        */

        'creator.portfolio' => [
            'translation_key' => 'ai_contexts.creator.portfolio',
            'archetype' => 'creator',
            'controller' => 'App\Http\Controllers\CreatorHomeController',
            'route_name' => 'creator.portfolio',
            'rag_boost_terms' => 'creator portfolio works biography EPP collections created owned artist',
            'priority' => 'high',
            'languages' => ['it', 'en', 'de', 'es', 'fr', 'pt'],
        ],

        'creator.collections' => [
            'translation_key' => 'ai_contexts.creator.collections',
            'archetype' => 'creator',
            'controller' => 'App\Http\Controllers\CreatorHomeController',
            'route_name' => 'creator.collections',
            'rag_boost_terms' => 'creator collections artistic works series creative vision EPP',
            'priority' => 'high',
            'languages' => ['it', 'en', 'de', 'es', 'fr', 'pt'],
        ],

        /*
        |--------------------------------------------------------------------------
        | Collector Views
        |--------------------------------------------------------------------------
        */

        'collector.portfolio' => [
            'translation_key' => 'ai_contexts.collector.portfolio',
            'archetype' => 'collector',
            'controller' => 'App\Http\Controllers\CollectorHomeController',
            'route_name' => 'collector.home', // Route redirects to portfolio
            'rag_boost_terms' => 'collector portfolio purchased owned acquisitions reservations buyer',
            'priority' => 'high',
            'languages' => ['it', 'en', 'de', 'es', 'fr', 'pt'],
        ],

        /*
        |--------------------------------------------------------------------------
        | Collection Views
        |--------------------------------------------------------------------------
        */

        'collection.show' => [
            'translation_key' => 'ai_contexts.collection.show',
            'archetype' => 'public', // Visible to all users
            'controller' => 'App\Http\Controllers\CollectionsController',
            'route_name' => 'collections.show',
            'rag_boost_terms' => 'collection EGI gallery creator EPP environmental project purchase reserve',
            'priority' => 'high',
            'languages' => ['it', 'en', 'de', 'es', 'fr', 'pt'],
        ],

        /*
        |--------------------------------------------------------------------------
        | Future Views (Template)
        |--------------------------------------------------------------------------
        |
        | Add more views following the same structure:
        |
        | 'epp.projects.index' => [...],
        | 'pa.dashboard' => [...],
        | etc.
        */
    ],

    /*
    |--------------------------------------------------------------------------
    | Context Injection Settings
    |--------------------------------------------------------------------------
    */

    'injection' => [
        // Enable/disable view context injection
        'enabled' => env('AI_VIEW_CONTEXT_ENABLED', true),

        // Use generic prompt if view context not found
        'fallback_on_missing' => true,

        // Cache translated contexts for performance
        'cache_enabled' => env('AI_VIEW_CONTEXT_CACHE', true),
        'cache_ttl' => 3600, // 1 hour

        // Log when view context is used (for debugging)
        'log_usage' => env('AI_VIEW_CONTEXT_LOG', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Context Format Settings
    |--------------------------------------------------------------------------
    */

    'format' => [
        // Include route names in context
        'include_routes' => true,

        // Include controller info in context (for debugging)
        'include_controller_info' => env('APP_DEBUG', false),

        // Maximum context length (characters) to prevent token overflow
        'max_length' => 4000,
    ],
];
