<?php

namespace App\Services\BusinessEnrichment\Sources;

use App\DataTransferObjects\BusinessEnrichment\SourceResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * @Oracode Service: Website Scraper for Business Data
 * 🎯 Purpose: Extract business info from company websites
 * 🛡️ Privacy: Only extracts publicly visible business data
 * 🧱 Core Logic: Pattern-based extraction with multiple fallbacks
 */
class WebsiteScraperSource {
    private const USER_AGENT = 'FlorenceEGI-BusinessEnrichment/1.0 (+https://florenceegi.com)';
    private const TIMEOUT_SECONDS = 15;

    /**
     * Fetch and extract business data from a website
     */
    public function fetch(string $url): SourceResult {
        // Normalize URL
        $url = $this->normalizeUrl($url);

        try {
            // Fetch main page
            $response = Http::withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html,application/xhtml+xml',
                'Accept-Language' => 'it-IT,it;q=0.9,en;q=0.8',
            ])
                ->timeout(self::TIMEOUT_SECONDS)
                ->get($url);

            if (!$response->successful()) {
                return SourceResult::failure('website', "HTTP {$response->status()}");
            }

            $html = $response->body();
            $data = $this->extractData($html, $url);

            // Try contact/about pages if main page has few results
            if ($this->needsMoreData($data)) {
                $data = $this->tryAdditionalPages($url, $data);
            }

