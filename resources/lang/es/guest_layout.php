<?php

return [
    'hero_left_content_aria_label' => 'Contenido de la visión del proyecto',
    'hero_right_content_aria_label' => 'Contenido del impacto personal',

    // Meta and titles
    'default_title' => 'FlorenceEGI | EGI - Inventario de Bienes Ecológicos',
    'default_description' => 'Explora, crea y colecciona activos digitales ecológicos (EGI) únicos en FlorenceEGI. Cada obra apoya proyectos concretos de protección ambiental. Únete al Renacimiento Digital del arte y la sostenibilidad.',

    // Schema.org
    'schema_website_name' => 'FlorenceEGI | EGI',
    'schema_website_description' => 'Plataforma para la creación e intercambio de Inventarios de Bienes Ecológicos (EGI) que financian proyectos ambientales.',
    'schema_organization_name' => 'Frangette Asociación de Promoción Cultural',

    // User Types
    'fegi_user_type' => [
        'committee' => 'Activador',
        'collector' => 'Coleccionista',
        'commissioner' => 'Comisionado',
        'creator' => 'Creador',
        'patron' => 'Mecenas',
        'epp' => 'EPP',
        'company' => 'Empresa',
        'trader_pro' => 'Trader Profesional',
        'pa_entity' => 'Administración Pública',
        'natan' => 'Natan',
        'frangette' => 'Frangette',
    ],

    'fegi_user_type_short' => [
        'committee' => 'Activador',
        'collector' => 'Coleccionista',
        'commissioner' => 'Comisionado',
        'creator' => 'Creador',
        'patron' => 'Mecenas',
        'epp' => 'EPP',
        'company' => 'Empresa',
        'trader_pro' => 'Trader Pro',
        'pa_entity' => 'AP',
        'natan' => 'Natan',
        'inspector' => 'Inspector',
        'frangette' => 'Frangette',
    ],

    // Header/Navbar
    'header_aria_label' => 'Encabezado del sitio',
    'logo_aria_label' => 'Ir a la página de inicio de Florence EGI',
    'logo_alt_text' => 'Logotipo de la plataforma Florence EGI',
    'brand_name' => 'Florence EGI',
    'navbar_brand_name' => 'Florence EGI Nuevo Renacimiento Digital',
    'desktop_nav_aria_label' => 'Navegación principal de escritorio',

    // Navigation items
    'home' => 'Inicio',
    'home_link_aria_label' => 'Ir a la página de inicio',
    'collections' => 'Colecciones',
    'collections_link_aria_label' => 'Ver todas las colecciones públicas',
    'my_galleries' => 'Mis Galerías',
    'my_galleries_dropdown_aria_label' => 'Abrir menú de mis galerías',
    'loading_galleries' => 'Cargando galerías...',
    'no_galleries_found' => 'No se encontraron galerías.',
    'create_one_question' => '¿Crear una?',
    'error_loading_galleries' => 'Error al cargar las galerías.',
    'epps' => 'PPA',
    'epps_link_aria_label' => 'Ver Proyectos de Protección Ambiental',
    'create_egi' => 'Crear EGI',
    'create_egi_aria_label' => 'Crear un nuevo EGI',
    'create_collection' => 'Crear Colección',
    'create_collection_aria_label' => 'Crear una nueva galería',

    // Current collection badge
    'current_collection_badge_aria_label' => 'Galería activa actual',

    // Wallet
    'connect_wallet' => 'Conectar Cartera',
    'connect_wallet_aria_label' => 'Conectar tu cartera Algorand',
    'wallet' => ' Cartera',
    'dashboard' => 'Panel de control',
    'dashboard_link_aria_label' => 'Ir a tu panel de control',
    'copy_address' => 'Copiar Dirección',
    'copy_wallet_address_aria_label' => 'Copiar tu dirección de cartera',
    'disconnect' => 'Desconectar',
    'disconnect_wallet_aria_label' => 'Desconectar tu cartera o salir',

    // Auth
    'login' => 'Iniciar sesión',
    'login_link_aria_label' => 'Iniciar sesión en tu cuenta',
    'register' => 'Registrarse',
    'register_link_aria_label' => 'Registrar una nueva cuenta',

    // Mobile menu
    'open_mobile_menu_sr' => 'Abrir menú principal',
    'mobile_nav_aria_label' => 'Navegación móvil principal',
    'mobile_home_link_aria_label' => 'Ir a la página de inicio',
    'mobile_collections_link_aria_label' => 'Ver todas las colecciones públicas',
    'mobile_epps_link_aria_label' => 'Ver Proyectos de Protección Ambiental',
    'mobile_create_egi_aria_label' => 'Crear un nuevo EGI',
    'mobile_create_collection_aria_label' => 'Crear una nueva galería',
    'mobile_connect_wallet_aria_label' => 'Conectar tu cartera Algorand',
    'mobile_login_link_aria_label' => 'Iniciar sesión en tu cuenta',
    'mobile_register_link_aria_label' => 'Registrar una nueva cuenta',

    // Hero section
    'hero_carousel_aria_label' => 'Carrusel de EGI destacados',
    'hero_intro_aria_label' => 'Introducción Hero',
    'hero_featured_content_aria_label' => 'Contenido destacado bajo la sección hero',

    // Footer
    'footer_sr_heading' => 'Contenido del pie de página y enlaces legales',
    'copyright_holder' => 'Frangette APS',
    'all_rights_reserved' => 'Todos los derechos reservados.',
    'privacy_policy' => 'Política de privacidad',
    'cookie_settings' => 'Configuración de cookies',
    'total_plastic_recovered' => 'Total de Plástico Recuperado',
    'algorand_blue_mission' => 'Algorand Misión Azul',

    // Modal
    'upload_modal_title' => 'Modal de carga de EGI',
    'close_upload_modal_aria_label' => 'Cerrar modal de carga de EGI',

    // Hidden elements
    'logout_sr_button' => 'Cerrar sesión',

    // --- ADDITIONS FOR FRONTEND TYPESCRIPT ---

    // Padmin messages
    'padminGreeting' => 'Hola, soy Padmin.',
    'padminReady' => 'Sistema listo.',

    // Error messages
    'errorModalNotFoundConnectWallet' => 'Modal de conexión de cartera no encontrado',
    'errorConnectionFailed' => 'Conexión fallida',
    'errorConnectionGeneric' => 'Error de conexión genérico',
    'errorEgiFormOpen' => 'Error al abrir el formulario EGI',
    'errorUnexpected' => 'Ocurrió un error inesperado',
    'errorWalletDropdownMissing' => 'Menú desplegable de cartera no encontrado',
    'errorNoWalletToCopy' => 'No hay dirección de cartera para copiar',
    'errorCopyAddress' => 'Error al copiar la dirección',
    'errorLogoutFormMissing' => 'Formulario de cierre de sesión no encontrado',
    'errorApiDisconnect' => 'Error durante la desconexión',
    'errorGalleriesListUIDOM' => 'Elementos de la interfaz de galerías no encontrados',
    'errorFetchCollections' => 'Error al cargar las colecciones',
    'errorLoadingGalleries' => 'Error al cargar las galerías',
    'errorMobileMenuElementsMissing' => 'Elementos del menú móvil no encontrados',
    'errorTitle' => 'Error',
    'warningTitle' => 'Advertencia',

    // UI states and messages
    'connecting' => 'Conectando...',
    'copied' => 'Copiado',
    'switchingGallery' => 'Cambiando galería...',
    'pageWillReload' => 'La página se recargará',

    // Wallet states
    'walletAddressRequired' => 'Dirección de cartera requerida',
    'walletConnectedTitle' => 'Cartera Conectada',
    'walletDefaultText' => 'Cartera',
    'walletAriaLabelLoggedIn' => 'Cartera {shortAddress} - Usuario autenticado: {status}',
    'walletAriaLabelConnected' => 'Cartera {shortAddress} - {status}',
    'loggedInStatus' => 'Autenticado',
    'connectedStatusWeak' => 'Conectado',
    'disconnectedTitle' => 'Desconectado',
    'disconnectedTextWeak' => 'Tu cartera ha sido desconectada',

    // Registration
    'registrationRequiredTitle' => 'Registro Requerido',
    'registrationRequiredTextCollections' => 'Es necesario un registro completo para crear colecciones',
    'registerNowButton' => 'Regístrate ahora',
    'laterButton' => 'Más tarde',

    // Galleries
    'byCreator' => 'por {creator}',
    'gallerySwitchedTitle' => 'Galería Cambiada',
    'gallerySwitchedText' => 'Ahora estás trabajando en la galería "{galleryName}"',
    'editCurrentGalleryTitle' => 'Editar la galería "{galleryName}"',
    'viewCurrentGalleryTitle' => 'Ver la galería "{galleryName}"',
    'myGalleries' => 'Mis Galerías',
    'myGalleriesOwned' => 'Galerías Propias',
    'myGalleriesCollaborations' => 'Colaboraciones',

    // Secret Link system
    'wallet_secret_required' => 'Código secreto requerido',
    'wallet_invalid_secret' => 'Código secreto no válido',
    'wallet_existing_connection' => 'Cartera conectada con éxito',
    'wallet_new_connection' => 'Nueva cartera registrada con éxito',
    'wallet_disconnected_successfully' => 'Cartera desconectada con éxito',
];