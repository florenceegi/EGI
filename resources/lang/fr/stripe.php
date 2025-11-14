<?php

return [
    'errors' => [
        'dev' => [
            'connect_account_failed' => '[STRIPE] Échec de création du compte Connect pour le wallet :wallet_id (utilisateur :user_id). Erreur : :error',
            'connect_account_link_failed' => '[STRIPE] Échec de génération du lien d’onboarding pour le compte :stripe_account_id. Erreur : :error',
            'connect_account_retrieve_failed' => '[STRIPE] Échec de récupération du compte Connect :stripe_account_id. Erreur : :error',
        ],
        'user' => [
            'connect_account_failed' => 'Impossible de préparer votre compte Stripe Connect. Réessayez ou contactez le support.',
            'connect_account_link_failed' => 'Impossible de générer le lien d’onboarding Stripe. Actualisez la page ou contactez le support.',
            'connect_account_retrieve_failed' => 'Impossible de récupérer l’état de votre compte Stripe. Réessayez plus tard.',
        ],
    ],
];

