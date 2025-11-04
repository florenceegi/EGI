<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

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
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 3.0.0 - EGI MARKETPLACE INTEGRATION
 * @date 2025-10-07
 * @purpose Laravel HTTP client bridge to AlgoKit microservice for EGI
 */
class AlgorandService
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private string $microserviceUrl;
    private int $apiTimeout;
    private int $apiRetries;
    private int $apiRetryDelay;
    private array $config;
    private array $asaConfig;

    /**
     * AlgorandService constructor.
     * Carica config e imposta HTTP client per microservice
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;

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
     * Crea un nuovo ASA per EGI su blockchain
     * @param int $egiId EGI ID dal database
     * @param array $metadata Metadata EGI
     * @return array [asaId, txId, certificate_number, asset_url, treasury_address]
     * @throws \Exception
     */
    public function mintEgi(int $egiId, array $metadata): array
    {
        $this->logger->info('ALGORAND_EGI_MINT_START', ['egi_id' => $egiId]);

        try {
            // CRITICAL: Ensure microservice is running before minting
            if (!$this->ensureMicroserviceRunning()) {
                throw new \Exception('AlgoKit microservice is not available and auto-start failed. Cannot proceed with minting.');
            }

            // Prepara metadata per il certificato EGI
            $egiMetadata = $this->buildEgiMetadata($egiId, $metadata);

            // Chiama microservice per mint
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

            $this->logger->info('ALGORAND_EGI_MINT_SUCCESS', compact('egiId', 'asaId', 'txId'));

            return [
                'asaId' => $asaId,
                'txId' => $txId,
                'certificate_number' => $data['certificate_number'] ?? null,
                'asset_url' => $data['asset_url'] ?? null,
                'treasury_address' => $data['treasury_address'] ?? null
            ];
        } catch (\Exception $e) {
            $this->logger->error('ALGORAND_EGI_MINT_FAILED', ['error' => $e->getMessage(), 'egi_id' => $egiId]);
            throw new \Exception("Fallimento creazione EGI token ASA: {$e->getMessage()}");
        }
    }

    /**
     * Trasferisce ASA EGI al wallet acquirente
     * @param string $to Wallet address destinazione
     * @param string $asaId ASA ID da trasferire
     * @param int $amount Quantità (default 1 per NFT)
     * @return string Transaction ID
     * @throws \Exception
     */
    public function transferEgiAsset(string $to, string $asaId, int $amount = 1): string
    {
        $this->logger->info('ALGORAND_EGI_TRANSFER_START', compact('to', 'asaId', 'amount'));

        try {
            if (!$this->isValidAlgorandAddress($to)) {
                throw new \InvalidArgumentException('Address Algorand non valido');
            }

            // Chiama microservice per transfer
            $response = $this->callMicroservice('POST', '/transfer-egi-asset', [
                'to' => $to,
                'asaId' => $asaId,
                'amount' => $amount
            ]);

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'EGI transfer failed without error message');
            }

            $txId = $response['data']['txId'];

            $this->logger->info('ALGORAND_EGI_TRANSFER_SUCCESS', ['txId' => $txId]);
            return $txId;
        } catch (\Exception $e) {
            $this->logger->error('ALGORAND_EGI_TRANSFER_FAILED', ['error' => $e->getMessage()]);
            throw new \Exception("Fallimento trasferimento EGI token: {$e->getMessage()}");
        }
    }

    /**
     * Crea anchor hash per certificato EGI
     * @param string $certificateHash Hash del file certificato
     * @return string Anchor hash su blockchain
     * @throws \Exception
     */
    public function createCertificateAnchor(string $certificateHash): string
    {
        $this->logger->info('ALGORAND_ANCHOR_START', ['certificate_hash' => $certificateHash]);

        try {
            $response = $this->callMicroservice('POST', '/create-anchor', [
                'data_hash' => $certificateHash,
                'anchor_type' => 'egi_certificate'
            ]);

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Anchor creation failed');
            }

            $anchorHash = $response['data']['anchor_hash'];
            $txId = $response['data']['txId'];

            $this->logger->info('ALGORAND_ANCHOR_SUCCESS', compact('anchorHash', 'txId'));
            return $anchorHash;
        } catch (\Exception $e) {
            $this->logger->error('ALGORAND_ANCHOR_FAILED', ['error' => $e->getMessage()]);
            throw new \Exception("Fallimento creazione anchor hash: {$e->getMessage()}");
        }
    }

    /**
     * Ottiene info account Algorand
     * @param string $address Wallet address
     * @return array Account info
     * @throws \Exception
     */
    public function getAccountInfo(string $address): array
    {
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
            $this->logger->error('ALGORAND_ACCOUNT_INFO_FAILED', ['error' => $e->getMessage()]);
            throw new \Exception("Errore recupero info account: {$e->getMessage()}");
        }
    }

    /**
     * Verifica stato microservice AlgoKit
     * @return array Network status
     * @throws \Exception
     */
    public function getNetworkStatus(): array
    {
        try {
            $response = $this->callMicroservice('GET', '/health');

            return [
                'success' => $response['success'],
                'microservice' => $response['service'] ?? 'AlgoKit Microservice',
                'version' => $response['version'] ?? 'Unknown',
                'algorand' => $response['algorand'] ?? [],
                'timestamp' => $response['timestamp'] ?? now()->toISOString()
            ];
        } catch (\Exception $e) {
            $this->logger->error('NETWORK_STATUS_FAILED', ['error' => $e->getMessage()]);
            throw new \Exception("Errore verifica stato rete: {$e->getMessage()}");
        }
    }

    /**
     * Stato treasury wallet EGI
     * @return array Treasury info
     * @throws \Exception
     */
    public function getTreasuryStatus(): array
    {
        try {
            $healthStatus = $this->getNetworkStatus();
            $treasuryAddress = $healthStatus['algorand']['treasury_address'] ?? null;

            if (!$treasuryAddress) {
                throw new \Exception('Treasury address not available from microservice');
            }

            return $this->getAccountInfo($treasuryAddress);
        } catch (\Exception $e) {
            $this->logger->error('TREASURY_STATUS_FAILED', ['error' => $e->getMessage()]);
            throw new \Exception("Errore stato treasury: {$e->getMessage()}");
        }
    }

    // ========================================
    // PRIVATE HELPER METHODS
    // ========================================

    /**
     * Effettua chiamata HTTP al microservice con retry logic
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @return array Response data
     * @throws \Exception
     */
    private function callMicroservice(string $method, string $endpoint, array $data = []): array
    {
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
                $this->logger->warning('MICROSERVICE_CALL_FAILED', [
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                    'will_retry' => $attempt < $this->apiRetries
                ]);

                if ($attempt < $this->apiRetries) {
                    usleep($this->apiRetryDelay * 1000); // Convert to microseconds
                }
            }
        }

        throw new \Exception(
            "Microservice call failed after {$this->apiRetries} attempts. Last error: " .
                ($lastException ? $lastException->getMessage() : 'Unknown error')
        );
    }

    /**
     * Build metadata per EGI su blockchain
     * @param int $egiId EGI ID
     * @param array $metadata EGI data
     * @return array Formatted metadata
     */
    private function buildEgiMetadata(int $egiId, array $metadata): array
    {
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
     * Validate Algorand address (basic validation)
     * @param string $address Wallet address
     * @return bool Is valid
     */
    private function isValidAlgorandAddress(string $address): bool
    {
        // Basic validation - 58 characters, alphanumeric
        if (strlen($address) !== 58) {
            return false;
        }

        return preg_match('/^[A-Z2-7]+$/', $address) === 1;
    }

    /**
     * Ensure microservice is running, attempt auto-start if down
     * 
     * @return bool true if microservice is available
     */
    protected function ensureMicroserviceRunning(): bool
    {
        // ULM: Trace health check start
        $this->logger->debug('AlgorandService: Microservice health check starting', [
            'url' => $this->microserviceUrl
        ]);

        try {
            // Health check attempt
            $response = Http::timeout(5)->get($this->microserviceUrl . '/health');

            if ($response->successful()) {
                // ULM: Trace health check success
                $this->logger->debug('AlgorandService: Microservice health check passed', [
                    'url' => $this->microserviceUrl,
                    'status' => 'healthy'
                ]);
                return true;
            }
        } catch (\Exception $e) {
            // ULM: Trace health check failure
            $this->logger->warning('AlgorandService: Microservice health check failed', [
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
            $this->logger->info('AlgorandService: Attempting microservice auto-start', [
                'timestamp' => now()->toDateTimeString()
            ]);

            // Attempt auto-start
            return $this->attemptMicroserviceAutoStart();
        }

        return false;
    }

    /**
     * Attempt to auto-start Algorand microservice
     *
     * @return bool true if auto-start successful
     */
    protected function attemptMicroserviceAutoStart(): bool
    {
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
            $this->logger->info('AlgorandService: Microservice auto-start command prepared', [
                'command' => $command,
                'path' => $microservicePath,
                'timestamp' => now()->toDateTimeString()
            ]);

            // Execute command and capture PID
            $output = shell_exec($command);
            $pid = trim($output);

            // ULM: Trace PID capture
            $this->logger->debug('AlgorandService: Microservice auto-start PID captured', [
                'pid' => $pid,
                'is_numeric' => is_numeric($pid)
            ]);

            if (!empty($pid) && is_numeric($pid)) {
                // Wait for startup
                sleep(3);

                // Verify process is still running
                $processCheck = trim(shell_exec("ps -p {$pid} -o pid= 2>/dev/null"));

                // ULM: Trace process verification
                $this->logger->debug('AlgorandService: Microservice process verification', [
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
            $this->logger->error('AlgorandService: Microservice auto-start exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
}
