<?php

namespace App\Services\Security;

use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\User;

/**
 * Key Management Service Client for Envelope Encryption
 *
 * Implements enterprise-grade envelope encryption pattern using DEK/KEK architecture.
 * Provides abstraction layer for key management operations with production KMS support.
 *
 * Architecture:
 * - DEK (Data Encryption Key): 256-bit key for encrypting sensitive data
 * - KEK (Key Encryption Key): Master key stored in KMS for encrypting DEKs
 * - AEAD: XChaCha20-Poly1305 for authenticated encryption
 * - Secure Memory: sodium_memzero() for key cleanup
 *
 * @package App\Services\Security
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Wallet Security)
 * @date 2025-10-21
 * @purpose Enterprise envelope encryption for PA/Enterprise compliance
 */
class KmsClient {
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;

    /** @var bool Development mode flag - enables mock KMS */
    private bool $developmentMode;

    /** @var string KEK identifier for production KMS */
    private string $kekId;

    /** @var string Mock KEK for development (base64 encoded) */
    private string $mockKek;

    /**
     * Constructor with GDPR/Ultra compliance
     *
     * @param UltraLogManager $logger Ultra logging for security operations
     * @param ErrorManagerInterface $errorManager Ultra error handling
     * @param AuditLogService $auditService GDPR audit trail
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;

        // Environment configuration
        $this->developmentMode = config('app.env') !== 'production';
        $this->kekId = config('kms.kek_id', 'egi-wallet-master-key');

        // Initialize mock KEK for development
        if ($this->developmentMode) {
            $this->initializeMockKek();
        }

        $this->logger->info('KmsClient initialized', [
            'mode' => $this->developmentMode ? 'DEVELOPMENT' : 'PRODUCTION',
            'kek_id' => $this->kekId
        ]);
    }

    /**
     * Generate new Data Encryption Key (DEK)
     *
     * Creates cryptographically secure 256-bit key for data encryption.
     * Each DEK is unique per operation for maximum security.
     *
     * @return string Base64-encoded 256-bit DEK
     * @throws \Exception If random_bytes fails
     */
    public function generateDEK(): string {
        try {
            // Generate 256-bit (32 bytes) random key
            $dek = \random_bytes(32);
            $dekBase64 = base64_encode($dek);

            // Clear original key from memory
            sodium_memzero($dek);

            $this->logger->debug('DEK generated successfully', [
                'dek_length' => 32,
                'encoding' => 'base64'
            ]);

            return $dekBase64;
        } catch (\Exception $e) {
            $this->errorManager->handle('KMS_DEK_GENERATION_FAILED', [
                'error' => $e->getMessage(),
                'sodium_available' => extension_loaded('sodium')
            ], $e);

            throw $e;
        }
    }

    /**
     * Encrypt DEK using Key Encryption Key (KEK)
     *
     * Encrypts the DEK with the master KEK stored in KMS.
     * Uses envelope encryption pattern for secure key management.
     *
     * @param string $dekBase64 Base64-encoded DEK to encrypt
     * @param User|null $user Optional user context for audit logging
     * @return array Encrypted DEK data with metadata
     * @throws \Exception If encryption fails
     */
    public function encryptDEK(string $dekBase64, ?User $user = null): array {
        try {
            $this->logger->info('Encrypting DEK', [
                'user_id' => $user?->id,
                'dek_length' => strlen($dekBase64)
            ]);

            // Decode DEK from base64
            $dek = base64_decode($dekBase64);
            if ($dek === false) {
                throw new \InvalidArgumentException('Invalid DEK base64 encoding');
            }

            if ($this->developmentMode) {
                $result = $this->encryptDEKMock($dek);
            } else {
                $result = $this->encryptDEKProduction($dek);
            }

            // Clear DEK from memory
            sodium_memzero($dek);

            // Audit log for GDPR compliance
            if ($user) {
                $this->auditService->logUserAction(
                    $user,
                    'DEK encrypted',
                    ['kek_id' => $this->kekId],
                    GdprActivityCategory::ENCRYPTION_KEY_MANAGEMENT
                );
            }

            $this->logger->info('DEK encrypted successfully');

            return $result;
        } catch (\Exception $e) {
            $this->errorManager->handle('KMS_DEK_ENCRYPTION_FAILED', [
                'error' => $e->getMessage(),
                'user_id' => $user?->id,
                'kek_id' => $this->kekId
            ], $e);

            throw $e;
        }
    }

