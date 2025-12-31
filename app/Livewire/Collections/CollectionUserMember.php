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
use App\Services\Blockchain\AlgorandClient;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Traits\HasPermissionTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role; // Importiamo i ruoli di Spatie
use App\Enums\UserRoleForInvite; // Importiamo l'enum per i ruoli di invito
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;


#[Layout('layouts.app')]
class CollectionUserMember extends Component
{

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
    public array $availableWalletTypes = []; // Tipi wallet disponibili in base a payment methods

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $role = '';

    // External Wallet Form Properties
    public bool $showExternalWalletModal = false;
    public string $externalWalletAddress = '';
    public string $externalWalletName = '';
    public float $externalWalletRoyaltyMint = 0;
    public float $externalWalletRoyaltyRebind = 0;

    // Wallet Type Selection Modal
    public bool $showWalletTypeModal = false;
    public string $selectedWalletType = '';

    // Stripe Wallet Form Properties
    public bool $showStripeWalletModal = false;
    public string $stripeMode = 'choose'; // 'choose', 'onboarding', 'existing'
    public string $stripeAccountId = ''; // For existing account
    public string $stripeAccountName = '';
    public float $stripeRoyaltyMint = 0;
    public float $stripeRoyaltyRebind = 0;
    public ?string $stripeOnboardingUrl = null; // Generated onboarding link

    // PayPal Wallet Form Properties
    public bool $showPaypalWalletModal = false;
    public string $paypalEmail = '';
    public string $paypalMerchantId = '';
    public float $paypalRoyaltyMint = 0;
    public float $paypalRoyaltyRebind = 0;

    // IBAN Wallet Form Properties
    public bool $showIbanWalletModal = false;
    public string $ibanNumber = '';
    public string $ibanBankName = '';
    public string $ibanAccountHolder = '';
    public string $ibanSwiftBic = '';
    public float $ibanRoyaltyMint = 0;
    public float $ibanRoyaltyRebind = 0;

    private InvitationService $invitationService;
    private WalletProvisioningService $walletProvisioningService;
    private AlgorandClient $algorandClient;
    private AuditLogService $auditService;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    private \App\Services\Payment\StripeConnectService $stripeConnectService;

    public function boot(
        InvitationService $invitationService,
        WalletProvisioningService $walletProvisioningService,
        AlgorandClient $algorandClient,
        AuditLogService $auditService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        \App\Services\Payment\StripeConnectService $stripeConnectService
    ) {
        $this->invitationService = $invitationService;
        $this->walletProvisioningService = $walletProvisioningService;
        $this->algorandClient = $algorandClient;
        $this->auditService = $auditService;
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->stripeConnectService = $stripeConnectService;
    }

    public function mount($id)
    {
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
        
        // Carica i tipi wallet disponibili in base ai payment methods
        $this->loadAvailableWalletTypes();
    }

    public function loadCollectionData()
    {
        $this->collection = Collection::findOrFail($this->collectionId);

        $this->collectionName = $this->collection->collection_name;

        $this->collectionOwner = $this->collection->creator;
    }

