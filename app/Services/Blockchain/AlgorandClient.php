<?php

namespace App\Services\Blockchain;

use Illuminate\Support\Facades\Http;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode AlgorandClient: Communication with Algorand Node.js Microservice
 * 🎯 Purpose: Create real Algorand accounts for user wallets
 * 🛡️ Security: Communicates with internal microservice, never exposes mnemonics
 * 🧱 Core Logic: HTTP client + UEM error handling + ULM logging
 *
 * @package App\Services\Blockchain
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Wallet Security Module)
 * @date 2025-10-22
 * @purpose Secure wallet generation via Node.js algosdk
 */
class AlgorandClient {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected string $microserviceUrl;
    protected int $timeout;
    protected string $apiToken;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;

        // Configuration - use correct config key path
        $this->microserviceUrl = config('algorand.algokit_microservice.url', 'http://localhost:3001');
        $this->timeout = config('algorand.algokit_microservice.timeout', 30);
        $this->apiToken = (string) config('algorand.algokit_microservice.api_token', '');
    }

    /**
     * Create new Algorand account
     *
     * @return array ['address' => string, 'mnemonic' => string, 'privateKeyBase64' => string]
     * @throws \Exception if account creation fails
     */
    public function createAccount(): array {
        try {
            // 0. Ensure microservice is running (auto-start if needed)
            if (!$this->ensureMicroserviceRunning()) {
                throw new \Exception("Algorand microservice is not available and auto-start failed");
            }

            // 1. ULM: Log request
            $this->logger->info('AlgorandClient: Creating new account', [
                'microservice_url' => $this->microserviceUrl,
                'log_category' => 'ALGORAND_ACCOUNT_CREATE'
            ]);

            // 2. Call microservice
            $response = Http::timeout($this->timeout)
                ->withToken($this->apiToken)
                ->post("{$this->microserviceUrl}/create-account");

            // 3. Check response
            if (!$response->successful()) {
                throw new \Exception("Microservice returned status {$response->status()}: {$response->body()}");
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success']) {
                throw new \Exception("Microservice error: " . ($data['error'] ?? 'Unknown error'));
            }

            if (!isset($data['data']['address']) || !isset($data['data']['mnemonic'])) {
                throw new \Exception("Invalid response from microservice: missing address or mnemonic");
            }

            // 4. Validate address format (58 chars, Base32)
            $address = $data['data']['address'];
            if (strlen($address) !== 58 || !preg_match('/^[A-Z2-7]+$/', $address)) {
                throw new \Exception("Invalid Algorand address format: {$address}");
            }

            // 5. ULM: Log success
            $this->logger->info('AlgorandClient: Account created successfully', [
                'address' => $address,
                'log_category' => 'ALGORAND_ACCOUNT_CREATED'
            ]);

            return [
                'address' => $address,
                'mnemonic' => $data['data']['mnemonic'],
                'privateKeyBase64' => $data['data']['privateKeyBase64'] ?? null,
            ];
        } catch (\Exception $e) {
            // 6. ULM: Log error
            $this->logger->error('AlgorandClient: Account creation failed', [
                'error' => $e->getMessage(),
                'microservice_url' => $this->microserviceUrl,
                'log_category' => 'ALGORAND_ACCOUNT_ERROR'
            ]);

            // 7. UEM: Handle error
            $this->errorManager->handle('ALGORAND_ACCOUNT_CREATE_FAILED', [
                'microservice_url' => $this->microserviceUrl,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Get account information from blockchain
     *
     * @param string $address Algorand address
     * @return array Account info
     * @throws \Exception if request fails
     */
    public function getAccountInfo(string $address): array {
        try {
            // 0. Ensure microservice is running (auto-start if needed)
            if (!$this->ensureMicroserviceRunning()) {
                throw new \Exception("Algorand microservice is not available and auto-start failed");
            }

            $this->logger->info('AlgorandClient: Getting account info', [
                'address' => $address,
                'log_category' => 'ALGORAND_ACCOUNT_INFO'
            ]);

            $response = Http::timeout($this->timeout)
                ->withToken($this->apiToken)
                ->get("{$this->microserviceUrl}/account/{$address}");

            if (!$response->successful()) {
                throw new \Exception("Microservice returned status {$response->status()}: {$response->body()}");
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success']) {
                throw new \Exception("Microservice error: " . ($data['error'] ?? 'Unknown error'));
            }

            return $data['data'] ?? [];
        } catch (\Exception $e) {
            $this->logger->error('AlgorandClient: Get account info failed', [
                'address' => $address,
                'error' => $e->getMessage(),
                'log_category' => 'ALGORAND_ACCOUNT_INFO_ERROR'
            ]);

            $this->errorManager->handle('ALGORAND_ACCOUNT_INFO_FAILED', [
                'address' => $address,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Health check for microservice
     *
     * @return bool true if microservice is healthy
     */
    public function healthCheck(): bool {
        try {
            $response = Http::timeout(5)->get("{$this->microserviceUrl}/health");

            if ($response->successful()) {
                $data = $response->json();
                return isset($data['status']) && $data['status'] === 'healthy';
            }

            return false;
        } catch (\Exception $e) {
            $this->logger->warning('AlgorandClient: Health check failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Ensure microservice is running, auto-start if needed
     * (Logic copied from AlgorandService for consistency)
     *
     * @return bool true if microservice is running
     */
    protected function ensureMicroserviceRunning(): bool {
        // ULM: Trace health check start
        $this->logger->debug('AlgorandClient: Microservice health check starting', [
            'url' => $this->microserviceUrl
        ]);

        try {
            // Health check attempt
            $response = Http::timeout(5)->get($this->microserviceUrl . '/health');

            if ($response->successful()) {
                // ULM: Trace health check success
                $this->logger->debug('AlgorandClient: Microservice health check passed', [
                    'url' => $this->microserviceUrl,
                    'status' => 'healthy'
                ]);
                return true;
            }
        } catch (\Exception $e) {
            // ULM: Trace health check failure
            $this->logger->warning('AlgorandClient: Microservice health check failed', [
                'url' => $this->microserviceUrl,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e)
            ]);

            // UEM: Handle error (ALERT TEAM)
            $this->errorManager->handle('MICROSERVICE_NOT_REACHABLE', [
                'url' => $this->microserviceUrl,
                'error' => $e->getMessage()
            ], $e);

            // ULM: Trace auto-start attempt
            $this->logger->info('AlgorandClient: Attempting microservice auto-start', [
                'timestamp' => now()->toDateTimeString()
            ]);

            // Attempt auto-start
            return $this->attemptMicroserviceAutoStart();
        }

        return false;
    }

    /**
     * Attempt to auto-start Algorand microservice
     * (Logic copied from AlgorandService for consistency)
     *
     * @return bool true if auto-start successful
     */
    protected function attemptMicroserviceAutoStart(): bool {
        try {
            $microservicePath = base_path('algokit-microservice');
            $serverJs = $microservicePath . '/server.js';

            // Verify files exist
            if (!file_exists($serverJs)) {
                $this->errorManager->handle('MICROSERVICE_NOT_FOUND', [
                    'path' => $serverJs
                ], new \Exception('Microservice files missing'));
                return false;
            }

            // Build auto-start command
            $command = sprintf(
                'cd %s && node server.js > /tmp/algokit-autostart.log 2>&1 & echo $!',
                escapeshellarg($microservicePath)
            );

            // ULM: Trace auto-start command
            $this->logger->info('AlgorandClient: Microservice auto-start command prepared', [
                'command' => $command,
                'path' => $microservicePath,
                'timestamp' => now()->toDateTimeString()
            ]);

            // Execute command and capture PID
            $output = shell_exec($command);
            $pid = trim($output);

            // ULM: Trace PID capture
            $this->logger->debug('AlgorandClient: Microservice auto-start PID captured', [
                'pid' => $pid,
                'is_numeric' => is_numeric($pid)
            ]);

            if (!empty($pid) && is_numeric($pid)) {
                // Wait for startup
                sleep(3);

                // Verify process is still running
                $processCheck = trim(shell_exec("ps -p {$pid} -o pid= 2>/dev/null") ?? '');

                // ULM: Trace process verification
                $this->logger->debug('AlgorandClient: Microservice process verification', [
                    'pid' => $pid,
                    'process_check' => $processCheck,
                    'is_running' => !empty($processCheck)
                ]);

                if (!empty($processCheck)) {
                    // Verify health check after startup with retry
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
                            sleep(2); // Wait before retry
                        }
                    }
                }
            }

            // UEM: Auto-start failed
            $this->errorManager->handle('MICROSERVICE_AUTO_START_FAILED', [
                'pid' => $pid ?? 'unknown',
                'path' => $microservicePath
            ], new \Exception('Microservice auto-start completed but health check failed'));

            return false;
        } catch (\Exception $e) {
            // ULM: Log auto-start error
            $this->logger->error('AlgorandClient: Microservice auto-start exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Fund a wallet with ALGO from Treasury
     *
     * Enables user wallets to perform opt-in operations for ASAs.
     * Default: 0.3 ALGO (0.1 min balance + 0.1 per opt-in + 0.1 margin)
     *
     * @param string $address The Algorand address to fund
     * @param int|null $amountMicroAlgos Amount in microAlgos (default: 300000 = 0.3 ALGO)
     * @return array ['txId' => string, 'amount' => int, 'block' => int]
     * @throws \Exception if funding fails
     */
    public function fundWallet(string $address, ?int $amountMicroAlgos = null): array {
        try {
            // 0. Ensure microservice is running
            if (!$this->ensureMicroserviceRunning()) {
                throw new \Exception("Algorand microservice is not available");
            }

            // Use config or default
            $amount = $amountMicroAlgos ?? config('algorand.wallet_fund_amount', 300000);

            // 1. ULM: Log request
            $this->logger->info('AlgorandClient: Funding wallet', [
                'address' => $address,
                'amount_micro_algos' => $amount,
                'amount_algo' => $amount / 1000000,
                'log_category' => 'ALGORAND_FUND_WALLET'
            ]);

            // 2. Call microservice
            $response = Http::timeout($this->timeout + 10) // Extra time for blockchain confirmation
                ->withToken($this->apiToken)
                ->post("{$this->microserviceUrl}/fund-wallet", [
                    'address' => $address,
                    'amount' => $amount
                ]);

            // 3. Check response
            if (!$response->successful()) {
                throw new \Exception("Microservice returned status {$response->status()}: {$response->body()}");
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success']) {
                throw new \Exception("Microservice error: " . ($data['error'] ?? 'Unknown error'));
            }

            // 4. ULM: Log success
            $this->logger->info('AlgorandClient: Wallet funded successfully', [
                'address' => $address,
                'txId' => $data['data']['txId'],
                'amount' => $data['data']['amount'],
                'block' => $data['data']['block'],
                'log_category' => 'ALGORAND_FUND_WALLET_SUCCESS'
            ]);

            return [
                'txId' => $data['data']['txId'],
                'amount' => $data['data']['amount'],
                'amount_algo' => $data['data']['amount_algo'],
                'block' => $data['data']['block'],
                'from' => $data['data']['from'],
            ];
        } catch (\Exception $e) {
            // 5. ULM: Log error
            $this->logger->error('AlgorandClient: Wallet funding failed', [
                'address' => $address,
                'error' => $e->getMessage(),
                'log_category' => 'ALGORAND_FUND_WALLET_ERROR'
            ]);

            // 6. UEM: Handle error
            $this->errorManager->handle('ALGORAND_FUND_WALLET_FAILED', [
                'address' => $address,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Opt-in to a single ASA (EGI)
     *
     * The user wallet must opt-in to an ASA before it can receive it.
     * Requires the user's mnemonic to sign the transaction.
     *
     * @param string $userMnemonic The user's 25-word mnemonic
     * @param int $asaId The ASA ID to opt-in to
     * @return array ['txId' => string, 'asa_id' => int, 'block' => int]
     * @throws \Exception if opt-in fails
     */
    public function optInToAsa(string $userMnemonic, int $asaId): array {
        try {
            // 0. Ensure microservice is running
            if (!$this->ensureMicroserviceRunning()) {
                throw new \Exception("Algorand microservice is not available");
            }

            // 1. ULM: Log request (no mnemonic in logs!)
            $this->logger->info('AlgorandClient: Opt-in to ASA', [
                'asa_id' => $asaId,
                'log_category' => 'ALGORAND_OPT_IN_ASA'
            ]);

            // 2. Call microservice
            $response = Http::timeout($this->timeout + 10)
                ->withToken($this->apiToken)
                ->post("{$this->microserviceUrl}/opt-in-asa", [
                    'user_mnemonic' => $userMnemonic,
                    'asa_id' => $asaId
                ]);

            // 3. Check response
            if (!$response->successful()) {
                throw new \Exception("Microservice returned status {$response->status()}: {$response->body()}");
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success']) {
                throw new \Exception("Microservice error: " . ($data['error'] ?? 'Unknown error'));
            }

            // 4. ULM: Log success
            $this->logger->info('AlgorandClient: Opt-in successful', [
                'txId' => $data['data']['txId'],
                'asa_id' => $data['data']['asa_id'],
                'block' => $data['data']['block'],
                'log_category' => 'ALGORAND_OPT_IN_ASA_SUCCESS'
            ]);

            return [
                'txId' => $data['data']['txId'],
                'user_address' => $data['data']['user_address'],
                'asa_id' => $data['data']['asa_id'],
                'block' => $data['data']['block'],
            ];
        } catch (\Exception $e) {
            // 5. ULM: Log error
            $this->logger->error('AlgorandClient: Opt-in failed', [
                'asa_id' => $asaId,
                'error' => $e->getMessage(),
                'log_category' => 'ALGORAND_OPT_IN_ASA_ERROR'
            ]);

            // 6. UEM: Handle error
            $this->errorManager->handle('ALGORAND_OPT_IN_FAILED', [
                'asa_id' => $asaId,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Batch opt-in to multiple ASAs (EGIs)
     *
     * More efficient than individual opt-ins - uses Algorand atomic transaction groups.
     * Maximum 16 ASAs per batch (Algorand protocol limit).
     *
     * @param string $userMnemonic The user's 25-word mnemonic
     * @param array $asaIds Array of ASA IDs to opt-in to
     * @return array ['groupTxId' => string, 'asa_ids' => array, 'count' => int, 'block' => int]
     * @throws \Exception if batch opt-in fails
     */
    public function batchOptInToAsas(string $userMnemonic, array $asaIds): array {
        try {
            // Validate batch size
            if (count($asaIds) > 16) {
                throw new \Exception("Maximum 16 ASAs per batch (Algorand protocol limit)");
            }

            if (empty($asaIds)) {
                throw new \Exception("No ASA IDs provided");
            }

            // 0. Ensure microservice is running
            if (!$this->ensureMicroserviceRunning()) {
                throw new \Exception("Algorand microservice is not available");
            }

            // 1. ULM: Log request
            $this->logger->info('AlgorandClient: Batch opt-in to ASAs', [
                'asa_count' => count($asaIds),
                'asa_ids' => $asaIds,
                'log_category' => 'ALGORAND_BATCH_OPT_IN_ASA'
            ]);

            // 2. Call microservice
            $response = Http::timeout($this->timeout + 20) // Extra time for batch
                ->withToken($this->apiToken)
                ->post("{$this->microserviceUrl}/batch-opt-in-asa", [
                    'user_mnemonic' => $userMnemonic,
                    'asa_ids' => $asaIds
                ]);

            // 3. Check response
            if (!$response->successful()) {
                throw new \Exception("Microservice returned status {$response->status()}: {$response->body()}");
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success']) {
                throw new \Exception("Microservice error: " . ($data['error'] ?? 'Unknown error'));
            }

            // 4. ULM: Log success
            $this->logger->info('AlgorandClient: Batch opt-in successful', [
                'groupTxId' => $data['data']['groupTxId'],
                'count' => $data['data']['count'],
                'block' => $data['data']['block'],
                'log_category' => 'ALGORAND_BATCH_OPT_IN_ASA_SUCCESS'
            ]);

            return [
                'groupTxId' => $data['data']['groupTxId'],
                'user_address' => $data['data']['user_address'],
                'asa_ids' => $data['data']['asa_ids'],
                'count' => $data['data']['count'],
                'block' => $data['data']['block'],
            ];
        } catch (\Exception $e) {
            // 5. ULM: Log error
            $this->logger->error('AlgorandClient: Batch opt-in failed', [
                'asa_count' => count($asaIds),
                'error' => $e->getMessage(),
                'log_category' => 'ALGORAND_BATCH_OPT_IN_ASA_ERROR'
            ]);

            // 6. UEM: Handle error
            $this->errorManager->handle('ALGORAND_BATCH_OPT_IN_FAILED', [
                'asa_count' => count($asaIds),
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Transfer a single ASA (EGI) from Treasury to user wallet
     *
     * REQUIRES: User must have already opted-in to the ASA
     *
     * @param string $toAddress The recipient's Algorand address
     * @param int $asaId The ASA ID to transfer
     * @param int $amount Amount to transfer (default: 1 for NFTs)
     * @return array ['txId' => string, 'asa_id' => int, 'block' => int]
     * @throws \Exception if transfer fails
     */
    public function transferAsa(string $toAddress, int $asaId, int $amount = 1): array {
        try {
            // 0. Ensure microservice is running
            if (!$this->ensureMicroserviceRunning()) {
                throw new \Exception("Algorand microservice is not available");
            }

            // 1. ULM: Log request
            $this->logger->info('AlgorandClient: Transfer ASA', [
                'to_address' => $toAddress,
                'asa_id' => $asaId,
                'amount' => $amount,
                'log_category' => 'ALGORAND_TRANSFER_ASA'
            ]);

            // 2. Call microservice
            $response = Http::timeout($this->timeout + 10)
                ->withToken($this->apiToken)
                ->post("{$this->microserviceUrl}/transfer-asa", [
                    'to_address' => $toAddress,
                    'asa_id' => $asaId,
                    'amount' => $amount
                ]);

            // 3. Check response
            if (!$response->successful()) {
                throw new \Exception("Microservice returned status {$response->status()}: {$response->body()}");
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success']) {
                throw new \Exception("Microservice error: " . ($data['error'] ?? 'Unknown error'));
            }

            // 4. ULM: Log success
            $this->logger->info('AlgorandClient: Transfer successful', [
                'txId' => $data['data']['txId'],
                'to' => $data['data']['to'],
                'asa_id' => $data['data']['asa_id'],
                'block' => $data['data']['block'],
                'log_category' => 'ALGORAND_TRANSFER_ASA_SUCCESS'
            ]);

            return [
                'txId' => $data['data']['txId'],
                'from' => $data['data']['from'],
                'to' => $data['data']['to'],
                'asa_id' => $data['data']['asa_id'],
                'amount' => $data['data']['amount'],
                'block' => $data['data']['block'],
            ];
        } catch (\Exception $e) {
            // 5. ULM: Log error
            $this->logger->error('AlgorandClient: Transfer failed', [
                'to_address' => $toAddress,
                'asa_id' => $asaId,
                'error' => $e->getMessage(),
                'log_category' => 'ALGORAND_TRANSFER_ASA_ERROR'
            ]);

            // 6. UEM: Handle error
            $this->errorManager->handle('ALGORAND_TRANSFER_ASA_FAILED', [
                'to_address' => $toAddress,
                'asa_id' => $asaId,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Batch transfer multiple ASAs (EGIs) from Treasury to user wallet
     *
     * More efficient than individual transfers - uses Algorand atomic transaction groups.
     * Maximum 16 ASAs per batch (Algorand protocol limit).
     * REQUIRES: User must have already opted-in to ALL ASAs
     *
     * @param string $toAddress The recipient's Algorand address
     * @param array $asaIds Array of ASA IDs to transfer
     * @return array ['groupTxId' => string, 'asa_ids' => array, 'count' => int, 'block' => int]
     * @throws \Exception if batch transfer fails
     */
    public function batchTransferAsas(string $toAddress, array $asaIds): array {
        try {
            // Validate batch size
            if (count($asaIds) > 16) {
                throw new \Exception("Maximum 16 ASAs per batch (Algorand protocol limit)");
            }

            if (empty($asaIds)) {
                throw new \Exception("No ASA IDs provided");
            }

            // 0. Ensure microservice is running
            if (!$this->ensureMicroserviceRunning()) {
                throw new \Exception("Algorand microservice is not available");
            }

            // 1. ULM: Log request
            $this->logger->info('AlgorandClient: Batch transfer ASAs', [
                'to_address' => $toAddress,
                'asa_count' => count($asaIds),
                'asa_ids' => $asaIds,
                'log_category' => 'ALGORAND_BATCH_TRANSFER_ASA'
            ]);

            // 2. Call microservice
            $response = Http::timeout($this->timeout + 20) // Extra time for batch
                ->withToken($this->apiToken)
                ->post("{$this->microserviceUrl}/batch-transfer-asa", [
                    'to_address' => $toAddress,
                    'asa_ids' => $asaIds
                ]);

            // 3. Check response
            if (!$response->successful()) {
                throw new \Exception("Microservice returned status {$response->status()}: {$response->body()}");
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success']) {
                throw new \Exception("Microservice error: " . ($data['error'] ?? 'Unknown error'));
            }

            // 4. ULM: Log success
            $this->logger->info('AlgorandClient: Batch transfer successful', [
                'groupTxId' => $data['data']['groupTxId'],
                'to' => $data['data']['to'],
                'count' => $data['data']['count'],
                'block' => $data['data']['block'],
                'log_category' => 'ALGORAND_BATCH_TRANSFER_ASA_SUCCESS'
            ]);

            return [
                'groupTxId' => $data['data']['groupTxId'],
                'from' => $data['data']['from'],
                'to' => $data['data']['to'],
                'asa_ids' => $data['data']['asa_ids'],
                'count' => $data['data']['count'],
                'block' => $data['data']['block'],
            ];
        } catch (\Exception $e) {
            // 5. ULM: Log error
            $this->logger->error('AlgorandClient: Batch transfer failed', [
                'to_address' => $toAddress,
                'asa_count' => count($asaIds),
                'error' => $e->getMessage(),
                'log_category' => 'ALGORAND_BATCH_TRANSFER_ASA_ERROR'
            ]);

            // 6. UEM: Handle error
            $this->errorManager->handle('ALGORAND_BATCH_TRANSFER_ASA_FAILED', [
                'to_address' => $toAddress,
                'asa_count' => count($asaIds),
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }
}
