<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Cache;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * KMS Health Check Service
 *
 * Pre-flight health check for KMS operations. Verifies encryption/decryption
 * works BEFORE attempting wallet operations. Caches results to avoid overhead.
 *
 * @package App\Services\Security
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - KMS Health Check)
 * @date 2025-10-29
 * @purpose Pre-flight validation of KMS availability and functionality
 */
class KmsHealthCheck {
    private KmsClient $kmsClient;
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;

    /** @var int Cache TTL in seconds (5 minutes) */
    private const CACHE_TTL = 300;

    /** @var string Cache key for health check result */
    private const CACHE_KEY = 'kms_health_check_status';

    public function __construct(
        KmsClient $kmsClient,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->kmsClient = $kmsClient;
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Check if KMS is healthy and operational
     *
     * Uses cached result if available (5 min TTL).
     * If cache miss, performs full encrypt/decrypt test.
     *
     * @param bool $forceRefresh Force new test, bypass cache
     * @return array ['healthy' => bool, 'error' => string|null, 'provider' => string, 'tested_at' => string]
     */
    public function check(bool $forceRefresh = false): array {
        // Try cache first (unless forced refresh)
        if (!$forceRefresh) {
            $cached = Cache::get(self::CACHE_KEY);
            if ($cached !== null) {
                $this->logger->debug('[KmsHealthCheck] Using cached health status', [
                    'healthy' => $cached['healthy'],
                    'cached_at' => $cached['tested_at']
                ]);
                return $cached;
            }
        }

        // Perform actual health test
        $result = $this->performHealthTest();

        // Cache result
        Cache::put(self::CACHE_KEY, $result, self::CACHE_TTL);

        return $result;
    }

    /**
     * Check if KMS is healthy (boolean shorthand)
     *
     * @param bool $forceRefresh Force new test, bypass cache
     * @return bool True if KMS is healthy and operational
     */
    public function isHealthy(bool $forceRefresh = false): bool {
        $status = $this->check($forceRefresh);
        return $status['healthy'];
    }

    /**
     * Ensure KMS is healthy or throw exception
     *
     * Use this as guard before wallet operations.
     * Throws exception with user-friendly message if KMS unavailable.
     *
     * @throws \RuntimeException If KMS is not healthy
     * @return void
     */
    public function ensureHealthy(): void {
        $status = $this->check();

        if (!$status['healthy']) {
            $this->logger->error('[KmsHealthCheck] KMS not healthy, blocking operation', [
                'error' => $status['error'],
                'provider' => $status['provider'],
                'tested_at' => $status['tested_at']
            ]);

            // Handle via UEM for proper error tracking and user notification
            $this->errorManager->handle('KMS_UNAVAILABLE', [
                'error' => $status['error'],
                'provider' => $status['provider'],
                'tested_at' => $status['tested_at']
            ]);

            throw new \RuntimeException(
                'Wallet encryption service is currently unavailable. Please try again in a few moments.'
            );
        }
    }

    /**
     * Invalidate cached health status
     *
     * Use after config changes or when forcing revalidation.
     *
     * @return void
     */
    public function invalidateCache(): void {
        Cache::forget(self::CACHE_KEY);
        $this->logger->info('[KmsHealthCheck] Health check cache invalidated');
    }

    /**
     * Perform actual health test (encrypt/decrypt cycle)
     *
     * @return array Health status with details
     */
    private function performHealthTest(): array {
        $startTime = microtime(true);

        try {
            $this->logger->info('[KmsHealthCheck] Starting health test');

            // Test data
            $testData = 'KMS_HEALTH_CHECK_' . time();
            $additionalData = 'health-check-context';

            // Test encrypt
            $encrypted = $this->kmsClient->secureEncrypt($testData, null, $additionalData);

            // Test decrypt
            $decrypted = $this->kmsClient->secureDecrypt($encrypted);

            // Verify integrity
            if ($decrypted !== $testData) {
                throw new \Exception('Data integrity check failed - decrypted data does not match original');
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $provider = $encrypted['encrypted_dek']['provider'] ?? 'unknown';

            $this->logger->info('[KmsHealthCheck] Health test PASSED', [
                'duration_ms' => $duration,
                'provider' => $provider
            ]);

            return [
                'healthy' => true,
                'error' => null,
                'provider' => $provider,
                'duration_ms' => $duration,
                'tested_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            $this->logger->error('[KmsHealthCheck] Health test FAILED', [
                'error' => $e->getMessage(),
                'duration_ms' => $duration
            ]);

            return [
                'healthy' => false,
                'error' => $e->getMessage(),
                'provider' => config('kms.provider', 'unknown'),
                'duration_ms' => $duration,
                'tested_at' => now()->toISOString()
            ];
        }
    }

    /**
     * Get detailed health status with diagnostics
     *
     * Includes configuration, provider info, and last test result.
     *
     * @return array Detailed health information
     */
    public function getDetailedStatus(): array {
        $health = $this->check();

        $kmsEnv = env('KMS_ENVIRONMENT', config('app.env'));
        $isProduction = $kmsEnv === 'production';

        return array_merge($health, [
            'configuration' => [
                'app_env' => config('app.env'),
                'kms_environment' => $kmsEnv,
                'mode' => $isProduction ? 'PRODUCTION' : 'DEVELOPMENT',
                'provider' => config('kms.provider', 'aws'),
                'kek_id' => config('kms.kek_id', 'egi-wallet-master-key'),
            ],
            'cache_ttl_seconds' => self::CACHE_TTL,
        ]);
    }
}
