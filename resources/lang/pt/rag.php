<?php

/**
 * @package Resources\Lang\Pt
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - RAG System)
 * @date 2026-02-06
 * @purpose Traduções para sistema RAG (Retrieval-Augmented Generation) - NATAN
 */

return [
    // === CATEGORIES ===
    'categories' => [
        // TIER 1 - CRITICAL
        'getting_started' => [
            'name' => 'Guia Rápido',
            'description' => 'Primeiros passos na plataforma FlorenceEGI'
        ],
        'security' => [
            'name' => 'Segurança e Conta',
            'description' => 'Gestão de conta, palavras-passe, autenticação e segurança'
        ],
        'privacy_gdpr' => [
            'name' => 'Privacidade e RGPD',
            'description' => 'Proteção de dados pessoais, consentimentos e direitos RGPD'
        ],
        'troubleshooting' => [
            'name' => 'Resolução de Problemas',
            'description' => 'FAQ e soluções para problemas comuns'
        ],
        'support' => [
            'name' => 'Ajuda e Suporte',
            'description' => 'Como contactar o suporte e obter assistência'
        ],

        // CORE FUNCTIONAL
        'platform' => [
            'name' => 'Plataforma',
            'description' => 'Fundamentos, visão e arquitetura da plataforma FlorenceEGI'
        ],
        'architecture' => [
            'name' => 'Arquitetura Técnica',
            'description' => 'Arquitetura técnica, stack tecnológico e padrões de design'
        ],
        'payments' => [
            'name' => 'Pagamentos',
            'description' => 'Sistema de pagamentos, Stripe, métodos de pagamento e transações'
        ],
        'billing' => [
            'name' => 'Faturação',
            'description' => 'Faturas, recibos e documentação fiscal'
        ],
        'royalty' => [
            'name' => 'Royalties',
            'description' => 'Sistema de royalties, cálculo e distribuição automática'
        ],
        'fiscal' => [
            'name' => 'Fiscalidade',
            'description' => 'Gestão fiscal, impostos e conformidade fiscal'
        ],
        'blockchain' => [
            'name' => 'Blockchain',
            'description' => 'Tecnologia blockchain, NFTs e certificação de obras'
        ],
        'wallet' => [
            'name' => 'Carteira e Cripto',
            'description' => 'Gestão de carteira, criptomoedas e transações blockchain'
        ],
        'rebind' => [
            'name' => 'Mercado Secundário',
            'description' => 'Rebind, revenda de obras e mercado secundário'
        ],
        'collections' => [
            'name' => 'Coleções',
            'description' => 'Gestão de coleções, carregamento e organização de obras'
        ],

        // TIER 2 - IMPORTANT
        'media_management' => [
            'name' => 'Gestão de Média',
            'description' => 'Carregamento, otimização e gestão de imagens e vídeos'
        ],
        'verification_kyc' => [
            'name' => 'Verificação e KYC',
            'description' => 'Processo de verificação de identidade e KYC'
        ],
        'search_discovery' => [
            'name' => 'Pesquisa e Descoberta',
            'description' => 'Pesquisa de obras, filtros e algoritmos de descoberta'
        ],
        'quality_standards' => [
            'name' => 'Padrões de Qualidade',
            'description' => 'Diretrizes de qualidade para obras e conteúdos'
        ],
        'legal_compliance' => [
            'name' => 'Legal e Conformidade',
            'description' => 'Aspetos legais, termos de serviço e conformidade regulamentar'
        ],
        'refunds_disputes' => [
            'name' => 'Reembolsos e Disputas',
            'description' => 'Gestão de reembolsos, reclamações e disputas'
        ],

        // TIER 3 - NICE-TO-HAVE
        'export_import' => [
            'name' => 'Exportar/Importar Dados',
            'description' => 'Exportação e importação de dados pessoais'
        ],
        'social_features' => [
            'name' => 'Funcionalidades Sociais',
            'description' => 'Perfis, seguidores, interações sociais'
        ],
        'promotions' => [
            'name' => 'Promoções e Marketing',
            'description' => 'Ferramentas promocionais, cupões e marketing'
        ],
        'mobile_app' => [
            'name' => 'App Móvel',
            'description' => 'Aplicação móvel e funcionalidades'
        ],
        'api_advanced' => [
            'name' => 'API e Funcionalidades Avançadas',
            'description' => 'APIs para programadores e funcionalidades avançadas'
        ],
        'accessibility' => [
            'name' => 'Acessibilidade',
            'description' => 'Funcionalidades de acessibilidade para utilizadores com deficiências'
        ],

        // SPECIALIZED
        'ai_natan' => [
            'name' => 'IA e NATAN',
            'description' => 'Sistema IA NATAN, assistente virtual e funcionalidades inteligentes'
        ],
        'oracode' => [
            'name' => 'Oracode OS3.0',
            'description' => 'Framework Oracode, padrões e standards de desenvolvimento'
        ],
        'development' => [
            'name' => 'Desenvolvimento e Programadores',
            'description' => 'Documentação técnica para programadores'
        ],
        'glossary' => [
            'name' => 'Glossário',
            'description' => 'Terminologia, definições e glossário técnico'
        ],
    ],

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

    // === QUERY OPERATIONS ===
    'query.processing_started' => 'Iniciando processamento de consulta do usuário',
    'query.cache_hit' => 'Resposta encontrada em cache',
    'query.cache_miss' => 'Resposta não em cache, gerando nova resposta',
    'query.response_created' => 'Resposta criada com sucesso',
    'query.processing_completed' => 'Processamento de consulta concluído',
    'query.processing_failed' => 'Falha no processamento da consulta',
    'query.recording_feedback' => 'Registrando feedback do usuário',
    'query.feedback_recorded' => 'Feedback registrado com sucesso',
    'query.feedback_recording_failed' => 'Erro ao registrar feedback',
    'query.fetching_user_history' => 'Recuperando histórico de consultas do usuário',
    'query.user_history_fetched' => 'Histórico do usuário recuperado com sucesso',
    'query.user_history_failed' => 'Erro ao recuperar histórico do usuário',
    'query.fetching_analytics' => 'Recuperando analytics de consultas',
    'query.analytics_fetched' => 'Analytics recuperados com sucesso',
    'query.analytics_failed' => 'Erro ao recuperar analytics',

    // === RESPONSE GENERATION ===
    'response.claude_api_error' => 'Erro da API Claude durante geração de resposta',
    'response.claude_exception' => 'Exceção durante chamada ao Claude',
    'response.openai_api_error' => 'Erro da API OpenAI durante geração de resposta',
    'response.openai_exception' => 'Exceção durante chamada ao OpenAI',
    'error.response_generation_failed' => 'Erro ao gerar resposta. Tente novamente mais tarde.',
];
