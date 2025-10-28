<?php

/**
 * @package Resources\Lang\Fr
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 3.0.0 (FlorenceEGI - N.A.T.A.N. Web Search Integration)
 * @date 2025-10-26
 * @purpose N.A.T.A.N. AI chat system translations (PA/Enterprise) - French
 */

return [
    'chat.title' => 'N.A.T.A.N. - Conseil Stratégique IA',
    'chat.subtitle' => 'Interrogez vos documents administratifs avec intelligence artificielle',
    'chat.input_placeholder' => 'Posez une question sur vos documents... (Shift+Enter pour envoyer)',
    'chat.send_button' => 'Envoyer',
    'web_search.toggle_label' => 'Rechercher aussi sur le web',
    'web_search.toggle_hint' => 'Enrichissez les réponses avec les meilleures pratiques mondiales, réglementations à jour et opportunités de financement',
    'web_sources.title' => 'Sources Externes (Web)',
    'web_sources.count' => '{count} résultats du web',
    'persona.select_title' => 'Sélectionner Consultant',
    'persona.auto_mode' => 'Automatique (recommandé)',
    'sources.title' => 'Sources',
    'copy.button' => 'Copier',
    'errors.generic' => 'Une erreur s\'est produite. Veuillez réessayer plus tard.',
    'errors.ai_consent_required' => 'Consentement de traitement IA requis. Veuillez mettre à jour vos paramètres de confidentialité pour utiliser N.A.T.A.N.',

    // === INTELLIGENT CHUNKING - Frontend Errors (Phase 3) ===
    'chunking.timeout_error' => 'Le traitement prend plus de temps que prévu (>5 minutes)',
    'chunking.session_not_found' => 'Session de traitement introuvable. Veuillez réessayer.',
    'chunking.unauthorized' => 'Vous n\'avez pas la permission d\'accéder à cette session de traitement.',
    'chunking.polling_error' => 'Erreur lors de la vérification de l\'état du traitement',
    'chunking.final_error' => 'Erreur lors de la récupération du résultat final',
    'chunking.retry_button' => 'Réessayer l\'Analyse',
];
