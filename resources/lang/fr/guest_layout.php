<?php

return [
    'hero_left_content_aria_label' => 'Contenu de la vision du projet',
    'hero_right_content_aria_label' => 'Contenu de l\'impact personnel',

    // Meta and titles
    'default_title' => 'FlorenceEGI | EGI - Inventaire de Biens Écologiques',
    'default_description' => 'Explorez, créez et collectionnez des actifs numériques écologiques (EGI) uniques sur FlorenceEGI. Chaque œuvre soutient des projets concrets de protection de l\'environnement. Rejoignez la Renaissance numérique de l\'art et de la durabilité.',

    // Schema.org
    'schema_website_name' => 'FlorenceEGI | EGI',
    'schema_website_description' => 'Plateforme pour la création et l\'échange d\'Inventaires de Biens Écologiques (EGI) qui financent des projets environnementaux.',
    'schema_organization_name' => 'Frangette Association de Promotion Culturelle',

    // User Types
    'fegi_user_type' => [
        'committee' => 'Activateur',
        'collector' => 'Collectionneur',
        'commissioner' => 'Commanditaire',
        'creator' => 'Créateur',
        'patron' => 'Mécène',
        'epp' => 'EPP',
        'company' => 'Entreprise',
        'trader_pro' => 'Trader Professionnel',
        'pa_entity' => 'Administration Publique',
        'natan' => 'Natan',
        'frangette' => 'Frangette',
    ],

    'fegi_user_type_short' => [
        'committee' => 'Activateur',
        'collector' => 'Collectionneur',
        'commissioner' => 'Commanditaire',
        'creator' => 'Créateur',
        'patron' => 'Mécène',
        'epp' => 'EPP',
        'company' => 'Entreprise',
        'trader_pro' => 'Trader Pro',
        'pa_entity' => 'AP',
        'natan' => 'Natan',
        'inspector' => 'Inspecteur',
        'frangette' => 'Frangette',
    ],

    // Header/Navbar
    'header_aria_label' => 'En-tête du site',
    'logo_aria_label' => 'Aller à la page d\'accueil de Florence EGI',
    'logo_alt_text' => 'Logo de la plateforme Florence EGI',
    'brand_name' => 'Florence EGI',
    'navbar_brand_name' => 'Florence EGI Nouvelle Renaissance Numérique',
    'desktop_nav_aria_label' => 'Navigation principale pour ordinateur',

    // Navigation items
    'home' => 'Accueil',
    'home_link_aria_label' => 'Aller à la page d\'accueil',
    'collections' => 'Collections',
    'collections_link_aria_label' => 'Voir toutes les collections publiques',
    'my_galleries' => 'Mes Galeries',
    'my_galleries_dropdown_aria_label' => 'Ouvrir le menu de mes galeries',
    'loading_galleries' => 'Chargement des galeries...',
    'no_galleries_found' => 'Aucune galerie trouvée.',
    'create_one_question' => 'En créer une ?',
    'error_loading_galleries' => 'Erreur de chargement des galeries.',
    'epps' => 'PPE',
    'epps_link_aria_label' => 'Voir les Projets de Protection Environnementale',
    'create_egi' => 'Créer un EGI',
    'create_egi_aria_label' => 'Créer un nouvel EGI',
    'create_collection' => 'Créer une Collection',
    'create_collection_aria_label' => 'Créer une nouvelle galerie',

    // Current collection badge
    'current_collection_badge_aria_label' => 'Galerie active actuelle',

    // Wallet
    'connect_wallet' => 'Connecter le Portefeuille',
    'connect_wallet_aria_label' => 'Connecter votre portefeuille Algorand',
    'wallet' => 'Portefeuille',
    'dashboard' => 'Tableau de bord',
    'dashboard_link_aria_label' => 'Aller à votre tableau de bord',
    'copy_address' => 'Copier l\'Adresse',
    'copy_wallet_address_aria_label' => 'Copier votre adresse de portefeuille',
    'disconnect' => 'Déconnecter',
    'disconnect_wallet_aria_label' => 'Déconnecter votre portefeuille ou quitter',

    // Auth
    'login' => 'Se connecter',
    'login_link_aria_label' => 'Se connecter à votre compte',
    'register' => 'S\'inscrire',
    'register_link_aria_label' => 'Créer un nouveau compte',

    // Mobile menu
    'open_mobile_menu_sr' => 'Ouvrir le menu principal',
    'mobile_nav_aria_label' => 'Navigation mobile principale',
    'mobile_home_link_aria_label' => 'Aller à la page d\'accueil',
    'mobile_collections_link_aria_label' => 'Voir toutes les collections publiques',
    'mobile_epps_link_aria_label' => 'Voir les Projets de Protection Environnementale',
    'mobile_create_egi_aria_label' => 'Créer un nouvel EGI',
    'mobile_create_collection_aria_label' => 'Créer une nouvelle galerie',
    'mobile_connect_wallet_aria_label' => 'Connecter votre portefeuille Algorand',
    'mobile_login_link_aria_label' => 'Se connecter à votre compte',
    'mobile_register_link_aria_label' => 'Créer un nouveau compte',

    // Hero section
    'hero_carousel_aria_label' => 'Carrousel des EGI en vedette',
    'hero_intro_aria_label' => 'Introduction Hero',
    'hero_featured_content_aria_label' => 'Contenu en vedette sous la section hero',

    // Footer
    'footer_sr_heading' => 'Contenu du pied de page et liens légaux',
    'copyright_holder' => 'Frangette APS',
    'all_rights_reserved' => 'Tous droits réservés.',
    'privacy_policy' => 'Politique de confidentialité',
    'cookie_settings' => 'Paramètres des cookies',
    'total_plastic_recovered' => 'Total de Plastique Récupéré',
    'algorand_blue_mission' => 'Algorand Mission Bleue',

    // Modal
    'upload_modal_title' => 'Modale de téléchargement d\'EGI',
    'close_upload_modal_aria_label' => 'Fermer la modale de téléchargement d\'EGI',

    // Hidden elements
    'logout SR_button' => 'Se déconnecter',

    // --- ADDITIONS FOR FRONTEND TYPESCRIPT ---

    // Padmin messages
    'padminGreeting' => 'Bonjour, je suis Padmin.',
    'padminReady' => 'Système prêt.',

    // Error messages
    'errorModalNotFoundConnectWallet' => 'Modale de connexion du portefeuille introuvable',
    'errorConnectionFailed' => 'Échec de la connexion',
    'errorConnectionGeneric' => 'Erreur de connexion générique',
    'errorEgiFormOpen' => 'Erreur lors de l\'ouverture du formulaire EGI',
    'errorUnexpected' => 'Une erreur inattendue s\'est produite',
    'errorWalletDropdownMissing' => 'Menu déroulant du portefeuille manquant',
    'errorNoWalletToCopy' => 'Aucune adresse de portefeuille à copier',
    'errorCopyAddress' => 'Erreur lors de la copie de l\'adresse',
    'errorLogoutFormMissing' => 'Formulaire de déconnexion introuvable',
    'errorApiDisconnect' => 'Erreur lors de la déconnexion',
    'errorGalleriesListUIDOM' => 'Éléments d\'interface des galeries introuvables',
    'errorFetchCollections' => 'Erreur lors du chargement des collections',
    'errorLoadingGalleries' => 'Erreur lors du chargement des galeries',
    'errorMobileMenuElementsMissing' => 'Éléments du menu mobile manquants',
    'errorTitle' => 'Erreur',
    'warningTitle' => 'Attention',

    // UI states and messages
    'connecting' => 'Connexion en cours...',
    'copied' => 'Copié',
    'switchingGallery' => 'Changement de galerie...',
    'pageWillReload' => 'La page va être rechargée',

    // Wallet states
    'walletAddressRequired' => 'Adresse du portefeuille requise',
    'walletConnectedTitle' => 'Portefeuille Connecté',
    'walletDefaultText' => 'Portefeuille',
    'walletAriaLabelLoggedIn' => 'Portefeuille {shortAddress} - Utilisateur authentifié : {status}',
    'walletAriaLabelConnected' => 'Portefeuille {shortAddress} - {status}',
    'loggedInStatus' => 'Authentifié',
    'connectedStatusWeak' => 'Connecté',
    'disconnectedTitle' => 'Déconnecté',
    'disconnectedTextWeak' => 'Votre portefeuille a été déconnecté',

    // Registration
    'registrationRequiredTitle' => 'Inscription Requise',
    'registrationRequiredTextCollections' => 'Une inscription complète est requise pour créer des collections',
    'registerNowButton' => 'S\'inscrire maintenant',
    'laterButton' => 'Plus tard',

    // Galleries
    'byCreator' => 'par {creator}',
    'gallerySwitchedTitle' => 'Galerie Changée',
    'gallerySwitchedText' => 'Vous travaillez maintenant dans la galerie "{galleryName}"',
    'editCurrentGalleryTitle' => 'Modifier la galerie "{galleryName}"',
    'viewCurrentGalleryTitle' => 'Voir la galerie "{galleryName}"',
    'myGalleries' => 'Mes Galeries',
    'myGalleriesOwned' => 'Galeries Possédées',
    'myGalleriesCollaborations' => 'Collaborations',

    // Secret Link system
    'wallet_secret_required' => 'Code secret requis',
    'wallet_invalid_secret' => 'Code secret non valide',
    'wallet_existing_connection' => 'Portefeuille connecté avec succès',
    'wallet_new_connection' => 'Nouveau portefeuille enregistré avec succès',
    'wallet_disconnected_successfully' => 'Portefeuille déconnecté avec succès',
];