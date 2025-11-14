<?php

return [
    'errors' => [
        'dev' => [
            'connect_account_failed' => '[STRIPE] Creazione account Connect fallita per wallet :wallet_id (utente :user_id). Errore: :error',
            'connect_account_link_failed' => '[STRIPE] Creazione link di onboarding fallita per account :stripe_account_id. Errore: :error',
            'connect_account_retrieve_failed' => '[STRIPE] Recupero account Connect fallito per account :stripe_account_id. Errore: :error',
        ],
        'user' => [
            'connect_account_failed' => 'Non siamo riusciti a preparare il tuo account Stripe Connect. Riprova o contatta il supporto.',
            'connect_account_link_failed' => 'Non è stato possibile generare il link di onboarding Stripe. Aggiorna la pagina o contatta il supporto.',
            'connect_account_retrieve_failed' => 'Non è stato possibile recuperare lo stato del tuo account Stripe. Aggiorna questa pagina più tardi.',
        ],
    ],
];

