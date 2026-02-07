<?php

/**
 * @package Resources\Lang\Pt
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - RAG System)
 * @date 2026-02-06
 * @purpose Traduções para sistema RAG (Retrieval-Augmented Generation) - NATAN
 */

return [
    // === DOCUMENT INDEXING ===
    'indexing.started' => 'Iniciando indexação de documento',
    'indexing.creating_document' => 'Criando registro de documento',
    'indexing.document_created' => 'Documento criado com sucesso',
    'indexing.creating_chunks' => 'Criando fragmentos do documento',
    'indexing.chunks_created' => 'Fragmentos criados com sucesso',
    'indexing.generating_embeddings' => 'Gerando embeddings para fragmentos',
    'indexing.embeddings_generated' => 'Embeddings gerados com sucesso',
    'indexing.completed' => 'Indexação de documento concluída',
    'indexing.failed' => 'Falha na indexação do documento',

    // === DOCUMENT RE-INDEXING ===
    'reindexing.started' => 'Iniciando re-indexação de documento',
    'reindexing.deleting_old_chunks' => 'Excluindo fragmentos anteriores',
    'reindexing.creating_new_chunks' => 'Criando novos fragmentos',
    'reindexing.generating_embeddings' => 'Gerando novos embeddings',
    'reindexing.completed' => 'Re-indexação concluída com sucesso',
    'reindexing.failed' => 'Falha na re-indexação do documento',

    // === CHUNKING OPERATIONS (Debug Level) ===
    'chunking.sections_count_check' => 'Verificando número de seções encontradas',
    'chunking.processing_multiple_sections' => 'Processando documento com múltiplas seções',
    'chunking.processing_section' => 'Processando seção',
    'chunking.section_chunked' => 'Seção dividida em fragmentos',
    'chunking.processing_single_section' => 'Processando documento com seção única',
    'chunking.text_chunked' => 'Texto dividido em fragmentos',
    'chunking.extracting_sections' => 'Extraindo seções do conteúdo',
    'chunking.lines_count' => 'Contando linhas do documento',
    'chunking.sections_extracted' => 'Seções extraídas com sucesso',
    'chunking.creating_chunk' => 'Criando fragmento',
    'chunking.chunk_created' => 'Fragmento criado com sucesso',

    // === BULK INDEXING ===
    'bulk_indexing.started' => 'Iniciando indexação em massa de documentos',
    'bulk_indexing.document_failed' => 'Falha na indexação de documento em lote',
    'bulk_indexing.completed' => 'Indexação em massa concluída',

    // === DOCUMENT DELETION ===
    'delete.started' => 'Iniciando exclusão de documento',
    'delete.completed' => 'Documento excluído com sucesso',
    'delete.failed' => 'Falha ao excluir documento',

    // === ERROR MESSAGES ===
    'error.index_failed' => 'Não foi possível indexar o documento. Tente novamente mais tarde.',
    'error.reindex_failed' => 'Não foi possível re-indexar o documento. Tente novamente mais tarde.',
    'error.delete_failed' => 'Não foi possível excluir o documento. Tente novamente mais tarde.',
    'error.bulk_index_failed' => 'Erro durante indexação em massa. Alguns documentos podem não ter sido indexados.',
    'error.embedding_failed' => 'Erro ao gerar embeddings. Verifique a configuração do OpenAI.',
    'error.chunking_failed' => 'Erro ao dividir conteúdo em fragmentos.',

    // === EMBEDDING OPERATIONS ===
    'embedding.empty_input' => 'Entrada vazia para geração de embeddings',
    'embedding.missing_api_key' => 'Chave API do OpenAI não configurada',
    'embedding.generating' => 'Gerando embeddings',
    'embedding.generated' => 'Embeddings gerados com sucesso',
    'embedding.api_error' => 'Erro da API OpenAI durante geração de embeddings',
    'embedding.exception' => 'Exceção durante geração de embeddings',
    'embedding.batch_started' => 'Iniciando geração de embeddings em lote',
    'embedding.processing_batch' => 'Processando lote de embeddings',
    'embedding.batch_completed' => 'Geração de embeddings em lote concluída',
    'embedding.batch_failed' => 'Erro durante geração de embeddings em lote',
    'embedding.stored' => 'Embedding salvo no banco de dados',
    'embedding.store_failed' => 'Erro ao salvar embedding no banco de dados',
];
