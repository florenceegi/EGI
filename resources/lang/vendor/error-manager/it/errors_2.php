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
    ],

    // Generic message (used by UserInterfaceHandler if no specific message found)
    'generic_error' => 'Si è verificato un errore. Riprova più tardi o contatta l\'assistenza.',
];
