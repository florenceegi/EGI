<?php

return [
    // Nom de catégorie pour badge
    'category_name' => 'Lingot d\'Or',

    // Étiquettes du composant
    'title' => 'Informations Lingot d\'Or',
    'subtitle' => 'Valeur Indicative Basée sur le Prix Actuel de l\'Or',

    // Propriétés or
    'weight' => 'Poids',
    'weight_unit' => 'Unité',
    'purity' => 'Pureté',
    'pure_gold' => 'Contenu Or Pur',

    // Étiquettes valeur
    'gold_price' => 'Prix Spot de l\'Or',
    'creator_margin' => 'Marge Créateur',
    'per_gram' => 'par gramme',
    'per_oz' => 'par once troy',
    'base_value' => 'Valeur Base Or',
    'margin' => 'Marge Créateur',
    'indicative_value' => 'Valeur Indicative',

    // Avertissements
    'disclaimer' => 'Ceci est une valeur indicative basée sur le prix spot actuel de l\'or. Le prix de vente réel est déterminé par le Créateur.',
    'price_updated_at' => 'Prix mis à jour à',
    'price_source' => 'Source',

    // Descriptions pureté
    'purity_999' => '24k - 99.9% Pur',
    'purity_995' => '99.5% Pur',
    'purity_990' => '99.0% Pur',
    'purity_916' => '22k - 91.6% Pur',
    'purity_750' => '18k - 75.0% Pur',

    // Unités de poids
    'unit_grams' => 'Grammes',
    'unit_ounces' => 'Onces',
    'unit_troy_ounces' => 'Onces Troy',

    // Messages d\'état
    'loading' => 'Chargement du prix de l\'or...',
    'error' => 'Impossible de récupérer le prix de l\'or. Veuillez réessayer plus tard.',
    'not_gold_bar' => 'Cet EGI n\'est pas un Lingot d\'Or.',

    // Fonction d\'actualisation
    'refresh_button' => 'Actualiser le Prix',
    'refresh_cost' => 'Coût: :cost Egili',
    'refresh_available_now' => 'Disponible maintenant',
    'next_refresh' => 'Prochaine mise à jour automatique dans :time',
    'refresh_success' => 'Prix de l\'or mis à jour avec succès!',
    'insufficient_egili' => 'Egili insuffisants. Vous avez besoin de :required mais vous avez :current.',
    'refresh_confirm_title' => 'Actualiser le prix de l\'or?',
    'refresh_confirm_message' => 'Cela coûtera :cost Egili de votre solde. Le prix sera mis à jour avec des données en temps réel.',
    'refresh_confirm_button' => 'Actualiser pour :cost Egili',
    'refresh_cancel' => 'Annuler',

    // Section marge CRUD
    'margin' => [
        'title' => 'Marge du Lingot d\'Or',
        'description' => 'Définissez votre marge sur la valeur du lingot d\'or. Vous pouvez utiliser un pourcentage, un montant fixe ou les deux.',
        'percent_label' => 'Marge Pourcentage',
        'percent_hint' => 'Pourcentage à ajouter à la valeur de l\'or (ex. 5%)',
        'fixed_label' => 'Marge Fixe',
        'fixed_hint' => 'Montant fixe en EUR à ajouter à la valeur de l\'or',
        'current_value' => 'Valeur indicative actuelle',
        'value_note' => 'Calculé à partir du prix spot de l\'or plus les marges',
    ],
    'buy_egili_hint'   => 'Acheter un Pack IA pour recharger vos Egili.',
    'buy_egili_button' => 'Acheter Pack IA',
];
