<?php

namespace App\Services\Coa;

use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Service: Cryptographic Hashing for CoA
 * 🎯 Purpose: Provide secure hashing functions for CoA data integrity
 * 🛡️ Privacy: No personal data handling, cryptographic operations only
 * 🧱 Core Logic: Manages hash generation, verification, and integrity checks
 *
 * @package App\Services\Coa
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-18
 * @purpose Data integrity verification for certificate authenticity
 */
class HashingService {
    /**
     * Logger instance for audit trail
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Error manager for robust error handling
     * @var ErrorManagerInterface
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * Default hashing algorithm
     */
    protected const DEFAULT_ALGORITHM = 'sha256';

    /**
     * Available hashing algorithms
     */
    protected const AVAILABLE_ALGORITHMS = [
        'sha256' => 'SHA-256',
        'sha384' => 'SHA-384',
        'sha512' => 'SHA-512',
        'sha3-256' => 'SHA3-256',
        'sha3-512' => 'SHA3-512'
    ];

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @privacy-safe No personal data dependencies
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Generate hash for any data
     *
     * @param mixed $data Data to hash
     * @param string $algorithm Algorithm to use
     * @return string Generated hash
     * @privacy-safe Hash generation only, no data storage
     *
     * @oracode-dimension governance
     * @value-flow Creates cryptographic fingerprint of data
     * @community-impact Enables data integrity verification
     * @transparency-level High - hash algorithms are publicly documented
     * @narrative-coherence Links data to its cryptographic representation
     */
    public function generateHash($data, string $algorithm = self::DEFAULT_ALGORITHM): string {
        try {
            // Validate algorithm
            if (!array_key_exists($algorithm, self::AVAILABLE_ALGORITHMS)) {
                throw new \Exception("Unsupported hashing algorithm: {$algorithm}");
            }

            // Normalize data to string
            $normalizedData = $this->normalizeData($data);

            // Generate hash
            $hash = hash($algorithm, $normalizedData);

            $this->logger->info('[CoA Hash] Hash generated', [
                'algorithm' => $algorithm,
                'data_length' => strlen($normalizedData),
                'hash_length' => strlen($hash),
                'hash_prefix' => substr($hash, 0, 8) . '...'
            ]);

            return $hash;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_HASH_GENERATION_ERROR', [
                'algorithm' => $algorithm,
                'data_type' => gettype($data),
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Generate hash for traits data specifically
     *
     * @param array $traitsData Traits data array
     * @param string $algorithm Algorithm to use
     * @return string Generated hash
     * @privacy-safe Hash generation for artwork metadata only
     */
    public function generateTraitsHash(array $traitsData, string $algorithm = self::DEFAULT_ALGORITHM): string {
        try {
            // Sort array to ensure consistent hashing
            $sortedData = $this->sortArrayRecursively($traitsData);

            // Generate hash
            $hash = $this->generateHash($sortedData, $algorithm);

            $this->logger->info('[CoA Hash] Traits hash generated', [
                'algorithm' => $algorithm,
                'traits_count' => count($traitsData),
                'hash' => substr($hash, 0, 16) . '...'
            ]);

            return $hash;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_TRAITS_HASH_ERROR', [
                'algorithm' => $algorithm,
                'traits_count' => count($traitsData),
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Verify hash against original data
     *
     * @param mixed $data Original data
     * @param string $expectedHash Expected hash value
     * @param string $algorithm Algorithm used
     * @return bool True if hash matches
     * @privacy-safe Verification only, no data modification
     */
    public function verifyHash($data, string $expectedHash, string $algorithm = self::DEFAULT_ALGORITHM): bool {
        try {
            // Generate hash from current data
            $currentHash = $this->generateHash($data, $algorithm);

            // Compare hashes securely
            $isValid = hash_equals($expectedHash, $currentHash);

            $this->logger->info('[CoA Hash] Hash verification', [
                'algorithm' => $algorithm,
                'expected_hash' => substr($expectedHash, 0, 16) . '...',
                'current_hash' => substr($currentHash, 0, 16) . '...',
                'is_valid' => $isValid
            ]);

            if (!$isValid) {
                $this->logger->warning('[CoA Hash] Hash verification failed', [
                    'algorithm' => $algorithm,
                    'expected_hash' => $expectedHash,
                    'current_hash' => $currentHash,
                    'data_type' => gettype($data)
                ]);
            }

            return $isValid;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_HASH_VERIFICATION_ERROR', [
                'algorithm' => $algorithm,
                'expected_hash' => $expectedHash,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return false;
        }
    }

    /**
     * Generate multi-algorithm hash for enhanced security
     *
     * @param mixed $data Data to hash
     * @param array $algorithms Algorithms to use
     * @return array Hash results for each algorithm
     * @privacy-safe Multi-algorithm hash generation
     */
    public function generateMultiHash($data, array $algorithms = ['sha256', 'sha512']): array {
        try {
            $results = [];

            foreach ($algorithms as $algorithm) {
                if (!array_key_exists($algorithm, self::AVAILABLE_ALGORITHMS)) {
                    $this->logger->warning('[CoA Hash] Skipping unsupported algorithm', [
                        'algorithm' => $algorithm
                    ]);
                    continue;
                }

                $results[$algorithm] = $this->generateHash($data, $algorithm);
            }

            $this->logger->info('[CoA Hash] Multi-algorithm hashes generated', [
                'algorithms' => array_keys($results),
                'hash_count' => count($results)
            ]);

            return $results;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_MULTI_HASH_ERROR', [
                'algorithms' => $algorithms,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Verify multi-algorithm hash
     *
     * @param mixed $data Original data
     * @param array $expectedHashes Expected hash values
     * @return array Verification results for each algorithm
     * @privacy-safe Multi-algorithm verification
     */
    public function verifyMultiHash($data, array $expectedHashes): array {
        try {
            $results = [];

            foreach ($expectedHashes as $algorithm => $expectedHash) {
                if (!array_key_exists($algorithm, self::AVAILABLE_ALGORITHMS)) {
                    $results[$algorithm] = [
                        'valid' => false,
                        'error' => 'Unsupported algorithm'
                    ];
                    continue;
                }

                try {
                    $isValid = $this->verifyHash($data, $expectedHash, $algorithm);
                    $results[$algorithm] = [
                        'valid' => $isValid,
                        'algorithm' => $algorithm,
                        'hash' => $expectedHash
                    ];
                } catch (\Exception $e) {
                    $results[$algorithm] = [
                        'valid' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }

            $validCount = count(array_filter($results, fn($r) => $r['valid'] ?? false));

            $this->logger->info('[CoA Hash] Multi-algorithm verification completed', [
                'total_algorithms' => count($results),
                'valid_algorithms' => $validCount,
                'all_valid' => $validCount === count($results)
            ]);

            return $results;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_MULTI_HASH_VERIFICATION_ERROR', [
                'expected_hashes' => array_keys($expectedHashes),
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Generate hash for file content
     *
     * @param string $filePath Path to file
     * @param string $algorithm Algorithm to use
     * @return string Generated hash
     * @privacy-safe File content hashing only
     */
    public function generateFileHash(string $filePath, string $algorithm = self::DEFAULT_ALGORITHM): string {
        try {
            // Validate algorithm
            if (!array_key_exists($algorithm, self::AVAILABLE_ALGORITHMS)) {
                throw new \Exception("Unsupported hashing algorithm: {$algorithm}");
            }

            // Check if file exists
            if (!file_exists($filePath)) {
                throw new \Exception("File not found: {$filePath}");
            }

            // Generate hash
            $hash = hash_file($algorithm, $filePath);

            if ($hash === false) {
                throw new \Exception("Failed to generate hash for file: {$filePath}");
            }

            $fileSize = filesize($filePath);

            $this->logger->info('[CoA Hash] File hash generated', [
                'file_path' => basename($filePath),
                'algorithm' => $algorithm,
                'file_size' => $fileSize,
                'hash' => substr($hash, 0, 16) . '...'
            ]);

            return $hash;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_FILE_HASH_ERROR', [
                'file_path' => $filePath,
                'algorithm' => $algorithm,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Normalize data for consistent hashing
     *
     * @param mixed $data Data to normalize
     * @return string Normalized data string
     * @privacy-safe Data normalization only
     */
    protected function normalizeData($data): string {
        if (is_array($data)) {
            // Sort array to ensure consistency
            $sortedData = $this->sortArrayRecursively($data);
            return json_encode($sortedData, JSON_SORT_KEYS | JSON_UNESCAPED_UNICODE);
        }

        if (is_object($data)) {
            // Convert object to array first
            return $this->normalizeData((array) $data);
        }

        if (is_bool($data)) {
            return $data ? 'true' : 'false';
        }

        if (is_null($data)) {
            return 'null';
        }

        // Convert to string
        return (string) $data;
    }

    /**
     * Sort array recursively for consistent hashing
     *
     * @param array $array Array to sort
     * @return array Sorted array
     * @privacy-safe Array sorting only
     */
    protected function sortArrayRecursively(array $array): array {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->sortArrayRecursively($value);
            }
        }

        ksort($array);
        return $array;
    }

    /**
     * Get available hashing algorithms
     *
     * @return array Available algorithms with descriptions
     * @privacy-safe Returns static configuration data
     */
    public function getAvailableAlgorithms(): array {
        return self::AVAILABLE_ALGORITHMS;
    }

    /**
     * Get default algorithm
     *
     * @return string Default algorithm
     * @privacy-safe Returns static configuration
     */
    public function getDefaultAlgorithm(): string {
        return self::DEFAULT_ALGORITHM;
    }

    /**
     * Validate hash format
     *
     * @param string $hash Hash to validate
     * @param string $algorithm Algorithm used
     * @return bool True if valid format
     * @privacy-safe Hash format validation only
     */
    public function validateHashFormat(string $hash, string $algorithm = self::DEFAULT_ALGORITHM): bool {
        try {
            // Expected lengths for different algorithms
            $expectedLengths = [
                'sha256' => 64,
                'sha384' => 96,
                'sha512' => 128,
                'sha3-256' => 64,
                'sha3-512' => 128
            ];

            if (!array_key_exists($algorithm, $expectedLengths)) {
                return false;
            }

            $expectedLength = $expectedLengths[$algorithm];
            $actualLength = strlen($hash);

            // Check length
            if ($actualLength !== $expectedLength) {
                return false;
            }

            // Check if hexadecimal
            return ctype_xdigit($hash);
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_HASH_FORMAT_VALIDATION_ERROR', [
                'hash' => substr($hash, 0, 16) . '...',
                'algorithm' => $algorithm,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return false;
        }
    }

    /**
     * Generate checksum for quick integrity verification
     *
     * @param string $data Data to checksum
     * @return string CRC32 checksum
     * @privacy-safe Quick checksum generation
     */
    public function generateChecksum(string $data): string {
        return sprintf('%08x', crc32($data));
    }

    /**
     * Verify checksum
     *
     * @param string $data Original data
     * @param string $expectedChecksum Expected checksum
     * @return bool True if checksum matches
     * @privacy-safe Checksum verification only
     */
    public function verifyChecksum(string $data, string $expectedChecksum): bool {
        $currentChecksum = $this->generateChecksum($data);
        return hash_equals($expectedChecksum, $currentChecksum);
    }
}
