<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CoA Traits Management - Traduções em Português
    |--------------------------------------------------------------------------
    |
    | Traduções para o sistema de gestão de características (traits) do CoA em FlorenceEGI
    | Usado pelos componentes do modal de vocabulário e gestão de características
    |
    */

    // Gestão de Características
    'management_title' => 'Gestão de Características do CoA',
    'management_description' => 'Configure as características técnicas da obra para o Certificado de Autenticidade',
    'status_configured' => 'Configurado',
    'status_not_configured' => 'Não Configurado',
    'edit_traits' => 'Editar Características',
    'last_updated' => 'Última atualização',
    'never_configured' => 'Nunca configurado',
    'clear_all' => 'Limpar Tudo',
    'saved' => 'Salvo',
    'custom' => 'personalizado',
    'issue_certificate_confirm' => 'Tem certeza de que deseja emitir o certificado? Você não poderá modificar os traits após a emissão.',
    'issue_certificate' => 'Emitir Certificado',

    // Categorias
    'category_technique' => 'Técnica',
    'category_materials' => 'Materiais',
    'category_support' => 'Suporte',
    'category_generic' => 'Genérico',

    // Seleções por Categoria
    'no_technique_selected' => 'Nenhuma técnica selecionada',
    'no_materials_selected' => 'Nenhum material selecionado',
    'no_support_selected' => 'Nenhum suporte selecionado',

    // Modal de Vocabulário
    'terms' => 'termo',
    'modal_title' => 'Selecione as Características do CoA',
    'search_placeholder' => 'Procurar termos...',
    'loading' => 'A carregar...',
    'selected_items' => 'Itens Selecionados',
    'no_items_selected' => 'Nenhum item selecionado',
    'add_custom' => 'Adicionar Personalizado',
    'custom_term_placeholder' => 'Insira um termo personalizado (máx. 60 caracteres)',
    'add' => 'Adicionar',
    'cancel' => 'Cancelar',
    'items_selected' => 'itens selecionados',
    'confirm' => 'Confirmar',

    // Componentes de Vocabulário - Categorias
    'terms_available' => 'termos disponíveis',
    'no_categories_available' => 'Nenhuma categoria disponível',
    'no_categories_found' => 'Não foram encontradas categorias de vocabulário.',

    // Componentes de Vocabulário - Termos
    'categories' => 'Categorias',
    'terms_found' => 'termos encontrados',
    'no_terms_available' => 'Nenhum termo disponível',
    'no_terms_found_category' => 'Não foram encontrados termos para a categoria',

    // Componentes de Vocabulário - Pesquisa
    'search_results' => 'Resultados da pesquisa',
    'results_for' => 'Para',
    'results_found' => 'resultados encontrados',
    'no_results_found' => 'Nenhum resultado encontrado',
    'no_terms_match_search' => 'Nenhum termo corresponde à pesquisa',
    'in_category' => 'na categoria',
    'in_all_categories' => 'em todas as categorias',
    'clear_search' => 'Limpar pesquisa',

    // Componentes de Vocabulário - Erros
    'error' => 'Erro',
    'unexpected_error' => 'Ocorreu um erro inesperado.',
    'retry' => 'Tentar novamente',
    'back_to_start' => 'Voltar ao início',

    // Erros específicos do modal
    'errors' => [
        'modal_not_ready' => 'O sistema de seleção de vocabulário ainda não foi carregado. Tente novamente em alguns segundos.',
        'modal_malfunction' => 'Erro no sistema de seleção. Recarregue a página e tente novamente.',
    ],

    // PDF Professional New - Additional Keys
    'pdf_certificate_id' => 'ID Certificado',
    'category_platform_metadata' => 'Metadados Plataforma',
    'pdf_verification_title' => 'Verificação Certificado',
    'pdf_scan_prompt' => 'Escaneie o código QR para verificar a autenticidade do certificado online',
    'pdf_additional_info_title' => 'Informações Adicionais',
    'pdf_stamp_area' => 'Área Carimbo',
    'pdf_stamp_caption' => 'Carimbo Autor',
    'pdf_author_signature' => 'Assinatura Autor',
    'pdf_core_certificate' => 'Certificado Base',

    // Common Fallbacks
    'not_available' => 'N/D',
];