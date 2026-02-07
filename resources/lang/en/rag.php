<?php

/**
 * @package Resources\Lang\En
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - RAG System)
 * @date 2026-02-06
 * @purpose Translations for RAG system (Retrieval-Augmented Generation) - NATAN
 */

return [
    // === DOCUMENT INDEXING ===
    'indexing.started' => 'Starting document indexing',
    'indexing.creating_document' => 'Creating document record',
    'indexing.document_created' => 'Document created successfully',
    'indexing.creating_chunks' => 'Creating chunks from document',
    'indexing.chunks_created' => 'Chunks created successfully',
    'indexing.generating_embeddings' => 'Generating embeddings for chunks',
    'indexing.embeddings_generated' => 'Embeddings generated successfully',
    'indexing.completed' => 'Document indexing completed',
    'indexing.failed' => 'Document indexing failed',

    // === DOCUMENT RE-INDEXING ===
    'reindexing.started' => 'Starting document re-indexing',
    'reindexing.deleting_old_chunks' => 'Deleting previous chunks',
    'reindexing.creating_new_chunks' => 'Creating new chunks',
    'reindexing.generating_embeddings' => 'Generating new embeddings',
    'reindexing.completed' => 'Re-indexing completed successfully',
    'reindexing.failed' => 'Document re-indexing failed',

    // === CHUNKING OPERATIONS (Debug Level) ===
    'chunking.sections_count_check' => 'Checking number of sections found',
    'chunking.processing_multiple_sections' => 'Processing document with multiple sections',
    'chunking.processing_section' => 'Processing section',
    'chunking.section_chunked' => 'Section split into chunks',
    'chunking.processing_single_section' => 'Processing document with single section',
    'chunking.text_chunked' => 'Text split into chunks',
    'chunking.extracting_sections' => 'Extracting sections from content',
    'chunking.lines_count' => 'Counting document lines',
    'chunking.sections_extracted' => 'Sections extracted successfully',
    'chunking.creating_chunk' => 'Creating chunk',
    'chunking.chunk_created' => 'Chunk created successfully',

    // === BULK INDEXING ===
    'bulk_indexing.started' => 'Starting bulk document indexing',
    'bulk_indexing.document_failed' => 'Document indexing failed in batch',
    'bulk_indexing.completed' => 'Bulk indexing completed',

    // === DOCUMENT DELETION ===
    'delete.started' => 'Starting document deletion',
    'delete.completed' => 'Document deleted successfully',
    'delete.failed' => 'Document deletion failed',

    // === ERROR MESSAGES ===
    'error.index_failed' => 'Unable to index document. Please try again later.',
    'error.reindex_failed' => 'Unable to re-index document. Please try again later.',
    'error.delete_failed' => 'Unable to delete document. Please try again later.',
    'error.bulk_index_failed' => 'Error during bulk indexing. Some documents may not have been indexed.',
    'error.embedding_failed' => 'Error generating embeddings. Please check OpenAI configuration.',
    'error.chunking_failed' => 'Error splitting content into chunks.',
];

    // === EMBEDDING OPERATIONS ===
    'embedding.empty_input' => 'Empty input for embedding generation',
    'embedding.missing_api_key' => 'OpenAI API key not configured',
    'embedding.generating' => 'Generating embeddings',
    'embedding.generated' => 'Embeddings generated successfully',
    'embedding.api_error' => 'OpenAI API error during embedding generation',
    'embedding.exception' => 'Exception during embedding generation',
    'embedding.batch_started' => 'Starting batch embedding generation',
    'embedding.processing_batch' => 'Processing embedding batch',
    'embedding.batch_completed' => 'Batch embedding generation completed',
    'embedding.batch_failed' => 'Error during batch embedding generation',
    'embedding.stored' => 'Embedding saved to database',
    'embedding.store_failed' => 'Error saving embedding to database',
];
