<?php

namespace App\Services\AI\Features\DTOs;

/**
 * @package App\Services\AI\Features\DTOs
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - AI Feature Orchestration)
 * @date 2025-11-04
 * @purpose DTO for unified AI Feature response
 */
class AiFeatureResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $message = null,
        public readonly mixed $data = null,
        public readonly ?string $featureCode = null,
        public readonly ?int $egiId = null,
        public readonly ?int $userId = null,
        public readonly ?array $metadata = null
    ) {}

    /**
     * Create success result
     */
    public static function success(
        string $message,
        mixed $data = null,
        ?string $featureCode = null,
        ?int $egiId = null,
        ?int $userId = null,
        ?array $metadata = null
    ): self {
        return new self(
            success: true,
            message: $message,
            data: $data,
            featureCode: $featureCode,
            egiId: $egiId,
            userId: $userId,
            metadata: $metadata
        );
    }

    /**
     * Create failure result
     */
    public static function failure(
        string $message,
        ?string $featureCode = null,
        ?int $egiId = null,
        ?int $userId = null,
        ?array $metadata = null
    ): self {
        return new self(
            success: false,
            message: $message,
            data: null,
            featureCode: $featureCode,
            egiId: $egiId,
            userId: $userId,
            metadata: $metadata
        );
    }

    /**
     * Convert to array for JSON response
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'feature_code' => $this->featureCode,
            'egi_id' => $this->egiId,
            'user_id' => $this->userId,
            'metadata' => $this->metadata,
        ];
    }
}

