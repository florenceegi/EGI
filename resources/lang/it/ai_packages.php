<?php

return [
    'section_title' => 'Feature Pricing',
    'section_description' => 'Pacchetti di accesso ai servizi AI della piattaforma FlorenceEGI',
    
    'packages' => [
        'starter' => [
            'name' => 'Starter Pack',
            'description' => 'Pacchetto di accesso ai servizi AI — livello base. Ideale per singoli utenti o prima esperienza.',
            'description_short' => 'Livello base per il primo approccio',
            'benefits' => [
                'Egili accreditati istantaneamente',
                'Pagamento sicuro Stripe/PayPal',
                'Fattura aggregata mensile',
            ],
        ],
        'professional' => [
            'name' => 'Professional Pack',
            'description' => 'Pacchetto professionale per uso intensivo dei servizi AI.',
            'description_short' => 'Per utilizzo regolare e professionale',
            'benefits' => [
                'Egili accreditati istantaneamente',
                'Pagamento sicuro Stripe/PayPal',
                'Fattura aggregata mensile',
                'Priorità support tecnico',
            ],
        ],
        'business' => [
            'name' => 'Business Pack',
            'description' => 'Pacchetto business per team e agenzie con utilizzo continuativo.',
            'description_short' => 'Per team e organizzazioni',
            'benefits' => [
                'Egili accreditati istantaneamente',
                'Pagamento sicuro Stripe/PayPal',
                'Fattura aggregata mensile',
                'Account manager dedicato',
                'API access prioritario',
            ],
        ],
        'enterprise' => [
            'name' => 'Enterprise Pack',
            'description' => 'Pacchetto enterprise per grandi organizzazioni e Pubbliche Amministrazioni.',
            'description_short' => 'Per grandi organizzazioni e PA',
            'benefits' => [
                'Egili accreditati istantaneamente',
                'Pagamento sicuro Stripe/PayPal',
                'Fattura aggregata mensile',
                'Account manager dedicato',
                'Support prioritario 24/7',
                'SLA garantito',
                'API unlimited',
            ],
        ],
    ],
    
    'columns' => [
        'feature' => 'Feature',
        'category' => 'Categoria',
        'egili' => 'Egili',
        'eur' => 'EUR',
        'accessi' => 'Accessi',
        'acquisiti' => 'Acquisiti',
        'azioni' => 'Azioni',
    ],
    
    'categories' => [
        'ai_services' => 'Servizi AI',
        'platform_services' => 'Servizi Platform',
        'premium_visibility' => 'Visibilità Premium',
        'governance' => 'Governance',
    ],
];
