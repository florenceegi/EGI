<?php

/**
 * @package Resources\Lang\Fr
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - RAG System)
 * @date 2026-02-06
 * @purpose Traductions pour système RAG (Retrieval-Augmented Generation) - NATAN
 */

return [
    // === DOCUMENT INDEXING ===
    'indexing.started' => 'Démarrage de l\'indexation du document',
    'indexing.creating_document' => 'Création de l\'enregistrement du document',
    'indexing.document_created' => 'Document créé avec succès',
    'indexing.creating_chunks' => 'Création de fragments à partir du document',
    'indexing.chunks_created' => 'Fragments créés avec succès',
    'indexing.generating_embeddings' => 'Génération d\'embeddings pour les fragments',
    'indexing.embeddings_generated' => 'Embeddings générés avec succès',
    'indexing.completed' => 'Indexation du document terminée',
    'indexing.failed' => 'Échec de l\'indexation du document',

    // === DOCUMENT RE-INDEXING ===
    'reindexing.started' => 'Démarrage de la ré-indexation du document',
    'reindexing.deleting_old_chunks' => 'Suppression des fragments précédents',
    'reindexing.creating_new_chunks' => 'Création de nouveaux fragments',
    'reindexing.generating_embeddings' => 'Génération de nouveaux embeddings',
    'reindexing.completed' => 'Ré-indexation terminée avec succès',
    'reindexing.failed' => 'Échec de la ré-indexation du document',

    // === CHUNKING OPERATIONS (Debug Level) ===
    'chunking.sections_count_check' => 'Vérification du nombre de sections trouvées',
    'chunking.processing_multiple_sections' => 'Traitement du document avec plusieurs sections',
    'chunking.processing_section' => 'Traitement de la section',
    'chunking.section_chunked' => 'Section divisée en fragments',
    'chunking.processing_single_section' => 'Traitement du document avec une seule section',
    'chunking.text_chunked' => 'Texte divisé en fragments',
    'chunking.extracting_sections' => 'Extraction des sections du contenu',
    'chunking.lines_count' => 'Comptage des lignes du document',
    'chunking.sections_extracted' => 'Sections extraites avec succès',
    'chunking.creating_chunk' => 'Création du fragment',
    'chunking.chunk_created' => 'Fragment créé avec succès',

    // === BULK INDEXING ===
    'bulk_indexing.started' => 'Démarrage de l\'indexation en masse des documents',
    'bulk_indexing.document_failed' => 'Échec de l\'indexation du document en lot',
    'bulk_indexing.completed' => 'Indexation en masse terminée',

    // === DOCUMENT DELETION ===
    'delete.started' => 'Démarrage de la suppression du document',
    'delete.completed' => 'Document supprimé avec succès',
    'delete.failed' => 'Échec de la suppression du document',

    // === ERROR MESSAGES ===
    'error.index_failed' => 'Impossible d\'indexer le document. Veuillez réessayer plus tard.',
    'error.reindex_failed' => 'Impossible de ré-indexer le document. Veuillez réessayer plus tard.',
    'error.delete_failed' => 'Impossible de supprimer le document. Veuillez réessayer plus tard.',
    'error.bulk_index_failed' => 'Erreur lors de l\'indexation en masse. Certains documents n\'ont peut-être pas été indexés.',
    'error.embedding_failed' => 'Erreur lors de la génération des embeddings. Veuillez vérifier la configuration OpenAI.',
    'error.chunking_failed' => 'Erreur lors de la division du contenu en fragments.',

    // === EMBEDDING OPERATIONS ===
    'embedding.empty_input' => 'Entrée vide pour la génération d\'embeddings',
    'embedding.missing_api_key' => 'Clé API OpenAI non configurée',
    'embedding.generating' => 'Génération d\'embeddings en cours',
    'embedding.generated' => 'Embeddings générés avec succès',
    'embedding.api_error' => 'Erreur API OpenAI lors de la génération d\'embeddings',
    'embedding.exception' => 'Exception lors de la génération d\'embeddings',
    'embedding.batch_started' => 'Démarrage de la génération d\'embeddings par lots',
    'embedding.processing_batch' => 'Traitement du lot d\'embeddings',
    'embedding.batch_completed' => 'Génération d\'embeddings par lots terminée',
    'embedding.batch_failed' => 'Erreur lors de la génération d\'embeddings par lots',
    'embedding.stored' => 'Embedding sauvegardé dans la base de données',
    'embedding.store_failed' => 'Erreur lors de la sauvegarde de l\'embedding dans la base de données',

    // === QUERY OPERATIONS ===
    'query.processing_started' => 'Démarrage du traitement de la requête utilisateur',
    'query.cache_hit' => 'Réponse trouvée dans le cache',
    'query.cache_miss' => 'Réponse absente du cache, génération d\'une nouvelle réponse',
    'query.response_created' => 'Réponse créée avec succès',
    'query.processing_completed' => 'Traitement de la requête terminé',
    'query.processing_failed' => 'Échec du traitement de la requête',
    'query.recording_feedback' => 'Enregistrement des commentaires de l\'utilisateur',
    'query.feedback_recorded' => 'Commentaires enregistrés avec succès',
    'query.feedback_recording_failed' => 'Erreur lors de l\'enregistrement des commentaires',
    'query.fetching_user_history' => 'Récupération de l\'historique des requêtes utilisateur',
    'query.user_history_fetched' => 'Historique utilisateur récupéré avec succès',
    'query.user_history_failed' => 'Erreur lors de la récupération de l\'historique utilisateur',
    'query.fetching_analytics' => 'Récupération des analytics de requêtes',
    'query.analytics_fetched' => 'Analytics récupérés avec succès',
    'query.analytics_failed' => 'Erreur lors de la récupération des analytics',

    // === RESPONSE GENERATION ===
    'response.claude_api_error' => 'Erreur API Claude lors de la génération de réponse',
    'response.claude_exception' => 'Exception lors de l\'appel à Claude',
    'response.openai_api_error' => 'Erreur API OpenAI lors de la génération de réponse',
    'response.openai_exception' => 'Exception lors de l\'appel à OpenAI',
    'error.response_generation_failed' => 'Erreur lors de la génération de la réponse. Veuillez réessayer plus tard.',
];
