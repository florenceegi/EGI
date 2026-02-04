<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use App\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Class CommerceNotificationHandler
 *
 * Handler per le notifiche Commerce.
 * Gestisce la logica specifica per le notifiche di spedizione e vendita.
 *
 * @package App\Services\Notifications
 */
class CommerceNotificationHandler implements NotificationHandlerInterface
{
    /**
     * Gestisce l'azione di notifica.
     *
     * @param string $action L'azione da eseguire
     * @param Model $payload Il payload della notifica
     * @param array $data Dati aggiuntivi
     * @return array Risposta con stato di successo e messaggio
     */
    public function handle(string $action, Model $payload, array $data = []): array
    {
        Log::channel('florenceegi')->info('Commerce Handler Invoked', [
            'action' => $action,
            'payload_id' => $payload->id,
            'data_keys' => array_keys($data)
        ]);

        return match ($action) {
            'archive' => $this->handleArchive($payload),
            default => [
                'success' => false,
                'message' => "Azione non supportata: $action"
            ]
        };
    }

    /**
     * Logica di archiviazione (se invocata via Handler invece che Controller)
     */
    private function handleArchive(Model $payload): array
    {
        // Nota: La logica principale è nel Controller, ma manteniamo questo per completezza
        return [
            'success' => true,
            'message' => 'Notifica archiviata (Handler)'
        ];
    }

    /**
     * Ottiene le azioni supportate da questo handler.
     *
     * @return array Lista delle azioni supportate
     */
    public function getSupportedActions(): array
    {
        return [
            'archive',
            'shipped'
        ];
    }
}
