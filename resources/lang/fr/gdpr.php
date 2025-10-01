<?php
// resources/lang/fr/gdpr.php

return [
    /*
    |--------------------------------------------------------------------------
    | Lignes de Langue GDPR
    |--------------------------------------------------------------------------
    |
    | Les lignes de langue suivantes sont utilisées pour les fonctionnalités liées au RGPD.
    |
    */

    // Général
    'gdpr' => 'RGPD',
    'gdpr_center' => 'Centre de Contrôle des Données RGPD',
    'dashboard' => 'Tableau de Bord',
    'back_to_dashboard' => 'Retour au Tableau de Bord',
    'save' => 'Enregistrer',
    'submit' => 'Envoyer',
    'cancel' => 'Annuler',
    'continue' => 'Continuer',
    'loading' => 'Chargement...',
    'success' => 'Succès',
    'error' => 'Erreur',
    'warning' => 'Avertissement',
    'info' => 'Information',
    'enabled' => 'Activé',
    'disabled' => 'Désactivé',
    'active' => 'Actif',
    'inactive' => 'Inactif',
    'pending' => 'En attente',
    'completed' => 'Terminé',
    'failed' => 'Échoué',
    'processing' => 'En cours de traitement',
    'retry' => 'Réessayer',
    'required_field' => 'Champ obligatoire',
    'required_consent' => 'Consentement obligatoire',
    'select_all_categories' => 'Sélectionner toutes les catégories',
    'no_categories_selected' => 'Aucune catégorie sélectionnée',
    'compliance_badge' => 'Badge de conformité',

    'consent_types' => [
        'terms-of-service' => [
            'name' => 'Conditions d\'Utilisation',
            'description' => 'Acceptation des conditions pour l\'utilisation de la plateforme.',
        ],
        'privacy-policy' => [
            'name' => 'Politique de Confidentialité',
            'description' => 'Prise de connaissance de la manière dont les données personnelles sont traitées.',
        ],
        'age-confirmation' => [
            'name' => 'Confirmation d\'Âge',
            'description' => 'Confirmation d\'avoir au moins 18 ans.',
        ],
        'analytics' => [
            'name' => 'Analyse et amélioration de la plateforme',
            'description' => 'Aidez-nous à améliorer FlorenceEGI en partageant des données d\'utilisation anonymes.',
        ],
        'marketing' => [
            'name' => 'Communications promotionnelles',
            'description' => 'Recevez des mises à jour sur les nouvelles fonctionnalités, événements et opportunités.',
        ],
        'personalization' => [
            'name' => 'Personnalisation des contenus',
            'description' => 'Permet la personnalisation des contenus et des recommandations.',
        ],
        'collaboration_participation' => [
            'name' => 'Participation à la collaboration',
            'description' => 'Consentement à participer à des collaborations de collections, au partage de données et à des activités collaboratives.',
        ],
        'purposes' => [
            'account_management' => 'Gestion du compte utilisateur',
            'service_delivery'   => 'Fourniture des services demandés',
            'legal_compliance'   => 'Conformité légale et réglementaire',
            'customer_support'   => 'Support client et assistance',
        ],
    ],

    // Fil d\'Ariane
    'breadcrumb' => [
        'dashboard' => 'Tableau de Bord',
        'gdpr' => 'Confidentialité et RGPD',
    ],

    // Messages d\'Alerte
    'alerts' => [
        'success' => 'Opération terminée !',
        'error' => 'Erreur :',
        'warning' => 'Avertissement :',
        'info' => 'Information :',
    ],

    // Éléments du Menu
    'menu' => [
        'gdpr_center' => 'Centre de Contrôle des Données RGPD',
        'consent_management' => 'Gestion des Consentements',
        'data_export' => 'Exporter Mes Données',
        'processing_restrictions' => 'Restreindre le Traitement des Données',
        'delete_account' => 'Supprimer Mon Compte',
        'breach_report' => 'Signaler une Violation de Données',
        'activity_log' => 'Registre de Mes Activités RGPD',
        'privacy_policy' => 'Politique de Confidentialité',
    ],

    // Gestion des Consentements
    'consent' => [
        'title' => 'Gérez Vos Préférences de Consentement',
        'description' => 'Contrôlez la manière dont vos données sont utilisées sur notre plateforme. Vous pouvez mettre à jour vos préférences à tout moment.',
        'update_success' => 'Vos préférences de consentement ont été mises à jour.',
        'update_error' => 'Une erreur s\'est produite lors de la mise à jour de vos préférences de consentement. Veuillez réessayer.',
        'save_all' => 'Enregistrer Toutes les Préférences',
        'last_updated' => 'Dernière mise à jour :',
        'never_updated' => 'Jamais mis à jour',
        'privacy_notice' => 'Avis de Confidentialité',
        'not_given' => 'Non fourni',
        'given_at' => 'Fourni le',
        'your_consents' => 'Vos Consentements',
        'subtitle' => 'Gérez vos préférences de confidentialité et consultez l\'état de vos consentements.',
        'breadcrumb' => 'Consentements',
        'history_title' => 'Historique des Consentements',
        'back_to_consents' => 'Retour aux Consentements',
        'preferences_title' => 'Gestion des Préférences de Consentement',
        'preferences_subtitle' => 'Configurez vos préférences de confidentialité détaillées',
        'preferences_breadcrumb' => 'Préférences',
        'preferences_info_title' => 'Gestion Granulaire des Consentements',
        'preferences_info_description' => 'Ici, vous pouvez configurer chaque type de consentement en détail...',
        'required' => 'Obligatoire',
        'optional' => 'Facultatif',
        'toggle_label' => 'Activer/Désactiver',
        'always_enabled' => 'Toujours Actif',
        'benefits_title' => 'Avantages pour Vous',
        'consequences_title' => 'Si Vous Désactivez',
        'third_parties_title' => 'Services Tiers',
        'save_preferences' => 'Enregistrer les Préférences',
        'back_to_overview' => 'Retour à l\'Aperçu',
        'never_updated' => 'Jamais mis à jour',

        // Détails du Consentement
        'given_at' => 'Fourni le',
        'withdrawn_at' => 'Retiré le',
        'not_given' => 'Non fourni',
        'method' => 'Méthode',
        'version' => 'Version',
        'unknown_version' => 'Version inconnue',

        // Actions
        'withdraw' => 'Retirer le Consentement',
        'withdraw_confirm' => 'Êtes-vous sûr de vouloir retirer ce consentement ? Cette action peut limiter certaines fonctionnalités.',
        'renew' => 'Renouveler le Consentement',
        'view_history' => 'Voir l\'Historique',

        // États Vides
        'no_consents' => 'Aucun Consentement Présent',
        'no_consents_description' => 'Vous n\'avez pas encore fourni de consentement pour le traitement des données. Vous pouvez gérer vos préférences en utilisant le bouton ci-dessous.',

        // Gestion des Préférences
        'manage_preferences' => 'Gérez Vos Préférences',
        'update_preferences' => 'Mettre à Jour les Préférences de Confidentialité',

        // État du Consentement
        'status' => [
            'granted' => 'Accordé',
            'denied' => 'Refusé',
            'active' => 'Actif',
            'withdrawn' => 'Retiré',
            'expired' => 'Expiré',
            'pending' => 'En attente',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'failed' => 'Échoué',
            'rejected' => 'Rejeté',
            'verification_required' => 'Vérification requise',
            'cancelled' => 'Annulé',
        ],

        // Résumé du Tableau de Bord
        'summary' => [
            'active' => 'Consentements Actifs',
            'total' => 'Consentements Totaux',
            'compliance' => 'Score de Conformité',
        ],

        // Méthodes de Consentement
        'methods' => [
            'web' => 'Interface Web',
            'api' => 'API',
            'import' => 'Importation',
            'admin' => 'Administrateur',
        ],

        // Finalités du Consentement
        'purposes' => [
            'functional' => 'Consentements Fonctionnels',
            'analytics' => 'Consentements Analytiques',
            'marketing' => 'Consentements Marketing',
            'profiling' => 'Consentements de Profilage',
            'platform-services' => 'Services de la Plateforme',
            'terms-of-service' => 'Conditions d\'Utilisation',
            'privacy-policy' => 'Politique de Confidentialité',
            'age-confirmation' => 'Confirmation d\'Âge',
            'personalization' => 'Personnalisation des Contenus',
            'allow-personal-data-processing' => 'Autoriser le Traitement des Données Personnelles',
            'collaboration_participation' => 'Participation aux Collaborations',
        ],

        // Descriptions des Consentements
        'descriptions' => [
            'functional' => 'Nécessaires au fonctionnement de base de la plateforme et à la fourniture des services demandés.',
            'analytics' => 'Utilisés pour analyser l\'utilisation du site et améliorer l\'expérience utilisateur.',
            'marketing' => 'Utilisés pour vous envoyer des communications promotionnelles et des offres personnalisées.',
            'profiling' => 'Utilisés pour créer des profils personnalisés et suggérer des contenus pertinents.',
            'platform-services' => 'Consentements nécessaires à la gestion du compte, à la sécurité et au support client.',
            'terms-of-service' => 'Acceptation des Conditions d\'Utilisation pour l\'utilisation de la plateforme.',
            'privacy-policy' => 'Acceptation de notre Politique de Confidentialité et du traitement des données personnelles.',
            'age-confirmation' => 'Confirmation d\'avoir l\'âge légal pour utiliser la plateforme.',
            'personalization' => 'Permet la personnalisation des contenus et des recommandations en fonction de vos préférences.',
            'allow-personal-data-processing' => 'Permet le traitement de vos données personnelles pour améliorer nos services et vous offrir une expérience personnalisée.',
            'collaboration_participation' => 'Permet la participation à des projets collaboratifs et à des activités partagées avec d\'autres utilisateurs de la plateforme.',
        ],

        'essential' => [
            'label' => 'Cookies Essentiels',
            'description' => 'Ces cookies sont nécessaires au fonctionnement du site web et ne peuvent pas être désactivés dans nos systèmes.',
        ],
        'functional' => [
            'label' => 'Cookies Fonctionnels',
            'description' => 'Ces cookies permettent au site web de fournir des fonctionnalités avancées et une personnalisation.',
        ],
        'analytics' => [
            'label' => 'Cookies Analytiques',
            'description' => 'Ces cookies nous permettent de compter les visites et les sources de trafic pour mesurer et améliorer les performances de notre site.',
        ],
        'marketing' => [
            'label' => 'Cookies Marketing',
            'description' => 'Ces cookies peuvent être définis via notre site par nos partenaires publicitaires pour créer un profil de vos intérêts.',
        ],
        'profiling' => [
            'label' => 'Profilage',
            'description' => 'Nous utilisons le profilage pour mieux comprendre vos préférences et personnaliser nos services selon vos besoins.',
        ],

        'allow_personal_data_processing' => [
            'label' => 'Consentement au Traitement des Données Personnelles',
            'description' => 'Permet le traitement de vos données personnelles pour améliorer nos services et vous offrir une expérience personnalisée.',
        ],

        'saving_consent' => 'Enregistrement...',
        'consent_saved' => 'Enregistré',
        'saving_all_consents' => 'Enregistrement de toutes les préférences...',
        'all_consents_saved' => 'Toutes les préférences de consentement ont été enregistrées avec succès.',
        'all_consents_save_error' => 'Une erreur s\'est produite lors de l\'enregistrement de toutes les préférences de consentement.',
        'consent_save_error' => 'Une erreur s\'est produite lors de l\'enregistrement de cette préférence de consentement.',

        // Finalités du Traitement
        'processing_purposes' => [
            'functional' => 'Opérations essentielles de la plateforme : authentification, sécurité, prestation de services, stockage des préférences utilisateur',
            'analytics' => 'Amélioration de la plateforme : analyse de l\'utilisation, suivi des performances, optimisation de l\'expérience utilisateur',
            'marketing' => 'Communication : newsletters, mises à jour des produits, offres promotionnelles, notifications d\'événements',
            'profiling' => 'Personnalisation : recommandations de contenu, analyse du comportement utilisateur, suggestions ciblées',
        ],

        // Périodes de Conservation
        'retention_periods' => [
            'functional' => 'Durée du compte + 1 an pour conformité légale',
            'analytics' => '2 ans depuis la dernière activité',
            'marketing' => '3 ans depuis la dernière interaction ou le retrait du consentement',
            'profiling' => '1 an depuis la dernière activité ou le retrait du consentement',
        ],

        // Avantages pour l\'Utilisateur
        'user_benefits' => [
            'functional' => [
                'Accès sécurisé à votre compte',
                'Paramètres utilisateur personnalisés',
                'Performance fiable de la plateforme',
                'Protection contre les fraudes et abus',
            ],
            'analytics' => [
                'Performance améliorée de la plateforme',
                'Conception optimisée de l\'expérience utilisateur',
                'Temps de chargement plus rapides',
                'Développement de fonctionnalités améliorées',
            ],
            'marketing' => [
                'Mises à jour de produits pertinentes',
                'Offres et promotions exclusives',
                'Invitations à des événements et annonces',
                'Contenus éducatifs et suggestions',
            ],
            'profiling' => [
                'Recommandations de contenu personnalisées',
                'Expérience utilisateur sur mesure',
                'Suggestions de projets pertinents',
                'Tableau de bord et fonctionnalités personnalisées',
            ],
        ],

        // Services Tiers
        'third_parties' => [
            'functional' => [
                'Fournisseurs de CDN (distribution de contenu statique)',
                'Services de sécurité (prévention des fraudes)',
                'Fournisseurs d\'infrastructure (hébergement)',
            ],
            'analytics' => [
                'Plateformes d\'analyse (données d\'utilisation anonymisées)',
                'Services de suivi des performances',
                'Services de suivi des erreurs',
            ],
            'marketing' => [
                'Fournisseurs de services d\'e-mail',
                'Plateformes d\'automatisation marketing',
                'Plateformes de réseaux sociaux (pour la publicité)',
            ],
            'profiling' => [
                'Moteurs de recommandation',
                'Services d\'analyse comportementale',
                'Plateformes de personnalisation de contenu',
            ],
        ],

        // Conséquences du Retrait
        'withdrawal_consequences' => [
            'functional' => [
                'Ne peut pas être retiré - essentiel au fonctionnement de la plateforme',
                'L\'accès au compte serait compromis',
                'Les fonctionnalités de sécurité seraient désactivées',
            ],
            'analytics' => [
                'Les améliorations de la plateforme pourraient ne pas refléter vos schémas d\'utilisation',
                'Expérience générique au lieu de performances optimisées',
                'Aucun impact sur les fonctionnalités principales',
            ],
            'marketing' => [
                'Aucun e-mail promotionnel ou mise à jour',
                'Vous pourriez manquer des annonces importantes',
                'Aucun impact sur la fonctionnalité de la plateforme',
                'Peut être réactivé à tout moment',
            ],
            'profiling' => [
                'Contenus génériques au lieu de recommandations personnalisées',
                'Mise en page standard du tableau de bord',
                'Suggestions de projets moins pertinentes',
                'Aucun impact sur les fonctionnalités principales de la plateforme',
            ],
        ],
    ],

    // Exportation des Données
    'export' => [
        'title' => 'Exporter Vos Données',
        'subtitle' => 'Demandez une copie complète de vos données personnelles dans un format portable',
        'description' => 'Demandez une copie de vos données personnelles. Le traitement peut prendre quelques minutes.',

        // Catégories de Données
        'select_data_categories' => 'Sélectionnez les Catégories de Données à Exporter',
        'categories' => [
            'profile' => 'Informations de Profil',
            'account' => 'Détails du Compte',
            'preferences' => 'Préférences et Paramètres',
            'activity' => 'Historique d\'Activité',
            'consents' => 'Historique des Consentements',
            'collections' => 'Collections et NFT',
            'purchases' => 'Achats et Transactions',
            'comments' => 'Commentaires et Avis',
            'messages' => 'Messages et Communications',
            'biography' => 'Biographies et Contenus',
        ],

        // Descriptions des Catégories
        'category_descriptions' => [
            'profile' => 'Données personnelles, informations de contact, photo de profil et descriptions personnelles',
            'account' => 'Détails du compte, paramètres de sécurité, historique de connexion et modifications',
            'preferences' => 'Préférences de l\'utilisateur, paramètres de confidentialité, configurations personnalisées',
            'activity' => 'Historique de navigation, interactions, visualisations et utilisation de la plateforme',
            'consents' => 'Historique des consentements de confidentialité, modifications des préférences, piste d\'audit RGPD',
            'collections' => 'Collections de NFT créées, métadonnées, propriété intellectuelle et actifs',
            'purchases' => 'Transactions, achats, factures, méthodes de paiement et historique des commandes',
            'comments' => 'Commentaires, avis, évaluations et retours laissés sur la plateforme',
            'messages' => 'Messages privés, communications, notifications et conversations',
            'biography' => 'Biographies créées, chapitres, chronologies, médias et contenus narratifs',
        ],

        // Formats d\'Exportation
        'select_format' => 'Sélectionnez le Format d\'Exportation',
        'formats' => [
            'json' => 'JSON - Format de Données Structuré',
            'csv' => 'CSV - Compatible avec les Tableurs',
            'pdf' => 'PDF - Document Lisible',
        ],

        // Descriptions des Formats
        'format_descriptions' => [
            'json' => 'Format de données structuré idéal pour les développeurs et les intégrations. Conserve la structure complète des données.',
            'csv' => 'Format compatible avec Excel et Google Sheets. Parfait pour l\'analyse et la manipulation des données.',
            'pdf' => 'Document lisible et imprimable. Idéal pour l\'archivage et le partage.',
        ],

        // Options Supplémentaires
        'additional_options' => 'Options Supplémentaires',
        'include_metadata' => 'Inclure les Métadonnées Techniques',
        'metadata_description' => 'Inclut des informations techniques telles que les horodatages, les adresses IP, les versions et la piste d\'audit.',
        'include_audit_trail' => 'Inclure le Registre Complet des Activités',
        'audit_trail_description' => 'Inclut l\'historique complet de toutes les modifications et activités RGPD.',

        // Actions
        'request_export' => 'Demander l\'Exportation des Données',
        'request_success' => 'Demande d\'exportation envoyée avec succès. Vous recevrez une notification une fois terminée.',
        'request_error' => 'Une erreur s\'est produite lors de l\'envoi de la demande. Veuillez réessayer.',

        // Historique des Exportations
        'history_title' => 'Historique des Exportations',
        'no_exports' => 'Aucune Exportation Présente',
        'no_exports_description' => 'Vous n\'avez pas encore demandé d\'exportation de vos données. Utilisez le formulaire ci-dessus pour en demander une.',

        // Détails des Éléments d\'Exportation
        'export_format' => 'Exportation {format}',
        'requested_on' => 'Demandée le',
        'completed_on' => 'Terminée le',
        'expires_on' => 'Expire le',
        'file_size' => 'Taille',
        'download' => 'Télécharger',
        'download_export' => 'Télécharger l\'Exportation',

        // Statut
        'status' => [
            'pending' => 'En attente',
            'processing' => 'En cours de traitement',
            'completed' => 'Terminée',
            'failed' => 'Échouée',
            'expired' => 'Expirée',
        ],

        // Limitation de Fréquence
        'rate_limit_title' => 'Limite d\'Exportations Atteinte',
        'rate_limit_message' => 'Vous avez atteint la limite maximale de {max} exportations pour aujourd\'hui. Réessayez demain.',
        'last_export_date' => 'Dernière exportation : {date}',

        // Validation
        'select_at_least_one_category' => 'Sélectionnez au moins une catégorie de données à exporter.',

        // Support Hérité
        'request_button' => 'Demander l\'Exportation des Données',
        'format' => 'Format d\'Exportation',
        'format_json' => 'JSON (recommandé pour les développeurs)',
        'format_csv' => 'CSV (compatible avec les tableurs)',
        'format_pdf' => 'PDF (document lisible)',
        'include_timestamps' => 'Inclure les horodatages',
        'password_protection' => 'Protéger l\'exportation par mot de passe',
        'password' => 'Mot de passe d\'exportation',
        'confirm_password' => 'Confirmer le mot de passe',
        'data_categories' => 'Catégories de données à exporter',
        'recent_exports' => 'Exportations Récentes',
        'no_recent_exports' => 'Vous n\'avez pas d\'exportations récentes.',
        'export_status' => 'Statut de l\'Exportation',
        'export_date' => 'Date d\'Exportation',
        'export_size' => 'Taille de l\'Exportation',
        'export_id' => 'ID d\'Exportation',
        'export_preparing' => 'Préparation de votre exportation de données...',
        'export_queued' => 'Votre exportation est en file d\'attente et commencera bientôt...',
        'export_processing' => 'Traitement de votre exportation de données...',
        'export_ready' => 'Votre exportation de données est prête à être téléchargée.',
        'export_failed' => 'Votre exportation de données a échoué.',
        'export_failed_details' => 'Une erreur s\'est produite lors du traitement de votre exportation de données. Réessayez ou contactez le support.',
        'export_unknown_status' => 'Statut de l\'exportation inconnu.',
        'check_status' => 'Vérifier le Statut',
        'retry_export' => 'Réessayer l\'Exportation',
        'export_download_error' => 'Une erreur s\'est produite lors du téléchargement de votre exportation.',
        'export_status_error' => 'Erreur lors de la vérification du statut de l\'exportation.',
        'limit_reached' => 'Vous avez atteint le nombre maximum d\'exportations autorisées par jour.',
        'existing_in_progress' => 'Vous avez déjà une exportation en cours. Attendez qu\'elle soit terminée.',
    ],

    // Restrictions de Traitement
    'restriction' => [
        'title' => 'Restreindre le Traitement des Données',
        'description' => 'Vous pouvez demander à limiter la manière dont nous traitons vos données dans certaines circonstances.',
        'active_restrictions' => 'Restrictions Actives',
        'no_active_restrictions' => 'Vous n\'avez pas de restrictions de traitement actives.',
        'request_new' => 'Demander une Nouvelle Restriction',
        'restriction_type' => 'Type de Restriction',
        'restriction_reason' => 'Raison de la Restriction',
        'data_categories' => 'Catégories de Données',
        'notes' => 'Notes Supplémentaires',
        'notes_placeholder' => 'Fournissez des détails supplémentaires pour nous aider à comprendre votre demande...',
        'submit_button' => 'Envoyer la Demande de Restriction',
        'remove_button' => 'Supprimer la Restriction',
        'processing_restriction_success' => 'Votre demande de restriction de traitement a été envoyée.',
        'processing_restriction_failed' => 'Une erreur s\'est produite lors de l\'envoi de votre demande de restriction de traitement.',
        'processing_restriction_system_error' => 'Une erreur système s\'est produite lors du traitement de votre demande.',
        'processing_restriction_removed' => 'La restriction de traitement a été supprimée.',
        'processing_restriction_removal_failed' => 'Une erreur s\'est produite lors de la suppression de la restriction de traitement.',
        'unauthorized_action' => 'Vous n\'êtes pas autorisé à effectuer cette action.',
        'date_submitted' => 'Date d\'Envoi',
        'expiry_date' => 'Expire le',
        'never_expires' => 'N\'expire jamais',
        'status' => 'Statut',
        'limit_reached' => 'Vous avez atteint le nombre maximum de restrictions actives autorisées.',
        'categories' => [
            'profile' => 'Informations de Profil',
            'activity' => 'Suivi d\'Activité',
            'preferences' => 'Préférences et Paramètres',
            'collections' => 'Collections et Contenus',
            'purchases' => 'Achats et Transactions',
            'comments' => 'Commentaires et Avis',
            'messages' => 'Messages et Communications',
        ],
        'types' => [
            'processing' => 'Restreindre Tout le Traitement',
            'automated_decisions' => 'Restreindre les Décisions Automatisées',
            'marketing' => 'Restreindre le Traitement Marketing',
            'analytics' => 'Restreindre le Traitement Analytique',
            'third_party' => 'Restreindre le Partage avec des Tiers',
            'profiling' => 'Restreindre le Profilage',
            'data_sharing' => 'Restreindre le Partage de Données',
            'removed' => 'Supprimer la Restriction',
            'all' => 'Restreindre Tout le Traitement',
        ],
        'reasons' => [
            'accuracy_dispute' => 'Je conteste l\'exactitude de mes données',
            'processing_unlawful' => 'Le traitement est illégal',
            'no_longer_needed' => 'Vous n\'avez plus besoin de mes données, mais j\'en ai besoin pour des réclamations légales',
            'objection_pending' => 'J\'ai objecté au traitement et j\'attends une vérification',
            'legitimate_interest' => 'Motifs légitimes impérieux',
            'legal_claims' => 'Pour la défense de réclamations légales',
            'other' => 'Autre motif (préciser dans les notes)',
        ],
        'descriptions' => [
            'processing' => 'Restreint le traitement de vos données personnelles en attendant la vérification de votre demande.',
            'automated_decisions' => 'Restreint les décisions automatisées qui peuvent affecter vos droits.',
            'marketing' => 'Restreint le traitement de vos données à des fins de marketing direct.',
            'analytics' => 'Restreint le traitement de vos données à des fins analytiques et de suivi.',
            'third_party' => 'Restreint le partage de vos données avec des tiers.',
            'profiling' => 'Restreint le profilage de vos données personnelles.',
            'data_sharing' => 'Restreint le partage de vos données avec d\'autres services ou plateformes.',
            'all' => 'Restreint toutes les formes de traitement de vos données personnelles.',
        ],
    ],

    // Suppression de Compte
    'deletion' => [
        'title' => 'Supprimer Mon Compte',
        'description' => 'Cela lancera le processus de suppression de votre compte et de toutes les données associées.',
        'warning' => 'Avertissement : La suppression du compte est permanente et ne peut pas être annulée.',
        'processing_delay' => 'Votre compte est programmé pour être supprimé dans :days jours.',
        'confirm_deletion' => 'Je comprends que cette action est permanente et ne peut pas être annulée.',
        'password_confirmation' => 'Entrez votre mot de passe pour confirmer',
        'reason' => 'Raison de la suppression (facultatif)',
        'additional_comments' => 'Commentaires supplémentaires (facultatif)',
        'submit_button' => 'Demander la Suppression du Compte',
        'request_submitted' => 'Votre demande de suppression de compte a été envoyée.',
        'request_error' => 'Une erreur s\'est produite lors de l\'envoi de votre demande de suppression de compte.',
        'pending_deletion' => 'Votre compte est programmé pour être supprimé le :date.',
        'cancel_deletion' => 'Annuler la Demande de Suppression',
        'cancellation_success' => 'Votre demande de suppression de compte a été annulée.',
        'cancellation_error' => 'Une erreur s\'est produite lors de l\'annulation de votre demande de suppression de compte.',
        'reasons' => [
            'no_longer_needed' => 'Je n\'ai plus besoin de ce service',
            'privacy_concerns' => 'Préoccupations concernant la confidentialité',
            'moving_to_competitor' => 'Passage à un autre service',
            'unhappy_with_service' => 'Insatisfait du service',
            'other' => 'Autre motif',
        ],
        'confirmation_email' => [
            'subject' => 'Confirmation de la Demande de Suppression de Compte',
            'line1' => 'Nous avons reçu votre demande de suppression de votre compte.',
            'line2' => 'Votre compte est programmé pour être supprimé le :date.',
            'line3' => 'Si vous n\'avez pas demandé cette action, contactez-nous immédiatement.',
        ],
        'data_retention_notice' => 'Notez que certaines données anonymisées peuvent être conservées à des fins légales et analytiques.',
        'blockchain_data_notice' => 'Les données stockées sur la blockchain ne peuvent pas être complètement supprimées en raison de la nature immuable de la technologie.',
    ],

    // Signalement de Violation
    'breach' => [
        'title' => 'Signaler une Violation de Données',
        'description' => 'Si vous pensez qu\'il y a eu une violation de vos données personnelles, signalez-la ici.',
        'reporter_name' => 'Votre Nom',
        'reporter_email' => 'Votre E-mail',
        'incident_date' => 'Quand l\'incident s\'est-il produit ?',
        'breach_description' => 'Décrivez la violation potentielle',
        'breach_description_placeholder' => 'Fournissez autant de détails que possible sur la violation potentielle des données...',
        'affected_data' => 'Quelles données pensez-vous avoir été compromises ?',
        'affected_data_placeholder' => 'Par exemple, informations personnelles, données financières, etc.',
        'discovery_method' => 'Comment avez-vous découvert cette violation potentielle ?',
        'supporting_evidence' => 'Preuves à l\'Appui (facultatif)',
        'upload_evidence' => 'Télécharger des Preuves',
        'file_types' => 'Types de fichiers acceptés : PDF, JPG, JPEG, PNG, TXT, DOC, DOCX',
        'max_file_size' => 'Taille maximale du fichier : 10 Mo',
        'consent_to_contact' => 'J\'accepte d\'être contacté concernant ce signalement',
        'submit_button' => 'Envoyer le Signalement de Violation',
        'report_submitted' => 'Votre signalement de violation a été envoyé.',
        'report_error' => 'Une erreur s\'est produite lors de l\'envoi de votre signalement de violation.',
        'thank_you' => 'Merci pour votre signalement',
        'thank_you_message' => 'Merci d\'avoir signalé cette violation potentielle. Notre équipe de protection des données enquêtera et pourrait vous contacter pour plus d\'informations.',
        'breach_description_min' => 'Fournissez au moins 20 caractères pour décrire la violation potentielle.',
    ],

    // Registre d\'Activité
    'activity' => [
        'title' => 'Registre de Mes Activités RGPD',
        'description' => 'Consultez un registre de toutes vos activités et demandes liées au RGPD.',
        'no_activities' => 'Aucune activité trouvée.',
        'date' => 'Date',
        'activity' => 'Activité',
        'details' => 'Détails',
        'ip_address' => 'Adresse IP',
        'user_agent' => 'Agent Utilisateur',
        'download_log' => 'Télécharger le Registre d\'Activité',
        'filter' => 'Filtrer les Activités',
        'filter_all' => 'Toutes les Activités',
        'filter_consent' => 'Activités de Consentement',
        'filter_export' => 'Activités d\'Exportation de Données',
        'filter_restriction' => 'Activités de Restriction de Traitement',
        'filter_deletion' => 'Activités de Suppression de Compte',
        'types' => [
            'consent_updated' => 'Préférences de Consentement Mises à Jour',
            'data_export_requested' => 'Exportation de Données Demandée',
            'data_export_completed' => 'Exportation de Données Terminée',
            'data_export_downloaded' => 'Exportation de Données Téléchargée',
            'processing_restricted' => 'Restriction de Traitement Demandée',
            'processing_restriction_removed' => 'Restriction de Traitement Supprimée',
            'account_deletion_requested' => 'Suppression de Compte Demandée',
            'account_deletion_cancelled' => 'Suppression de Compte Annulée',
            'account_deletion_completed' => 'Suppression de Compte Terminée',
            'breach_reported' => 'Violation de Données Signalée',
        ],
    ],

    // Validation
    'validation' => [
        'consents_required' => 'Les préférences de consentement sont obligatoires.',
        'consents_format' => 'Le format des préférences de consentement n\'est pas valide.',
        'consent_value_required' => 'La valeur du consentement est obligatoire.',
        'consent_value_boolean' => 'La valeur du consentement doit être un booléen.',
        'format_required' => 'Le format d\'exportation est obligatoire.',
        'data_categories_required' => 'Il est requis de sélectionner au moins une catégorie de données.',
        'data_categories_format' => 'Le format des catégories de données n\'est pas valide.',
        'data_categories_min' => 'Il est requis de sélectionner au moins une catégorie de données.',
        'data_categories_distinct' => 'Les catégories de données doivent être distinctes.',
        'export_password_required' => 'Le mot de passe est obligatoire lorsque la protection par mot de passe est activée.',
        'export_password_min' => 'Le mot de passe doit comporter au moins 8 caractères.',
        'restriction_type_required' => 'Le type de restriction est obligatoire.',
        'restriction_reason_required' => 'La raison de la restriction est obligatoire.',
        'notes_max' => 'Les notes ne peuvent pas dépasser 500 caractères.',
        'reporter_name_required' => 'Votre nom est obligatoire.',
        'reporter_email_required' => 'Votre e-mail est obligatoire.',
        'reporter_email_format' => 'Entrez une adresse e-mail valide.',
        'incident_date_required' => 'La date de l\'incident est obligatoire.',
        'incident_date_format' => 'La date de l\'incident doit être une date valide.',
        'incident_date_past' => 'La date de l\'incident doit être dans le passé ou aujourd\'hui.',
        'breach_description_required' => 'La description de la violation est obligatoire.',
        'breach_description_min' => 'La description de la violation doit comporter au moins 20 caractères.',
        'affected_data_required' => 'Les informations sur les données compromises sont obligatoires.',
        'discovery_method_required' => 'La méthode de découverte est obligatoire.',
        'supporting_evidence_format' => 'Les preuves doivent être un fichier PDF, JPG, JPEG, PNG, TXT, DOC ou DOCX.',
        'supporting_evidence_max' => 'Le fichier de preuves ne peut pas dépasser 10 Mo.',
        'consent_to_contact_required' => 'Le consentement au contact est obligatoire.',
        'consent_to_contact_accepted' => 'Le consentement au contact doit être accepté.',
        'required_consent_message' => 'Ce consentement est requis pour utiliser la plateforme.',
        'confirm_deletion_required' => 'Vous devez confirmer que vous comprenez les conséquences de la suppression du compte.',
        'form_error_title' => 'Corrigez les erreurs ci-dessous',
        'form_error_message' => 'Il y a une ou plusieurs erreurs dans le formulaire qui doivent être corrigées.',
    ],

    // Messages d\'Erreur
    'errors' => [
        'general' => 'Une erreur inattendue s\'est produite.',
        'unauthorized' => 'Vous n\'êtes pas autorisé à effectuer cette action.',
        'forbidden' => 'Cette action est interdite.',
        'not_found' => 'La ressource demandée n\'a pas été trouvée.',
        'validation_failed' => 'Les données envoyées ne sont pas valides.',
        'rate_limited' => 'Trop de demandes. Réessayez plus tard.',
        'service_unavailable' => 'Le service n\'est pas disponible pour le moment. Réessayez plus tard.',
    ],

    'requests' => [
        'types' => [
            'consent_update' => 'Demande de mise à jour du consentement envoyée.',
            'data_export' => 'Demande d\'exportation des données envoyée.',
            'processing_restriction' => 'Demande de restriction de traitement envoyée.',
            'account_deletion' => 'Demande de suppression de compte envoyée.',
            'breach_report' => 'Signalement de violation de données envoyé.',
            'erasure' => 'Demande de suppression des données envoyée.',
            'access' => 'Demande d\'accès aux données envoyée.',
            'rectification' => 'Demande de rectification des données envoyée.',
            'objection' => 'Demande d\'opposition au traitement envoyée.',
            'restriction' => 'Demande de limitation du traitement envoyée.',
            'portability' => 'Demande de portabilité des données envoyée.',
        ],
    ],

    // Version Information
    'current_version' => 'Version Actuelle',
    'version' => 'Version : 1.0',
    'effective_date' => 'Date d\'Entrée en Vigueur : 30 Septembre 2025',
    'last_updated' => 'Dernière Mise à Jour : 30 Septembre 2025, 17:41',

    // Actions
    'download_pdf' => 'Télécharger PDF',
    'print' => 'Imprimer',

    'modal' => [
        'clarification' => [
            'title' => 'Clarification Nécessaire',
            'explanation' => 'Pour garantir votre sécurité, nous devons comprendre la raison de votre action :',
        ],
        'revoke_button_text' => 'J\'ai changé d\'avis',
        'revoke_description' => 'Vous souhaitez simplement retirer le consentement précédemment donné.',
        'disavow_button_text' => 'Je ne reconnais pas cette action',
        'disavow_description' => 'Vous n\'avez jamais donné ce consentement (possible problème de sécurité).',

        'confirmation' => [
            'title' => 'Confirmer le Protocole de Sécurité',
            'warning' => 'Cette action activera un protocole de sécurité qui inclut :',
        ],
        'confirm_disavow' => 'Oui, activer le protocole de sécurité',
        'final_warning' => 'Ne proceedez que si vous êtes certain de n\'avoir jamais autorisé cette action.',

        'consequences' => [
            'consent_revocation' => 'Retrait immédiat du consentement',
            'security_notification' => 'Notification à l\'équipe de sécurité',
            'account_review' => 'Vérifications supplémentaires possibles sur le compte',
            'email_confirmation' => 'E-mail de confirmation avec instructions',
        ],

        'security' => [
            'title' => 'Protocole de Sécurité Activé',
            'understood' => 'Compris',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Section Notifications RGPD
    |--------------------------------------------------------------------------
    | Déplacé de `notification.php` pour centralisation.
    */
    'notifications' => [
        'acknowledged' => 'Prise en compte enregistrée.',
        'consent_updated' => [
            'title' => 'Préférences de Confidentialité Mises à Jour',
            'content' => 'Vos préférences de consentement ont été mises à jour avec succès.',
        ],
        'data_exported' => [
            'title' => 'Votre Exportation de Données est Prête',
            'content' => 'Votre demande d\'exportation de données a été traitée. Vous pouvez télécharger le fichier via le lien fourni.',
        ],
        'processing_restricted' => [
            'title' => 'Restriction de Traitement Appliquée',
            'content' => 'Nous avons appliqué avec succès votre demande de restriction du traitement des données pour la catégorie : :type.',
        ],
        'account_deletion_requested' => [
            'title' => 'Demande de Suppression de Compte Reçue',
            'content' => 'Nous avons reçu votre demande de suppression de votre compte. Le processus sera terminé dans :days jours. Pendant cette période, vous pouvez encore annuler la demande en vous reconnectant.',
        ],
        'account_deletion_processed' => [
            'title' => 'Compte Supprimé avec Succès',
            'content' => 'Comme demandé, votre compte et les données associées ont été définitivement supprimés de notre plateforme. Nous sommes désolés de vous voir partir.',
        ],
        'breach_report_received' => [
            'title' => 'Signalement de Violation Reçu',
            'content' => 'Merci pour votre signalement. Il a été reçu avec l\'ID #:report_id et notre équipe de sécurité l\'examine.',
        ],
        'status' => [
            'pending_user_confirmation' => 'En attente de confirmation de l\'utilisateur',
            'user_confirmed_action' => 'Action de l\'utilisateur confirmée',
            'user_revoked_consent' => 'Action de l\'utilisateur retirée',
            'user_disavowed_suspicious' => 'Action de l\'utilisateur non reconnue',
        ],
    ],

    'consent_management' => [
        'title' => 'Gestion des Consentements',
        'subtitle' => 'Contrôlez l\'utilisation de vos données personnelles',
        'description' => 'Ici, vous pouvez gérer vos préférences de consentement pour différents objectifs et services.',
        'update_preferences' => 'Mettre à jour vos préférences de consentement',
        'preferences_updated' => 'Vos préférences de consentement ont été mises à jour avec succès.',
        'preferences_update_error' => 'Une erreur s\'est produite lors de la mise à jour de vos préférences de consentement. Veuillez réessayer.',
    ],

    // Pied de Page
    'privacy_policy' => 'Politique de Confidentialité',
    'terms_of_service' => 'Conditions d\'Utilisation',
    'all_rights_reserved' => 'Tous droits réservés.',
    'navigation_label' => 'Navigation RGPD',
    'main_content_label' => 'Contenu principal RGPD',
];
