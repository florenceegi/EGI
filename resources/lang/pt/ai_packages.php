<?php

return [
    'section_title' => 'Preços de Funcionalidades',
    'section_description' => 'Pacotes de acesso aos serviços de IA da plataforma FlorenceEGI',
    
    'packages' => [
        'starter' => [
            'name' => 'Pacote Iniciante',
            'description' => 'Pacote de acesso aos serviços de IA — nível básico. Ideal para usuários individuais ou primeira abordagem.',
            'description_short' => 'Nível básico para começar',
            'benefits' => [
                'Egili creditados instantaneamente',
                'Pagamento seguro Stripe/PayPal',
                'Fatura mensal agregada',
            ],
        ],
        'professional' => [
            'name' => 'Pacote Profissional',
            'description' => 'Pacote profissional para uso intensivo de serviços de IA.',
            'description_short' => 'Para uso regular e profissional',
            'benefits' => [
                'Egili creditados instantaneamente',
                'Pagamento seguro Stripe/PayPal',
                'Fatura mensal agregada',
                'Suporte técnico prioritário',
            ],
        ],
        'business' => [
            'name' => 'Pacote Empresarial',
            'description' => 'Pacote empresarial para equipes e agências com uso contínuo.',
            'description_short' => 'Para equipes e organizações',
            'benefits' => [
                'Egili creditados instantaneamente',
                'Pagamento seguro Stripe/PayPal',
                'Fatura mensal agregada',
                'Gerente de conta dedicado',
                'Acesso prioritário à API',
            ],
        ],
        'enterprise' => [
            'name' => 'Pacote Enterprise',
            'description' => 'Pacote enterprise para grandes organizações e Administrações Públicas.',
            'description_short' => 'Para grandes organizações e AP',
            'benefits' => [
                'Egili creditados instantaneamente',
                'Pagamento seguro Stripe/PayPal',
                'Fatura mensal agregada',
                'Gerente de conta dedicado',
                'Suporte prioritário 24/7',
                'SLA garantido',
                'API ilimitado',
            ],
        ],
    ],
    
    'columns' => [
        'feature' => 'Funcionalidade',
        'category' => 'Categoria',
        'egili' => 'Egili',
        'eur' => 'EUR',
        'accessi' => 'Acessos',
        'acquisiti' => 'Adquiridos',
        'azioni' => 'Ações',
    ],
    
    'categories' => [
        'ai_services' => 'Serviços de IA',
        'platform_services' => 'Serviços de Plataforma',
        'premium_visibility' => 'Visibilidade Premium',
        'governance' => 'Governança',
    ],
];