    /**
     * Decrypt DEK using Key Encryption Key (KEK)
     *
     * Decrypts the DEK using the master KEK from KMS.
     * Returns the plain DEK for data decryption operations.
     *
     * @param array $encryptedDekData Encrypted DEK data from encryptDEK()
     * @param User|null $user Optional user context for audit logging
     * @return string Base64-encoded plain DEK
     * @throws \Exception If decryption fails
     */
    public function decryptDEK(array $encryptedDekData, ?User $user = null): string {
        try {
            $this->logger->info('Decrypting DEK', [
                'user_id' => $user?->id,
                'kek_id' => $encryptedDekData['kek_id'] ?? 'unknown'
            ]);

            if ($this->developmentMode) {
                $dek = $this->decryptDEKMock($encryptedDekData);
            } else {
                $dek = $this->decryptDEKProduction($encryptedDekData);
            }

            $dekBase64 = base64_encode($dek);

            // Clear DEK from memory
            sodium_memzero($dek);

            // Audit log for GDPR compliance
            if ($user) {
                $this->auditService->logUserAction(
                    $user,
                    'DEK decrypted',
                    ['kek_id' => $encryptedDekData['kek_id'] ?? 'unknown'],
                    GdprActivityCategory::ENCRYPTION_KEY_MANAGEMENT
                );
            }

            $this->logger->info('DEK decrypted successfully');

            return $dekBase64;
        } catch (\Exception $e) {
            $this->errorManager->handle('KMS_DEK_DECRYPTION_FAILED', [
                'error' => $e->getMessage(),
                'user_id' => $user?->id,
                'kek_id' => $encryptedDekData['kek_id'] ?? 'unknown'
            ], $e);

            throw $e;
        }
    }

