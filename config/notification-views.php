<?php

return [
    'invitations' => [
        'accepted' => [
            'view' => 'notifications.invitations.approval',
            'render' => 'livewire',
        ],

        'request' => [
            'view' => 'notifications.invitations.request',
            'render' => 'livewire',
        ],

        'rejected' => [
            'view' => 'notifications.invitations.rejected',
            'render' => 'livewire',
        ],
    ],
    'wallets' => [
        'creation' => [
            'view' => 'notifications.wallets.creation',
            'render' => 'controller',
            'controller' => 'App\\Http\\Controllers\\Notifications\Wallets\\NotificationWalletResponseController'
        ],
        'accepted' => [
            'view' => 'notifications.wallets.accepted',
            'render' => 'livewire',
        ],
        'rejected' => [
            'view' => 'notifications.wallets.rejected',
            'render' => 'controller',
            'controller' => 'App\\Http\\Controllers\\Notifications\Wallets\\NotificationWalletResponseController'
        ],
        'pending_create' => [
            'view' => 'notifications.wallets.creation',
            'render' => 'controller',
            'controller' => 'App\\Http\\Controllers\\Notifications\Wallets\\NotificationWalletResponseController'
        ],
        'expired' => [
            'view' => 'notifications.wallets.update',
            'render' => 'controller',
            'controller' => 'App\\Http\\Controllers\\Notifications\Wallets\\NotificationWalletResponseController'
        ],
        'pending_update' => [
            'view' => 'notifications.wallets.creation',
            'render' => 'controller',
            'controller' => 'App\\Http\\Controllers\\Notifications\Wallets\\NotificationWalletResponseController'
        ],
        'change-request' => [
            'view' => 'livewire.notifications.wallets.change-request',
            'render' => 'include',
        ],
        'change-response-rejected' => [
            'view' => 'livewire.notifications.wallets.change-response-rejected',
            'render' => 'include',
        ],
    ],
    'gdpr' => [
        // Interactive notifications (require user action)
        'consent_updated' => [
            'view' => 'notifications.gdpr.generic_alert',
            'render' => 'include',
            'type' => 'interactive', // ← Extra metadata
        ],

        // Informational notifications (read-only)
        'data_exported' => [
            'view' => 'notifications.gdpr.informational',
            'render' => 'include',
            'type' => 'informational',
        ],
        'processing_restricted' => [
            'view' => 'notifications.gdpr.informational',
            'render' => 'include',
            'type' => 'informational',
        ],
        'account_deletion_requested' => [
            'view' => 'notifications.gdpr.informational',
            'render' => 'include',
            'type' => 'informational',
        ],
        'account_deletion_processed' => [
            'view' => 'notifications.gdpr.informational',
            'render' => 'include',
            'type' => 'informational',
        ],
        'breach_report_received' => [
            'view' => 'notifications.gdpr.informational',
            'render' => 'include',
            'type' => 'informational',
        ],
        'default' => [
            'view' => 'notifications.gdpr.default',
            'render' => 'include',
            'type' => 'informational',
        ],
    ],

    // === NUOVA SEZIONE: COMMERCE ===
    'commerce' => [
        'egi_sold' => [
            'view' => 'notifications.commerce.egi_sold',
            'render' => 'include',
            'type' => 'interactive',
        ],
        'egi_shipped' => [
            'view' => 'notifications.commerce.egi_shipped',
            'render' => 'include',
            'type' => 'informational',
        ],
        'default' => [
            'view' => 'notifications.commerce.default',
            'render' => 'include',
            'type' => 'informational',
        ],
    ],

    // === NUOVA SEZIONE: RESERVATIONS ===
    'reservations' => [
        'highest' => [
            'view' => 'notifications.reservations.highest',
            'render' => 'include',

        ],
        'superseded' => [
            'view' => 'notifications.reservations.superseded',
            'render' => 'include',
            'type' => 'informational',
        ],
        'rank-changed' => [
            'view' => 'notifications.reservations.rank-changed',
            'render' => 'include',
            'type' => 'informational',
        ],
        'rank-improved' => [
            'view' => 'notifications.reservations.rank-improved',
            'render' => 'include',
            'type' => 'informational',
        ],
        'competitor-withdrew' => [
            'view' => 'notifications.reservations.competitor-withdrew',
            'render' => 'include',
            'type' => 'informational',
        ],
        'reservation-expired' => [
            'view' => 'notifications.reservations.reservation-expired',
            'render' => 'include',
            'type' => 'informational',
        ],
        'pre-launch-reminder' => [
            'view' => 'notifications.reservations.pre-launch-reminder',
            'render' => 'include',
            'type' => 'informational',
        ],
        'mint-window-open' => [
            'view' => 'notifications.reservations.mint-window-open',
            'render' => 'include',
            'type' => 'informational',
        ],
        'mint-window-closing' => [
            'view' => 'notifications.reservations.mint-window-closing',
            'render' => 'include',
            'type' => 'informational',
        ],
        'default' => [
            'view' => 'notifications.reservations.default',
            'render' => 'include',
            'type' => 'informational',
        ],
    ],
];
