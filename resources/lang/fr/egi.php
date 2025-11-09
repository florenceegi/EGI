<?php

return [

    /*
    |--------------------------------------------------------------------------
    |
    | Traductions pour le système CRUD de EGI dans FlorenceEGI
    | Version : 1.0.0 - Conforme au système Oracode 2.0
    |
    */

    // Meta et SEO
    'meta_description_default' => 'Détails pour EGI : :title',
    'image_alt_default' => 'Image EGI',
    'view_full' => 'Vue Complète',
    'artwork_loading' => 'Œuvre en Chargement...',

    // Informations de Base
    'by_author' => 'par :name',
    'unknown_creator' => 'Artiste Inconnu',

    // Actions Principales
    'like_button_title' => 'Ajouter aux Favoris',
    'unlike_button_title' => 'Retirer des Favoris',
    'like_button_aria' => 'Ajouter cet EGI à vos favoris',
    'unlike_button_aria' => 'Retirer cet EGI de vos favoris',
    'share_button_title' => 'Partager cet EGI',

    'current_price' => 'Prix Actuel',
    'not_currently_listed' => 'À Activer',
    'contact_owner_availability' => 'Contacter le propriétaire pour la disponibilité',
    'not_for_sale' => 'Non à Vendre',
    'not_for_sale_description' => 'Cet EGI n’est pas actuellement disponible à l’achat',
    'liked' => 'Aimé',
    'add_to_favorites' => 'Ajouter aux Favoris',
    'reserve_this_piece' => 'Activer',

    /*
    |--------------------------------------------------------------------------
    | Système de Cartes NFT - Système de Cartes NFT
    |--------------------------------------------------------------------------
    */

    // Badges et États
    'badge' => [
        'owned' => 'POSSEDÉ',
        'media_content' => 'Contenu Multimédia',
        'winning_bid' => 'ENCHÈRE GAGNANTE',
        'outbid' => 'SURPASSÉ',
        'not_owned' => 'NON POSSEDÉ',
        'to_activate' => 'À ACTIVER',
        'activated' => 'ACTIVÉ',
        'reserved' => 'RÉSERVÉ',
        'minted' => 'MINTÉ',
        'auction_active' => 'À MINTER',  // Badge pour EGI en enchère
    ],

    'ownership' => [
        'badge_title' => 'Propriété',
        'current_owner' => 'Propriétaire actuel',
        'creator_owner' => 'Créé et encore détenu par le creator',
        'roles' => [
            'creator' => 'Creator',
            'collector' => 'Collectionneur',
        ],
        'creator_default' => 'Le creator conserve la propriété tant que le mint on-chain n’est pas terminé.',
        'collector_since' => 'En possession depuis le :date',
        'collector_default' => 'La propriété est enregistrée on-chain.',
        'minted_on' => 'Minté le :date',
        'minted_unknown' => 'Minté sur la blockchain',
        'unminted_hint' => 'Mint en attente : la propriété reste au creator.',
        'owner_avatar_alt' => 'Portrait de :name',
    ],

    // Titres
    'title' => [
        'untitled' => '✨ EGI Sans Titre',
    ],

    // Plateforme
    'platform' => [
        'powered_by' => 'Propulsé par :platform',
    ],

    // Artiste
    'creator' => [
        'created_by' => '👨‍🎨 Créé par :',
    ],

    // Prix
    'price' => [
        'purchased_for' => '💳 Acheté pour',
        'price' => '💰 Prix',
        'floor' => '📊 Prix de Base',
        'highest_bid' => '🏆 Enchère la Plus Haute',
    ],

    // Réservations
    'reservation' => [
        'count' => 'Réservations',
        'highest_bidder' => 'Meilleur Enchérisseur',
        'by' => 'par',
        'highest_bid' => 'Enchère la Plus Haute',
        'fegi_reservation' => 'Réservation FEGI',
        'strong_bidder' => 'Meilleur Enchérisseur',
        'weak_bidder' => 'Code FEGI',
        'activator' => 'Co-Créateur',
        'activated_by' => 'Activé par',
    ],

    // Note de Monnaie Originale
    'originally_reserved_in' => 'Réservé initialement en :currency pour :amount',
    'originally_reserved_in_short' => 'Rés. :currency :amount',

    // Système d'Enchères
    'auction' => [
        'auction_details' => 'Détails de l\'Enchère',
        'minimum_price' => 'Mise de Départ',
        'starting_price' => 'Prix de Départ',
        'current_bid' => 'Enchère Actuelle',
        'highest_bid' => 'Enchère la Plus Haute',
        'no_bids' => 'Aucune Enchère',
        'starts_at' => 'Début',
        'ends_at' => 'Fin',
        'ended' => 'Enchère Terminée',
        'not_started' => 'Enchère Non Commencée',
        'time_remaining' => 'Temps Restant',
        'days' => 'jours',
        'hours' => 'heures',
        'minutes' => 'minutes',
    ],

    // États
    'status' => [
        'not_for_sale' => '🚫 Non à Vendre',
        'draft' => '⏳ Brouillon',
    ],

    // Actions
    'actions' => [
        'view' => 'Voir',
        'view_details' => 'Voir les Détails de l\'EGI',
        'reserve' => 'Activer',
        'reserved' => 'Réservé',
        'outbid' => 'Surenchérir pour Activer',
        'view_history' => 'Historique',
        'reserve_egi' => 'Réserver :title',
        'complete_purchase' => 'Finaliser l\'Achat',
        'mint_now' => 'Minter Maintenant',
        'mint_direct' => 'Minter Instantanément',
        // Actions d'enchère
        'make_offer' => 'Faire une Offre',
    ],

    // Système d’Historique des Réservations
    'history' => [
        'title' => 'Historique des Réservations',
        'no_reservations' => 'Aucune réservation trouvée',
        'total_reservations' => '{1} :count réservation|[2,*] :count réservations',
        'current_highest' => 'Priorité la Plus Haute Actuelle',
        'superseded' => 'Priorité Inférieure',
        'created_at' => 'Créé le',
        'amount' => 'Montant',
        'type_strong' => 'Réservation Forte',
        'type_weak' => 'Réservation Faible',
        'loading' => 'Chargement de l’historique...',
        'error' => 'Erreur lors du chargement de l’historique',
    ],

    // Sections Informatives
    'properties' => 'Propriétés',
    'supports_epp' => 'Supporte EPP',
    'asset_type' => 'Type d’Actif',
    'format' => 'Format',
    'about_this_piece' => 'À Propos de Cette Œuvre',
    'default_description' => 'Cette œuvre numérique unique représente un moment d’expression créative, capturant l’essence de l’art numérique à l’ère du blockchain.',
    'provenance' => 'Provenance',
    'view_full_collection' => 'Voir la Collection Complète',

    /*
    |--------------------------------------------------------------------------
    | Système CRUD - Système d’Édition
    |--------------------------------------------------------------------------
    */

    'crud' => [

        // En-tête et Navigation
        'edit_egi' => 'Modifier EGI',
        'toggle_edit_mode' => 'Basculer en Mode Édition',
        'start_editing' => 'Commencer l’Édition',
        'save_changes' => 'Enregistrer les Modifications',
        'cancel' => 'Annuler',

        // Champ Titre
        'title' => 'Titre',
        'title_placeholder' => 'Entrez le titre de l’œuvre...',
        'title_hint' => 'Maximum 60 caractères',
        'characters_remaining' => 'caractères restants',

        // Champ Description
        'description' => 'Description',
        'description_placeholder' => 'Décrivez votre œuvre, son histoire et sa signification...',
        'description_hint' => 'Racontez l’histoire derrière votre création',

        // Champ Prix
        'price' => 'Prix',
        'price_placeholder' => '0.00',
        'price_hint' => 'Prix en ALGO (laissez vide si non à vendre)',
        'price_locked_message' => 'Prix verrouillé - EGI déjà réservé',

        // Champ Date de Création
        'creation_date' => 'Date de Création',
        'creation_date_hint' => 'Quand avez-vous créé cette œuvre ?',

        // Champ Publié
        'is_published' => 'Publié',
        'is_published_hint' => 'Rendre l’œuvre visible publiquement',

        // Mode Visualisation - État Actuel
        'current_title' => 'Titre Actuel',
        'no_title' => 'Aucun titre défini',
        'current_price' => 'Prix Actuel',
        'price_not_set' => 'Prix non défini',
        'current_status' => 'Statut de Publication',
        'status_published' => 'Publié',
        'status_draft' => 'Brouillon',

        // Système de Suppression
        'delete_egi' => 'Supprimer EGI',
        'delete_confirmation_title' => 'Confirmer la Suppression',
        'delete_confirmation_message' => 'Êtes-vous sûr de vouloir supprimer cet EGI ? Cette action est irréversible.',
        'delete_confirm' => 'Supprimer Définitivement',

        // Messages de Validation
        'title_required' => 'Le titre est requis',
        'title_max_length' => 'Le titre ne peut pas dépasser 60 caractères',
        'price_numeric' => 'Le prix doit être un nombre valide',
        'price_min' => 'Le prix ne peut pas être négatif',
        'price_required_for_fixed_price' => 'Le mode Prix Fixe nécessite un prix supérieur à zéro',
        'creation_date_format' => 'Format de date invalide',

        // Messages de Succès
        'update_success' => 'EGI mis à jour avec succès !',
        'delete_success' => 'EGI supprimé avec succès.',

        // Messages d’Erreur
        'update_error' => 'Erreur lors de la mise à jour de l’EGI.',
        'delete_error' => 'Erreur lors de la suppression de l’EGI.',
        'permission_denied' => 'Vous n’avez pas les permissions nécessaires pour cette action.',
        'not_found' => 'EGI non trouvé.',

        // Messages Généraux
        'no_changes_detected' => 'Aucune modification détectée.',
        'unsaved_changes_warning' => 'Vous avez des modifications non enregistrées. Êtes-vous sûr de vouloir quitter ?',
    ],

    /*
    |--------------------------------------------------------------------------
    | Étiquettes Réactives - Mobile/Tablette
    |--------------------------------------------------------------------------
    */

    'mobile' => [
        'edit_egi_short' => 'Modifier',
        'save_short' => 'Enregistrer',
        'delete_short' => 'Supprimer',
        'cancel_short' => 'Annuler',
        'published_short' => 'Pub.',
        'draft_short' => 'Brouillon',
    ],

    /*
    |--------------------------------------------------------------------------
    | Carrousel EGI - EGIs en Vedette sur la Page d’Accueil
    |--------------------------------------------------------------------------
    */

    'carousel' => [
        'two_columns' => 'Vue en Liste',
        'three_columns' => 'Vue en Carte',
        'navigation' => [
            'previous' => 'Précédent',
            'next' => 'Suivant',
            'slide' => 'Aller à la diapositive :number',
        ],
        'empty_state' => [
            'title' => 'Aucun Contenu Disponible',
            'subtitle' => 'Revenez bientôt pour du nouveau contenu !',
            'no_egis' => 'Aucune œuvre EGI disponible pour le moment.',
            'no_creators' => 'Aucun artiste disponible pour le moment.',
            'no_collections' => 'Aucune collection disponible pour le moment.',
            'no_collectors' => 'Aucun collectionneur disponible pour le moment.'
        ],

        // Boutons de Type de Contenu
        'content_types' => [
            'egi_list' => 'Vue en Liste EGI',
            'egi_card' => 'Vue en Carte EGI',
            'creators' => 'Artistes en Vedette',
            'collections' => 'Collections d’Art',
            'collectors' => 'Meilleurs Collectionneurs'
        ],

        // Boutons de Mode de Visualisation
        'view_modes' => [
            'carousel' => 'Vue Carrousel',
            'list' => 'Vue Liste'
        ],

        // Étiquettes de Mode
        'carousel_mode' => 'Carrousel',
        'list_mode' => 'Liste',

        // Étiquettes de Contenu
        'creators' => 'Artistes',
        'collections' => 'Collections',
        'collectors' => 'Collectionneurs',

        // En-têtes Dynamiques
        'headers' => [
            'egi_list' => 'EGI',
            'egi_card' => 'EGI',
            'creators' => 'Artistes',
            'collections' => 'Collections',
            'collectors' => 'Activateurs'
        ],

        // Sections du Carrousel
        'sections' => [
            'egis' => 'EGIs en Vedette',
            'creators' => 'Artistes Émergents',
            'collections' => 'Collections Exclusives',
            'collectors' => 'Meilleurs Collectionneurs'
        ],
        'view_all' => 'Voir Tout',
        'items' => 'éléments',

        // Titre et Sous-titre pour le Carrousel Multi-contenu
        'title' => 'Activez un EGI !',
        'subtitle' => 'Activer une œuvre signifie s’y joindre et être reconnu pour toujours comme faisant partie de son histoire.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Vue Liste - Mode Liste sur la Page d’Accueil
    |--------------------------------------------------------------------------
    */

    'list' => [
        'title' => 'Explorer par Catégorie',
        'subtitle' => 'Parcourez les différentes catégories pour trouver ce que vous cherchez',

        'content_types' => [
            'egi_list' => 'Liste EGI',
            'creators' => 'Liste des Artistes',
            'collections' => 'Liste des Collections',
            'collectors' => 'Liste des Collectionneurs'
        ],

        'headers' => [
            'egi_list' => 'Œuvres EGI',
            'creators' => 'Artistes',
            'collections' => 'Collections',
            'collectors' => 'Collectionneurs'
        ],

        'empty_state' => [
            'title' => 'Aucun Élément Trouvé',
            'subtitle' => 'Essayez de sélectionner une catégorie différente',
            'no_egis' => 'Aucune œuvre EGI trouvée.',
            'no_creators' => 'Aucun artiste trouvé.',
            'no_collections' => 'Aucune collection trouvée.',
            'no_collectors' => 'Aucun collectionneur trouvé.'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Carrousel de Bureau - Carrousel EGI Uniquement pour Bureau
    |--------------------------------------------------------------------------
    */

    'desktop_carousel' => [
        'title' => 'Œuvres Numériques en Vedette',
        'subtitle' => 'Les meilleures créations EGI de notre communauté',
        'navigation' => [
            'previous' => 'Précédent',
            'next' => 'Suivant',
            'slide' => 'Aller à la diapositive :number',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bascule Mobile - Bascule de Vue Mobile
    |--------------------------------------------------------------------------
    */

    'mobile_toggle' => [
        'title' => 'Explorer FlorenceEGI',
        'subtitle' => 'Choisissez comment vous souhaitez parcourir le contenu',
        'carousel_mode' => 'Vue Carrousel',
        'list_mode' => 'Vue Liste',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero Coverflow - Section Hero avec Effet Coverflow 3D
    |--------------------------------------------------------------------------
    */

    'hero_coverflow' => [
        'title' => 'Activer un EGI, c’est laisser une marque.',
        'subtitle' => 'Votre nom reste à jamais aux côtés du Créateur : sans vous, l’œuvre n’existerait pas.',
        'carousel_mode' => 'Vue Carrousel',
        'list_mode' => 'Vue Grille',
        'carousel_label' => 'Carrousel des œuvres en vedette',
        'no_egis' => 'Aucune œuvre en vedette disponible pour le moment.',
        'navigation' => [
            'previous' => 'Œuvre Précédente',
            'next' => 'Œuvre Suivante',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Étiquettes d’Accessibilité - Lecteurs d’Écran
    |--------------------------------------------------------------------------
    */

    'a11y' => [
        'edit_form' => 'Formulaire d’Édition EGI',
        'delete_button' => 'Bouton Supprimer EGI',
        'toggle_edit' => 'Basculer en Mode Édition',
        'save_form' => 'Enregistrer les Modifications EGI',
        'close_modal' => 'Fermer la Fenêtre de Confirmation',
        'required_field' => 'Champ Obligatoire',
        'optional_field' => 'Champ Facultatif',
    ],

    'collection' => [
        'part_of' => 'Fait partie de',
    ],

    // Collaborateurs de la Collection
    'collection_collaborators' => 'Collaborateurs',
    'owner' => 'Propriétaire',
    // 'creator' => 'Créateur',
    'no_other_collaborators' => 'Aucun autre collaborateur',

    /*
    |--------------------------------------------------------------------------
    | Certificat d’Authenticité (CoA)
    |--------------------------------------------------------------------------
    */

    'coa' => [
        'none' => 'Aucun Certificat d’Authenticité',
        'title' => 'Certificat d’Authenticité',
        'status' => 'Statut',
        'issued' => 'Émis le',
        'verification' => 'ID de Vérification',
        'copy' => 'Copier',
        'copied' => 'Copié !',
        'view' => 'Voir',
        'pdf' => 'PDF',
        'reissue' => 'Réémettre',
        'issue' => 'Émettre un Certificat',
        'annexes' => 'Annexes',
        'add_annex' => 'Ajouter une Annexe',
        'annex_coming_soon' => 'Gestion des annexes bientôt disponible !',
        'pro' => 'Pro',
        'unlock_pro' => 'Débloquer avec CoA Pro',
        'provenance' => 'Documentation de Provenance',
        'pdf_bundle' => 'Pack PDF Professionnel',
        'issue_description' => 'Émettez un certificat pour fournir une preuve d’authenticité et débloquer les fonctionnalités Pro',
        'creator_only' => 'Seul le créateur peut émettre des certificats',
        'active' => 'Actif',
        'revoked' => 'Révoqué',
        'expired' => 'Expiré',
        'manage_coa' => 'Gérer CoA',
        'no_certificate' => 'Aucun certificat émis pour le moment',

        // Messages JavaScript
        'confirm_issue' => 'Émettre un Certificat d’Authenticité pour cet EGI ?',
        'issued_success' => 'Certificat émis avec succès !',
        'confirm_reissue' => 'Réémettre ce certificat ? Cela créera une nouvelle version.',
        'reissued_success' => 'Certificat réémis avec succès !',
        'reissue_certificate_confirm' => 'Êtes-vous sûr de vouloir réémettre ce certificat ?',
        'certificate_reissued_successfully' => 'Certificat réémis avec succès !',
        'error_reissuing_certificate' => 'Erreur lors de la réémission du certificat',
        'revocation_reason' => 'Raison de la Révocation :',
        'confirm_revoke' => 'Révoquer ce certificat ? Cette action est irréversible.',
        'revoked_success' => 'Certificat révoqué avec succès !',
        'error_issuing' => 'Erreur lors de l’émission du certificat',
        'error_reissuing' => 'Erreur lors de la réémission du certificat',
        'error_revoking' => 'Erreur lors de la révocation du certificat',
        'unknown_error' => 'Erreur Inconnue',
        'verify_any_certificate' => 'Vérifier N’importe Quel Certificat',

        // Modal des Annexes
        'manage_annexes_title' => 'Gérer les Annexes CoA Pro',
        'annexes_description' => 'Ajoutez une documentation professionnelle pour enrichir votre certificat',
        'provenance_tab' => 'Provenance',
        'condition_tab' => 'Condition',
        'exhibitions_tab' => 'Expositions',
        'photos_tab' => 'Photos',
        'provenance_title' => 'Documentation de Provenance',
        'provenance_description' => 'Documentez l’historique de propriété et la chaîne d’authenticité',
        'condition_title' => 'Rapport de Condition',
        'condition_description' => 'Évaluation professionnelle de l’état physique de l’œuvre',
        'exhibitions_title' => 'Historique des Expositions',
        'exhibitions_description' => 'Registre des expositions publiques et de l’historique d’affichage',
        'photos_title' => 'Photographie Professionnelle',
        'photos_description' => 'Documentation haute résolution et photographie détaillée',
        'save_annex' => 'Enregistrer l’Annexe',
        'cancel' => 'Annuler',
        'upload_files' => 'Télécharger des Fichiers',
        'drag_drop_files' => 'Glissez-déposez les fichiers ici, ou cliquez pour sélectionner',
        'max_file_size' => 'Taille maximale du fichier : 10 Mo par fichier',
        'supported_formats' => 'Formats supportés : PDF, JPG, PNG, DOCX',

        // Formulaire de Provenance
        'ownership_history_description' => 'Documentez l’historique de propriété et la chaîne d’authenticité de cette œuvre',
        'previous_owners' => 'Propriétaires Précédents',
        'previous_owners_placeholder' => 'Listez les propriétaires précédents et les dates de possession...',
        'acquisition_details' => 'Détails d’Acquisition',
        'acquisition_details_placeholder' => 'Comment cette œuvre a-t-elle été acquise ? Incluez les dates, prix, maisons de vente...',
        'authenticity_sources' => 'Sources d’Authenticité',
        'authenticity_sources_placeholder' => 'Avis d’experts, catalogues raisonnés, archives institutionnelles...',
        'save_provenance_data' => 'Enregistrer les Données de Provenance',

        // Formulaire de Condition
        'condition_assessment_description' => 'Évaluation professionnelle de l’état physique de l’œuvre et des besoins de conservation',
        'overall_condition' => 'Condition Générale',
        'condition_excellent' => 'Excellent',
        'condition_very_good' => 'Très Bon',
        'condition_good' => 'Bon',
        'condition_fair' => 'Moyen',
        'condition_poor' => 'Mauvais',
        'condition_notes' => 'Notes sur la Condition',
        'condition_notes_placeholder' => 'Description détaillée des dommages, restaurations ou problèmes de conservation...',
        'conservation_history' => 'Historique de Conservation',
        'conservation_history_placeholder' => 'Restaurations précédentes, traitements ou interventions de conservation...',
        'save_condition_data' => 'Enregistrer les Données de Condition',

        // Formulaire d’Expositions
        'exhibition_history_description' => 'Registre des musées, galeries et expositions publiques où cette œuvre a été exposée',
        'exhibition_title' => 'Titre de l’Exposition',
        'exhibition_title_placeholder' => 'Nom de l’exposition...',
        'venue' => 'Lieu',
        'venue_placeholder' => 'Nom du musée, de la galerie ou de l’institution...',
        'exhibition_dates' => 'Dates de l’Exposition',
        'exhibition_notes' => 'Notes',
        'exhibition_notes_placeholder' => 'Numéro de catalogue, mentions spéciales, critiques...',
        'add_exhibition' => 'Ajouter une Exposition',
        'save_exhibitions_data' => 'Enregistrer les Données d’Expositions',

        // Formulaire de Photos
        'photo_documentation_description' => 'Images de haute qualité pour la documentation et les archives',
        'photo_type' => 'Type de Photo',
        'photo_overall' => 'Vue d’Ensemble',
        'photo_detail' => 'Détail',
        'photo_raking' => 'Lumière Rasante',
        'photo_uv' => 'Photographie UV',
        'photo_infrared' => 'Infrarouge',
        'photo_back' => 'Verso',
        'photo_signature' => 'Signature/Marques',
        'photo_frame' => 'Cadre/Montage',
        'photo_description' => 'Description',
        'photo_description_placeholder' => 'Décrivez ce que montre cette photo...',
        'save_photos_data' => 'Enregistrer les Données de Photos',

        // Champs supplémentaires pour le formulaire de condition
        'select_condition' => 'Sélectionner la condition...',
        'detailed_assessment' => 'Évaluation Détaillée',
        'detailed_assessment_placeholder' => 'Description détaillée de la condition, y compris tout dommage, restauration ou caractéristique spéciale...',
        'conservation_history_placeholder' => 'Traitements de conservation précédents, dates et conservateurs...',
        'assessor_information' => 'Informations de l’Évaluateur',
        'assessor_placeholder' => 'Nom et références de l’évaluateur de la condition...',
        'save_condition_report' => 'Enregistrer le Rapport de Condition',

        // Champs du formulaire d’expositions
        'major_exhibitions' => 'Expositions Principales',
        'major_exhibitions_placeholder' => 'Listez les expositions principales, musées, galeries, dates...',
        'publications_catalogues' => 'Publications et Catalogues',
        'publications_placeholder' => 'Livres, catalogues, articles où cette œuvre a été publiée...',
        'awards_recognition' => 'Prix et Reconnaissances',
        'awards_placeholder' => 'Prix, reconnaissances, critiques reçues...',
        'save_exhibition_history' => 'Enregistrer l’Historique des Expositions',
        'exhibition_history_description' => 'Registre des expositions où cette œuvre a été exposée',

        // Champs du formulaire de photos
        'click_upload_images' => 'Cliquez pour télécharger des images',
        'png_jpg_webp' => 'PNG, JPG, WEBP jusqu’à 10 Mo chacun',
        'photo_descriptions' => 'Descriptions des Photos',
        'photo_descriptions_placeholder' => 'Décrivez les images : conditions d’éclairage, détails capturés, objectif...',
        'photographer_credits' => 'Crédits du Photographe',
        'photographer_placeholder' => 'Nom du photographe et date...',
        'save_photo_documentation' => 'Enregistrer la Documentation Photographique',
        'photo_documentation_description' => 'Images haute résolution pour la documentation et les besoins d’assurance',

        // Actions du Modal
        'close' => 'Fermer',
        'error_no_certificate' => 'Erreur : Aucun certificat sélectionné',
        'saving' => 'Enregistrement...',
        'annex_saved_success' => 'Données de l’annexe enregistrées avec succès !',
        'error_saving_annex' => 'Erreur lors de l’enregistrement des données de l’annexe',

        // Traductions manquantes pour la barre latérale et les composants CoA
        'certificate' => 'Certificat CoA',
        'no_certificate' => 'Aucun Certificat',
        'certificate_active' => 'Certificat Actif',
        'serial_number' => 'Numéro de Série',
        'issue_date' => 'Date d’Émission',
        'expires' => 'Expire',
        'no_certificate_issued' => 'Cet EGI n’a pas de Certificat d’Authenticité',
        'issue_certificate' => 'Émettre un Certificat',
        'certificate_issued_successfully' => 'Certificat émis avec succès !',
        'pdf_generated_automatically' => 'PDF généré automatiquement !',
        'download_pdf_now' => 'Voulez-vous télécharger le PDF maintenant ?',
        'digital_signatures' => 'Signatures Numériques',
        'signature_by' => 'Signé par',
        'signature_role' => 'Rôle',
        'signature_provider' => 'Fournisseur',
        'signature_date' => 'Date de Signature',
        'unknown_signer' => 'Signataire Inconnu',
        'step_creating_certificate' => 'Création du certificat...',
        'step_generating_snapshot' => 'Génération de l’instantané...',
        'step_generating_pdf' => 'Génération du PDF...',
        'step_finalizing' => 'Finalisation...',
        'generating' => 'Génération...',
        'generating_pdf' => 'Génération du PDF...',
        'error_issuing_certificate' => 'Erreur lors de l’émission du certificat : ',
        'issuing' => 'Émission...',
        'unlock_with_coa_pro' => 'Débloquer avec CoA Pro',
        'provenance_documentation' => 'Documentation de Provenance',
        'condition_reports' => 'Rapports de Condition',
        'exhibition_history' => 'Historique des Expositions',
        'professional_pdf' => 'PDF Professionnel',
        'only_creator_can_issue' => 'Seul le créateur peut émettre des certificats',

        // Système de Vocabulaire des Traits CoA
        'traits_management_title' => 'Gérer les Traits CoA',
        'traits_management_description' => 'Configurez les caractéristiques techniques de l’œuvre pour le Certificat d’Authenticité',
        'status_configured' => 'Configuré',
        'status_not_configured' => 'Non Configuré',
        'edit_traits' => 'Modifier les Traits',
        'no_technique_selected' => 'Aucune technique sélectionnée',
        'no_materials_selected' => 'Aucun matériau sélectionné',
        'no_support_selected' => 'Aucun support sélectionné',
        'custom' => 'personnalisé',
        'last_updated' => 'Dernière Mise à Jour',
        'never_configured' => 'Jamais Configuré',
        'clear_all' => 'Tout Effacer',
        'saved' => 'Enregistré',

        // Modal de Vocabulaire
        'modal_title' => 'Sélectionner les Traits CoA',
        'category_technique' => 'Technique',
        'category_materials' => 'Matériaux',
        'category_support' => 'Support',
        'search_placeholder' => 'Rechercher des termes...',
        'loading' => 'Chargement...',
        'selected_items' => 'Éléments Sélectionnés',
        'no_items_selected' => 'Aucun élément sélectionné',
        'add_custom' => 'Ajouter Personnalisé',
        'custom_term_placeholder' => 'Entrez un terme personnalisé (max. 60 caractères)',
        'add' => 'Ajouter',
        'cancel' => 'Annuler',
        'items_selected' => 'éléments sélectionnés',
        'confirm' => 'Confirmer',

        // Composants de Vocabulaire
        'terms_available' => 'termes disponibles',
        'no_categories_available' => 'Aucune catégorie disponible',
        'no_categories_found' => 'Aucune catégorie de vocabulaire trouvée.',
        'search_results' => 'Résultats de la Recherche',
        'results_for' => 'Pour',
        'terms_found' => 'termes trouvés',
        'results_found' => 'résultats trouvés',
        'no_results_found' => 'Aucun résultat trouvé',
        'no_terms_match_search' => 'Aucun terme ne correspond à la recherche',
        'in_category' => 'dans la catégorie',
        'clear_search' => 'Effacer la Recherche',
        'no_terms_available' => 'Aucun terme disponible',
        'no_terms_found_category' => 'Aucun terme trouvé pour la catégorie',
        'categories' => 'Catégories',
        'back_to_start' => 'Retour au Début',
        'retry' => 'Réessayer',
        'error' => 'Erreur',
        'unexpected_error' => 'Une erreur inattendue s’est produite.',
        'exhibition_history' => 'Historique des Expositions',
        'professional_pdf_bundle' => 'Pack PDF Professionnel',
        'only_creator_can_issue' => 'Seul le créateur peut émettre des certificats',
        'public_verification' => 'Vérification Publique',
        'verification_description' => 'Vérifiez l’authenticité d’un Certificat d’Authenticité EGI',
        'verification_instructions' => 'Entrez le numéro de série du certificat pour vérifier son authenticité',
        'enter_serial' => 'Entrez le Numéro de Série',
        'serial_help' => 'Format : ABC-123-DEF (lettres, chiffres et tirets)',
        'certificate_of_authenticity' => 'Certificat d’Authenticité',
        'public_verification_display' => 'Affichage de la Vérification Publique',
        'verified_authentic' => 'Certificat Vérifié et Authentique',
        'verified_at' => 'Vérifié le',
        'artwork_information' => 'Informations sur l’Œuvre',
        'artwork_title' => 'Titre de l’Œuvre',
        'creator' => 'Créateur',
        'description' => 'Description',
        'certificate_details' => 'Détails du Certificat',
        'cryptographic_verification' => 'Vérification Cryptographique',
        'verify_again' => 'Vérifier à Nouveau',
        'print_certificate' => 'Imprimer le Certificat',
        'share_verification' => 'Partager la Vérification',
        'powered_by_florenceegi' => 'Propulsé par FlorenceEGI',
        'verification_timestamp' => 'Horodatage de Vérification',
        'link_copied' => 'Lien copié dans le presse-papiers',
        'issuing' => 'Émission...',
        'certificate_issued_successfully' => 'Certificat émis avec succès !',
        'error_issuing_certificate' => 'Erreur lors de l’émission du certificat : ',
        'reissue_certificate_confirm' => 'Réémettre ce certificat ? Une nouvelle version sera créée.',
        'certificate_reissued_successfully' => 'Certificat réémis avec succès !',
        'error_reissuing_certificate' => 'Erreur lors de la réémission du certificat : ',
        'revoke_certificate_confirm' => 'Révoquer ce certificat ? Cette action est irréversible.',
        'reason_for_revocation' => 'Raison de la Révocation :',
        'certificate_revoked_successfully' => 'Certificat révoqué avec succès !',
        'error_revoking_certificate' => 'Erreur lors de la révocation du certificat : ',
        'manage_certificate' => 'Gérer le Certificat',
        'annex_management_coming_soon' => 'Gestion des annexes bientôt disponible !',
        'issue_certificate_description' => 'Émettez un certificat pour fournir une preuve d’authenticité et débloquer des fonctions Pro',
        'serial' => 'Série',
        'pro_features' => 'Fonctionnalités Pro',
        'provenance_docs' => 'Documentation de Provenance',
        'professional_pdf' => 'PDF Professionnel',
        'unlock_pro_features' => 'Débloquer les Fonctionnalités Pro',
        'reason_for' => 'Raison pour',

        // Badges de Signatures QES
        'badge_author_signed' => 'Signé par l’Auteur (QES)',
        'badge_inspector_signed' => 'Signé par l’Inspecteur (QES)',
        'badge_integrity_ok' => 'Intégrité Vérifiée',

        // Interface de Localisation (CoA)
        'issue_place' => 'Lieu d’Émission',
        'location_placeholder' => 'Ex. Florence, Toscane, Italie',
        'save' => 'Enregistrer',
        'location_hint' => 'Utilisez le format "Ville, Région/Province, Pays" (ou équivalent).',
        'location_required' => 'La localisation est requise',
        'location_saved' => 'Localisation enregistrée',
        'location_save_failed' => 'Échec de l’enregistrement de la localisation',
        'location_updated' => 'Localisation mise à jour avec succès',

        // Co-signature de l’Inspecteur (QES)
        'inspector_countersign' => 'Co-signature de l’Inspecteur (QES)',
        'confirm_inspector_countersign' => 'Procéder à la co-signature de l’inspecteur ?',
        'inspector_countersign_applied' => 'Co-signature de l’inspecteur appliquée',
        'operation_failed' => 'Opération échouée',
        'author_countersign' => 'Signature de l’Auteur (QES)',
        'confirm_author_countersign' => 'Procéder à la signature de l’auteur ?',
        'author_countersign_applied' => 'Signature de l’auteur appliquée',
        'regenerate_pdf' => 'Régénérer le PDF',
        'pdf_regenerated' => 'PDF régénéré',
        'pdf_regenerate_failed' => 'Échec de la régénération du PDF',

        // Page de Vérification Publique
        'public_verify' => [
            'signature' => 'Signature',
            'author_signed' => 'Signé par l’Auteur',
            'inspector_countersigned' => 'Co-signé par l’Inspecteur',
            'timestamp_tsa' => 'Horodatage TSA',
            'qes' => 'QES',
            'wallet_signature' => 'Signature du Portefeuille',
            'verify_signature' => 'vérifier la signature',
            'certificate_hash' => 'Hachage du Certificat (SHA-256)',
            'pdf_hash' => 'Hachage du PDF (SHA-256)',
            'copy_hash' => 'Copier le Hachage',
            'copy_pdf_hash' => 'Copier le Hachage du PDF',
            'hash_copied' => 'Hachage copié dans le presse-papiers !',
            'pdf_hash_copied' => 'Hachage du PDF copié dans le presse-papiers !',
            'qr_code_verify' => 'Vérification par Code QR',
            'qr_code' => 'Code QR',
            'scan_to_verify' => 'Scanner pour Vérifier',
            'status' => 'Statut',
            'valid' => 'Valide',
            'incomplete' => 'Incomplet',
            'revoked' => 'Révoqué',

            // En-têtes et Titres
            'certificate_title' => 'Certificat d’Authenticité',
            'public_verification_display' => 'Affichage de la Vérification Publique',
            'verified_authentic' => 'Certificat Vérifié et Authentique',
            'verified_at' => 'Vérifié le',
            'serial_number' => 'Numéro de Série',
            'certificate_not_ready' => 'Certificat Non Prêt',
            'certificate_revoked' => 'Certificat Révoqué',
            'certificate_not_valid' => 'Ce certificat n’est plus valide',
            'requires_coa_traits' => 'Nécessite des Traits CoA',
            'certificate_not_ready_generic' => 'Certificat Non Prêt - Traits Génériques',

            // Informations sur l’Œuvre
            'artwork_title' => 'Titre',
            'year' => 'Année',
            'dimensions' => 'Dimensions',
            'edition' => 'Édition',
            'author' => 'Auteur',
            'technique' => 'Technique',
            'material' => 'Matériau',
            'support' => 'Support',
            'platform' => 'Plateforme',
            'published_by' => 'Publié par',
            'image' => 'Image',

            // Informations sur le Certificat
            'issue_date' => 'Date d’Émission',
            'issued_by' => 'Émis par',
            'issue_location' => 'Lieu d’Émission',
            'notes' => 'Notes',

            // Annexes Professionnelles
            'professional_annexes' => 'Annexes Professionnelles',
            'provenance' => 'Provenance',
            'condition_report' => 'Rapport de Condition',
            'exhibitions_publications' => 'Expositions/Publications',
            'additional_photos' => 'Photos Supplémentaires',

            // Informations sur la Chaîne
            'on_chain_info' => 'Informations sur la Chaîne',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Système de Dossier - Système de Dossier
    |--------------------------------------------------------------------------
    */
    'dossier' => [
        'title' => 'Dossier d’Images',
        'loading' => 'Chargement du dossier...',
        'view_complete' => 'Voir le dossier d’images complet',
        'close' => 'Fermer le Dossier',

        // Informations sur l’Œuvre
        'artwork_info' => 'Informations sur l’Œuvre',
        'author' => 'Auteur',
        'year' => 'Année',
        'internal_id' => 'ID Interne',

        // Informations sur le Dossier
        'dossier_info' => 'Informations sur le Dossier',
        'images_count' => 'Images',
        'type' => 'Type',
        'utility_gallery' => 'Galerie d’Utilité',

        // Galerie
        'gallery_title' => 'Galerie d’Images',
        'image_number' => 'Image :number',
        'image_of_total' => 'Image :current de :total',

        // États
        'no_utility_title' => 'Dossier non disponible',
        'no_utility_message' => 'Aucune image supplémentaire disponible pour cette œuvre.',
        'no_utility_description' => 'Le dossier d’images supplémentaires n’a pas encore été configuré pour cette œuvre.',

        'no_images_title' => 'Aucune image disponible',
        'no_images_message' => 'Le dossier existe mais ne contient pas encore d’images.',
        'no_images_description' => 'Des images supplémentaires seront ajoutées à l’avenir par le créateur de l’œuvre.',

        'error_title' => 'Erreur',
        'error_loading' => 'Erreur lors du chargement du dossier',

        // Navigation
        'previous_image' => 'Image Précédente',
        'next_image' => 'Image Suivante',
        'close_viewer' => 'Fermer le Visualiseur',
        'of' => 'de',

        // Contrôles de Zoom
        'zoom_help' => 'Utilisez la molette de la souris ou le toucher pour zoomer • Faites glisser pour déplacer',
        'zoom_in' => 'Zoomer',
        'zoom_out' => 'Dézoomer',
        'zoom_reset' => 'Réinitialiser le Zoom',
        'zoom_fit' => 'Ajuster à l’Écran',
    ],

];
