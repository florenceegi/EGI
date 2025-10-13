<?php

return [
    // Page Meta
    'page_title' => 'Mint :title - FlorenceEGI',
    'meta_description' => 'Mint del tuo EGI :title sulla blockchain Algorand. Processo sicuro e trasparente.',

    // Header
    'header_title' => 'Mint del tuo EGI',
    'header_description' => 'Completa l\'acquisto e minta il tuo EGI sulla blockchain Algorand. Questo processo è irreversibile.',

    // Buttons
    'mint_button' => 'Mint (:price)',
    'mint_button_processing' => 'Minting in corso...',
    'cancel_button' => 'Annulla',
    'back_button' => 'Torna indietro',

    // EGI Preview Section
    'egi_preview' => [
        'title' => 'Anteprima EGI',
        'creator_by' => 'Creato da :name',
    ],

    // Blockchain Info Section
    'blockchain_info' => [
        'title' => 'Informazioni Blockchain',
        'network' => 'Rete',
        'network_value' => 'Algorand Mainnet',
        'token_type' => 'Tipo Token',
        'token_type_value' => 'ASA (Algorand Standard Asset)',
        'supply' => 'Supply',
        'supply_value' => '1 (Unico)',
        'royalty' => 'Royalty',
        'royalty_value' => ':percentage% al creatore',
    ],

    // Certificate of Authenticity (CoA) Section
    'coa' => [
        'title' => 'Certificato di Autenticità',
        'certified' => 'Certificato Autentico',
        'certificate_number' => 'Numero Certificato',
        'issuer' => 'Emesso da',
        'issue_date' => 'Data Emissione',
        'authenticity_level' => 'Livello Autenticità',
        'info_note' => 'Questo certificato verrà incluso nei metadati NFT e verificato sulla blockchain.',
    ],

    // Payment Section
    'payment' => [
        'title' => 'Dettagli Pagamento',
        'price_label' => 'Prezzo finale',
        'currency' => 'Valuta',
        'payment_method' => 'Metodo di pagamento',
        'payment_method_card' => 'Carta di Credito/Debito',
        'total_label' => 'Totale da pagare',
        'winning_reservation' => 'Prenotazione Vincente',
        'your_offer' => 'La tua offerta',
        'reservation_date' => 'Data prenotazione',
        'payment_method_label' => 'Metodo di pagamento',
        'submit_button' => 'Procedi al Pagamento',
        'reservation_not_found' => 'Prenotazione non trovata',
        'cannot_proceed_no_reservation' => 'Impossibile procedere senza una prenotazione valida.',
        'direct_mint_price' => 'Acquisto Diretto',
        'base_price' => 'Prezzo base',
        'credit_card' => 'Carta di Credito/Debito',
        'paypal' => 'PayPal',
        'wallet_label' => 'Wallet Algorand (Opzionale)',
        'wallet_placeholder' => 'Inserisci il tuo indirizzo wallet Algorand',
        'wallet_help' => 'Se inserisci un wallet, l\'EGI verrà trasferito direttamente lì. Altrimenti sarà custodito nel Treasury della piattaforma.',

        // AREA 5.5.1: Co-Creator Display Name
        'optional' => 'opzionale',
        'co_creator_name_label' => 'Il tuo Nome come Co-Creatore',
        'co_creator_name_help' => 'Questo nome apparirà permanentemente nei metadati dell\'EGI. Se lasciato vuoto, verrà utilizzato il nome del tuo profilo.',
        'co_creator_name_warning' => '⚠️ ATTENZIONE: Questo nome diventerà permanente e immutabile dopo il mint. Verifica attentamente prima di procedere.',
        'co_creator_name_pattern' => 'Solo lettere, numeri, spazi e caratteri . \' - sono ammessi',
        'co_creator_name_invalid' => 'Il nome contiene caratteri non validi. Usa solo lettere, numeri, spazi e . \' -',
    ],

    // Buyer Info Section
    'buyer_info' => [
        'title' => 'Informazioni Acquirente',
        'wallet_label' => 'Wallet Algorand di destinazione',
        'wallet_placeholder' => 'Inserisci il tuo indirizzo wallet Algorand',
        'wallet_help' => 'L\'EGI verrà trasferito a questo indirizzo dopo il mint.',
        'verify_wallet' => 'Assicurati che l\'indirizzo sia corretto - non può essere modificato dopo il mint.',
    ],

    // Confirmation
    'confirmation' => [
        'title' => 'Conferma Mint',
        'description' => 'Stai per mintare questo EGI. Questa operazione è irreversibile.',
        'agree_terms' => 'Accetto i termini e condizioni',
        'final_warning' => 'Attenzione: Il mint non può essere annullato dopo la conferma.',
    ],

    // Success Messages
    'success' => [
        'minted' => 'EGI mintato con successo!',
        'transaction_id' => 'ID Transazione: :id',
        'view_on_explorer' => 'Visualizza su Algorand Explorer',
        'certificate_ready' => 'Il certificato di autenticità è pronto per il download.',
    ],

    // Error Messages
    'errors' => [
        'missing_params' => 'Parametri mancanti per il mint.',
        'invalid_reservation' => 'Prenotazione non valida o scaduta.',
        'already_minted' => 'Questo EGI è già stato mintato.',
        'payment_failed' => 'Pagamento fallito. Riprova.',
        'mint_failed' => 'Mint fallito. Contatta l\'assistenza.',
        'invalid_wallet' => 'Indirizzo wallet non valido.',
        'blockchain_error' => 'Errore della blockchain. Riprova più tardi.',
    ],

    // Validation
    'validation' => [
        'wallet_required' => 'L\'indirizzo wallet è obbligatorio.',
        'wallet_format' => 'L\'indirizzo wallet deve essere un indirizzo Algorand valido.',
        'terms_required' => 'Devi accettare i termini e condizioni.',
    ],

    // MiCA Compliance
    'compliance' => [
        'mica_title' => '⚖️ MiCA Compliance',
        'mica_description' => 'Questo processo è completamente MiCA-SAFE. Paghiamo in FIAT tramite PSP autorizzati, mintiamo l\'NFT per tuo conto, e gestiamo solo la custodia temporanea se necessario.',
    ],

    // Modal Messages
    'modal' => [
        'processing_title' => 'Elaborazione Mint in Corso',
        'processing_message' => 'Stiamo creando il tuo NFT sulla blockchain Algorand. Questa operazione potrebbe richiedere alcuni istanti. Non chiudere questa finestra.',
        'please_wait' => 'Attendere prego...',
    ],

    // JavaScript Messages
    'js' => [
        'default_error' => 'Si è verificato un errore durante il processo di mint. Riprova.',
        'error_prefix' => 'Errore: ',
        'success_title' => 'Mint Completato!',
        'success_message' => 'Il tuo EGI è stato creato con successo sulla blockchain Algorand.',
        'view_transaction' => 'Visualizza Transazione',
    ],

    // Status Messages (for already minted EGIs)
    'status' => [
        'already_minted' => 'EGI Già Mintato',
        'minted_message' => 'Questo EGI è già stato creato sulla blockchain Algorand. Non è possibile eseguire un nuovo mint.',
        'asa_id' => 'Asset ID (ASA)',
        'transaction_id' => 'ID Transazione',
        'view_on_explorer' => 'Visualizza su AlgoExplorer',
        'already_minted_button' => 'Già Mintato',

        // Processing status
        'processing_title' => 'Mint in Elaborazione',
        'processing_message' => 'Il tuo EGI è in fase di creazione sulla blockchain Algorand. Questo processo potrebbe richiedere alcuni minuti. Puoi chiudere questa pagina e tornare più tardi.',
        'processing_button' => 'Elaborazione in Corso...',
        'status_label' => 'Stato',
        'queued' => 'In Coda',
        'minting' => 'Creazione in Corso',
        'estimated_time' => 'Tempo stimato: 5-10 minuti',

        // Failed status
        'failed_title' => 'Mint Fallito',
        'failed_message' => 'Si è verificato un errore durante la creazione dell\'EGI sulla blockchain. Contatta l\'assistenza con il messaggio di errore seguente.',
    ],

    // Notification Messages
    'notification' => [
        'success_title' => '✅ NFT Creato con Successo!',
        'success_message' => 'Il tuo EGI è stato mintato sulla blockchain Algorand ed è ora verificabile on-chain.',
        'asa_label' => 'Asset ID',
        'view_blockchain' => 'Visualizza sulla Blockchain',
    ],

    // Worker Status Progress
    'worker' => [
        'checking' => 'Verifica disponibilità sistema...',
        'starting' => 'Avvio sistema di elaborazione...',
        'finalizing' => 'Preparazione finale...',
        'ready' => '✅ Sistema pronto!',
        'unavailable' => '❌ Sistema non disponibile',
        'error_title' => 'Sistema Non Disponibile',
        'error_message' => 'Il sistema di elaborazione non è al momento disponibile. Riprova tra qualche minuto o contatta l\'assistenza.',
        'error_button' => 'Ho Capito',
        'step_1' => 'Verifica',
        'step_2' => 'Avvio',
        'step_3' => 'Pronto',
    ],
];
