<?php

namespace App\Notifications\Channels;

use App\Enums\NotificationStatus;
use App\Models\CustomDatabaseNotification;
use App\Models\Notification as ModelsNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CustomDatabaseChannel
{
    public function send($notifiable, Notification $notification)
    {

        // Recupera i dati dal metodo "toCustomDatabase()" della notifica
        $data = $notification->toCustomDatabase($notifiable);

        /**
         *  Legge l'ID della notifica precedente, se presente
         *  Questo ID viene utilizzato per aggiornare lo stato della notifica precedente
         *  quando l'azione è ACCEPTED o REJECTED.
         */
        $notification_prevId = $data['prev_id'] ?? null;

        Log::channel('florenceegi')->info('CustomDatabaseChannel:send', [
            'data' => $data,
        ]);

        // Ottieni il nome della classe della notifica
        $action = get_class($notification);

        Log::channel('florenceegi')->info('CustomDatabaseChannel:send', [
            'action' => $action,
            // 'notification' => $notification,
            // 'data' => $data,
        ]);

        // Mappatura delle classi di notifica alle sole azioni di risposta
        $actionResponseMap = [
            'App\\Notifications\\Invitations\\InvitationAccepted' => NotificationStatus::ACCEPTED,
            'App\\Notifications\\Invitations\\InvitationRejection' => NotificationStatus::REJECTED,
            'App\\Notifications\\Wallets\\WalletRejection' => NotificationStatus::REJECTED,
            'App\\Notifications\\Wallets\\WalletAccepted' => NotificationStatus::ACCEPTED,
             // Commerce (Uses Enum)
            'App\\Notifications\\Commerce\\EgiShippedNotification' => NotificationStatus::SHIPPED,
        ];

        // Controlla se l'azione corrisponde a una chiave nella mappatura
        if (isset($actionResponseMap[$action])) {
            $action = $actionResponseMap[$action];
        }

        // Se l'azione è ACCEPTED, REJECTED o SHIPPED, aggiorna la notifica precedente
        if ($action === NotificationStatus::ACCEPTED || $action === NotificationStatus::REJECTED || $action === NotificationStatus::SHIPPED) {
            Log::channel('florenceegi')->info('Notifica precedente aggiornata', [
                'notification->id' => $notification->id,
            ]);
            // Aggiorna la notifica precedente con lo stato di risposta
            $this->updatePreviousNotification($notification_prevId, $action);
        }

        // Validazione dei dati
        if (!isset($data['view'], $data['model_type'], $data['model_id'])) {
            Log::channel('florenceegi')->error('Dati mancanti per la notifica', ['data' => $data]);
            return null;
        }

        // Creiamo manualmente il record nella tabella notifications
        $createdNotification = CustomDatabaseNotification::create([
            'id'             => $notification->id,
            'type'           => get_class($notification),
            'view'           => $data['view'],
            'notifiable_type'=> get_class($notifiable),
            'notifiable_id'  => $notifiable->getKey(),
            'sender_id'      => $data['sender_id'] ?? 1, // Default: sistema
            'model_type'     => $data['model_type'],
            'model_id'       => $data['model_id'],
            'data'           => $data['data'] ?? [],
            'outcome'        => $data['outcome'] ?? 'pending', // Default: pending
        ]);

        Log::channel('florenceegi')->info('Notifica creata', [
            'id' => $createdNotification->id,
            'type' => $createdNotification->type,
        ]);

        return $createdNotification;
    }

    /**
     * Aggiorna il record della notifica precedente con lo stato di risposta.
     *
     * Una volta aggiornato, la notifica viene considerata archiviata.
     *
     * @param string $notificationId L'ID della notifica da aggiornare
     * @param string $outcome Lo stato di risposta (ACCEPTED o REJECTED)
     * @return void
     */
    private function updatePreviousNotification($notificationId, $outcome)
    {
        $prev_notification = ModelsNotification::where('id', "=",$notificationId)->first();

        if (!$prev_notification) {
            Log::channel('florenceegi')->warning('Notifica precedente non trovata', [
                'id' => $notificationId,
                'outcome' => $outcome,
                // 'trace' => (new \Exception())->getTraceAsString() // Aggiunge lo stack trace
            ]);
            return;
        }

        $prev_notification->update([
            'read_at' => now(),
            'outcome' => $outcome,
        ]);

        Log::channel('florenceegi')->info('Notifica precedente aggiornata', [
            'id' => $notificationId,
            'outcome' => $outcome,
        ]);
    }
}
