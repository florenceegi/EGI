<?php

return [
    'errors' => [
        'dev' => [
            'connect_account_failed' => '[STRIPE] Connect account creation failed for wallet :wallet_id (user :user_id). Error: :error',
            'connect_account_link_failed' => '[STRIPE] Account onboarding link creation failed for account :stripe_account_id. Error: :error',
            'connect_account_retrieve_failed' => '[STRIPE] Connect account retrieval failed for account :stripe_account_id. Error: :error',
        ],
        'user' => [
            'connect_account_failed' => 'We could not prepare your Stripe Connect account. Please try again or contact support.',
            'connect_account_link_failed' => 'We could not generate the Stripe onboarding link. Please refresh the page or contact support.',
            'connect_account_retrieve_failed' => 'We could not retrieve your Stripe account status. Please refresh this page later.',
        ],
    ],
];

