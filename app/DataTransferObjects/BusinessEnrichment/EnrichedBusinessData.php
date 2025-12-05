<?php

namespace App\DataTransferObjects\BusinessEnrichment;

use Carbon\Carbon;

/**
 * Enriched business data from multiple sources
 */
class EnrichedBusinessData {
    public function __construct(
        public readonly array $data,
        public readonly array $sources,
        public readonly array $errors,
        public readonly array $confidence,
        public readonly Carbon $fetchedAt,
    ) {
    }

    public static function fromArray(array $array): self {
        return new self(
            data: $array['data'] ?? [],
            sources: $array['sources'] ?? [],
            errors: $array['errors'] ?? [],
            confidence: $array['confidence'] ?? [],
            fetchedAt: isset($array['fetched_at']) ? Carbon::parse($array['fetched_at']) : now(),
        );
    }

    public function toArray(): array {
        return [
            'data' => $this->data,
            'sources' => $this->sources,
            'errors' => $this->errors,
            'confidence' => $this->confidence,
            'fetched_at' => $this->fetchedAt->toIso8601String(),
        ];
    }

    /**
     * Get field value
     */
    public function get(string $field, mixed $default = null): mixed {
        return $this->data[$field] ?? $default;
    }

    /**
     * Get which source provided a specific field
     */
    public function getSourceFor(string $field): ?string {
        return $this->confidence[$field] ?? null;
    }

    /**
     * Check if any source returned data
     */
    public function hasData(): bool {
        return !empty(array_filter($this->data));
    }

    /**
     * Check if VAT is validated
     */
    public function isVatValid(): ?bool {
        return $this->data['is_vat_valid'] ?? null;
    }

    /**
     * Get completeness score (0-100)
     */
    public function getCompletenessScore(): int {
        $importantFields = [
            'legal_name',
            'vat_number',
            'street',
            'city',
            'zip_code',
            'email',
            'phone',
            'ateco_code'
        ];

        $filled = 0;
        foreach ($importantFields as $field) {
            if (!empty($this->data[$field])) {
                $filled++;
            }
        }

        return (int) round(($filled / count($importantFields)) * 100);
    }

    /**
     * Convert to organization data format for saving
     */
    public function toOrganizationData(): array {
        return [
            'org_name' => $this->data['legal_name'] ?? $this->data['trade_name'],
            'org_street' => $this->data['street'],
            'org_city' => $this->data['city'],
            'org_region' => $this->data['province'],
            'org_state' => $this->data['region'],
            'org_zip' => $this->data['zip_code'],
            'org_site_url' => $this->data['website'],
            'org_email' => $this->data['email'],
            'org_phone_1' => $this->data['phone'],
            'org_phone_2' => $this->data['fax'],
            'org_vat_number' => $this->data['vat_number'],
            'org_fiscal_code' => $this->data['fiscal_code'],
            'rea' => $this->data['rea_number'],
            'pec' => $this->data['pec'],
            'ateco_code' => $this->data['ateco_code'],
            'ateco_description' => $this->data['ateco_description'],
        ];
    }
}
