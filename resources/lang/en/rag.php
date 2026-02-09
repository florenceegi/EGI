<?php

/**
 * @package Resources\Lang\En
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - RAG System)
 * @date 2026-02-06
 * @purpose Translations for RAG system (Retrieval-Augmented Generation) - NATAN
 */

return [
    // === CATEGORIES ===
    'categories' => [
        // TIER 1 - CRITICAL
        'getting_started' => [
            'name' => 'Getting Started',
            'description' => 'First steps on the FlorenceEGI platform'
        ],
        'security' => [
            'name' => 'Security & Account',
            'description' => 'Account management, passwords, authentication and security'
        ],
        'privacy_gdpr' => [
            'name' => 'Privacy & GDPR',
            'description' => 'Personal data protection, consent and GDPR rights'
        ],
        'troubleshooting' => [
            'name' => 'Troubleshooting',
            'description' => 'FAQ and solutions to common problems'
        ],
        'support' => [
            'name' => 'Help & Support',
            'description' => 'How to contact support and get assistance'
        ],

        // CORE FUNCTIONAL
        'platform' => [
            'name' => 'Platform',
            'description' => 'Fundamentals, vision and architecture of FlorenceEGI platform'
        ],
        'architecture' => [
            'name' => 'Technical Architecture',
            'description' => 'Technical architecture, tech stack and design patterns'
        ],
        'payments' => [
            'name' => 'Payments',
            'description' => 'Payment system, Stripe, payment methods and transactions'
        ],
        'billing' => [
            'name' => 'Billing',
            'description' => 'Invoices, receipts and tax documentation'
        ],
        'royalty' => [
            'name' => 'Royalties',
            'description' => 'Royalty system, calculation and automatic distribution'
        ],
        'fiscal' => [
            'name' => 'Tax & Fiscal',
            'description' => 'Tax management, taxes and fiscal compliance'
        ],
        'blockchain' => [
            'name' => 'Blockchain',
            'description' => 'Blockchain technology, NFTs and artwork certification'
        ],
        'wallet' => [
            'name' => 'Wallet & Crypto',
            'description' => 'Wallet management, cryptocurrencies and blockchain transactions'
        ],
        'rebind' => [
            'name' => 'Secondary Market',
            'description' => 'Rebind, artwork resale and secondary market'
        ],
        'collections' => [
            'name' => 'Collections',
            'description' => 'Collection management, artwork upload and organization'
        ],

        // TIER 2 - IMPORTANT
        'media_management' => [
            'name' => 'Media Management',
            'description' => 'Upload, optimization and management of images and videos'
        ],
        'verification_kyc' => [
            'name' => 'Verification & KYC',
            'description' => 'Identity verification and KYC process'
        ],
        'search_discovery' => [
            'name' => 'Search & Discovery',
            'description' => 'Artwork search, filters and discovery algorithms'
        ],
        'quality_standards' => [
            'name' => 'Quality Standards',
            'description' => 'Quality guidelines for artworks and content'
        ],
        'legal_compliance' => [
            'name' => 'Legal & Compliance',
            'description' => 'Legal aspects, terms of service and regulatory compliance'
        ],
        'refunds_disputes' => [
            'name' => 'Refunds & Disputes',
            'description' => 'Refund management, claims and disputes'
        ],

        // TIER 3 - NICE-TO-HAVE
        'export_import' => [
            'name' => 'Export/Import Data',
            'description' => 'Export and import of personal data'
        ],
        'social_features' => [
            'name' => 'Social Features',
            'description' => 'Profiles, followers, social interactions'
        ],
        'promotions' => [
            'name' => 'Promotions & Marketing',
            'description' => 'Promotional tools, coupons and marketing'
        ],
        'mobile_app' => [
            'name' => 'Mobile App',
            'description' => 'Mobile application and features'
        ],
        'api_advanced' => [
            'name' => 'API & Advanced Features',
            'description' => 'Developer APIs and advanced features'
        ],
        'accessibility' => [
            'name' => 'Accessibility',
            'description' => 'Accessibility features for users with disabilities'
        ],

        // SPECIALIZED
        'ai_natan' => [
            'name' => 'AI & NATAN',
            'description' => 'NATAN AI system, virtual assistant and intelligent features'
        ],
        'oracode' => [
            'name' => 'Oracode OS3.0',
            'description' => 'Oracode framework, patterns and development standards'
        ],
        'development' => [
            'name' => 'Development & Developers',
            'description' => 'Technical documentation for developers'
        ],
        'glossary' => [
            'name' => 'Glossary',
            'description' => 'Terminology, definitions and technical glossary'
        ],
    ],

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

    // === QUERY OPERATIONS ===
    'query.processing_started' => 'Starting user query processing',
    'query.cache_hit' => 'Response found in cache',
    'query.cache_miss' => 'Response not in cache, generating new response',
    'query.response_created' => 'Response created successfully',
    'query.processing_completed' => 'Query processing completed',
    'query.processing_failed' => 'Query processing failed',
    'query.recording_feedback' => 'Recording user feedback',
    'query.feedback_recorded' => 'Feedback recorded successfully',
    'query.feedback_recording_failed' => 'Error recording feedback',
    'query.fetching_user_history' => 'Fetching user query history',
    'query.user_history_fetched' => 'User history fetched successfully',
    'query.user_history_failed' => 'Error fetching user history',
    'query.fetching_analytics' => 'Fetching query analytics',
    'query.analytics_fetched' => 'Analytics fetched successfully',
    'query.analytics_failed' => 'Error fetching analytics',

    // === RESPONSE GENERATION ===
    'response.claude_api_error' => 'Claude API error during response generation',
    'response.claude_exception' => 'Exception during Claude call',
    'response.openai_api_error' => 'OpenAI API error during response generation',
    'response.openai_exception' => 'Exception during OpenAI call',
    'error.response_generation_failed' => 'Error generating response. Please try again later.',

    // === ERROR MESSAGES ===
    'error.index_failed' => 'Unable to index document. Please try again later.',
    'error.reindex_failed' => 'Unable to re-index document. Please try again later.',
    'error.delete_failed' => 'Unable to delete document. Please try again later.',
    'error.bulk_index_failed' => 'Error during bulk indexing. Some documents may not have been indexed.',
    'error.embedding_failed' => 'Error generating embeddings. Please check OpenAI configuration.',
    'error.chunking_failed' => 'Error splitting content into chunks.',
];
