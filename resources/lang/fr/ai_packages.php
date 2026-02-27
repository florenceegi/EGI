<?php

return [
    'section_title' => 'Tarification des Fonctionnalités',
    'section_description' => 'Forfaits d\'accès aux services IA pour la plateforme FlorenceEGI',

    'packages' => [
        'starter' => [
            'name' => 'Forfait Démarrage',
            'description' => 'Forfait d\'accès aux services IA — niveau de base. Idéal pour les utilisateurs individuels ou première approche.',
            'description_short' => 'Niveau de base pour débuter',
            'benefits' => [
                'Egili crédités instantanément',
                'Paiement sécurisé Stripe/PayPal',
                'Facture mensuelle agrégée',
            ],
        ],
        'professional' => [
            'name' => 'Forfait Professionnel',
            'description' => 'Forfait professionnel pour un usage intensif des services IA.',
            'description_short' => 'Pour un usage régulier et professionnel',
            'benefits' => [
                'Egili crédités instantanément',
                'Paiement sécurisé Stripe/PayPal',
                'Facture mensuelle agrégée',
                'Support technique prioritaire',
            ],
        ],
        'business' => [
            'name' => 'Forfait Entreprise',
            'description' => 'Forfait entreprise pour équipes et agences avec utilisation continue.',
            'description_short' => 'Pour équipes et organisations',
            'benefits' => [
                'Egili crédités instantanément',
                'Paiement sécurisé Stripe/PayPal',
                'Facture mensuelle agrégée',
                'Gestionnaire de compte dédié',
                'Accès API prioritaire',
            ],
        ],
        'enterprise' => [
            'name' => 'Forfait Entreprise',
            'description' => 'Forfait enterprise pour grandes organisations et administrations publiques.',
            'description_short' => 'Pour grandes organisations et AP',
            'benefits' => [
                'Egili crédités instantanément',
                'Paiement sécurisé Stripe/PayPal',
                'Facture mensuelle agrégée',
                'Gestionnaire de compte dédié',
                'Support prioritaire 24/7',
                'SLA garanti',
                'API illimité',
            ],
        ],
    ],

    'columns' => [
        'feature' => 'Fonctionnalité',
        'category' => 'Catégorie',
        'egili' => 'Egili',
        'eur' => 'EUR',
        'accessi' => 'Accès',
        'acquisiti' => 'Acquis',
        'azioni' => 'Actions',
    ],

    'categories' => [
        'ai_services' => 'Services IA',
        'platform_services' => 'Services Plateforme',
        'premium_visibility' => 'Visibilité Premium',
        'governance' => 'Gouvernance',
    ],
];
