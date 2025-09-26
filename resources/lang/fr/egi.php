<?php

return [
    'coa' => [
        'inspector_countersign' => 'Co-signature Expert (QES)',
        'confirm_inspector_countersign' => 'Procéder à la co-signature de l’expert ?',
        'inspector_countersign_applied' => 'Co-signature de l’expert appliquée',
        'operation_failed' => 'Opération échouée',
    ],

    /*
    |--------------------------------------------------------------------------
    | NFT Card System - Système de Cartes NFT
    |--------------------------------------------------------------------------
    */

    // Badges et Statut
    'badge' => [
        'owned' => 'POSSÉDÉ',
        'media_content' => 'Contenu Média',
    ],

    // Titres
    'title' => [
        'untitled' => '✨ EGI Sans Titre',
    ],

    // Plateforme
    'platform' => [
        'powered_by' => 'Powered by :platform',
    ],

    // Créateur
    'creator' => [
        'created_by' => '👨‍🎨 Créé par:',
    ],

    // Prix
    'price' => [
        'purchased_for' => '💳 Acheté pour',
        'price' => '💰 Prix',
        'floor' => '📊 Prix Plancher',
    ],

    // Statut
    'status' => [
        'not_for_sale' => '🚫 Pas à vendre',
        'draft' => '⏳ Brouillon',
    ],

    // Actions
    'actions' => [
        'view' => 'Voir',
        'view_details' => 'Voir les détails de l\'EGI',
        'reserve' => 'L\'Activer',
        'outbid' => 'Surenchérir pour activer',
    ],

    // Détails de réservation
    'reservation' => [
        'highest_bid' => 'Enchère la Plus Haute',
        'fegi_reservation' => 'Réservation FEGI',
        'strong_bidder' => 'Meilleur Enchérisseur',
        'weak_bidder' => 'Code FEGI',
        'activator' => 'Co Créateur',
        'activated_by' => 'Activé par',
    ],

    'carousel' => [
        // Dynamic Headers
        'headers' => [
            'egi_list' => 'EGI',
            'egi_card' => 'EGI',
            'creators' => 'Artistes',
            'collections' => 'Collections',
            'collectors' => 'Activateurs'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero Coverflow - Section Hero avec Effet Coverflow 3D
    |--------------------------------------------------------------------------
    */

    'hero_coverflow' => [
        'title' => 'Activer un EGI, c\'est laisser sa marque.',
        'subtitle' => 'Votre nom demeure à jamais aux côtés de celui du Créateur : sans vous, l\'œuvre n\'existerait pas.',
        'carousel_mode' => 'Vue Carrousel',
        'list_mode' => 'Vue Grille',
        'carousel_label' => 'Carrousel des œuvres en vedette',
        'no_egis' => 'Aucune œuvre en vedette disponible pour le moment.',
        'navigation' => [
            'previous' => 'Œuvre précédente',
            'next' => 'Œuvre suivante',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Système Dossier - Dossier System
    |--------------------------------------------------------------------------
    */
    'dossier' => [
        'title' => 'Dossier d\'Images',
        'loading' => 'Chargement du dossier...',
        'view_complete' => 'Voir le dossier d\'images complet',
        'close' => 'Fermer le dossier',

        // Artwork Info
        'artwork_info' => 'Informations sur l\'Œuvre',
        'author' => 'Auteur',
        'year' => 'Année',
        'internal_id' => 'ID Interne',

        // Dossier Info
        'dossier_info' => 'Informations du Dossier',
        'images_count' => 'Images',
        'type' => 'Type',
        'utility_gallery' => 'Galerie Utilitaire',

        // Gallery
        'gallery_title' => 'Galerie d\'Images',
        'image_number' => 'Image :number',
        'image_of_total' => 'Image :current sur :total',

        // States
        'no_utility_title' => 'Dossier non disponible',
        'no_utility_message' => 'Aucune image supplémentaire n\'est disponible pour cette œuvre.',
        'no_utility_description' => 'Le dossier d\'images supplémentaires n\'a pas encore été configuré pour cette œuvre.',

        'no_images_title' => 'Aucune image disponible',
        'no_images_message' => 'Le dossier existe mais ne contient pas encore d\'images.',
        'no_images_description' => 'Des images supplémentaires seront ajoutées à l\'avenir par le créateur de l\'œuvre.',

        'error_title' => 'Erreur',
        'error_loading' => 'Erreur lors du chargement du dossier',

        // Navigation
        'previous_image' => 'Image précédente',
        'next_image' => 'Image suivante',
        'close_viewer' => 'Fermer le visualiseur',
        'of' => 'de',

        // Zoom Controls
        'zoom_help' => 'Utilisez la molette de la souris ou le tactile pour zoomer • Glissez pour déplacer',
        'zoom_in' => 'Agrandir',
        'zoom_out' => 'Réduire',
        'zoom_reset' => 'Réinitialiser le zoom',
        'zoom_fit' => 'Ajuster à l\'écran',
    ],

    /*
    |--------------------------------------------------------------------------
    | Certificat d'Authenticité (CoA)
    |--------------------------------------------------------------------------
    */

    'coa' => [
        // Badge Signatures QES
        'badge_author_signed' => 'Signé Auteur (QES)',
        'badge_inspector_signed' => 'Signé Inspecteur (QES)',
        'badge_integrity_ok' => 'Intégrité Vérifiée',

        // Contresignature Inspecteur (QES)
        'inspector_countersign' => 'Contresignature Inspecteur (QES)',
        'confirm_inspector_countersign' => 'Procéder avec la contresignature de l\'inspecteur ?',
        'inspector_countersign_applied' => 'Contresignature de l\'inspecteur appliquée',
        'operation_failed' => 'Opération échouée',
        'author_countersign' => 'Signature Auteur (QES)',
        'confirm_author_countersign' => 'Procéder avec la signature de l\'auteur ?',
        'author_countersign_applied' => 'Signature de l\'auteur appliquée',
        'regenerate_pdf' => 'Régénérer PDF',
        'pdf_regenerated' => 'PDF régénéré',
        'pdf_regenerate_failed' => 'Régénération PDF échouée',

        // Page publique de vérification
        'public_verify' => [
            'signature' => 'Signature',
            'author_signed' => 'Auteur signé',
            'inspector_countersigned' => 'Contresignature inspecteur',
            'timestamp_tsa' => 'Timestamp TSA',
            'qes' => 'QES',
            'wallet_signature' => 'Signature wallet',
            'verify_signature' => 'vérifier signature',
            'certificate_hash' => 'Hash du certificat (SHA-256)',
            'pdf_hash' => 'Hash du PDF (SHA-256)',
            'copy_hash' => 'Copier hash',
            'copy_pdf_hash' => 'Copier hash PDF',
            'hash_copied' => 'Hash copié dans le presse-papiers !',
            'pdf_hash_copied' => 'Hash PDF copié dans le presse-papiers !',
            'qr_code_verify' => 'Vérification Code QR',
            'qr_code' => 'Code QR',
            'scan_to_verify' => 'Scanner pour vérifier',
            'status' => 'Statut',
            'valid' => 'Valide',
            'incomplete' => 'Incomplet',
            'revoked' => 'Révoqué',

            // En-tête et titres
            'certificate_title' => 'Certificat d'Authenticité',
            'public_verification_display' => 'Affichage Public de Vérification',
            'verified_authentic' => 'Certificat Vérifié et Authentique',
            'verified_at' => 'Vérifié le',
            'serial_number' => 'Numéro de Série',
            'certificate_not_ready' => 'Certificat Non Prêt',
            'certificate_revoked' => 'Certificat Révoqué',
            'certificate_not_valid' => 'Ce certificat n'est plus valide',
            'requires_coa_traits' => 'Requiert Traits CoA',
            'certificate_not_ready_generic' => 'Certificat Non Prêt - Traits Génériques',

            // Informations œuvre
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

            // Informations certificat
            'issue_date' => 'Date d'Émission',
            'issued_by' => 'Émis par',
            'issue_location' => 'Lieu d'émission',
            'notes' => 'Notes',

            // Annexes professionnelles
            'professional_annexes' => 'Annexes Professionnelles',
            'provenance' => 'Provenance',
            'condition_report' => 'Rapport d'État',
            'exhibitions_publications' => 'Expositions/Publications',
            'additional_photos' => 'Photos Supplémentaires',

            // Informations on-chain
            'on_chain_info' => 'Informations On-chain',
        ],
    ],

];
