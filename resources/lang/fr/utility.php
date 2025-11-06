<?php

return [
    // Titres et en-têtes
    'title' => 'Gestion des Utilités',
    'subtitle' => 'Ajoutez de la valeur réelle à votre EGI',
    'status_configured' => 'Utilité Configurée',
    'status_none' => 'Aucune Utilité',
    'available_images' => ':count images disponibles pour ":title"',
    'view_details' => 'Voir les Détails',
    'manage_utility' => 'Gérer l\'Utilité',

    // Alertes et messages
    'info_edit_before_publish' => 'L\'utilité ne peut être ajoutée ou modifiée qu\'avant la publication de la collection. Une fois publiée, elle ne peut plus être modifiée.',
    'success_created' => 'Utilité ajoutée avec succès !',
    'success_updated' => 'Utilité mise à jour avec succès !',
    'confirm_reset' => 'Êtes-vous sûr de vouloir annuler ? Les modifications non sauvegardées seront perdues.',
    'confirm_remove_image' => 'Supprimer cette image ?',
    'note' => 'Note',

    // Types d'utilité
    'types' => [
        'label' => 'Type d\'Utilité',
        'physical' => [
            'label' => 'Bien Physique',
            'description' => 'Objet physique à expédier (tableau, sculpture, etc.)'
        ],
        'service' => [
            'label' => 'Service',
            'description' => 'Service ou expérience (atelier, consultation, etc.)'
        ],
        'hybrid' => [
            'label' => 'Hybride',
            'description' => 'Combinaison physique + service'
        ],
        'digital' => [
            'label' => 'Numérique',
            'description' => 'Contenu ou accès numérique'
        ],
        'remove' => 'Supprimer l\'Utilité'
    ],

    // Champs du formulaire de base
    'fields' => [
        'title' => 'Titre de l\'Utilité',
        'title_placeholder' => 'Ex : Tableau Original 50x70cm',
        'description' => 'Description Détaillée',
        'description_placeholder' => 'Décrivez en détail ce que l\'acheteur recevra...',
    ],

    // Section expédition
    'shipping' => [
        'title' => 'Détails d\'Expédition',
        'weight' => 'Poids (kg)',
        'dimensions' => 'Dimensions (cm)',
        'length' => 'Longueur',
        'width' => 'Largeur',
        'height' => 'Hauteur',
        'days' => 'Jours de préparation/expédition',
        'fragile' => 'Objet Fragile',
        'insurance' => 'Assurance Recommandée',
        'notes' => 'Notes d\'Expédition',
        'notes_placeholder' => 'Instructions spéciales pour l\'emballage ou l\'expédition...'
    ],

    // Section service
    'service' => [
        'title' => 'Détails du Service',
        'valid_from' => 'Valide À Partir Du',
        'valid_until' => 'Valide Jusqu\'Au',
        'max_uses' => 'Nombre Maximum d\'Utilisations',
        'max_uses_placeholder' => 'Laisser vide pour illimité',
        'instructions' => 'Instructions d\'Activation',
        'instructions_placeholder' => 'Comment l\'acheteur peut utiliser le service...'
    ],

    // Escrow
    'escrow' => [
        'immediate' => [
            'label' => 'Paiement Immédiat',
            'description' => 'Pas d\'escrow, paiement direct au créateur'
        ],
        'standard' => [
            'label' => 'Escrow Standard',
            'description' => 'Fonds libérés après 14 jours depuis la livraison',
            'requirement_tracking' => 'Suivi obligatoire'
        ],
        'premium' => [
            'label' => 'Escrow Premium',
            'description' => 'Fonds libérés après 21 jours depuis la livraison',
            'requirement_tracking' => 'Suivi obligatoire',
            'requirement_signature' => 'Signature à la livraison',
            'requirement_insurance' => 'Assurance recommandée'
        ]
    ],

    // Media/Galerie
    'media' => [
        'title' => 'Galerie d\'Images Détail',
        'description' => 'Ajoutez des photos de l\'objet sous différents angles, détails importants, certificats d\'authenticité, etc. (Max 10 images)',
        'upload_prompt' => 'Cliquez pour télécharger ou glissez les images ici',
        'current_images' => 'Images Actuelles :',
        'remove_image' => 'Supprimer'
    ],

    // Erreurs de validation
    'validation' => [
        'errors_found' => 'Quelques erreurs se sont produites :',
        'title_required' => 'Le titre est obligatoire',
        'type_required' => 'Veuillez sélectionner un type d\'utilité',
        'weight_required' => 'Le poids est obligatoire pour les biens physiques',
        'valid_until_after' => 'La date de fin doit être postérieure à la date de début',
        'wait_upload_completion' => 'Veuillez attendre la fin du téléchargement des images',
        'wait_before_save' => 'Veuillez attendre la fin du téléchargement des images avant de sauvegarder.',
        'upload_in_progress' => 'Téléchargement déjà en cours. Veuillez attendre la fin avant d\'ajouter de nouvelles images.',
        'select_images_only' => 'Veuillez sélectionner uniquement des fichiers image.',
        'images_too_large' => 'Certaines images dépassent 10MB et ne peuvent pas être téléchargées.'
    ],

    // Actions
    'actions' => [
        'delete' => 'Supprimer l\'Utilitaire',
        'confirm_delete_title' => 'Confirmer la Suppression',
        'confirm_delete_message' => 'Êtes-vous sûr de vouloir supprimer cet utilitaire ? Cette action ne peut pas être annulée.',
        'delete_success' => 'Utilitaire supprimé avec succès !',
        'delete_error' => 'Erreur lors de la suppression de l\'utilitaire.',
    ],

    // Messages JavaScript
    'js' => [
        'drag_drop_text' => 'Glissez les images ici ou cliquez pour sélectionner',
        'processing_images' => 'Traitement des images en cours...',
        'upload_in_progress' => 'Téléchargement des images en cours...',
        'uploading' => 'Téléchargement en cours...',
        'upload_completed' => 'Images téléchargées avec succès!'
    ],

    'upload_completed' => 'Images téléchargées avec succès!'
];
