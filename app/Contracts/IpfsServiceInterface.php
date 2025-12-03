<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

/**
 * @Oracode Contract: IPFS Service Interface
 * 🎯 Purpose: Define contract for IPFS pinning operations
 * 🧱 Core Logic: Upload files to IPFS and retrieve URLs
 * 
 * @package FlorenceEGI\Contracts
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2025-12-03
 */
interface IpfsServiceInterface
{
    /**
     * Upload a file to IPFS
     *
     * @param UploadedFile|string $file File to upload (UploadedFile or path string)
     * @param array $metadata Optional metadata for the pin
     * @return array{success: bool, cid: string|null, url: string|null, error: string|null}
     */
    public function upload(UploadedFile|string $file, array $metadata = []): array;

    /**
     * Upload file contents directly to IPFS
     *
     * @param string $contents File contents as binary string
     * @param string $filename Original filename for metadata
     * @param array $metadata Optional metadata for the pin
     * @return array{success: bool, cid: string|null, url: string|null, error: string|null}
     */
    public function uploadContents(string $contents, string $filename, array $metadata = []): array;

    /**
     * Get the gateway URL for a CID
     *
     * @param string $cid IPFS Content Identifier
     * @return string Full gateway URL
     */
    public function getGatewayUrl(string $cid): string;

    /**
     * Check if a CID exists/is pinned
     *
     * @param string $cid IPFS Content Identifier
     * @return bool True if pinned, false otherwise
     */
    public function isPinned(string $cid): bool;

    /**
     * Unpin a file from IPFS
     *
     * @param string $cid IPFS Content Identifier
     * @return bool True on success, false on failure
     */
    public function unpin(string $cid): bool;

    /**
     * Check if IPFS service is enabled and configured
     *
     * @return bool True if enabled and properly configured
     */
    public function isEnabled(): bool;
}
