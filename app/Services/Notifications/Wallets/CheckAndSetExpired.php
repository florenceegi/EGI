<?php

namespace App\Services\Notifications\Wallets;

use App\DataTransferObjects\Payloads\Wallets\WalletExpireRequest;
use App\DataTransferObjects\Payloads\Wallets\WalletExpireResponse;
use App\Enums\NotificationHandlerType;
use App\Enums\NotificationStatus;
use App\Notifications\NotificationExpired;
use App\Services\Notifications\NotificationHandlerFactory;
use App\Services\Notifications\RequestWalletService;
use App\Services\Notifications\ResponseWalletService;
use App\Services\StatusService;
use Illuminate\Support\Facades\Schedule;
use App\Models\CustomDatabaseNotification;
use App\Models\NotificationPayloadWallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckAndSetExpired {
    public function __construct(
        private readonly RequestWalletService $requestWalletService,
        private readonly NotificationHandlerFactory $notificationFactory,
        private readonly ResponseWalletService $responseWalletService
    ) {
    }

    public function checkAndSetExpired(): void {
        $expirationHours = config('app.notifications.expiration_hours', 72);
        $old_now = Carbon::now()->subHours($expirationHours);

        Log::channel('florenceegi')->info('Notifiche scadute', [
            'expiration_hours' => $expirationHours,
            'old_now' => $old_now,
            'now' => Carbon::now()
        ]);

        $array_statuses = config("statuses." . NotificationStatus::PENDING->value, []);
        Log::channel('florenceegi')->info('Array status', [
            'array_statuses' => $array_statuses
        ]);

        $expiredNotifications = CustomDatabaseNotification::where('created_at', '<', $old_now)
            ->whereIn('outcome', $array_statuses)
            ->where('model_type', NotificationPayloadWallet::class)
            ->get();

        Log::channel('florenceegi')->info('Notifiche scadute', [
            'expired_notifications' => $expiredNotifications->count()
        ]);

        foreach ($expiredNotifications as $notification) {

            $expiredRequest = WalletExpireResponse::fromRequest($notification);

            Log::channel('florenceegi')->info('Notifica scaduta', [
                'notification_id' => $notification->id,
                'expired_request' => $expiredRequest
            ]);

            $this->responseWalletService->expiredWallet($expiredRequest);
        }

        // Log::channel('florenceegi')->info("🔔 Notifiche scadute aggiornate: " . $expiredNotifications->count());
    }
}