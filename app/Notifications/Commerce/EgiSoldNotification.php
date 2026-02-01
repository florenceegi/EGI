<?php

namespace App\Notifications\Commerce;

use App\Models\EgiBlockchain;
use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EgiSoldNotification extends Notification
{
    use Queueable;

    private EgiBlockchain $purchase;

    public function __construct(EgiBlockchain $purchase)
    {
        $this->purchase = $purchase;
    }

    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class];
    }

    public function toCustomDatabase($notifiable)
    {
        return [
            'view'          => 'notifications.commerce.egi_sold',
            'model_type'    => EgiBlockchain::class,
            'model_id'      => $this->purchase->id,
            'sender_id'     => $this->purchase->buyer_user_id, // Il buyer è il sender della "richiesta" di ordine
            'data'          => [
                'amount'            => $this->purchase->formatted_amount,
                'buyer_name'        => $this->purchase->buyer->name,
                'buyer_email'       => $this->purchase->buyer->email,
                'shipping_snapshot' => $this->purchase->shipping_address_snapshot,
                'egi_name'          => $this->purchase->egi->name ?? 'EGI Asset',
            ],
            'outcome'       => 'pending',
        ];
    }
}
