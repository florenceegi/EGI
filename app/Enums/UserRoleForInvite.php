<?php

namespace App\Enums;

enum UserRoleForInvite: string {
    case CREATOR = 'creator';
    case PATRON = 'patron';
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case GUEST = 'guest';

    /**
     * Ottieni tutti i valori dell'enum come array
     */
    public static function values(): array {
        return array_column(self::cases(), 'value');
    }

    /**
     * Ruoli che possono essere INVITATI (ruolo sulla piattaforma)
     * Solo questi user types possono ricevere inviti per collezioni
     */
    public static function allowedPlatformRoles(): array {
        return ['creator', 'patron', 'enterprise'];
    }

    /**
     * Ruoli disponibili DENTRO la collezione (ruolo assegnato nella collezione)
     * Questi sono i ruoli che si possono assegnare agli invitati dentro la collezione
     */
    public static function collectionRoles(): array {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Ottieni tutti i valori dell'enum con labels leggibili
     */
    public static function options(): array {
        return [
            self::CREATOR->value => 'Creator',
            self::PATRON->value => 'Patron',
            self::ADMIN->value => 'Admin',
            self::EDITOR->value => 'Editor',
            self::GUEST->value => 'Guest',
        ];
    }

    /**
     * Labels per i ruoli piattaforma consentiti agli inviti
     */
    public static function allowedPlatformRoleLabels(): array {
        return [
            'creator' => 'Creator',
            'patron' => 'Patron',
            'enterprise' => 'Enterprise',
        ];
    }

    /**
     * Ottieni il label per un valore specifico
     */
    public function label(): string {
        return match ($this) {
            self::CREATOR => 'Creator',
            self::PATRON => 'Patron',
            self::ADMIN => 'Admin',
            self::EDITOR => 'Editor',
            self::GUEST => 'Guest',
        };
    }

    /**
     * Verifica se un ruolo piattaforma può essere invitato
     */
    public static function canBeInvited(string $platformRole): bool {
        return in_array($platformRole, self::allowedPlatformRoles());
    }

    /**
     * Verifica se un ruolo è valido per essere assegnato in una collezione
     */
    public static function isValidCollectionRole(string $collectionRole): bool {
        return in_array($collectionRole, self::collectionRoles());
    }
}