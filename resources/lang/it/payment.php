<?php

return [
    'settings_restricted_to_sellers' => 'Le impostazioni di pagamento sono disponibili solo per i venditori.',
    'invalid_method' => 'Metodo di pagamento non valido.',
    'method_enabled' => ':method abilitato con successo.',
    'method_disabled' => ':method disabilitato con successo.',
    'must_enable_first' => 'Il metodo di pagamento deve essere prima abilitato.',
    'default_set' => ':method impostato come predefinito.',
    'bank_details_saved' => 'Dettagli bonifico salvati con successo.',
    'generic_error' => 'Si è verificato un errore durante l\'elaborazione della richiesta.',
    'collection_method_enabled' => ':method abilitato per la collezione.',
    'collection_method_disabled' => ':method disabilitato per la collezione.',
    'collection_must_enable_first' => 'Il metodo di pagamento deve essere prima abilitato per questa collezione.',
    'collection_default_set' => ':method impostato come predefinito per questa collezione.',
    'collection_bank_details_saved' => 'Dettagli bancari della collezione salvati con successo.',
    'settings_title' => 'Impostazioni Pagamenti',
    'settings_description' => 'Configura come ricevere i pagamenti per le tue vendite',
    'stripe_description' => 'Ricevi pagamenti via carta di credito direttamente sul tuo conto bancario.',
    'egili_description' => 'Ricevi pagamenti in token Egili (presto disponibile).',
    'bank_description' => 'Ricevi pagamenti tramite bonifico bancario diretto.',
    'errors' => [
        'merchant_account_incomplete' => 'Account venditore incompleto.',
        'paypal_not_configured' => 'PayPal non configurato.',
    ],

    'wizard' => [
        'chip_label'  => 'Attiva pagamenti',
        'intro_title' => 'Attiva il sistema di pagamento',
        'intro_text'  => 'Per iniziare a vendere le tue opere devi attivare :psp_name. È una procedura guidata che richiede solo pochi minuti.',
        'intro_note'  => 'I pagamenti arrivano direttamente sul tuo conto. FlorenceEGI non trattiene denaro.',
        'cta'         => 'Attiva :psp_name',
        'processing'  => 'Avvio in corso…',
        'link_failed' => 'Impossibile generare il link. Riprova tra qualche istante.',
        'no_wallet'   => 'Nessun wallet configurato. Contatta il supporto.',
        'success'     => 'Pagamenti attivati! Ora puoi vendere le tue opere.',
        'refresh'     => 'Il link è scaduto. Clicca di nuovo su "Attiva pagamenti" per riprendere.',
    ],
];