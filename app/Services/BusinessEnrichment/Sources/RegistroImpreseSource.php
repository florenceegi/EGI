<?php

namespace App\Services\BusinessEnrichment\Sources;

use App\DataTransferObjects\BusinessEnrichment\SourceResult;
use Illuminate\Support\Facades\Http;

/**
 * @Oracode Service: Registro Imprese Data Source
 * 🎯 Purpose: Fetch official business data from Registro Imprese public search
 * 🛡️ Privacy: Only public business registry data
 * 🧱 Core Logic: Scrapes registroimprese.it free search results
 *
 * Note: This uses the FREE public search, not the paid API.
 * Limited data but includes: ragione sociale, sede legale, ATECO, REA
 */
class RegistroImpreseSource {
    private const BASE_URL = 'https://www.registroimprese.it';
    private const SEARCH_URL = 'https://www.registroimprese.it/ricerca-gratuita-imprese';
    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
    private const TIMEOUT_SECONDS = 20;

    /**
     * Fetch business data from Registro Imprese by VAT number
     */
    public function fetch(string $vatNumber): SourceResult {
        // Clean VAT number
        $vatNumber = preg_replace('/[^0-9]/', '', $vatNumber);

        if (strlen($vatNumber) !== 11) {
            return SourceResult::failure('registro_imprese', 'Invalid VAT number format');
        }

        try {
            // Step 1: Get search page to extract form tokens/cookies
            $session = Http::withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'it-IT,it;q=0.9',
            ])
                ->timeout(self::TIMEOUT_SECONDS)
                ->get(self::SEARCH_URL);

            if (!$session->successful()) {
                // Fallback: try alternative free sources
                return $this->tryAlternativeSources($vatNumber);
            }

            $cookies = $session->cookies();

