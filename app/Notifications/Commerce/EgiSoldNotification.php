<?php

namespace App\Notifications\Commerce;

use App\Models\NotificationPayloadShipping;
use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EgiSoldNotification extends Notification
{
    use Queueable;

    private NotificationPayloadShipping $payload;

    public function __construct(NotificationPayloadShipping $payload)
    {
        $this->payload = $payload;
    }

    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class];
    }

    public function toCustomDatabase($notifiable)
    {
        return [
            'view'          => 'notifications.commerce.egi_sold',
            'model_type'    => NotificationPayloadShipping::class,
            'model_id'      => $this->payload->id,
            'sender_id'     => $this->payload->buyer_id, 
            'data'          => [
                'amount'            => $this->payload->formatted_amount, // Uses helper in model
                'buyer_name'        => $this->payload->buyer->name,      // Uses helper in model
                'buyer_email'       => $this->payload->buyer->email,
                'shipping_snapshot' => $this->payload->shipping_address_snapshot,
                'egi_name'          => $this->payload->egi->name ?? 'EGI Asset',
            ],
            'outcome'       => 'pending',
        ];
    }
}
