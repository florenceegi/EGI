<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\Wallet;
use App\Services\Blockchain\AlgorandClient;
use App\Services\Security\KmsClient;
use App\Services\Security\KmsHealthCheck;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode WalletProvisioningService: Secure wallet creation for users
 * 🎯 Purpose: Create and secure Algorand + IBAN wallets with envelope encryption
 * 🛡️ Privacy: Full GDPR compliance with audit logging + KMS encryption
 * 🧱 Core Logic: UEM error handling + ULM logging + GDPR audit + Envelope encryption
 *
 * @package App\Services\Wallet
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Wallet Security Module)
 * @date 2025-10-22
 * @purpose Enterprise-grade custodial wallet provisioning
 * @source docs/ai/blockchain/nuova_logica_wallet.md
 */
class WalletProvisioningService
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;
    protected AlgorandClient $algorandClient;
    protected KmsClient $kms;
    protected KmsHealthCheck $kmsHealth;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        AlgorandClient $algorandClient,
        KmsClient $kms,
        KmsHealthCheck $kmsHealth
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->algorandClient = $algorandClient;
        $this->kms = $kms;
        $this->kmsHealth = $kmsHealth;
    }

    /**
     * Provision complete wallet setup for user
     *
     * Creates:
     * - Real Algorand wallet with encrypted mnemonic
     * - IBAN wallet (if provided) with encryption
     *
     * @param User $user The user to provision wallets for
     * @param array $data ['iban' => string|null, 'wallet_passphrase' => string|null, 'collection_id' => int|null]
     * @return Wallet The created Algorand wallet
     * @throws \Exception if provisioning fails
     */
    public function provisionUserWallet(User $user, array $data = []): Wallet
    {
        try {
            // 0. PRE-FLIGHT: Verify KMS is healthy before attempting wallet creation
            $this->kmsHealth->ensureHealthy();

            // 1. ULM: Log start
            $this->logger->info('WalletProvisioning: Starting wallet creation', [
                'user_id' => $user->id,
                'user_type' => $user->usertype,
                'has_iban' => !empty($data['iban']),
                'log_category' => 'WALLET_PROVISION_START'
            ]);

            // 2. Start database transaction
            return DB::transaction(function () use ($user, $data) {
                // 3. Create Algorand wallet
                $algorandWallet = $this->createAlgorandWallet($user, $data['collection_id'] ?? null);

                // 4. Add IBAN if provided (same wallet record)
                if (!empty($data['iban'])) {
                    $this->addIbanToWalletInternal($algorandWallet, $data['iban']);
                }

                // 5. GDPR: Log wallet creation
                $this->auditService->logUserAction(
                    $user,
                    'wallet_provisioned',
                    [
                        'algorand_address' => $algorandWallet->wallet,
                        'has_iban' => !empty($data['iban']),
                        'usertype' => $user->usertype
                    ],
                    GdprActivityCategory::WALLET_CREATED
                );

                // 6. ULM: Log success
                $this->logger->info('WalletProvisioning: Wallet created successfully', [
                    'user_id' => $user->id,
                    'wallet_id' => $algorandWallet->id,
                    'address' => $algorandWallet->wallet,
                    'log_category' => 'WALLET_PROVISION_SUCCESS'
                ]);

                return $algorandWallet;
            });
        } catch (\Exception $e) {
            // 7. ULM: Log error
            $this->logger->error('WalletProvisioning: Wallet creation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'log_category' => 'WALLET_PROVISION_ERROR'
            ]);

            // 8. UEM: Handle error
            $this->errorManager->handle('WALLET_PROVISION_FAILED', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Provision wallet with flexible parameters (support non-user wallets for collections)
     *
     * Use cases:
     * - User wallet: provisionWallet(userId: 123, collectionId: null)
     * - Collection wallet WITH user: provisionWallet(userId: 123, collectionId: 456)
     * - Collection wallet WITHOUT user: provisionWallet(userId: null, collectionId: 456)
     *
     * @param int|null $userId Optional user ID (can be null for collection-only wallets)
     * @param int|null $collectionId Optional collection ID
     * @param array $data Additional data (iban, etc.)
     * @return Wallet The created wallet
     * @throws \Exception
     */
    public function provisionWallet(?int $userId = null, ?int $collectionId = null, array $data = []): Wallet
    {
        try {
            // 1. ULM: Log start
            $this->logger->info('WalletProvisioning: Starting flexible wallet creation', [
                'user_id' => $userId,
                'collection_id' => $collectionId,
                'has_iban' => !empty($data['iban']),
                'log_category' => 'WALLET_PROVISION_FLEXIBLE_START'
            ]);

            // 2. Start database transaction
            return DB::transaction(function () use ($userId, $collectionId, $data) {
                // 3. Create Algorand wallet
                $algorandWallet = $this->createAlgorandWalletFlexible($userId, $collectionId);

                // 4. Add IBAN if provided
                if (!empty($data['iban'])) {
                    $this->addIbanToWalletInternal($algorandWallet, $data['iban']);
                }

                // 5. GDPR: Log wallet creation (only if user is present)
                if ($userId) {
                    $user = User::findOrFail($userId);
                    $this->auditService->logUserAction(
                        $user,
                        'wallet_provisioned',
                        [
                            'algorand_address' => $algorandWallet->wallet,
                            'has_iban' => !empty($data['iban']),
                            'collection_id' => $collectionId
                        ],
                        GdprActivityCategory::WALLET_CREATED
                    );
                }

                // 6. ULM: Log success
                $this->logger->info('WalletProvisioning: Flexible wallet created successfully', [
                    'user_id' => $userId,
                    'collection_id' => $collectionId,
                    'wallet_id' => $algorandWallet->id,
                    'address' => $algorandWallet->wallet,
                    'log_category' => 'WALLET_PROVISION_FLEXIBLE_SUCCESS'
                ]);

                return $algorandWallet;
            });
        } catch (\Exception $e) {
            // 7. ULM: Log error
            $this->logger->error('WalletProvisioning: Flexible wallet creation failed', [
                'user_id' => $userId,
                'collection_id' => $collectionId,
                'error' => $e->getMessage(),
                'log_category' => 'WALLET_PROVISION_FLEXIBLE_ERROR'
            ]);

            // 8. UEM: Handle error
            $this->errorManager->handle('WALLET_PROVISION_FLEXIBLE_FAILED', [
                'user_id' => $userId,
                'collection_id' => $collectionId,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Create Algorand wallet with flexible user/collection parameters
     *
     * @param int|null $userId Can be null for collection-only wallets
     * @param int|null $collectionId Optional collection association
     * @return Wallet
     * @throws \Exception
     */
    protected function createAlgorandWalletFlexible(?int $userId, ?int $collectionId): Wallet
    {
        try {
            // 1. Generate real Algorand account
            $accountData = $this->algorandClient->createAccount();
            $address = $accountData['address'];
            $mnemonic = $accountData['mnemonic'];

            // 2. Encrypt mnemonic using KMS envelope encryption
            $encrypted = $this->kms->secureEncrypt($mnemonic);

            // 3. Wipe mnemonic from memory
            if (function_exists('sodium_memzero')) {
                sodium_memzero($mnemonic);
            }

            // 4. Create wallet record in wallets table
            $wallet = Wallet::create([
                // Relationships
                'collection_id' => $collectionId,
                'user_id' => $userId, // Can be NULL for collection-only wallets

                // Business logic
                'wallet' => $address,
                'platform_role' => null,
                'royalty_mint' => null,
                'royalty_rebind' => null,
                'is_anonymous' => false,

                // Encryption fields
                'secret_ciphertext' => base64_decode($encrypted['ciphertext']),
                'secret_nonce' => base64_decode($encrypted['nonce']),
                'dek_encrypted' => json_encode($encrypted['encrypted_dek']),
                'cipher_algo' => $encrypted['algorithm'],

                // Metadata
                'wallet_type' => 'algorand',
                'version' => 1,
                'metadata' => [
                    'created_via' => 'provisioning_service_flexible',
                    'network' => config('algorand.network', 'sandbox'),
                    'created_at' => now()->toISOString(),
                    'has_user' => $userId !== null,
                    'has_collection' => $collectionId !== null
                ]
            ]);

            $this->logger->info('Algorand wallet created (flexible)', [
                'user_id' => $userId,
                'collection_id' => $collectionId,
                'wallet_id' => $wallet->id,
                'address' => $address,
                'log_category' => 'ALGORAND_WALLET_CREATED_FLEXIBLE'
            ]);

            return $wallet;
        } catch (\Exception $e) {
            $this->logger->error('Algorand wallet creation failed (flexible)', [
                'user_id' => $userId,
                'collection_id' => $collectionId,
                'error' => $e->getMessage(),
                'log_category' => 'ALGORAND_WALLET_FLEXIBLE_ERROR'
            ]);

            throw new \Exception("Failed to create Algorand wallet: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create Algorand wallet with envelope encryption
     *
     * Security:
     * - Generates real Algorand account via microservice
     * - Encrypts mnemonic with XChaCha20-Poly1305
     * - Protects DEK with KMS (AWS/Azure/Vault)
     * - Uses sodium_memzero() for secure memory cleanup
     *
     * @param User $user
     * @param int|null $collectionId Optional collection to link wallet to
     * @return Wallet
     * @throws \Exception
     */
    protected function createAlgorandWallet(User $user, ?int $collectionId = null): Wallet
    {
        try {
            // 1. Generate real Algorand account
            $accountData = $this->algorandClient->createAccount();
            $address = $accountData['address'];
            $mnemonic = $accountData['mnemonic'];

            // 2. Encrypt mnemonic using KMS envelope encryption
            $encrypted = $this->kms->secureEncrypt($mnemonic);

            // 3. Wipe mnemonic from memory
            if (function_exists('sodium_memzero')) {
                sodium_memzero($mnemonic);
            }

            // 4. Create wallet record in wallets table
            $wallet = Wallet::create([
                // Relationships
                'collection_id' => $collectionId,
                'user_id' => $user->id,

                // Business logic (will be set later if in collection context)
                'wallet' => $address,
                'platform_role' => null, // Will be set by WalletService if needed
                'royalty_mint' => null,
                'royalty_rebind' => null,
                'is_anonymous' => false,

                // Encryption fields
                'secret_ciphertext' => base64_decode($encrypted['ciphertext']),
                'secret_nonce' => base64_decode($encrypted['nonce']),
                'dek_encrypted' => json_encode($encrypted['encrypted_dek']),
                'cipher_algo' => $encrypted['algorithm'],

                // Metadata
                'wallet_type' => 'algorand',
                'version' => 1,
                'metadata' => [
                    'created_via' => 'provisioning_service',
                    'network' => config('algorand.network', 'sandbox'),
                    'created_at' => now()->toISOString()
                ]
            ]);

            $this->logger->info('Algorand wallet created', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'address' => $address,
                'log_category' => 'ALGORAND_WALLET_CREATED'
            ]);

            return $wallet;
        } catch (\Exception $e) {
            $this->logger->error('Algorand wallet creation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'log_category' => 'ALGORAND_WALLET_ERROR'
            ]);

            throw new \Exception("Failed to create Algorand wallet: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Add IBAN to existing wallet (internal method)
     *
     * Security:
     * - Stores encrypted IBAN using Laravel cast encryption
     * - Creates SHA-256 hash with pepper for uniqueness checks
     * - Stores last 4 digits for UI display
     *
     * @param Wallet $wallet
     * @param string $iban Raw IBAN string
     * @return Wallet
     * @throws \Exception
     */
    protected function addIbanToWalletInternal(Wallet $wallet, string $iban): Wallet
    {
        try {
            // 1. Normalize IBAN (remove spaces, uppercase)
            $ibanNorm = strtoupper(preg_replace('/\s+/', '', $iban));

            // 2. Validate IBAN format
            if (!$this->validateIban($ibanNorm)) {
                throw new \Exception("Invalid IBAN format: {$iban}");
            }

            // 3. Check for duplicate IBAN
            $pepper = config('app.iban_pepper', config('app.key'));
            $ibanHash = hash('sha256', $ibanNorm . $pepper);

            $exists = Wallet::where('iban_hash', $ibanHash)
                ->where('id', '!=', $wallet->id)
                ->exists();

            if ($exists) {
                throw new \Exception("IBAN already registered to another wallet");
            }

            // 4. Update wallet with IBAN data
            $wallet->update([
                'iban_encrypted' => $ibanNorm, // Will be encrypted by model cast
                'iban_hash' => $ibanHash,
                'iban_last4' => substr($ibanNorm, -4),
                'wallet_type' => 'both', // Has both Algorand and IBAN
            ]);

            // Update metadata
            $metadata = $wallet->metadata ?? [];
            $metadata['iban_added_at'] = now()->toISOString();
            $metadata['iban_country_code'] = substr($ibanNorm, 0, 2);
            $wallet->update(['metadata' => $metadata]);

            $this->logger->info('IBAN added to wallet', [
                'wallet_id' => $wallet->id,
                'iban_last4' => $wallet->iban_last4,
                'log_category' => 'IBAN_ADDED'
            ]);

            return $wallet;
        } catch (\Exception $e) {
            $this->logger->error('IBAN addition failed', [
                'wallet_id' => $wallet->id,
                'error' => $e->getMessage(),
                'log_category' => 'IBAN_ADD_ERROR'
            ]);

            throw new \Exception("Failed to add IBAN to wallet: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Validate IBAN format
     *
     * Basic validation:
     * - Length: 15-34 characters
     * - Format: 2 letters + 2 digits + alphanumeric
     * - Checksum validation
     *
     * @param string $iban Normalized IBAN
     * @return bool
     */
    protected function validateIban(string $iban): bool
    {
        // Basic length check
        if (strlen($iban) < 15 || strlen($iban) > 34) {
            return false;
        }

        // Format check: 2 letters + 2 digits + rest
        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/', $iban)) {
            return false;
        }

        // MOD-97 checksum validation
        $rearranged = substr($iban, 4) . substr($iban, 0, 4);
        $numeric = '';

        for ($i = 0; $i < strlen($rearranged); $i++) {
            $char = $rearranged[$i];
            if (ctype_alpha($char)) {
                $numeric .= (ord($char) - ord('A') + 10);
            } else {
                $numeric .= $char;
            }
        }

        return bcmod($numeric, '97') === '1';
    }

    /**
     * Add IBAN to wallet by wallet ID (public method for controllers)
     *
     * @param int $walletId The ID of the wallet
     * @param string $iban The IBAN to add
     * @return Wallet The updated wallet
     * @throws \Exception
     */
    public function addIbanToWallet(int $walletId, string $iban): Wallet
    {
        try {
            // 1. Find wallet by ID
            $wallet = Wallet::findOrFail($walletId);

            // 2. Call protected method with wallet object
            return $this->addIbanToWalletInternal($wallet, $iban);
        } catch (\Exception $e) {
            $this->logger->error('Failed to add IBAN to wallet', [
                'wallet_id' => $walletId,
                'error' => $e->getMessage(),
                'log_category' => 'IBAN_ADD_ERROR'
            ]);

            $this->errorManager->handle('IBAN_ADD_FAILED', [
                'wallet_id' => $walletId,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Retrieve decrypted mnemonic (with audit)
     *
     * SECURITY CRITICAL:
     * - Requires step-up authentication
     * - Logs to audit trail
     * - Should only be called after 2FA verification
     *
     * @param Wallet $wallet
     * @param User $user For audit logging
     * @return string Decrypted mnemonic
     * @throws \Exception
     */
    public function retrieveMnemonic(Wallet $wallet, User $user): string
    {
        try {
            if (!$wallet->hasMnemonic()) {
                throw new \Exception("Wallet does not have encrypted mnemonic");
            }

            // 1. Prepare encrypted data for KMS
            $encrypted = [
                'ciphertext' => base64_encode($wallet->secret_ciphertext),
                'nonce' => base64_encode($wallet->secret_nonce),
                'encrypted_dek' => json_decode($wallet->dek_encrypted, true),
                'algorithm' => $wallet->cipher_algo ?? 'xchacha20poly1305'
            ];

            // 2. Decrypt using KMS
            $mnemonic = $this->kms->secureDecrypt($encrypted);

            // 3. GDPR Audit: Log secret access
            $this->auditService->logUserAction(
                $user,
                'wallet_mnemonic_accessed',
                [
                    'wallet_id' => $wallet->id,
                    'address' => $wallet->wallet,
                    'access_reason' => 'user_export'
                ],
                GdprActivityCategory::WALLET_SECRET_ACCESSED
            );

            // 4. ULM: Log access
            $this->logger->warning('Wallet mnemonic accessed', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'address' => $wallet->wallet,
                'log_category' => 'WALLET_MNEMONIC_ACCESS'
            ]);

            return $mnemonic;
        } catch (\Exception $e) {
            $this->logger->error('Mnemonic retrieval failed', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'error' => $e->getMessage(),
                'log_category' => 'WALLET_MNEMONIC_ERROR'
            ]);

            $this->errorManager->handle('WALLET_MNEMONIC_RETRIEVAL_FAILED', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id
            ], $e);

            throw $e;
        }
    }
}
