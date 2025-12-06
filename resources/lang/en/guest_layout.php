<?php

return [
    'hero_left_content_aria_label' => 'Project vision content',
    'hero_right_content_aria_label' => 'Personal impact content',

    // Meta and titles
    'default_title' => 'FlorenceEGI | EGI - Ecological Goods Invent',
    'default_description' => 'Explore, create, and collect unique ecological digital assets (EGI) on FlorenceEGI. Each work supports tangible environmental protection projects. Join the Digital Renaissance of art and sustainability.',

    // Schema.org
    'schema_website_name' => 'FlorenceEGI | EGI',
    'schema_website_description' => 'Platform for the creation and exchange of Ecological Goods Invent (EGI) that fund environmental projects.',
    'schema_organization_name' => 'Frangette Cultural Promotion Association',

    // User Types
    'fegi_user_type' => [
        'committee' => 'Activator',
        'collector' => 'Collector',
        'commissioner' => 'Commissioner',
        'creator' => 'Creator',
        'patron' => 'Patron',
        'epp' => 'EPP',
        'company' => 'Company',
        'trader_pro' => 'Professional Trader',
        'pa_entity' => 'Public Administration',
        'natan' => 'Natan',
    ],

    'fegi_user_type_short' => [
        'committee' => 'Activator',
        'collector' => 'Collector',
        'commissioner' => 'Commissioner',
        'creator' => 'Creator',
        'patron' => 'Patron',
        'epp' => 'EPP',
        'company' => 'Company',
        'trader_pro' => 'Trader Pro',
        'pa_entity' => 'PA',
        'natan' => 'Natan',
        'inspector' => 'Inspector',
    ],

    // Header/Navbar
    'header_aria_label' => 'Site header',
    'logo_aria_label' => 'Go to Florence EGI Homepage',
    'logo_alt_text' => 'Florence EGI platform logo',
    'brand_name' => 'Florence EGI',
    'navbar_brand_name' => 'Florence EGI New Digital Renaissance',
    'desktop_nav_aria_label' => 'Desktop main navigation',

    // Navigation items
    'home' => 'Home',
    'creators' => 'Creators',
    'collectors' => 'Collectors',
    'companies' => 'Companies',
    'companies_link_aria_label' => 'View all companies',
    'home_link_aria_label' => 'Go to Homepage',
    'collections' => 'Collections',
    'collections_link_aria_label' => 'View all public collections',
    'collectors_link_aria_label' => 'View all EGI collectors',
    'my_galleries' => 'My Galleries',
    'my_galleries_dropdown_aria_label' => 'Open my galleries menu',
    'loading_galleries' => 'Loading galleries...',
    'no_galleries_found' => 'No galleries found.',
    'create_one_question' => 'Create one?',
    'error_loading_galleries' => 'Error loading galleries.',
    'epps' => 'EPP',
    'epps_link_aria_label' => 'View Environmental Protection Projects',
    'create_egi' => 'Create EGI',
    'create_egi_aria_label' => 'Create new EGI',
    'create_collection' => 'Create Collection',
    'create_collection_aria_label' => 'Create new gallery',

    // Current collection badge
    'current_collection_badge_aria_label' => 'Current active gallery',

    // Wallet
    'connect_wallet' => 'Connect Wallet',
    'connect_wallet_aria_label' => 'Connect your Algorand wallet',
    'wallet' => 'Wallet',
    'dashboard' => 'Dashboard',
    'dashboard_link_aria_label' => 'Go to your dashboard',
    'copy_address' => 'Copy Address',
    'copy_wallet_address_aria_label' => 'Copy your wallet address',
    'disconnect' => 'Disconnect',
    'disconnect_wallet_aria_label' => 'Disconnect your wallet or log out',

    // Auth
    'login' => 'Log In',
    'login_link_aria_label' => 'Log in to your account',
    'register' => 'Register',
    'register_link_aria_label' => 'Register a new account',

    // Mobile menu
    'open_mobile_menu_sr' => 'Open main menu',
    'mobile_nav_aria_label' => 'Mobile main navigation',
    'mobile_home_link_aria_label' => 'Go to Homepage',
    'mobile_collections_link_aria_label' => 'View all public collections',
    'mobile_epps_link_aria_label' => 'View Environmental Protection Projects',
    'mobile_create_egi_aria_label' => 'Create new EGI',
    'mobile_create_collection_aria_label' => 'Create new gallery',
    'mobile_connect_wallet_aria_label' => 'Connect your Algorand wallet',
    'mobile_login_link_aria_label' => 'Log in to your account',
    'mobile_register_link_aria_label' => 'Register a new account',

    // Hero section
    'hero_carousel_aria_label' => 'Featured EGI carousel',
    'hero_intro_aria_label' => 'Hero introduction',
    'hero_featured_content_aria_label' => 'Featured content below hero',

    // Footer
    'footer_sr_heading' => 'Footer content and legal links',
    'copyright_holder' => 'Frangette APS',
    'all_rights_reserved' => 'All rights reserved.',
    'privacy_policy' => 'Privacy Policy',
    'cookie_settings' => 'Cookie Settings',
    'total_plastic_recovered' => 'Total Plastic Recovered',
    'algorand_blue_mission' => 'Algorand Blue Mission',

    // Modal
    'upload_modal_title' => 'EGI Upload Modal',
    'close_upload_modal_aria_label' => 'Close EGI upload modal',

    // Hidden elements
    'logout_sr_button' => 'Log Out',

    // --- ADDITIONS FOR FRONTEND TYPESCRIPT ---

    // Padmin messages
    'padminGreeting' => 'Hello, I am Padmin.',
    'padminReady' => 'System ready.',

    // Error messages
    'errorModalNotFoundConnectWallet' => 'Wallet connection modal not found',
    'errorConnectionFailed' => 'Connection failed',
    'errorConnectionGeneric' => 'Generic connection error',
    'errorEgiFormOpen' => 'Error opening EGI form',
    'errorUnexpected' => 'An unexpected error occurred',
    'errorWalletDropdownMissing' => 'Wallet dropdown missing',
    'errorNoWalletToCopy' => 'No wallet address to copy',
    'errorCopyAddress' => 'Error copying address',
    'errorLogoutFormMissing' => 'Logout form not found',
    'errorApiDisconnect' => 'Error during disconnection',
    'errorGalleriesListUIDOM' => 'Gallery UI elements not found',
    'errorFetchCollections' => 'Error loading collections',
    'errorLoadingGalleries' => 'Error loading galleries',
    'errorMobileMenuElementsMissing' => 'Mobile menu elements missing',
    'errorTitle' => 'Error',
    'warningTitle' => 'Warning',

    // UI states and messages
    'connecting' => 'Connecting...',
    'copied' => 'Copied',
    'switchingGallery' => 'Switching gallery...',
    'pageWillReload' => 'The page will reload',

    // Wallet states
    'walletAddressRequired' => 'Wallet address required',
    'walletConnectedTitle' => 'Wallet Connected',
    'walletDefaultText' => 'Wallet',
    'walletAriaLabelLoggedIn' => 'Wallet {shortAddress} - User authenticated: {status}',
    'walletAriaLabelConnected' => 'Wallet {shortAddress} - {status}',
    'loggedInStatus' => 'Authenticated',
    'connectedStatusWeak' => 'Connected',
    'disconnectedTitle' => 'Disconnected',
    'disconnectedTextWeak' => 'Your wallet has been disconnected',

    // Registration
    'registrationRequiredTitle' => 'Registration Required',
    'registrationRequiredTextCollections' => 'Full registration is required to create collections',
    'registerNowButton' => 'Register Now',
    'laterButton' => 'Later',

    // Galleries
    'byCreator' => 'by {creator}',
    'gallerySwitchedTitle' => 'Gallery Switched',
    'gallerySwitchedText' => 'You are now working in the "{galleryName}" gallery',
    'editCurrentGalleryTitle' => 'Edit the "{galleryName}" gallery',
    'viewCurrentGalleryTitle' => 'View the "{galleryName}" gallery',
    'myGalleries' => 'My Galleries',
    'myGalleriesOwned' => 'Owned Galleries',
    'myGalleriesCollaborations' => 'Collaborations',

    // Secret Link system
    'wallet_secret_required' => 'Secret code required',
    'wallet_invalid_secret' => 'Invalid secret code',
    'wallet_existing_connection' => 'Wallet connected successfully',
    'wallet_new_connection' => 'New wallet registered successfully',
    'wallet_disconnected_successfully' => 'Wallet disconnected successfully',

    // Default Meta and Titles
    'default_title' => 'FlorenceEGI',
    'default_description' => 'FlorenceEGI: The marketplace where digital art meets sustainability and shared value.',
    'schema_website_name' => 'FlorenceEGI',
    'schema_website_description' => 'Platform for digital art with environmental impact and a virtuous economic model.',
    'schema_organization_name' => 'Frangette APS', // Or FlorenceEGI SRL if more appropriate for publisher

    // Header and Navigation
    'header_aria_label' => 'Main navigation menu',
    'logo_aria_label' => 'FlorenceEGI Homepage',
    'navbar_brand_name' => 'FlorenceEGI', // Or empty if you only want the logo
    'desktop_nav_aria_label' => 'Desktop navigation',
    'my_galleries' => 'My Galleries',
    'my_galleries_dropdown_aria_label' => 'Open My Galleries menu',
    'loading_galleries' => 'Loading galleries...',
    'no_galleries_found' => 'No galleries found.',
    'create_one_question' => 'Create one?',
    'error_loading_galleries' => 'Error loading galleries.',
    'current_collection_badge_aria_label' => 'Currently selected collection',
    'connect_wallet' => 'Connect Wallet',
    'connect_wallet_aria_label' => 'Connect your crypto wallet',
    'wallet' => 'Wallet', // Short text for connected wallet button
    'dashboard' => 'Dashboard',
    'dashboard_link_aria_label' => 'Go to your dashboard',
    'copy_address' => 'Copy Address',
    'copy_wallet_address_aria_label' => 'Copy your wallet address',
    'disconnect' => 'Disconnect',
    'disconnect_wallet_aria_label' => 'Disconnect your wallet',
    'login' => 'Login',
    'login_link_aria_label' => 'Login to your account',
    'register' => 'Register',
    'register_link_aria_label' => 'Create a new account',
    'toggle_mobile_menu_aria_label' => 'Open/Close mobile menu',
    'open_mobile_menu_sr' => 'Open navigation menu', // Screen reader only
    'mobile_nav_aria_label' => 'Mobile navigation',
    'mobile_connect_wallet_aria_label' => 'Connect your crypto wallet (mobile)',
    'mobile_login_link_aria_label' => 'Login (mobile)',
    'mobile_register_link_aria_label' => 'Register (mobile)',

    // Hero Section (generic layout texts, if any)
    'hero_left_content_aria_label' => 'Hero section left informational content',
    'hero_right_content_aria_label' => 'Hero section right informational content',
    'hero_carousel_aria_label' => 'Featured collections carousel',
    'hero_left_content_tablet_aria_label' => 'Informational content left (tablet)',
    'hero_right_content_tablet_aria_label' => 'Informational content right (tablet)',
    'hero_featured_content_aria_label' => 'Featured content below the hero',
    'scroll_down_aria_label' => 'Scroll down',

    // Actors Section (if titles were in layout and not x-actors-section component)
    'actors_section_title' => 'Join the FlorenceEGI Ecosystem',
    'actors_section_subtitle' => 'Discover how you can participate and benefit from our shared value model.',

    // Footer
    'footer_sr_heading' => 'Additional information and useful links', // Screen reader only
    'copyright_holder' => 'FlorenceEGI',
    'all_rights_reserved' => 'All rights reserved.',
    'algorand_blue_mission' => 'Algorand Blue Mission Partner',

    // Modals
    'close_upload_modal_aria_label' => 'Close upload modal',
    'logout_sr_button' => 'Logout from account', // Screen reader only

    // Environmental Stats (if "Total plastic recovered" string is generic layout)
    'total_plastic_recovered' => 'Total Plastic Recovered',
];
