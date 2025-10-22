<?php

namespace App\Livewire\Notifications\Wallets;

use App\Enums\NotificationStatus;
use App\Enums\PlatformRole;
use App\Enums\WalletStatus;
use App\Models\CollectionUser;
use App\Models\CustomDatabaseNotification;
use App\Models\NotificationPayloadWallet;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Notifications\NotificationHandlerFactory;
use App\Services\Notifications\WalletNotificationHandler;
use App\Services\Wallet\WalletProvisioningService;
use Exception;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Creation extends Component
{
    public $notification;
    protected $userName;
    protected WalletProvisioningService $walletProvisioningService;

    public function boot(WalletProvisioningService $walletProvisioningService)
    {
        $this->walletProvisioningService = $walletProvisioningService;
    }

    public function mount($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Gestisce la risposta a una notifica di proposta di creazione di un nuovo wallet (accettazione o rifiuto).
     *
     * Questo metodo:
     * - Recupera la notifica associata alla proposta e il payload corrispondente.
     * - Esegue la logica di accettazione o rifiuto in base all'opzione scelta dall'utente.
     * - Aggiorna lo stato della notifica e invia una notifica di aggiornamento al proponente.
     * - Utilizza una transazione per garantire l'integrità dei dati.
     *
     * @param string $option L'opzione scelta dall'utente: 'accept' o 'reject'.
     *
     * @throws \Exception Se si verifica un errore durante la transazione o l'elaborazione della risposta.
     *
     * @return void
     *
     * Dettagli tecnici:
     * - La transazione garantisce che tutte le modifiche al database siano atomiche.
     * - In caso di errore, tutte le operazioni vengono annullate e l'eccezione è loggata.
     * - Il metodo utilizza la factory NotificationHandlerFactory per gestire l'invio della notifica.
     */
    #[On('response')]
    public function response($option)
    {

        try {
            // Inizio della transazione
            DB::beginTransaction();

            // L'utente che ha proposto il wallet
            $proposer_id = $this->notification->model->proposer_id ?? null;
            $prev_id = $this->notification->id; // L'id di notification appartiene alla notifica di creazione, qui stiamo creando una notifica di accettazione e dobbiamo passare l'id della notifica di creazione per poterne aggiornare lo stato

            Log::channel('florenceegi')->info('WalletResponse: response PRIMA', [
                'notification->id' => $this->notification->id,
            ]);

            Log::channel('florenceegi')->info('WalletResponse: ', [
                'proposer_id' => $proposer_id,
            ]);

            // Si crea l'oggetto User da usare per inviare la notifica
            $message_to = User::find($proposer_id);

            if (!$message_to) {
                throw new Exception('Utente non trovato.');
            }

            $this->userName = Auth::user()->name . ' ' . Auth::user()->last_name;

            // Si recupera l'oggetto NotificationPayloadWallet creato al momento dell'invio della proposta
            $notificationPayloadWallet = NotificationPayloadWallet::find($this->notification->model_id);

            // Accetta o rifiuta l'invito
            if ($option === 'accepted') {
                $this->accept($notificationPayloadWallet);
                // Se accetta non occorre creare una notifica di risposta
            } else {
                $this->reject($notificationPayloadWallet);

                $notificationPayloadWallet['proposer_name'] = Auth::user()->name . ' ' . Auth::user()->last_name; // Nome di chi fa la proposta
                $notificationPayloadWallet['model_id'] = $notificationPayloadWallet->id;
                $notificationPayloadWallet['model_type'] = get_class($notificationPayloadWallet);
                $notificationPayloadWallet['message'] = __('collection.wallet.wallet_change_rejected');
                $notificationPayloadWallet['view'] = 'wallets.' . NotificationStatus::ACCEPTED->value; // La vista da mostrare
                $notificationPayloadWallet['prev_id'] = $prev_id;

                // Invia la notifica
                $handler = NotificationHandlerFactory::getHandler(WalletNotificationHandler::class);
                $handler->handle($message_to, $notificationPayloadWallet);

                Log::channel('florenceegi')->info('WalletResponse: response DOPO', [
                    'notification->id' => $this->notification->id,
                ]);
            }

            // Conferma la transazione
            DB::commit();

            Log::channel('florenceegi')->info('Transazione completata con successo per la risposta alla creazione del wallet.');

            // Dispatcha un evento di successo al frontend
            // $this->dispatch('notification-response', option: $option, success: true);

        } catch (Exception $e) {
            // Annulla la transazione in caso di errore
            DB::rollBack();

            // Log dell'errore
            Log::channel('florenceegi')->error('Errore durante la gestione della risposta alla creazione del wallet:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Rilancia l'eccezione per gestirla altrove, se necessario
            throw $e;
        }
    }

    public function accept($notificationPayloadWallet)
    {

        Log::channel('florenceegi')->info('WalletAccepted:accept', [
            'notificationPayloadWallet' => $notificationPayloadWallet,
        ]);

        // Validazione preliminare del payload
        if (!$notificationPayloadWallet || !$notificationPayloadWallet->collection_id) {
            throw new Exception('Dati mancanti nel payload dell\'invito.');
        }

        try {

            // Aggiorna lo stato dalla proposta come approvata
            $notificationPayloadWallet->handleAccepted();

            log::channel('florenceegi')->info('WalletAccepted:handleAccepted', [
                'notificationPayloadWallet' => $notificationPayloadWallet->status,
            ]);

            $this->AjustCreatorQuota($notificationPayloadWallet);
        } catch (Exception $e) {
            Log::error('Errore durante l\'accettazione della creazione di un wallet:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Rilancia l'eccezione per una gestione ulteriore
        }
    }
    #[On('archive')]
    public function archive($id)
    {
        Log::channel('florenceegi')->info('archive', [
            'notification' => $id,
        ]);

        $notification = CustomDatabaseNotification::find($id);

        $notification->update([
            'read_at' => now(),
        ]);

        // $this->dispatch('load-notifications');

    }

    public function AjustCreatorQuota($notificationPayloadWallet)
    {

        $collection_id = $notificationPayloadWallet->collection_id;
        $receiver_id = $notificationPayloadWallet->receiver_id;
        $proposer_id = $notificationPayloadWallet->proposer_id;
        $newMint = $notificationPayloadWallet->royalty_mint;
        $newRebind = $notificationPayloadWallet->royalty_rebind;
        $wallet = $notificationPayloadWallet->wallet;

        Log::channel('florenceegi')->info('validateAndAdjustCreatorQuota', [
            'collection_id' => $collection_id,
            'receiver_id' => $receiver_id,
            'newMint' => $newMint,
            'newRebind' => $newRebind,
            'wallet' => $wallet,
        ]);

        // 🔗 NUOVO: Verifica e crea wallet Algorand reale se necessario
        $receiver = User::findOrFail($receiver_id);
        $realWalletAddress = $wallet; // Default: usa il wallet dalla proposta

        // Se l'utente non ha un wallet reale o è PENDING, crealo
        if (!$receiver->wallet || $receiver->wallet === 'PENDING' || str_starts_with($receiver->wallet, 'pending_wallet_')) {
            try {
                Log::channel('florenceegi')->info('User does not have real Algorand wallet, creating one', [
                    'user_id' => $receiver_id,
                    'current_wallet' => $receiver->wallet,
                ]);

                // Crea il wallet Algorand con encryption (senza IBAN per ora)
                $algorandWallet = $this->walletProvisioningService->provisionUserWallet($receiver, [
                    'iban' => null,
                    'wallet_passphrase' => null,
                    'accept_custody_seed' => true,
                ]);

                // Aggiorna il wallet address reale
                $realWalletAddress = $algorandWallet->wallet;

                // Aggiorna il wallet dell'utente nella tabella users
                $receiver->update(['wallet' => $realWalletAddress]);

                Log::channel('florenceegi')->info('Real Algorand wallet created for user', [
                    'user_id' => $receiver_id,
                    'wallet_address' => $realWalletAddress,
                ]);
            } catch (Exception $e) {
                Log::channel('florenceegi')->error('Failed to create real Algorand wallet', [
                    'user_id' => $receiver_id,
                    'error' => $e->getMessage(),
                ]);

                // Se fallisce, usa il wallet dalla proposta (fallback al comportamento precedente)
                // Questo evita di bloccare tutto il flusso se c'è un problema con AWS KMS
            }
        } else {
            // L'utente ha già un wallet reale, usalo
            $realWalletAddress = $receiver->wallet;

            Log::channel('florenceegi')->info('User already has real Algorand wallet', [
                'user_id' => $receiver_id,
                'wallet_address' => $realWalletAddress,
            ]);
        }

        // Recupera il wallet del creator per poter aggiornare le quote di mint e rebind
        $creatorWallet = Wallet::where('collection_id', $collection_id)
            ->where('user_id', $proposer_id)
            ->first();

        // Riduci la quota del creator
        $creatorWallet->update([
            'royalty_mint' => $creatorWallet->royalty_mint - $newMint,
            'royalty_rebind' => $creatorWallet->royalty_rebind - $newRebind,
        ]);

        $this->notification->update([
            'outcome' => NotificationStatus::ACCEPTED->value,
        ]);

        // Crea il record wallet con il wallet address reale
        Wallet::create([
            'collection_id' => $collection_id,
            'user_id' => $receiver_id,
            'wallet' => $realWalletAddress, // 🔗 USA IL WALLET REALE
            'royalty_mint' => $newMint,
            'royalty_rebind' => $newRebind,
            'platform_role' => null, // Membri collection non hanno platform_role specifico
        ]);

        // $this->dispatch('load-notifications');

        // Creazione del nuovo wallet
        return;
    }

    public function reject($notificationPayloadWallet)
    {

        $user = Auth::user();
        $receiverName = $user->name . ' ' . $user->last_name;
        $collectionName = $this->notification->data['collection_name'] ?? null;


        // Validazione preliminare dei parametri
        if (!$notificationPayloadWallet || !$collectionName || !$receiverName) {
            throw new Exception('Parametri mancanti o non validi per la gestione del rifiuto.');
        }

        try {

            // Aggiorna lo stato dell'invito come rifiutato
            $notificationPayloadWallet->handleRejection();

            // Verifica che lo stato sia stato aggiornato correttamente
            // if (!$notificationPayloadWallet->isRejected()) { // Metodo ipotetico
            //     throw new Exception('Errore durante l\'aggiornamento dello stato dell\'invito.');
            // }

            // Aggiorna lo stato della notifica
            $this->notification['status'] = NotificationStatus::REJECTED->value;
            $this->notification['view'] = 'wallets.' . NotificationStatus::REJECTED->value;
            $this->notification['collection_name'] = $collectionName;
            $this->notification['receiver_name'] = $receiverName;
            $this->notification['receiver_id'] = $user->id;

            // Aggiungi un messaggio personalizzato
            $this->notification['message'] = __('collection.wallet.wallet_change_rejected');

            // Log dell'operazione
            Log::info('Proposta creazione wallet rifiutata', [
                'notification_id' => $this->notification['id'] ?? null,
                'collection_name' => $collectionName,
                'receiver_name' => $receiverName,
            ]);
        } catch (Exception $e) {
            // Log dell'errore
            Log::error('Errore durante il rifiuto della proposta di creazione di un nuovo wallet:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Rilancia l'eccezione per una gestione ulteriore
            throw $e;
        }
    }

    public function render()
    {
        Log::channel('florenceegi')->info('WalletsCreation:render');
        return view('livewire.notifications.wallets.creation');
    }
}