            // Step 2: Search by VAT number
            $searchResponse = Http::withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html,application/xhtml+xml',
                'Referer' => self::SEARCH_URL,
            ])
                ->withCookies($cookies->toArray(), parse_url(self::BASE_URL, PHP_URL_HOST))
                ->timeout(self::TIMEOUT_SECONDS)
                ->asForm()
                ->post(self::SEARCH_URL, [
                    '_searchAdvancedImprese_WAR_searchAdvancedImpreseSRLportlet_codiceFiscale' => $vatNumber,
                    '_searchAdvancedImprese_WAR_searchAdvancedImpreseSRLportlet_formDate' => time() * 1000,
                ]);

            if (!$searchResponse->successful()) {
                return $this->tryAlternativeSources($vatNumber);
            }

            $data = $this->extractFromSearchResults($searchResponse->body());

            if (empty($data['legal_name'])) {
                // Try alternative sources if main search failed
                return $this->tryAlternativeSources($vatNumber);
            }

            $data['vat_number'] = $vatNumber;

            return SourceResult::success('registro_imprese', $data);
        } catch (\Exception $e) {
            return $this->tryAlternativeSources($vatNumber);
        }
    }

    /**
     * Extract data from search results page
     */
    private function extractFromSearchResults(string $html): array {
        $data = [
            'legal_name' => null,
            'trade_name' => null,
            'rea_number' => null,
            'ateco_code' => null,
            'ateco_description' => null,
            'legal_form' => null,
            'street' => null,
            'city' => null,
            'province' => null,
            'zip_code' => null,
            'region' => null,
            'vat_status' => 'active', // Assume active if found
        ];

        // Extract company name
        if (preg_match('/<span[^>]*class="[^"]*denominazione[^"]*"[^>]*>([^<]+)</i', $html, $matches)) {
            $data['legal_name'] = trim(html_entity_decode($matches[1]));
        } elseif (preg_match('/Denominazione[\s:]*<[^>]*>([^<]+)</i', $html, $matches)) {
            $data['legal_name'] = trim(html_entity_decode($matches[1]));
        }

        // Extract REA
        if (preg_match('/REA[\s:]*([A-Z]{2}[\s-]*\d+)/i', $html, $matches)) {
            $data['rea_number'] = preg_replace('/\s/', '', $matches[1]);
        }

        // Extract ATECO code
        if (preg_match('/ATECO[\s:]*(\d{2}(?:\.\d{2})?(?:\.\d{1,2})?)/i', $html, $matches)) {
            $data['ateco_code'] = $matches[1];
        } elseif (preg_match('/Codice\s*Ateco[\s:]*(\d{2}(?:\.\d{2})?(?:\.\d{1,2})?)/i', $html, $matches)) {
            $data['ateco_code'] = $matches[1];
        }

        // Extract ATECO description
        if (preg_match('/ATECO[^<]*<[^>]*>[^<]*<[^>]*>([^<]+)</i', $html, $matches)) {
            $data['ateco_description'] = trim(html_entity_decode($matches[1]));
        }

        // Extract legal form
        $legalForms = [
            'S.R.L.',
            'S.P.A.',
            'S.N.C.',
            'S.A.S.',
            'SOCIETA\' A RESPONSABILITA\' LIMITATA',
            'SOCIETA\' PER AZIONI',
            'DITTA INDIVIDUALE',
            'IMPRESA INDIVIDUALE'
        ];
        foreach ($legalForms as $form) {
            if (stripos($html, $form) !== false) {
                $data['legal_form'] = $form;
                break;
            }
        }

        // Extract address
        if (preg_match('/Sede[\s:]*(?:legale)?[^<]*<[^>]*>([^<]+)</i', $html, $matches)) {
            $address = trim(html_entity_decode($matches[1]));
            $this->parseAddress($address, $data);
        }

        // Extract province
        if (preg_match('/\(([A-Z]{2})\)/', $html, $matches)) {
            $data['province'] = $matches[1];
        }

        return $data;
    }

    /**
     * Parse Italian address string
     */
    private function parseAddress(string $address, array &$data): void {
        // Try: "Via Nome 123 - 12345 Città (PR)"
        if (preg_match('/(.+?)\s*[-–]\s*(\d{5})\s+([^(]+)\s*\(([A-Z]{2})\)/u', $address, $matches)) {
            $data['street'] = trim($matches[1]);
            $data['zip_code'] = $matches[2];
            $data['city'] = trim($matches[3]);
            $data['province'] = $matches[4];
        }
        // Try: "Via Nome 123, 12345 Città (PR)"
        elseif (preg_match('/(.+?),\s*(\d{5})\s+([^(]+)\s*\(([A-Z]{2})\)/u', $address, $matches)) {
            $data['street'] = trim($matches[1]);
            $data['zip_code'] = $matches[2];
            $data['city'] = trim($matches[3]);
            $data['province'] = $matches[4];
        }
    }

    /**
     * Try alternative free data sources
     */
    private function tryAlternativeSources(string $vatNumber): SourceResult {
        // Try Kompass Italia
        $kompassData = $this->tryKompass($vatNumber);
        if ($kompassData) {
            return SourceResult::success('registro_imprese_alt', $kompassData);
        }

        // Try Italian Yellow Pages (PagineBianche Business)
        $ypData = $this->tryYellowPages($vatNumber);
        if ($ypData) {
            return SourceResult::success('registro_imprese_alt', $ypData);
        }

        return SourceResult::failure('registro_imprese', 'No data found in any source');
    }

    /**
     * Try Kompass.com for business data
     */
    private function tryKompass(string $vatNumber): ?array {
        try {
            $response = Http::withHeaders([
                'User-Agent' => self::USER_AGENT,
            ])
                ->timeout(15)
                ->get("https://it.kompass.com/searchCompanies?text={$vatNumber}");

            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();
            $data = [];

            // Extract company name from Kompass
            if (preg_match('/<h2[^>]*class="[^"]*companyName[^"]*"[^>]*>.*?<a[^>]*>([^<]+)</is', $html, $matches)) {
                $data['legal_name'] = trim(html_entity_decode($matches[1]));
            }

            // Extract ATECO/NAF code
            if (preg_match('/NAF[^:]*:\s*(\d{2}(?:\.\d{2})?)/i', $html, $matches)) {
                $data['ateco_code'] = $matches[1];
            }

            return !empty($data['legal_name']) ? $data : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Try Italian Yellow Pages business search
     */
    private function tryYellowPages(string $vatNumber): ?array {
        try {
            // PagineBianche doesn't have VAT search, but we can try Google-style search
            $response = Http::withHeaders([
                'User-Agent' => self::USER_AGENT,
            ])
                ->timeout(15)
                ->get("https://www.paginebianche.it/ricerca?qs={$vatNumber}&dession=business");

            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();
            $data = [];

            // Extract company info
            if (preg_match('/<h2[^>]*class="[^"]*denominazione[^"]*"[^>]*>([^<]+)</i', $html, $matches)) {
                $data['legal_name'] = trim(html_entity_decode($matches[1]));
            }

            return !empty($data['legal_name']) ? $data : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
