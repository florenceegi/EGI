<?php

/**
 * Dual Architecture EGI - UI Messages
 *
 * Portuguese translations for user interface messages related
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
    'auto_mint_enabled' => 'Auto-Mint ativado com sucesso. Você pode agora cunhar seu EGI quando quiser.',
    'auto_mint_disabled' => 'Auto-Mint desativado. O EGI agora está disponível para venda no marketplace.',

    /*
    |--------------------------------------------------------------------------
    | AI Analysis Messages
    |--------------------------------------------------------------------------
    */
    'ai_analysis_requested' => 'Solicitação de análise de IA enviada com sucesso. N.A.T.A.N processará seus dados em breve.',
    'description_generated' => 'Descrição gerada com sucesso pela IA N.A.T.A.N e salva no seu EGI.',
    'description_improved' => 'Descrição melhorada com sucesso pela IA N.A.T.A.N e salva no seu EGI.',

    /*
    |--------------------------------------------------------------------------
    | Promotion Messages
    |--------------------------------------------------------------------------
    */
    'promotion_initiated' => 'Promoção blockchain iniciada. A transação está sendo processada na rede Algorand.',

    /*
    |--------------------------------------------------------------------------
    | EGI Type Labels
    |--------------------------------------------------------------------------
    */
    'type' => [
        'ASA' => 'EGI Clássico',
        'SmartContract' => 'EGI Vivo',
        'PreMint' => 'Pre-Mint',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Messages
    |--------------------------------------------------------------------------
    */
    'status' => [
        'pre_mint_active' => 'Pre-Mint Ativo',
        'auto_mint_enabled' => 'Auto-Mint Ativado',
        'minting_in_progress' => 'Cunhagem em Progresso',
        'minted' => 'Cunhado',
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Traits Generation Messages
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'unauthorized' => 'Você deve estar autenticado para usar esta funcionalidade.',
        'forbidden' => 'Você não tem permissão para acessar este recurso.',
        'generation_started' => 'Geração de traits com IA iniciada com sucesso! N.A.T.A.N está analisando a imagem.',
        'generation_failed' => 'Ocorreu um erro durante a geração de traits. Por favor, tente novamente mais tarde.',
        'generation_not_found' => 'Sessão de geração não encontrada.',
        'review_completed' => 'Revisão de propostas concluída com sucesso.',
        'review_failed' => 'Ocorreu um erro durante a revisão. Por favor, tente novamente mais tarde.',
        'traits_applied' => 'Traits aplicados com sucesso ao seu EGI!',
        'apply_failed' => 'Ocorreu um erro ao aplicar os traits. Por favor, tente novamente mais tarde.',

        // UI Labels
        'generate_traits' => 'Gerar Traits com IA',
        'requested_count' => 'Número de traits a gerar',
        'trait_proposals' => 'Propostas de Traits',
        'confidence' => 'Confiança',
        'match_type' => 'Tipo de Correspondência',
        'exact_match' => 'Correspondência Exata',
        'fuzzy_match' => 'Correspondência Aproximada',
        'new_value' => 'Novo Valor',
        'new_type' => 'Novo Tipo',
        'new_category' => 'Nova Categoria',
        'approve' => 'Aprovar',
        'reject' => 'Rejeitar',
        'modify' => 'Modificar',
        'apply_traits' => 'Aplicar Traits Aprovados',
        'analyzing' => 'N.A.T.A.N está analisando...',
        'pending_review' => 'Aguardando Revisão',
        'approved' => 'Aprovado',
        'rejected' => 'Rejeitado',
        'applied' => 'Aplicado',
        
        // Modal UI Labels (v2.0)
        'review_proposals_modal' => 'Revisar Propostas de IA',
        'proposals_modal_title' => 'Propostas de Traits IA',
        'close_modal' => 'Fechar',
        'approve_all' => 'Aprovar Todos',
        'reject_all' => 'Rejeitar Todos',
        'apply_selected' => 'Aplicar',
    ],
];


