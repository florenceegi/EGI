<?php

return [
    // Modal Header
    'title' => 'Bem-vindo ao FlorenceEGI!',
    'subtitle' => 'Sua Carteira Digital está pronta',
    
    // Intro
    'intro' => 'Durante o registro, geramos automaticamente uma <strong>carteira digital Algorand</strong> associada à sua conta. Esta carteira é necessária para receber seus <strong>Certificados Digitais de Autenticidade (EGI)</strong> quando você comprar obras de arte na plataforma.',
    
    // Section 1: Security
    'security_title' => '🔒 Segurança e Privacidade LGPD/GDPR',
    'security_items' => [
        'Sua carteira está protegida com <strong>criptografia XChaCha20-Poly1305</strong>',
        'Chaves privadas são criptografadas usando <strong>AWS Key Management Service (KMS)</strong> com envelope encryption (DEK + KEK)',
        'Armazenamento seguro em banco de dados conforme LGPD/GDPR',
        'Você pode <strong>solicitar a qualquer momento</strong> as credenciais da sua carteira (frase secreta de 25 palavras)',
        'Você pode importar a carteira para <strong>Pera Wallet</strong> ou outros clientes compatíveis com Algorand',
        'Você pode <strong>solicitar a exclusão definitiva</strong> da carteira de nossos sistemas',
    ],
    'security_note' => '<strong>Nota:</strong> Uma vez exportada a frase secreta e excluída a carteira de nossos servidores, o gerenciamento se torna completamente <strong>não custodial</strong> e será sua exclusiva responsabilidade.',
    
    // Section 2: Content
    'content_title' => '💎 O que sua carteira contém',
    'content_has_title' => '✅ Contém:',
    'content_has' => [
        'Seus <strong>Certificados EGI</strong> (NFTs únicos de obras)',
        'Metadados de obras certificadas',
        'Histórico de autenticidade on-chain',
    ],
    'content_not_has_title' => '❌ NÃO contém:',
    'content_not_has' => [
        'ALGO (criptomoeda Algorand)',
        'Stablecoins ou outros tokens fungíveis',
        'Fundos ou ativos financeiros',
    ],
    'content_note' => 'A carteira é dedicada <strong>exclusivamente</strong> a certificados digitais. Não pode ser usada para operações financeiras.',
    
    // Section 3: Payments
    'payments_title' => '💶 Pagamentos e Recibos FIAT',
    'payments_how_title' => 'Como funcionam os pagamentos:',
    'payments_how' => [
        'Todas as suas compras são feitas em <strong>euros (€)</strong> via cartão de crédito, transferência bancária ou outros métodos tradicionais',
        'A carteira serve <strong>apenas</strong> para receber o certificado digital da obra, não para gerenciar pagamentos',
        'Transações de pagamento são processadas por nosso PSP (Provedor de Serviços de Pagamento) certificado',
    ],
    'payments_iban_title' => '💳 Quer receber pagamentos em FIAT?',
    'payments_iban_intro' => 'Se você é um <strong>Criador</strong> e deseja receber os rendimentos de suas vendas diretamente em sua conta bancária, pode adicionar seu <strong>IBAN</strong> nas configurações do perfil.',
    'payments_iban_security_title' => 'Seu IBAN será:',
    'payments_iban_security' => [
        'Criptografado com padrões de segurança bancária (AES-256)',
        'Protegido com hash SHA-256 + pepper para unicidade',
        'Usado apenas para pagamentos para você',
        'Gerenciado em total conformidade com LGPD/GDPR',
        'Apenas os últimos 4 caracteres armazenados para UI',
    ],
    
    // Section 4: Compliance
    'compliance_title' => '🔐 Conformidade Regulatória (MiCA-safe)',
    'compliance_intro' => 'Esta modalidade constitui <strong>"custódia técnica limitada de ativos digitais não financeiros"</strong> e:',
    'compliance_items' => [
        '<strong>Não constitui atividade CASP</strong> (Provedor de Serviços de Cripto-ativos)',
        'Opera <strong>fora do perímetro MiCA</strong> (Regulamento de Mercados de Cripto-ativos)',
        'Está sujeita exclusivamente às obrigações LGPD/GDPR para proteção de dados pessoais',
    ],
    'compliance_platform_title' => 'FlorenceEGI:',
    'compliance_platform' => [
        '✅ Emite certificados digitais (NFTs únicos)',
        '✅ Fornece custódia técnica temporária de chaves',
        '❌ NÃO realiza operações de câmbio',
        '❌ NÃO custodia fundos ou criptomoedas',
        '❌ NÃO intermedeia transações financeiras',
    ],
    
    // Section 5: Options
    'options_title' => '📱 O que você pode fazer',
    'option1_title' => '✨ Opção 1 - Gerenciamento Automático',
    'option1_subtitle' => '(Recomendado para iniciantes)',
    'option1_items' => [
        'Carteira permanece "invisível" e gerenciada automaticamente',
        'Receba seus certificados sem se preocupar com blockchain',
        'Ideal se não estiver familiarizado com criptomoedas',
        'Máxima simplicidade de uso',
    ],
    'option2_title' => '🔓 Opção 2 - Controle Total',
    'option2_subtitle' => '(Para usuários experientes)',
    'option2_items' => [
        'Baixe a frase secreta (25 palavras) de <strong>Configurações → Segurança</strong>',
        'Importe-a para Pera Wallet ou outro cliente Algorand',
        'Gerencie seus certificados independentemente',
        'Solicite exclusão da carteira de nossos servidores',
    ],
    
    // Section 6: Glossary
    'glossary_title' => '📖 Glossário de Termos Técnicos',
    'glossary' => [
        'wallet_algorand' => [
            'term' => 'Carteira Algorand:',
            'definition' => 'Carteira digital na blockchain Algorand. Contém seus certificados EGI (NFTs únicos).',
        ],
        'egi' => [
            'term' => 'EGI (Certificado Digital):',
            'definition' => 'NFT único que certifica autenticidade de obra de arte. Contém metadados imutáveis e rastreáveis.',
        ],
        'envelope_encryption' => [
            'term' => 'Envelope Encryption (DEK+KEK):',
            'definition' => 'Sistema de criptografia de dois níveis. Uma chave (DEK) criptografa dados, uma segunda chave (KEK) criptografa a primeira. AWS KMS gerencia a KEK.',
        ],
        'seed_phrase' => [
            'term' => 'Frase Secreta (Seed Phrase):',
            'definition' => 'Sequência de 25 palavras que permite recuperar acesso à carteira. <strong>Nunca compartilhar com ninguém!</strong>',
        ],
        'non_custodial' => [
            'term' => 'Carteira Não Custodial:',
            'definition' => 'Carteira onde apenas você possui as chaves privadas. A plataforma não pode acessar seus ativos.',
        ],
        'gdpr' => [
            'term' => 'LGPD/GDPR:',
            'definition' => 'Lei Geral de Proteção de Dados / Regulamento Geral sobre Proteção de Dados. Garante seus direitos de privacidade e segurança de dados pessoais.',
        ],
        'mica' => [
            'term' => 'MiCA (Mercados de Cripto-ativos):',
            'definition' => 'Regulamento UE sobre mercados de cripto-ativos. FlorenceEGI opera fora do perímetro MiCA porque não gerencia ativos financeiros.',
        ],
        'casp' => [
            'term' => 'CASP:',
            'definition' => 'Provedor de Serviços de Cripto-ativos. Entidade que oferece serviços de câmbio, custódia ou transferência de criptomoedas. FlorenceEGI não é CASP.',
        ],
    ],
    
    // Section 7: Help
    'help_title' => '🆘 Tem dúvidas?',
    'help_whitepaper' => 'White Paper',
    'help_whitepaper_desc' => 'Guia completo',
    'help_support' => 'Suporte',
    'help_support_desc' => 'Assistência 24/7',
    'help_faq' => 'FAQ',
    'help_faq_desc' => 'Respostas rápidas',
    
    // Footer
    'dont_show_again' => 'Não mostrar esta mensagem novamente',
    'btn_add_iban' => 'Adicionar IBAN',
    'btn_continue' => 'Entendi, continuar',
];

