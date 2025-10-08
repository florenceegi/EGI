<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Error Messages 2 - Italian
    |--------------------------------------------------------------------------
    | Nuove traduzioni errori per evitare file troppo grandi
    */

    'dev' => [
        // ProfileImage Controller Errors
        'profile_image_upload_validation_error' => 'Errore di validazione durante il caricamento dell\'immagine del profilo per utente :user_id.',
        'profile_image_upload_error' => 'Errore durante il caricamento dell\'immagine del profilo per utente :user_id.',
        'profile_set_current_image_error' => 'Errore durante l\'impostazione dell\'immagine come principale per utente :user_id.',
        'profile_image_delete_error' => 'Errore durante l\'eliminazione dell\'immagine del profilo per utente :user_id.',
        'profile_banner_upload_error' => 'Errore durante il caricamento del banner per utente :user_id.',
        'profile_set_current_banner_error' => 'Errore durante l\'impostazione del banner principale per utente :user_id.',
        'profile_banner_delete_error' => 'Errore durante l\'eliminazione del banner per utente :user_id.',

        // COA Signature Errors
        'coa_author_sign_error' => 'Errore durante la firma dell\'autore per COA :coa_id. Database query fallita: :error',

        // PA/Enterprise System Errors
        'pa_dashboard_error' => 'Errore durante il caricamento della dashboard PA per utente :user_id.',
        'pa_dashboard_quickstats_error' => 'Errore durante il caricamento delle statistiche PA per utente :user_id.',
        'pa_heritage_list_error' => 'Errore durante il caricamento della lista patrimonio per utente :user_id.',
        'pa_heritage_detail_error' => 'Errore durante il caricamento del dettaglio patrimonio :egi_id per utente :user_id.',

        // PA Acts Upload Errors (Dev)
        'pa_act_auth_required' => 'Tentativo di upload atto PA senza autenticazione. User: :user_id, IP: :ip',
        'pa_act_role_required' => 'Utente :user_id non autorizzato (ruolo non PA) per upload atto.',
        'pa_act_validation_failed' => 'Validazione fallita per upload atto PA. User: :user_id, Errori: :errors',
        'pa_act_invalid_file' => 'File non valido o mancante per upload atto PA. User: :user_id, File: :filename',
        'pa_act_invalid_signature' => 'Firma digitale non valida o mancante su PDF. User: :user_id, File: :filename, Reason: :reason',
        'pa_act_upload_failed' => 'Errore durante upload atto PA. User: :user_id, File: :filename, Error: :error',
        'pa_act_collection_failed' => 'Errore creazione/recupero fascicolo per atto PA. User: :user_id, Protocol: :protocol_number',

        // Blockchain Minting Errors (Dev)
        'mint_checkout_error' => 'Errore durante caricamento checkout mint. User: :user_id, EGI: :egi_id, Error: :error',
        'mint_process_error' => 'Errore processo mint blockchain. User: :user_id, EGI: :egi_id, Reservation: :reservation_id, Payment: :payment_method, Error: :error',
        'real_blockchain_mint_failed' => 'CRITICAL: Real blockchain mint failed. EgiBlockchain: :egi_blockchain_id, Attempt: :attempt, Error: :error',
    ],

    'user' => [
        // ProfileImage Controller User Messages
        'profile_image_upload_validation_error' => 'I dati dell\'immagine non sono validi. Controlla il formato e le dimensioni.',
        'profile_image_upload_error' => 'Impossibile caricare l\'immagine del profilo. Riprova più tardi.',
        'profile_set_current_image_error' => 'Impossibile impostare l\'immagine come principale. Riprova.',
        'profile_image_delete_error' => 'Impossibile eliminare l\'immagine del profilo. Riprova più tardi.',
        'profile_banner_upload_error' => 'Impossibile caricare l\'immagine banner. Riprova più tardi.',
        'profile_set_current_banner_error' => 'Impossibile impostare il banner principale. Riprova.',
        'profile_banner_delete_error' => 'Impossibile eliminare il banner. Riprova più tardi.',

        // COA Signature User Messages
        'coa_author_sign_error' => 'Errore durante la firma del certificato. Riprova più tardi o contatta l\'assistenza.',

        // PA/Enterprise System User Messages
        'pa_dashboard_error' => 'Impossibile caricare la dashboard PA. Riprova tra poco.',
        'pa_dashboard_quickstats_error' => 'Impossibile aggiornare le statistiche. Riprova.',
        'pa_heritage_list_error' => 'Impossibile caricare la lista del patrimonio. Riprova tra poco.',
        'pa_heritage_detail_error' => 'Impossibile caricare il dettaglio del bene patrimoniale. Riprova tra poco.',

        // PA Acts Upload Errors (User-Friendly)
        'pa_act_auth_required' => 'Devi effettuare l\'accesso per caricare atti PA.',
        'pa_act_role_required' => 'Solo gli enti della Pubblica Amministrazione possono caricare atti amministrativi.',
        'pa_act_validation_failed' => 'I dati inseriti non sono validi. Controlla i campi e riprova.',
        'pa_act_invalid_file' => 'Il file caricato non è valido o è mancante. Assicurati di caricare un PDF.',
        'pa_act_invalid_signature' => 'Il PDF deve essere firmato digitalmente con firma qualificata (QES/PAdES). Il documento caricato non ha una firma valida.',
        'pa_act_upload_failed' => 'Si è verificato un errore durante il caricamento dell\'atto. Riprova più tardi o contatta l\'assistenza.',
        'pa_act_collection_failed' => 'Impossibile creare o recuperare il fascicolo per l\'atto. Riprova o contatta l\'assistenza.',

        // Blockchain Minting Errors (User)
        'mint_checkout_error' => 'Impossibile caricare la pagina di acquisto. Riprova tra poco.',
        'mint_process_error' => 'Errore durante il processo di acquisto. Il pagamento non è stato effettuato. Riprova o contatta l\'assistenza.',
        'real_blockchain_mint_failed' => 'Il mint sulla blockchain è fallito. Il tuo pagamento è al sicuro, contatteremo l\'assistenza per risolvere.',
    ],

    // Generic message (used by UserInterfaceHandler if no specific message found)
    'generic_error' => 'Si è verificato un errore. Riprova più tardi o contatta l\'assistenza.',
];
