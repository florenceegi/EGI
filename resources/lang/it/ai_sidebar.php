<?php

/**
 * AI Sidebar - Traduzioni Italiane
 * P0-2: Translation keys only
 * P0-9: All 6 languages required
 */

return [
    // General
    'title' => 'Natan',
    'subtitle' => 'Ti aiuto a completare il profilo',
    'toggle_title' => 'Apri assistente onboarding',
    'close' => 'Chiudi',
    'send' => 'Invia',
    'chat_placeholder' => 'Chiedi qualcosa...',
    'assistant_name' => 'Natan',
    'quick_actions_label' => 'Prossimi passi consigliati:',

    // Messages (programmatic AI)
    'messages' => [
        'welcome' => 'Benvenuto! Ti aiuto a completare la configurazione del tuo profilo.',
        'progress_low' => 'Ottimo inizio! Hai completato :completed di :total passaggi. Prossimo: :nextStep',
        'progress_high' => 'Ci sei quasi! Mancano solo :remaining passaggi.',
        'complete' => 'Congratulazioni! Il tuo profilo è completamente configurato. 🎉',
        'all_done' => 'Tutti i passaggi completati!',
    ],

    // Checklist
    'checklist' => [
        'progress' => 'Progresso setup',
        'title' => 'Checklist Onboarding',
    ],

    // Discourse messages (AI-like text)
    'discourse' => [
        'greeting' => 'Ciao',
        'greeting_suffix' => '! Sono qui per aiutarti a completare il tuo profilo.',
        'progress_intro' => 'Hai completato',
        'progress_of' => 'su',
        'progress_suffix' => ' passaggi. Vediamo insieme cosa manca per rendere il tuo profilo perfetto.',
        'missing_title' => 'Ecco cosa devi ancora fare:',
        'suggestion_intro' => 'Ti consiglio di iniziare da',
        'click_hint' => 'Clicca su uno degli elementi nella lista qui sotto per completarlo.',
        'complete_title' => 'Fantastico!',
        'complete_text' => 'Il tuo profilo è completo e pronto per essere scoperto. Ora puoi concentrarti sulla creazione delle tue opere e sulla crescita della tua community.',
    ],

    // Checklist Items - Creator
    'steps' => [
        'avatar' => [
            'title' => 'Carica il tuo avatar',
            'description' => 'Aggiungi una foto profilo per farti riconoscere',
        ],
        'banner' => [
            'title' => 'Personalizza il banner',
            'description' => 'Aggiungi un\'immagine di copertina al tuo profilo',
        ],
        'bio' => [
            'title' => 'Scrivi la tua bio',
            'description' => 'Racconta chi sei e cosa crei',
        ],
        'company_about' => [
            'title' => 'Scrivi la tua bio',
            'description' => 'Racconta chi sei e cosa crei',
        ],
        'stripe' => [
            'title' => 'Configura i pagamenti',
            'description' => 'Collega Stripe per ricevere pagamenti',
        ],
        'collection' => [
            'title' => 'Crea la prima collezione',
            'description' => 'Organizza le tue opere in una collezione',
        ],
        'first_egi' => [
            'title' => 'Crea il primo EGI',
            'description' => 'Pubblica la tua prima opera digitale',
        ],
        'social_links' => [
            'title' => 'Aggiungi i link social',
            'description' => 'Connetti i tuoi profili social',
        ],
        'verify_email' => [
            'title' => 'Verifica email',
            'description' => 'Conferma il tuo indirizzo email',
        ],
    ],

    // Errors
    'errors' => [
        'request_failed' => 'Richiesta fallita. Riprova.',
        'connection_error' => 'Errore di connessione. Verifica la tua connessione internet.',
        'invalid_user_type' => 'Tipo utente non valido.',
        'unauthorized' => 'Non autorizzato ad accedere a questa risorsa.',
    ],
];
