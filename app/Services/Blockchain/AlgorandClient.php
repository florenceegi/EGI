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
class AlgorandClient
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected string $microserviceUrl;
    protected int $timeout;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;

        // Configuration
        $this->microserviceUrl = config('algorand.microservice_url', 'http://localhost:3000');
        $this->timeout = config('algorand.timeout', 10);
    }

    /**
     * Create new Algorand account
     *
     * @return array ['address' => string, 'mnemonic' => string, 'privateKeyBase64' => string]
     * @throws \Exception if account creation fails
     */
    public function createAccount(): array
    {
        try {
            // 1. ULM: Log request
            $this->logger->info('AlgorandClient: Creating new account', [
                'microservice_url' => $this->microserviceUrl,
                'log_category' => 'ALGORAND_ACCOUNT_CREATE'
            ]);

            // 2. Call microservice
            $response = Http::timeout($this->timeout)
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
    public function getAccountInfo(string $address): array
    {
        try {
            $this->logger->info('AlgorandClient: Getting account info', [
                'address' => $address,
                'log_category' => 'ALGORAND_ACCOUNT_INFO'
            ]);

            $response = Http::timeout($this->timeout)
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
    public function healthCheck(): bool
    {
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
}


