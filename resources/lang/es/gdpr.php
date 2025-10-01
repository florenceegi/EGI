<?php
// resources/lang/es/gdpr.php

return [
    /*
    |--------------------------------------------------------------------------
    | Líneas de Idioma GDPR
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas de idioma se utilizan para funciones relacionadas con el GDPR.
    |
    */

    // General
    'gdpr' => 'RGPD',
    'gdpr_center' => 'Centro de Control de Datos RGPD',
    'dashboard' => 'Panel de Control',
    'back_to_dashboard' => 'Volver al Panel de Control',
    'save' => 'Guardar',
    'submit' => 'Enviar',
    'cancel' => 'Cancelar',
    'continue' => 'Continuar',
    'loading' => 'Cargando...',
    'success' => 'Éxito',
    'error' => 'Error',
    'warning' => 'Advertencia',
    'info' => 'Información',
    'enabled' => 'Habilitado',
    'disabled' => 'Deshabilitado',
    'active' => 'Activo',
    'inactive' => 'Inactivo',
    'pending' => 'Pendiente',
    'completed' => 'Completado',
    'failed' => 'Fallido',
    'processing' => 'Procesando',
    'retry' => 'Reintentar',
    'required_field' => 'Campo obligatorio',
    'required_consent' => 'Consentimiento obligatorio',
    'select_all_categories' => 'Seleccionar todas las categorías',
    'no_categories_selected' => 'No se ha seleccionado ninguna categoría',
    'compliance_badge' => 'Insignia de cumplimiento',

    'consent_types' => [
        'terms-of-service' => [
            'name' => 'Términos de Servicio',
            'description' => 'Aceptación de las condiciones para el uso de la plataforma.',
        ],
        'privacy-policy' => [
            'name' => 'Política de Privacidad',
            'description' => 'Reconocimiento de cómo se procesan los datos personales.',
        ],
        'age-confirmation' => [
            'name' => 'Confirmación de Edad',
            'description' => 'Confirmación de tener al menos 18 años.',
        ],
        'analytics' => [
            'name' => 'Análisis y mejora de la plataforma',
            'description' => 'Ayúdanos a mejorar FlorenceEGI compartiendo datos de uso anónimos.',
        ],
        'marketing' => [
            'name' => 'Comunicaciones promocionales',
            'description' => 'Recibe actualizaciones sobre nuevas funciones, eventos y oportunidades.',
        ],
        'personalization' => [
            'name' => 'Personalización de contenidos',
            'description' => 'Permite la personalización de contenidos y recomendaciones.',
        ],
        'collaboration_participation' => [
            'name' => 'Participación en Colaboraciones',
            'description' => 'Consentimiento para participar en colaboraciones de colecciones, intercambio de datos y actividades colaborativas.',
        ],
        'purposes' => [
            'account_management' => 'Gestión de la Cuenta de Usuario',
            'service_delivery'   => 'Prestación de Servicios Solicitados',
            'legal_compliance'   => 'Cumplimiento Legal y Normativo',
            'customer_support'   => 'Soporte al Cliente y Asistencia',
        ],
    ],

    // Migas de Pan
    'breadcrumb' => [
        'dashboard' => 'Panel de Control',
        'gdpr' => 'Privacidad y RGPD',
    ],

    // Mensajes de Alerta
    'alerts' => [
        'success' => '¡Operación completada!',
        'error' => 'Error:',
        'warning' => 'Advertencia:',
        'info' => 'Información:',
    ],

    // Elementos del Menú
    'menu' => [
        'gdpr_center' => 'Centro de Control de Datos RGPD',
        'consent_management' => 'Gestión de Consentimientos',
        'data_export' => 'Exportar Mis Datos',
        'processing_restrictions' => 'Restringir el Procesamiento de Datos',
        'delete_account' => 'Eliminar Mi Cuenta',
        'breach_report' => 'Reportar una Violación de Datos',
        'activity_log' => 'Registro de Mis Actividades RGPD',
        'privacy_policy' => 'Política de Privacidad',
    ],

    // Gestión de Consentimientos
    'consent' => [
        'title' => 'Gestiona Tus Preferencias de Consentimiento',
        'description' => 'Controla cómo se utilizan tus datos dentro de nuestra plataforma. Puedes actualizar tus preferencias en cualquier momento.',
        'update_success' => 'Tus preferencias de consentimiento han sido actualizadas.',
        'update_error' => 'Se produjo un error al actualizar tus preferencias de consentimiento. Inténtalo de nuevo.',
        'save_all' => 'Guardar Todas las Preferencias',
        'last_updated' => 'Última actualización:',
        'never_updated' => 'Nunca actualizado',
        'privacy_notice' => 'Aviso de Privacidad',
        'not_given' => 'No Proporcionado',
        'given_at' => 'Proporcionado el',
        'your_consents' => 'Tus Consentimientos',
        'subtitle' => 'Gestiona tus preferencias de privacidad y revisa el estado de tus consentimientos.',
        'breadcrumb' => 'Consentimientos',
        'history_title' => 'Historial de Consentimientos',
        'back_to_consents' => 'Volver a Consentimientos',
        'preferences_title' => 'Gestión de Preferencias de Consentimiento',
        'preferences_subtitle' => 'Configura tus preferencias de privacidad detalladas',
        'preferences_breadcrumb' => 'Preferencias',
        'preferences_info_title' => 'Gestión Granular de Consentimientos',
        'preferences_info_description' => 'Aquí puedes configurar en detalle cada tipo de consentimiento...',
        'required' => 'Obligatorio',
        'optional' => 'Opcional',
        'toggle_label' => 'Activar/Desactivar',
        'always_enabled' => 'Siempre Activo',
        'benefits_title' => 'Beneficios para Ti',
        'consequences_title' => 'Si Desactivas',
        'third_parties_title' => 'Servicios de Terceros',
        'save_preferences' => 'Guardar Preferencias',
        'back_to_overview' => 'Volver a la Vista General',
        'never_updated' => 'Nunca actualizado',

        // Detalles del Consentimiento
        'given_at' => 'Proporcionado el',
        'withdrawn_at' => 'Retirado el',
        'not_given' => 'No proporcionado',
        'method' => 'Método',
        'version' => 'Versión',
        'unknown_version' => 'Versión desconocida',

        // Acciones
        'withdraw' => 'Retirar el Consentimiento',
        'withdraw_confirm' => '¿Estás seguro de que quieres retirar este consentimiento? Esta acción puede limitar algunas funcionalidades.',
        'renew' => 'Renovar el Consentimiento',
        'view_history' => 'Ver Historial',

        // Estados Vacíos
        'no_consents' => 'No Hay Consentimientos',
        'no_consents_description' => 'Aún no has proporcionado ningún consentimiento para el procesamiento de datos. Puedes gestionar tus preferencias usando el botón de abajo.',

        // Gestión de Preferencias
        'manage_preferences' => 'Gestiona Tus Preferencias',
        'update_preferences' => 'Actualizar las Preferencias de Privacidad',

        // Estado del Consentimiento
        'status' => [
            'granted' => 'Concedido',
            'denied' => 'Denegado',
            'active' => 'Activo',
            'withdrawn' => 'Retirado',
            'expired' => 'Expirado',
            'pending' => 'Pendiente',
            'in_progress' => 'En curso',
            'completed' => 'Completado',
            'failed' => 'Fallido',
            'rejected' => 'Rechazado',
            'verification_required' => 'Verificación requerida',
            'cancelled' => 'Cancelado',
        ],

        // Resumen del Panel de Control
        'summary' => [
            'active' => 'Consentimientos Activos',
            'total' => 'Consentimientos Totales',
            'compliance' => 'Puntuación de Cumplimiento',
        ],

        // Métodos de Consentimiento
        'methods' => [
            'web' => 'Interfaz Web',
            'api' => 'API',
            'import' => 'Importación',
            'admin' => 'Administrador',
        ],

        // Propósitos del Consentimiento
        'purposes' => [
            'functional' => 'Consentimientos Funcionales',
            'analytics' => 'Consentimientos Analíticos',
            'marketing' => 'Consentimientos de Marketing',
            'profiling' => 'Consentimientos de Perfilación',
            'platform-services' => 'Servicios de la Plataforma',
            'terms-of-service' => 'Términos de Servicio',
            'privacy-policy' => 'Política de Privacidad',
            'age-confirmation' => 'Confirmación de Edad',
            'personalization' => 'Personalización de Contenidos',
            'allow-personal-data-processing' => 'Permitir Procesamiento de Datos Personales',
            'collaboration_participation' => 'Participación en Colaboraciones',
        ],

        // Descripciones de los Consentimientos
        'descriptions' => [
            'functional' => 'Necesarios para el funcionamiento básico de la plataforma y para proporcionar los servicios solicitados.',
            'analytics' => 'Utilizados para analizar el uso del sitio y mejorar la experiencia del usuario.',
            'marketing' => 'Utilizados para enviarte comunicaciones promocionales y ofertas personalizadas.',
            'profiling' => 'Utilizados para crear perfiles personalizados y sugerir contenidos relevantes.',
            'platform-services' => 'Consentimientos necesarios para la gestión de la cuenta, la seguridad y el soporte al cliente.',
            'terms-of-service' => 'Aceptación de los Términos de Servicio para el uso de la plataforma.',
            'privacy-policy' => 'Aceptación de nuestra Política de Privacidad y del procesamiento de datos personales.',
            'age-confirmation' => 'Confirmación de tener la mayoría de edad para el uso de la plataforma.',
            'personalization' => 'Permite la personalización de contenidos y recomendaciones según tus preferencias.',
            'allow-personal-data-processing' => 'Permite el procesamiento de tus datos personales para mejorar nuestros servicios y proporcionarte una experiencia personalizada.',
            'collaboration_participation' => 'Permite la participación en proyectos colaborativos y actividades compartidas con otros usuarios de la plataforma.',
        ],

        'essential' => [
            'label' => 'Cookies Esenciales',
            'description' => 'Estas cookies son necesarias para el funcionamiento del sitio web y no pueden desactivarse en nuestros sistemas.',
        ],
        'functional' => [
            'label' => 'Cookies Funcionales',
            'description' => 'Estas cookies permiten al sitio web proporcionar funcionalidades avanzadas y personalización.',
        ],
        'analytics' => [
            'label' => 'Cookies Analíticas',
            'description' => 'Estas cookies nos permiten contar las visitas y las fuentes de tráfico para medir y mejorar el rendimiento de nuestro sitio.',
        ],
        'marketing' => [
            'label' => 'Cookies de Marketing',
            'description' => 'Estas cookies pueden ser establecidas a través de nuestro sitio por nuestros socios publicitarios para crear un perfil de tus intereses.',
        ],
        'profiling' => [
            'label' => 'Perfilación',
            'description' => 'Utilizamos la perfilación para comprender mejor tus preferencias y personalizar nuestros servicios según tus necesidades.',
        ],

        'allow_personal_data_processing' => [
            'label' => 'Consentimiento para el Procesamiento de Datos Personales',
            'description' => 'Permite el procesamiento de tus datos personales para mejorar nuestros servicios y proporcionarte una experiencia personalizada.',
        ],

        'saving_consent' => 'Guardando...',
        'consent_saved' => 'Guardado',
        'saving_all_consents' => 'Guardando todas las preferencias...',
        'all_consents_saved' => 'Todas las preferencias de consentimiento se han guardado con éxito.',
        'all_consents_save_error' => 'Se produjo un error al guardar todas las preferencias de consentimiento.',
        'consent_save_error' => 'Se produjo un error al guardar esta preferencia de consentimiento.',

        // Propósitos del Procesamiento
        'processing_purposes' => [
            'functional' => 'Operaciones esenciales de la plataforma: autenticación, seguridad, prestación de servicios, almacenamiento de preferencias de usuario',
            'analytics' => 'Mejora de la plataforma: análisis de uso, monitoreo de rendimiento, optimización de la experiencia del usuario',
            'marketing' => 'Comunicación: boletines, actualizaciones de productos, ofertas promocionales, notificaciones de eventos',
            'profiling' => 'Personalización: recomendaciones de contenido, análisis del comportamiento del usuario, sugerencias dirigidas',
        ],

        // Períodos de Retención
        'retention_periods' => [
            'functional' => 'Duración de la cuenta + 1 año para cumplimiento legal',
            'analytics' => '2 años desde la última actividad',
            'marketing' => '3 años desde la última interacción o retirada del consentimiento',
            'profiling' => '1 año desde la última actividad o retirada del consentimiento',
        ],

        // Beneficios para el Usuario
        'user_benefits' => [
            'functional' => [
                'Acceso seguro a tu cuenta',
                'Configuraciones de usuario personalizadas',
                'Rendimiento confiable de la plataforma',
                'Protección contra fraudes y abusos',
            ],
            'analytics' => [
                'Rendimiento mejorado de la plataforma',
                'Diseño de experiencia de usuario optimizado',
                'Tiempos de carga más rápidos',
                'Desarrollo de funciones mejoradas',
            ],
            'marketing' => [
                'Actualizaciones de productos relevantes',
                'Ofertas y promociones exclusivas',
                'Invitaciones a eventos y anuncios',
                'Contenidos educativos y sugerencias',
            ],
            'profiling' => [
                'Recomendaciones de contenido personalizadas',
                'Experiencia de usuario personalizada',
                'Sugerencias de proyectos relevantes',
                'Panel de control y funciones personalizadas',
            ],
        ],

        // Servicios de Terceros
        'third_parties' => [
            'functional' => [
                'Proveedores de CDN (distribución de contenido estático)',
                'Servicios de seguridad (prevención de fraudes)',
                'Proveedores de infraestructura (alojamiento)',
            ],
            'analytics' => [
                'Plataformas de análisis (datos de uso anonimizados)',
                'Servicios de monitoreo de rendimiento',
                'Servicios de seguimiento de errores',
            ],
            'marketing' => [
                'Proveedores de servicios de correo electrónico',
                'Plataformas de automatización de marketing',
                'Plataformas de redes sociales (para publicidad)',
            ],
            'profiling' => [
                'Motores de recomendación',
                'Servicios de análisis de comportamiento',
                'Plataformas de personalización de contenido',
            ],
        ],

        // Consecuencias de la Retirada
        'withdrawal_consequences' => [
            'functional' => [
                'No se puede retirar - esencial para el funcionamiento de la plataforma',
                'El acceso a la cuenta se vería comprometido',
                'Las funcionalidades de seguridad se desactivarían',
            ],
            'analytics' => [
                'Las mejoras de la plataforma podrían no reflejar tus patrones de uso',
                'Experiencia genérica en lugar de un rendimiento optimizado',
                'Sin impacto en las funcionalidades principales',
            ],
            'marketing' => [
                'No recibirás correos promocionales ni actualizaciones',
                'Podrías perderte anuncios importantes',
                'Sin impacto en la funcionalidad de la plataforma',
                'Se puede reactivar en cualquier momento',
            ],
            'profiling' => [
                'Contenidos genéricos en lugar de recomendaciones personalizadas',
                'Diseño de panel de control estándar',
                'Sugerencias de proyectos menos relevantes',
                'Sin impacto en las funcionalidades principales de la plataforma',
            ],
        ],
    ],

    // Exportación de Datos
    'export' => [
        'title' => 'Exportar Tus Datos',
        'subtitle' => 'Solicita una copia completa de tus datos personales en un formato portátil',
        'description' => 'Solicita una copia de tus datos personales. El procesamiento puede tomar algunos minutos.',

        // Categorías de Datos
        'select_data_categories' => 'Selecciona las Categorías de Datos a Exportar',
        'categories' => [
            'profile' => 'Información del Perfil',
            'account' => 'Detalles de la Cuenta',
            'preferences' => 'Preferencias y Configuraciones',
            'activity' => 'Historial de Actividades',
            'consents' => 'Historial de Consentimientos',
            'collections' => 'Colecciones y NFT',
            'purchases' => 'Compras y Transacciones',
            'comments' => 'Comentarios y Reseñas',
            'messages' => 'Mensajes y Comunicaciones',
            'biography' => 'Biografías y Contenidos',
        ],

        // Descripciones de Categorías
        'category_descriptions' => [
            'profile' => 'Datos personales, información de contacto, foto de perfil y descripciones personales',
            'account' => 'Detalles de la cuenta, configuraciones de seguridad, historial de inicio de sesión y cambios',
            'preferences' => 'Preferencias de usuario, configuraciones de privacidad, configuraciones personalizadas',
            'activity' => 'Historial de navegación, interacciones, visualizaciones y uso de la plataforma',
            'consents' => 'Historial de consentimientos de privacidad, cambios de preferencias, registro de auditoría RGPD',
            'collections' => 'Colecciones de NFT creadas, metadatos, propiedad intelectual y activos',
            'purchases' => 'Transacciones, compras, facturas, métodos de pago e historial de pedidos',
            'comments' => 'Comentarios, reseñas, valoraciones y retroalimentación dejados en la plataforma',
            'messages' => 'Mensajes privados, comunicaciones, notificaciones y conversaciones',
            'biography' => 'Biografías creadas, capítulos, líneas temporales, medios y contenidos narrativos',
        ],

        // Formatos de Exportación
        'select_format' => 'Selecciona el Formato de Exportación',
        'formats' => [
            'json' => 'JSON - Formato de Datos Estructurado',
            'csv' => 'CSV - Compatible con Hojas de Cálculo',
            'pdf' => 'PDF - Documento Legible',
        ],

        // Descripciones de Formatos
        'format_descriptions' => [
            'json' => 'Formato de datos estructurado ideal para desarrolladores e integraciones. Mantiene la estructura completa de los datos.',
            'csv' => 'Formato compatible con Excel y Google Sheets. Perfecto para análisis y manipulación de datos.',
            'pdf' => 'Documento legible e imprimible. Ideal para archivado y compartición.',
        ],

        // Opciones Adicionales
        'additional_options' => 'Opciones Adicionales',
        'include_metadata' => 'Incluir Metadatos Técnicos',
        'metadata_description' => 'Incluye información técnica como marcas de tiempo, direcciones IP, versiones y registros de auditoría.',
        'include_audit_trail' => 'Incluir Registro Completo de Actividades',
        'audit_trail_description' => 'Incluye un historial completo de todas las modificaciones y actividades RGPD.',

        // Acciones
        'request_export' => 'Solicitar Exportación de Datos',
        'request_success' => 'Solicitud de exportación enviada con éxito. Recibirás una notificación al completarse.',
        'request_error' => 'Se produjo un error al enviar la solicitud. Inténtalo de nuevo.',

        // Historial de Exportaciones
        'history_title' => 'Historial de Exportaciones',
        'no_exports' => 'No Hay Exportaciones',
        'no_exports_description' => 'Aún no has solicitado ninguna exportación de tus datos. Usa el formulario de arriba para solicitar una.',

        // Detalles de Elementos de Exportación
        'export_format' => 'Exportación {format}',
        'requested_on' => 'Solicitada el',
        'completed_on' => 'Completada el',
        'expires_on' => 'Expira el',
        'file_size' => 'Tamaño',
        'download' => 'Descargar',
        'download_export' => 'Descargar Exportación',

        // Estado
        'status' => [
            'pending' => 'Pendiente',
            'processing' => 'Procesando',
            'completed' => 'Completada',
            'failed' => 'Fallida',
            'expired' => 'Expirada',
        ],

        // Límite de Frecuencia
        'rate_limit_title' => 'Límite de Exportaciones Alcanzado',
        'rate_limit_message' => 'Has alcanzado el límite máximo de {max} exportaciones por hoy. Inténtalo de nuevo mañana.',
        'last_export_date' => 'Última exportación: {date}',

        // Validación
        'select_at_least_one_category' => 'Selecciona al menos una categoría de datos para exportar.',

        // Soporte Heredado
        'request_button' => 'Solicitar Exportación de Datos',
        'format' => 'Formato de Exportación',
        'format_json' => 'JSON (recomendado para desarrolladores)',
        'format_csv' => 'CSV (compatible con hojas de cálculo)',
        'format_pdf' => 'PDF (documento legible)',
        'include_timestamps' => 'Incluir marcas de tiempo',
        'password_protection' => 'Proteger la exportación con contraseña',
        'password' => 'Contraseña de exportación',
        'confirm_password' => 'Confirmar contraseña',
        'data_categories' => 'Categorías de datos para exportar',
        'recent_exports' => 'Exportaciones Recientes',
        'no_recent_exports' => 'No tienes exportaciones recientes.',
        'export_status' => 'Estado de Exportación',
        'export_date' => 'Fecha de Exportación',
        'export_size' => 'Tamaño de Exportación',
        'export_id' => 'ID de Exportación',
        'export_preparing' => 'Preparando tu exportación de datos...',
        'export_queued' => 'Tu exportación está en cola y comenzará pronto...',
        'export_processing' => 'Procesando tu exportación de datos...',
        'export_ready' => 'Tu exportación de datos está lista para descargar.',
        'export_failed' => 'Tu exportación de datos ha fallado.',
        'export_failed_details' => 'Se produjo un error al procesar tu exportación de datos. Inténtalo de nuevo o contacta con soporte.',
        'export_unknown_status' => 'Estado de exportación desconocido.',
        'check_status' => 'Verificar Estado',
        'retry_export' => 'Reintentar Exportación',
        'export_download_error' => 'Se produjo un error al descargar tu exportación.',
        'export_status_error' => 'Error al verificar el estado de la exportación.',
        'limit_reached' => 'Has alcanzado el número máximo de exportaciones permitidas por día.',
        'existing_in_progress' => 'Ya tienes una exportación en curso. Espera a que se complete.',
    ],

    // Restricciones de Procesamiento
    'restriction' => [
        'title' => 'Restringir el Procesamiento de Datos',
        'description' => 'Puedes solicitar restringir cómo procesamos tus datos en ciertas circunstancias.',
        'active_restrictions' => 'Restricciones Activas',
        'no_active_restrictions' => 'No tienes restricciones de procesamiento activas.',
        'request_new' => 'Solicitar Nueva Restricción',
        'restriction_type' => 'Tipo de Restricción',
        'restriction_reason' => 'Motivo de la Restricción',
        'data_categories' => 'Categorías de Datos',
        'notes' => 'Notas Adicionales',
        'notes_placeholder' => 'Proporciona cualquier detalle adicional para ayudarnos a entender tu solicitud...',
        'submit_button' => 'Enviar Solicitud de Restricción',
        'remove_button' => 'Eliminar Restricción',
        'processing_restriction_success' => 'Tu solicitud de restricción de procesamiento ha sido enviada.',
        'processing_restriction_failed' => 'Se produjo un error al enviar tu solicitud de restricción de procesamiento.',
        'processing_restriction_system_error' => 'Se produjo un error del sistema al procesar tu solicitud.',
        'processing_restriction_removed' => 'La restricción de procesamiento ha sido eliminada.',
        'processing_restriction_removal_failed' => 'Se produjo un error al eliminar la restricción de procesamiento.',
        'unauthorized_action' => 'No estás autorizado para realizar esta acción.',
        'date_submitted' => 'Fecha de Envío',
        'expiry_date' => 'Expira el',
        'never_expires' => 'Nunca Expira',
        'status' => 'Estado',
        'limit_reached' => 'Has alcanzado el número máximo de restricciones activas permitidas.',
        'categories' => [
            'profile' => 'Información del Perfil',
            'activity' => 'Seguimiento de Actividades',
            'preferences' => 'Preferencias y Configuraciones',
            'collections' => 'Colecciones y Contenidos',
            'purchases' => 'Compras y Transacciones',
            'comments' => 'Comentarios y Reseñas',
            'messages' => 'Mensajes y Comunicaciones',
        ],
        'types' => [
            'processing' => 'Restringir Todo el Procesamiento',
            'automated_decisions' => 'Restringir Decisiones Automatizadas',
            'marketing' => 'Restringir el Procesamiento de Marketing',
            'analytics' => 'Restringir el Procesamiento Analítico',
            'third_party' => 'Restringir el Intercambio con Terceros',
            'profiling' => 'Restringir la Perfilación',
            'data_sharing' => 'Restringir el Intercambio de Datos',
            'removed' => 'Eliminar Restricción',
            'all' => 'Restringir Todo el Procesamiento',
        ],
        'reasons' => [
            'accuracy_dispute' => 'Disputo la precisión de mis datos',
            'processing_unlawful' => 'El procesamiento es ilícito',
            'no_longer_needed' => 'Ya no necesitas mis datos, pero los necesito para reclamaciones legales',
            'objection_pending' => 'He objetado al procesamiento y estoy esperando verificación',
            'legitimate_interest' => 'Motivos legítimos apremiantes',
            'legal_claims' => 'Para la defensa de reclamaciones legales',
            'other' => 'Otro motivo (especificar en las notas)',
        ],
        'descriptions' => [
            'processing' => 'Restringe el procesamiento de tus datos personales mientras se verifica tu solicitud.',
            'automated_decisions' => 'Restringe las decisiones automatizadas que pueden afectar tus derechos.',
            'marketing' => 'Restringe el procesamiento de tus datos para fines de marketing directo.',
            'analytics' => 'Restringe el procesamiento de tus datos para fines analíticos y de monitoreo.',
            'third_party' => 'Restringe el intercambio de tus datos con terceros.',
            'profiling' => 'Restringe la perfilación de tus datos personales.',
            'data_sharing' => 'Restringe el intercambio de tus datos con otros servicios o plataformas.',
            'all' => 'Restringe todas las formas de procesamiento de tus datos personales.',
        ],
    ],

    // Eliminación de Cuenta
    'deletion' => [
        'title' => 'Eliminar Mi Cuenta',
        'description' => 'Esto iniciará el proceso para eliminar tu cuenta y todos los datos asociados.',
        'warning' => 'Advertencia: La eliminación de la cuenta es permanente y no se puede deshacer.',
        'processing_delay' => 'Tu cuenta está programada para eliminarse en :days días.',
        'confirm_deletion' => 'Entiendo que esta acción es permanente y no se puede deshacer.',
        'password_confirmation' => 'Ingresa tu contraseña para confirmar',
        'reason' => 'Motivo de la eliminación (opcional)',
        'additional_comments' => 'Comentarios adicionales (opcional)',
        'submit_button' => 'Solicitar Eliminación de Cuenta',
        'request_submitted' => 'Tu solicitud de eliminación de cuenta ha sido enviada.',
        'request_error' => 'Se produjo un error al enviar tu solicitud de eliminación de cuenta.',
        'pending_deletion' => 'Tu cuenta está programada para eliminarse el :date.',
        'cancel_deletion' => 'Cancelar Solicitud de Eliminación',
        'cancellation_success' => 'Tu solicitud de eliminación de cuenta ha sido cancelada.',
        'cancellation_error' => 'Se produjo un error al cancelar tu solicitud de eliminación de cuenta.',
        'reasons' => [
            'no_longer_needed' => 'Ya no necesito este servicio',
            'privacy_concerns' => 'Preocupaciones sobre la privacidad',
            'moving_to_competitor' => 'Cambio a otro servicio',
            'unhappy_with_service' => 'Insatisfecho con el servicio',
            'other' => 'Otro motivo',
        ],
        'confirmation_email' => [
            'subject' => 'Confirmación de Solicitud de Eliminación de Cuenta',
            'line1' => 'Hemos recibido tu solicitud para eliminar tu cuenta.',
            'line2' => 'Tu cuenta está programada para eliminarse el :date.',
            'line3' => 'Si no solicitaste esta acción, contáctanos de inmediato.',
        ],
        'data_retention_notice' => 'Ten en cuenta que algunos datos anonimizados pueden conservarse para fines legales y analíticos.',
        'blockchain_data_notice' => 'Los datos almacenados en blockchain no pueden eliminarse completamente debido a la naturaleza inmutable de la tecnología.',
    ],

    // Reporte de Violación
    'breach' => [
        'title' => 'Reportar una Violación de Datos',
        'description' => 'Si crees que ha habido una violación de tus datos personales, repórtala aquí.',
        'reporter_name' => 'Tu Nombre',
        'reporter_email' => 'Tu Correo Electrónico',
        'incident_date' => '¿Cuándo ocurrió el incidente?',
        'breach_description' => 'Describe la posible violación',
        'breach_description_placeholder' => 'Proporciona tantos detalles como sea posible sobre la posible violación de datos...',
        'affected_data' => '¿Qué datos crees que se han visto comprometidos?',
        'affected_data_placeholder' => 'Por ejemplo, información personal, datos financieros, etc.',
        'discovery_method' => '¿Cómo descubriste esta posible violación?',
        'supporting_evidence' => 'Evidencia de Apoyo (opcional)',
        'upload_evidence' => 'Cargar Evidencia',
        'file_types' => 'Tipos de archivo aceptados: PDF, JPG, JPEG, PNG, TXT, DOC, DOCX',
        'max_file_size' => 'Tamaño máximo del archivo: 10MB',
        'consent_to_contact' => 'Doy mi consentimiento para ser contactado respecto a este reporte',
        'submit_button' => 'Enviar Reporte de Violación',
        'report_submitted' => 'Tu reporte de violación ha sido enviado.',
        'report_error' => 'Se produjo un error al enviar tu reporte de violación.',
        'thank_you' => 'Gracias por tu reporte',
        'thank_you_message' => 'Gracias por reportar esta posible violación. Nuestro equipo de protección de datos investigará y podría contactarte para más información.',
        'breach_description_min' => 'Proporciona al menos 20 caracteres para describir la posible violación.',
    ],

    // Registro de Actividades
    'activity' => [
        'title' => 'Registro de Mis Actividades RGPD',
        'description' => 'Consulta un registro de todas tus actividades y solicitudes relacionadas con el RGPD.',
        'no_activities' => 'No se encontraron actividades.',
        'date' => 'Fecha',
        'activity' => 'Actividad',
        'details' => 'Detalles',
        'ip_address' => 'Dirección IP',
        'user_agent' => 'Agente de Usuario',
        'download_log' => 'Descargar Registro de Actividades',
        'filter' => 'Filtrar Actividades',
        'filter_all' => 'Todas las Actividades',
        'filter_consent' => 'Actividades de Consentimiento',
        'filter_export' => 'Actividades de Exportación de Datos',
        'filter_restriction' => 'Actividades de Restricción de Procesamiento',
        'filter_deletion' => 'Actividades de Eliminación de Cuenta',
        'types' => [
            'consent_updated' => 'Preferencias de Consentimiento Actualizadas',
            'data_export_requested' => 'Exportación de Datos Solicitada',
            'data_export_completed' => 'Exportación de Datos Completada',
            'data_export_downloaded' => 'Exportación de Datos Descargada',
            'processing_restricted' => 'Restricción de Procesamiento Solicitada',
            'processing_restriction_removed' => 'Restricción de Procesamiento Eliminada',
            'account_deletion_requested' => 'Eliminación de Cuenta Solicitada',
            'account_deletion_cancelled' => 'Eliminación de Cuenta Cancelada',
            'account_deletion_completed' => 'Eliminación de Cuenta Completada',
            'breach_reported' => 'Violación de Datos Reportada',
        ],
    ],

    // Validación
    'validation' => [
        'consents_required' => 'Las preferencias de consentimiento son obligatorias.',
        'consents_format' => 'El formato de las preferencias de consentimiento no es válido.',
        'consent_value_required' => 'El valor del consentimiento es obligatorio.',
        'consent_value_boolean' => 'El valor del consentimiento debe ser un booleano.',
        'format_required' => 'El formato de exportación es obligatorio.',
        'data_categories_required' => 'Es necesario seleccionar al menos una categoría de datos.',
        'data_categories_format' => 'El formato de las categorías de datos no es válido.',
        'data_categories_min' => 'Es necesario seleccionar al menos una categoría de datos.',
        'data_categories_distinct' => 'Las categorías de datos deben ser distintas.',
        'export_password_required' => 'La contraseña es obligatoria cuando la protección con contraseña está habilitada.',
        'export_password_min' => 'La contraseña debe tener al menos 8 caracteres.',
        'restriction_type_required' => 'El tipo de restricción es obligatorio.',
        'restriction_reason_required' => 'El motivo de la restricción es obligatorio.',
        'notes_max' => 'Las notas no pueden superar los 500 caracteres.',
        'reporter_name_required' => 'Tu nombre es obligatorio.',
        'reporter_email_required' => 'Tu correo electrónico es obligatorio.',
        'reporter_email_format' => 'Ingresa una dirección de correo electrónico válida.',
        'incident_date_required' => 'La fecha del incidente es obligatoria.',
        'incident_date_format' => 'La fecha del incidente debe ser una fecha válida.',
        'incident_date_past' => 'La fecha del incidente debe ser en el pasado o hoy.',
        'breach_description_required' => 'La descripción de la violación es obligatoria.',
        'breach_description_min' => 'La descripción de la violación debe tener al menos 20 caracteres.',
        'affected_data_required' => 'La información sobre los datos comprometidos es obligatoria.',
        'discovery_method_required' => 'El método de descubrimiento es obligatorio.',
        'supporting_evidence_format' => 'La evidencia debe ser un archivo PDF, JPG, JPEG, PNG, TXT, DOC o DOCX.',
        'supporting_evidence_max' => 'El archivo de evidencia no puede superar los 10MB.',
        'consent_to_contact_required' => 'El consentimiento para ser contactado es obligatorio.',
        'consent_to_contact_accepted' => 'El consentimiento para ser contactado debe ser aceptado.',
        'required_consent_message' => 'Este consentimiento es necesario para usar la plataforma.',
        'confirm_deletion_required' => 'Debes confirmar que comprendes las consecuencias de la eliminación de la cuenta.',
        'form_error_title' => 'Corrige los errores a continuación',
        'form_error_message' => 'Hay uno o más errores en el formulario que deben corregirse.',
    ],

    // Mensajes de Error
    'errors' => [
        'general' => 'Se produjo un error inesperado.',
        'unauthorized' => 'No estás autorizado para realizar esta acción.',
        'forbidden' => 'Esta acción está prohibida.',
        'not_found' => 'El recurso solicitado no se encontró.',
        'validation_failed' => 'Los datos enviados no son válidos.',
        'rate_limited' => 'Demasiadas solicitudes. Inténtalo de nuevo más tarde.',
        'service_unavailable' => 'El servicio no está disponible actualmente. Inténtalo de nuevo más tarde.',
    ],

    'requests' => [
        'types' => [
            'consent_update' => 'Solicitud de actualización de consentimiento enviada.',
            'data_export' => 'Solicitud de exportación de datos enviada.',
            'processing_restriction' => 'Solicitud de restricción de procesamiento enviada.',
            'account_deletion' => 'Solicitud de eliminación de cuenta enviada.',
            'breach_report' => 'Reporte de violación de datos enviado.',
            'erasure' => 'Solicitud de eliminación de datos enviada.',
            'access' => 'Solicitud de acceso a datos enviada.',
            'rectification' => 'Solicitud de rectificación de datos enviada.',
            'objection' => 'Solicitud de objeción al procesamiento enviada.',
            'restriction' => 'Solicitud de limitación de procesamiento enviada.',
            'portability' => 'Solicitud de portabilidad de datos enviada.',
        ],
    ],

    // Version Information
    'current_version' => 'Versión Actual',
    'version' => 'Versión: 1.0',
    'effective_date' => 'Fecha de Vigencia: 30 Sep 2025',
    'last_updated' => 'Última Actualización: 30 Sep 2025, 17:41',

    // Actions
    'download_pdf' => 'Descargar PDF',
    'print' => 'Imprimir',

    'modal' => [
        'clarification' => [
            'title' => 'Se Requiere Aclaración',
            'explanation' => 'Para garantizar tu seguridad, necesitamos entender el motivo de tu acción:',
        ],
        'revoke_button_text' => 'He cambiado de opinión',
        'revoke_description' => 'Simplemente deseas retirar el consentimiento previamente otorgado.',
        'disavow_button_text' => 'No reconozco esta acción',
        'disavow_description' => 'Nunca diste este consentimiento (posible problema de seguridad).',

        'confirmation' => [
            'title' => 'Confirmar Protocolo de Seguridad',
            'warning' => 'Esta acción activará un protocolo de seguridad que incluye:',
        ],
        'confirm_disavow' => 'Sí, activar protocolo de seguridad',
        'final_warning' => 'Proceda solo si estás seguro de que nunca autorizaste esta acción.',

        'consequences' => [
            'consent_revocation' => 'Retiro inmediato del consentimiento',
            'security_notification' => 'Notificación al equipo de seguridad',
            'account_review' => 'Posibles verificaciones adicionales en la cuenta',
            'email_confirmation' => 'Correo de confirmación con instrucciones',
        ],

        'security' => [
            'title' => 'Protocolo de Seguridad Activado',
            'understood' => 'Entendido',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sección de Notificaciones RGPD
    |--------------------------------------------------------------------------
    | Movido desde `notification.php` para centralización.
    */
    'notifications' => [
        'acknowledged' => 'Confirmación registrada.',
        'consent_updated' => [
            'title' => 'Preferencias de Privacidad Actualizadas',
            'content' => 'Tus preferencias de consentimiento han sido actualizadas con éxito.',
        ],
        'data_exported' => [
            'title' => 'Tu Exportación de Datos está Lista',
            'content' => 'Tu solicitud de exportación de datos ha sido procesada. Puedes descargar el archivo desde el enlace proporcionado.',
        ],
        'processing_restricted' => [
            'title' => 'Restricción de Procesamiento Aplicada',
            'content' => 'Hemos aplicado con éxito tu solicitud para restringir el procesamiento de datos para la categoría: :type.',
        ],
        'account_deletion_requested' => [
            'title' => 'Solicitud de Eliminación de Cuenta Recibida',
            'content' => 'Hemos recibido tu solicitud para eliminar tu cuenta. El proceso se completará en :days días. Durante este período, aún puedes cancelar la solicitud iniciando sesión nuevamente.',
        ],
        'account_deletion_processed' => [
            'title' => 'Cuenta Eliminada con Éxito',
            'content' => 'Como solicitaste, tu cuenta y los datos asociados han sido eliminados permanentemente de nuestra plataforma. Lamentamos verte partir.',
        ],
        'breach_report_received' => [
            'title' => 'Reporte de Violación Recibido',
            'content' => 'Gracias por tu reporte. Lo hemos recibido con el ID #:report_id y nuestro equipo de seguridad lo está revisando.',
        ],
        'status' => [
            'pending_user_confirmation' => 'Pendiente de confirmación del usuario',
            'user_confirmed_action' => 'Acción del usuario confirmada',
            'user_revoked_consent' => 'Acción del usuario retirada',
            'user_disavowed_suspicious' => 'Acción del usuario no reconocida',
        ],
    ],

    'consent_management' => [
        'title' => 'Gestión de Consentimientos',
        'subtitle' => 'Controla cómo se utilizan tus datos personales',
        'description' => 'Aquí puedes gestionar tus preferencias de consentimiento para diferentes propósitos y servicios.',
        'update_preferences' => 'Actualiza tus preferencias de consentimiento',
        'preferences_updated' => 'Tus preferencias de consentimiento han sido actualizadas con éxito.',
        'preferences_update_error' => 'Se produjo un error al actualizar tus preferencias de consentimiento. Inténtalo de nuevo.',
    ],

    // Pie de Página
    'privacy_policy' => 'Política de Privacidad',
    'terms_of_service' => 'Términos de Servicio',
    'all_rights_reserved' => 'Todos los derechos reservados.',
    'navigation_label' => 'Navegación RGPD',
    'main_content_label' => 'Contenido principal RGPD',
];