    /**
     * High-level secure encrypt operation
     *
     * Combines DEK generation, KEK encryption, and data encryption
     * in a single operation. Returns complete envelope encryption result.
     *
     * @param string $plaintext Data to encrypt
     * @param string $additionalData Optional additional authenticated data
     * @param User|null $user Optional user context for audit logging
     * @return array Complete envelope encryption result
     * @throws \Exception If encryption fails
     */
    public function secureEncrypt(string $plaintext, string $additionalData = '', ?User $user = null): array {
        try {
            $this->logger->info('Starting secure encryption', [
                'user_id' => $user?->id,
                'plaintext_length' => strlen($plaintext),
                'additional_data_length' => strlen($additionalData)
            ]);

            // Step 1: Generate DEK
            $dekBase64 = $this->generateDEK();

            // Step 2: Encrypt DEK with KEK
            $encryptedDekData = $this->encryptDEK($dekBase64, $user);

            // Step 3: Encrypt data with DEK using XChaCha20-Poly1305
            $dek = base64_decode($dekBase64);
            $nonce = \random_bytes(24); // XChaCha20-Poly1305 requires 24-byte nonce

            $ciphertext = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
                $plaintext,
                $additionalData,
                $nonce,
                $dek
            );

            // Clear DEK from memory
            sodium_memzero($dek);

            $result = [
                'ciphertext' => base64_encode($ciphertext),
                'nonce' => base64_encode($nonce),
                'additional_data' => $additionalData,
                'encrypted_dek' => $encryptedDekData,
                'algorithm' => 'xchacha20poly1305',
                'created_at' => now()->toISOString()
            ];

            $this->logger->info('Secure encryption completed successfully');

            return $result;
        } catch (\Exception $e) {
            $this->errorManager->handle('KMS_SECURE_ENCRYPTION_FAILED', [
                'error' => $e->getMessage(),
                'user_id' => $user?->id
            ], $e);

            throw $e;
        }
    }

    /**
     * High-level secure decrypt operation
     *
     * Reverses the secure encryption process: decrypts DEK with KEK,
     * then decrypts data with DEK. Returns original plaintext.
     *
     * @param array $encryptedData Complete envelope encryption result
     * @param User|null $user Optional user context for audit logging
     * @return string Original plaintext
     * @throws \Exception If decryption fails
     */
    public function secureDecrypt(array $encryptedData, ?User $user = null): string {
        try {
            $this->logger->info('Starting secure decryption', [
                'user_id' => $user?->id,
                'algorithm' => $encryptedData['algorithm'] ?? 'unknown'
            ]);

            // Validate required fields
            $requiredFields = ['ciphertext', 'nonce', 'encrypted_dek', 'algorithm'];
            foreach ($requiredFields as $field) {
                if (!isset($encryptedData[$field])) {
                    throw new \InvalidArgumentException("Missing required field: $field");
                }
            }

            if ($encryptedData['algorithm'] !== 'xchacha20poly1305') {
                throw new \InvalidArgumentException('Unsupported encryption algorithm');
            }

            // Step 1: Decrypt DEK with KEK
            $dekBase64 = $this->decryptDEK($encryptedData['encrypted_dek'], $user);

            // Step 2: Decrypt data with DEK
            $dek = base64_decode($dekBase64);
            $ciphertext = base64_decode($encryptedData['ciphertext']);
            $nonce = base64_decode($encryptedData['nonce']);
            $additionalData = $encryptedData['additional_data'] ?? '';

            $plaintext = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
                $ciphertext,
                $additionalData,
                $nonce,
                $dek
            );

            // Clear DEK from memory
            sodium_memzero($dek);

            if ($plaintext === false) {
                throw new \Exception('Decryption failed - invalid ciphertext or corrupted data');
            }

            $this->logger->info('Secure decryption completed successfully');

            return $plaintext;
        } catch (\Exception $e) {
            $this->errorManager->handle('KMS_SECURE_DECRYPTION_FAILED', [
                'error' => $e->getMessage(),
                'user_id' => $user?->id
            ], $e);

            throw $e;
        }
    }

    /**
     * Initialize mock KEK for development environment
     *
     * Generates a secure mock KEK that simulates production KMS behavior.
     * Only used in development mode for testing and development.
     *
     * @return void
     */
    private function initializeMockKek(): void {
        $configKey = 'kms.mock_kek';

        // Use existing mock KEK or generate new one
        $this->mockKek = config($configKey);

        if (!$this->mockKek) {
            // Generate new mock KEK (256-bit)
            $kek = \random_bytes(32);
            $this->mockKek = base64_encode($kek);
            sodium_memzero($kek);

            $this->logger->warning('Generated new mock KEK for development', [
                'kek_id' => $this->kekId,
                'action' => 'Add to config/kms.php for persistence'
            ]);
        }
    }

    /**
     * Mock DEK encryption for development
     *
     * Simulates production KMS behavior using local mock KEK.
     * Provides identical API to production for seamless development.
     *
     * @param string $dek Raw DEK bytes
     * @return array Encrypted DEK data
     * @throws \Exception If mock encryption fails
     */
    private function encryptDEKMock(string $dek): array {
        $kek = base64_decode($this->mockKek);
        $nonce = \random_bytes(24);

        $encryptedDek = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
            $dek,
            $this->kekId, // Additional authenticated data
            $nonce,
            $kek
        );

        sodium_memzero($kek);

        return [
            'encrypted_dek' => base64_encode($encryptedDek),
            'nonce' => base64_encode($nonce),
            'kek_id' => $this->kekId,
            'provider' => 'MOCK_KMS_DEVELOPMENT',
            'created_at' => now()->toISOString()
        ];
    }

    /**
     * Mock DEK decryption for development
     *
     * Reverses mock DEK encryption using local mock KEK.
     * Provides identical behavior to production KMS.
     *
     * @param array $encryptedDekData Encrypted DEK data from mock
     * @return string Raw DEK bytes
     * @throws \Exception If mock decryption fails
     */
    private function decryptDEKMock(array $encryptedDekData): string {
        $kek = base64_decode($this->mockKek);
        $encryptedDek = base64_decode($encryptedDekData['encrypted_dek']);
        $nonce = base64_decode($encryptedDekData['nonce']);
        $kekId = $encryptedDekData['kek_id'];

        $dek = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
            $encryptedDek,
            $kekId, // Additional authenticated data
            $nonce,
            $kek
        );

        sodium_memzero($kek);

        if ($dek === false) {
            throw new \Exception('Mock DEK decryption failed');
        }

        return $dek;
    }

    /**
     * Production DEK encryption using real KMS
     *
     * Integrates with production KMS (AWS KMS, Azure Key Vault, etc.).
     * Implements enterprise-grade key management with HSM backing.
     *
     * @param string $dek Raw DEK bytes
     * @return array Encrypted DEK data
     * @throws \Exception If production KMS encryption fails
     */
    private function encryptDEKProduction(string $dek): array {
        // TODO: Implement production KMS integration
        // Examples:
        // - AWS KMS: $kms->encrypt(['KeyId' => $kekId, 'Plaintext' => $dek])
        // - Azure Key Vault: $keyVault->encrypt($kekId, $dek)
        // - HashiCorp Vault: $vault->encrypt($kekId, $dek)

        throw new \Exception('Production KMS integration not yet implemented');
    }

    /**
     * Production DEK decryption using real KMS
     *
     * Decrypts DEK using production KMS with enterprise security.
     * Provides audit trails and access control integration.
     *
     * @param array $encryptedDekData Encrypted DEK data from production
     * @return string Raw DEK bytes
     * @throws \Exception If production KMS decryption fails
     */
    private function decryptDEKProduction(array $encryptedDekData): string {
        // TODO: Implement production KMS integration
        // Examples:
        // - AWS KMS: $kms->decrypt(['CiphertextBlob' => $encryptedDek])
        // - Azure Key Vault: $keyVault->decrypt($kekId, $encryptedDek)
        // - HashiCorp Vault: $vault->decrypt($kekId, $encryptedDek)

        throw new \Exception('Production KMS integration not yet implemented');
    }
}
