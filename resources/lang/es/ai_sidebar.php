<?php

/**
 * AI Sidebar - Traducciones en Español
 * P0-2: Translation keys only
 * P0-9: All 6 languages required
 */

return [
    // General
    'title' => 'Asistente IA',
    'subtitle' => 'Te ayudo a completar tu perfil',
    'toggle_title' => 'Abrir asistente de onboarding',
    'close' => 'Cerrar',
    'send' => 'Enviar',
    'chat_placeholder' => 'Pregunta algo...',
    'assistant_name' => 'EGI Asistente',
    'quick_actions_label' => 'Próximos pasos sugeridos:',

    // Messages (programmatic AI)
    'messages' => [
        'welcome' => '¡Bienvenido! Te ayudo a completar la configuración de tu perfil.',
        'progress_low' => '¡Buen comienzo! Has completado :completed de :total pasos. Siguiente: :nextStep',
        'progress_high' => '¡Ya casi! Solo faltan :remaining pasos.',
        'complete' => '¡Felicitaciones! Tu perfil está completamente configurado. 🎉',
        'all_done' => '¡Todos los pasos completados!',
    ],

    // Checklist
    'checklist' => [
        'progress' => 'Progreso de configuración',
        'title' => 'Lista de Onboarding',
    ],

    // Checklist Items - Creator
    'steps' => [
        'avatar' => [
            'title' => 'Sube tu avatar',
            'description' => 'Añade una foto de perfil para ser reconocido',
        ],
        'banner' => [
            'title' => 'Personaliza tu banner',
            'description' => 'Añade una imagen de portada a tu perfil',
        ],
        'bio' => [
            'title' => 'Escribe tu bio',
            'description' => 'Cuéntanos quién eres y qué creas',
        ],
        'stripe' => [
            'title' => 'Configura los pagos',
            'description' => 'Conecta Stripe para recibir pagos',
        ],
        'collection' => [
            'title' => 'Crea tu primera colección',
            'description' => 'Organiza tus obras en una colección',
        ],
        'first_egi' => [
            'title' => 'Crea tu primer EGI',
            'description' => 'Publica tu primera obra digital',
        ],
        'social_links' => [
            'title' => 'Añade enlaces sociales',
            'description' => 'Conecta tus perfiles sociales',
        ],
        'verify_email' => [
            'title' => 'Verifica tu email',
            'description' => 'Confirma tu dirección de correo',
        ],
    ],

    // Errors
    'errors' => [
        'request_failed' => 'Solicitud fallida. Por favor, inténtalo de nuevo.',
        'connection_error' => 'Error de conexión. Por favor, verifica tu conexión a internet.',
    ],
];
