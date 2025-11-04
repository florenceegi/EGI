<?php

/**
 * Dual Architecture EGI - UI Messages
 *
 * French translations for user interface messages related
 * to Auto-Mint and Pre-Mint EGI management.
 *
 * @package FlorenceEGI\Translations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - Dual Architecture + AI Traits Modal)
 * @date 2025-11-04
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Auto-Mint Messages
    |--------------------------------------------------------------------------
    */
    'auto_mint_enabled' => 'Auto-Mint activé avec succès. Vous pouvez maintenant minter votre EGI quand vous voulez.',
    'auto_mint_disabled' => 'Auto-Mint désactivé. L\'EGI est maintenant disponible à la vente sur le marketplace.',

    /*
    |--------------------------------------------------------------------------
    | AI Analysis Messages
    |--------------------------------------------------------------------------
    */
    'ai_analysis_requested' => 'Demande d\'analyse IA envoyée avec succès. N.A.T.A.N traitera vos données sous peu.',
    'description_generated' => 'Description générée avec succès par N.A.T.A.N IA et enregistrée dans votre EGI.',
    'description_improved' => 'Description améliorée avec succès par N.A.T.A.N IA et enregistrée dans votre EGI.',

    /*
    |--------------------------------------------------------------------------
    | Promotion Messages
    |--------------------------------------------------------------------------
    */
    'promotion_initiated' => 'Promotion blockchain lancée. La transaction est en cours de traitement sur le réseau Algorand.',

    /*
    |--------------------------------------------------------------------------
    | EGI Type Labels
    |--------------------------------------------------------------------------
    */
    'type' => [
        'ASA' => 'EGI Classique',
        'SmartContract' => 'EGI Vivant',
        'PreMint' => 'Pre-Mint',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Messages
    |--------------------------------------------------------------------------
    */
    'status' => [
        'pre_mint_active' => 'Pre-Mint Actif',
        'auto_mint_enabled' => 'Auto-Mint Activé',
        'minting_in_progress' => 'Minting en Cours',
        'minted' => 'Minté',
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Traits Generation Messages
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'unauthorized' => 'Vous devez être authentifié pour utiliser cette fonctionnalité.',
        'forbidden' => 'Vous n\'avez pas la permission d\'accéder à cette ressource.',
        'generation_started' => 'Génération de traits IA lancée avec succès ! N.A.T.A.N analyse l\'image.',
        'generation_failed' => 'Une erreur s\'est produite lors de la génération des traits. Veuillez réessayer plus tard.',
        'generation_not_found' => 'Session de génération introuvable.',
        'review_completed' => 'Révision des propositions terminée avec succès.',
        'review_failed' => 'Une erreur s\'est produite lors de la révision. Veuillez réessayer plus tard.',
        'traits_applied' => 'Traits appliqués avec succès à votre EGI !',
        'apply_failed' => 'Une erreur s\'est produite lors de l\'application des traits. Veuillez réessayer plus tard.',

        // UI Labels
        'generate_traits' => 'Générer des Traits avec l\'IA',
        'requested_count' => 'Nombre de traits à générer',
        'trait_proposals' => 'Propositions de Traits',
        'confidence' => 'Confiance',
        'match_type' => 'Type de Correspondance',
        'exact_match' => 'Correspondance Exacte',
        'fuzzy_match' => 'Correspondance Approximative',
        'new_value' => 'Nouvelle Valeur',
        'new_type' => 'Nouveau Type',
        'new_category' => 'Nouvelle Catégorie',
        'approve' => 'Approuver',
        'reject' => 'Rejeter',
        'modify' => 'Modifier',
        'apply_traits' => 'Appliquer les Traits Approuvés',
        'analyzing' => 'N.A.T.A.N analyse...',
        'pending_review' => 'En Attente de Révision',
        'approved' => 'Approuvé',
        'rejected' => 'Rejeté',
        'applied' => 'Appliqué',
        
        // Modal UI Labels (v2.0)
        'review_proposals_modal' => 'Réviser les Propositions IA',
        'proposals_modal_title' => 'Propositions de Traits IA',
        'close_modal' => 'Fermer',
        'approve_all' => 'Tout Approuver',
        'reject_all' => 'Tout Rejeter',
        'apply_selected' => 'Appliquer',
    ],
];


