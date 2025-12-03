<?php

namespace App\Services\Ipfs;

use App\Contracts\IpfsServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: IPFS Pinning Service (Pinata Implementation)
 * 🎯 Purpose: Handle IPFS uploads via Pinata for original EGI images
 * 🧱 Core Logic: Upload files to Pinata, get CID, construct gateway URLs
 * 🛡️ Privacy: No personal data stored on IPFS, only EGI artwork images
 * 
 * @package FlorenceEGI\Services\Ipfs
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2025-12-03
 * 
 * @dependency config/ipfs.php - IPFS configuration
 * @dependency Ultra\UltraLogManager - Logging
 */
class IpfsService implements IpfsServiceInterface
{
    /**
     * ULM Logger instance
     */
    protected UltraLogManager $logger;

    /**
     * Log channel for IPFS operations
     */
    protected string $logChannel;

    /**
     * Constructor
     *
     * @param UltraLogManager $logger
     */
    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
        $this->logChannel = config('ipfs.logging.channel', 'egi_upload');
    }

    /**
     * Check if IPFS service is enabled and configured
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        if (!config('ipfs.enabled', false)) {
            return false;
        }

        $provider = config('ipfs.provider', 'disabled');
        
        if ($provider === 'disabled') {
            return false;
        }

        if ($provider === 'pinata') {
            $jwt = config('ipfs.pinata.jwt');
            return !empty($jwt);
        }

        return false;
    }

    /**
     * Upload a file to IPFS
     *
     * @param UploadedFile|string $file
     * @param array $metadata
     * @return array{success: bool, cid: string|null, url: string|null, error: string|null}
     */
    public function upload(UploadedFile|string $file, array $metadata = []): array
    {
        if (!$this->isEnabled()) {
            return $this->errorResponse('IPFS service is disabled or not configured');
        }

        try {
            // Get file path and contents
            if ($file instanceof UploadedFile) {
                $filePath = $file->getRealPath();
                $filename = $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
            } else {
                $filePath = $file;
                $filename = basename($file);
                $mimeType = \mime_content_type($file) ?: 'application/octet-stream';
            }

            // Validate file exists
            if (!file_exists($filePath)) {
                return $this->errorResponse("File not found: {$filePath}");
            }

            // Check file size
            $maxSizeMb = config('ipfs.upload.max_size_mb', 50);
            $fileSizeMb = filesize($filePath) / (1024 * 1024);
            
            if ($maxSizeMb > 0 && $fileSizeMb > $maxSizeMb) {
                return $this->errorResponse("File too large: {$fileSizeMb}MB exceeds limit of {$maxSizeMb}MB");
            }

            // Check MIME type
            $allowedMimes = config('ipfs.upload.allowed_mimes', []);
            if (!empty($allowedMimes) && !in_array($mimeType, $allowedMimes)) {
                return $this->errorResponse("MIME type not allowed: {$mimeType}");
            }

            // Read file contents
            $contents = file_get_contents($filePath);
            if ($contents === false) {
                return $this->errorResponse("Failed to read file: {$filePath}");
            }

            return $this->uploadContents($contents, $filename, $metadata);

        } catch (\Exception $e) {
            $this->logError('IPFS upload exception', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Upload file contents directly to IPFS
     *
     * @param string $contents
     * @param string $filename
     * @param array $metadata
     * @return array{success: bool, cid: string|null, url: string|null, error: string|null}
     */
    public function uploadContents(string $contents, string $filename, array $metadata = []): array
    {
        if (!$this->isEnabled()) {
            return $this->errorResponse('IPFS service is disabled or not configured');
        }

        $provider = config('ipfs.provider');

        if ($provider === 'pinata') {
            return $this->uploadToPinata($contents, $filename, $metadata);
        }

        return $this->errorResponse("Unknown IPFS provider: {$provider}");
    }

    /**
     * Upload to Pinata IPFS pinning service
     *
     * @param string $contents
     * @param string $filename
     * @param array $metadata
     * @return array
     */
    protected function uploadToPinata(string $contents, string $filename, array $metadata = []): array
    {
        $jwt = config('ipfs.pinata.jwt');
        $uploadUrl = config('ipfs.pinata.upload_url', 'https://uploads.pinata.cloud');
        $timeout = config('ipfs.upload.timeout', 120);
        $retryAttempts = config('ipfs.upload.retry_attempts', 3);
        $retryDelay = config('ipfs.upload.retry_delay', 2);

        // Build pin metadata
        $pinName = $this->buildPinName($filename, $metadata);
        $pinMetadata = $this->buildPinMetadata($metadata);

        // Pinata options
        $pinataOptions = [
            'cidVersion' => config('ipfs.pinata.pin_options.cidVersion', 1),
        ];

        $lastError = null;

        // Retry loop
        for ($attempt = 1; $attempt <= $retryAttempts; $attempt++) {
            try {
                $this->logInfo("Pinata upload attempt {$attempt}/{$retryAttempts}", [
                    'filename' => $filename,
                    'size_bytes' => strlen($contents),
                ]);

                // Create multipart request
                $response = Http::timeout($timeout)
                    ->withHeaders([
                        'Authorization' => "Bearer {$jwt}",
                    ])
                    ->attach('file', $contents, $filename)
                    ->post("{$uploadUrl}/pinning/pinFileToIPFS", [
                        'pinataOptions' => json_encode($pinataOptions),
                        'pinataMetadata' => json_encode([
                            'name' => $pinName,
                            'keyvalues' => $pinMetadata,
                        ]),
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $cid = $data['IpfsHash'] ?? null;

                    if (empty($cid)) {
                        $lastError = 'Pinata response missing IpfsHash';
                        continue;
                    }

                    $gatewayUrl = $this->getGatewayUrl($cid);

                    $this->logSuccess('Pinata upload successful', [
                        'cid' => $cid,
                        'url' => $gatewayUrl,
                        'pin_size' => $data['PinSize'] ?? 0,
                        'timestamp' => $data['Timestamp'] ?? null,
                    ]);

                    return [
                        'success' => true,
                        'cid' => $cid,
                        'url' => $gatewayUrl,
                        'error' => null,
                        'pin_size' => $data['PinSize'] ?? 0,
                    ];
                }

                // Handle error response
                $errorBody = $response->body();
                $lastError = "Pinata API error (HTTP {$response->status()}): {$errorBody}";
                
                $this->logError('Pinata upload failed', [
                    'attempt' => $attempt,
                    'status' => $response->status(),
                    'body' => $errorBody,
                ]);

            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                $this->logError('Pinata upload exception', [
                    'attempt' => $attempt,
                    'error' => $lastError,
                ]);
            }

            // Wait before retry (except on last attempt)
            if ($attempt < $retryAttempts) {
                sleep($retryDelay);
            }
        }

        return $this->errorResponse($lastError ?? 'Upload failed after all retry attempts');
    }

    /**
     * Get the gateway URL for a CID
     *
     * @param string $cid
     * @return string
     */
    public function getGatewayUrl(string $cid): string
    {
        $useDedicated = config('ipfs.gateway.use_dedicated', true);
        
        if ($useDedicated) {
            $gateway = config('ipfs.pinata.gateway');
            if (!empty($gateway)) {
                // Ensure gateway has proper format
                $gateway = rtrim($gateway, '/');
                if (!str_starts_with($gateway, 'http')) {
                    $gateway = "https://{$gateway}";
                }
                return "{$gateway}/ipfs/{$cid}";
            }
        }

        // Fallback to public gateway
        $publicGateway = config('ipfs.gateway.public_fallback', 'https://ipfs.io/ipfs/');
        return rtrim($publicGateway, '/') . '/' . $cid;
    }

    /**
     * Check if a CID is pinned
     *
     * @param string $cid
     * @return bool
     */
    public function isPinned(string $cid): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        try {
            $jwt = config('ipfs.pinata.jwt');
            $apiUrl = config('ipfs.pinata.api_url', 'https://api.pinata.cloud');

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$jwt}",
                ])
                ->get("{$apiUrl}/data/pinList", [
                    'hashContains' => $cid,
                    'status' => 'pinned',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return ($data['count'] ?? 0) > 0;
            }

            return false;

        } catch (\Exception $e) {
            $this->logError('IPFS isPinned check failed', [
                'cid' => $cid,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Unpin a file from IPFS
     *
     * @param string $cid
     * @return bool
     */
    public function unpin(string $cid): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        try {
            $jwt = config('ipfs.pinata.jwt');
            $apiUrl = config('ipfs.pinata.api_url', 'https://api.pinata.cloud');

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$jwt}",
                ])
                ->delete("{$apiUrl}/pinning/unpin/{$cid}");

            if ($response->successful()) {
                $this->logInfo('IPFS unpin successful', ['cid' => $cid]);
                return true;
            }

            $this->logError('IPFS unpin failed', [
                'cid' => $cid,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;

        } catch (\Exception $e) {
            $this->logError('IPFS unpin exception', [
                'cid' => $cid,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Build pin name from filename and metadata
     *
     * @param string $filename
     * @param array $metadata
     * @return string
     */
    protected function buildPinName(string $filename, array $metadata): string
    {
        $prefix = config('ipfs.metadata.name_prefix', 'florenceegi');
        $egiId = $metadata['egi_id'] ?? null;
        
        if ($egiId) {
            return "{$prefix}-egi-{$egiId}";
        }

        return "{$prefix}-" . pathinfo($filename, PATHINFO_FILENAME);
    }

    /**
     * Build pin metadata from input metadata
     *
     * @param array $metadata
     * @return array
     */
    protected function buildPinMetadata(array $metadata): array
    {
        $result = [
            'source' => 'florenceegi',
            'uploaded_at' => now()->toIso8601String(),
        ];

        if (config('ipfs.metadata.include_egi_id') && isset($metadata['egi_id'])) {
            $result['egi_id'] = (string) $metadata['egi_id'];
        }

        if (config('ipfs.metadata.include_collection_id') && isset($metadata['collection_id'])) {
            $result['collection_id'] = (string) $metadata['collection_id'];
        }

        if (config('ipfs.metadata.include_creator') && isset($metadata['creator'])) {
            $result['creator'] = (string) $metadata['creator'];
        }

        return $result;
    }

    /**
     * Create error response array
     *
     * @param string $message
     * @return array
     */
    protected function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'cid' => null,
            'url' => null,
            'error' => $message,
        ];
    }

    /**
     * Log info message
     *
     * @param string $message
     * @param array $context
     */
    protected function logInfo(string $message, array $context = []): void
    {
        if (config('ipfs.logging.log_success', true)) {
            Log::channel($this->logChannel)->info("[IpfsService] {$message}", $context);
        }
    }

    /**
     * Log success message
     *
     * @param string $message
     * @param array $context
     */
    protected function logSuccess(string $message, array $context = []): void
    {
        if (config('ipfs.logging.log_success', true)) {
            Log::channel($this->logChannel)->info("[IpfsService] ✅ {$message}", $context);
        }
    }

    /**
     * Log error message
     *
     * @param string $message
     * @param array $context
     */
    protected function logError(string $message, array $context = []): void
    {
        if (config('ipfs.logging.log_failures', true)) {
            Log::channel($this->logChannel)->error("[IpfsService] ❌ {$message}", $context);
        }
    }
}
