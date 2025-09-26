<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CoA Traits Management - Spanish Translations
    |--------------------------------------------------------------------------
    |
    | Translations for CoA traits management system in FlorenceEGI
    | Used by vocabulary modal and traits management components
    |
    */

    // Traits Management
    'management_title' => 'Gestión de Traits CoA',
    'management_description' => 'Configura las características técnicas de la obra para el Certificado de Autenticidad',
    'status_configured' => 'Configurado',
    'status_not_configured' => 'No Configurado',
    'edit_traits' => 'Editar Traits',
    'last_updated' => 'Última actualización',
    'never_configured' => 'Nunca configurado',
    'clear_all' => 'Limpiar Todo',
    'saved' => 'Guardado',
    'custom' => 'personalizado',
    'issue_certificate_confirm' => '¿Estás seguro de que quieres emitir el certificado? No podrás modificar los traits después de la emisión.',
    'issue_certificate' => 'Emitir Certificado',

    // Categories
    'category_technique' => 'Técnica',
    'category_materials' => 'Materiales',
    'category_support' => 'Soporte',
    'category_generic' => 'Genérico',

    // Category Selections
    'no_technique_selected' => 'Ninguna técnica seleccionada',
    'no_materials_selected' => 'Ningún material seleccionado',
    'no_support_selected' => 'Ningún soporte seleccionado',

    // Vocabulary Modal
    'terms' => 'término',
    'modal_title' => 'Seleccionar Traits CoA',
    'search_placeholder' => 'Buscar términos...',
    'loading' => 'Cargando...',
    'selected_items' => 'Elementos Seleccionados',
    'no_items_selected' => 'Ningún elemento seleccionado',
    'add_custom' => 'Agregar Personalizado',
    'custom_term_placeholder' => 'Ingresar término personalizado (máx 60 caracteres)',
    'add' => 'Agregar',
    'cancel' => 'Cancelar',
    'items_selected' => 'elementos seleccionados',
    'confirm' => 'Confirmar',

    // Vocabulary Components - Categories
    'terms_available' => 'términos disponibles',
    'no_categories_available' => 'No hay categorías disponibles',
    'no_categories_found' => 'No se encontraron categorías de vocabulario.',

    // Vocabulary Components - Terms
    'categories' => 'Categorías',
    'terms_found' => 'términos encontrados',
    'no_terms_available' => 'No hay términos disponibles',
    'no_terms_found_category' => 'No se encontraron términos para la categoría',

    // Vocabulary Components - Search
    'search_results' => 'Resultados de búsqueda',
    'results_for' => 'Para',
    'results_found' => 'resultados encontrados',
    'no_results_found' => 'No se encontraron resultados',
    'no_terms_match_search' => 'Ningún término coincide con la búsqueda',
    'in_category' => 'en categoría',
    'clear_search' => 'Limpiar búsqueda',

    // Vocabulary Components - Errors
    'error' => 'Error',
    'unexpected_error' => 'Ocurrió un error inesperado.',
    'retry' => 'Reintentar',
    'back_to_start' => 'Volver al inicio',

    // Modal specific errors
    'errors' => [
        'modal_not_ready' => 'El sistema de selección de vocabulario aún no está cargado. Inténtelo de nuevo en unos segundos.',
        'modal_malfunction' => 'Error en el sistema de selección. Recargue la página e inténtelo de nuevo.',
    ],

    // PDF Professional New - Additional Keys
    'pdf_certificate_id' => 'ID Certificado',
    'category_platform_metadata' => 'Metadatos Plataforma',
    'pdf_verification_title' => 'Verificación Certificado',
    'pdf_scan_prompt' => 'Escanee el código QR para verificar la autenticidad del certificado en línea',
    'pdf_additional_info_title' => 'Información Adicional',
    'pdf_stamp_area' => 'Área Sello',
    'pdf_stamp_caption' => 'Sello Autor',
    'pdf_author_signature' => 'Firma Autor',
    'pdf_core_certificate' => 'Certificado Base',

    // Common Fallbacks
    'not_available' => 'N/D',
];