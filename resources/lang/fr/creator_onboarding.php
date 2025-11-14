<?php

return [
    'page' => [
        'title' => 'Résumé d’onboarding créateur',
        'description' => 'Consultez l’état des paiements, du wallet Algorand et de la conformité FlorenceEGI.',
        'heading' => 'Bienvenue dans votre centre de contrôle créateur',
        'intro' => 'Merci d’avoir complété les premières étapes. Cette page présente un résumé opérationnel des paiements, de la garde Algorand et des prochaines actions.',
    ],
    'profile' => [
        'title' => 'Profil et wallet',
        'user_name' => 'Nom complet',
        'user_email' => 'Email',
        'user_type' => 'Rôle utilisateur',
        'wallet_address' => 'Wallet Algorand',
        'iban_masked' => 'IBAN enregistré',
        'iban_missing' => 'IBAN non configuré. Ajoutez-le pour recevoir vos virements.',
    ],
    'stripe' => [
        'title' => 'Compte Stripe Connect',
        'account_id' => 'ID du compte',
        'status' => 'Vue d’ensemble du statut',
        'charges_enabled' => 'Paiements par carte activés',
        'payouts_enabled' => 'Virements activés',
        'details_submitted' => 'Vérification complétée',
        'status_ready' => 'Prêt à accepter des paiements et à recevoir des virements',
        'status_pending' => 'Actions supplémentaires requises avant l’activation',
        'cta_onboarding' => 'Terminer l’onboarding Stripe',
        'cta_dashboard' => 'Ouvrir le dashboard Stripe Express',
        'onboarding_hint' => 'Stripe peut demander des documents fiscaux ou d’identité avant de libérer les virements.',
    ],
    'actions' => [
        'title' => 'Prochaines étapes',
        'checklist' => [
            'onboarding' => 'Terminez le parcours d’onboarding Stripe s’il est encore en attente.',
            'documents' => 'Préparez les documents fiscaux et d’identité demandés pour le KYC.',
            'pricing' => 'Configurez prix et options Egili pour vos EGI.',
            'compliance' => 'Relisez les directives MiCA-safe et PA pour votre écosystème.',
        ],
    ],
    'pera' => [
        'title' => 'Wallet Pera Algorand & garde',
        'intro' => 'Le wallet Algorand est conservé par FlorenceEGI tant que vous n’avez pas demandé le transfert certifié.',
        'request' => 'Pour recevoir la phrase secrète Pera, ouvrez un ticket dans le Support Center et planifiez une session de vérification d’identité avec notre équipe.',
        'note' => 'Jusqu’à la finalisation du transfert, FlorenceEGI signe les transactions on-chain en votre nom tandis que les fonds fiat sont versés directement sur votre compte Stripe.',
    ],
    'badges' => [
        'ready' => 'Prêt',
        'pending' => 'Action requise',
        'missing' => 'Configuration nécessaire',
    ],
    'buttons' => [
        'refresh' => 'Actualiser le statut',
        'support' => 'Contacter le support FlorenceEGI',
    ],
];

