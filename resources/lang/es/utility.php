<?php

return [
    // Títulos y encabezados
    'title' => 'Gestión de Utilidad',
    'subtitle' => 'Añade valor real a tu EGI',
    'status_configured' => 'Utilidad Configurada',
    'status_none' => 'Sin Utilidad',
    'available_images' => ':count imágenes disponibles para ":title"',
    'view_details' => 'Ver Detalles',
    'manage_utility' => 'Gestionar Utilidad',

    // Alertas y mensajes
    'info_edit_before_publish' => 'La utilidad solo puede agregarse o modificarse antes de que se publique la colección. Una vez publicada, no se puede modificar.',
    'success_created' => '¡Utilidad añadida con éxito!',
    'success_updated' => '¡Utilidad actualizada con éxito!',
    'confirm_reset' => '¿Estás seguro de que quieres cancelar? Se perderán los cambios no guardados.',
    'confirm_remove_image' => '¿Eliminar esta imagen?',
    'note' => 'Nota',

    // Tipos de utilidad
    'types' => [
        'label' => 'Tipo de Utilidad',
        'physical' => [
            'label' => 'Bien Físico',
            'description' => 'Objeto físico para enviar (cuadro, escultura, etc.)'
        ],
        'service' => [
            'label' => 'Servicio',
            'description' => 'Servicio o experiencia (taller, consultoría, etc.)'
        ],
        'hybrid' => [
            'label' => 'Híbrido',
            'description' => 'Combinación físico + servicio'
        ],
        'digital' => [
            'label' => 'Digital',
            'description' => 'Contenido o acceso digital'
        ],
        'remove' => 'Eliminar Utilidad'
    ],

    // Campos del formulario base
    'fields' => [
        'title' => 'Título de Utilidad',
        'title_placeholder' => 'Ej.: Cuadro Original 50x70cm',
        'description' => 'Descripción Detallada',
        'description_placeholder' => 'Describe en detalle lo que recibirá el comprador...',
    ],

    // Sección de envío
    'shipping' => [
        'title' => 'Detalles de Envío',
        'weight' => 'Peso (kg)',
        'dimensions' => 'Dimensiones (cm)',
        'length' => 'Longitud',
        'width' => 'Anchura',
        'height' => 'Altura',
        'days' => 'Días de preparación/envío',
        'fragile' => 'Objeto Frágil',
        'insurance' => 'Seguro Recomendado',
        'notes' => 'Notas de Envío',
        'notes_placeholder' => 'Instrucciones especiales para el embalaje o envío...'
    ],

    // Sección de servicio
    'service' => [
        'title' => 'Detalles del Servicio',
        'valid_from' => 'Válido Desde',
        'valid_until' => 'Válido Hasta',
        'max_uses' => 'Número Máximo de Usos',
        'max_uses_placeholder' => 'Dejar vacío para ilimitado',
        'instructions' => 'Instrucciones de Activación',
        'instructions_placeholder' => 'Cómo el comprador puede usar el servicio...'
    ],

    // Escrow
    'escrow' => [
        'immediate' => [
            'label' => 'Pago Inmediato',
            'description' => 'Sin escrow, pago directo al creador'
        ],
        'standard' => [
            'label' => 'Escrow Estándar',
            'description' => 'Fondos liberados después de 14 días desde la entrega',
            'requirement_tracking' => 'Seguimiento obligatorio'
        ],
        'premium' => [
            'label' => 'Escrow Premium',
            'description' => 'Fondos liberados después de 21 días desde la entrega',
            'requirement_tracking' => 'Seguimiento obligatorio',
            'requirement_signature' => 'Firma en la entrega',
            'requirement_insurance' => 'Seguro recomendado'
        ]
    ],

    // Media/Galería
    'media' => [
        'title' => 'Galería de Imágenes Detalle',
        'description' => 'Añade fotos del objeto desde varios ángulos, detalles importantes, certificados de autenticidad, etc. (Máx 10 imágenes)',
        'upload_prompt' => 'Haz clic para cargar o arrastra las imágenes aquí',
        'current_images' => 'Imágenes Actuales:',
        'remove_image' => 'Eliminar'
    ],

    // Errores de validación
    'validation' => [
        'title_required' => 'El título es obligatorio',
        'type_required' => 'Selecciona un tipo de utilidad',
        'weight_required' => 'El peso es obligatorio para bienes físicos',
        'valid_until_after' => 'La fecha de fin debe ser posterior a la fecha de inicio'
    ],

    // Actions
    'actions' => [
        'delete' => 'Eliminar Utilidad',
        'confirm_delete_title' => 'Confirmar Eliminación',
        'confirm_delete_message' => '¿Estás seguro de que quieres eliminar esta utilidad? Esta acción no se puede deshacer.',
        'delete_success' => '¡Utilidad eliminada con éxito!',
        'delete_error' => 'Error al eliminar la utilidad.',
    ]
];
