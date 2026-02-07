<?php

/**
 * @package Resources\Lang\De
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - RAG System)
 * @date 2026-02-06
 * @purpose Übersetzungen für RAG-System (Retrieval-Augmented Generation) - NATAN
 */

return [
    // === DOCUMENT INDEXING ===
    'indexing.started' => 'Dokumentenindizierung wird gestartet',
    'indexing.creating_document' => 'Dokumentendatensatz wird erstellt',
    'indexing.document_created' => 'Dokument erfolgreich erstellt',
    'indexing.creating_chunks' => 'Chunks aus Dokument werden erstellt',
    'indexing.chunks_created' => 'Chunks erfolgreich erstellt',
    'indexing.generating_embeddings' => 'Embeddings für Chunks werden generiert',
    'indexing.embeddings_generated' => 'Embeddings erfolgreich generiert',
    'indexing.completed' => 'Dokumentenindizierung abgeschlossen',
    'indexing.failed' => 'Dokumentenindizierung fehlgeschlagen',

    // === DOCUMENT RE-INDEXING ===
    'reindexing.started' => 'Neuindizierung des Dokuments wird gestartet',
    'reindexing.deleting_old_chunks' => 'Vorherige Chunks werden gelöscht',
    'reindexing.creating_new_chunks' => 'Neue Chunks werden erstellt',
    'reindexing.generating_embeddings' => 'Neue Embeddings werden generiert',
    'reindexing.completed' => 'Neuindizierung erfolgreich abgeschlossen',
    'reindexing.failed' => 'Neuindizierung des Dokuments fehlgeschlagen',

    // === CHUNKING OPERATIONS (Debug Level) ===
    'chunking.sections_count_check' => 'Anzahl gefundener Abschnitte wird überprüft',
    'chunking.processing_multiple_sections' => 'Dokument mit mehreren Abschnitten wird verarbeitet',
    'chunking.processing_section' => 'Abschnitt wird verarbeitet',
    'chunking.section_chunked' => 'Abschnitt in Chunks aufgeteilt',
    'chunking.processing_single_section' => 'Dokument mit einzelnem Abschnitt wird verarbeitet',
    'chunking.text_chunked' => 'Text in Chunks aufgeteilt',
    'chunking.extracting_sections' => 'Abschnitte werden aus Inhalt extrahiert',
    'chunking.lines_count' => 'Dokumentzeilen werden gezählt',
    'chunking.sections_extracted' => 'Abschnitte erfolgreich extrahiert',
    'chunking.creating_chunk' => 'Chunk wird erstellt',
    'chunking.chunk_created' => 'Chunk erfolgreich erstellt',

    // === BULK INDEXING ===
    'bulk_indexing.started' => 'Massenindizierung von Dokumenten wird gestartet',
    'bulk_indexing.document_failed' => 'Dokumentenindizierung im Batch fehlgeschlagen',
    'bulk_indexing.completed' => 'Massenindizierung abgeschlossen',

    // === DOCUMENT DELETION ===
    'delete.started' => 'Dokumentenlöschung wird gestartet',
    'delete.completed' => 'Dokument erfolgreich gelöscht',
    'delete.failed' => 'Dokumentenlöschung fehlgeschlagen',

    // === ERROR MESSAGES ===
    'error.index_failed' => 'Dokument kann nicht indiziert werden. Bitte versuchen Sie es später erneut.',
    'error.reindex_failed' => 'Dokument kann nicht neu indiziert werden. Bitte versuchen Sie es später erneut.',
    'error.delete_failed' => 'Dokument kann nicht gelöscht werden. Bitte versuchen Sie es später erneut.',
    'error.bulk_index_failed' => 'Fehler bei Massenindizierung. Einige Dokumente wurden möglicherweise nicht indiziert.',
    'error.embedding_failed' => 'Fehler beim Generieren von Embeddings. Bitte überprüfen Sie die OpenAI-Konfiguration.',
    'error.chunking_failed' => 'Fehler beim Aufteilen des Inhalts in Chunks.',

    // === EMBEDDING OPERATIONS ===
    'embedding.empty_input' => 'Leere Eingabe für Embedding-Generierung',
    'embedding.missing_api_key' => 'OpenAI API-Schlüssel nicht konfiguriert',
    'embedding.generating' => 'Embeddings werden generiert',
    'embedding.generated' => 'Embeddings erfolgreich generiert',
    'embedding.api_error' => 'OpenAI API-Fehler bei Embedding-Generierung',
    'embedding.exception' => 'Ausnahme bei Embedding-Generierung',
    'embedding.batch_started' => 'Batch-Embedding-Generierung wird gestartet',
    'embedding.processing_batch' => 'Embedding-Batch wird verarbeitet',
    'embedding.batch_completed' => 'Batch-Embedding-Generierung abgeschlossen',
    'embedding.batch_failed' => 'Fehler bei Batch-Embedding-Generierung',
    'embedding.stored' => 'Embedding in Datenbank gespeichert',
    'embedding.store_failed' => 'Fehler beim Speichern des Embeddings in Datenbank',

    // === QUERY OPERATIONS ===
    'query.processing_started' => 'Benutzeranfrage-Verarbeitung wird gestartet',
    'query.cache_hit' => 'Antwort im Cache gefunden',
    'query.cache_miss' => 'Antwort nicht im Cache, neue Antwort wird generiert',
    'query.response_created' => 'Antwort erfolgreich erstellt',
    'query.processing_completed' => 'Anfrage-Verarbeitung abgeschlossen',
    'query.processing_failed' => 'Anfrage-Verarbeitung fehlgeschlagen',
    'query.recording_feedback' => 'Benutzerfeedback wird aufgezeichnet',
    'query.feedback_recorded' => 'Feedback erfolgreich aufgezeichnet',
    'query.feedback_recording_failed' => 'Fehler beim Aufzeichnen des Feedbacks',
    'query.fetching_user_history' => 'Benutzeranfrage-Verlauf wird abgerufen',
    'query.user_history_fetched' => 'Benutzerverlauf erfolgreich abgerufen',
    'query.user_history_failed' => 'Fehler beim Abrufen des Benutzerverlaufs',
    'query.fetching_analytics' => 'Anfrage-Analytics werden abgerufen',
    'query.analytics_fetched' => 'Analytics erfolgreich abgerufen',
    'query.analytics_failed' => 'Fehler beim Abrufen der Analytics',

    // === RESPONSE GENERATION ===
    'response.claude_api_error' => 'Claude API-Fehler bei Antwortgenerierung',
    'response.claude_exception' => 'Ausnahme während Claude-Aufruf',
    'response.openai_api_error' => 'OpenAI API-Fehler bei Antwortgenerierung',
    'response.openai_exception' => 'Ausnahme während OpenAI-Aufruf',
    'error.response_generation_failed' => 'Fehler beim Generieren der Antwort. Bitte versuchen Sie es später erneut.',
];
