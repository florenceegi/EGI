<?php

return [
    'section_title' => 'Feature Pricing',
    'section_description' => 'AI service access packages for the FlorenceEGI platform',
    
    'packages' => [
        'starter' => [
            'name' => 'Starter Pack',
            'description' => 'AI services access package — basic level. Ideal for individual users or first-time approach.',
            'description_short' => 'Basic level for initial approach',
            'benefits' => [
                'Egili credited instantly',
                'Secure payment Stripe/PayPal',
                'Aggregated monthly invoice',
            ],
        ],
        'professional' => [
            'name' => 'Professional Pack',
            'description' => 'Professional package for intensive AI services usage.',
            'description_short' => 'For regular and professional use',
            'benefits' => [
                'Egili credited instantly',
                'Secure payment Stripe/PayPal',
                'Aggregated monthly invoice',
                'Priority technical support',
            ],
        ],
        'business' => [
            'name' => 'Business Pack',
            'description' => 'Business package for teams and agencies with continuous usage.',
            'description_short' => 'For teams and organizations',
            'benefits' => [
                'Egili credited instantly',
                'Secure payment Stripe/PayPal',
                'Aggregated monthly invoice',
                'Dedicated account manager',
                'Priority API access',
            ],
        ],
        'enterprise' => [
            'name' => 'Enterprise Pack',
            'description' => 'Enterprise package for large organizations and Public Administrations.',
            'description_short' => 'For large organizations and PA',
            'benefits' => [
                'Egili credited instantly',
                'Secure payment Stripe/PayPal',
                'Aggregated monthly invoice',
                'Dedicated account manager',
                'Priority 24/7 support',
                'Guaranteed SLA',
                'Unlimited API',
            ],
        ],
    ],
    
    'columns' => [
        'feature' => 'Feature',
        'category' => 'Category',
        'egili' => 'Egili',
        'eur' => 'EUR',
        'accessi' => 'Access',
        'acquisiti' => 'Acquired',
        'azioni' => 'Actions',
    ],
    
    'categories' => [
        'ai_services' => 'AI Services',
        'platform_services' => 'Platform Services',
        'premium_visibility' => 'Premium Visibility',
        'governance' => 'Governance',
    ],
];
