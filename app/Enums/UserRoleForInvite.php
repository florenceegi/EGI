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
}
