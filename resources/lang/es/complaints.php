<?php

return [

    // Page
    'title' => 'Denuncias y Reclamaciones DSA',
    'subtitle' => 'Denuncie contenidos ilícitos o presente una reclamación de conformidad con la Ley de Servicios Digitales (Reg. UE 2022/2065)',
    'dsa_info_title' => 'Tus derechos de conformidad con la DSA',
    'dsa_info_text' => 'De conformidad con el Reglamento (UE) 2022/2065 (Ley de Servicios Digitales), tienes el derecho de denunciar contenidos que consideres ilícitos (Art. 16) y de presentar reclamaciones contra las decisiones de moderación de la plataforma (Art. 20). Cada denuncia será examinada por personal calificado en plazos razonables.',
    'legal_contact' => 'Para denuncias urgentes también puedes escribir a',

    // Types
    'types' => [
        'content_report' => 'Denuncia de contenido ilícito',
        'ip_violation' => 'Violación de propiedad intelectual',
        'fraud' => 'Fraude o actividad fraudulenta',
        'moderation_appeal' => 'Reclamación contra decisión de moderación',
        'general' => 'Denuncia genérica',
    ],

    // Type descriptions (for form helper text)
    'type_descriptions' => [
        'content_report' => 'Contenidos ilegales, ofensivos o que violan nuestras condiciones de uso',
        'ip_violation' => 'Obras falsificadas, plagio, violación de derechos de autor o marcas registradas',
        'fraud' => 'Estafas, fraude en pagos o comportamientos engañosos',
        'moderation_appeal' => 'Desafía una decisión tomada por la plataforma respecto a tus contenidos',
        'general' => 'Cualquier otra denuncia que no se ajuste a las categorías anteriores',
    ],

    // Content types
    'content_types' => [
        'egi' => 'Obra (EGI)',
        'collection' => 'Colección',
        'user_profile' => 'Perfil de usuario',
        'comment' => 'Comentario',
    ],

    // Statuses
    'statuses' => [
        'received' => 'Recibida',
        'under_review' => 'En revisión',
        'action_taken' => 'Medida adoptada',
        'dismissed' => 'Desestimada',
        'appealed' => 'Reclamación presentada',
        'resolved' => 'Resuelta',
    ],

    // Form labels
    'form_title' => 'Nueva denuncia',
    'select_type' => 'Selecciona el tipo de denuncia',
    'complaint_type' => 'Tipo de denuncia',
    'reported_content_type' => 'Tipo de contenido denunciado',
    'select_content_type' => 'Selecciona el tipo de contenido',
    'reported_content_id' => 'ID del contenido',
    'reported_content_id_help' => 'Ingresa el ID del contenido que deseas denunciar (visible en la página del contenido)',
    'description' => 'Descripción detallada',
    'description_placeholder' => 'Describe en detalle el motivo de la denuncia, incluyendo todos los elementos útiles para la evaluación. Mínimo 20 caracteres.',
    'description_chars' => ':count / :max caracteres',
    'evidence_urls' => 'URLs de prueba (opcional)',
    'evidence_urls_help' => 'Ingresa enlaces a capturas de pantalla, páginas web u otros elementos para respaldar la denuncia. Máximo 5 URLs.',
    'add_evidence_url' => 'Agregar URL',
    'remove_evidence_url' => 'Eliminar',
    'evidence_url_placeholder' => 'https://...',
    'consent_label' => 'Consentimiento de tratamiento',
    'consent_text' => 'Consiento el tratamiento de datos personales necesarios para la gestión de esta denuncia, de conformidad con el Reg. UE 2016/679 (RGPD) y el Reg. UE 2022/2065 (DSA). Declaro que la información proporcionada es veraz y de buena fe.',

    // Actions
    'submit' => 'Enviar denuncia',
    'submitting' => 'Enviando...',
    'cancel' => 'Cancelar',
    'back_to_list' => 'Volver a denuncias',
    'view_details' => 'Detalles',

    // Messages
    'submitted_successfully' => 'Tu denuncia ha sido enviada exitosamente. Número de referencia: :reference. Recibirás una confirmación por correo electrónico.',
    'no_complaints' => 'Aún no has presentado denuncias o reclamaciones.',

    // Table headers
    'date' => 'Fecha',
    'reference' => 'Referencia',
    'type' => 'Tipo',
    'status' => 'Estado',
    'actions' => 'Acciones',

    // Previous complaints section
    'your_complaints' => 'Tus denuncias',
    'your_complaints_description' => 'Historial de denuncias y reclamaciones que has presentado',

    // Validation
    'validation' => [
        'type_required' => 'Selecciona el tipo de denuncia.',
        'type_invalid' => 'El tipo de denuncia seleccionado no es válido.',
        'description_required' => 'La descripción es obligatoria.',
        'description_min' => 'La descripción debe contener al menos 20 caracteres.',
        'description_max' => 'La descripción no puede exceder 5000 caracteres.',
        'content_id_required' => 'El ID del contenido es obligatorio cuando se selecciona un tipo de contenido.',
        'evidence_urls_max' => 'Puedes ingresar un máximo de 5 URLs de prueba.',
        'evidence_url_format' => 'Cada URL de prueba debe ser una dirección web válida.',
        'consent_required' => 'Debes consentir el tratamiento de datos para continuar.',
    ],

    // Detail page
    'detail_title' => 'Detalle de denuncia',
    'submitted_on' => 'Enviada el',
    'current_status' => 'Estado actual',
    'complaint_type_label' => 'Tipo',
    'reported_content' => 'Contenido denunciado',
    'description_label' => 'Descripción',
    'evidence_label' => 'Pruebas adjuntas',
    'decision' => 'Decisión',
    'decision_date' => 'Fecha de la decisión',
    'decided_by_label' => 'Decidida por',
    'no_decision_yet' => 'En espera de revisión por parte del equipo.',
    'appeal_section' => 'Reclamación / Apelación',
    'no_appeal' => 'Ninguna reclamación presentada.',
    'content_id_label' => 'ID de contenido',
    'content_type_label' => 'Tipo de contenido',
    'reported_user_label' => 'Usuario denunciado',

    // Timeline
    'timeline' => [
        'received' => 'Denuncia recibida',
        'under_review' => 'En procesamiento',
        'action_taken' => 'Medida adoptada',
        'dismissed' => 'Denuncia desestimada',
        'appealed' => 'Reclamación presentada',
        'resolved' => 'Caso resuelto',
    ],

    // Notification email
    'notification' => [
        'subject' => 'Confirmación de recepción de denuncia DSA - :reference',
        'greeting' => 'Estimado :name,',
        'body' => 'Tu denuncia ha sido recibida y registrada con el número de referencia **:reference**.',
        'body_2' => 'Examinaremos tu denuncia y nos pondremos en contacto contigo dentro de los plazos previstos por la Ley de Servicios Digitales (Reg. UE 2022/2065).',
        'reference_label' => 'Número de referencia',
        'type_label' => 'Tipo de denuncia',
        'date_label' => 'Fecha de envío',
        'closing' => 'El equipo de FlorenceEGI',
    ],

];
