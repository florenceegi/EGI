<?php

namespace App\Notifications\Commerce;

use App\Models\EgiBlockchain;
use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EgiShippedNotification extends Notification
{
    use Queueable;

    private EgiBlockchain $purchase;

    public function __construct(EgiBlockchain $purchase)
    {
        $this->purchase = $purchase;
    }

    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class, 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('commerce.notifications.shipped.subject'))
            ->greeting(__('commerce.notifications.shipped.greeting', ['name' => $notifiable->name]))
            ->line(__('commerce.notifications.shipped.intro', ['item' => $this->purchase->egi->name ?? 'Articolo']))
            ->line(__('commerce.notifications.shipped.carrier', ['carrier' => $this->purchase->carrier]))
            ->line(__('commerce.notifications.shipped.tracking', ['code' => $this->purchase->tracking_code]))
            ->action(__('commerce.notifications.shipped.action_view'), url('/dashboard/purchases/' . $this->purchase->id))
            ->line(__('commerce.notifications.shipped.outro'));
    }

    public function toCustomDatabase($notifiable)
    {
        return [
            'view'          => 'commerce.egi_shipped',
            'model_type'    => EgiBlockchain::class,
            'model_id'      => $this->purchase->id,
            'sender_id'     => 1, // System
            'data'          => [
                // No hardcoded message needed here since the view will handle it
                // using the keys and the data provided below
                'carrier'       => $this->purchase->carrier,
                'tracking_code' => $this->purchase->tracking_code,
                'egi_name'      => $this->purchase->egi->name ?? 'EGI Asset',
            ],
            'outcome'       => \App\Enums\NotificationStatus::PENDING->value, // Visibile in lista
        ];
    }
    /**
     * Get the type of the notification being stored.
     */
    public function databaseType(object $notifiable): string
    {
        return 'commerce';
    }
}
