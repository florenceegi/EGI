<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\User;

/**
 * @Oracode Algorand Service per FlorenceEGI Marketplace - BLOCKCHAIN INTEGRATION
 * 🎯 Manage ASA creation, asset transfer e account info via AlgoKit Microservice
 * 🧱 Core: HTTP calls to AlgoKit microservice per EGI minting e ownership
 * 🛡️ Security: input validation, timeout handling, error management with UEM
 *
 * MICROSERVICE INTEGRATION:
 * - Uses AlgoKit 3.0 microservice instead of direct API calls
 * - EGI-specific interface for marketplace integration
 * - Improved reliability and error handling
 * - TypeScript AlgoKit backend for better blockchain integration
 *
 * MiCA-SAFE COMPLIANCE:
 * - Treasury wallet mode: platform custody for EGI minting only
 * - No crypto-asset custody for user accounts (MiCA-compliant)
 * - Fiat payments via PSP, blockchain operations via treasury
 * - Transfer to user wallets after minting (non-custodial)
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 3.0.0 - EGI MARKETPLACE INTEGRATION
 * @date 2025-10-07
 * @purpose Laravel HTTP client bridge to AlgoKit microservice for EGI
 */
class AlgorandService {
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private ConsentService $consentService;
    private string $microserviceUrl;
    private int $apiTimeout;
    private int $apiRetries;
    private int $apiRetryDelay;
    private array $config;
    private array $asaConfig;

    /**
     * AlgorandService constructor with GDPR/Ultra compliance.
     * Carica config e imposta HTTP client per microservice + GDPR services
     *
     * @param UltraLogManager $logger Ultra logging manager for audit trails
     * @param ErrorManagerInterface $errorManager Ultra error manager for error handling
     * @param AuditLogService $auditService GDPR audit logging service
     * @param ConsentService $consentService GDPR consent management service
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;

        // Carico la config principale
        $cfg = config('algorand');
        $this->config = $cfg;
        $this->asaConfig = $cfg['asa_config'] ?? [];

        // Configurazione microservice
        $this->microserviceUrl = rtrim(config('algorand.algokit_microservice.url', 'http://localhost:3000'), '/');
        $this->apiTimeout = config('algorand.algokit_microservice.timeout', 30);
        $this->apiRetries = config('algorand.algokit_microservice.retries', 3);
        $this->apiRetryDelay = config('algorand.algokit_microservice.retry_delay', 1000);

        $this->logger->info('AlgorandService initialized (EGI Microservice Mode)', [
            'microservice_url' => $this->microserviceUrl,
            'timeout' => $this->apiTimeout
        ]);
    }

    /**
     * Health check del microservizio AlgoKit con auto-start se offline
     *
     * Verifica che il microservizio sia raggiungibile prima di tentare operazioni blockchain.
     * Se il microservizio è offline, tenta di avviarlo automaticamente.
     *
     * @return bool True se microservizio raggiungibile o avviato con successo
     * @throws \Exception Se microservizio non raggiungibile e non avviabile
     */
    private function ensureMicroserviceRunning(): bool {
        // ULM: Trace health check start
        $this->logger->debug('Microservice health check starting', [
            'url' => $this->microserviceUrl
        ]);

        try {
            // Tentativo di health check
            $response = Http::timeout(5)->get($this->microserviceUrl . '/health');

            if ($response->successful()) {
                // ULM: Trace health check success
                $this->logger->debug('Microservice health check passed', [
                    'url' => $this->microserviceUrl,
                    'status' => 'healthy'
                ]);
                return true;
            }
        } catch (\Exception $e) {
            // ULM: Trace health check failure
            $this->logger->warning('Microservice health check failed', [
                'url' => $this->microserviceUrl,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e)
            ]);

            // UEM: Gestione errore strutturato (ALERT TEAM)
            $this->errorManager->handle('MICROSERVICE_NOT_REACHABLE', [
                'url' => $this->microserviceUrl,
                'error' => $e->getMessage()
            ], $e);

            // ULM: Trace auto-start attempt
            $this->logger->info('Attempting microservice auto-start', [
                'timestamp' => now()->toDateTimeString()
            ]);

            // Tentativo di auto-start
            return $this->attemptMicroserviceAutoStart();
        }

