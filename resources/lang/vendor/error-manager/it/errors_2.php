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
    ],

    // Generic message (used by UserInterfaceHandler if no specific message found)
    'generic_error' => 'Si è verificato un errore. Riprova più tardi o contatta l\'assistenza.',
];
