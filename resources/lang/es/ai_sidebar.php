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
    'assistant_name' => 'Natan',
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

    // Discourse messages (AI-like text)
    'discourse' => [
        'greeting' => 'Hola',
        'greeting_suffix' => '! Estoy aquí para ayudarte a completar tu perfil.',
        'progress_intro' => 'Has completado',
        'progress_of' => 'de',
        'progress_suffix' => ' pasos. Veamos juntos qué falta para que tu perfil sea perfecto.',
        'missing_title' => 'Esto es lo que aún necesitas hacer:',
        'suggestion_intro' => 'Te sugiero empezar por',
        'click_hint' => 'Haz clic en cualquier elemento de la lista de abajo para completarlo.',
        'complete_title' => '¡Fantástico!',
        'complete_text' => 'Tu perfil está completo y listo para ser descubierto. Ahora puedes concentrarte en crear tus obras y hacer crecer tu comunidad.',
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
        'invalid_user_type' => 'Tipo de usuario no válido.',
        'unauthorized' => 'No autorizado para acceder a este recurso.',
    ],
];
