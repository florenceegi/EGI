<?php

namespace App\Notifications\Dsa;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * DSA Complaint Received Notification
 *
 * Sends email confirmation to the user after submitting a complaint.
 * Compliance: Digital Services Act (Reg. UE 2022/2065) Art. 16, 20.
 */
class ComplaintReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Complaint $complaint
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $reference = $this->complaint->complaint_reference;

        return (new MailMessage)
            ->subject(__('complaints.notification.subject', ['reference' => $reference]))
            ->greeting(__('complaints.notification.greeting', ['name' => $notifiable->name]))
            ->line(__('complaints.notification.body', ['reference' => $reference]))
            ->line(__('complaints.notification.body_2'))
            ->line('**' . __('complaints.notification.reference_label') . '**: ' . $reference)
            ->line('**' . __('complaints.notification.type_label') . '**: ' . __('complaints.types.' . $this->complaint->type))
            ->line('**' . __('complaints.notification.date_label') . '**: ' . $this->complaint->created_at->format('d/m/Y H:i'))
            ->action(__('complaints.view_details'), route('complaints.show', $this->complaint))
            ->salutation(__('complaints.notification.closing'));
    }
}
