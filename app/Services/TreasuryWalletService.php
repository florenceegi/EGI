<?php

namespace App\Services;

use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\User;
use App\Models\EgiBlockchain;
use App\Services\AlgorandService;
use Illuminate\Support\Facades\Cache;

/**
 * @Oracode Treasury Wallet Service - Manages platform treasury operations
 * 🎯 Purpose: Handle treasury wallet operations for EGI blockchain integration
 * 🧱 Core Logic: Treasury balance, EGI custody, automated transfers, monitoring
 * 🛡️ Security: MiCA-SAFE compliance, secure custody, audit trails
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Treasury wallet management for EGI marketplace
 */
class TreasuryWalletService {
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private ConsentService $consentService;
    private AlgorandService $algorandService;
    private string $treasuryAddress;
    private string $treasuryMnemonic;

    /**
     * Constructor with GDPR/Ultra compliance
     *
     * @param UltraLogManager $logger Ultra logging manager for audit trails
     * @param ErrorManagerInterface $errorManager Ultra error manager for error handling
     * @param AuditLogService $auditService GDPR audit logging service
     * @param ConsentService $consentService GDPR consent management service
     * @param AlgorandService $algorandService Algorand blockchain service
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService,
        AlgorandService $algorandService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
        $this->algorandService = $algorandService;
        
        // Load treasury configuration
        $this->treasuryAddress = config('algorand.algorand.treasury_address', '');
        $this->treasuryMnemonic = config('algorand.algorand.treasury_mnemonic', '');
        
        if (empty($this->treasuryAddress)) {
            throw new \Exception('Treasury address not configured. Run: php artisan egi:generate-treasury-wallet');
        }
    }

    /**
     * Get treasury wallet status and balance
     * @return array Treasury status information
     * @throws \Exception
     */
    public function getTreasuryStatus(): array {
        try {
            $this->logger->info('TREASURY_STATUS_CHECK', ['address' => $this->treasuryAddress]);

            // Get treasury status from AlgorandService
            $treasuryStatus = $this->algorandService->getTreasuryStatus();
            
            // Get EGI custody information
            $custodyInfo = $this->getEgiCustodyInfo();
            
            $status = [
                'address' => $this->treasuryAddress,
                'balance' => $treasuryStatus['balance'] ?? 0,
                'status' => $treasuryStatus['status'] ?? 'unknown',
                'network' => $treasuryStatus['network'] ?? 'sandbox',
                'custody' => $custodyInfo,
                'health' => $this->checkTreasuryHealth(),
                'last_updated' => now()->toISOString()
            ];

            $this->logger->info('TREASURY_STATUS_SUCCESS', $status);
            return $status;

        } catch (\Exception $e) {
            $this->errorManager->handle('TREASURY_STATUS_FAILED', [
                'address' => $this->treasuryAddress,
                'error' => $e->getMessage()
            ], $e);
            
            throw new \Exception("Treasury status check failed: {$e->getMessage()}");
        }
    }

    /**
     * Transfer EGI from treasury to user wallet - GDPR COMPLIANT
     * @param string $toAddress Destination wallet address
     * @param string $asaId ASA ID to transfer
     * @param User $user User requesting transfer
     * @param int $amount Amount to transfer (default 1 for NFT)
     * @return array Transfer result
     * @throws \Exception
     */
    public function transferEgiToUser(string $toAddress, string $asaId, User $user, int $amount = 1): array {
        try {
            // 1. ULM: Log start
            $this->logger->info('TREASURY_TRANSFER_INITIATED', [
                'user_id' => $user->id,
                'to_address' => $toAddress,
                'asa_id' => $asaId,
                'amount' => $amount
            ]);

            // 2. GDPR: Check consent
            if (!$this->consentService->hasConsent($user, 'allow-blockchain-operations')) {
                throw new \Exception('User consent required for blockchain operations');
            }

            // 3. Validate destination address
            if (!$this->isValidAlgorandAddress($toAddress)) {
                throw new \Exception('Invalid destination address format');
            }

            // 4. Check treasury has the asset
            if (!$this->treasuryHasAsset($asaId)) {
                throw new \Exception('Asset not found in treasury wallet');
            }

            // 5. Execute transfer via AlgorandService
            $transferResult = $this->algorandService->transferEgiAsset($toAddress, $asaId, $user, $amount);

            // 6. Update database record
            $this->updateEgiBlockchainOwnership($asaId, $toAddress, $user);

            // 7. GDPR: Audit trail
            $this->auditService->logActivity(
                $user,
                GdprActivityCategory::BLOCKCHAIN_TRANSFER,
                'EGI transferred from treasury to user wallet',
                [
                    'asa_id' => $asaId,
                    'to_address' => $toAddress,
                    'amount' => $amount,
                    'tx_id' => $transferResult
                ]
            );

            // 8. ULM: Log success
            $this->logger->info('TREASURY_TRANSFER_SUCCESS', [
                'tx_id' => $transferResult,
                'user_id' => $user->id,
                'asa_id' => $asaId
            ]);

            return [
                'success' => true,
                'tx_id' => $transferResult,
                'from_address' => $this->treasuryAddress,
                'to_address' => $toAddress,
                'asa_id' => $asaId,
                'amount' => $amount,
                'timestamp' => now()->toISOString()
            ];

        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('TREASURY_TRANSFER_FAILED', [
                'user_id' => $user->id,
                'to_address' => $toAddress,
                'asa_id' => $asaId,
                'error' => $e->getMessage()
            ], $e);

            throw new \Exception("Treasury transfer failed: {$e->getMessage()}");
        }
    }

