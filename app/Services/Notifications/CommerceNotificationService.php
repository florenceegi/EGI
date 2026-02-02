<?php

namespace App\Services\Notifications;

use App\Models\EgiBlockchain;
use App\Models\NotificationPayloadShipping;
use App\Models\User;
use App\Notifications\Commerce\EgiSoldNotification;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Support\Facades\DB;

/**
 * Service to manage Commerce-related notifications
 * Handles notification logic for Sales, Shipping, and Logistics flows.
 *
 * @package App\Services\Notifications
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Commerce Notification System)
 * @date 2026-02-02
 * @purpose Decouple commerce notification logic (e.g., Shipping Payload creation) from Controllers
 */
class CommerceNotificationService
{
    /**
     * Constructor with Dependency Injection
     *
     * @param UltraLogManager $logger Ultra Log Manager for structured logging
     * @param ErrorManagerInterface $errorManager Ultra Error Manager for error handling
     */
    public function __construct(
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager
    ) {}

    /**
     * Handle notifications when an EGI is sold.
     * Creates the shipping payload (for logistics tracking) and notifies the seller.
     *
     * @param EgiBlockchain $record The blockchain record of the mint/sale transaction
     * @param User $seller The owner/seller of the EGI (who needs to ship it)
     * @param User $buyer The user who bought/minted the EGI
     * @return void
     * @throws \Exception If critical failure occurs (though usually handled via UEM)
     */
    public function handleSold(EgiBlockchain $record, User $seller, User $buyer): void
    {
        $this->logger->info('[COMMERCE_NOTIFICATION] Starting handleSold', [
            'record_id' => $record->id,
            'egi_id' => $record->egi_id,
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id
        ]);

        try {
            DB::transaction(function () use ($record, $seller, $buyer) {
                // 1. Create Persistent Payload for Shipping/Logistics
                // This payload links the financial transaction to the logistics workflow
                // and provides the data context for the "Insert Tracking" action in the Dashboard.
                $payload = NotificationPayloadShipping::create([
                    'egi_blockchain_id' => $record->id,
                    'seller_id' => $seller->id,
                    'buyer_id' => $buyer->id,
                    'shipping_address_snapshot' => $record->shipping_address_snapshot,
                    'status' => 'pending' // Initial logistics status
                ]);

                $this->logger->debug('[COMMERCE_NOTIFICATION] Shipping Payload created', [
                    'payload_id' => $payload->id,
                    'status' => $payload->status
                ]);

                // 2. Notify the Seller (Merchant)
                // Triggers the notification that contains the Action Button to insert tracking info.
                $seller->notify(new EgiSoldNotification($payload));

                $this->logger->info('[COMMERCE_NOTIFICATION] EgiSoldNotification sent to Seller', [
                    'seller_id' => $seller->id,
                    'payload_id' => $payload->id
                ]);
            });

        } catch (\Exception $e) {
            // UEM: Handle error via standard ErrorManager
            // We use 'not' blocking type typically, as payment succeeded and we don't want to revert
            // the whole transaction just because a notification failed. But we alert DEV team.
            $this->errorManager->handle('COMMERCE_NOTIFICATION_SEND_ERROR', [
                'operation' => 'handleSold',
                'record_id' => $record->id,
                'seller_id' => $seller->id,
                'error_message' => $e->getMessage(),
                'method' => __METHOD__,
            ], $e);

            // Re-throw if blocking policy requires it, otherwise consume exception
            // For now, consistent with ReservationService, we throw to allow caller to decide
            throw $e;
        }
    }
}
