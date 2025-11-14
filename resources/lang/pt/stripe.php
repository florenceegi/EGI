<?php

return [
    'errors' => [
        'dev' => [
            'connect_account_failed' => '[STRIPE] Falha ao criar conta Connect para o wallet :wallet_id (usuário :user_id). Erro: :error',
            'connect_account_link_failed' => '[STRIPE] Falha ao gerar link de onboarding para a conta :stripe_account_id. Erro: :error',
            'connect_account_retrieve_failed' => '[STRIPE] Falha ao recuperar conta Connect :stripe_account_id. Erro: :error',
        ],
        'user' => [
            'connect_account_failed' => 'Não foi possível preparar a sua conta Stripe Connect. Tente novamente ou contate o suporte.',
            'connect_account_link_failed' => 'Não foi possível gerar o link de onboarding da Stripe. Atualize a página ou contate o suporte.',
            'connect_account_retrieve_failed' => 'Não foi possível recuperar o status da sua conta Stripe. Tente novamente mais tarde.',
        ],
    ],
];