            return SourceResult::success('website', $data);
        } catch (\Exception $e) {
            return SourceResult::failure('website', $e->getMessage());
        }
    }

    /**
     * Extract business data from HTML
     */
    private function extractData(string $html, string $baseUrl): array {
        $data = [
            'legal_name' => null,
            'trade_name' => null,
            'vat_number' => null,
            'fiscal_code' => null,
            'street' => null,
            'city' => null,
            'province' => null,
            'zip_code' => null,
            'phone' => null,
            'fax' => null,
            'email' => null,
            'pec' => null,
            'website' => $baseUrl,
        ];

        // Remove scripts and styles for cleaner text extraction
        $cleanHtml = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $cleanHtml = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $cleanHtml);
        $text = strip_tags($cleanHtml);
        $text = preg_replace('/\s+/', ' ', $text);

        // Extract P.IVA (Italian VAT number - 11 digits)
        // Patterns: "P.IVA: 12345678901", "P.iva:12345678901", "Partita IVA IT12345678901"
        $vatPatterns = [
            '/P\.?\s*IVA[\s:]*(?:IT)?(\d{11})\b/i',
            '/(?:partita\s*iva|vat)[\s:]*(?:IT)?(\d{11})\b/i',
            '/(?:IT)?(\d{11})(?=\s*(?:RICCARDO|S\.?R\.?L|S\.?P\.?A|$))/i', // VAT before company suffix
        ];
        foreach ($vatPatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $data['vat_number'] = $matches[1];
                break;
            }
        }

        // Extract Codice Fiscale (16 chars alphanumeric OR 11 digits for companies)
        if (preg_match('/C\.?\s*F\.?[\s:]*([A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z])\b/i', $text, $matches)) {
            $data['fiscal_code'] = strtoupper($matches[1]);
        } elseif (preg_match('/codice\s*fiscale[\s:]*([A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z])\b/i', $text, $matches)) {
            $data['fiscal_code'] = strtoupper($matches[1]);
        }

        // Extract PEC (look for PEC domains: @pec.it, @*.pec.it, @legalmail.it, etc.)
        $pecPatterns = [
            // Match emails ending with common Italian PEC domains
            '/([a-zA-Z0-9._%+-]+@(?:[a-zA-Z0-9.-]+\.)?pec\.it)\b/i',
            '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.legalmail\.it)\b/i',
            '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.pecspeciale\.it)\b/i',
            '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.postecert\.it)\b/i',
            // Explicit PEC: label
            '/PEC[\s:]*([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/i',
        ];
        foreach ($pecPatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $data['pec'] = strtolower($matches[1]);
                break;
            }
        }

        // Extract email (non-PEC) - prefer info@ addresses
        $foundEmails = [];
        if (preg_match_all('/\b([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})\b/i', $text, $matches)) {
            foreach ($matches[1] as $email) {
                $email = strtolower($email);
                // Skip PEC addresses, image files, and common non-business emails
                if (
                    !preg_match('/\.(pec\.it|legalmail\.it|png|jpg|gif|jpeg|webp)$/i', $email) &&
                    !preg_match('/(noreply|no-reply|example|wix\.com|wixstatic)/i', $email) &&
                    $email !== $data['pec']
                ) {
                    $foundEmails[] = $email;
                }
            }
        }
        // Prefer info@ or contatti@ emails
        foreach ($foundEmails as $email) {
            if (preg_match('/^(info|contatti|contact)@/i', $email)) {
                $data['email'] = $email;
                break;
            }
        }
        if (!$data['email'] && !empty($foundEmails)) {
            $data['email'] = $foundEmails[0];
        }

        // Extract phone numbers (Italian format)
        $phonePatterns = [
            '/Tel\.?[\s:]*(\+?39[\s\-]?)?(\d{2,4}[\s\-]?\d{5,8})/i',
            '/Telefono[\s:]*(\+?39[\s\-]?)?(\d{2,4}[\s\-]?\d{5,8})/i',
            '/(?<!\d)(\+39[\s\-]?)?(0\d{1,3}[\s\-]?\d{5,8})(?!\d)/i',
        ];
        foreach ($phonePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $phone = preg_replace('/[^\d+]/', '', $matches[0]);
                if (strlen($phone) >= 9) {
                    $data['phone'] = $phone;
                    break;
                }
            }
        }

        // Extract fax
        if (preg_match('/Fax[\s:]*(\+?39[\s\-]?)?(\d{2,4}[\s\-]?\d{5,8})/i', $text, $matches)) {
            $data['fax'] = preg_replace('/[^\d+]/', '', $matches[0]);
        }

        // Extract address with Italian patterns
        $addressData = $this->extractItalianAddress($text);
        $data = array_merge($data, $addressData);

        // Extract company name from various sources
        $data['legal_name'] = $this->extractCompanyName($html, $text);

        return $data;
    }

    /**
     * Extract Italian address components
     */
    private function extractItalianAddress(string $text): array {
        $data = [
            'street' => null,
            'city' => null,
            'province' => null,
            'zip_code' => null,
        ];

        // Pattern: Via/Piazza/Corso + name + number
        if (preg_match('/((?:Via|Viale|Piazza|P\.za|P\.zza|Corso|C\.so|Largo|Vicolo|Strada)[^,\n\d]{3,50}),?\s*(\d{1,5}[a-zA-Z]?)/iu', $text, $matches)) {
            $data['street'] = trim($matches[1]) . ', ' . trim($matches[2]);
        }

        // Pattern: CAP (5 digits) followed by city name
        // Stop at: Italy, Tel, Fax, Email, -, common separators
        if (preg_match('/\b(\d{5})\s+([A-Za-zÀ-ÿ\'\-]{2,30})(?:\s*[-–]\s*Italy|\s*\(([A-Z]{2})\)|[-–]\s*([A-Z]{2})\b|\s+(?:Tel|Fax|Email|PEC|Italy))/ui', $text, $matches)) {
            $data['zip_code'] = $matches[1];
            $data['city'] = trim($matches[2]);
            $data['province'] = $matches[3] ?? $matches[4] ?? null;
        }
        // Alternative: simpler pattern "59100 Prato"
        elseif (preg_match('/\b(\d{5})\s+([A-Za-zÀ-ÿ]{2,20})\b/u', $text, $matches)) {
            $data['zip_code'] = $matches[1];
            // Clean city name - stop at common words
            $city = preg_replace('/\s*(Italy|Tel|Fax|Email|PEC|Via|Phone).*$/i', '', $matches[2]);
            $data['city'] = trim($city);
        }

        // Try alternative: City (PROVINCE) pattern
        if (!$data['city'] && preg_match('/([A-Za-zÀ-ÿ\s\'-]{2,30})\s*\(([A-Z]{2})\)/u', $text, $matches)) {
            $data['city'] = trim($matches[1]);
            $data['province'] = $matches[2];
        }

        // Clean up city if it contains noise
        if ($data['city']) {
            $data['city'] = preg_replace('/\s*[-–]\s*(Italy|IT|Italia).*$/i', '', $data['city']);
            $data['city'] = trim($data['city']);
        }

        return $data;
    }

    /**
     * Extract company name from HTML
     */
    private function extractCompanyName(string $html, string $text): ?string {
        // Try structured data (JSON-LD)
        if (preg_match('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches)) {
            $jsonLd = json_decode($matches[1], true);
            if ($jsonLd) {
                if (isset($jsonLd['name'])) return $jsonLd['name'];
                if (isset($jsonLd['legalName'])) return $jsonLd['legalName'];
                if (isset($jsonLd['@graph'])) {
                    foreach ($jsonLd['@graph'] as $item) {
                        if (
                            isset($item['name']) && isset($item['@type']) &&
                            in_array($item['@type'], ['Organization', 'Corporation', 'LocalBusiness'])
                        ) {
                            return $item['name'];
                        }
                    }
                }
            }
        }

        // Try og:site_name
        if (preg_match('/<meta[^>]*property=["\']og:site_name["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1]);
        }

        // Try title tag
        if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $html, $matches)) {
            $title = trim(html_entity_decode($matches[1]));
            // Remove common suffixes
            $title = preg_replace('/\s*[-–|]\s*(Home|Homepage|Benvenuto|Welcome).*$/i', '', $title);
            if (strlen($title) > 3 && strlen($title) < 100) {
                return $title;
            }
        }

        // Look for S.R.L., S.P.A., etc. patterns in text
        if (preg_match('/([A-ZÀ-Ÿ][A-Za-zÀ-ÿ\s\.\'\-&]{2,50})\s*(?:S\.?R\.?L\.?|S\.?P\.?A\.?|S\.?N\.?C\.?|S\.?A\.?S\.?)/iu', $text, $matches)) {
            return trim($matches[0]);
        }

        return null;
    }

    /**
     * Check if we need more data
     */
    private function needsMoreData(array $data): bool {
        $important = ['vat_number', 'email', 'phone', 'street'];
        $found = 0;
        foreach ($important as $field) {
            if (!empty($data[$field])) $found++;
        }
        return $found < 3;
    }

    /**
     * Try additional pages (contact, about, etc.)
     */
    private function tryAdditionalPages(string $baseUrl, array $currentData): array {
        $additionalPaths = [
            '/contatti',
            '/contacts',
            '/contact',
            '/contact-us',
            '/chi-siamo',
            '/about',
            '/about-us',
            '/azienda',
            '/company',
            '/privacy',
            '/privacy-policy', // Often has legal info
        ];

        $parsedUrl = parse_url($baseUrl);
        $baseHost = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

        foreach ($additionalPaths as $path) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => self::USER_AGENT,
                ])
                    ->timeout(10)
                    ->get($baseHost . $path);

                if ($response->successful()) {
                    $pageData = $this->extractData($response->body(), $baseUrl);

                    // Merge: keep existing data, add new non-null values
                    foreach ($pageData as $key => $value) {
                        if ($value !== null && $currentData[$key] === null) {
                            $currentData[$key] = $value;
                        }
                    }

                    // If we have enough data, stop
                    if (!$this->needsMoreData($currentData)) {
                        break;
                    }
                }
            } catch (\Exception $e) {
                // Silently continue
            }
        }

        return $currentData;
    }

    /**
     * Normalize URL
     */
    private function normalizeUrl(string $url): string {
        $url = trim($url);

        if (!preg_match('/^https?:\/\//i', $url)) {
            $url = 'https://' . $url;
        }

        // Remove trailing slash
        $url = rtrim($url, '/');

        return $url;
    }
}