    public function loadTeamUsers()
    {
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

    /**
     * Load available wallet types based on collection's enabled payment methods.
     * 
     * Maps payment methods to wallet types:
     * - stripe -> stripe (Stripe Connect account)
     * - paypal -> paypal (PayPal Merchant ID)
     * - iban -> iban (Bank transfer)
     * - algorand -> algorand, algorand_external (Blockchain wallet)
     */
    public function loadAvailableWalletTypes(): void
    {
        $collection = Collection::find($this->collectionId);
        
        if (!$collection) {
            $this->availableWalletTypes = [];
            return;
        }

        // Algorand wallets are ALWAYS available - they use blockchain, not fiat PSPs
        $this->availableWalletTypes = [
            'algorand' => [
                'label' => __('collection.wallet.types.algorand'),
                'description' => __('collection.wallet.types.algorand_desc'),
                'icon' => 'wallet'
            ],
            'algorand_external' => [
                'label' => __('collection.wallet.types.algorand_external'),
                'description' => __('collection.wallet.types.algorand_external_desc'),
                'icon' => 'link'
            ]
        ];

        // Get enabled payment methods for fiat wallets (Stripe, PayPal, IBAN)
        $enabledMethods = $collection->paymentMethods()
            ->where('is_enabled', true)
            ->pluck('method')
            ->toArray();

        Log::channel('florenceegi')->debug('[CollectionUserMember] Enabled payment methods', [
            'collection_id' => $this->collectionId,
            'enabled_methods' => $enabledMethods
        ]);

        // Add fiat wallet types only if their payment methods are configured
        $fiatWalletTypes = $this->mapPaymentMethodsToWalletTypes($enabledMethods);
        $this->availableWalletTypes = array_merge($this->availableWalletTypes, $fiatWalletTypes);

        Log::channel('florenceegi')->info('[CollectionUserMember] Available wallet types loaded', [
            'collection_id' => $this->collectionId,
            'available_types' => array_keys($this->availableWalletTypes)
        ]);
    }

    /**
     * Map payment methods to supported wallet types.
     * 
     * @param array $methods Enabled payment method names
     * @return array Available wallet types with labels
     */
    protected function mapPaymentMethodsToWalletTypes(array $methods): array
    {
        $walletTypes = [];

        foreach ($methods as $method) {
            switch ($method) {
                case 'stripe':
                    $walletTypes['stripe'] = [
                        'label' => __('collection.wallet.types.stripe'),
                        'description' => __('collection.wallet.types.stripe_desc'),
                        'icon' => 'credit_card'
                    ];
                    break;

                case 'paypal':
                    $walletTypes['paypal'] = [
                        'label' => __('collection.wallet.types.paypal'),
                        'description' => __('collection.wallet.types.paypal_desc'),
                        'icon' => 'paypal'
                    ];
                    break;

                case 'iban':
                    $walletTypes['iban'] = [
                        'label' => __('collection.wallet.types.iban'),
                        'description' => __('collection.wallet.types.iban_desc'),
                        'icon' => 'account_balance'
                    ];
                    break;

                case 'algorand':
                case 'crypto':
                    // Algorand supports both custodial and non-custodial wallets
                    $walletTypes['algorand'] = [
                        'label' => __('collection.wallet.types.algorand'),
                        'description' => __('collection.wallet.types.algorand_desc'),
                        'icon' => 'wallet'
                    ];
                    $walletTypes['algorand_external'] = [
                        'label' => __('collection.wallet.types.algorand_external'),
                        'description' => __('collection.wallet.types.algorand_external_desc'),
                        'icon' => 'link'
                    ];
                    break;
            }
        }

        return $walletTypes;
    }

    public function deleteProposalWallet(Request $request, $id, $walletId)
    {

        Log::channel('florenceegi')->info('DeleteProposalWallet', [
            'walletId' => $walletId
        ]);

        try {
            // Recupero sicuro; se non esiste, gestito dal catch specifico
            $wallet = NotificationPayloadWallet::findOrFail($walletId);
            $notification = $wallet->notifications()->first();

            // Elimina risorse collegate con controlli di esistenza
            $wallet->delete();
            if ($notification) {
                $notification->delete();
            }

            return response()->json(['message' => 'Proposta Wallet eliminata con successo'], 200);
        } catch (ModelNotFoundException $e) {
            // UEM-FIRST: wallet non trovato
            $this->errorManager->handle('WALLET_NOT_FOUND', [
                'wallet_id' => $walletId,
                'route' => 'collections.members.delete_proposal_wallet'
            ], $e);

            return response()->json(['message' => 'Proposta Wallet non trovata'], 404);
        } catch (Exception $e) {
            // UEM-FIRST: altri errori applicativi
            $this->errorManager->handle('WALLET_DELETE_ERROR', [
                'wallet_id' => $walletId,
                'error' => $e->getMessage()
            ], $e);

            return response()->json(['message' => 'Errore durante l\'eliminazione'], 500);
        }
    }

    public function deleteProposalInvitation(Request $request, $id, $invitationId)
    {
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

    public function invite()
    {
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

    public function openInviteModal()
    {

        Log::channel('florenceegi')->info('OpenInviteModal', [
            'collectionId' => $this->collectionId
        ]);
        $this->resetFields(); // Pulisce i campi
        $this->show = true; // Mostra la modale
    }

    public function resetFields()
    {
        $this->email = '';
        $this->role = '';
    }

    public function closeModal()
    {
        $this->show = false;
    }

    /**
     * Open wallet type selection modal
     */
    public function openWalletTypeModal()
    {
        $this->selectedWalletType = '';
        $this->showWalletTypeModal = true;
        
        Log::channel('florenceegi')->info('[CollectionUserMember] Wallet type selection modal opened', [
            'collection_id' => $this->collectionId,
            'user_id' => Auth::id(),
            'available_types' => array_keys($this->availableWalletTypes)
        ]);
    }

    /**
     * Close wallet type selection modal
     */
    public function closeWalletTypeModal()
    {
        $this->showWalletTypeModal = false;
        $this->selectedWalletType = '';
    }

    /**
     * Handle wallet type selection and proceed with creation
     */
    public function selectWalletType(string $type)
    {
        Log::channel('florenceegi')->info('[CollectionUserMember] Wallet type selected', [
            'collection_id' => $this->collectionId,
            'user_id' => Auth::id(),
            'selected_type' => $type
        ]);

        $this->selectedWalletType = $type;
        $this->showWalletTypeModal = false;

        // Route to appropriate action based on wallet type
        switch ($type) {
            case 'algorand':
                // Custodial Algorand wallet - create with KMS
                $this->createNewWallet();
                break;

            case 'algorand_external':
                // Non-custodial external Algorand wallet - open form modal
                $this->openExternalWalletModal();
                break;

            case 'stripe':
                $this->openStripeWalletModal();
                break;

            case 'paypal':
                $this->openPaypalWalletModal();
                break;

            case 'iban':
                $this->openIbanWalletModal();
                break;

            default:
                session()->flash('error', __('collection.wallet.invalid_type'));
                break;
        }
    }

    /**
     * Open modal to add external Algorand wallet
     */
    public function openExternalWalletModal()
    {
        // ULM: Log modal open
        $this->logger->info('[CollectionUserMember] Opening external wallet modal', [
            'collection_id' => $this->collectionId,
            'user_id' => Auth::id()
        ]);

        $this->resetExternalWalletFields();
        $this->showExternalWalletModal = true;
    }

    /**
     * Close external wallet modal
     */
    public function closeExternalWalletModal()
    {
        $this->showExternalWalletModal = false;
        $this->resetExternalWalletFields();
    }

    /**
     * Reset external wallet form fields
     */
    protected function resetExternalWalletFields()
    {
        $this->externalWalletAddress = '';
        $this->externalWalletName = '';
        $this->externalWalletRoyaltyMint = 0;
        $this->externalWalletRoyaltyRebind = 0;
        $this->resetErrorBag();
    }

    // ========================================
    // STRIPE WALLET METHODS
    // ========================================

    public function openStripeWalletModal()
    {
        $this->resetStripeWalletFields();
        $this->showStripeWalletModal = true;
        $this->stripeMode = 'choose'; // Start with choice screen
        
        Log::channel('florenceegi')->info('[CollectionUserMember] Stripe wallet modal opened', [
            'collection_id' => $this->collectionId,
            'user_id' => Auth::id()
        ]);
    }

    public function closeStripeWalletModal()
    {
        $this->showStripeWalletModal = false;
        $this->resetStripeWalletFields();
    }

    protected function resetStripeWalletFields()
    {
        $this->stripeMode = 'choose';
        $this->stripeAccountId = '';
        $this->stripeAccountName = '';
        $this->stripeRoyaltyMint = 0;
        $this->stripeRoyaltyRebind = 0;
        $this->stripeOnboardingUrl = null;
        $this->resetErrorBag();
    }

    /**
     * Set the Stripe connection mode
     */
    public function setStripeMode(string $mode)
    {
        $this->stripeMode = $mode;
        
        Log::channel('florenceegi')->info('[CollectionUserMember] Stripe mode selected', [
            'collection_id' => $this->collectionId,
            'mode' => $mode
        ]);
    }

    /**
     * Initiate Stripe Express onboarding for NEW users
     * Creates a connected Express account and generates onboarding link
     */
    public function initiateStripeOnboarding()
    {
        $logContext = [
            'collection_id' => $this->collectionId,
            'user_id' => Auth::id(),
            'account_name' => $this->stripeAccountName,
            'royalty_mint' => $this->stripeRoyaltyMint,
            'royalty_rebind' => $this->stripeRoyaltyRebind
        ];

        Log::channel('florenceegi')->info('[CollectionUserMember] Initiating Stripe Express onboarding', $logContext);

        try {
            // Validate royalties
            if ($this->stripeRoyaltyMint < 0 || $this->stripeRoyaltyMint > 100) {
                throw new \Exception(__('collection.wallet.validation.mint_invalid'));
            }
            if ($this->stripeRoyaltyRebind < 0 || $this->stripeRoyaltyRebind > 100) {
                throw new \Exception(__('collection.wallet.validation.rebind_invalid'));
            }

            $user = Auth::user();
            $collection = Collection::find($this->collectionId);
            
            // Variables to capture from transaction
            $onboardingUrl = null;
            $stripeAccountId = null;

            DB::transaction(function () use ($logContext, $user, $collection, &$onboardingUrl, &$stripeAccountId) {
                // Verify creator quota
                $creatorWallet = $this->getCreatorWalletAndVerifyQuota($logContext);
                $this->subtractCreatorQuota($creatorWallet, $logContext);

                // Create wallet record first (will be linked to Stripe later)
                $wallet = Wallet::create([
                    'collection_id' => $this->collectionId,
                    'user_id' => Auth::id(),
                    'wallet' => 'stripe_pending_' . uniqid(),
                    'wallet_type' => 'stripe',
                    'royalty_mint' => $this->stripeRoyaltyMint,
                    'royalty_rebind' => $this->stripeRoyaltyRebind,
                    'platform_role' => null,
                    'is_anonymous' => false,
                    'version' => 1,
                    'metadata' => [
                        'account_name' => $this->stripeAccountName ?: 'Stripe Account',
                        'stripe_status' => 'pending_onboarding',
                        'created_via' => 'stripe_express_onboarding',
                        'created_by_user_id' => Auth::id(),
                        'added_at' => now()->toISOString()
                    ]
                ]);

                // Create Express account via StripeConnectService
                $result = $this->stripeConnectService->ensureExpressAccount($wallet, $user);
                $stripeAccountId = $result['account']['id'];

                // Update wallet with Stripe account ID
                $wallet->update([
                    'stripe_account_id' => $stripeAccountId,
                    'wallet' => 'stripe_' . $stripeAccountId,
                    'metadata' => array_merge($wallet->metadata ?? [], [
                        'stripe_status' => 'onboarding_required',
                        'stripe_account_id' => $stripeAccountId
                    ])
                ]);

                // Generate onboarding link
                $returnUrl = url('/collections/' . $collection->id . '/edit') . '?stripe_onboarding=complete';
                $refreshUrl = url('/collections/' . $collection->id . '/edit') . '?stripe_onboarding=refresh';
                $onboardingUrl = $this->stripeConnectService->createAccountLink(
                    $stripeAccountId,
                    $returnUrl,
                    $refreshUrl
                );

                // GDPR Audit
                $this->auditService->logUserAction(
                    $user,
                    'stripe_express_account_created',
                    [
                        'wallet_id' => $wallet->id,
                        'collection_id' => $this->collectionId,
                        'stripe_account_id' => $stripeAccountId
                    ],
                    GdprActivityCategory::WALLET_CREATED
                );
            });

            // Set the onboarding URL OUTSIDE the transaction closure
            $this->stripeOnboardingUrl = $onboardingUrl;
            
            Log::channel('florenceegi')->info('[CollectionUserMember] Stripe onboarding URL generated', [
                'collection_id' => $this->collectionId,
                'stripe_account_id' => $stripeAccountId,
                'has_url' => !empty($onboardingUrl)
            ]);

            $this->loadTeamUsers();
            session()->flash('success', __('collection.wallet.stripe.onboarding_started'));

        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('[CollectionUserMember] Stripe onboarding failed', [
                ...$logContext,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', $e->getMessage());
        }
    }

    /**
     * Link an existing Stripe Connect account
     * For users who already have their own Stripe account
     */
    public function linkExistingStripeAccount()
    {
        $logContext = [
            'collection_id' => $this->collectionId,
            'user_id' => Auth::id(),
            'stripe_account_id' => $this->stripeAccountId,
            'royalty_mint' => $this->stripeRoyaltyMint,
            'royalty_rebind' => $this->stripeRoyaltyRebind
        ];

        Log::channel('florenceegi')->info('[CollectionUserMember] Linking existing Stripe account', $logContext);

        try {
            // Validate account ID format
            if (empty($this->stripeAccountId) || !str_starts_with($this->stripeAccountId, 'acct_')) {
                throw new \Exception(__('collection.wallet.stripe.account_id_invalid'));
            }
            if ($this->stripeRoyaltyMint < 0 || $this->stripeRoyaltyMint > 100) {
                throw new \Exception(__('collection.wallet.validation.mint_invalid'));
            }
            if ($this->stripeRoyaltyRebind < 0 || $this->stripeRoyaltyRebind > 100) {
                throw new \Exception(__('collection.wallet.validation.rebind_invalid'));
            }

            // Verify account exists in Stripe
            $stripeAccount = $this->stripeConnectService->retrieveAccount($this->stripeAccountId);
            if (!$stripeAccount) {
                throw new \Exception(__('collection.wallet.stripe.account_not_found'));
            }

            $user = Auth::user();

            DB::transaction(function () use ($logContext, $user, $stripeAccount) {
                // Verify creator quota
                $creatorWallet = $this->getCreatorWalletAndVerifyQuota($logContext);
                $this->subtractCreatorQuota($creatorWallet, $logContext);

                // Create wallet with linked Stripe account
                $wallet = Wallet::create([
                    'collection_id' => $this->collectionId,
                    'user_id' => Auth::id(),
                    'wallet' => 'stripe_' . $this->stripeAccountId,
                    'wallet_type' => 'stripe',
                    'stripe_account_id' => $this->stripeAccountId,
                    'royalty_mint' => $this->stripeRoyaltyMint,
                    'royalty_rebind' => $this->stripeRoyaltyRebind,
                    'platform_role' => null,
                    'is_anonymous' => false,
                    'version' => 1,
                    'metadata' => [
                        'account_name' => $this->stripeAccountName ?: ($stripeAccount['business_profile']['name'] ?? 'Linked Stripe Account'),
                        'stripe_status' => $stripeAccount['charges_enabled'] ? 'active' : 'pending_verification',
                        'stripe_account_id' => $this->stripeAccountId,
                        'created_via' => 'stripe_existing_link',
                        'created_by_user_id' => Auth::id(),
                        'added_at' => now()->toISOString()
                    ]
                ]);

                // Also update user's stripe_account_id if not set
                if (empty($user->stripe_account_id)) {
                    $user->update(['stripe_account_id' => $this->stripeAccountId]);
                }

                // GDPR Audit
                $this->auditService->logUserAction(
                    $user,
                    'stripe_existing_account_linked',
                    [
                        'wallet_id' => $wallet->id,
                        'collection_id' => $this->collectionId,
                        'stripe_account_id' => $this->stripeAccountId
                    ],
                    GdprActivityCategory::WALLET_CREATED
                );
            });

            session()->flash('success', __('collection.wallet.stripe.linked_successfully'));
            $this->loadTeamUsers();
            $this->closeStripeWalletModal();

        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('[CollectionUserMember] Stripe account linking failed', [
                ...$logContext,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', $e->getMessage());
        }
    }

    /**
     * Legacy method - redirect to appropriate new method
     */
    public function createStripeWallet()
    {
        if ($this->stripeMode === 'onboarding') {
            $this->initiateStripeOnboarding();
        } elseif ($this->stripeMode === 'existing') {
            $this->linkExistingStripeAccount();
        } else {
            session()->flash('error', __('collection.wallet.stripe.select_mode'));
        }
    }

    // ========================================
    // PAYPAL WALLET METHODS
    // ========================================

    public function openPaypalWalletModal()
    {
        $this->resetPaypalWalletFields();
        $this->showPaypalWalletModal = true;
        
        Log::channel('florenceegi')->info('[CollectionUserMember] PayPal wallet modal opened', [
            'collection_id' => $this->collectionId,
            'user_id' => Auth::id()
        ]);
    }

    public function closePaypalWalletModal()
    {
        $this->showPaypalWalletModal = false;
        $this->resetPaypalWalletFields();
    }

    protected function resetPaypalWalletFields()
    {
        $this->paypalEmail = '';
        $this->paypalMerchantId = '';
        $this->paypalRoyaltyMint = 0;
        $this->paypalRoyaltyRebind = 0;
        $this->resetErrorBag();
    }

    public function createPaypalWallet()
    {
        $logContext = [
            'collection_id' => $this->collectionId,
            'user_id' => Auth::id(),
            'paypal_email' => $this->paypalEmail,
            'merchant_id' => $this->paypalMerchantId,
            'royalty_mint' => $this->paypalRoyaltyMint,
            'royalty_rebind' => $this->paypalRoyaltyRebind
        ];

        Log::channel('florenceegi')->info('[CollectionUserMember] Creating PayPal wallet', $logContext);

        try {
            // Validate
            if (empty($this->paypalEmail) || !filter_var($this->paypalEmail, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception(__('collection.wallet.paypal.email_invalid'));
            }
            if ($this->paypalRoyaltyMint < 0 || $this->paypalRoyaltyMint > 100) {
                throw new \Exception(__('collection.wallet.validation.mint_invalid'));
            }
            if ($this->paypalRoyaltyRebind < 0 || $this->paypalRoyaltyRebind > 100) {
                throw new \Exception(__('collection.wallet.validation.rebind_invalid'));
            }

            DB::transaction(function () use ($logContext) {
                $creatorWallet = $this->getCreatorWalletAndVerifyQuota($logContext);
                $this->subtractCreatorQuota($creatorWallet, $logContext);

                $wallet = Wallet::create([
                    'collection_id' => $this->collectionId,
                    'user_id' => Auth::id(),
                    'wallet' => 'paypal_' . uniqid(),
                    'wallet_type' => 'paypal',
                    'royalty_mint' => $this->paypalRoyaltyMint,
                    'royalty_rebind' => $this->paypalRoyaltyRebind,
                    'platform_role' => null,
                    'is_anonymous' => false,
                    'version' => 1,
                    'metadata' => [
                        'paypal_email' => $this->paypalEmail,
                        'merchant_id' => $this->paypalMerchantId ?: null,
                        'created_via' => 'paypal_wallet_add',
                        'created_by_user_id' => Auth::id(),
                        'added_at' => now()->toISOString()
                    ]
                ]);

                $this->auditService->logUserAction(
                    Auth::user(),
                    'paypal_wallet_added_to_collection',
                    [
                        'wallet_id' => $wallet->id,
                        'collection_id' => $this->collectionId,
                        'paypal_email' => $this->paypalEmail
                    ],
                    GdprActivityCategory::WALLET_CREATED
                );
            });

            session()->flash('success', __('collection.wallet.paypal.created_successfully'));
            $this->loadTeamUsers();
            $this->closePaypalWalletModal();

        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('[CollectionUserMember] PayPal wallet creation failed', [
                ...$logContext,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', $e->getMessage());
        }
    }

    // ========================================
    // IBAN WALLET METHODS
    // ========================================

    public function openIbanWalletModal()
    {
        $this->resetIbanWalletFields();
        $this->showIbanWalletModal = true;
        
        Log::channel('florenceegi')->info('[CollectionUserMember] IBAN wallet modal opened', [
            'collection_id' => $this->collectionId,
            'user_id' => Auth::id()
        ]);
    }

    public function closeIbanWalletModal()
    {
        $this->showIbanWalletModal = false;
        $this->resetIbanWalletFields();
    }

    protected function resetIbanWalletFields()
    {
        $this->ibanNumber = '';
        $this->ibanBankName = '';
        $this->ibanAccountHolder = '';
        $this->ibanSwiftBic = '';
        $this->ibanRoyaltyMint = 0;
        $this->ibanRoyaltyRebind = 0;
        $this->resetErrorBag();
    }

    public function createIbanWallet()
    {
        $logContext = [
            'collection_id' => $this->collectionId,
            'user_id' => Auth::id(),
            'iban' => substr($this->ibanNumber, 0, 4) . '****' . substr($this->ibanNumber, -4),
            'bank_name' => $this->ibanBankName,
            'royalty_mint' => $this->ibanRoyaltyMint,
            'royalty_rebind' => $this->ibanRoyaltyRebind
        ];

        Log::channel('florenceegi')->info('[CollectionUserMember] Creating IBAN wallet', $logContext);

        try {
            // Validate IBAN format (basic validation)
            $iban = strtoupper(str_replace(' ', '', $this->ibanNumber));
            if (strlen($iban) < 15 || strlen($iban) > 34) {
                throw new \Exception(__('collection.wallet.iban.invalid_format'));
            }
            if (empty($this->ibanAccountHolder)) {
                throw new \Exception(__('collection.wallet.iban.holder_required'));
            }
            if ($this->ibanRoyaltyMint < 0 || $this->ibanRoyaltyMint > 100) {
                throw new \Exception(__('collection.wallet.validation.mint_invalid'));
            }
            if ($this->ibanRoyaltyRebind < 0 || $this->ibanRoyaltyRebind > 100) {
                throw new \Exception(__('collection.wallet.validation.rebind_invalid'));
            }

            DB::transaction(function () use ($logContext, $iban) {
                $creatorWallet = $this->getCreatorWalletAndVerifyQuota($logContext);
                $this->subtractCreatorQuota($creatorWallet, $logContext);

                $wallet = Wallet::create([
                    'collection_id' => $this->collectionId,
                    'user_id' => Auth::id(),
                    'wallet' => 'iban_' . uniqid(),
                    'wallet_type' => 'iban',
                    'royalty_mint' => $this->ibanRoyaltyMint,
                    'royalty_rebind' => $this->ibanRoyaltyRebind,
                    'platform_role' => null,
                    'is_anonymous' => false,
                    'version' => 1,
                    'metadata' => [
                        'iban' => $iban,
                        'bank_name' => $this->ibanBankName ?: null,
                        'account_holder' => $this->ibanAccountHolder,
                        'swift_bic' => $this->ibanSwiftBic ?: null,
                        'created_via' => 'iban_wallet_add',
                        'created_by_user_id' => Auth::id(),
                        'added_at' => now()->toISOString()
                    ]
                ]);

                $this->auditService->logUserAction(
                    Auth::user(),
                    'iban_wallet_added_to_collection',
                    [
                        'wallet_id' => $wallet->id,
                        'collection_id' => $this->collectionId,
                        'iban_masked' => substr($iban, 0, 4) . '****' . substr($iban, -4)
                    ],
                    GdprActivityCategory::WALLET_CREATED
                );
            });

            session()->flash('success', __('collection.wallet.iban.created_successfully'));
            $this->loadTeamUsers();
            $this->closeIbanWalletModal();

        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('[CollectionUserMember] IBAN wallet creation failed', [
                ...$logContext,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', $e->getMessage());
        }
    }

    /**
     * Add external Algorand wallet to collection
     *
     * ULTRA + GDPR Compliant:
     * - ULM: Full logging
     * - UEM: Error handling
     * - GDPR: Audit trail
     *
     * Workflow:
     * 1. Validate Algorand address format
     * 2. Verify address exists on-chain
     * 3. Check for duplicates in collection
     * 4. Verify creator has sufficient royalty quota
     * 5. Subtract quota from creator
     * 6. Create wallet record
     * 7. Audit log
     */
    public function addExternalWallet()
    {
        // ULM: Log start
        $logContext = [
            'collection_id' => $this->collectionId,
            'user_id' => Auth::id(),
            'address' => $this->externalWalletAddress,
            'royalty_mint' => $this->externalWalletRoyaltyMint,
            'royalty_rebind' => $this->externalWalletRoyaltyRebind,
            'wallet_name' => $this->externalWalletName,
            'log_category' => 'EXTERNAL_WALLET_ADD_START'
        ];
        $this->logger->info('[CollectionUserMember] Adding external wallet', $logContext);

        try {
            // 1. Permission check
            if (!$this->canCreateWallet) {
                $this->logger->warning('[CollectionUserMember] Unauthorized external wallet creation', $logContext);
                session()->flash('error', __('collection.wallet.creation_denied'));
                return;
            }

            // 2. Validate inputs
            $this->validateExternalWallet();

            // 3. Normalize address
            $address = trim($this->externalWalletAddress);
            $logContext['address_normalized'] = $address;

            // 4. DB Transaction with all operations
            DB::transaction(function () use ($address, $logContext) {
                // 4a. Verify address exists on-chain
                $this->verifyAddressOnChain($address, $logContext);

                // 4b. Check duplicates
                $this->checkDuplicateAddress($address, $logContext);

                // 4c. Get creator wallet and verify quota
                $creatorWallet = $this->getCreatorWalletAndVerifyQuota($logContext);

                // 4d. Subtract quota from creator
                $this->subtractCreatorQuota($creatorWallet, $logContext);

                // 4e. Create wallet record
                $newWallet = $this->createExternalWalletRecord($address, $logContext);

                // 4f. GDPR Audit Log
                $this->logWalletCreationAudit($newWallet, $logContext);
            });

            // 5. Success feedback
            $this->logger->info('[CollectionUserMember] External wallet added successfully', [
                ...$logContext,
                'log_category' => 'EXTERNAL_WALLET_ADD_SUCCESS'
            ]);

            session()->flash('success', __('collection.wallet.external_added_successfully', [
                'address' => substr($address, 0, 8) . '...' . substr($address, -8)
            ]));

            // 6. Reload and close modal
            $this->loadTeamUsers();
            $this->closeExternalWalletModal();
        } catch (\Exception $e) {
            // ULM: Log error
            $this->logger->error('[CollectionUserMember] External wallet add failed', [
                ...$logContext,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'log_category' => 'EXTERNAL_WALLET_ADD_ERROR'
            ]);

            // UEM: Handle error
            $this->errorManager->handle('EXTERNAL_WALLET_ADD_FAILED', [
                'collection_id' => $this->collectionId,
                'user_id' => Auth::id(),
                'address' => $this->externalWalletAddress,
                'error' => $e->getMessage()
            ], $e);

            session()->flash('error', $e->getMessage());
        }
    }

    /**
     * Validate external wallet inputs
     */
    protected function validateExternalWallet()
    {
        // Basic validation
        if (empty($this->externalWalletAddress)) {
            throw new \Exception(__('collection.wallet.validation.address_required'));
        }

        // Algorand address format: 58 characters, Base32
        $address = trim($this->externalWalletAddress);
        if (strlen($address) !== 58) {
            throw new \Exception(__('collection.wallet.validation.address_invalid_length'));
        }

        if (!preg_match('/^[A-Z2-7]+$/', $address)) {
            throw new \Exception(__('collection.wallet.validation.address_invalid_format'));
        }

        // Royalty validation
        if ($this->externalWalletRoyaltyMint < 0 || $this->externalWalletRoyaltyMint > 100) {
            throw new \Exception(__('collection.wallet.validation.mint_invalid'));
        }

        if ($this->externalWalletRoyaltyRebind < 0 || $this->externalWalletRoyaltyRebind > 100) {
            throw new \Exception(__('collection.wallet.validation.rebind_invalid'));
        }
    }

    /**
     * Verify address exists on Algorand blockchain
     */
    protected function verifyAddressOnChain(string $address, array &$logContext)
    {
        $this->logger->debug('[CollectionUserMember] Verifying address on-chain', [
            ...$logContext,
            'address' => $address
        ]);

        try {
            $accountInfo = $this->algorandClient->getAccountInfo($address);

            $logContext['on_chain_verified'] = true;
            $logContext['account_balance'] = $accountInfo['amount'] ?? null;

            $this->logger->info('[CollectionUserMember] Address verified on-chain', $logContext);
        } catch (\Exception $e) {
            $this->logger->error('[CollectionUserMember] Address not found on-chain', [
                ...$logContext,
                'error' => $e->getMessage()
            ]);

            throw new \Exception(__('collection.wallet.validation.address_not_found_onchain'));
        }
    }

    /**
     * Check for duplicate address in collection
     */
    protected function checkDuplicateAddress(string $address, array $logContext)
    {
        $exists = Wallet::where('collection_id', $this->collectionId)
            ->where('wallet', $address)
            ->exists();

        if ($exists) {
            $this->logger->warning('[CollectionUserMember] Duplicate address detected', [
                ...$logContext,
                'address' => $address
            ]);

            throw new \Exception(__('collection.wallet.validation.address_already_exists'));
        }
    }

    /**
     * Get creator wallet and verify sufficient quota
     */
    protected function getCreatorWalletAndVerifyQuota(array $logContext): Wallet
    {
        // Find creator wallet (user who is adding the wallet)
        $creatorWallet = Wallet::where('collection_id', $this->collectionId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$creatorWallet) {
            $this->logger->error('[CollectionUserMember] Creator wallet not found', $logContext);
            throw new \Exception(__('collection.wallet.creator_wallet_not_found'));
        }

        // Get thresholds
        $thresholdMint = config('app.creator_royalty_mint_threshold', 0);
        $thresholdRebind = config('app.creator_royalty_rebind_threshold', 0);

        $logContext['creator_wallet_id'] = $creatorWallet->id;
        $logContext['creator_current_mint'] = $creatorWallet->royalty_mint;
        $logContext['creator_current_rebind'] = $creatorWallet->royalty_rebind;
        $logContext['threshold_mint'] = $thresholdMint;
        $logContext['threshold_rebind'] = $thresholdRebind;

        // Verify sufficient quota with threshold
        $hasQuota = (
            $creatorWallet->royalty_mint >= $this->externalWalletRoyaltyMint &&
            $creatorWallet->royalty_rebind >= $this->externalWalletRoyaltyRebind &&
            ($creatorWallet->royalty_mint - $this->externalWalletRoyaltyMint) >= $thresholdMint &&
            ($creatorWallet->royalty_rebind - $this->externalWalletRoyaltyRebind) >= $thresholdRebind
        );

        if (!$hasQuota) {
            $this->logger->warning('[CollectionUserMember] Insufficient quota', $logContext);
            throw new \Exception(__('collection.wallet.creator_does_not_have_enough_quota_to_allocate'));
        }

        $this->logger->info('[CollectionUserMember] Creator has sufficient quota', $logContext);

        return $creatorWallet;
    }

    /**
     * Subtract quota from creator wallet
     */
    protected function subtractCreatorQuota(Wallet $creatorWallet, array $logContext)
    {
        $oldMint = $creatorWallet->royalty_mint;
        $oldRebind = $creatorWallet->royalty_rebind;

        $creatorWallet->update([
            'royalty_mint' => $oldMint - $this->externalWalletRoyaltyMint,
            'royalty_rebind' => $oldRebind - $this->externalWalletRoyaltyRebind,
        ]);

        $this->logger->info('[CollectionUserMember] Creator quota subtracted', [
            ...$logContext,
            'old_mint' => $oldMint,
            'old_rebind' => $oldRebind,
            'new_mint' => $creatorWallet->royalty_mint,
            'new_rebind' => $creatorWallet->royalty_rebind
        ]);
    }

    /**
     * Create external wallet record (NON-custodial)
     */
    protected function createExternalWalletRecord(string $address, array $logContext): Wallet
    {
        $wallet = Wallet::create([
            // Relationships
            'collection_id' => $this->collectionId,
            'user_id' => null, // External wallet not tied to platform user

            // Address
            'wallet' => $address,

            // Royalties
            'royalty_mint' => $this->externalWalletRoyaltyMint,
            'royalty_rebind' => $this->externalWalletRoyaltyRebind,

            // Business logic
            'platform_role' => null,
            'is_anonymous' => false,

            // NO encryption fields - this is NON-custodial external wallet
            'wallet_type' => 'algorand_external',
            'version' => 1,
            'metadata' => [
                'created_via' => 'external_wallet_add',
                'created_by_user_id' => Auth::id(),
                'wallet_name' => $this->externalWalletName ?: null,
                'added_at' => now()->toISOString()
            ]
        ]);

        $this->logger->info('[CollectionUserMember] External wallet record created', [
            ...$logContext,
            'wallet_id' => $wallet->id
        ]);

        return $wallet;
    }

    /**
     * GDPR Audit log for wallet creation
     */
    protected function logWalletCreationAudit(Wallet $wallet, array $logContext)
    {
        $this->auditService->logUserAction(
            Auth::user(),
            'external_wallet_added_to_collection',
            [
                'wallet_id' => $wallet->id,
                'collection_id' => $this->collectionId,
                'algorand_address' => $wallet->wallet,
                'royalty_mint' => $wallet->royalty_mint,
                'royalty_rebind' => $wallet->royalty_rebind,
                'wallet_name' => $this->externalWalletName ?: null
            ],
            GdprActivityCategory::WALLET_CREATED
        );

        $this->logger->debug('[CollectionUserMember] GDPR audit logged', $logContext);
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
    public function createNewWallet()
    {
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

    public function render()
    {
        return view('livewire.collections.collection-user-member');
    }
}
