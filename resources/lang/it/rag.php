<?php

/**
 * @package Resources\Lang\It
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - RAG System)
 * @date 2026-02-06
 * @purpose Traduzioni per sistema RAG (Retrieval-Augmented Generation) - NATAN
 */

return [
    // === CATEGORIES ===
    'categories' => [
        // TIER 1 - CRITICAL
        'getting_started' => [
            'name' => 'Guida Rapida',
            'description' => 'Primi passi sulla piattaforma FlorenceEGI'
        ],
        'security' => [
            'name' => 'Sicurezza e Account',
            'description' => 'Gestione account, password, autenticazione e sicurezza'
        ],
        'privacy_gdpr' => [
            'name' => 'Privacy e GDPR',
            'description' => 'Protezione dati personali, consensi e diritti GDPR'
        ],
        'troubleshooting' => [
            'name' => 'Risoluzione Problemi',
            'description' => 'FAQ e soluzioni ai problemi comuni'
        ],
        'support' => [
            'name' => 'Assistenza e Supporto',
            'description' => 'Come contattare il supporto e ottenere assistenza'
        ],

        // CORE FUNCTIONAL
        'platform' => [
            'name' => 'Piattaforma',
            'description' => 'Fondamenti, visione e architettura della piattaforma FlorenceEGI'
        ],
        'architecture' => [
            'name' => 'Architettura Tecnica',
            'description' => 'Architettura tecnica, stack tecnologico e design patterns'
        ],
        'payments' => [
            'name' => 'Pagamenti',
            'description' => 'Sistema pagamenti, Stripe, metodi di pagamento e transazioni'
        ],
        'billing' => [
            'name' => 'Fatturazione',
            'description' => 'Fatture, ricevute e documentazione fiscale'
        ],
        'royalty' => [
            'name' => 'Royalty',
            'description' => 'Sistema royalty, calcolo e distribuzione automatica'
        ],
        'fiscal' => [
            'name' => 'Fiscalità',
            'description' => 'Gestione fiscale, tasse e compliance fiscale'
        ],
        'blockchain' => [
            'name' => 'Blockchain',
            'description' => 'Tecnologia blockchain, NFT e certificazione opere'
        ],
        'wallet' => [
            'name' => 'Wallet e Cripto',
            'description' => 'Gestione wallet, criptovalute e transazioni blockchain'
        ],
        'rebind' => [
            'name' => 'Mercato Secondario',
            'description' => 'Rebind, rivendita opere e mercato secondario'
        ],
        'collections' => [
            'name' => 'Collezioni',
            'description' => 'Gestione collezioni, caricamento e organizzazione opere'
        ],

        // TIER 2 - IMPORTANT
        'media_management' => [
            'name' => 'Gestione Media',
            'description' => 'Caricamento, ottimizzazione e gestione immagini e video'
        ],
        'verification_kyc' => [
            'name' => 'Verifica e KYC',
            'description' => 'Processo di verifica identità e KYC'
        ],
        'search_discovery' => [
            'name' => 'Ricerca e Scoperta',
            'description' => 'Ricerca opere, filtri e algoritmi di scoperta'
        ],
        'quality_standards' => [
            'name' => 'Standard Qualità',
            'description' => 'Linee guida qualità opere e contenuti'
        ],
        'legal_compliance' => [
            'name' => 'Legale e Compliance',
            'description' => 'Aspetti legali, termini di servizio e conformità normativa'
        ],
        'refunds_disputes' => [
            'name' => 'Rimborsi e Dispute',
            'description' => 'Gestione rimborsi, reclami e dispute'
        ],

        // TIER 3 - NICE-TO-HAVE
        'export_import' => [
            'name' => 'Export/Import Dati',
            'description' => 'Esportazione e importazione dati personali'
        ],
        'social_features' => [
            'name' => 'Funzioni Social',
            'description' => 'Profili, follower, interazioni social'
        ],
        'promotions' => [
            'name' => 'Promozioni e Marketing',
            'description' => 'Strumenti promozionali, coupon e marketing'
        ],
        'mobile_app' => [
            'name' => 'App Mobile',
            'description' => 'Applicazione mobile e funzionalità'
        ],
        'api_advanced' => [
            'name' => 'API e Funzioni Avanzate',
            'description' => 'API per sviluppatori e funzionalità avanzate'
        ],
        'accessibility' => [
            'name' => 'Accessibilità',
            'description' => 'Funzionalità di accessibilità per utenti con disabilità'
        ],

        // SPECIALIZED
        'ai_natan' => [
            'name' => 'AI e NATAN',
            'description' => 'Sistema AI NATAN, assistente virtuale e funzionalità intelligenti'
        ],
        'oracode' => [
            'name' => 'Oracode OS3.0',
            'description' => 'Framework Oracode, pattern e standard di sviluppo'
        ],
        'development' => [
            'name' => 'Sviluppo e Developer',
            'description' => 'Documentazione tecnica per sviluppatori'
        ],
        'glossary' => [
            'name' => 'Glossario',
            'description' => 'Terminologia, definizioni e glossario tecnico'
        ],
    ],

    // === DOCUMENT INDEXING ===
    'indexing.started' => 'Avvio indicizzazione documento',
    'indexing.creating_document' => 'Creazione record documento',
    'indexing.document_created' => 'Documento creato con successo',
    'indexing.creating_chunks' => 'Creazione chunks da documento',
    'indexing.chunks_created' => 'Chunks creati con successo',
    'indexing.generating_embeddings' => 'Generazione embeddings per chunks',
    'indexing.embeddings_generated' => 'Embeddings generati con successo',
    'indexing.completed' => 'Indicizzazione documento completata',
    'indexing.failed' => 'Errore durante indicizzazione documento',

    // === DOCUMENT RE-INDEXING ===
    'reindexing.started' => 'Avvio re-indicizzazione documento',
    'reindexing.deleting_old_chunks' => 'Eliminazione chunks precedenti',
    'reindexing.creating_new_chunks' => 'Creazione nuovi chunks',
    'reindexing.generating_embeddings' => 'Generazione nuovi embeddings',
    'reindexing.completed' => 'Re-indicizzazione completata con successo',
    'reindexing.failed' => 'Errore durante re-indicizzazione documento',

    // === CHUNKING OPERATIONS (Debug Level) ===
    'chunking.sections_count_check' => 'Verifica numero sezioni trovate',
    'chunking.processing_multiple_sections' => 'Elaborazione documento con sezioni multiple',
    'chunking.processing_section' => 'Elaborazione sezione',
    'chunking.section_chunked' => 'Sezione suddivisa in chunks',
    'chunking.processing_single_section' => 'Elaborazione documento con sezione singola',
    'chunking.text_chunked' => 'Testo suddiviso in chunks',
    'chunking.extracting_sections' => 'Estrazione sezioni da contenuto',
    'chunking.lines_count' => 'Conteggio linee documento',
    'chunking.sections_extracted' => 'Sezioni estratte con successo',
    'chunking.creating_chunk' => 'Creazione chunk',
    'chunking.chunk_created' => 'Chunk creato con successo',

    // === BULK INDEXING ===
    'bulk_indexing.started' => 'Avvio indicizzazione massiva documenti',
    'bulk_indexing.document_failed' => 'Errore indicizzazione documento in batch',
    'bulk_indexing.completed' => 'Indicizzazione massiva completata',

    // === DOCUMENT DELETION ===
    'delete.started' => 'Avvio eliminazione documento',
    'delete.completed' => 'Documento eliminato con successo',
    'delete.failed' => 'Errore durante eliminazione documento',

    // === EMBEDDING OPERATIONS ===
    'embedding.empty_input' => 'Input vuoto per generazione embeddings',
    'embedding.missing_api_key' => 'Chiave API OpenAI non configurata',
    'embedding.generating' => 'Generazione embeddings in corso',
    'embedding.generated' => 'Embeddings generati con successo',
    'embedding.api_error' => 'Errore API OpenAI durante generazione embeddings',
    'embedding.exception' => 'Eccezione durante generazione embeddings',
    'embedding.batch_started' => 'Avvio generazione batch embeddings',
    'embedding.processing_batch' => 'Elaborazione batch embeddings',
    'embedding.batch_completed' => 'Generazione batch embeddings completata',
    'embedding.batch_failed' => 'Errore durante generazione batch embeddings',
    'embedding.stored' => 'Embedding salvato in database',
    'embedding.store_failed' => 'Errore salvataggio embedding in database',

    // === ERROR MESSAGES ===
    'error.index_failed' => 'Impossibile indicizzare il documento. Riprova più tardi.',
    'error.reindex_failed' => 'Impossibile re-indicizzare il documento. Riprova più tardi.',
    'error.delete_failed' => 'Impossibile eliminare il documento. Riprova più tardi.',
    'error.bulk_index_failed' => 'Errore durante indicizzazione massiva. Alcuni documenti potrebbero non essere stati indicizzati.',
    'error.embedding_failed' => 'Errore durante generazione embeddings. Verifica la configurazione OpenAI.',
    'error.chunking_failed' => 'Errore durante suddivisione contenuto in chunks.',

    // === QUERY OPERATIONS ===
    'query.processing_started' => 'Avvio elaborazione query utente',
    'query.cache_hit' => 'Risposta trovata in cache',
    'query.cache_miss' => 'Risposta non in cache, generazione nuova risposta',
    'query.response_created' => 'Risposta creata con successo',
    'query.processing_completed' => 'Elaborazione query completata',
    'query.processing_failed' => 'Errore durante elaborazione query',
    'query.recording_feedback' => 'Registrazione feedback utente',
    'query.feedback_recorded' => 'Feedback registrato con successo',
    'query.feedback_recording_failed' => 'Errore durante registrazione feedback',
    'query.fetching_user_history' => 'Recupero cronologia query utente',
    'query.user_history_fetched' => 'Cronologia utente recuperata',
    'query.user_history_failed' => 'Errore durante recupero cronologia',
    'query.fetching_analytics' => 'Recupero analytics query',
    'query.analytics_fetched' => 'Analytics recuperati con successo',
    'query.analytics_failed' => 'Errore durante recupero analytics',

    // === RESPONSE GENERATION ===
    'response.claude_api_error' => 'Errore API Claude durante generazione risposta',
    'response.claude_exception' => 'Eccezione durante chiamata Claude',
    'response.openai_api_error' => 'Errore API OpenAI durante generazione risposta',
    'response.openai_exception' => 'Eccezione durante chiamata OpenAI',
    'error.response_generation_failed' => 'Errore durante generazione risposta. Riprova più tardi.',
];
