<?php

namespace App\Exceptions;

use Exception;

/**
 * Document Analysis Exception
 *
 * @package App\Exceptions
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-18
 * @purpose Exception thrown when document analysis fails (API errors, invalid responses, timeouts)
 */
class DocumentAnalysisException extends Exception
{
    /**
     * Provider that caused the exception
     *
     * @var string|null
     */
    protected ?string $provider = null;

    /**
     * Original error response from provider
     *
     * @var array|null
     */
    protected ?array $providerResponse = null;

    /**
     * Set provider name
     *
     * @param string $provider Provider identifier
     * @return self
     */
    public function setProvider(string $provider): self
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * Get provider name
     *
     * @return string|null
     */
    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * Set provider response for debugging
     *
     * @param array $response Provider error response
     * @return self
     */
    public function setProviderResponse(array $response): self
    {
        $this->providerResponse = $response;
        return $this;
    }

    /**
     * Get provider response
     *
     * @return array|null
     */
    public function getProviderResponse(): ?array
    {
        return $this->providerResponse;
    }
}

