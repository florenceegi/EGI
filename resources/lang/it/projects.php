<?php

return [
    // Messaggi success
    'created_successfully' => 'Progetto ":name" creato con successo',
    'updated_successfully' => 'Progetto aggiornato con successo',
    'deleted_successfully' => 'Progetto eliminato con successo',
    'document_uploaded_successfully' => 'Documento ":filename" caricato con successo',

    // Messaggi error
    'not_found' => 'Progetto non trovato',
    'unauthorized' => 'Non sei autorizzato ad accedere a questo progetto',
    'limit_reached' => 'Hai raggiunto il limite massimo di :limit progetti. Elimina progetti inutilizzati prima di crearne di nuovi.',
    'document_limit_reached' => 'Hai raggiunto il limite massimo di :limit documenti per questo progetto',
    'file_too_large' => 'Il file supera la dimensione massima di :size MB',
    'invalid_file_type' => 'Tipo di file non permesso. Formati supportati: :types',

    // Labels
    'name' => 'Nome progetto',
    'description' => 'Descrizione',
    'icon' => 'Icona',
    'color' => 'Colore',
    'created_at' => 'Creato il',
    'updated_at' => 'Aggiornato il',
    'documents_count' => 'Documenti',
    'ready_documents_count' => 'Documenti pronti',
    'processing_documents_count' => 'Documenti in elaborazione',
    'failed_documents_count' => 'Documenti falliti',
    'total_chunks_count' => 'Chunk totali',
    'chat_messages_count' => 'Messaggi chat',

    // Actions
    'create' => 'Crea progetto',
    'create_new' => 'Nuovo Progetto',
    'create_first_project' => 'Crea il tuo primo progetto',
    'new_project' => 'Nuovo Progetto',
    'creating' => 'Creazione in corso...',
    'cancel' => 'Annulla',
    'edit' => 'Modifica progetto',
    'delete' => 'Elimina progetto',
    'upload_document' => 'Carica documento',
    'view_documents' => 'Vedi documenti',
    'view_details' => 'Vedi dettagli',
    'view_chat' => 'Vedi chat',
    'settings' => 'Impostazioni',
    'filter_apply' => 'Applica filtri',
    'filter_clear' => 'Cancella filtri',
    'clear_filters' => 'Rimuovi filtri',
    'close' => 'Chiudi',
    'coming_soon' => 'Funzionalità in arrivo',
    'modal_description' => 'La gestione progetti sarà disponibile a breve. Potrai caricare documenti, organizzarli in progetti e usarli per chat prioritarie con N.A.T.A.N.',
    'remove_context' => 'Rimuovi Progetto',
    'active_context' => 'Progetto attivo',

    // Page titles
    'page_title_index' => 'I Miei Progetti',
    'page_title_create' => 'Nuovo Progetto',
    'page_title_edit' => 'Modifica Progetto',
    'page_title_show' => 'Dettagli Progetto',
    'projects' => 'Progetti',

    // Search and Filters
    'search_placeholder' => 'Cerca progetti',
    'search_by_name_desc' => 'Cerca per nome o descrizione',
    'filter_status' => 'Stato',
    'all_status' => 'Tutti gli stati',
    'status' => 'Stato',
    'status_active' => 'Attivo',
    'status_inactive' => 'Inattivo',
    'active_filters' => 'Filtri attivi',
    'search' => 'Ricerca',

    // Empty states
    'no_projects_title' => 'Nessun progetto ancora',
    'no_projects' => 'Nessun progetto trovato',
    'create_first' => 'Crea il tuo primo progetto per iniziare',
    'no_projects_message' => 'Crea il tuo primo progetto per iniziare a organizzare documenti e conversazioni.',
    'no_results_title' => 'Nessun risultato trovato',
    'no_results_message' => 'Non ci sono progetti che corrispondono ai filtri selezionati. Prova a modificare i criteri di ricerca.',
    'no_description' => 'Nessuna descrizione',

    // Form placeholders
    'name_placeholder' => 'Es: PNRR Sostenibilità 2024',
    'description_placeholder' => 'Descrizione opzionale del progetto...',

    // Form validation
    'name_required' => 'Il nome del progetto è obbligatorio',
    'create_error' => 'Errore durante la creazione del progetto',
    'network_error' => 'Errore di connessione. Riprova.',
    'select_error' => 'Errore nella selezione del progetto',
    'remove_error' => 'Errore nella rimozione del progetto',
    'remove_confirm' => 'Sei sicuro di voler rimuovere il progetto attivo? La chat tornerà in modalità generica.',

    // Stats and info
    'chats_count' => 'Chat',
    'limits_title' => 'Limiti progetto',
    'limits_message' => 'Hai :current progetti su :max massimi (:remaining disponibili).',

    // Tabs
    'tab_documents' => 'Documenti',
    'tab_chat' => 'Chat',
    'tab_settings' => 'Impostazioni',
];
