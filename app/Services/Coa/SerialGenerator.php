<?php

namespace App\Services\Coa;

use App\Models\Coa;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @Oracode Service: CoA Serial Number Generation
 * 🎯 Purpose: Generate unique, sequential serial numbers for CoA certificates
 * 🛡️ Privacy: No personal data handling, generates public identifiers only
 * 🧱 Core Logic: Manages serial number allocation with collision prevention
 *
 * @package App\Services\Coa
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-18
 * @purpose Unique certificate identification for authenticity
 */
class SerialGenerator {
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
     * Serial number format pattern
     * Format: COA-EGI-YYYY-NNNNNN
     */
    protected const SERIAL_FORMAT = 'COA-EGI-%s-%06d';

    /**
     * Prefix for all CoA serials
     */
    protected const SERIAL_PREFIX = 'COA-EGI';

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
     * Generate a new unique serial number for a CoA
     *
     * @param string|null $year Year for the serial (defaults to current year)
     * @return string Unique serial number
     * @privacy-safe Generates public identifier only
     *
     * @oracode-dimension governance
     * @value-flow Creates unique identifier for certificate tracking
     * @community-impact Enables public certificate verification
     * @transparency-level High - serial numbers are public identifiers
     * @narrative-coherence Links certificate to issuance year and sequence
     */
    public function generateSerial(?string $year = null): string {
        try {
            $year = $year ?? Carbon::now()->year;

            // Ensure year is a string
            if (!is_string($year)) {
                $year = (string)$year;
            }

            $this->logger->info('[CoA Serial] Generating new serial number', [
                'year' => $year,
                'year_type' => gettype($year),
                'timestamp' => now()->toIso8601String()
            ]);

            // Use database transaction to prevent race conditions
            $serial = DB::transaction(function () use ($year) {
                return $this->generateSerialInTransaction($year);
            });

            // Safety check: ensure generated serial is a string
            if (!is_string($serial)) {
                $serialType = is_array(gettype($serial)) ? 'array' : (string) gettype($serial);
                $this->logger->error('[CoA Serial] Generated serial is not a string', [
                    'serial_type' => $serialType,
                    'serial_value' => is_array($serial) ? json_encode($serial) : (string) $serial,
                    'year' => $year
                ]);
                throw new \TypeError('Generated serial must be a string, ' . $serialType . ' given');
            }

            $this->logger->info('[CoA Serial] Serial number generated successfully', [
                'serial' => $serial,
                'year' => $year,
                'timestamp' => now()->toIso8601String()
            ]);

            return $serial;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_SERIAL_GENERATION_ERROR', [
                'year' => $year ?? 'current',
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Generate serial number within a database transaction
     *
     * @param string $year Year for the serial
     * @return string Generated serial number
     * @privacy-safe Internal method for serial generation
     */
    protected function generateSerialInTransaction(string $year): string {
        // Get the highest counter for the given year
        $lastSerial = Coa::where('serial', 'LIKE', self::SERIAL_PREFIX . '-' . $year . '-%')
            ->orderBy('serial', 'desc')
            ->lockForUpdate() // Prevent concurrent access
            ->first();

        $nextCounter = 1;

        if ($lastSerial) {
            // Robust handling: convert to string if needed
            $serialValue = $lastSerial->serial;

            // Handle the case where serial might be an array (staging environment issue)
            if (is_array($serialValue)) {
                // Try to get first element if it's an array
                $serialValue = reset($serialValue);
                $this->logger->warning('[CoA Serial] Serial was array, converted to string', [
                    'model_id' => $lastSerial->id,
                    'original_value' => $lastSerial->serial,
                    'converted_value' => $serialValue
                ]);
            }

            // Ensure it's a string
            if (!is_string($serialValue)) {
                $serialValue = (string)$serialValue;
                $this->logger->warning('[CoA Serial] Serial converted to string', [
                    'model_id' => $lastSerial->id,
                    'original_type' => gettype($lastSerial->serial),
                    'converted_value' => $serialValue
                ]);
            }

            // Validate it looks like a serial
            if (empty($serialValue) || !str_contains($serialValue, 'COA-EGI')) {
                $this->logger->error('[CoA Serial] Invalid serial format after conversion', [
                    'model_id' => $lastSerial->id,
                    'serial_value' => $serialValue
                ]);
                // Fallback: start from 1
                $nextCounter = 1;
            } else {
                // Extract counter from last serial
                $lastCounter = $this->extractCounterFromSerial($serialValue);
                $nextCounter = $lastCounter + 1;
            }
        }

        // Generate the new serial
        try {
            // Ensure parameters are of correct type
            if (!is_string($year) && !is_numeric($year)) {
                $yearType = is_array(gettype($year)) ? 'array' : (string) gettype($year);
                throw new \TypeError('Year must be string or numeric, ' . $yearType . ' given');
            }

            if (!is_int($nextCounter) && !is_numeric($nextCounter)) {
                $counterType = is_array(gettype($nextCounter)) ? 'array' : (string) gettype($nextCounter);
                throw new \TypeError('Counter must be numeric, ' . $counterType . ' given');
            }

            $newSerial = sprintf(self::SERIAL_FORMAT, (string)$year, (int)$nextCounter);

            // Ensure result is a string
            if (!is_string($newSerial)) {
                $resultType = is_array(gettype($newSerial)) ? 'array' : (string) gettype($newSerial);
                throw new \TypeError('sprintf returned ' . $resultType . ' instead of string');
            }
        } catch (\Exception $e) {
            $this->logger->error('[CoA Serial] Error in sprintf generation', [
                'year' => $year,
                'year_type' => gettype($year),
                'counter' => $nextCounter,
                'counter_type' => gettype($nextCounter),
                'format' => self::SERIAL_FORMAT,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        // Double-check uniqueness (paranoid check)
        $attempts = 0;
        while (Coa::where('serial', $newSerial)->exists() && $attempts < 100) {
            $nextCounter++;
            $newSerial = sprintf(self::SERIAL_FORMAT, $year, $nextCounter);
            $attempts++;
        }

        if ($attempts >= 100) {
            throw new \Exception('Unable to generate unique serial after 100 attempts');
        }

        $this->logger->info('[CoA Serial] Serial generated in transaction', [
            'serial' => $newSerial,
            'year' => $year,
            'counter' => $nextCounter,
            'attempts' => $attempts
        ]);

        return $newSerial;
    }

    /**
     * Extract counter number from a serial string
     *
     * @param string $serial The serial to extract counter from
     * @return int The counter number
     * @privacy-safe Parsing public identifier only
     */
    protected function extractCounterFromSerial(string $serial): int {
        // Robust handling: ensure serial is actually a string
        if (is_array($serial)) {
            $serial = reset($serial); // Get first element
            $this->logger->warning('[CoA Serial] Serial extraction from array', [
                'converted_value' => $serial
            ]);
        }

        // Convert to string if not already
        $serial = (string)$serial;

        // Validate basic format
        if (empty($serial)) {
            $this->logger->warning('[CoA Serial] Empty serial for extraction');
            return 0;
        }

        // Expected format: COA-EGI-YYYY-NNNNNN
        $parts = explode('-', $serial);

        if (count($parts) !== 4) {
            $this->logger->warning('[CoA Serial] Invalid serial format for extraction', [
                'serial' => $serial,
                'parts_count' => count($parts)
            ]);
            return 0;
        }

        $counterPart = end($parts);
        return (int) $counterPart;
    }

    /**
     * Validate serial number format
     *
     * @param string $serial The serial to validate
     * @return bool True if valid format
     * @privacy-safe Validation only, no data storage
     */
    public function validateSerialFormat(string $serial): bool {
        try {
            // Check basic pattern: COA-EGI-YYYY-NNNNNN
            $parts = explode('-', $serial);
            $isValid = count($parts) === 4
                && $parts[0] === 'COA'
                && $parts[1] === 'EGI'
                && strlen($parts[2]) === 4
                && is_numeric($parts[2])
                && strlen($parts[3]) === 6
                && is_numeric($parts[3]);

            $this->logger->info('[CoA Serial] Serial format validation', [
                'serial' => $serial,
                'is_valid' => $isValid
            ]);

            return $isValid;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_SERIAL_VALIDATION_ERROR', [
                'serial' => $serial,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return false;
        }
    }

    /**
     * Check if a serial number is unique
     *
     * @param string $serial The serial to check
     * @return bool True if unique (not exists)
     * @privacy-safe Database check only
     */
    public function isSerialUnique(string $serial): bool {
        try {
            $exists = Coa::where('serial', $serial)->exists();

            $this->logger->info('[CoA Serial] Serial uniqueness check', [
                'serial' => $serial,
                'exists' => $exists,
                'is_unique' => !$exists
            ]);

            return !$exists;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_SERIAL_UNIQUENESS_CHECK_ERROR', [
                'serial' => $serial,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            // Assume not unique on error for safety
            return false;
        }
    }

    /**
     * Parse serial number to extract year and counter
     *
     * @param string $serial The serial to parse
     * @return array|null Array with 'year' and 'counter' keys, or null if invalid
     * @privacy-safe Parsing public identifier only
     */
    public function parseSerial(string $serial): ?array {
        try {
            if (!$this->validateSerialFormat($serial)) {
                return null;
            }

            $parts = explode('-', $serial);

            return [
                'prefix' => $parts[0] . '-' . $parts[1], // COA-EGI
                'year' => $parts[2],
                'counter' => (int) $parts[3]
            ];
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_SERIAL_PARSE_ERROR', [
                'serial' => $serial,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return null;
        }
    }

    /**
     * Get statistics about serial generation for a year
     *
     * @param string|null $year Year to get stats for (defaults to current year)
     * @return array Statistics array
     * @privacy-safe Statistical data only, no personal information
     */
    public function getYearStatistics(?string $year = null): array {
        try {
            $year = $year ?? Carbon::now()->year;

            $count = Coa::where('serial', 'LIKE', self::SERIAL_PREFIX . '-' . $year . '-%')
                ->count();

            $firstSerial = Coa::where('serial', 'LIKE', self::SERIAL_PREFIX . '-' . $year . '-%')
                ->orderBy('serial', 'asc')
                ->first();

            $lastSerial = Coa::where('serial', 'LIKE', self::SERIAL_PREFIX . '-' . $year . '-%')
                ->orderBy('serial', 'desc')
                ->first();

            $statistics = [
                'year' => $year,
                'total_issued' => $count,
                'first_serial' => $firstSerial?->serial,
                'last_serial' => $lastSerial?->serial,
                'next_counter' => $count + 1,
                'formatted_next' => sprintf(self::SERIAL_FORMAT, $year, $count + 1)
            ];

            $this->logger->info('[CoA Serial] Year statistics retrieved', $statistics);

            return $statistics;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_SERIAL_STATISTICS_ERROR', [
                'year' => $year ?? 'current',
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'year' => $year ?? Carbon::now()->year,
                'total_issued' => 0,
                'first_serial' => null,
                'last_serial' => null,
                'next_counter' => 1,
                'formatted_next' => sprintf(self::SERIAL_FORMAT, $year ?? Carbon::now()->year, 1),
                'error' => true
            ];
        }
    }

    /**
     * Reserve a serial number for upcoming issuance
     * Note: This is useful for pre-validation before complex operations
     *
     * @param string|null $year Year for the serial
     * @return string Reserved serial number
     * @privacy-safe Generates public identifier only
     */
    public function reserveSerial(?string $year = null): string {
        // For now, this just generates a serial
        // In future versions, we could implement a reservation table
        return $this->generateSerial($year);
    }

    /**
     * Get all available years that have CoAs issued
     *
     * @return array Array of years with CoA counts
     * @privacy-safe Statistical data only
     */
    public function getAvailableYears(): array {
        try {
            $years = Coa::selectRaw('
                    SUBSTRING(serial, 9, 4) as year,
                    COUNT(*) as count
                ')
                ->where('serial', 'LIKE', self::SERIAL_PREFIX . '-%')
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->get()
                ->toArray();

            $this->logger->info('[CoA Serial] Available years retrieved', [
                'years_count' => count($years),
                'total_certificates' => array_sum(array_column($years, 'count'))
            ]);

            return $years;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_SERIAL_YEARS_QUERY_ERROR', [
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [];
        }
    }
}
