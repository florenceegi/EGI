<?php

return [
    // Modal Header
    'title' => 'Benvenuto su FlorenceEGI!',
    'subtitle' => 'Il tuo Wallet Digitale è pronto',
    
    // Intro
    'intro' => 'Durante la registrazione, abbiamo generato automaticamente un <strong>wallet digitale Algorand</strong> associato al tuo account. Questo wallet è necessario per ricevere i tuoi <strong>Certificati Digitali di Autenticità (EGI)</strong> quando acquisti opere d\'arte sulla piattaforma.',
    
    // Section 1: Sicurezza
    'security_title' => '🔒 Sicurezza e Privacy GDPR',
    'security_items' => [
        'Il tuo wallet è protetto con <strong>crittografia XChaCha20-Poly1305</strong>',
        'Le chiavi private sono cifrate utilizzando <strong>AWS Key Management Service (KMS)</strong> con envelope encryption (DEK + KEK)',
        'Archiviazione sicura nel database conforme GDPR',
        'Puoi <strong>richiedere in qualsiasi momento</strong> le credenziali del tuo wallet (frase segreta di 25 parole)',
        'Puoi importare il wallet in <strong>Pera Wallet</strong> o altri client Algorand compatibili',
        'Puoi <strong>richiedere la cancellazione definitiva</strong> del wallet dai nostri sistemi',
    ],
    'security_note' => '<strong>Nota:</strong> Una volta esportata la frase segreta e cancellato il wallet dai nostri server, la gestione diventa completamente <strong>non-custodial</strong> e sarà tua esclusiva responsabilità.',
    
    // Section 2: Contenuto
    'content_title' => '💎 Cosa contiene il tuo wallet',
    'content_has_title' => '✅ Contiene:',
    'content_has' => [
        'I tuoi <strong>Certificati EGI</strong> (NFT unici delle opere)',
        'Metadata delle opere certificate',
        'Storico di autenticità on-chain',
    ],
    'content_not_has_title' => '❌ NON contiene:',
    'content_not_has' => [
        'ALGO (criptovaluta Algorand)',
        'Stablecoin o altri token fungibili',
        'Fondi o asset finanziari',
    ],
    'content_note' => 'Il wallet è dedicato <strong>esclusivamente</strong> ai certificati digitali. Non può essere utilizzato per operazioni finanziarie.',
    
    // Section 3: Pagamenti
    'payments_title' => '💶 Pagamenti e Ricevute FIAT',
    'payments_how_title' => 'Come funzionano i pagamenti:',
    'payments_how' => [
        'Tutti i tuoi acquisti avvengono in <strong>euro (€)</strong> tramite carta di credito, bonifico o altri metodi tradizionali',
        'Il wallet serve <strong>solo</strong> per ricevere il certificato digitale dell\'opera, non per gestire pagamenti',
        'Le transazioni di pagamento sono gestite dal nostro PSP (Payment Service Provider) certificato',
    ],
    'payments_iban_title' => '💳 Vuoi ricevere pagamenti in FIAT?',
    'payments_iban_intro' => 'Se sei un <strong>Creator</strong> e desideri ricevere i proventi delle tue vendite direttamente sul tuo conto bancario, puoi aggiungere il tuo <strong>IBAN</strong> nelle impostazioni del profilo.',
    'payments_iban_security_title' => 'Il tuo IBAN sarà:',
    'payments_iban_security' => [
        'Crittografato con standard di sicurezza bancaria (AES-256)',
        'Protetto con hash SHA-256 + pepper per unicità',
        'Utilizzato solo per i pagamenti verso di te',
        'Gestito nel pieno rispetto del GDPR',
        'Memorizzati solo gli ultimi 4 caratteri per UI',
    ],
    
    // Section 4: Conformità
    'compliance_title' => '🔐 Conformità Normativa (MiCA-safe)',
    'compliance_intro' => 'Questa modalità costituisce <strong>"custodia tecnica limitata di asset digitali non finanziari"</strong> e:',
    'compliance_items' => [
        '<strong>Non configura attività di CASP</strong> (Crypto-Asset Service Provider)',
        'Opera <strong>fuori dal perimetro MiCA</strong> (Markets in Crypto-Assets Regulation)',
        'È soggetta esclusivamente agli obblighi GDPR per la protezione dei dati personali',
    ],
    'compliance_platform_title' => 'FlorenceEGI:',
    'compliance_platform' => [
        '✅ Emette certificati digitali (NFT unici)',
        '✅ Fornisce custodia tecnica temporanea delle chiavi',
        '❌ NON esegue operazioni di cambio',
        '❌ NON custodisce fondi o criptovalute',
        '❌ NON intermedia transazioni finanziarie',
    ],
    
    // Section 5: Opzioni
    'options_title' => '📱 Cosa puoi fare',
    'option1_title' => '✨ Opzione 1 - Gestione Automatica',
    'option1_subtitle' => '(Consigliata per principianti)',
    'option1_items' => [
        'Il wallet rimane "invisibile" e gestito automaticamente',
        'Ricevi i tuoi certificati senza preoccuparti della blockchain',
        'Ideale se non hai familiarità con le criptovalute',
        'Massima semplicità d\'uso',
    ],
    'option2_title' => '🔓 Opzione 2 - Controllo Totale',
    'option2_subtitle' => '(Per utenti esperti)',
    'option2_items' => [
        'Scarica la frase segreta (25 parole) dalle <strong>Impostazioni → Sicurezza</strong>',
        'Importala in Pera Wallet o altro client Algorand',
        'Gestisci i tuoi certificati in autonomia',
        'Richiedi la cancellazione del wallet dai nostri server',
    ],
    
    // Section 6: Glossario
    'glossary_title' => '📖 Glossario Termini Tecnici',
    'glossary' => [
        'wallet_algorand' => [
            'term' => 'Wallet Algorand:',
            'definition' => 'Portafoglio digitale sulla blockchain Algorand. Contiene i tuoi certificati EGI (NFT unici).',
        ],
        'egi' => [
            'term' => 'EGI (Certificato Digitale):',
            'definition' => 'NFT unico che certifica l\'autenticità di un\'opera d\'arte. Contiene metadata immutabili e tracciabili.',
        ],
        'envelope_encryption' => [
            'term' => 'Envelope Encryption (DEK+KEK):',
            'definition' => 'Sistema di crittografia a doppio livello. Una chiave (DEK) cifra i dati, una seconda chiave (KEK) cifra la prima. AWS KMS gestisce la KEK.',
        ],
        'seed_phrase' => [
            'term' => 'Frase Segreta (Seed Phrase):',
            'definition' => 'Sequenza di 25 parole che permette di recuperare l\'accesso al wallet. <strong>Mai condividerla con nessuno!</strong>',
        ],
        'non_custodial' => [
            'term' => 'Non-Custodial Wallet:',
            'definition' => 'Wallet di cui solo tu possiedi le chiavi private. La piattaforma non può accedere ai tuoi asset.',
        ],
        'gdpr' => [
            'term' => 'GDPR:',
            'definition' => 'Regolamento Generale sulla Protezione dei Dati. Garantisce i tuoi diritti di privacy e sicurezza dei dati personali in UE.',
        ],
        'mica' => [
            'term' => 'MiCA (Markets in Crypto-Assets):',
            'definition' => 'Regolamento UE sui mercati delle cripto-attività. FlorenceEGI opera fuori dal perimetro MiCA perché non gestisce asset finanziari.',
        ],
        'casp' => [
            'term' => 'CASP:',
            'definition' => 'Crypto-Asset Service Provider. Soggetto che offre servizi di cambio, custodia o trasferimento di criptovalute. FlorenceEGI non è un CASP.',
        ],
    ],
    
    // Section 7: Help
    'help_title' => '🆘 Hai domande?',
    'help_whitepaper' => 'White Paper',
    'help_whitepaper_desc' => 'Guida completa',
    'help_support' => 'Supporto',
    'help_support_desc' => 'Assistenza 24/7',
    'help_faq' => 'FAQ',
    'help_faq_desc' => 'Risposte rapide',
    
    // Footer
    'dont_show_again' => 'Non mostrare più questo messaggio',
    'btn_add_iban' => 'Aggiungi IBAN',
    'btn_continue' => 'Ho capito, procedi',
];

