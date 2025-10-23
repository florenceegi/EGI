<?php

namespace App\Livewire\Collections;

use App\Models\Collection;
use App\Models\CollectionUser;
use App\Models\NotificationPayloadInvitation;
use App\Models\Wallet;
use App\Models\WalletChangeApproval;
use App\Models\NotificationPayloadWallet;
use App\Services\Notifications\InvitationService;
use App\Services\Wallet\WalletProvisioningService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Traits\HasPermissionTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role; // Importiamo i ruoli di Spatie
use App\Enums\UserRoleForInvite; // Importiamo l'enum per i ruoli di invito
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;


#[Layout('layouts.app')]
class CollectionUserMember extends Component {

    use HasPermissionTrait;

    public $collectionUsers; // Lista membri del team
    public $wallets;
    public $collection;
    public $collectionId;
    public $collectionName;
    public $collectionOwner; // Proprietario della collection
    public $walletProposals;
    public $invitationProposal;
    public $show = false; // Proprietà per gestire la visibilità della modale
    public $roles = []; // Ruoli disponibili dalla tabella roles
    public $rolesForInvite = []; // Ruoli specifici per gli inviti
    public $canCreateWallet = true; // Permesso per creare wallet
    public $canCreateTeam = true; // Permesso per creare inviti

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $role = '';

    private InvitationService $invitationService;
    private WalletProvisioningService $walletProvisioningService;

    public function boot(
        InvitationService $invitationService,
        WalletProvisioningService $walletProvisioningService
    ) {
        $this->invitationService = $invitationService;
        $this->walletProvisioningService = $walletProvisioningService;
    }

    public function mount($id) {
        Log::channel('florenceegi')->info('CollectionUserMember: Collection id', [
            'collectionId' => $id
        ]);

        // Verifica se l'utente ha i permessi per visualizzare i membri della collection
        $collection = Collection::findOrFail($id);
        if (!$this->hasPermission($collection, 'view_team')) {
            Log::channel('florenceegi')->error('Utente non autorizzato a visualizzare i membri della collection', [
                'collectionId' => $id
            ]);
            session()->flash('error', __('collection.collaborators.view_denied'));
            return redirect()->route('collections.show', ['id' => $id]);
        }

        // Imposta l'ID della collection
        $this->collectionId = $id;

        // Verifica se l'utente ha i permessi per creare wallet
        $this->canCreateWallet = $this->userHasPermissionInCollection($this->collectionId, 'create_wallet');

        // Verifica se l'utente ha i permessi per creare inviti
        $this->canCreateTeam = $this->userHasPermissionInCollection($this->collectionId, 'create_team');

        // Carica i ruoli disponibili da Spatie
        $this->roles = Role::pluck('name')->toArray(); // Recupera i nomi dei ruoli dalla tabella 'roles'

        // Carica i ruoli specifici per gli inviti dall'enum
        $this->rolesForInvite = UserRoleForInvite::values();

        // Carica la collection e i suoi dati
        $this->loadCollectionData();

        // Carica i collaboratori della collection
        $this->loadTeamUsers();
    }

    public function loadCollectionData() {
        $this->collection = Collection::findOrFail($this->collectionId);

        $this->collectionName = $this->collection->collection_name;

        $this->collectionOwner = $this->collection->creator;
    }

    public function loadTeamUsers() {
        $this->collectionUsers = CollectionUser::where('collection_id', $this->collectionId)->get();

        $this->wallets = Wallet::where('collection_id', '=', $this->collectionId)->get();

        $this->walletProposals = NotificationPayloadWallet::where('collection_id', '=', $this->collectionId)
            ->where('status', 'LIKE', '%pending%')
            ->get();

        $this->invitationProposal = NotificationPayloadInvitation::where('collection_id', '=', $this->collectionId)
            ->where('status', 'LIKE', '%pending%')
            ->get();

        Log::channel('florenceegi')->info('CollectionUserMember: Team users', [
            'collectionUsers' => $this->collectionUsers->count()
        ]);
    }

