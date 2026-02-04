<?php

namespace App\Enums;

use ValueError;

/**
 * @package App\Enums
 * @author  Padmin D. Curtis (for Fabio Cherici)
 * @version 2.0.0
 * @date    2025-06-11
 * @solution Provides a centralized and type-safe enumeration for all possible notification states, including the new interactive GDPR flow.
 */
enum NotificationStatus: string {
    // Stati generici
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case DONE = 'done';
    case EXPIRED = 'expired';
    case ARCHIVED = 'Archived';

        // Stati per proposte a due vie (Inviti, Wallet)
    case REQUEST = 'request';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

        // Stati specifici per il nuovo flusso GDPR
    case PENDING_CONFIRMATION = 'pending_confirmation';
    case CONFIRMED = 'confirmed';
    case REVOKED = 'revoked';
    case DISAVOWED = 'disavowed';

        // Stati legacy/tecnici
    case CREATION = 'creation';
    case PENDING_CREATE = 'pending_create';
    case PENDING_UPDATE = 'pending_update';
    case UPDATE = 'update';
        // Commerce
    case SHIPPED = 'shipped';

    public static function fromDatabase(string $value): self {
        foreach (self::cases() as $case) {
            if ($value === $case->value) {
                return $case;
            }
        }
        throw new ValueError("Status '{$value}' non valido");
    }
}