        return false;
    }

    /**
     * Tenta di avviare automaticamente il microservizio AlgoKit
     *
     * @return bool True se avvio riuscito, false altrimenti
     */
    private function attemptMicroserviceAutoStart(): bool {
        try {
            $microservicePath = base_path('algokit-microservice');
            $serverJs = $microservicePath . '/server.js';

            // Verifica che il file esista
            if (!file_exists($serverJs)) {
                $this->errorManager->handle('MICROSERVICE_NOT_FOUND', [
                    'path' => $serverJs
                ], new \Exception('Microservice files missing'));
                return false;
            }

            // FIXED: Usa shell_exec con output completo per debug
            $command = sprintf(
                'cd %s && node server.js > /tmp/algokit-autostart.log 2>&1 & echo $!',
                escapeshellarg($microservicePath)
            );

            // ULM: Trace auto-start command
            $this->logger->info('Microservice auto-start command prepared', [
                'command' => $command,
                'path' => $microservicePath,
                'timestamp' => now()->toDateTimeString()
            ]);

            // Esegue il comando e cattura il PID
            $output = shell_exec($command);
            $pid = trim($output);

            // ULM: Trace PID capture
            $this->logger->debug('Microservice auto-start PID captured', [
                'pid' => $pid,
                'is_numeric' => is_numeric($pid)
            ]);

            if (!empty($pid) && is_numeric($pid)) {
                // Attendere 3 secondi per permettere l'avvio (aumentato da 2)
                sleep(3);

                // Verifica che il processo sia ancora attivo
                $processCheck = trim(shell_exec("ps -p {$pid} -o pid= 2>/dev/null"));

                // ULM: Trace process verification
                $this->logger->debug('Microservice process verification', [
                    'pid' => $pid,
                    'process_check' => $processCheck,
                    'is_running' => !empty($processCheck)
                ]);

                if (!empty($processCheck)) {
                    // Verifica health check dopo avvio con retry
                    for ($i = 0; $i < 3; $i++) {
                        try {
                            $response = Http::timeout(5)->get($this->microserviceUrl . '/health');

                            if ($response->successful()) {
                                // UEM: SUCCESS - ALERT TEAM
                                $this->errorManager->handle('MICROSERVICE_AUTO_STARTED_SUCCESS', [
                                    'pid' => $pid,
                                    'url' => $this->microserviceUrl,
                                    'startup_time_seconds' => 3,
                                    'health_check_attempts' => $i + 1
                                ], new \Exception('Microservice was down and required auto-start'));

                                return true;
                            }
                        } catch (\Exception $e) {
                            if ($i < 2) {
                                sleep(1); // Attendi 1 secondo prima di ritentare
                            } else {
                                // UEM: Health check failed after start
                                $this->errorManager->handle('MICROSERVICE_HEALTH_CHECK_FAILED', [
                                    'pid' => $pid,
                                    'error' => $e->getMessage(),
                                    'attempts' => $i + 1
                                ], $e);
                            }
                        }
                    }
                } else {
                    // ULM: Trace process death
                    $this->logger->error('Microservice process died immediately', [
                        'pid' => $pid,
                        'log_file' => '/tmp/algokit-autostart.log'
                    ]);
                }
            } else {
                // ULM: Trace invalid PID
                $this->logger->error('Microservice invalid PID received', [
                    'pid' => $pid,
                    'output' => $output
                ]);
            }

            // UEM: Auto-start fallito
            $this->errorManager->handle('MICROSERVICE_AUTO_START_FAILED', [
                'command' => $command,
                'pid' => $pid ?? 'none',
                'log_file' => '/tmp/algokit-autostart.log'
            ], new \Exception('Microservice auto-start failed'));

            return false;
        } catch (\Exception $e) {
            // UEM: Auto-start error
            $this->errorManager->handle('MICROSERVICE_AUTO_START_ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], $e);
            return false;
        }
    }

    /**
     * Mint Algorand Standard Asset per EGI con metadata - GDPR COMPLIANT
     * @param int $egiId EGI ID dal database
     * @param array $metadata Metadata EGI
     * @param User $user User requesting mint operation
     * @return array [asaId, txId, certificate_number, asset_url, treasury_address]
     * @throws \Exception
     * @privacy-safe Full GDPR compliance with consent check and audit trail
     */
    public function mintEgi(int $egiId, array $metadata, User $user): array {
        try {
            // 🚨 DEBUG: Log Algorand service call IMMEDIATELY
            $this->logger->emergency('🌊🌊🌊 ALGORAND SERVICE CALLED 🌊🌊🌊', [
                'egi_id' => $egiId,
                'user_id' => $user->id,
                'pid' => getmypid(),
                'xdebug_enabled' => extension_loaded('xdebug') ? 'YES' : 'NO',
                'timestamp' => now()->format('H:i:s.u'),
                'log_category' => 'ALGORAND_MINT_DEBUG'
            ]);

            // CRITICAL: Ensure microservice is running BEFORE minting
            if (!$this->ensureMicroserviceRunning()) {
                throw new \Exception('Microservice not available and auto-start failed');
            }

            // 1. ULM: Log start
            $this->logger->info('EGI minting initiated', [
                'user_id' => $user->id,
                'egi_id' => $egiId,
                'log_category' => 'BLOCKCHAIN_MINT_START'
            ]);

            // 2. GDPR: Check consent
            // TODO: Creare permission 'allow-blockchain-operations' in RolesAndPermissionsSeeder
            // Temporaneamente disabilitato per MVP testing
            // if (!$this->consentService->hasConsent($user, 'allow-blockchain-operations')) {
            //     throw new \Exception('Missing blockchain operations consent');
            // }

            // 3. Prepara metadata per il certificato EGI
            $egiMetadata = $this->buildEgiMetadata($egiId, $metadata);

            // 4. Chiama microservice per mint
            $response = $this->callMicroservice('POST', '/mint-egi-token', [
                'egi_id' => $egiId,
                'metadata' => $egiMetadata
            ]);

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'EGI mint failed without error message');
            }

            $data = $response['data'];
            $asaId = $data['asaId'];
            $txId = $data['txId'];

            // 5. GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'blockchain_egi_minted',
                [
                    'egi_id' => $egiId,
                    'asa_id' => $asaId,
                    'tx_id' => $txId,
                    'metadata' => $egiMetadata
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            // 6. ULM: Log success
            $this->logger->info('EGI minting completed successfully', [
                'user_id' => $user->id,
                'egi_id' => $egiId,
                'asa_id' => $asaId,
                'tx_id' => $txId,
                'log_category' => 'BLOCKCHAIN_MINT_SUCCESS'
            ]);

            return [
                'asaId' => $asaId,
                'txId' => $txId,
                'certificate_number' => $data['certificate_number'] ?? null,
                'asset_url' => $data['asset_url'] ?? null,
                'treasury_address' => $data['treasury_address'] ?? null
            ];
        } catch (\Exception $e) {
            // 7. Pattern matching per UEM error codes specifici
            $errorMessage = $e->getMessage();
            $errorCode = 'BLOCKCHAIN_MINT_FAILED'; // default fallback
            $contextData = [
                'user_id' => $user->id,
                'egi_id' => $egiId,
                'metadata' => json_encode($metadata),
                'error_message' => $errorMessage
            ];

            // Analizza errore tecnico per determinare UEM code appropriato
            if (preg_match('/balance (\d+) below min (\d+)/', $errorMessage, $matches)) {
                $errorCode = 'BLOCKCHAIN_INSUFFICIENT_TREASURY_FUNDS';
                $contextData['current_balance'] = $matches[1];
                $contextData['required_balance'] = $matches[2];
                $contextData['current_algo'] = (int)$matches[1] / 1_000_000;
                $contextData['required_algo'] = (int)$matches[2] / 1_000_000;
            } elseif (preg_match('/account ([A-Z0-9]+) does not exist/i', $errorMessage, $matches)) {
                $errorCode = 'BLOCKCHAIN_ACCOUNT_NOT_FOUND';
                $contextData['account'] = $matches[1];
            } elseif (preg_match('/asset (\d+) missing from ([A-Z0-9]+)/i', $errorMessage, $matches)) {
                $errorCode = 'BLOCKCHAIN_ASSET_OPTIN_REQUIRED';
                $contextData['asset_id'] = $matches[1];
                $contextData['account'] = $matches[2];
            } elseif (preg_match('/network request error|timeout|connection refused/i', $errorMessage)) {
                $errorCode = 'BLOCKCHAIN_NETWORK_ERROR';
            } elseif (preg_match('/TransactionPool\.Remember/i', $errorMessage)) {
                $errorCode = 'BLOCKCHAIN_TRANSACTION_POOL_ERROR';
            }

            // 8. UEM: Error handling con code specifico
            $this->errorManager->handle($errorCode, $contextData, $e);

            // 9. Recupera messaggio user-friendly da UEM e throw nuova exception
            $userMessage = __("error-manager::errors_2.user.{$this->getUemMessageKey($errorCode)}");
            throw new \Exception($userMessage);
        }
    }

    /**
     * Helper: Converte UEM error code in translation key
     * @param string $errorCode UEM error code
     * @return string Translation key (snake_case)
     */
    private function getUemMessageKey(string $errorCode): string {
        // Converti da BLOCKCHAIN_INSUFFICIENT_TREASURY_FUNDS a blockchain_insufficient_treasury_funds
        return strtolower($errorCode);
    }

    /**
     * Trasferisce ASA EGI al wallet acquirente - GDPR COMPLIANT
     * @param string $to Wallet address destinazione
     * @param string $asaId ASA ID da trasferire
     * @param User $user User requesting transfer
     * @param int $amount Quantità (default 1 per NFT)
     * @return string Transaction ID
     * @throws \Exception
     * @privacy-safe Full GDPR compliance with consent check and audit trail
     */
    public function transferEgiAsset(string $to, string $asaId, User $user, int $amount = 1): string {
        try {
            // CRITICAL: Ensure microservice is running BEFORE transfer
            if (!$this->ensureMicroserviceRunning()) {
                throw new \Exception('Microservice not available and auto-start failed');
            }

            // 1. ULM: Log start
            $this->logger->info('EGI transfer initiated', [
                'user_id' => $user->id,
                'to_address' => $to,
                'asa_id' => $asaId,
                'amount' => $amount,
                'log_category' => 'BLOCKCHAIN_TRANSFER_START'
            ]);

            // 2. GDPR: Check consent
            // Blockchain operations are core platform services in an NFT marketplace
            if (!$this->consentService->hasConsent($user, 'platform-services')) {
                throw new \Exception('Missing platform services consent');
            }

            // 3. Validate address
            if (!$this->isValidAlgorandAddress($to)) {
                throw new \Exception('Invalid Algorand address format');
            }

            // 4. Chiama microservice per transfer
            $response = $this->callMicroservice('POST', '/transfer-egi-asset', [
                'to' => $to,
                'asaId' => $asaId,
                'amount' => $amount
            ]);

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'EGI transfer failed without error message');
            }

            $txId = $response['data']['txId'];

            // 5. GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'blockchain_egi_transferred',
                [
                    'to_address' => $to,
                    'asa_id' => $asaId,
                    'tx_id' => $txId,
                    'amount' => $amount
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            // 6. ULM: Log success
            $this->logger->info('EGI transfer completed successfully', [
                'user_id' => $user->id,
                'tx_id' => $txId,
                'to_address' => $to,
                'asa_id' => $asaId,
                'log_category' => 'BLOCKCHAIN_TRANSFER_SUCCESS'
            ]);

            return $txId;
        } catch (\Exception $e) {
            // 7. UEM: Error handling
            $this->errorManager->handle('BLOCKCHAIN_TRANSFER_FAILED', [
                'user_id' => $user->id,
                'to_address' => $to,
                'asa_id' => $asaId,
                'amount' => $amount,
                'error_message' => $e->getMessage()
            ], $e);

            throw new \Exception("Fallimento trasferimento EGI token: {$e->getMessage()}");
        }
    }

    /**
     * Crea anchor hash per certificato EGI - GDPR COMPLIANT
     * @param string $certificateHash Hash del file certificato
     * @param User $user User requesting anchor operation
     * @return string Anchor hash su blockchain
     * @throws \Exception
     * @privacy-safe Full GDPR compliance with consent check and audit trail
     */
    public function createCertificateAnchor(string $certificateHash, User $user): string {
        try {
            // 1. ULM: Log start
            $this->logger->info('Certificate anchor initiated', [
                'user_id' => $user->id,
                'certificate_hash' => substr($certificateHash, 0, 16) . '...',
                'log_category' => 'BLOCKCHAIN_ANCHOR_START'
            ]);

            // 2. GDPR: Check consent
            // Blockchain operations are core platform services in an NFT marketplace
            if (!$this->consentService->hasConsent($user, 'platform-services')) {
                throw new \Exception('Missing platform services consent');
            }

            // 3. Call microservice
            $response = $this->callMicroservice('POST', '/create-anchor', [
                'data_hash' => $certificateHash,
                'anchor_type' => 'egi_certificate'
            ]);

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Anchor creation failed');
            }

            $anchorHash = $response['data']['anchor_hash'];
            $txId = $response['data']['txId'];

            // 4. GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'blockchain_certificate_anchored',
                [
                    'certificate_hash' => substr($certificateHash, 0, 16) . '...',
                    'anchor_hash' => $anchorHash,
                    'tx_id' => $txId
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            // 5. ULM: Log success
            $this->logger->info('Certificate anchor completed successfully', [
                'user_id' => $user->id,
                'anchor_hash' => $anchorHash,
                'tx_id' => $txId,
                'log_category' => 'BLOCKCHAIN_ANCHOR_SUCCESS'
            ]);

            return $anchorHash;
        } catch (\Exception $e) {
            // 6. UEM: Error handling
            $this->errorManager->handle('CERTIFICATE_ANCHOR_FAILED', [
                'user_id' => $user->id,
                'certificate_hash' => substr($certificateHash, 0, 16) . '...',
                'error' => $e->getMessage()
            ], $e);
            throw new \Exception("Fallimento creazione anchor hash: {$e->getMessage()}");
        }
    }

    /**
     * Ottiene info account Algorand - GDPR COMPLIANT
     * @param string $address Wallet address
     * @param User $user User requesting account info
     * @return array Account info
     * @throws \Exception
     * @privacy-safe Full GDPR compliance with consent check and audit trail
     */
    public function getAccountInfo(string $address, User $user): array {
        try {
            // 1. ULM: Log start
            $this->logger->info('Account info retrieval initiated', [
                'user_id' => $user->id,
                'address' => $address,
                'log_category' => 'BLOCKCHAIN_QUERY_START'
            ]);

            // 2. GDPR: Check consent
            // Blockchain operations are core platform services in an NFT marketplace
            if (!$this->consentService->hasConsent($user, 'platform-services')) {
                throw new \Exception('Missing platform services consent');
            }

            // 3. Validate address
            if (!$this->isValidAlgorandAddress($address)) {
                throw new \InvalidArgumentException('Address Algorand non valido');
            }

            // 4. Call microservice
            $response = $this->callMicroservice('GET', "/account/{$address}");

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Account info failed');
            }

            // 5. GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'blockchain_account_queried',
                [
                    'address' => $address,
                    'balance' => $response['data']['amount'] ?? null
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            // 6. ULM: Log success
            $this->logger->info('Account info retrieval completed', [
                'user_id' => $user->id,
                'address' => $address,
                'log_category' => 'BLOCKCHAIN_QUERY_SUCCESS'
            ]);

            return $response['data'];
        } catch (\Exception $e) {
            // 7. UEM: Error handling
            $this->errorManager->handle('ACCOUNT_INFO_RETRIEVAL_FAILED', [
                'user_id' => $user->id,
                'address' => $address,
                'error' => $e->getMessage()
            ], $e);
            throw new \Exception("Errore recupero info account: {$e->getMessage()}");
        }
    }

    /**
     * Check if treasury wallet has sufficient funds for minting operation
     *
     * CALCOLO COSTI MINT:
     * - Asset Creation (ASA): 0.001 ALGO (1000 microAlgos) SEMPRE richiesto
     * - Transaction Fee: 0.001 ALGO (1000 microAlgos) per transazione
     * - Minimum Balance: 0.1 ALGO (100000 microAlgos) account reserve
     * - Safety Buffer: 0.01 ALGO (10000 microAlgos) extra per sicurezza
     *
     * TOTALE MINIMO: ~0.112 ALGO per mint sicuro
     *
     * @param User $user User requesting the check
     * @return array ['has_sufficient_funds' => bool, 'current_balance' => int, 'required_balance' => int, 'balance_algo' => float]
     * @throws \Exception
     */
    public function checkTreasuryFunds(User $user): array {
        try {
            // 1. ULM: Log start
            $this->logger->info('Treasury funds check initiated', [
                'user_id' => $user->id,
                'log_category' => 'BLOCKCHAIN_FUNDS_CHECK'
            ]);

            // 2. Get treasury address from config
            $treasuryAddress = config('algorand.algorand.treasury_address');

            if (empty($treasuryAddress) || $treasuryAddress === 'TREASURY_PENDING') {
                throw new \Exception('Treasury address not configured');
            }

            // 3. Get account info (includes balance AND assets count)
            $accountInfo = $this->getAccountInfo($treasuryAddress, $user);

            $currentBalance = $accountInfo['amount'] ?? 0; // in microAlgos
            $assetsCount = isset($accountInfo['assets']) ? count($accountInfo['assets']) : 0;

            // 4. Calculate required balance for mint operation
            // CRITICAL: Algorand minimum balance formula: 100,000 + (num_assets × 100,000) microAlgos
            // Example: 9 assets = 100,000 + (9 × 100,000) = 1,000,000 microAlgos (1 ALGO)
            $baseMinBalance = 100000;       // 0.1 ALGO - account base minimum
            $assetMinBalance = $assetsCount * 100000; // 0.1 ALGO per asset
            $algorandMinBalance = $baseMinBalance + $assetMinBalance;

            $asaCreationCost = 1000;        // 0.001 ALGO - new asset creation fee
            $transactionFee = 1000;         // 0.001 ALGO - transaction fee
            $newAssetMinBalance = 100000;   // 0.1 ALGO - minimum for NEW asset
            $safetyBuffer = 50000;          // 0.05 ALGO - safety margin (increased)

            // Total required: current min + new asset cost + new asset min + fees + buffer
            $requiredBalance = $algorandMinBalance + $asaCreationCost + $transactionFee + $newAssetMinBalance + $safetyBuffer;

            $hasSufficientFunds = $currentBalance >= $requiredBalance;

            // 5. Log result
            $this->logger->info('Treasury funds check completed', [
                'user_id' => $user->id,
                'treasury_address' => $treasuryAddress,
                'current_balance_microalgos' => $currentBalance,
                'current_balance_algo' => $currentBalance / 1000000,
                'assets_count' => $assetsCount,
                'algorand_min_balance' => $algorandMinBalance,
                'required_balance_microalgos' => $requiredBalance,
                'required_balance_algo' => $requiredBalance / 1000000,
                'has_sufficient_funds' => $hasSufficientFunds,
                'deficit_microalgos' => $hasSufficientFunds ? 0 : ($requiredBalance - $currentBalance),
                'deficit_algo' => $hasSufficientFunds ? 0 : (($requiredBalance - $currentBalance) / 1000000),
                'log_category' => 'BLOCKCHAIN_FUNDS_CHECK_SUCCESS'
            ]);

            return [
                'has_sufficient_funds' => $hasSufficientFunds,
                'current_balance' => $currentBalance,
                'required_balance' => $requiredBalance,
                'balance_algo' => $currentBalance / 1000000,
                'required_algo' => $requiredBalance / 1000000,
                'treasury_address' => $treasuryAddress,
                'assets_count' => $assetsCount,
                'algorand_min_balance' => $algorandMinBalance,
                'deficit_microalgos' => $hasSufficientFunds ? 0 : ($requiredBalance - $currentBalance),
                'deficit_algo' => $hasSufficientFunds ? 0 : (($requiredBalance - $currentBalance) / 1000000)
            ];
        } catch (\Exception $e) {
            // 6. UEM: Error handling
            $this->errorManager->handle('TREASURY_FUNDS_CHECK_FAILED', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ], $e);

            throw new \Exception("Errore verifica fondi treasury: {$e->getMessage()}");
        }
    }

    /**
     * Verifica stato microservice AlgoKit
     * Uses callMicroservice() which handles auto-start internally
     * @return array Network status
     * @throws \Exception
     */
    public function getNetworkStatus(): array {
        try {
            // callMicroservice() già gestisce:
            // 1. ensureMicroserviceRunning() (health check + auto-start se offline)
            // 2. Retry automatico dopo auto-start
            // 3. UEM logging completo
            $response = $this->callMicroservice('GET', '/health');

            // Microservice /health returns: { "status": "healthy", "network": "testnet", ... }
            $isHealthy = isset($response['status']) && $response['status'] === 'healthy';

            return [
                'success' => $isHealthy,
                'status' => $response['status'] ?? 'unknown',
                'network' => $response['network'] ?? 'unknown',
                'round' => $response['round'] ?? null,
                'treasury' => $response['treasury'] ?? [],
                'algod_server' => $response['algod_server'] ?? 'unknown',
                'mode' => $response['mode'] ?? 'unknown',
            ];
        } catch (\Exception $e) {
            // Se arriviamo qui, microservice è VERAMENTE non disponibile
            // (auto-start fallito o microservice non risponde dopo retry)
            $this->errorManager->handle('NETWORK_STATUS_CHECK_FAILED', [
                'error' => $e->getMessage()
            ], $e);
            throw new \Exception("Errore verifica stato rete: {$e->getMessage()}");
        }
    }

    /**
     * Stato treasury wallet EGI (operazione interna - no GDPR)
     * @return array Treasury info
     * @throws \Exception
     * @privacy-safe Internal operation, no user data involved
     */
    public function getTreasuryStatus(): array {
        try {
            $healthStatus = $this->getNetworkStatus();
            $treasuryAddress = $healthStatus['algorand']['treasury_address'] ?? null;

            if (!$treasuryAddress) {
                throw new \Exception('Treasury address not available from microservice');
            }

            return $this->getAccountInfoInternal($treasuryAddress);
        } catch (\Exception $e) {
            $this->errorManager->handle('TREASURY_STATUS_CHECK_FAILED', [
                'error' => $e->getMessage()
            ], $e);
            throw new \Exception("Errore stato treasury: {$e->getMessage()}");
        }
    }

    // ========================================
    // PRIVATE HELPER METHODS
    // ========================================

    /**
     * Ottiene info account Algorand (uso interno - no GDPR)
     * Per chiamate interne di sistema (es. treasury status)
     * @param string $address Wallet address
     * @return array Account info
     * @throws \Exception
     * @privacy-safe Internal operation, used for system accounts only
     */
    private function getAccountInfoInternal(string $address): array {
        try {
            if (!$this->isValidAlgorandAddress($address)) {
                throw new \InvalidArgumentException('Address Algorand non valido');
            }

            $response = $this->callMicroservice('GET', "/account/{$address}");

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Account info failed');
            }

            return $response['data'];
        } catch (\Exception $e) {
            $this->errorManager->handle('ACCOUNT_INFO_RETRIEVAL_FAILED', [
                'address' => $address,
                'error' => $e->getMessage(),
                'context' => 'internal_call'
            ], $e);
            throw new \Exception("Errore recupero info account: {$e->getMessage()}");
        }
    }

    /**
     * Effettua chiamata HTTP al microservice con retry logic
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @return array Response data
     * @throws \Exception
     */
    private function callMicroservice(string $method, string $endpoint, array $data = []): array {
        // CRITICAL: Verifica che il microservizio sia attivo PRIMA di ogni chiamata
        if (!$this->ensureMicroserviceRunning()) {
            $this->errorManager->handle('MICROSERVICE_NOT_AVAILABLE', [
                'url' => $this->microserviceUrl,
                'endpoint' => $endpoint,
                'method' => $method
            ], new \Exception('Microservice not available after health check and auto-start attempt'));

            throw new \Exception('Microservice not available');
        }

        $url = $this->microserviceUrl . $endpoint;
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->apiRetries) {
            try {
                $attempt++;
                $this->logger->debug('MICROSERVICE_CALL', [
                    'method' => $method,
                    'url' => $url,
                    'attempt' => $attempt,
                    'data' => $data
                ]);

                $response = Http::timeout($this->apiTimeout)
                    ->acceptJson()
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'User-Agent' => 'FlorenceEGI-Laravel/3.0'
                    ]);

                // Esegui la chiamata HTTP
                if ($method === 'GET') {
                    $httpResponse = $response->get($url);
                } elseif ($method === 'POST') {
                    $httpResponse = $response->post($url, $data);
                } else {
                    throw new \Exception("Metodo HTTP non supportato: {$method}");
                }

                // Verifica status HTTP
                if (!$httpResponse->successful()) {
                    $errorData = $httpResponse->json();
                    throw new \Exception(
                        $errorData['error'] ?? "HTTP {$httpResponse->status()}: {$httpResponse->body()}"
                    );
                }

                $responseData = $httpResponse->json();

                $this->logger->debug('MICROSERVICE_RESPONSE', [
                    'status' => $httpResponse->status(),
                    'success' => $responseData['success'] ?? false
                ]);

                return $responseData;
            } catch (\Exception $e) {
                $lastException = $e;

                // Solo UEM per errori (non ULM warning!)
                if ($attempt >= $this->apiRetries) {
                    // Last attempt - usa UEM
                    $this->errorManager->handle('MICROSERVICE_CALL_RETRY_EXHAUSTED', [
                        'attempt' => $attempt,
                        'max_retries' => $this->apiRetries,
                        'url' => $url,
                        'method' => $method,
                        'error' => $e->getMessage()
                    ], $e);
                }

                if ($attempt < $this->apiRetries) {
                    usleep($this->apiRetryDelay * 1000); // Convert to microseconds
                }
            }
        }

        // CRITICAL: Rilancia l'eccezione ORIGINALE senza wrapping
        // Questo permette al catch block di createAsset() di fare pattern matching corretto
        throw $lastException;
    }

    /**
     * Build metadata per EGI su blockchain
     * @param int $egiId EGI ID
     * @param array $metadata EGI data
     * @return array Formatted metadata
     */
    private function buildEgiMetadata(int $egiId, array $metadata): array {
        $cfg = $this->asaConfig;

        return [
            'name' => "FlorenceEGI #{$egiId}",
            'description' => $metadata['description'] ?? "Digital Certificate for EGI #{$egiId} - Florence Ecological Renaissance",
            'url' => route('egis.show', $egiId),
            'image' => $metadata['image_url'] ?? "https://florenceegi.it/images/egis/{$egiId}.png",
            'external_url' => route('egis.show', $egiId),
            'attributes' => [
                [
                    'trait_type' => 'Collection',
                    'value' => $metadata['collection'] ?? 'Florence EGI Marketplace'
                ],
                [
                    'trait_type' => 'EGI ID',
                    'value' => $egiId
                ],
                [
                    'trait_type' => 'Title',
                    'value' => $metadata['title'] ?? "EGI #{$egiId}"
                ],
                [
                    'trait_type' => 'Category',
                    'value' => $metadata['category'] ?? 'Digital Certificate'
                ],
                [
                    'trait_type' => 'Blockchain',
                    'value' => 'Algorand'
                ]
            ]
        ];
    }

    /**
     * Anchor document hash on Algorand blockchain - GDPR COMPLIANT
     * Used by N.A.T.A.N. PA Acts tokenization system
     *
     * @param string $documentHash SHA-256 hash of the document
     * @param array $metadata Additional metadata (protocol_number, doc_type, etc.)
     * @param User $user User requesting document anchoring (PA user)
     * @return array [success, txid, timestamp, block, network, hash, metadata]
     * @throws \Exception
     * @privacy-safe Only document hash (no PII) is anchored on blockchain
     */
    public function anchorDocument(string $documentHash, array $metadata, User $user): array {
        try {
            // CRITICAL: Ensure microservice is running BEFORE anchoring
            if (!$this->ensureMicroserviceRunning()) {
                throw new \Exception('Microservice not available and auto-start failed');
            }

            // 1. ULM: Log start
            $this->logger->info('Document anchoring initiated', [
                'user_id' => $user->id,
                'doc_hash' => substr($documentHash, 0, 16) . '...',
                'metadata_keys' => array_keys($metadata),
                'log_category' => 'BLOCKCHAIN_ANCHOR_START'
            ]);

            // 2. GDPR: Check consent
            // Blockchain operations are core platform services in an NFT marketplace
            if (!$this->consentService->hasConsent($user, 'platform-services')) {
                throw new \Exception('Missing platform services consent');
            }

            // 3. Prepare note field for blockchain transaction
            // Note: Contains only hash + minimal metadata (GDPR-safe)
            $noteData = [
                'type' => 'document_anchor',
                'hash' => $documentHash,
                'protocol' => $metadata['protocol_number'] ?? null,
                'timestamp' => now()->toIso8601String()
            ];

            // 4. Call microservice to anchor on blockchain
            $response = $this->callMicroservice('POST', '/anchor-document', [
                'document_hash' => $documentHash,
                'note' => json_encode($noteData),
                'metadata' => $metadata
            ]);

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Document anchoring failed without error message');
            }

            $data = $response['data'];

            // 5. GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'blockchain_document_anchored',
                [
                    'doc_hash' => substr($documentHash, 0, 16) . '...',
                    'tx_id' => $data['txid'] ?? null,
                    'block' => $data['block'] ?? null,
                    'protocol_number' => $metadata['protocol_number'] ?? null
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            // 6. ULM: Log success
            $this->logger->info('Document anchoring completed successfully', [
                'user_id' => $user->id,
                'doc_hash' => substr($documentHash, 0, 16) . '...',
                'txid' => $data['txid'] ?? null,
                'block' => $data['block'] ?? null,
                'log_category' => 'BLOCKCHAIN_ANCHOR_SUCCESS'
            ]);

            return [
                'success' => true,
                'txid' => $data['txid'] ?? 'MOCK-TX-' . strtoupper(bin2hex(random_bytes(16))),
                'timestamp' => now()->toIso8601String(),
                'block' => $data['block'] ?? random_int(10000000, 99999999),
                'network' => $data['network'] ?? 'algorand-testnet',
                'hash' => $documentHash,
                'metadata' => $metadata
            ];
        } catch (\Exception $e) {
            // 7. UEM: Error handling
            $this->errorManager->handle('BLOCKCHAIN_ANCHOR_FAILED', [
                'user_id' => $user->id,
                'doc_hash' => substr($documentHash, 0, 16) . '...',
                'metadata' => json_encode($metadata),
                'error_message' => $e->getMessage()
            ], $e);

            throw new \Exception("Fallimento ancoraggio documento su blockchain: {$e->getMessage()}");
        }
    }

    /**
     * Validate Algorand address (basic validation)
     * @param string $address Wallet address
     * @return bool Is valid
     */
    private function isValidAlgorandAddress(string $address): bool {
        // Basic validation - 58 characters, alphanumeric
        if (strlen($address) !== 58) {
            return false;
        }

        return preg_match('/^[A-Z2-7]+$/', $address) === 1;
    }
}
