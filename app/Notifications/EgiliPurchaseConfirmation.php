<?php

namespace App\Notifications;

use App\Models\EgiliMerchantPurchase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * @Oracode Notification: Egili Purchase Confirmation Email
 * 🎯 Purpose: Send purchase confirmation email to buyer after successful Egili purchase
 * 🧱 Core Logic: Beautiful HTML email with order details and receipt
 * 🛡️ GDPR Compliance: Contains order reference and purchase details
 * 
 * @package App\Notifications
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili Purchase System)
 * @date 2025-11-02
 * @purpose Purchase confirmation email notification
 */
class EgiliPurchaseConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The Egili purchase instance
     *
     * @var EgiliMerchantPurchase
     */
    protected EgiliMerchantPurchase $purchase;

    /**
     * Create a new notification instance.
     *
     * @param EgiliMerchantPurchase $purchase
     */
    public function __construct(EgiliMerchantPurchase $purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $confirmationUrl = route('egili.purchase.confirmation', [
            'orderReference' => $this->purchase->order_reference
        ]);

        return (new MailMessage)
            ->subject(__('egili.email.purchase_confirmation_subject', [
                'order_ref' => $this->purchase->order_reference
            ]))
            ->greeting(__('egili.email.greeting', ['name' => $notifiable->name]))
            ->line(__('egili.email.purchase_success'))
            ->line(__('egili.email.order_reference', [
                'reference' => $this->purchase->order_reference
            ]))
            ->line(__('egili.email.purchase_details'))
            ->line('💎 **' . __('egili.confirmation.egili_purchased') . '**: ' . 
                number_format($this->purchase->egili_amount) . ' Egili')
            ->line('💰 **' . __('egili.confirmation.total_paid') . '**: ' . 
                $this->purchase->formatted_total)
            ->line('💳 **' . __('egili.confirmation.payment_method') . '**: ' . 
                ($this->purchase->isFiatPayment() ? 'FIAT (EUR)' : 'Crypto'))
            ->line('🕒 **' . __('egili.confirmation.purchased_at') . '**: ' . 
                $this->purchase->purchased_at->format('d/m/Y H:i'))
            ->action(__('egili.email.view_order'), $confirmationUrl)
            ->line(__('egili.email.invoice_info'))
            ->line(__('egili.email.thank_you'))
            ->salutation(__('egili.email.signature'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'purchase_id' => $this->purchase->id,
            'order_reference' => $this->purchase->order_reference,
            'egili_amount' => $this->purchase->egili_amount,
            'total_eur' => $this->purchase->total_price_eur,
        ];
    }
}





