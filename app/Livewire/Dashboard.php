<?php

namespace App\Livewire;

use App\Helpers\FegiAuth;
use App\Models\CustomDatabaseNotification;
use App\Models\User;
use App\Services\Notifications\NotificationHandlerFactory;
use Livewire\Component;
use App\Models\Collection;
use App\Models\CollectionUser;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class Dashboard extends Component {
    public $collectionsCount;
    public $collectionMembersCount;
    public $notifications;
    public $viewingHistoricalNotifications = false;

    public $showHistoricalNotifications = false;

    public $pendingNotifications = [];
    public $historicalNotifications = [];

    public $activeNotificationId = null;
    protected $user_id;

    public function mount() {
        $user = FegiAuth::user();

        $this->user_id = $user?->id; // Salva l'ID utente se esiste, altrimenti sarà null

        // Log lo stato finale dell'utente recuperato in mount
        Log::channel('florenceegi')->info('>>> DASHBOARD USER STATUS IN MOUNT', [
            'user_id' => $user?->id, // Usa l'operatore nullsafe per evitare errori se $user è null
            'auth_type' => FegiAuth::getAuthType(),
            'user_is_null' => $user === null, // Log esplicitamente se $user è null
            'is_strong_auth' => FegiAuth::isStrongAuth(), // Log lo stato del flag forte
            'is_weak_auth' => FegiAuth::isWeakAuth(),     // Log lo stato del flag debole
        ]);

        // *** LOGICA DEL COMPONENTE CHE DIPENDE DALL'UTENTE ***

        // Ora puoi usare la variabile $user e i flag $isStrongAuth o $isWeakAuth
        // per decidere cosa mostrare o caricare.

        // Carica stats e notifiche SOLO se un utente è autenticato (forte o debole)
        if ($user) { // $user conterrà l'istanza User se trovata dalla logica sopra
            // Chiama loadStats passandogli solo l'ID utente
            $this->loadStats(); // <-- Modifica QUI (passa solo l'ID utente)

            // loadNotifications probabilmente ha bisogno almeno dell'ID utente.
            // Assicurati che loadNotifications sia adattato per accettare $user->id se necessario.
            $this->loadNotifications();

            // Se ci sono notifiche pendenti...
            // FIXED: Non selezioniamo automaticamente la prima notifica. L'utente deve cliccare.
            // if (isset($this->pendingNotifications) && $this->pendingNotifications->isNotEmpty()) {
            //     $this->activeNotificationId = $this->pendingNotifications->first()->id;
            // }
        } else {
            // Gestisci il caso ospite (utente è null)
            Log::channel('florenceegi')->info('Dashboard: mount - User is guest, skipping privileged actions.');
            // Inizializza *tutte* le proprietà rilevanti per evitare errori nelle viste.
            $this->collectionsCount = 0;
            $this->collectionMembersCount = 0; // <-- Inizializza la nuova proprietà qui!
            $this->pendingNotifications = collect();
            $this->historicalNotifications = collect();
            $this->activeNotificationId = null;
            // ... potresti voler reindirizzare o mostrare un messaggio ...
        }

        // IMPORTANTISSIMO: Per estrema sicurezza, inizializza le proprietà anche fuori dai blocchi if/else
        // per garantire che esistano sempre per la vista.
        if (!isset($this->collectionsCount)) $this->collectionsCount = 0;
        if (!isset($this->collectionMembersCount)) $this->collectionMembersCount = 0;
        // if (!isset($this->collectionMembersCount)) $this->collectionMembersCount = 0; // Se usi ancora questa property
        if (!isset($this->pendingNotifications)) $this->pendingNotifications = collect();
        if (!isset($this->historicalNotifications)) $this->historicalNotifications = collect();
        if (!isset($this->activeNotificationId)) $this->activeNotificationId = null;
    }

    /**
     * @Oracode Load Dashboard Stats
     * 🎯 Purpose: Load collection count for the user and member count for a specific collection
     * 🧱 Core Logic: Query database based on provided user ID and collection ID
     * @param int $userId The ID of the user (for created collections count)
     * @param int $collectionId The ID of the specific collection to count members for
     */
    public function loadStats() {
        Log::channel('florenceegi')->info('Dashboard: loadStats - Started', ['for_user_id' => $this->user_id]);

        // Conta le collection create da questo utente (Usa $userId)
        $this->collectionsCount = Collection::where('creator_id', $this->user_id)->count();

        // Conta il numero TOTALE di membri UNICI presenti nelle collection create da questo utente.
        // Questa query filtra CollectionUser entries dove:
        // 1. L'entry CollectionUser appartiene a una Collection...
        // 2. ...la quale Collection è stata creata da $userId.
        // 3. Contiamo gli user_id distinti tra questi risultati.
        // Questo conteggio INCLUDE il creatore stesso se è presente nella tabella collection_user
        // per una delle sue collection.

        $userId = $this->user_id; // Assicurati di usare l'ID utente corretto (non posso usare $this->user_id perché la closer non lo riconosce)
        $this->collectionMembersCount = CollectionUser::whereHas('collection', function ($query) use ($userId) {
            // Dentro whereHas, $query si riferisce al builder per il modello Collection.
            $query->where('creator_id', $userId);
        })
            ->distinct('user_id') // <-- Conta solo i user_id unici tra le entry filtrate
            ->count();
    }

    /**
     * Questo metodo gestisce l'evento "proposal-declined" emesso dal metodo decline() del componente DeclineProposalModal.
     *
     * @return void
     */
    #[On('proposal-declined')]
    public function handleProposalDeclined() {
        // Log dell'evento per verifica
        Log::channel('florenceegi')->info('Dashboard: proposal-declined event received.');

        // Ricaricare le notifiche pendenti e storiche
        $this->loadNotifications();

        // Mostrare un messaggio di successo all'utente
        session()->flash('message', __('The proposal was declined successfully and a notification was sent to the proposer.'));
    }

    #[On('proposal-accepted')]
    public function handleProposalAccepted() {
        // Log dell'evento per verifica
        Log::channel('florenceegi')->info('Dashboard: proposal-accepted event received.');

        // Ricaricare le notifiche pendenti e storiche
        $this->loadNotifications();

        // Mostrare un messaggio di successo all'utente
        session()->flash('message', __('The proposal was accepted successfully and a notification was sent to the proposer.'));
    }

    public function openDeclineModal($notification) {
        $this->dispatch('open-decline-modal', $notification);
    }

    public function openAcceptModal($notification) {
        // Log::channel('florenceegi')->info('Dashboard: openAcceptModal', [
        //     'notification' => $notification,
        // ]);

        // il listener si trova in app/Livewire/Proposals/AcceptProposalModal.php
        $this->dispatch('open-accept-modal', $notification);
    }

    // public function notificationArchive($notificationId, $action)
    // {
    //     $notification = Auth::user()->notifications()->findOrFail($notificationId);
    //     $notification->update([
    //         'read_at' => now(),
    //         'outcome' => $action,
    //     ]);

    //     $this->loadNotifications();
    // }
    #[On('deleteNotification')]
    public function deleteNotification($notificationId) {

        $user = FegiAuth::user();

        Log::channel('florenceegi')->info('🗑 deleteNotificationAction() - Deleting notification:', [
            'notificationId' => $notificationId,
        ]);
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->delete();

        $this->loadNotifications();
    }
    #[On('load-notifications')]
    public function loadNotifications() {
        $user = FegiAuth::user();
        // Usa optional() per evitare errori se user è null
        $this->pendingNotifications = optional($user)->customNotifications()
            ?->where(function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('outcome', 'LIKE', '%pending%')
                        ->whereNull('read_at');
                })->orWhere(function ($subQuery) {
                    $subQuery->whereIn('outcome', ['accepted', 'rejected', 'expired'])
                        ->whereNull('read_at');
                });
            })
            ->orderBy('created_at', 'desc')
            ->with('model')
            ->get() ?? collect();

        Log::channel('florenceegi')->info('🔍 loadNotifications() - Pending Notifications:', [
            'pendingNotifications' => $this->pendingNotifications,
            'activeNotificationId' => $this->activeNotificationId,
        ]);

        // Notifiche storiche
        $this->historicalNotifications = optional($user)->customNotifications()
            ?->whereNotNull('read_at')
            ->with('model')
            ->orderBy('read_at', 'desc')
            ->get() ?? collect();

        Log::channel('florenceegi')->info('🔍 loadNotifications() - Historical Notifications:', [
            'historicalNotifications' => $this->historicalNotifications->pluck('id')->toArray(),
        ]);

        Log::channel('florenceegi')->info('🔍 loadNotifications() - Active Notification:', [
            'activeNotification' => $this->historicalNotifications,
        ]);
    }

    public function handleNotificationAction($notificationId, $action) {

        $user = FegiAuth::user();
        Log::channel('florenceegi')->info('🔔 handleNotificationAction() - Handling notification action:', [
            'notificationId' => $notificationId,
            'action' => $action,
        ]);

        // crea il record della notifica corrente, per trovare i dati necessari alla risposta
        $notification = $user->notifications()->findOrFail($notificationId);

        $type = $notification->type;

        $message_to = $notification->data['user_id'];

        $handler = NotificationHandlerFactory::getHandler($type);
        $handler->handle($message_to, $notification, $action);

        $this->loadStats();
        $this->loadNotifications();
    }

    public function toggleHistoricalNotifications() {
        $this->loadNotifications();
        $this->showHistoricalNotifications = !$this->showHistoricalNotifications;
    }

    #[On('setActiveNotification')]
    public function setActiveNotification($id) {
        $this->activeNotificationId = $id;
        // Non ricarichiamo tutto il dataset, cambiamo solo il puntatore attivo.
        // $this->loadNotifications(); 

        Log::channel('florenceegi')->info('🔄 setActiveNotification() - Active Notification Set:', [
            'activeNotificationId' => $this->activeNotificationId,
        ]);

        // Dispatch a Livewire per aggiornare solo il componente della notifica attiva
        $this->dispatch('notification-updated');
    }


    public function getActiveNotification() {
        if (!$this->activeNotificationId) {
            return null;
        }

        // 1. Cerca nella collezione in memoria (Pending)
        $notification = $this->pendingNotifications->firstWhere('id', $this->activeNotificationId);

        if ($notification) {
            Log::channel('florenceegi')->info('🎯 getActiveNotification() - HIT in Pending', [
                'requested_id' => $this->activeNotificationId,
                'found_id' => $notification->id,
                'egi_name' => $notification->data['egi_name'] ?? 'N/A'
            ]);
        }

        // 2. Cerca nella collezione in memoria (Historical)
        if (!$notification) {
            $notification = $this->historicalNotifications->firstWhere('id', $this->activeNotificationId);
        }

        // 3. Fallback DB
        if (!$notification) {
            $user = FegiAuth::user();
            if ($user) {
                $notification = $user->customNotifications()
                    ->with('model')
                    ->find($this->activeNotificationId);
                    
                if ($notification) {
                     Log::channel('florenceegi')->info('💾 getActiveNotification() - FETCHED from DB (Not in memory?)', [
                        'requested_id' => $this->activeNotificationId,
                        'found_id' => $notification->id
                    ]);
                }
            }
        }

        if (!$notification) {
             Log::channel('florenceegi')->warning('⚠️ getActiveNotification() - Notification not found even in DB.', [
                'id' => $this->activeNotificationId
             ]);
             return null;
        }

        // Recupera la vista dal file di configurazione
        $viewKey = $notification->view ?? null;
        $config = $viewKey ? config('notification-views.' . $viewKey, []) : [];
        $view = $config['view'] ?? null;
        $render = $config['render'] ?? 'livewire';

        Log::channel('florenceegi')->info('✅ getActiveNotification() - View retrieved:', [
            'viewKey' => $viewKey,
            'view' => $view,
            'render' => $render,
        ]);

        return $notification;
    }

    /**
     * Listen for real-time notifications from Laravel Echo
     */
    public function getListeners()
    {
        $userId = auth()->id();
        return [
            "echo-private:App.Models.User.{$userId},.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'handleBroadcastNotification',
             // Merge with existing attribute-based listeners handled by Livewire automatically
        ];
    }

    /**
     * Handle incoming real-time notification
     */
    public function handleBroadcastNotification($payload)
    {
        Log::channel('florenceegi')->info('📡 Real-time Notification Received', ['payload' => $payload]);
        
        $this->loadStats();
        $this->loadNotifications();
        
        // FIXED: Non selezioniamo automaticamente la nuova notifica. L'utente deve cliccare.
        // if (!$this->activeNotificationId && isset($this->pendingNotifications) && $this->pendingNotifications->isNotEmpty()) {
        //    $this->activeNotificationId = $this->pendingNotifications->first()->id;
        // }

        $this->dispatch('notification-received'); // Custom event for UI effects
    }

    public function render() {
        return view('livewire.dashboard', [
            'pendingNotifications' => $this->pendingNotifications ?? collect(),
            'historicalNotifications' => $this->historicalNotifications ?? collect(),
        ]);
    }
}