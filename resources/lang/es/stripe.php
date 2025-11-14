<?php

return [
    'errors' => [
        'dev' => [
            'connect_account_failed' => '[STRIPE] Error al crear la cuenta Connect para el wallet :wallet_id (usuario :user_id). Error: :error',
            'connect_account_link_failed' => '[STRIPE] Error al generar el enlace de onboarding para la cuenta :stripe_account_id. Error: :error',
            'connect_account_retrieve_failed' => '[STRIPE] Error al recuperar la cuenta Connect :stripe_account_id. Error: :error',
        ],
        'user' => [
            'connect_account_failed' => 'No pudimos preparar tu cuenta Stripe Connect. Intenta nuevamente o contacta al soporte.',
            'connect_account_link_failed' => 'No fue posible generar el enlace de onboarding de Stripe. Actualiza la página o contacta al soporte.',
            'connect_account_retrieve_failed' => 'No fue posible recuperar el estado de tu cuenta Stripe. Vuelve a intentarlo más tarde.',
        ],
    ],
];

