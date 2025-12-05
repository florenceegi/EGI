<?php

namespace App\Services\BusinessEnrichment\Sources;

use App\DataTransferObjects\BusinessEnrichment\SourceResult;
use Illuminate\Support\Facades\Http;

/**
 * @Oracode Service: Agenzia Entrate VAT Validation
 * 🎯 Purpose: Validate Italian VAT numbers via official Agenzia Entrate service
 * 🛡️ Privacy: Only validates public VAT registration status
 * 🧱 Core Logic: Uses official AdE web service for validation
 */
class AgenziaEntrateSource {
    private const VERIFICATION_URL = 'https://telematici.agenziaentrate.gov.it/VerificaPIVA/VerificaPiva.do';
    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
    private const TIMEOUT_SECONDS = 15;

    /**
     * Validate VAT number with Agenzia Entrate
     */
    public function fetch(string $vatNumber): SourceResult {
        // Clean VAT number
        $vatNumber = preg_replace('/[^0-9]/', '', $vatNumber);

        if (strlen($vatNumber) !== 11) {
            return SourceResult::failure('agenzia_entrate', 'Invalid VAT number format: must be 11 digits');
        }

        // Validate checksum (Luhn mod 10)
        if (!$this->validateVatChecksum($vatNumber)) {
            return SourceResult::failure('agenzia_entrate', 'Invalid VAT number checksum');
        }

        try {
            // Call Agenzia Entrate verification service
            $response = Http::withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html,application/xhtml+xml',
                'Accept-Language' => 'it-IT,it;q=0.9',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
                ->timeout(self::TIMEOUT_SECONDS)
                ->asForm()
                ->post(self::VERIFICATION_URL, [
                    'piva' => $vatNumber,
                    'action' => 'verificaPiva',
                ]);

            if (!$response->successful()) {
                return SourceResult::failure('agenzia_entrate', "HTTP error: {$response->status()}");
            }

            $html = $response->body();
            $data = $this->parseResponse($html, $vatNumber);

            return SourceResult::success('agenzia_entrate', $data);
        } catch (\Exception $e) {
            // Fallback: Try VIES for cross-validation
            return $this->tryViesValidation($vatNumber);
        }
    }

    /**
     * Parse Agenzia Entrate response
     */
    private function parseResponse(string $html, string $vatNumber): array {
        $data = [
            'vat_number' => $vatNumber,
            'is_vat_valid' => false,
            'vat_status' => 'unknown',
            'vat_start_date' => null,
            'legal_name' => null,
        ];

        // Check for "PARTITA IVA VALIDA" or similar
        if (preg_match('/PARTITA\s+IVA\s+VALIDA|Partita\s+Iva\s+valida|P\.IVA\s+attiva/i', $html)) {
            $data['is_vat_valid'] = true;
            $data['vat_status'] = 'active';
        } elseif (preg_match('/PARTITA\s+IVA\s+NON\s+VALIDA|non\s+valida|cessata|non\s+attiva/i', $html)) {
            $data['is_vat_valid'] = false;
            $data['vat_status'] = 'ceased';
        } elseif (preg_match('/PARTITA\s+IVA\s+NON\s+PRESENTE|non\s+risulta|non\s+esistente/i', $html)) {
            $data['is_vat_valid'] = false;
            $data['vat_status'] = 'not_found';
        }

        // Extract start date if available
        if (preg_match('/(?:data\s+(?:di\s+)?inizio\s+attivit|inizio)[\s:]*(\d{2})[\/\-](\d{2})[\/\-](\d{4})/i', $html, $matches)) {
            $data['vat_start_date'] = "{$matches[3]}-{$matches[2]}-{$matches[1]}";
        }

        // Extract company name if shown
        if (preg_match('/(?:denominazione|ragione\s+sociale)[\s:]*<[^>]*>([^<]+)</i', $html, $matches)) {
            $data['legal_name'] = trim(html_entity_decode($matches[1]));
        }

        // Check response body for any success indicator
        if (strpos($html, $vatNumber) !== false && $data['vat_status'] === 'unknown') {
            // If the VAT number appears and no explicit error, assume it might be valid
            // but mark as unverified
            $data['vat_status'] = 'unverified';
        }

        return $data;
    }

    /**
     * Validate Italian VAT checksum (Luhn mod 10 variant)
     */
    private function validateVatChecksum(string $vatNumber): bool {
        if (strlen($vatNumber) !== 11) {
            return false;
        }

        $digits = str_split($vatNumber);
        $sum = 0;

        for ($i = 0; $i < 10; $i++) {
            $digit = (int) $digits[$i];
            if ($i % 2 === 0) {
                // Odd positions (0, 2, 4, ...) - just add
                $sum += $digit;
            } else {
                // Even positions (1, 3, 5, ...) - double and adjust
                $doubled = $digit * 2;
                $sum += intdiv($doubled, 10) + ($doubled % 10);
            }
        }

        $checkDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit === (int) $digits[10];
    }

    /**
     * Fallback: Try EU VIES validation
     */
    private function tryViesValidation(string $vatNumber): SourceResult {
        try {
            $response = Http::withHeaders([
                'User-Agent' => self::USER_AGENT,
            ])
                ->timeout(self::TIMEOUT_SECONDS)
                ->get("https://ec.europa.eu/taxation_customs/vies/rest-api/ms/IT/vat/{$vatNumber}");

            if (!$response->successful()) {
                return SourceResult::failure('agenzia_entrate', 'VIES service unavailable');
            }

            $json = $response->json();

            $data = [
                'vat_number' => $vatNumber,
                'is_vat_valid' => $json['isValid'] ?? false,
                'vat_status' => ($json['isValid'] ?? false) ? 'active' : 'invalid',
                'legal_name' => $json['name'] ?? null,
                'street' => $json['address'] ?? null,
            ];

            // Parse VIES address if provided
            if (!empty($json['address'])) {
                $addressParts = $this->parseViesAddress($json['address']);
                $data = array_merge($data, $addressParts);
            }

            return SourceResult::success('agenzia_entrate_vies', $data);
        } catch (\Exception $e) {
            return SourceResult::failure('agenzia_entrate', 'Both AdE and VIES validation failed: ' . $e->getMessage());
        }
    }

    /**
     * Parse VIES address format
     */
    private function parseViesAddress(string $address): array {
        $data = [
            'street' => null,
            'city' => null,
            'zip_code' => null,
            'province' => null,
        ];

        // VIES returns address in various formats, try common Italian pattern
        // "VIA EXAMPLE 123\n12345 CITY PROVINCE"
        $lines = preg_split('/[\r\n]+/', $address);

        if (count($lines) >= 1) {
            $data['street'] = trim($lines[0]);
        }

        if (count($lines) >= 2) {
            if (preg_match('/(\d{5})\s+(.+)/u', $lines[1], $matches)) {
                $data['zip_code'] = $matches[1];
                $cityPart = trim($matches[2]);

                // Check for province code at end
                if (preg_match('/^(.+?)\s+([A-Z]{2})$/u', $cityPart, $cityMatches)) {
                    $data['city'] = trim($cityMatches[1]);
                    $data['province'] = $cityMatches[2];
                } else {
                    $data['city'] = $cityPart;
                }
            }
        }

        return $data;
    }

    /**
     * Quick checksum validation only (no network call)
     */
    public function isValidFormat(string $vatNumber): bool {
        $vatNumber = preg_replace('/[^0-9]/', '', $vatNumber);
        return strlen($vatNumber) === 11 && $this->validateVatChecksum($vatNumber);
    }
}
