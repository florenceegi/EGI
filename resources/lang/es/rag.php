<?php

/**
 * @package Resources\Lang\Es
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - RAG System)
 * @date 2026-02-06
 * @purpose Traducciones para sistema RAG (Retrieval-Augmented Generation) - NATAN
 */

return [
    // === CATEGORIES ===
    'categories' => [
        // TIER 1 - CRITICAL
        'getting_started' => [
            'name' => 'Guía Rápida',
            'description' => 'Primeros pasos en la plataforma FlorenceEGI'
        ],
        'security' => [
            'name' => 'Seguridad y Cuenta',
            'description' => 'Gestión de cuenta, contraseñas, autenticación y seguridad'
        ],
        'privacy_gdpr' => [
            'name' => 'Privacidad y GDPR',
            'description' => 'Protección de datos personales, consentimientos y derechos GDPR'
        ],
        'troubleshooting' => [
            'name' => 'Resolución de Problemas',
            'description' => 'FAQ y soluciones a problemas comunes'
        ],
        'support' => [
            'name' => 'Ayuda y Soporte',
            'description' => 'Cómo contactar con soporte y obtener asistencia'
        ],

        // CORE FUNCTIONAL
        'platform' => [
            'name' => 'Plataforma',
            'description' => 'Fundamentos, visión y arquitectura de la plataforma FlorenceEGI'
        ],
        'architecture' => [
            'name' => 'Arquitectura Técnica',
            'description' => 'Arquitectura técnica, stack tecnológico y patrones de diseño'
        ],
        'payments' => [
            'name' => 'Pagos',
            'description' => 'Sistema de pagos, Stripe, métodos de pago y transacciones'
        ],
        'billing' => [
            'name' => 'Facturación',
            'description' => 'Facturas, recibos y documentación fiscal'
        ],
        'royalty' => [
            'name' => 'Regalías',
            'description' => 'Sistema de regalías, cálculo y distribución automática'
        ],
        'fiscal' => [
            'name' => 'Fiscalidad',
            'description' => 'Gestión fiscal, impuestos y cumplimiento fiscal'
        ],
        'blockchain' => [
            'name' => 'Blockchain',
            'description' => 'Tecnología blockchain, NFTs y certificación de obras'
        ],
        'wallet' => [
            'name' => 'Wallet y Cripto',
            'description' => 'Gestión de wallet, criptomonedas y transacciones blockchain'
        ],
        'rebind' => [
            'name' => 'Mercado Secundario',
            'description' => 'Rebind, reventa de obras y mercado secundario'
        ],
        'collections' => [
            'name' => 'Colecciones',
            'description' => 'Gestión de colecciones, carga y organización de obras'
        ],

        // TIER 2 - IMPORTANT
        'media_management' => [
            'name' => 'Gestión de Medios',
            'description' => 'Carga, optimización y gestión de imágenes y vídeos'
        ],
        'verification_kyc' => [
            'name' => 'Verificación y KYC',
            'description' => 'Proceso de verificación de identidad y KYC'
        ],
        'search_discovery' => [
            'name' => 'Búsqueda y Descubrimiento',
            'description' => 'Búsqueda de obras, filtros y algoritmos de descubrimiento'
        ],
        'quality_standards' => [
            'name' => 'Estándares de Calidad',
            'description' => 'Directrices de calidad para obras y contenidos'
        ],
        'legal_compliance' => [
            'name' => 'Legal y Cumplimiento',
            'description' => 'Aspectos legales, términos de servicio y cumplimiento normativo'
        ],
        'refunds_disputes' => [
            'name' => 'Reembolsos y Disputas',
            'description' => 'Gestión de reembolsos, reclamaciones y disputas'
        ],

        // TIER 3 - NICE-TO-HAVE
        'export_import' => [
            'name' => 'Exportar/Importar Datos',
            'description' => 'Exportación e importación de datos personales'
        ],
        'social_features' => [
            'name' => 'Funciones Sociales',
            'description' => 'Perfiles, seguidores, interacciones sociales'
        ],
        'promotions' => [
            'name' => 'Promociones y Marketing',
            'description' => 'Herramientas promocionales, cupones y marketing'
        ],
        'mobile_app' => [
            'name' => 'App Móvil',
            'description' => 'Aplicación móvil y funcionalidades'
        ],
        'api_advanced' => [
            'name' => 'API y Funciones Avanzadas',
            'description' => 'APIs para desarrolladores y funciones avanzadas'
        ],
        'accessibility' => [
            'name' => 'Accesibilidad',
            'description' => 'Funciones de accesibilidad para usuarios con discapacidades'
        ],

        // SPECIALIZED
        'ai_natan' => [
            'name' => 'IA y NATAN',
            'description' => 'Sistema IA NATAN, asistente virtual y funciones inteligentes'
        ],
        'oracode' => [
            'name' => 'Oracode OS3.0',
            'description' => 'Framework Oracode, patrones y estándares de desarrollo'
        ],
        'development' => [
            'name' => 'Desarrollo y Desarrolladores',
            'description' => 'Documentación técnica para desarrolladores'
        ],
        'glossary' => [
            'name' => 'Glosario',
            'description' => 'Terminología, definiciones y glosario técnico'
        ],
    ],

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

    // === QUERY OPERATIONS ===
    'query.processing_started' => 'Iniciando procesamiento de consulta de usuario',
    'query.cache_hit' => 'Respuesta encontrada en caché',
    'query.cache_miss' => 'Respuesta no en caché, generando nueva respuesta',
    'query.response_created' => 'Respuesta creada exitosamente',
    'query.processing_completed' => 'Procesamiento de consulta completado',
    'query.processing_failed' => 'Error en procesamiento de consulta',
    'query.recording_feedback' => 'Registrando comentarios del usuario',
    'query.feedback_recorded' => 'Comentarios registrados exitosamente',
    'query.feedback_recording_failed' => 'Error al registrar comentarios',
    'query.fetching_user_history' => 'Recuperando historial de consultas del usuario',
    'query.user_history_fetched' => 'Historial de usuario recuperado exitosamente',
    'query.user_history_failed' => 'Error al recuperar historial de usuario',
    'query.fetching_analytics' => 'Recuperando analytics de consultas',
    'query.analytics_fetched' => 'Analytics recuperados exitosamente',
    'query.analytics_failed' => 'Error al recuperar analytics',

    // === RESPONSE GENERATION ===
    'response.claude_api_error' => 'Error de API Claude durante generación de respuesta',
    'response.claude_exception' => 'Excepción durante llamada a Claude',
    'response.openai_api_error' => 'Error de API OpenAI durante generación de respuesta',
    'response.openai_exception' => 'Excepción durante llamada a OpenAI',
    'error.response_generation_failed' => 'Error al generar respuesta. Inténtelo más tarde.',
];
