<?php

return [
    'page' => [
        'title' => 'Riepilogo onboarding creator',
        'description' => 'Rivedi lo stato dei pagamenti, del wallet Algorand e della compliance FlorenceEGI.',
        'heading' => 'Benvenuto nel centro di controllo creator',
        'intro' => 'Grazie per aver completato i primi step. In questa pagina trovi il riepilogo operativo su pagamenti, custodia Algorand e prossimi passi.',
    ],
    'profile' => [
        'title' => 'Profilo e wallet',
        'user_name' => 'Nome completo',
        'user_email' => 'Email',
        'user_type' => 'Ruolo utente',
        'wallet_address' => 'Wallet Algorand',
        'iban_masked' => 'IBAN registrato',
        'iban_missing' => 'IBAN non configurato. Aggiungilo per ricevere i payout.',
    ],
    'stripe' => [
        'title' => 'Account Stripe Connect',
        'account_id' => 'ID account',
        'status' => 'Stato generale',
        'charges_enabled' => 'Pagamenti con carta abilitati',
        'payouts_enabled' => 'Payout abilitati',
        'details_submitted' => 'Verifica completata',
        'status_ready' => 'Pronto per incassare pagamenti e ricevere payout',
        'status_pending' => 'Sono richieste azioni aggiuntive prima dell’attivazione',
        'cta_onboarding' => 'Completa onboarding Stripe',
        'cta_dashboard' => 'Apri dashboard Stripe Express',
        'onboarding_hint' => 'Stripe potrebbe richiedere documenti fiscali o d’identità prima di attivare i payout.',
    ],
    'actions' => [
        'title' => 'Passi successivi',
        'checklist' => [
            'onboarding' => 'Completa il flusso di onboarding Stripe se ancora aperto.',
            'documents' => 'Prepara documenti fiscali e di identità per la verifica KYC.',
            'pricing' => 'Configura prezzi e opzioni Egili per i tuoi EGI.',
            'compliance' => 'Rivedi le linee guida MiCA-safe e PA per il tuo ecosistema.',
        ],
    ],
    'pera' => [
        'title' => 'Wallet Pera Algorand & custodia',
        'intro' => 'Il wallet Algorand viene custodito da FlorenceEGI finché non richiedi il trasferimento certificato.',
        'request' => 'Per ricevere la frase segreta Pera apri un ticket dal Support Center e pianifica una sessione di verifica identità con il nostro team.',
        'note' => 'Fino al completamento del trasferimento FlorenceEGI firma le transazioni on-chain per tuo conto mentre i proventi fiat vanno direttamente al tuo account Stripe.',
    ],
    'badges' => [
        'ready' => 'Pronto',
        'pending' => 'Azione richiesta',
        'missing' => 'Configurazione necessaria',
    ],
    'buttons' => [
        'refresh' => 'Aggiorna stato',
        'support' => 'Contatta il supporto FlorenceEGI',
    ],
];

