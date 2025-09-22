<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CoA Traits Management - French Translations
    |--------------------------------------------------------------------------
    |
    | Translations for CoA traits management system in FlorenceEGI
    | Used by vocabulary modal and traits management components
    |
    */

    // Traits Management
    'management_title' => 'Gestion des Traits CoA',
    'management_description' => 'Configurez les caractéristiques techniques de l\'œuvre pour le Certificat d\'Authenticité',
    'status_configured' => 'Configuré',
    'status_not_configured' => 'Non Configuré',
    'edit_traits' => 'Modifier les Traits',
    'last_updated' => 'Dernière mise à jour',
    'never_configured' => 'Jamais configuré',
    'clear_all' => 'Tout Effacer',
    'saved' => 'Sauvegardé',
    'custom' => 'personnalisé',
    'issue_certificate_confirm' => 'Êtes-vous sûr de vouloir émettre le certificat ? Vous ne pourrez plus modifier les traits après l\'émission.',
    'issue_certificate' => 'Émettre le Certificat',

    // Categories
    'category_technique' => 'Technique',
    'category_materials' => 'Matériaux',
    'category_support' => 'Support',

    // Category Selections
    'no_technique_selected' => 'Aucune technique sélectionnée',
    'no_materials_selected' => 'Aucun matériau sélectionné',
    'no_support_selected' => 'Aucun support sélectionné',

    // Vocabulary Modal
    'terms' => 'terme',
    'modal_title' => 'Sélectionner les Traits CoA',
    'search_placeholder' => 'Rechercher des termes...',
    'loading' => 'Chargement...',
    'selected_items' => 'Éléments Sélectionnés',
    'no_items_selected' => 'Aucun élément sélectionné',
    'add_custom' => 'Ajouter Personnalisé',
    'custom_term_placeholder' => 'Entrer un terme personnalisé (max 60 caractères)',
    'add' => 'Ajouter',
    'cancel' => 'Annuler',
    'items_selected' => 'éléments sélectionnés',
    'confirm' => 'Confirmer',

    // Vocabulary Components - Categories
    'terms_available' => 'termes disponibles',
    'no_categories_available' => 'Aucune catégorie disponible',
    'no_categories_found' => 'Aucune catégorie de vocabulaire trouvée.',

    // Vocabulary Components - Terms
    'categories' => 'Catégories',
    'terms_found' => 'termes trouvés',
    'no_terms_available' => 'Aucun terme disponible',
    'no_terms_found_category' => 'Aucun terme trouvé pour la catégorie',

    // Vocabulary Components - Search
    'search_results' => 'Résultats de recherche',
    'results_for' => 'Pour',
    'results_found' => 'résultats trouvés',
    'no_results_found' => 'Aucun résultat trouvé',
    'no_terms_match_search' => 'Aucun terme ne correspond à la recherche',
    'in_category' => 'dans la catégorie',
    'clear_search' => 'Effacer la recherche',

    // Vocabulary Components - Errors
    'error' => 'Erreur',
    'unexpected_error' => 'Une erreur inattendue s\'est produite.',
    'retry' => 'Réessayer',
    'back_to_start' => 'Retour au début',

    // Modal specific errors
    'errors' => [
        'modal_not_ready' => 'Le système de sélection du vocabulaire n\'est pas encore chargé. Veuillez réessayer dans quelques secondes.',
        'modal_malfunction' => 'Erreur dans le système de sélection. Veuillez recharger la page et réessayer.',
    ],
];
