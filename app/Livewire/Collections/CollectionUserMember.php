<?php

namespace App\Livewire\Collections;

use App\Models\Collection;
use App\Models\CollectionUser;
use App\Models\NotificationPayloadInvitation;
use App\Models\Wallet;
use App\Models\WalletChangeApproval;
use App\Models\NotificationPayloadWallet;
use App\Services\Notifications\InvitationService;
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

    public function boot(InvitationService $invitationService) {
        $this->invitationService = $invitationService;
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


    public function render() {
        return view('livewire.collections.collection-user-member');
    }
}
