<?php

namespace App\DataTransferObjects\BusinessEnrichment;

/**
 * Result from a single data source
 */
class SourceResult {
    public function __construct(
        public readonly string $source,
        public readonly array $data,
        public readonly bool $success,
        public readonly ?string $error = null,
    ) {
    }

    public static function success(string $source, array $data): self {
        return new self(
            source: $source,
            data: $data,
            success: true,
            error: null,
        );
    }

    public static function failure(string $source, string $error): self {
        return new self(
            source: $source,
            data: [],
            success: false,
            error: $error,
        );
    }
}
