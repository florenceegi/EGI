<?php

return [
    'errors' => [
        'dev' => [
            'connect_account_failed' => '[STRIPE] Connect-Konto konnte für Wallet :wallet_id (Benutzer :user_id) nicht erstellt werden. Fehler: :error',
            'connect_account_link_failed' => '[STRIPE] Onboarding-Link konnte für Konto :stripe_account_id nicht erstellt werden. Fehler: :error',
            'connect_account_retrieve_failed' => '[STRIPE] Connect-Konto :stripe_account_id konnte nicht abgerufen werden. Fehler: :error',
        ],
        'user' => [
            'connect_account_failed' => 'Wir konnten dein Stripe-Connect-Konto nicht vorbereiten. Bitte versuche es erneut oder kontaktiere den Support.',
            'connect_account_link_failed' => 'Der Stripe-Onboarding-Link konnte nicht erzeugt werden. Aktualisiere die Seite oder kontaktiere den Support.',
            'connect_account_retrieve_failed' => 'Der Status deines Stripe-Kontos konnte nicht abgerufen werden. Versuche es später erneut.',
        ],
    ],
];

