<?php

return [

    /*
    |--------------------------------------------------------------------------
    | NFT Card System - Sistema de Tarjetas NFT
    |--------------------------------------------------------------------------
    */

    // Insignias y Estado
    'badge' => [
        'owned' => 'POSEÍDO',
        'media_content' => 'Contenido Multimedia',
    ],

    // Títulos
    'title' => [
        'untitled' => '✨ EGI Sin Título',
    ],

    // Plataforma
    'platform' => [
        'powered_by' => 'Powered by :platform',
    ],

    // Creador
    'creator' => [
        'created_by' => '👨‍🎨 Creado por:',
    ],

    // Precios
    'price' => [
        'purchased_for' => '💳 Comprado por',
        'price' => '💰 Precio',
        'floor' => '📊 Precio Mínimo',
    ],

    // Estado
    'status' => [
        'not_for_sale' => '🚫 No está en venta',
        'draft' => '⏳ Borrador',
    ],

    // Acciones
    'actions' => [
        'view' => 'Ver',
        'view_details' => 'Ver detalles del EGI',
        'reserve' => 'Activarlo',
        'outbid' => 'Pujar más para activar',
    ],

    // Detalles de reserva
    'reservation' => [
        'highest_bid' => 'Oferta Más Alta',
        'fegi_reservation' => 'Reserva FEGI',
        'strong_bidder' => 'Mejor Postor',
        'weak_bidder' => 'Código FEGI',
        'activator' => 'Co Creador',
        'activated_by' => 'Activado por',
    ],

    'carousel' => [
        // Dynamic Headers
        'headers' => [
            'egi_list' => 'EGI',
            'egi_card' => 'EGI',
            'creators' => 'Artistas',
            'collections' => 'Colecciones',
            'collectors' => 'Activadores'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero Coverflow - Sección Hero con Efecto Coverflow 3D
    |--------------------------------------------------------------------------
    */

    'hero_coverflow' => [
        'title' => 'Activar un EGI es dejar huella.',
        'subtitle' => 'Tu nombre permanece para siempre junto al del Creator: sin ti, la obra no existiría.',
        'carousel_mode' => 'Vista Carousel',
        'list_mode' => 'Vista Cuadrícula',
        'carousel_label' => 'Carrusel de obras destacadas',
        'no_egis' => 'No hay obras destacadas disponibles en este momento.',
        'navigation' => [
            'previous' => 'Obra anterior',
            'next' => 'Obra siguiente',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sistema Dossier - Dossier System
    |--------------------------------------------------------------------------
    */
    'dossier' => [
        'title' => 'Dossier de Imágenes',
        'loading' => 'Cargando dossier...',
        'view_complete' => 'Ver dossier de imágenes completo',
        'close' => 'Cerrar dossier',

        // Artwork Info
        'artwork_info' => 'Información de la Obra',
        'author' => 'Autor',
        'year' => 'Año',
        'internal_id' => 'ID Interno',

        // Dossier Info  
        'dossier_info' => 'Información del Dossier',
        'images_count' => 'Imágenes',
        'type' => 'Tipo',
        'utility_gallery' => 'Galería de Utilidad',

        // Gallery
        'gallery_title' => 'Galería de Imágenes',
        'image_number' => 'Imagen :number',
        'image_of_total' => 'Imagen :current de :total',

        // States
        'no_utility_title' => 'Dossier no disponible',
        'no_utility_message' => 'No hay imágenes adicionales disponibles para esta obra.',
        'no_utility_description' => 'El dossier de imágenes adicionales aún no ha sido configurado para esta obra.',

        'no_images_title' => 'No hay imágenes disponibles',
        'no_images_message' => 'El dossier existe pero aún no contiene imágenes.',
        'no_images_description' => 'Las imágenes adicionales serán agregadas en el futuro por el creador de la obra.',

        'error_title' => 'Error',
        'error_loading' => 'Error al cargar el dossier',

        // Navigation
        'previous_image' => 'Imagen anterior',
        'next_image' => 'Imagen siguiente',
        'close_viewer' => 'Cerrar visor',
        'of' => 'de',

        // Zoom Controls
        'zoom_help' => 'Usa la rueda del ratón o táctil para zoom • Arrastra para mover',
        'zoom_in' => 'Acercar',
        'zoom_out' => 'Alejar',
        'zoom_reset' => 'Restablecer zoom',
        'zoom_fit' => 'Ajustar a pantalla',
    ],

];
