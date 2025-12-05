<?php

namespace App\DataTransferObjects\BusinessEnrichment;

/**
 * Request DTO for business enrichment
 */
class EnrichmentRequest {
    public function __construct(
        public readonly ?string $vatNumber = null,
        public readonly ?string $websiteUrl = null,
        public readonly ?string $fiscalCode = null,
    ) {
        if (!$this->vatNumber && !$this->websiteUrl) {
            throw new \InvalidArgumentException('At least vatNumber or websiteUrl must be provided');
        }
    }

    public static function fromArray(array $data): self {
        return new self(
            vatNumber: $data['vat_number'] ?? $data['vatNumber'] ?? null,
            websiteUrl: $data['website_url'] ?? $data['websiteUrl'] ?? null,
            fiscalCode: $data['fiscal_code'] ?? $data['fiscalCode'] ?? null,
        );
    }

    public function toArray(): array {
        return [
            'vat_number' => $this->vatNumber,
            'website_url' => $this->websiteUrl,
            'fiscal_code' => $this->fiscalCode,
        ];
    }
}
