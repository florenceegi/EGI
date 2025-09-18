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

            $this->logger->info('[CoA Serial] Generating new serial number', [
                'year' => $year,
                'timestamp' => now()->toIso8601String()
            ]);

            // Use database transaction to prevent race conditions
            $serial = DB::transaction(function () use ($year) {
                return $this->generateSerialInTransaction($year);
            });

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
            // Extract counter from last serial
            $lastCounter = $this->extractCounterFromSerial($lastSerial->serial);
            $nextCounter = $lastCounter + 1;
        }

        // Generate the new serial
        $newSerial = sprintf(self::SERIAL_FORMAT, $year, $nextCounter);

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
