<?php

return [
    // Modal Header
    'title' => 'Welcome to FlorenceEGI!',
    'subtitle' => 'Your Digital Wallet is ready',
    
    // Intro
    'intro' => 'During registration, we automatically generated an <strong>Algorand digital wallet</strong> associated with your account. This wallet is necessary to receive your <strong>Digital Certificates of Authenticity (EGI)</strong> when you purchase artworks on the platform.',
    
    // Section 1: Security
    'security_title' => '🔒 Security and GDPR Privacy',
    'security_items' => [
        'Your wallet is protected with <strong>XChaCha20-Poly1305 encryption</strong>',
        'Private keys are encrypted using <strong>AWS Key Management Service (KMS)</strong> with envelope encryption (DEK + KEK)',
        'Secure storage in GDPR-compliant database',
        'You can <strong>request at any time</strong> your wallet credentials (25-word secret phrase)',
        'You can import the wallet into <strong>Pera Wallet</strong> or other Algorand-compatible clients',
        'You can <strong>request permanent deletion</strong> of the wallet from our systems',
    ],
    'security_note' => '<strong>Note:</strong> Once you export the secret phrase and delete the wallet from our servers, management becomes completely <strong>non-custodial</strong> and will be your sole responsibility.',
    
    // Section 2: Content
    'content_title' => '💎 What your wallet contains',
    'content_has_title' => '✅ Contains:',
    'content_has' => [
        'Your <strong>EGI Certificates</strong> (unique artwork NFTs)',
        'Certified artwork metadata',
        'On-chain authenticity history',
    ],
    'content_not_has_title' => '❌ Does NOT contain:',
    'content_not_has' => [
        'ALGO (Algorand cryptocurrency)',
        'Stablecoins or other fungible tokens',
        'Funds or financial assets',
    ],
    'content_note' => 'The wallet is dedicated <strong>exclusively</strong> to digital certificates. It cannot be used for financial operations.',
    
    // Section 3: Payments
    'payments_title' => '💶 Payments and FIAT Receipts',
    'payments_how_title' => 'How payments work:',
    'payments_how' => [
        'All your purchases are made in <strong>euros (€)</strong> via credit card, bank transfer, or other traditional methods',
        'The wallet is used <strong>only</strong> to receive the digital certificate of the artwork, not to manage payments',
        'Payment transactions are handled by our certified PSP (Payment Service Provider)',
    ],
    'payments_iban_title' => '💳 Want to receive FIAT payments?',
    'payments_iban_intro' => 'If you are a <strong>Creator</strong> and wish to receive proceeds from your sales directly to your bank account, you can add your <strong>IBAN</strong> in profile settings.',
    'payments_iban_security_title' => 'Your IBAN will be:',
    'payments_iban_security' => [
        'Encrypted with banking security standards (AES-256)',
        'Protected with SHA-256 hash + pepper for uniqueness',
        'Used only for payments to you',
        'Managed in full compliance with GDPR',
        'Only last 4 characters stored for UI',
    ],
    
    // Section 4: Compliance
    'compliance_title' => '🔐 Regulatory Compliance (MiCA-safe)',
    'compliance_intro' => 'This mode constitutes <strong>"limited technical custody of non-financial digital assets"</strong> and:',
    'compliance_items' => [
        '<strong>Does not constitute CASP activity</strong> (Crypto-Asset Service Provider)',
        'Operates <strong>outside the MiCA perimeter</strong> (Markets in Crypto-Assets Regulation)',
        'Is subject exclusively to GDPR obligations for personal data protection',
    ],
    'compliance_platform_title' => 'FlorenceEGI:',
    'compliance_platform' => [
        '✅ Issues digital certificates (unique NFTs)',
        '✅ Provides temporary technical custody of keys',
        '❌ Does NOT perform exchange operations',
        '❌ Does NOT custody funds or cryptocurrencies',
        '❌ Does NOT intermediate financial transactions',
    ],
    
    // Section 5: Options
    'options_title' => '📱 What you can do',
    'option1_title' => '✨ Option 1 - Automatic Management',
    'option1_subtitle' => '(Recommended for beginners)',
    'option1_items' => [
        'Wallet remains "invisible" and automatically managed',
        'Receive your certificates without worrying about blockchain',
        'Ideal if unfamiliar with cryptocurrencies',
        'Maximum ease of use',
    ],
    'option2_title' => '🔓 Option 2 - Full Control',
    'option2_subtitle' => '(For expert users)',
    'option2_items' => [
        'Download secret phrase (25 words) from <strong>Settings → Security</strong>',
        'Import it into Pera Wallet or other Algorand client',
        'Manage your certificates independently',
        'Request wallet deletion from our servers',
    ],
    
    // Section 6: Glossary
    'glossary_title' => '📖 Technical Terms Glossary',
    'glossary' => [
        'wallet_algorand' => [
            'term' => 'Algorand Wallet:',
            'definition' => 'Digital portfolio on Algorand blockchain. Contains your EGI certificates (unique NFTs).',
        ],
        'egi' => [
            'term' => 'EGI (Digital Certificate):',
            'definition' => 'Unique NFT certifying artwork authenticity. Contains immutable and traceable metadata.',
        ],
        'envelope_encryption' => [
            'term' => 'Envelope Encryption (DEK+KEK):',
            'definition' => 'Two-level encryption system. One key (DEK) encrypts data, a second key (KEK) encrypts the first. AWS KMS manages the KEK.',
        ],
        'seed_phrase' => [
            'term' => 'Secret Phrase (Seed Phrase):',
            'definition' => '25-word sequence that allows wallet access recovery. <strong>Never share with anyone!</strong>',
        ],
        'non_custodial' => [
            'term' => 'Non-Custodial Wallet:',
            'definition' => 'Wallet where only you own the private keys. The platform cannot access your assets.',
        ],
        'gdpr' => [
            'term' => 'GDPR:',
            'definition' => 'General Data Protection Regulation. Guarantees your privacy rights and personal data security in EU.',
        ],
        'mica' => [
            'term' => 'MiCA (Markets in Crypto-Assets):',
            'definition' => 'EU regulation on crypto-asset markets. FlorenceEGI operates outside MiCA perimeter as it doesn\'t manage financial assets.',
        ],
        'casp' => [
            'term' => 'CASP:',
            'definition' => 'Crypto-Asset Service Provider. Entity offering exchange, custody, or transfer services for cryptocurrencies. FlorenceEGI is not a CASP.',
        ],
    ],
    
    // Section 7: Help
    'help_title' => '🆘 Questions?',
    'help_whitepaper' => 'White Paper',
    'help_whitepaper_desc' => 'Complete guide',
    'help_support' => 'Support',
    'help_support_desc' => '24/7 assistance',
    'help_faq' => 'FAQ',
    'help_faq_desc' => 'Quick answers',
    
    // Footer
    'dont_show_again' => 'Don\'t show this message again',
    'btn_add_iban' => 'Add IBAN',
    'btn_continue' => 'I understand, continue',
];

