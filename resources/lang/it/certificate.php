<?php

return [
    // Titoli e descrizioni pagine
    'page_title' => 'Certificato di Prenotazione #:uuid',
    'blockchain_certificate_title' => 'Certificato di Acquisto Blockchain',
    'meta_description' => 'Certificato di Prenotazione :type per EGI - FlorenceEGI',
    'verify_page_title' => 'Verifica Certificato #:uuid',
    'verify_meta_description' => 'Verifica l\'autenticità del certificato di prenotazione EGI #:uuid su FlorenceEGI',
    'list_by_egi_title' => 'Certificati per EGI #:egi_id',
    'list_by_egi_meta_description' => 'Visualizza tutti i certificati di prenotazione per EGI #:egi_id su FlorenceEGI',
    'user_certificates_title' => 'I tuoi Certificati di Prenotazione',
    'user_certificates_meta_description' => 'Visualizza tutti i tuoi certificati di prenotazione EGI su FlorenceEGI',

    // Certificate types
    'type' => [
        'blockchain_purchase' => 'Acquisto Blockchain',
        'reservation' => 'Prenotazione',
    ],

    // Messaggi errore
    'not_found' => 'Il certificato richiesto non è stato trovato.',
    'download_failed' => 'Impossibile scaricare il PDF del certificato. Riprova più tardi.',
    'verification_failed' => 'Impossibile verificare il certificato. Potrebbe essere non valido o non più esistente.',
    'list_failed' => 'Impossibile recuperare l\'elenco dei certificati.',
    'auth_required' => 'Accedi per visualizzare i tuoi certificati.',
    'pdf_not_found' => 'Il file PDF del certificato non è stato trovato sul server.',
    'check_failed' => 'Impossibile verificare la disponibilità del PDF. Riprova più tardi.',
    'unauthorized_access' => 'Non sei autorizzato ad accedere a questo certificato.',
    'egi_not_minted' => 'L\'EGI non è stato ancora mintato sulla blockchain.',
    'generation_failed' => 'Impossibile generare il certificato. Riprova più tardi.',

    // Dettagli
    'details' => [
        'title' => 'Dettagli del Certificato',
        'egi_title' => 'Titolo EGI',
        'reservation_type' => 'Tipo di Prenotazione',
        'wallet_address' => 'Indirizzo Wallet',
        'offer_amount_fiat' => 'Importo Offerta (EUR)',
        'purchase_amount' => 'Importo Pagato (EUR)',
        'offer_amount_algo' => 'Importo Offerta (ALGO)',
        'certificate_uuid' => 'UUID Certificato',
        'signature_hash' => 'Hash della Firma',
        'created_at' => 'Creato il',
        'status' => 'Stato',
        'priority' => 'Priorità',
        'certificate_type' => 'Tipo Certificato',
        'buyer' => 'Acquirente',
        'amount_paid' => 'Importo Pagato',
        'minted_at' => 'Mintato il',
    ],

    // Certificate types
    'types' => [
        'reservation' => 'Certificato di Prenotazione',
        'blockchain' => 'Certificato Blockchain',
    ],

    // Ownership types (for mint certificates)
    'ownership' => [
        'user_wallet' => 'Wallet Utente',
        'treasury' => 'Custodia Treasury',
    ],

    // Azioni
    'actions' => [
        'download_pdf' => 'Scarica PDF',
        'verify' => 'Verifica Certificato',
        'view_egi' => 'Visualizza EGI',
        'back_to_list' => 'Torna ai Certificati',
        'share' => 'Condividi Certificato'
    ],

    // Verifica
    'verification' => [
        'title' => 'Risultato della Verifica del Certificato',
        'valid' => 'Questo certificato è valido e autentico.',
        'invalid' => 'Questo certificato sembra non valido o è stato alterato.',
        'highest_priority' => 'Questo certificato rappresenta la prenotazione con la massima priorità per questo EGI.',
        'not_highest_priority' => 'Questo certificato è stato superato da una prenotazione con priorità più alta.',
        'egi_available' => 'L\'EGI per questa prenotazione è ancora disponibile.',
        'egi_not_available' => 'L\'EGI per questa prenotazione è stato coniato o non è più disponibile.',
        'what_this_means' => 'Cosa Significa',
        'explanation_valid' => 'Questo certificato è stato emesso da FlorenceEGI e non è stato modificato.',
        'explanation_invalid' => 'I dati del certificato non corrispondono alla firma. Potrebbe essere stato modificato.',
        'explanation_priority' => 'È stata effettuata una prenotazione con priorità più alta (tipo forte o importo maggiore) dopo questa.',
        'explanation_not_available' => 'L\'EGI è stato coniato o non è più disponibile per la prenotazione.',
        'blockchain_confirmed' => 'Certificato Blockchain Confermato',
        'minted_on' => 'Mintato il',
        'share_text' => 'Scansiona per verificare il certificato'
    ],

    // Altro
    'unknown_egi' => 'EGI Sconosciuto',
    'unknown_buyer' => 'Acquirente Sconosciuto',
    'no_certificates' => 'Nessun certificato trovato.',
    'success_message' => 'Prenotazione completata con successo! Ecco il tuo certificato.',
    'created_just_now' => 'Creato ora',
    'qr_code_alt' => 'Codice QR per la verifica del certificato',
    'view_certificate' => 'Vedi Certificato',
    'view_on_explorer' => 'Vedi su Explorer',

    // Mint/Rebind History
    'mint_history' => [
        'title' => 'Storico Acquisti Blockchain',
        'section_title' => 'Acquisti e Mercato Secondario',
        'entries' => '{1} acquisto|[2,*] acquisti',
        'no_entries' => 'Nessun Acquisto Blockchain',
        'no_purchases_yet' => 'Questo EGI non è ancora stato acquistato tramite blockchain.',
    ],

    // Badge labels
    'badge' => [
        'minted' => 'Mintato',
        'highest' => 'Priorità Massima',
        'superseded' => 'Superato',
    ],

    // Ownership type (used in mint history component)
    'ownership_type' => 'Tipo di Proprietà',

    // Payment distribution roles (aligned with PlatformRole enum + UserTypeEnum for backward compat)
    'roles' => [
        // Platform Roles (from wallets table - SOURCE OF TRUTH)
        'epp' => 'Progetto Ambientale (EPP)',
        'natan' => 'Natan Platform',
        'frangette' => 'Frangette',
        'creator' => 'Creatore',
        'collector' => 'Collezionista',
        'commissioner' => 'Committente',
        'company' => 'Azienda',
        'trader_pro' => 'Trader Professionale',
        'vip' => 'VIP',
        'weak' => 'Utente Base',
        'pa_entity' => 'Ente Pubblico',
        'inspector' => 'Ispettore',

        // Backward compatibility (old user_type enum)
        'trader-pro' => 'Trader Professionale',

        // Unknown fallback
        'unknown' => 'Ruolo Sconosciuto',

        // Legacy roles (deprecated but kept for backward compatibility)
        'co_creator' => 'Co-Creatore',
        'collection_owner' => 'Proprietario Collezione',
        'platform' => 'Piattaforma',
        'royalty' => 'Royalty',
        'reseller' => 'Rivenditore',
    ],

    // Post-mint messages
    'unauthorized_access' => 'Non sei autorizzato ad accedere a questo certificato.',
    'egi_not_minted' => 'L\'EGI non è ancora stato mintato.',
    'generation_failed' => 'Generazione del certificato non riuscita. Riprova più tardi.',
    'unknown_recipient' => 'Destinatario Sconosciuto',
];
