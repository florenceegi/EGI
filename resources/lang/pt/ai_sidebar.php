<?php

/**
 * AI Sidebar - Traduções em Português
 * P0-2: Translation keys only
 * P0-9: All 6 languages required
 */

return [
    // General
    'title' => 'Natan',
    'subtitle' => 'Ajudo você a completar seu perfil',
    'toggle_title' => 'Abrir assistente de onboarding',
    'close' => 'Fechar',
    'send' => 'Enviar',
    'chat_placeholder' => 'Pergunte algo...',
    'assistant_name' => 'Natan',
    'quick_actions_label' => 'Próximos passos sugeridos:',

    // Messages (programmatic AI)
    'messages' => [
        'welcome' => 'Bem-vindo! Ajudo você a completar a configuração do seu perfil.',
        'progress_low' => 'Bom começo! Você completou :completed de :total passos. Próximo: :nextStep',
        'progress_high' => 'Quase lá! Faltam apenas :remaining passos.',
        'complete' => 'Parabéns! Seu perfil está totalmente configurado. 🎉',
        'all_done' => 'Todos os passos de configuração completados!',
    ],

    // Checklist
    'checklist' => [
        'progress' => 'Progresso da configuração',
        'title' => 'Lista de Onboarding',
    ],

    // Discourse messages (AI-like text)
    'discourse' => [
        'greeting' => 'Olá',
        'greeting_suffix' => '! Estou aqui para te ajudar a completar o seu perfil.',
        'progress_intro' => 'Você completou',
        'progress_of' => 'de',
        'progress_suffix' => ' passos. Vamos ver o que falta para deixar seu perfil perfeito.',
        'missing_title' => 'Aqui está o que você ainda precisa fazer:',
        'suggestion_intro' => 'Sugiro começar por',
        'click_hint' => 'Clique em qualquer item da lista abaixo para completá-lo.',
        'complete_title' => 'Fantástico!',
        'complete_text' => 'Seu perfil está completo e pronto para ser descoberto. Agora você pode se concentrar em criar suas obras e fazer sua comunidade crescer.',
    ],

    // Checklist Items - Creator
    'steps' => [
        'avatar' => [
            'title' => 'Carregue seu avatar',
            'description' => 'Adicione uma foto de perfil para ser reconhecido',
        ],
        'banner' => [
            'title' => 'Personalize seu banner',
            'description' => 'Adicione uma imagem de capa ao seu perfil',
        ],
        'bio' => [
            'title' => 'Escreva sua bio',
            'description' => 'Conte-nos quem você é e o que cria',
        ],
        'stripe' => [
            'title' => 'Configure os pagamentos',
            'description' => 'Conecte o Stripe para receber pagamentos',
        ],
        'collection' => [
            'title' => 'Crie sua primeira coleção',
            'description' => 'Organize suas obras em uma coleção',
        ],
        'first_egi' => [
            'title' => 'Crie seu primeiro EGI',
            'description' => 'Publique sua primeira obra digital',
        ],
        'social_links' => [
            'title' => 'Adicione links sociais',
            'description' => 'Conecte seus perfis sociais',
        ],
        'verify_email' => [
            'title' => 'Verifique seu email',
            'description' => 'Confirme seu endereço de e-mail',
        ],
    ],

    // Errors
    'errors' => [
        'request_failed' => 'Requisição falhou. Por favor, tente novamente.',
        'connection_error' => 'Erro de conexão. Por favor, verifique sua conexão com a internet.',
        'invalid_user_type' => 'Tipo de usuário inválido.',
        'unauthorized' => 'Não autorizado a acessar este recurso.',
    ],
];
