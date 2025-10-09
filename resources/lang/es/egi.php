<?php

return [

    /*
    |--------------------------------------------------------------------------
    | EGI (Ecological Goods Invent) - Traducciones al Español
    |--------------------------------------------------------------------------
    |
    | Traducciones para el sistema CRUD de EGI en FlorenceEGI
    | Versión: 1.0.0 - Compatible con Oracode System 2.0
    |
    */

    // Meta y SEO
    'meta_description_default' => 'Detalles para EGI: :title',
    'image_alt_default' => 'Imagen EGI',
    'view_full' => 'Ver Completo',
    'artwork_loading' => 'Obra en Carga...',

    // Información Básica
    'by_author' => 'por :name',
    'unknown_creator' => 'Artista Desconocido',

    // Acciones Principales
    'like_button_title' => 'Añadir a Favoritos',
    'unlike_button_title' => 'Eliminar de Favoritos',
    'like_button_aria' => 'Añadir este EGI a tus favoritos',
    'unlike_button_aria' => 'Eliminar este EGI de tus favoritos',
    'share_button_title' => 'Compartir este EGI',

    'current_price' => 'Precio Actual',
    'not_currently_listed' => 'Por Activar',
    'contact_owner_availability' => 'Contacta al propietario para disponibilidad',
    'not_for_sale' => 'No en Venta',
    'not_for_sale_description' => 'Este EGI no está actualmente disponible para la compra',
    'liked' => 'Me Gusta',
    'add_to_favorites' => 'Añadir a Favoritos',
    'reserve_this_piece' => 'Actívalo',

    /*
    |--------------------------------------------------------------------------
    | Sistema de Tarjetas NFT - Sistema de Tarjetas NFT
    |--------------------------------------------------------------------------
    */

    // Insignias y Estados
    'badge' => [
        'owned' => 'POSEÍDO',
        'media_content' => 'Contenido Multimedia',
        'winning_bid' => 'PUJA GANADORA',
        'outbid' => 'SUPERADO',
        'not_owned' => 'NO POSEÍDO',
        'to_activate' => 'POR ACTIVAR',
        'activated' => 'ACTIVADO',
    ],

    // Títulos
    'title' => [
        'untitled' => '✨ EGI Sin Título',
    ],

    // Plataforma
    'platform' => [
        'powered_by' => 'Desarrollado por :platform',
    ],

    // Artista
    'creator' => [
        'created_by' => '👨‍🎨 Creado por:',
    ],

    // Precios
    'price' => [
        'purchased_for' => '💳 Comprado por',
        'price' => '💰 Precio',
        'floor' => '📊 Precio Base',
        'highest_bid' => '🏆 Puja Más Alta',
    ],

    // Reservas
    'reservation' => [
        'count' => 'Reservas',
        'highest_bidder' => 'Mejor Postor',
        'by' => 'por',
        'highest_bid' => 'Puja Más Alta',
        'fegi_reservation' => 'Reserva FEGI',
        'strong_bidder' => 'Mejor Postor',
        'weak_bidder' => 'Código FEGI',
        'activator' => 'Co Creador',
        'activated_by' => 'Activado por',
    ],

    // Nota de Moneda Original
    'originally_reserved_in' => 'Reservado originalmente en :currency por :amount',
    'originally_reserved_in_short' => 'Res. :currency :amount',

    // Estados
    'status' => [
        'not_for_sale' => '🚫 No en Venta',
        'draft' => '⏳ Borrador',
        // Phase 2: Availability status
        'login_required' => '🔐 Inicio de Sesión Requerido',
        'already_minted' => '✅ Ya Minteado',
        'not_available' => '⚠️ No Disponible',
    ],

    // Acciones
    'actions' => [
        'view' => 'Ver',
        'view_details' => 'Ver Detalles del EGI',
        'reserve' => 'Actívalo',
        'reserved' => 'Reservado',
        'outbid' => 'Superar para Activar',
        'view_history' => 'Historial',
        'reserve_egi' => 'Reservar :title',
        // Phase 2: Dual path actions
        'mint_now' => 'Mintear Ahora',
        'mint_direct' => 'Mintear Instantáneamente',
    ],

    // Sistema de Historial de Reservas
    'history' => [
        'title' => 'Historial de Reservas',
        'no_reservations' => 'No se encontraron reservas',
        'total_reservations' => '{1} :count reserva|[2,*] :count reservas',
        'current_highest' => 'Prioridad Máxima Actual',
        'superseded' => 'Prioridad Inferior',
        'created_at' => 'Creado el',
        'amount' => 'Monto',
        'type_strong' => 'Reserva Fuerte',
        'type_weak' => 'Reserva Débil',
        'loading' => 'Cargando historial...',
        'error' => 'Error al cargar el historial',
    ],

    // Secciones Informativas
    'properties' => 'Propiedades',
    'supports_epp' => 'Soporta EPP',
    'asset_type' => 'Tipo de Activo',
    'format' => 'Formato',
    'about_this_piece' => 'Acerca de Esta Obra',
    'default_description' => 'Esta obra digital única representa un momento de expresión creativa, capturando la esencia del arte digital en la era del blockchain.',
    'provenance' => 'Proveniencia',
    'view_full_collection' => 'Ver Colección Completa',

    /*
    |--------------------------------------------------------------------------
    | Sistema CRUD - Sistema de Edición
    |--------------------------------------------------------------------------
    */

    'crud' => [

        // Encabezado y Navegación
        'edit_egi' => 'Editar EGI',
        'toggle_edit_mode' => 'Activar/Desactivar Modo Edición',
        'start_editing' => 'Comenzar Edición',
        'save_changes' => 'Guardar Cambios',
        'cancel' => 'Cancelar',

        // Campo Título
        'title' => 'Título',
        'title_placeholder' => 'Introduce el título de la obra...',
        'title_hint' => 'Máximo 60 caracteres',
        'characters_remaining' => 'caracteres restantes',

        // Campo Descripción
        'description' => 'Descripción',
        'description_placeholder' => 'Describe tu obra, su historia y su significado...',
        'description_hint' => 'Cuenta la historia detrás de tu creación',

        // Campo Precio
        'price' => 'Precio',
        'price_placeholder' => '0.00',
        'price_hint' => 'Precio en ALGO (deja en blanco si no está a la venta)',
        'price_locked_message' => 'Precio bloqueado - EGI ya reservado',

        // Campo Fecha de Creación
        'creation_date' => 'Fecha de Creación',
        'creation_date_hint' => '¿Cuándo creaste esta obra?',

        // Campo Publicado
        'is_published' => 'Publicado',
        'is_published_hint' => 'Hacer la obra visible públicamente',

        // Modo Visualización - Estado Actual
        'current_title' => 'Título Actual',
        'no_title' => 'Sin título establecido',
        'current_price' => 'Precio Actual',
        'price_not_set' => 'Precio no establecido',
        'current_status' => 'Estado de Publicación',
        'status_published' => 'Publicado',
        'status_draft' => 'Borrador',

        // Sistema de Eliminación
        'delete_egi' => 'Eliminar EGI',
        'delete_confirmation_title' => 'Confirmar Eliminación',
        'delete_confirmation_message' => '¿Estás seguro de que deseas eliminar este EGI? Esta acción no se puede deshacer.',
        'delete_confirm' => 'Eliminar Permanentemente',

        // Mensajes de Validación
        'title_required' => 'El título es obligatorio',
        'title_max_length' => 'El título no puede superar los 60 caracteres',
        'price_numeric' => 'El precio debe ser un número válido',
        'price_min' => 'El precio no puede ser negativo',
        'creation_date_format' => 'Formato de fecha no válido',

        // Mensajes de Éxito
        'update_success' => '¡EGI actualizado con éxito!',
        'delete_success' => 'EGI eliminado con éxito.',

        // Mensajes de Error
        'update_error' => 'Error al actualizar el EGI.',
        'delete_error' => 'Error al eliminar el EGI.',
        'permission_denied' => 'No tienes los permisos necesarios para esta acción.',
        'not_found' => 'EGI no encontrado.',

        // Mensajes Generales
        'no_changes_detected' => 'No se detectaron cambios.',
        'unsaved_changes_warning' => 'Tienes cambios sin guardar. ¿Estás seguro de que deseas salir?',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquetas Responsivas - Móvil/Tablet
    |--------------------------------------------------------------------------
    */

    'mobile' => [
        'edit_egi_short' => 'Editar',
        'save_short' => 'Guardar',
        'delete_short' => 'Eliminar',
        'cancel_short' => 'Cancelar',
        'published_short' => 'Pub.',
        'draft_short' => 'Borrador',
    ],

    /*
    |--------------------------------------------------------------------------
    | Carrusel EGI - EGIs Destacados en la Página Principal
    |--------------------------------------------------------------------------
    */

    'carousel' => [
        'two_columns' => 'Vista de Lista',
        'three_columns' => 'Vista de Tarjeta',
        'navigation' => [
            'previous' => 'Anterior',
            'next' => 'Siguiente',
            'slide' => 'Ir a la diapositiva :number',
        ],
        'empty_state' => [
            'title' => 'No Hay Contenido Disponible',
            'subtitle' => '¡Vuelve pronto para nuevo contenido!',
            'no_egis' => 'No hay obras EGI disponibles en este momento.',
            'no_creators' => 'No hay artistas disponibles en este momento.',
            'no_collections' => 'No hay colecciones disponibles en este momento.',
            'no_collectors' => 'No hay coleccionistas disponibles en este momento.'
        ],

        // Botones de Tipo de Contenido
        'content_types' => [
            'egi_list' => 'Vista de Lista EGI',
            'egi_card' => 'Vista de Tarjeta EGI',
            'creators' => 'Artistas Destacados',
            'collections' => 'Colecciones de Arte',
            'collectors' => 'Mejores Coleccionistas'
        ],

        // Botones de Modo de Visualización
        'view_modes' => [
            'carousel' => 'Vista de Carrusel',
            'list' => 'Vista de Lista'
        ],

        // Etiquetas de Modo
        'carousel_mode' => 'Carrusel',
        'list_mode' => 'Lista',

        // Etiquetas de Contenido
        'creators' => 'Artistas',
        'collections' => 'Colecciones',
        'collectors' => 'Coleccionistas',

        // Encabezados Dinámicos
        'headers' => [
            'egi_list' => 'EGI',
            'egi_card' => 'EGI',
            'creators' => 'Artistas',
            'collections' => 'Colecciones',
            'collectors' => 'Activadores'
        ],

        // Secciones del Carrusel
        'sections' => [
            'egis' => 'EGIs Destacados',
            'creators' => 'Artistas Emergentes',
            'collections' => 'Colecciones Exclusivas',
            'collectors' => 'Mejores Coleccionistas'
        ],
        'view_all' => 'Ver Todos',
        'items' => 'elementos',

        // Título y Subtítulo para el Carrusel Multi-contenido
        'title' => '¡Activa un EGI!',
        'subtitle' => 'Activar una obra significa unirte a ella y ser reconocido para siempre como parte de su historia.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Vista de Lista - Modo Lista en la Página Principal
    |--------------------------------------------------------------------------
    */

    'list' => [
        'title' => 'Explora por Categoría',
        'subtitle' => 'Navega por las diferentes categorías para encontrar lo que buscas',

        'content_types' => [
            'egi_list' => 'Lista EGI',
            'creators' => 'Lista de Artistas',
            'collections' => 'Lista de Colecciones',
            'collectors' => 'Lista de Coleccionistas'
        ],

        'headers' => [
            'egi_list' => 'Obras EGI',
            'creators' => 'Artistas',
            'collections' => 'Colecciones',
            'collectors' => 'Coleccionistas'
        ],

        'empty_state' => [
            'title' => 'No se Encontraron Elementos',
            'subtitle' => 'Intenta seleccionar una categoría diferente',
            'no_egis' => 'No se encontraron obras EGI.',
            'no_creators' => 'No se encontraron artistas.',
            'no_collections' => 'No se encontraron colecciones.',
            'no_collectors' => 'No se encontraron coleccionistas.'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Carrusel de Escritorio - Carrusel EGI Solo para Escritorio
    |--------------------------------------------------------------------------
    */

    'desktop_carousel' => [
        'title' => 'Obras Digitales Destacadas',
        'subtitle' => 'Las mejores creaciones EGI de nuestra comunidad',
        'navigation' => [
            'previous' => 'Anterior',
            'next' => 'Siguiente',
            'slide' => 'Ir a la diapositiva :number',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Alternancia Móvil - Alternancia de Vista Móvil
    |--------------------------------------------------------------------------
    */

    'mobile_toggle' => [
        'title' => 'Explora FlorenceEGI',
        'subtitle' => 'Elige cómo quieres navegar por el contenido',
        'carousel_mode' => 'Vista de Carrusel',
        'list_mode' => 'Vista de Lista',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero Coverflow - Sección Hero con Efecto Coverflow 3D
    |--------------------------------------------------------------------------
    */

    'hero_coverflow' => [
        'title' => 'Activar un EGI es dejar una marca.',
        'subtitle' => 'Tu nombre permanece para siempre junto al del Creador: sin ti, la obra no existiría.',
        'carousel_mode' => 'Vista de Carrusel',
        'list_mode' => 'Vista de Cuadrícula',
        'carousel_label' => 'Carrusel de obras destacadas',
        'no_egis' => 'No hay obras destacadas disponibles en este momento.',
        'navigation' => [
            'previous' => 'Obra Anterior',
            'next' => 'Obra Siguiente',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquetas de Accesibilidad - Lectores de Pantalla
    |--------------------------------------------------------------------------
    */

    'a11y' => [
        'edit_form' => 'Formulario de Edición de EGI',
        'delete_button' => 'Botón Eliminar EGI',
        'toggle_edit' => 'Activar Modo Edición',
        'save_form' => 'Guardar Cambios de EGI',
        'close_modal' => 'Cerrar Ventana de Confirmación',
        'required_field' => 'Campo Obligatorio',
        'optional_field' => 'Campo Opcional',
    ],

    'collection' => [
        'part_of' => 'Parte de',
    ],

    // Colaboradores de la Colección
    'collection_collaborators' => 'Colaboradores',
    'owner' => 'Propietario',
    // 'creator' => 'Creador',
    'no_other_collaborators' => 'No hay otros colaboradores',

    /*
    |--------------------------------------------------------------------------
    | Certificado de Autenticidad (CoA)
    |--------------------------------------------------------------------------
    */

    'coa' => [
        'none' => 'Sin Certificado de Autenticidad',
        'title' => 'Certificado de Autenticidad',
        'status' => 'Estado',
        'issued' => 'Emitido el',
        'verification' => 'ID de Verificación',
        'copy' => 'Copiar',
        'copied' => '¡Copiado!',
        'view' => 'Ver',
        'pdf' => 'PDF',
        'reissue' => 'Reemitir',
        'issue' => 'Emitir Certificado',
        'annexes' => 'Anexos',
        'add_annex' => 'Añadir Anexo',
        'annex_coming_soon' => '¡Gestión de anexos disponible pronto!',
        'pro' => 'Pro',
        'unlock_pro' => 'Desbloquear con CoA Pro',
        'provenance' => 'Documentación de Proveniencia',
        'pdf_bundle' => 'Paquete PDF Profesional',
        'issue_description' => 'Emite un certificado para proporcionar prueba de autenticidad y desbloquear funciones Pro',
        'creator_only' => 'Solo el creador puede emitir certificados',
        'active' => 'Activo',
        'revoked' => 'Revocado',
        'expired' => 'Expirado',
        'manage_coa' => 'Gestionar CoA',
        'no_certificate' => 'Aún no se ha emitido ningún certificado',

        // Mensajes de JavaScript
        'confirm_issue' => '¿Emitir un Certificado de Autenticidad para este EGI?',
        'issued_success' => '¡Certificado emitido con éxito!',
        'confirm_reissue' => '¿Reemitir este certificado? Esto creará una nueva versión.',
        'reissued_success' => '¡Certificado reemitido con éxito!',
        'reissue_certificate_confirm' => '¿Estás seguro de que quieres reemitir este certificado?',
        'certificate_reissued_successfully' => '¡Certificado reemitido con éxito!',
        'error_reissuing_certificate' => 'Error al reemitir el certificado',
        'revocation_reason' => 'Motivo de Revocación:',
        'confirm_revoke' => '¿Revocar este certificado? Esta acción no se puede deshacer.',
        'revoked_success' => '¡Certificado revocado con éxito!',
        'error_issuing' => 'Error al emitir el certificado',
        'error_reissuing' => 'Error al reemitir el certificado',
        'error_revoking' => 'Error al revocar el certificado',
        'unknown_error' => 'Error Desconocido',
        'verify_any_certificate' => 'Verificar Cualquier Certificado',

        // Modal de Anexos
        'manage_annexes_title' => 'Gestionar Anexos CoA Pro',
        'annexes_description' => 'Añade documentación profesional para mejorar tu certificado',
        'provenance_tab' => 'Proveniencia',
        'condition_tab' => 'Condición',
        'exhibitions_tab' => 'Exposiciones',
        'photos_tab' => 'Fotos',
        'provenance_title' => 'Documentación de Proveniencia',
        'provenance_description' => 'Documenta la historia de propiedad y la cadena de autenticidad',
        'condition_title' => 'Informe de Condición',
        'condition_description' => 'Evaluación profesional de la condición física de la obra',
        'exhibitions_title' => 'Historial de Exposiciones',
        'exhibitions_description' => 'Registro de exposiciones públicas e historial de exhibición',
        'photos_title' => 'Fotografía Profesional',
        'photos_description' => 'Documentación de alta resolución y fotografía de detalle',
        'save_annex' => 'Guardar Anexo',
        'cancel' => 'Cancelar',
        'upload_files' => 'Subir Archivos',
        'drag_drop_files' => 'Arrastra y suelta archivos aquí, o haz clic para seleccionar',
        'max_file_size' => 'Tamaño máximo de archivo: 10MB por archivo',
        'supported_formats' => 'Formatos soportados: PDF, JPG, PNG, DOCX',

        // Formulario de Proveniencia
        'ownership_history_description' => 'Documenta la historia de propiedad y la cadena de autenticidad de esta obra',
        'previous_owners' => 'Propietarios Anteriores',
        'previous_owners_placeholder' => 'Lista los propietarios anteriores y las fechas de posesión...',
        'acquisition_details' => 'Detalles de Adquisición',
        'acquisition_details_placeholder' => '¿Cómo se adquirió esta obra? Incluye fechas, precios, casas de subastas...',
        'authenticity_sources' => 'Fuentes de Autenticidad',
        'authenticity_sources_placeholder' => 'Opiniones de expertos, catálogos razonados, archivos institucionales...',
        'save_provenance_data' => 'Guardar Datos de Proveniencia',

        // Formulario de Condición
        'condition_assessment_description' => 'Evaluación profesional del estado físico de la obra y necesidades de conservación',
        'overall_condition' => 'Condición General',
        'condition_excellent' => 'Excelente',
        'condition_very_good' => 'Muy Bueno',
        'condition_good' => 'Bueno',
        'condition_fair' => 'Regular',
        'condition_poor' => 'Pobre',
        'condition_notes' => 'Notas de Condición',
        'condition_notes_placeholder' => 'Descripción detallada de cualquier daño, restauraciones o problemas de conservación...',
        'conservation_history' => 'Historial de Conservación',
        'conservation_history_placeholder' => 'Restauraciones previas, tratamientos o intervenciones de conservación...',
        'save_condition_data' => 'Guardar Datos de Condición',

        // Formulario de Exposiciones
        'exhibition_history_description' => 'Registro de museos, galerías y exposiciones públicas donde esta obra ha sido exhibida',
        'exhibition_title' => 'Título de la Exposición',
        'exhibition_title_placeholder' => 'Nombre de la exposición...',
        'venue' => 'Sede',
        'venue_placeholder' => 'Nombre del museo, galería o institución...',
        'exhibition_dates' => 'Fechas de la Exposición',
        'exhibition_notes' => 'Notas',
        'exhibition_notes_placeholder' => 'Número de catálogo, menciones especiales, reseñas...',
        'add_exhibition' => 'Añadir Exposición',
        'save_exhibitions_data' => 'Guardar Datos de Exposiciones',

        // Formulario de Fotos
        'photo_documentation_description' => 'Imágenes de alta calidad para documentación y fines de archivo',
        'photo_type' => 'Tipo de Foto',
        'photo_overall' => 'Vista General',
        'photo_detail' => 'Detalle',
        'photo_raking' => 'Luz Rasante',
        'photo_uv' => 'Fotografía UV',
        'photo_infrared' => 'Infrarrojo',
        'photo_back' => 'Reverso',
        'photo_signature' => 'Firma/Marcas',
        'photo_frame' => 'Marco/Montaje',
        'photo_description' => 'Descripción',
        'photo_description_placeholder' => 'Describe qué muestra esta foto...',
        'save_photos_data' => 'Guardar Datos de Fotos',

        // Campos adicionales para el formulario de condición
        'select_condition' => 'Seleccionar condición...',
        'detailed_assessment' => 'Evaluación Detallada',
        'detailed_assessment_placeholder' => 'Descripción detallada de la condición, incluyendo cualquier daño, restauraciones o características especiales...',
        'conservation_history_placeholder' => 'Tratamientos de conservación previos, fechas y conservadores...',
        'assessor_information' => 'Información del Evaluador',
        'assessor_placeholder' => 'Nombre y credenciales del evaluador de la condición...',
        'save_condition_report' => 'Guardar Informe de Condición',

        // Campos del formulario de exposiciones
        'major_exhibitions' => 'Exposiciones Principales',
        'major_exhibitions_placeholder' => 'Lista las exposiciones principales, museos, galerías, fechas...',
        'publications_catalogues' => 'Publicaciones y Catálogos',
        'publications_placeholder' => 'Libros, catálogos, artículos donde esta obra ha sido publicada...',
        'awards_recognition' => 'Premios y Reconocimientos',
        'awards_placeholder' => 'Premios, reconocimientos, críticas recibidas...',
        'save_exhibition_history' => 'Guardar Historial de Exposiciones',
        'exhibition_history_description' => 'Registro de exposiciones donde esta obra ha sido exhibida',

        // Campos del formulario de fotos
        'click_upload_images' => 'Haz clic para subir imágenes',
        'png_jpg_webp' => 'PNG, JPG, WEBP hasta 10MB cada una',
        'photo_descriptions' => 'Descripciones de Fotos',
        'photo_descriptions_placeholder' => 'Describe las imágenes: condiciones de iluminación, detalles capturados, propósito...',
        'photographer_credits' => 'Créditos del Fotógrafo',
        'photographer_placeholder' => 'Nombre del fotógrafo y fecha...',
        'save_photo_documentation' => 'Guardar Documentación Fotográfica',
        'photo_documentation_description' => 'Imágenes de alta resolución para documentación y fines de seguro',

        // Acciones del Modal
        'close' => 'Cerrar',
        'error_no_certificate' => 'Error: No se seleccionó ningún certificado',
        'saving' => 'Guardando...',
        'annex_saved_success' => '¡Datos del anexo guardados con éxito!',
        'error_saving_annex' => 'Error al guardar los datos del anexo',

        // Traducciones faltantes para la barra lateral y componentes CoA
        'certificate' => 'Certificado CoA',
        'no_certificate' => 'Sin Certificado',
        'certificate_active' => 'Certificado Activo',
        'serial_number' => 'Número de Serie',
        'issue_date' => 'Fecha de Emisión',
        'expires' => 'Expira',
        'no_certificate_issued' => 'Este EGI no tiene un Certificado de Autenticidad',
        'issue_certificate' => 'Emitir Certificado',
        'certificate_issued_successfully' => '¡Certificado emitido con éxito!',
        'pdf_generated_automatically' => '¡PDF generado automáticamente!',
        'download_pdf_now' => '¿Quieres descargar el PDF ahora?',
        'digital_signatures' => 'Firmas Digitales',
        'signature_by' => 'Firmado por',
        'signature_role' => 'Rol',
        'signature_provider' => 'Proveedor',
        'signature_date' => 'Fecha de Firma',
        'unknown_signer' => 'Firmante Desconocido',
        'step_creating_certificate' => 'Creando certificado...',
        'step_generating_snapshot' => 'Generando instantánea...',
        'step_generating_pdf' => 'Generando PDF...',
        'step_finalizing' => 'Finalizando...',
        'generating' => 'Generando...',
        'generating_pdf' => 'Generando PDF...',
        'error_issuing_certificate' => 'Error al emitir el certificado: ',
        'issuing' => 'Emitiendo...',
        'unlock_with_coa_pro' => 'Desbloquear con CoA Pro',
        'provenance_documentation' => 'Documentación de Proveniencia',
        'condition_reports' => 'Informes de Condición',
        'exhibition_history' => 'Historial de Exposiciones',
        'professional_pdf' => 'PDF Profesional',
        'only_creator_can_issue' => 'Solo el creador puede emitir certificados',

        // Sistema de Vocabulario de Traits CoA
        'traits_management_title' => 'Gestionar Traits CoA',
        'traits_management_description' => 'Configura las características técnicas de la obra para el Certificado de Autenticidad',
        'status_configured' => 'Configurado',
        'status_not_configured' => 'No Configurado',
        'edit_traits' => 'Editar Traits',
        'no_technique_selected' => 'No se seleccionó ninguna técnica',
        'no_materials_selected' => 'No se seleccionaron materiales',
        'no_support_selected' => 'No se seleccionó soporte',
        'custom' => 'personalizado',
        'last_updated' => 'Última Actualización',
        'never_configured' => 'Nunca Configurado',
        'clear_all' => 'Borrar Todo',
        'saved' => 'Guardado',

        // Modal de Vocabulario
        'modal_title' => 'Seleccionar Traits CoA',
        'category_technique' => 'Técnica',
        'category_materials' => 'Materiales',
        'category_support' => 'Soporte',
        'search_placeholder' => 'Buscar términos...',
        'loading' => 'Cargando...',
        'selected_items' => 'Elementos Seleccionados',
        'no_items_selected' => 'No se seleccionaron elementos',
        'add_custom' => 'Añadir Personalizado',
        'custom_term_placeholder' => 'Introduce un término personalizado (máx. 60 caracteres)',
        'add' => 'Añadir',
        'cancel' => 'Cancelar',
        'items_selected' => 'elementos seleccionados',
        'confirm' => 'Confirmar',

        // Componentes de Vocabulario
        'terms_available' => 'términos disponibles',
        'no_categories_available' => 'No hay categorías disponibles',
        'no_categories_found' => 'No se encontraron categorías de vocabulario.',
        'search_results' => 'Resultados de Búsqueda',
        'results_for' => 'Para',
        'terms_found' => 'términos encontrados',
        'results_found' => 'resultados encontrados',
        'no_results_found' => 'No se encontraron resultados',
        'no_terms_match_search' => 'No hay términos que coincidan con la búsqueda',
        'in_category' => 'en la categoría',
        'clear_search' => 'Borrar Búsqueda',
        'no_terms_available' => 'No hay términos disponibles',
        'no_terms_found_category' => 'No se encontraron términos para la categoría',
        'categories' => 'Categorías',
        'back_to_start' => 'Volver al Inicio',
        'retry' => 'Reintentar',
        'error' => 'Error',
        'unexpected_error' => 'Ocurrió un error inesperado.',
        'exhibition_history' => 'Historial de Exposiciones',
        'professional_pdf_bundle' => 'Paquete PDF Profesional',
        'only_creator_can_issue' => 'Solo el creador puede emitir certificados',
        'public_verification' => 'Verificación Pública',
        'verification_description' => 'Verifica la autenticidad de un Certificado de Autenticidad EGI',
        'verification_instructions' => 'Introduce el número de serie del certificado para verificar su autenticidad',
        'enter_serial' => 'Introduce Número de Serie',
        'serial_help' => 'Formato: ABC-123-DEF (letras, números y guiones)',
        'certificate_of_authenticity' => 'Certificado de Autenticidad',
        'public_verification_display' => 'Visualización de Verificación Pública',
        'verified_authentic' => 'Certificado Verificado y Auténtico',
        'verified_at' => 'Verificado el',
        'artwork_information' => 'Información de la Obra',
        'artwork_title' => 'Título de la Obra',
        'creator' => 'Creador',
        'description' => 'Descripción',
        'certificate_details' => 'Detalles del Certificado',
        'cryptographic_verification' => 'Verificación Criptográfica',
        'verify_again' => 'Verificar de Nuevo',
        'print_certificate' => 'Imprimir Certificado',
        'share_verification' => 'Compartir Verificación',
        'powered_by_florenceegi' => 'Desarrollado por FlorenceEGI',
        'verification_timestamp' => 'Marca de Tiempo de Verificación',
        'link_copied' => 'Enlace copiado al portapapeles',
        'issuing' => 'Emitiendo...',
        'certificate_issued_successfully' => '¡Certificado emitido con éxito!',
        'error_issuing_certificate' => 'Error al emitir el certificado: ',
        'reissue_certificate_confirm' => '¿Reemitir este certificado? Se creará una nueva versión.',
        'certificate_reissued_successfully' => '¡Certificado reemitido con éxito!',
        'error_reissuing_certificate' => 'Error al reemitir el certificado: ',
        'revoke_certificate_confirm' => '¿Revocar este certificado? Esta acción no se puede deshacer.',
        'reason_for_revocation' => 'Motivo de la Revocación:',
        'certificate_revoked_successfully' => '¡Certificado revocado con éxito!',
        'error_revoking_certificate' => 'Error al revocar el certificado: ',
        'manage_certificate' => 'Gestionar Certificado',
        'annex_management_coming_soon' => '¡Gestión de anexos próximamente!',
        'issue_certificate_description' => 'Emite un certificado para proporcionar prueba de autenticidad y desbloquear funciones Pro',
        'serial' => 'Serial',
        'pro_features' => 'Funciones Pro',
        'provenance_docs' => 'Documentación de Proveniencia',
        'professional_pdf' => 'PDF Profesional',
        'unlock_pro_features' => 'Desbloquear Funciones Pro',
        'reason_for' => 'Motivo para',

        // Insignias de Firmas QES
        'badge_author_signed' => 'Firmado por el Autor (QES)',
        'badge_inspector_signed' => 'Firmado por el Inspector (QES)',
        'badge_integrity_ok' => 'Integridad Verificada',

        // Interfaz de Ubicación (CoA)
        'issue_place' => 'Lugar de Emisión',
        'location_placeholder' => 'Ej. Florencia, Toscana, Italia',
        'save' => 'Guardar',
        'location_hint' => 'Usa el formato "Ciudad, Región/Provincia, País" (o equivalente).',
        'location_required' => 'La ubicación es obligatoria',
        'location_saved' => 'Ubicación guardada',
        'location_save_failed' => 'Fallo al guardar la ubicación',
        'location_updated' => 'Ubicación actualizada con éxito',

        // Co-firma del Inspector (QES)
        'inspector_countersign' => 'Co-firma del Inspector (QES)',
        'confirm_inspector_countersign' => '¿Proceder con la co-firma del inspector?',
        'inspector_countersign_applied' => 'Co-firma del inspector aplicada',
        'operation_failed' => 'Operación fallida',
        'author_countersign' => 'Firma del Autor (QES)',
        'confirm_author_countersign' => '¿Proceder con la firma del autor?',
        'author_countersign_applied' => 'Firma del autor aplicada',
        'regenerate_pdf' => 'Regenerar PDF',
        'pdf_regenerated' => 'PDF regenerado',
        'pdf_regenerate_failed' => 'Fallo al regenerar el PDF',

        // Página de Verificación Pública
        'public_verify' => [
            'signature' => 'Firma',
            'author_signed' => 'Firmado por el Autor',
            'inspector_countersigned' => 'Contrafirma del Inspector',
            'timestamp_tsa' => 'Marca de Tiempo TSA',
            'qes' => 'QES',
            'wallet_signature' => 'Firma de Cartera',
            'verify_signature' => 'verificar firma',
            'certificate_hash' => 'Hash del Certificado (SHA-256)',
            'pdf_hash' => 'Hash del PDF (SHA-256)',
            'copy_hash' => 'Copiar Hash',
            'copy_pdf_hash' => 'Copiar Hash del PDF',
            'hash_copied' => '¡Hash copiado al portapapeles!',
            'pdf_hash_copied' => '¡Hash del PDF copiado al portapapeles!',
            'qr_code_verify' => 'Verificación de Código QR',
            'qr_code' => 'Código QR',
            'scan_to_verify' => 'Escanear para Verificar',
            'status' => 'Estado',
            'valid' => 'Válido',
            'incomplete' => 'Incompleto',
            'revoked' => 'Revocado',

            // Encabezados y Títulos
            'certificate_title' => 'Certificado de Autenticidad',
            'public_verification_display' => 'Visualización de Verificación Pública',
            'verified_authentic' => 'Certificado Verificado y Auténtico',
            'verified_at' => 'Verificado el',
            'serial_number' => 'Número de Serie',
            'certificate_not_ready' => 'Certificado No Listo',
            'certificate_revoked' => 'Certificado Revocado',
            'certificate_not_valid' => 'Este certificado ya no es válido',
            'requires_coa_traits' => 'Requiere Traits CoA',
            'certificate_not_ready_generic' => 'Certificado No Listo - Traits Genéricos',

            // Información de la Obra
            'artwork_title' => 'Título',
            'year' => 'Año',
            'dimensions' => 'Dimensiones',
            'edition' => 'Edición',
            'author' => 'Autor',
            'technique' => 'Técnica',
            'material' => 'Material',
            'support' => 'Soporte',
            'platform' => 'Plataforma',
            'published_by' => 'Publicado por',
            'image' => 'Imagen',

            // Información del Certificado
            'issue_date' => 'Fecha de Emisión',
            'issued_by' => 'Emitido por',
            'issue_location' => 'Lugar de Emisión',
            'notes' => 'Notas',

            // Anexos Profesionales
            'professional_annexes' => 'Anexos Profesionales',
            'provenance' => 'Proveniencia',
            'condition_report' => 'Informe de Condición',
            'exhibitions_publications' => 'Exposiciones/Publicaciones',
            'additional_photos' => 'Fotos Adicionales',

            // Información en Cadena
            'on_chain_info' => 'Información en Cadena',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sistema de Dossier - Sistema de Dossier
    |--------------------------------------------------------------------------
    */
    'dossier' => [
        'title' => 'Dossier de Imágenes',
        'loading' => 'Cargando dossier...',
        'view_complete' => 'Ver dossier de imágenes completo',
        'close' => 'Cerrar Dossier',

        // Información de la Obra
        'artwork_info' => 'Información de la Obra',
        'author' => 'Autor',
        'year' => 'Año',
        'internal_id' => 'ID Interno',

        // Información del Dossier
        'dossier_info' => 'Información del Dossier',
        'images_count' => 'Imágenes',
        'type' => 'Tipo',
        'utility_gallery' => 'Galería de Utilidad',

        // Galería
        'gallery_title' => 'Galería de Imágenes',
        'image_number' => 'Imagen :number',
        'image_of_total' => 'Imagen :current de :total',

        // Estados
        'no_utility_title' => 'Dossier no disponible',
        'no_utility_message' => 'No hay imágenes adicionales disponibles para esta obra.',
        'no_utility_description' => 'El dossier de imágenes adicionales aún no ha sido configurado para esta obra.',

        'no_images_title' => 'No hay imágenes disponibles',
        'no_images_message' => 'El dossier existe pero aún no contiene imágenes.',
        'no_images_description' => 'Las imágenes adicionales serán añadidas en el futuro por el creador de la obra.',

        'error_title' => 'Error',
        'error_loading' => 'Error al cargar el dossier',

        // Navegación
        'previous_image' => 'Imagen Anterior',
        'next_image' => 'Imagen Siguiente',
        'close_viewer' => 'Cerrar Visor',
        'of' => 'de',

        // Controles de Zoom
        'zoom_help' => 'Usa la rueda del ratón o el tacto para zoom • Arrastra para mover',
        'zoom_in' => 'Acercar',
        'zoom_out' => 'Alejar',
        'zoom_reset' => 'Restablecer Zoom',
        'zoom_fit' => 'Ajustar a la Pantalla',
    ],

];