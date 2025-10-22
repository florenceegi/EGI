<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\DataTransferObjects\Notifications\Wallets\WalletCreateRequest;
use App\DataTransferObjects\Notifications\NotificationData;
use App\DataTransferObjects\Notifications\Wallets\WalletUpdateRequest;
use App\DataTransferObjects\Payloads\Wallets\WalletAcceptRequest;
use App\DataTransferObjects\Payloads\Wallets\WalletError;
use App\DataTransferObjects\Payloads\Wallets\WalletExpireResponse;
use App\DataTransferObjects\Payloads\Wallets\WalletQuotaValidation;
use App\DataTransferObjects\Payloads\Wallets\WalletRejectRequest;
use App\Enums\NotificationHandlerType;
use App\Enums\NotificationStatus;
use App\Enums\PlatformRole;
use App\Exceptions\WalletException;
use App\Models\CustomDatabaseNotification;
use App\Models\NotificationPayloadWallet;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service per la gestione delle operazioni sui wallet
 */
class ResponseWalletService
{
    public function __construct(
        private readonly NotificationHandlerFactory $notificationFactory
    ) {}


    /**
     * Gestisce l'accettazione di un wallet
     */
    public function acceptCreateWallet(WalletAcceptRequest $request): void
    {

        Log::channel('florenceegi')->info('WalletService:Accepting create wallet', [
            'request' => $request,
        ]);

        try {
            DB::transaction(function () use ($request) {

                // Recupero il payload della notifica
                $walletPayload = NotificationPayloadWallet::findOrFail($request->wallet_payload_id);

                // Aggiornamento stati
                $walletPayload->update(['status' => NotificationStatus::ACCEPTED->value]);

                Log::channel('florenceegi')->info('WalletService:Accepting create wallet', [
                    'wallet_payload' => $walletPayload,
                    'notification_id' => $request->notification_id,
                ]);

                // Validazione quote
                $validation = WalletQuotaValidation::fromPayload($walletPayload);
                $validationArray = (array) $validation;
                $$walletPayload['metadata'] = $walletPayload->id;

                $this->createWallet($validationArray);

                $notification = CustomDatabaseNotification::findOrFail($request->notification_id);
                // Aggiornamento stati
                $notification->update(['outcome' => NotificationStatus::ACCEPTED->value]);


                // Invio notifica: ATTUALMENTE LA NOTIFICA SULLA RISPOSTA NON VIENE INVIATA
                // $this->acceptNotification($walletPayload, $request);

            });
        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore durante l\'accettazione del wallet:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request,
            ]);
            throw $e;
        }
    }

    public function acceptUpdateWallet(WalletAcceptRequest $request): void
    {

        Log::channel('florenceegi')->info('WalletService:Accepting updating wallet', [
            'request' => $request,
        ]);

        try {
            DB::transaction(function () use ($request) {

                // Recupero il payload della notifica
                $walletPayload = NotificationPayloadWallet::findOrFail($request->wallet_payload_id);

                Log::channel('florenceegi')->info('WalletService:Accepting updating wallet', [
                    'wallet_payload' => $walletPayload,
                ]);

                // Validazione quote
                $validation = WalletQuotaValidation::fromPayload($walletPayload);

                $validationArray = (array) $validation;
                $validationArray['metadata'] = $walletPayload->id;

                $this->updateWallet($validationArray);

                $notification = CustomDatabaseNotification::findOrFail($request->notification_id);
                // Aggiornamento stati
                $notification->update(['outcome' => NotificationStatus::ACCEPTED->value]);
                $walletPayload->update(['status' => NotificationStatus::ACCEPTED->value]);

                // Invio notifica: ATTUALMENTE LA NOTIFICA SULLA RISPOSTA NON VIENE INVIATA
                // $this->acceptNotification($walletPayload, $request);

            });
        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore durante l\'accettazione del wallet:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request,
            ]);
            throw $e;
        }
    }

    private function createWallet(array $validation)
    {

        Log::channel('florenceegi')->info('Validation wallet', [
            'validation' => $validation,
        ]);

        // Recupera il wallet del creator
        $creatorWallet = Wallet::where('collection_id', $validation['collection_id'])
            ->where('user_id', $validation['proposer_id'])
            ->first();


        // Aggiorna quote creator
        $creatorWallet->update([
            'royalty_mint' => $creatorWallet->royalty_mint - $validation['required_mint_quota'],
            'royalty_rebind' => $creatorWallet->royalty_rebind - $validation['required_rebind_quota'],
        ]);

        // Crea nuovo wallet
        Wallet::create(
            [
                'collection_id' => $validation['collection_id'],
                'user_id' => $validation['receiver_id'],
                'notification_payload_wallets_id' => $validation['id'],
                'wallet' => $validation['wallet'],
                'royalty_mint' => $validation['required_mint_quota'],
                'royalty_rebind' => $validation['required_rebind_quota'],
                'metadata' => $validation['metadata'],
                'platform_role' => null, // Membri collection non hanno platform_role specifico
            ]
        );
    }

    private function requestNotification($walletPayload, $request): void
    {
        // Preparare i dati per la notifica
        $notificationData = new NotificationData(
            model_type: $walletPayload::class,
            model_id: $walletPayload->id,
            view: 'wallets.' . $walletPayload->status,
            sender_id: Auth::id(),
            prev_id: null,
            message: $walletPayload->message,
            reason: null,
            sender_name: Auth::user()->name . ' ' . Auth::user()->last_name,
            sender_email: Auth::user()->email,
            collection_name: $walletPayload->collection->collection_name,
            status: $walletPayload->status,
            old_royalty_mint: $request->old_royalty_mint,
            old_royalty_rebind: $request->old_royalty_rebind,
        );

        // Recuperare il ricevente della notifica
        /** @var User|null $recipient */
        $recipient = User::findOrFail($request->receiver_id);

        if (! $recipient) {
            throw new Exception(__('errors.user_not_found', [
                'id' => $request->receiver_id,
            ]));
        }

        // Inviare la notifica usando il factory pattern esistente
        $handler = $this->notificationFactory->getHandler(NotificationHandlerType::WALLET);
        $handler->handle('request', $walletPayload, [
            'user' => $recipient,
            'notification_data' => $notificationData
        ]);

        Log::channel('florenceegi')->info('Wallet request created successfully', [
            'wallet_payload_id' => $walletPayload->id,
            'receiver_id' => $recipient->id,
            'sender_id' => Auth::id(),
        ]);
    }

    private function acceptNotification($walletPayload, $request)
    {

        $notification = CustomDatabaseNotification::findOrFail($request->notification_id);

        // Preparazione e invio notifica
        $notificationData = new NotificationData(
            model_type: $walletPayload::class,
            model_id: $walletPayload->id,
            view: 'wallets.' . NotificationStatus::ACCEPTED->value,
            prev_id: $notification->id,
            sender_id: Auth::id(),
            message: __('collection.wallet.wallet_change_accepted'),
            reason: null,
            sender_name: Auth::user()->name . ' ' . Auth::user()->last_name,
            sender_email: Auth::user()->email,  // Email di chi sta inviando la notifica
            collection_name: $walletPayload->collection->collection_name,
            status: NotificationStatus::ACCEPTED->value
        );

        /** @var User|null $recipient */
        $recipient = User::find($walletPayload->proposer_id);

        if (! $recipient) {
            throw new Exception(__('collection.wallet.user_not_found', [
                'id' => $walletPayload->proposer_id,
            ]));
        }

        $handler = $this->notificationFactory->getHandler(NotificationHandlerType::WALLET);
        $handler->handle('accepted', $walletPayload, [
            'user' => $recipient,
            'notification_data' => $notificationData
        ]);
    }

    /**
     * Gestisce il rifiuto di un wallet
     */
    public function rejectWallet(WalletRejectRequest $request): void
    {
        try {
            DB::transaction(function () use ($request) {
                // Recupero gli oggetti dal database
                $notification = CustomDatabaseNotification::findOrFail($request->notification_id);
                $walletPayload = NotificationPayloadWallet::findOrFail($request->wallet_payload_id);

                // Aggiornamento stati
                $notification->update(['outcome' => NotificationStatus::REJECTED->value]);
                $walletPayload->update(['status' => NotificationStatus::REJECTED->value]);

                // Invio notifica
                /** @var User|null $recipient */
                $recipient = User::findOrFail($request->proposer_id);

                $notificationData = new NotificationData(
                    model_type: $walletPayload::class,
                    model_id: $walletPayload->id,
                    view: 'wallets.' . NotificationStatus::REJECTED->value,
                    prev_id: $notification->id,
                    sender_id: Auth::id(),
                    message: __('collection.wallet.wallet_change_rejected'),
                    reason: $request->reason,
                    sender_name: Auth::user()->name . ' ' . Auth::user()->last_name,
                    sender_email: Auth::user()->email,  // Email di chi sta inviando la notifica
                    collection_name: $walletPayload->collection->collection_name,
                    status: NotificationStatus::REJECTED->value
                );

                $handler = $this->notificationFactory->getHandler(NotificationHandlerType::WALLET);
                $handler->handle('rejected', $walletPayload, [
                    'user' => $recipient,
                    'notification_data' => $notificationData
                ]);
            });
        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore durante il rifiuto del wallet:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'notification_id' => $request->notification_id,
                'wallet_payload_id' => $request->wallet_payload_id,
            ]);
            throw $e;
        }
    }

    /**
     * Gestisce il rifiuto di un wallet
     */
    public function expiredWallet(WalletExpireResponse $request): void
    {
        try {
            DB::transaction(function () use ($request) {

                // Recupero la notifica
                $notification = CustomDatabaseNotification::findOrFail($request->notification_id);
                $walletPayload = $notification->model;

                // recupero il wallet del receiver per recuperare i valori delle quote
                $receiverWallet = Wallet::where('collection_id', $walletPayload->collection_id)
                    ->where('user_id', $walletPayload->receiver_id)
                    ->firstOrFail();

                // recuper i valori delle quote
                $oldRoyaltyMint = $receiverWallet->royalty_mint;
                $oldRoyaltyRebind = $receiverWallet->royalty_rebind;

                Log::channel('florenceegi')->info('WalletService:Expiring wallet', [
                    'notification' => $notification,
                    'walletPayload' => $walletPayload,
                ]);

                // Aggiornamento stati
                $notification->update([
                    'outcome' => NotificationStatus::EXPIRED->value,
                    'read_at' => now(),
                ]);
                $walletPayload->update(['status' => NotificationStatus::EXPIRED->value]);

                // Invio notifica
                /** @var User|null $recipient */
                $recipient = User::findOrFail($request->proposer_id);

                /** @var User|null $receiver */
                $receiver = User::findOrFail($walletPayload->receiver_id);

                Log::channel('florenceegi')->info('WalletService:Expiring wallet', [
                    'receiver' => $walletPayload->walletModel,
                ]);

                $notificationData = new NotificationData(
                    model_type: $walletPayload::class,
                    model_id: $walletPayload->id,
                    view: 'wallets.' . NotificationStatus::EXPIRED->value,
                    prev_id: null,
                    sender_id: $walletPayload->receiver_id, // Auth::id(),
                    message: __('collection.wallet.wallet_change_expired'),
                    reason: '',
                    sender_name: $receiver->name . ' ' . $receiver->last_name,
                    sender_email: $receiver->email,  // Email di chi sta inviando la notifica
                    collection_name: $walletPayload->collection->collection_name,
                    status: NotificationStatus::EXPIRED->value,
                    old_royalty_mint: $oldRoyaltyMint,
                    old_royalty_rebind: $oldRoyaltyRebind,
                );

                $handler = $this->notificationFactory->getHandler(NotificationHandlerType::WALLET);
                $handler->handle('expired', $walletPayload, [
                    'user' => $recipient,
                    'notification_data' => $notificationData
                ]);
            });
        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore durante la expired', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'notification_id' => $request->notification_id,
                'wallet_payload_id' => $request->wallet_payload_id,
            ]);
            throw $e;
        }
    }


    /**
     * Aggiorna le quote di un wallet esistente in base alla proposta di modifica.
     *
     * @param array $validation
     * @throws \Exception
     */
    private function updateWallet(array $validation)
    {
        Log::channel('florenceegi')->info('WalletService: Updating wallet', [
            'validation' => (array) $validation,
        ]);

        try {

            // Wallet del receiver
            $receiverWallet = Wallet::where('collection_id', $validation['collection_id'])
                ->where('user_id', $validation['receiver_id'])
                ->firstOrFail();

            // Wallet del creator
            $creatorWallet = Wallet::where('collection_id', $validation['collection_id'])
                ->where('user_id', $validation['proposer_id'])
                ->firstOrFail();

            // Log PRIMA dell'aggiornamento
            Log::channel('florenceegi')->info('WalletService: Before Update', [
                'wallet_before' => $receiverWallet->toArray(),
                'creatorWallet_before' => $creatorWallet->toArray(),
            ]);

            $creatorWallet->update([
                'royalty_mint' => max(0, $creatorWallet->royalty_mint + ($receiverWallet->royalty_mint - ($validation['required_mint_quota'] ?? 0))),
                'royalty_rebind' => max(0, $creatorWallet->royalty_rebind + ($receiverWallet->royalty_rebind - ($validation['required_rebind_quota'] ?? 0))),
            ]);

            $receiverWallet->update([
                'royalty_mint' => $validation['required_mint_quota'] ?? $receiverWallet->royalty_mint,
                'royalty_rebind' => $validation['required_rebind_quota'] ?? $receiverWallet->royalty_rebind,
            ]);

            // Log DOPO aggiornamento
            Log::channel('florenceegi')->info('WalletService: Wallet updated successfully', [
                'updatedWallet' => $receiverWallet->toArray(),
                'updatedCreatorWallet' => $creatorWallet->toArray(),
            ]);
        } catch (Exception $e) {
            Log::channel('florenceegi')->error('❌ WalletService: Errore durante l’aggiornamento del wallet', [
                'error' => $e->getMessage(),
            ]);

            throw $e; // Rilancia l'errore per permettere alla transazione principale di gestirlo
        }
    }

    /**
     * Ottiene lo stato di una notifica
     */
    public function getNotificationStatus(CustomDatabaseNotification $notification): string
    {
        return match ($notification->outcome) {
            'pending_create', 'pending_update', 'pending' => 'pending',
            'Accepted' => 'accepted',
            'Rejected' => 'rejected',
            'expired' => 'expired',
            default => 'unknown',
        };
    }

    /**
     * Ottiene la classe CSS per uno stato di notifica
     */
    public function getNotificationStatusClass(string $status): string
    {
        return match ($status) {
            'pending' => 'text-yellow-500',
            'Accepted' => 'text-green-500',
            'Rejected' => 'text-red-500',
            'expired' => 'text-red-500',
            default => 'text-gray-500',
        };
    }
}
