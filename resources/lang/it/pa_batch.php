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
        'subtitle' => 'Gestisci le directory monitorate per elaborazione automatica atti',
        'create' => 'Nuova Sorgente',
        'edit' => 'Modifica Sorgente',
        'name' => 'Nome',
        'description' => 'Descrizione',
        'path' => 'Percorso',
        'path_help' => 'Percorso assoluto della directory da monitorare',
        'pattern' => 'Pattern File',
        'pattern_help' => 'Pattern glob per filtrare i file (es: *.pdf, *.p7m)',
        'status' => 'Stato',
        'priority' => 'Priorità',
        'priority_help' => 'Priorità di elaborazione (1-10, 10 = massima)',
        'auto_process' => 'Elaborazione Automatica',
        'auto_process_help' => 'Elabora automaticamente i file rilevati',
        'no_sources' => 'Nessuna sorgente configurata',
        'no_sources_desc' => 'Crea la prima sorgente per iniziare l\'elaborazione batch degli atti',
        'created_successfully' => 'Sorgente creata con successo',
        'updated_successfully' => 'Sorgente aggiornata con successo',
        'deleted_successfully' => 'Sorgente eliminata con successo',
        'cannot_delete_with_active_jobs' => 'Impossibile eliminare: ci sono job attivi in elaborazione',
        'status_updated' => 'Stato aggiornato con successo',
        'confirm_delete' => 'Sei sicuro di voler eliminare questa sorgente?',
        'active' => 'Attiva',
        'paused' => 'In Pausa',
        'disabled' => 'Disabilitata',
    ],

    'jobs' => [
        'title' => 'Job di Elaborazione',
        'file_name' => 'Nome File',
        'status' => 'Stato',
        'attempts' => 'Tentativi',
        'error' => 'Errore',
        'created_at' => 'Creato',
        'completed_at' => 'Completato',
        'no_jobs' => 'Nessun job trovato',
        'view_egi' => 'Visualizza Atto',
        'total' => 'Totale',
        'recent_jobs' => 'Job Recenti',
    ],

    'dashboard' => [
        'title' => 'Elaborazione Batch',
        'subtitle' => 'Monitoraggio elaborazione automatica atti',
        'global_stats' => 'Statistiche Globali',
        'total_jobs' => 'Job Totali',
        'completed' => 'Completati',
        'failed' => 'Falliti',
        'pending' => 'In Attesa',
        'success_rate' => 'Tasso di Successo',
        'active_sources' => 'Sorgenti Attive',
        'view_details' => 'Vedi Dettagli',
        'manage' => 'Gestisci',
        'pause' => 'Pausa',
        'resume' => 'Riprendi',
        'delete' => 'Elimina',
    ],
];
