<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @package   App\Enums
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   1.1.0
 * @date      2025-06-11
 * @solution  Provides a centralized, type-safe enumeration for all available notification handlers, driving the NotificationHandlerFactory.
 *
 * --- OS1 DOCUMENTATION ---
 * @oracode-intent: To create a single source of truth for all notification handler types, ensuring type safety and facilitating the factory pattern for handler instantiation.
 * @oracode-value-flow: This enum provides the `key` (e.g., 'gdpr') and maps it to a concrete `value` (the handler's class string), which is then used by the factory to create the correct handler instance.
 * @os1-compliance: Full.
 */
enum NotificationHandlerType: string
{
    /**
     * Handler per le notifiche relative ai wallet.
     */
    case WALLET = 'wallet';

    /**
     * Handler per le notifiche di invito.
     */
    case INVITATION = 'invitation';

    /**
     * Handler per le notifiche relative al GDPR.
     */
    /**
     * Handler per le notifiche relative al GDPR.
     */
    case GDPR = 'gdpr';

    /**
     * Handler per le notifiche Commerce.
     */
    case COMMERCE = 'commerce';

    /**
     * Ottiene il nome della classe handler associata al tipo.
     *
     * @return class-string La classe dell'handler.
     */
    public function getHandlerClass(): string
    {
        return match($this) {
            self::WALLET => \App\Services\Notifications\WalletNotificationHandler::class,
            self::INVITATION => \App\Services\Notifications\InvitationNotificationHandler::class,
            self::GDPR => \App\Services\Notifications\GdprNotificationHandler::class,
            self::COMMERCE => \App\Services\Notifications\CommerceNotificationHandler::class,
        };
    }
}
