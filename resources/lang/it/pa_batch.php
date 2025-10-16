<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PA Batch Processing Language Lines
    |--------------------------------------------------------------------------
    |
    | Traduzioni per il sistema di elaborazione batch degli atti PA
    |
    */

    'api' => [
        'success' => 'Atto ricevuto con successo',
        'duplicate' => 'Atto già presente nel sistema',
        'job_create_failed' => 'Errore nella creazione del job di elaborazione',
        'processing_failed' => 'Errore durante l\'elaborazione dei metadati',
        'invalid_signature' => 'Firma digitale non valida',
        'validation_failed' => 'Dati non valid',
    ],

    'status' => [
        'pending' => 'In attesa',
        'processing' => 'In elaborazione',
        'completed' => 'Completato',
        'failed' => 'Fallito',
        'duplicate' => 'Duplicato',
    ],

    'sources' => [
        'title' => 'Sorgenti Batch',
        'create' => 'Nuova Sorgente',
        'edit' => 'Modifica Sorgente',
        'name' => 'Nome',
        'description' => 'Descrizione',
        'path' => 'Percorso',
        'pattern' => 'Pattern File',
        'status' => 'Stato',
        'priority' => 'Priorità',
        'auto_process' => 'Elaborazione Automatica',
    ],

    'jobs' => [
        'title' => 'Job di Elaborazione',
        'file_name' => 'Nome File',
        'status' => 'Stato',
        'attempts' => 'Tentativi',
        'error' => 'Errore',
        'created_at' => 'Creato',
        'completed_at' => 'Completato',
    ],
];

