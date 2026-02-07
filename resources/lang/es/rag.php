<?php

/**
 * @package Resources\Lang\Es
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - RAG System)
 * @date 2026-02-06
 * @purpose Traducciones para sistema RAG (Retrieval-Augmented Generation) - NATAN
 */

return [
    // === DOCUMENT INDEXING ===
    'indexing.started' => 'Iniciando indexación de documento',
    'indexing.creating_document' => 'Creando registro de documento',
    'indexing.document_created' => 'Documento creado exitosamente',
    'indexing.creating_chunks' => 'Creando fragmentos del documento',
    'indexing.chunks_created' => 'Fragmentos creados exitosamente',
    'indexing.generating_embeddings' => 'Generando embeddings para fragmentos',
    'indexing.embeddings_generated' => 'Embeddings generados exitosamente',
    'indexing.completed' => 'Indexación de documento completada',
    'indexing.failed' => 'Error en la indexación del documento',

    // === DOCUMENT RE-INDEXING ===
    'reindexing.started' => 'Iniciando re-indexación de documento',
    'reindexing.deleting_old_chunks' => 'Eliminando fragmentos anteriores',
    'reindexing.creating_new_chunks' => 'Creando nuevos fragmentos',
    'reindexing.generating_embeddings' => 'Generando nuevos embeddings',
    'reindexing.completed' => 'Re-indexación completada exitosamente',
    'reindexing.failed' => 'Error en la re-indexación del documento',

    // === CHUNKING OPERATIONS (Debug Level) ===
    'chunking.sections_count_check' => 'Verificando número de secciones encontradas',
    'chunking.processing_multiple_sections' => 'Procesando documento con múltiples secciones',
    'chunking.processing_section' => 'Procesando sección',
    'chunking.section_chunked' => 'Sección dividida en fragmentos',
    'chunking.processing_single_section' => 'Procesando documento con sección única',
    'chunking.text_chunked' => 'Texto dividido en fragmentos',
    'chunking.extracting_sections' => 'Extrayendo secciones del contenido',
    'chunking.lines_count' => 'Contando líneas del documento',
    'chunking.sections_extracted' => 'Secciones extraídas exitosamente',
    'chunking.creating_chunk' => 'Creando fragmento',
    'chunking.chunk_created' => 'Fragmento creado exitosamente',

    // === BULK INDEXING ===
    'bulk_indexing.started' => 'Iniciando indexación masiva de documentos',
    'bulk_indexing.document_failed' => 'Error en la indexación de documento en lote',
    'bulk_indexing.completed' => 'Indexación masiva completada',

    // === DOCUMENT DELETION ===
    'delete.started' => 'Iniciando eliminación de documento',
    'delete.completed' => 'Documento eliminado exitosamente',
    'delete.failed' => 'Error al eliminar documento',

    // === ERROR MESSAGES ===
    'error.index_failed' => 'No se puede indexar el documento. Inténtelo más tarde.',
    'error.reindex_failed' => 'No se puede re-indexar el documento. Inténtelo más tarde.',
    'error.delete_failed' => 'No se puede eliminar el documento. Inténtelo más tarde.',
    'error.bulk_index_failed' => 'Error durante la indexación masiva. Algunos documentos pueden no haberse indexado.',
    'error.embedding_failed' => 'Error al generar embeddings. Verifique la configuración de OpenAI.',
    'error.chunking_failed' => 'Error al dividir el contenido en fragmentos.',

    // === EMBEDDING OPERATIONS ===
    'embedding.empty_input' => 'Entrada vacía para generación de embeddings',
    'embedding.missing_api_key' => 'Clave API de OpenAI no configurada',
    'embedding.generating' => 'Generando embeddings',
    'embedding.generated' => 'Embeddings generados exitosamente',
    'embedding.api_error' => 'Error de API de OpenAI durante generación de embeddings',
    'embedding.exception' => 'Excepción durante generación de embeddings',
    'embedding.batch_started' => 'Iniciando generación de embeddings en lote',
    'embedding.processing_batch' => 'Procesando lote de embeddings',
    'embedding.batch_completed' => 'Generación de embeddings en lote completada',
    'embedding.batch_failed' => 'Error durante generación de embeddings en lote',
    'embedding.stored' => 'Embedding guardado en base de datos',
    'embedding.store_failed' => 'Error al guardar embedding en base de datos',
];
