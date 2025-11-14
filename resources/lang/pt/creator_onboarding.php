<?php

return [
    'page' => [
        'title' => 'Resumo de onboarding do creator',
        'description' => 'Revise o status dos pagamentos, do wallet Algorand e da conformidade FlorenceEGI.',
        'heading' => 'Bem-vindo ao centro de controle do creator',
        'intro' => 'Obrigado por concluir as primeiras etapas. Esta página apresenta um resumo operacional sobre pagamentos, custódia Algorand e próximos passos.',
    ],
    'profile' => [
        'title' => 'Perfil e wallet',
        'user_name' => 'Nome completo',
        'user_email' => 'Email',
        'user_type' => 'Tipo de usuário',
        'wallet_address' => 'Wallet Algorand',
        'iban_masked' => 'IBAN registrado',
        'iban_missing' => 'IBAN não configurado. Adicione para receber pagamentos.',
    ],
    'stripe' => [
        'title' => 'Conta Stripe Connect',
        'account_id' => 'ID da conta',
        'status' => 'Visão geral do status',
        'charges_enabled' => 'Pagamentos com cartão ativados',
        'payouts_enabled' => 'Pagamentos (payouts) ativados',
        'details_submitted' => 'Verificação concluída',
        'status_ready' => 'Pronto para receber pagamentos e payouts',
        'status_pending' => 'Ações adicionais necessárias antes da liberação',
        'cta_onboarding' => 'Concluir onboarding da Stripe',
        'cta_dashboard' => 'Abrir painel Stripe Express',
        'onboarding_hint' => 'A Stripe pode solicitar documentos fiscais ou de identidade antes de liberar os payouts.',
    ],
    'actions' => [
        'title' => 'Próximos passos',
        'checklist' => [
            'onboarding' => 'Finalize o fluxo de onboarding da Stripe se ainda estiver pendente.',
            'documents' => 'Prepare documentos fiscais e de identidade para a verificação KYC.',
            'pricing' => 'Configure preços e opções Egili para seus EGI.',
            'compliance' => 'Revise as diretrizes MiCA-safe e PA para o seu ecossistema.',
        ],
    ],
    'pera' => [
        'title' => 'Wallet Algorand Pera & custódia',
        'intro' => 'A FlorenceEGI guarda o wallet Algorand até que você solicite a transferência certificada.',
        'request' => 'Para receber a frase secreta do Pera, abra um ticket no Centro de Suporte e agende uma sessão de verificação de identidade com nossa equipe.',
        'note' => 'Até a conclusão da transferência, a FlorenceEGI assina as transações on-chain em seu nome enquanto os valores em fiat são depositados diretamente na sua conta Stripe.',
    ],
    'badges' => [
        'ready' => 'Pronto',
        'pending' => 'Ação necessária',
        'missing' => 'Configuração necessária',
    ],
    'buttons' => [
        'refresh' => 'Atualizar status',
        'support' => 'Contactar suporte FlorenceEGI',
    ],
];