    public function deleteProposalWallet(Request $request, $id, $walletId) {

        Log::channel('florenceegi')->info('DeleteProposalWallet', [
            'walletId' => $walletId
        ]);

        try {

            $wallet = NotificationPayloadWallet::findOrFail($walletId);
            $notification = $wallet->notifications()->first();

            if (!$wallet) {
                Log::channel('florenceegi')->error('Proposta Wallet non trovata', [
                    'walletId' => $walletId
                ]);
                return response()->json(['message' => 'Proposta Wallet non trovata'], 404);
            }

            $wallet->delete();
            $notification->delete();
            return response()->json(['message' => 'Proposta Wallet eliminata con successo'], 200);
        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore durante l\'eliminazione della proposta wallet', [
                'walletId' => $walletId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Errore durante l\'eliminazione'], 500);
        }
    }

    public function deleteProposalInvitation(Request $request, $id, $invitationId) {
        Log::channel('florenceegi')->info('DeleteProposalInvitation', [
            'invitationId' => $invitationId
        ]);

        try {
            $invitation = NotificationPayloadInvitation::findOrFail($invitationId);
            $collection = Collection::findOrFail($id);

            // Verifica permessi per l'utente autenticato
            if (!$this->hasPermission($collection, 'add_team_member')) {
                Log::channel('florenceegi')->error('Utente non autorizzato a cancellare la proposta di invito', [
                    'collectionId' => $id,
                    'invitationId' => $invitationId
                ]);
                return response()->json(['message' => __('label.unauthorized_action')], 403);
            }

            if (!$invitation) {
                Log::channel('florenceegi')->error('Proposta Invito non trovata', [
                    'invitationId' => $invitationId
                ]);
                return response()->json(['message' => 'Proposta Invito non trovata'], 404);
            }

            $invitation->delete();
            return response()->json(['message' => 'Proposta Invito eliminata con successo'], 200);
        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore durante l\'eliminazione della proposta invito', [
                'invitationId' => $invitationId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Errore durante l\'eliminazione'], 500);
        }
    }

    public function invite() {
        $this->validate();

        try {

            /**
             * @var mixed
             */
            $collection = Collection::findOrFail($this->collectionId);

            // Verifica permessi per l'utente autenticato
            if (!$this->hasPermission($collection, 'add_team_member')) {
                session()->flash('error', __('collection.collaborators.add_denied'));
                return;
            }

            // CONTROLLO PREVENTIVO: Verifica se l'utente è già membro della collezione
            $invitedUser = \App\Models\User::where('email', $this->email)->first();

            if ($invitedUser) {
                // CONTROLLO RUOLO PIATTAFORMA: Verifica se l'utente può essere invitato
                $userRole = $invitedUser->getRoleNames()->first(); // Ottieni il primo ruolo Spatie dell'utente

                if (!UserRoleForInvite::canBeInvited($userRole)) {
                    Log::channel('florenceegi')->warning('Tentativo di invitare utente con ruolo non autorizzato', [
                        'invited_email' => $this->email,
                        'invited_user_id' => $invitedUser->id,
                        'user_role' => $userRole,
                        'allowed_roles' => UserRoleForInvite::allowedPlatformRoles(),
                        'collection_id' => $this->collectionId,
                        'inviter_user_id' => auth()->id()
                    ]);

                    $this->addError('email', __('collection.invitation.unauthorized_role'));
                    return;
                }

                $existingMember = CollectionUser::where('collection_id', $this->collectionId)
                    ->where('user_id', $invitedUser->id)
                    ->first();

                if ($existingMember) {
                    Log::channel('florenceegi')->warning('Tentativo di invitare utente già membro della collezione da CollectionUserMember', [
                        'invited_email' => $this->email,
                        'invited_user_id' => $invitedUser->id,
                        'collection_id' => $this->collectionId,
                        'existing_role' => $existingMember->role,
                        'inviter_user_id' => auth()->id()
                    ]);

                    $this->addError('email', __('collection.invitation.user_already_member'));
                    return;
                }
            } else {
                // Se l'utente non esiste, non possiamo verificare il suo ruolo
                // Puoi decidere se permettere l'invito di nuovi utenti o bloccare
                Log::channel('florenceegi')->warning('Tentativo di invitare email non registrata', [
                    'invited_email' => $this->email,
                    'collection_id' => $this->collectionId,
                    'inviter_user_id' => auth()->id()
                ]);

                $this->addError('email', __('collection.invitation.user_not_found'));
                return;
            }

            $this->invitationService->createInvitation(
                $collection,
                $this->email,
                $this->role
            );

            $this->loadTeamUsers();
            $this->resetFields();
            $this->show = false;
            $this->dispatch('collection-member-updated'); // Aggiorna il genitore

        } catch (Exception $e) {

            Log::channel('florenceegi')->error('Errore invito', [
                'error' => $e->getMessage(),
                'collection_id' => $this->collectionId
            ]);

            $this->addError(name: 'invitation', message: 'Errore durante l\'invio dell\'invito');
        }
    }

    public function openInviteModal() {

        Log::channel('florenceegi')->info('OpenInviteModal', [
            'collectionId' => $this->collectionId
        ]);
        $this->resetFields(); // Pulisce i campi
        $this->show = true; // Mostra la modale
    }

    public function resetFields() {
        $this->email = '';
        $this->role = '';
    }

    public function closeModal() {
        $this->show = false;
    }

    /**
     * Creates a new Algorand wallet for the collection (not necessarily tied to a user).
     * 
     * This method provisions a secure Algorand wallet using WalletProvisioningService
     * with AWS KMS encryption for the mnemonic phrase. The wallet will be associated
     * with the current collection but can exist independently of any user.
     *
     * @return void
     */
    public function createNewWallet() {
        Log::channel('florenceegi')->info('[CollectionUserMember] Create new wallet request', [
            'collection_id' => $this->collectionId,
            'user_id' => Auth::id(),
            'canCreateWallet' => $this->canCreateWallet
        ]);

        try {
            // OS2: Security check - verify user has permission
            if (!$this->canCreateWallet) {
                Log::channel('florenceegi')->warning('[CollectionUserMember] Unauthorized wallet creation attempt', [
                    'collection_id' => $this->collectionId,
                    'user_id' => Auth::id()
                ]);
                
                session()->flash('error', __('collection.wallet.creation_denied'));
                return;
            }

            // OS2: Verify collection exists
            $collection = Collection::findOrFail($this->collectionId);

            Log::channel('florenceegi')->info('[CollectionUserMember] Provisioning new Algorand wallet', [
                'collection_id' => $this->collectionId,
                'collection_name' => $collection->collection_name
            ]);

            // OS3: Provision wallet using WalletProvisioningService
            // Creates Algorand wallet + encrypts mnemonic with AWS KMS + stores in DB
            $wallet = $this->walletProvisioningService->provisionWallet(
                userId: null, // Collection wallet without specific user
                collectionId: $this->collectionId
            );

            Log::channel('florenceegi')->info('[CollectionUserMember] Wallet created successfully', [
                'wallet_id' => $wallet->id,
                'wallet_address' => $wallet->address,
                'collection_id' => $this->collectionId
            ]);

            // Reload wallets to show the new one
            $this->loadTeamUsers();

            // Success feedback with wallet address
            session()->flash('success', __('collection.wallet.created_successfully', [
                'address' => $wallet->address
            ]));

        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('[CollectionUserMember] Wallet creation failed', [
                'collection_id' => $this->collectionId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', __('collection.wallet.creation_failed'));
        }
    }

    public function render() {
        return view('livewire.collections.collection-user-member');
    }
}
