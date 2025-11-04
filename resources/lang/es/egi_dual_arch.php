<?php

/**
 * Dual Architecture EGI - UI Messages
 *
 * Spanish translations for user interface messages related
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
    'auto_mint_enabled' => 'Auto-Mint activado con éxito. Ahora puedes acuñar tu EGI cuando quieras.',
    'auto_mint_disabled' => 'Auto-Mint desactivado. El EGI ahora está disponible para la venta en el marketplace.',

    /*
    |--------------------------------------------------------------------------
    | AI Analysis Messages
    |--------------------------------------------------------------------------
    */
    'ai_analysis_requested' => 'Solicitud de análisis de IA enviada con éxito. N.A.T.A.N procesará tus datos en breve.',
    'description_generated' => 'Descripción generada con éxito por N.A.T.A.N IA y guardada en tu EGI.',
    'description_improved' => 'Descripción mejorada con éxito por N.A.T.A.N IA y guardada en tu EGI.',

    /*
    |--------------------------------------------------------------------------
    | Promotion Messages
    |--------------------------------------------------------------------------
    */
    'promotion_initiated' => 'Promoción en blockchain iniciada. La transacción se está procesando en la red Algorand.',

    /*
    |--------------------------------------------------------------------------
    | EGI Type Labels
    |--------------------------------------------------------------------------
    */
    'type' => [
        'ASA' => 'EGI Clásico',
        'SmartContract' => 'EGI Viviente',
        'PreMint' => 'Pre-Mint',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Messages
    |--------------------------------------------------------------------------
    */
    'status' => [
        'pre_mint_active' => 'Pre-Mint Activo',
        'auto_mint_enabled' => 'Auto-Mint Activado',
        'minting_in_progress' => 'Acuñación en Progreso',
        'minted' => 'Acuñado',
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Traits Generation Messages
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'unauthorized' => 'Debes estar autenticado para usar esta función.',
        'forbidden' => 'No tienes permisos para acceder a este recurso.',
        'generation_started' => '¡Generación de traits con IA iniciada con éxito! N.A.T.A.N está analizando la imagen.',
        'generation_failed' => 'Se produjo un error durante la generación de traits. Por favor, inténtalo de nuevo más tarde.',
        'generation_not_found' => 'Sesión de generación no encontrada.',
        'review_completed' => 'Revisión de propuestas completada con éxito.',
        'review_failed' => 'Se produjo un error durante la revisión. Por favor, inténtalo de nuevo más tarde.',
        'traits_applied' => '¡Traits aplicados con éxito a tu EGI!',
        'apply_failed' => 'Se produjo un error al aplicar los traits. Por favor, inténtalo de nuevo más tarde.',

        // UI Labels
        'generate_traits' => 'Generar Traits con IA',
        'requested_count' => 'Número de traits a generar',
        'trait_proposals' => 'Propuestas de Traits',
        'confidence' => 'Confianza',
        'match_type' => 'Tipo de Coincidencia',
        'exact_match' => 'Coincidencia Exacta',
        'fuzzy_match' => 'Coincidencia Aproximada',
        'new_value' => 'Valor Nuevo',
        'new_type' => 'Tipo Nuevo',
        'new_category' => 'Categoría Nueva',
        'approve' => 'Aprobar',
        'reject' => 'Rechazar',
        'modify' => 'Modificar',
        'apply_traits' => 'Aplicar Traits Aprobados',
        'analyzing' => 'N.A.T.A.N está analizando...',
        'pending_review' => 'Pendiente de Revisión',
        'approved' => 'Aprobado',
        'rejected' => 'Rechazado',
        'applied' => 'Aplicado',
        
        // Modal UI Labels (v2.0)
        'review_proposals_modal' => 'Revisar Propuestas de IA',
        'proposals_modal_title' => 'Propuestas de Traits IA',
        'close_modal' => 'Cerrar',
        'approve_all' => 'Aprobar Todos',
        'reject_all' => 'Rechazar Todos',
        'apply_selected' => 'Aplicar',
    ],
];