    /**
     * Get custody information for EGIs in treasury
     * @return array Custody statistics
     */
    private function getEgiCustodyInfo(): array {
        $custodyStats = EgiBlockchain::where('ownership_type', 'treasury')
            ->selectRaw('
                COUNT(*) as total_egis,
                COUNT(CASE WHEN mint_status = "minted" THEN 1 END) as minted_egis,
                COUNT(CASE WHEN mint_status = "pending" THEN 1 END) as pending_egis,
                COUNT(CASE WHEN buyer_wallet IS NOT NULL THEN 1 END) as ready_for_transfer
            ')
            ->first();

        return [
            'total_egis_in_custody' => $custodyStats->total_egis ?? 0,
            'minted_egis' => $custodyStats->minted_egis ?? 0,
            'pending_egis' => $custodyStats->pending_egis ?? 0,
            'ready_for_transfer' => $custodyStats->ready_for_transfer ?? 0,
        ];
    }

    /**
     * Check treasury wallet health
     * @return array Health status
     */
    private function checkTreasuryHealth(): array {
        $cacheKey = 'treasury_health_' . substr($this->treasuryAddress, 0, 8);
        
        return Cache::remember($cacheKey, 300, function () {
            try {
                // Get network status
                $networkStatus = $this->algorandService->getNetworkStatus();
                
                // Check for pending transfers
                $pendingTransfers = EgiBlockchain::where('ownership_type', 'treasury')
                    ->whereNotNull('buyer_wallet')
                    ->where('mint_status', 'minted')
                    ->count();

                return [
                    'network_connected' => $networkStatus['success'] ?? false,
                    'pending_transfers' => $pendingTransfers,
                    'last_activity' => $this->getLastTreasuryActivity(),
                    'status' => $pendingTransfers > 0 ? 'pending_transfers' : 'healthy'
                ];
                
            } catch (\Exception $e) {
                return [
                    'network_connected' => false,
                    'error' => $e->getMessage(),
                    'status' => 'error'
                ];
            }
        });
    }

    /**
     * Get last treasury activity timestamp
     * @return string|null Last activity timestamp
     */
    private function getLastTreasuryActivity(): ?string {
        $lastActivity = EgiBlockchain::where('ownership_type', 'treasury')
            ->orderBy('updated_at', 'desc')
            ->first();
            
        return $lastActivity ? $lastActivity->updated_at->toISOString() : null;
    }

    /**
     * Check if treasury has specific asset
     * @param string $asaId ASA ID to check
     * @return bool Has asset
     */
    private function treasuryHasAsset(string $asaId): bool {
        return EgiBlockchain::where('asa_id', $asaId)
            ->where('ownership_type', 'treasury')
            ->where('mint_status', 'minted')
            ->exists();
    }

    /**
     * Update EGI blockchain ownership after transfer
     * @param string $asaId ASA ID
     * @param string $toAddress New owner address
     * @param User $user User who received transfer
     */
    private function updateEgiBlockchainOwnership(string $asaId, string $toAddress, User $user): void {
        EgiBlockchain::where('asa_id', $asaId)->update([
            'ownership_type' => 'wallet',
            'buyer_wallet' => $toAddress,
            'buyer_user_id' => $user->id,
            'updated_at' => now()
        ]);
    }

    /**
     * Validate Algorand address format
     * @param string $address Address to validate
     * @return bool Is valid
     */
    private function isValidAlgorandAddress(string $address): bool {
        // Check length and character set (Algorand Base32)
        if (strlen($address) !== 58) {
            return false;
        }
        
        // Check if contains only valid Base32 characters (A-Z, 2-7)
        $validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        for ($i = 0; $i < strlen($address); $i++) {
            if (strpos($validChars, $address[$i]) === false) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get treasury address (read-only)
     * @return string Treasury address
     */
    public function getTreasuryAddress(): string {
        return $this->treasuryAddress;
    }

    /**
     * Process pending transfers (batch operation)
     * @return array Processing results
     */
    public function processPendingTransfers(): array {
        try {
            $this->logger->info('TREASURY_BATCH_PROCESSING_START');

            $pendingTransfers = EgiBlockchain::where('ownership_type', 'treasury')
                ->whereNotNull('buyer_wallet')
                ->where('mint_status', 'minted')
                ->limit(10) // Process in batches
                ->get();

            $results = [
                'processed' => 0,
                'failed' => 0,
                'errors' => []
            ];

            foreach ($pendingTransfers as $transfer) {
                try {
                    $user = User::find($transfer->buyer_user_id);
                    if (!$user) {
                        throw new \Exception('User not found');
                    }

                    $this->transferEgiToUser(
                        $transfer->buyer_wallet,
                        $transfer->asa_id,
                        $user
                    );

                    $results['processed']++;

                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'egi_id' => $transfer->egi_id,
                        'error' => $e->getMessage()
                    ];
                }
            }

            $this->logger->info('TREASURY_BATCH_PROCESSING_COMPLETE', $results);
            return $results;

        } catch (\Exception $e) {
            $this->errorManager->handle('TREASURY_BATCH_PROCESSING_FAILED', [
                'error' => $e->getMessage()
            ], $e);

            throw new \Exception("Batch processing failed: {$e->getMessage()}");
        }
    }
}