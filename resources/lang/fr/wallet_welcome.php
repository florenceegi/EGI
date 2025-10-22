<?php

return [
    // Modal Header
    'title' => 'Bienvenue sur FlorenceEGI !',
    'subtitle' => 'Votre Portefeuille Numérique est prêt',
    
    // Intro
    'intro' => 'Lors de l\'inscription, nous avons généré automatiquement un <strong>portefeuille numérique Algorand</strong> associé à votre compte. Ce portefeuille est nécessaire pour recevoir vos <strong>Certificats Numériques d\'Authenticité (EGI)</strong> lorsque vous achetez des œuvres d\'art sur la plateforme.',
    
    // Section 1: Security
    'security_title' => '🔒 Sécurité et Confidentialité RGPD',
    'security_items' => [
        'Votre portefeuille est protégé avec <strong>chiffrement XChaCha20-Poly1305</strong>',
        'Les clés privées sont chiffrées via <strong>AWS Key Management Service (KMS)</strong> avec chiffrement d\'enveloppe (DEK + KEK)',
        'Stockage sécurisé dans base de données conforme RGPD',
        'Vous pouvez <strong>demander à tout moment</strong> vos identifiants de portefeuille (phrase secrète de 25 mots)',
        'Vous pouvez importer le portefeuille dans <strong>Pera Wallet</strong> ou autres clients compatibles Algorand',
        'Vous pouvez <strong>demander la suppression définitive</strong> du portefeuille de nos systèmes',
    ],
    'security_note' => '<strong>Note :</strong> Une fois la phrase secrète exportée et le portefeuille supprimé de nos serveurs, la gestion devient complètement <strong>non-custodiale</strong> et sera votre seule responsabilité.',
    
    // Section 2: Content
    'content_title' => '💎 Contenu de votre portefeuille',
    'content_has_title' => '✅ Contient :',
    'content_has' => [
        'Vos <strong>Certificats EGI</strong> (NFT uniques d\'œuvres)',
        'Métadonnées d\'œuvres certifiées',
        'Historique d\'authenticité on-chain',
    ],
    'content_not_has_title' => '❌ Ne contient PAS :',
    'content_not_has' => [
        'ALGO (cryptomonnaie Algorand)',
        'Stablecoins ou autres jetons fongibles',
        'Fonds ou actifs financiers',
    ],
    'content_note' => 'Le portefeuille est dédié <strong>exclusivement</strong> aux certificats numériques. Il ne peut pas être utilisé pour des opérations financières.',
    
    // Section 3: Payments
    'payments_title' => '💶 Paiements et Reçus FIAT',
    'payments_how_title' => 'Comment fonctionnent les paiements :',
    'payments_how' => [
        'Tous vos achats sont effectués en <strong>euros (€)</strong> par carte bancaire, virement ou autres méthodes traditionnelles',
        'Le portefeuille sert <strong>uniquement</strong> à recevoir le certificat numérique de l\'œuvre, pas à gérer les paiements',
        'Les transactions de paiement sont gérées par notre PSP (Prestataire de Services de Paiement) certifié',
    ],
    'payments_iban_title' => '💳 Vous souhaitez recevoir des paiements en FIAT ?',
    'payments_iban_intro' => 'Si vous êtes un <strong>Créateur</strong> et souhaitez recevoir les revenus de vos ventes directement sur votre compte bancaire, vous pouvez ajouter votre <strong>IBAN</strong> dans les paramètres du profil.',
    'payments_iban_security_title' => 'Votre IBAN sera :',
    'payments_iban_security' => [
        'Chiffré avec normes de sécurité bancaire (AES-256)',
        'Protégé avec hash SHA-256 + pepper pour unicité',
        'Utilisé uniquement pour paiements vers vous',
        'Géré dans le respect complet du RGPD',
        'Seuls les 4 derniers caractères stockés pour UI',
    ],
    
    // Section 4: Compliance
    'compliance_title' => '🔐 Conformité Réglementaire (MiCA-safe)',
    'compliance_intro' => 'Cette modalité constitue une <strong>"garde technique limitée d\'actifs numériques non financiers"</strong> et :',
    'compliance_items' => [
        '<strong>Ne constitue pas une activité CASP</strong> (Prestataire de Services sur Crypto-actifs)',
        'Opère <strong>hors du périmètre MiCA</strong> (Règlement sur les Marchés de Crypto-actifs)',
        'Est soumise exclusivement aux obligations RGPD pour protection des données personnelles',
    ],
    'compliance_platform_title' => 'FlorenceEGI :',
    'compliance_platform' => [
        '✅ Émet des certificats numériques (NFT uniques)',
        '✅ Fournit garde technique temporaire des clés',
        '❌ N\'effectue PAS d\'opérations de change',
        '❌ Ne garde PAS de fonds ou cryptomonnaies',
        '❌ N\'intermédie PAS de transactions financières',
    ],
    
    // Section 5: Options
    'options_title' => '📱 Ce que vous pouvez faire',
    'option1_title' => '✨ Option 1 - Gestion Automatique',
    'option1_subtitle' => '(Recommandée pour débutants)',
    'option1_items' => [
        'Le portefeuille reste "invisible" et géré automatiquement',
        'Recevez vos certificats sans vous soucier de la blockchain',
        'Idéal si peu familier avec cryptomonnaies',
        'Simplicité maximale d\'utilisation',
    ],
    'option2_title' => '🔓 Option 2 - Contrôle Total',
    'option2_subtitle' => '(Pour utilisateurs experts)',
    'option2_items' => [
        'Téléchargez la phrase secrète (25 mots) depuis <strong>Paramètres → Sécurité</strong>',
        'Importez-la dans Pera Wallet ou autre client Algorand',
        'Gérez vos certificats de manière indépendante',
        'Demandez suppression du portefeuille de nos serveurs',
    ],
    
    // Section 6: Glossary
    'glossary_title' => '📖 Glossaire des Termes Techniques',
    'glossary' => [
        'wallet_algorand' => [
            'term' => 'Portefeuille Algorand :',
            'definition' => 'Portefeuille numérique sur blockchain Algorand. Contient vos certificats EGI (NFT uniques).',
        ],
        'egi' => [
            'term' => 'EGI (Certificat Numérique) :',
            'definition' => 'NFT unique certifiant authenticité d\'une œuvre d\'art. Contient métadonnées immuables et traçables.',
        ],
        'envelope_encryption' => [
            'term' => 'Chiffrement d\'Enveloppe (DEK+KEK) :',
            'definition' => 'Système de chiffrement à double niveau. Une clé (DEK) chiffre données, une seconde clé (KEK) chiffre la première. AWS KMS gère la KEK.',
        ],
        'seed_phrase' => [
            'term' => 'Phrase Secrète (Seed Phrase) :',
            'definition' => 'Séquence de 25 mots permettant de récupérer l\'accès au portefeuille. <strong>Ne jamais partager avec personne !</strong>',
        ],
        'non_custodial' => [
            'term' => 'Portefeuille Non-Custodial :',
            'definition' => 'Portefeuille dont vous seul possédez les clés privées. La plateforme ne peut accéder à vos actifs.',
        ],
        'gdpr' => [
            'term' => 'RGPD :',
            'definition' => 'Règlement Général sur la Protection des Données. Garantit vos droits de confidentialité et sécurité des données personnelles en UE.',
        ],
        'mica' => [
            'term' => 'MiCA (Marchés de Crypto-actifs) :',
            'definition' => 'Règlement UE sur marchés des crypto-actifs. FlorenceEGI opère hors périmètre MiCA car ne gère pas actifs financiers.',
        ],
        'casp' => [
            'term' => 'CASP :',
            'definition' => 'Prestataire de Services sur Crypto-actifs. Entité offrant services d\'échange, garde ou transfert de cryptomonnaies. FlorenceEGI n\'est pas CASP.',
        ],
    ],
    
    // Section 7: Help
    'help_title' => '🆘 Des questions ?',
    'help_whitepaper' => 'White Paper',
    'help_whitepaper_desc' => 'Guide complet',
    'help_support' => 'Support',
    'help_support_desc' => 'Assistance 24/7',
    'help_faq' => 'FAQ',
    'help_faq_desc' => 'Réponses rapides',
    
    // Footer
    'dont_show_again' => 'Ne plus afficher ce message',
    'btn_add_iban' => 'Ajouter IBAN',
    'btn_continue' => 'J\'ai compris, continuer',
];

