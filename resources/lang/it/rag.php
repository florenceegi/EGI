<?php

/**
 * @package Resources\Lang\It
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - RAG System)
 * @date 2026-02-06
 * @purpose Traduzioni per sistema RAG (Retrieval-Augmented Generation) - NATAN
 */

return [
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
];
