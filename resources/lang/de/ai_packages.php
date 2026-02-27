<?php

return [
    'section_title' => 'Feature Pricing',
    'section_description' => 'KI-Service-Zugriffspakete für die FlorenceEGI-Plattform',

    'packages' => [
        'starter' => [
            'name' => 'Starter Pack',
            'description' => 'KI-Service-Zugangspaket – Grundstufe. Ideal für Einzelnutzer oder ersten Zugang.',
            'description_short' => 'Grundstufe für den ersten Ansatz',
            'benefits' => [
                'Egili sofort gutgeschrieben',
                'Sichere Zahlung Stripe/PayPal',
                'Aggregierte monatliche Rechnung',
            ],
        ],
        'professional' => [
            'name' => 'Professional Pack',
            'description' => 'Professionelles Paket für intensive KI-Service-Nutzung.',
            'description_short' => 'Für regelmäßige und professionelle Nutzung',
            'benefits' => [
                'Egili sofort gutgeschrieben',
                'Sichere Zahlung Stripe/PayPal',
                'Aggregierte monatliche Rechnung',
                'Priorisierter technischer Support',
            ],
        ],
        'business' => [
            'name' => 'Business Pack',
            'description' => 'Business-Paket für Teams und Agenturen mit kontinuierlicher Nutzung.',
            'description_short' => 'Für Teams und Organisationen',
            'benefits' => [
                'Egili sofort gutgeschrieben',
                'Sichere Zahlung Stripe/PayPal',
                'Aggregierte monatliche Rechnung',
                'Dedizierter Account Manager',
                'Priorisierter API-Zugang',
            ],
        ],
        'enterprise' => [
            'name' => 'Enterprise Pack',
            'description' => 'Enterprise-Paket für große Organisationen und öffentliche Verwaltungen.',
            'description_short' => 'Für große Organisationen und Behörden',
            'benefits' => [
                'Egili sofort gutgeschrieben',
                'Sichere Zahlung Stripe/PayPal',
                'Aggregierte monatliche Rechnung',
                'Dedizierter Account Manager',
                'Priorisierter 24/7 Support',
                'Garantierte SLA',
                'Unbegrenztes API',
            ],
        ],
    ],

    'columns' => [
        'feature' => 'Funktion',
        'category' => 'Kategorie',
        'egili' => 'Egili',
        'eur' => 'EUR',
        'accessi' => 'Zugriffe',
        'acquisiti' => 'Erworben',
        'azioni' => 'Aktionen',
    ],

    'categories' => [
        'ai_services' => 'KI-Services',
        'platform_services' => 'Plattform-Services',
        'premium_visibility' => 'Premium-Sichtbarkeit',
        'governance' => 'Governance',
    ],
];
