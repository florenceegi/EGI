<?php

namespace App\Notifications\Commerce;

use App\Models\NotificationPayloadShipping;
use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // ✅ Changed to NOW to bypass Queue
use Illuminate\Notifications\Messages\BroadcastMessage;

class EgiSoldNotification extends Notification implements ShouldBroadcastNow // ✅ SYNC DELIVERY
{
    use Queueable;

    private NotificationPayloadShipping $payload;

    public function __construct(NotificationPayloadShipping $payload)
    {
        $this->payload = $payload;
    }

    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class, 'broadcast'];
    }

    public function toCustomDatabase($notifiable)
    {
        return [
            'view'          => 'commerce.egi_sold', // Matches config key structure
            'model_type'    => NotificationPayloadShipping::class,
            'model_id'      => $this->payload->id,
            'sender_id'     => $this->payload->buyer_id, 
            'data'          => [
                'amount'            => $this->payload->formatted_amount,
                'buyer_name'        => $this->payload->buyer->name,
                'buyer_email'       => $this->payload->buyer->email,
                'shipping_snapshot' => $this->payload->shipping_address_snapshot,
                'egi_name'          => ($this->payload->egi->title ?? 'EGI Asset') . 
                                       ($this->payload->egi->token_EGI ? ' (ASA: ' . $this->payload->egi->token_EGI . ')' : ''),
            ],
            'outcome'       => \App\Enums\NotificationStatus::PENDING->value, // Visibile in lista
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'   => 'Nuova Vendita EGI!',
            'body'    => "Hai venduto '{$this->payload->egi->title}' a {$this->payload->buyer->name}. Clicca per gestire la spedizione.",
            'action_url' => route('dashboard'), // Or specific management URL if available
            'type'    => 'commerce',
            'payload_id' => $this->payload->id,
        ]);
    }
    /**
     * Get the type of the notification being stored.
     */
    public function databaseType(object $notifiable): string
    {
        return 'commerce';
    }
}
