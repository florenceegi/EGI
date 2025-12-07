<?php

namespace App\Services;

use App\Contracts\ImageOptimizationManagerInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use RuntimeException;

/**
 * Class ImageOptimizationManager
 *
 * @Oracode Manager: Image optimization service with multi-variant processing
 * 🎯 Purpose: Optimizes uploaded images into multiple variants (avatar, thumbnail, card, original)
 * 🧱 Core Logic: Uses Imagick for processing, follows existing storage patterns
 * 🔧 Enhancement: Configurable quality settings and WebP conversion
 *
 * Following NAMING.md: Manager suffix required for business logic classes
 * Following ULTRA_STANDARDS.md: UEM for all errors, ULM for all logging
 * Following PILLAR0.md: No deductions - asks for missing data instead of guessing
 *
 * @package App\Services
 * @author Assistant (following Oracode standards)
 * @version 1.0.0
 *
 * @requires imagick PHP extension for image processing
 */
class ImageOptimizationManager implements ImageOptimizationManagerInterface {
    /**
     * UltraLogManager instance for structured logging
     * @var UltraLogManager
     */
    protected readonly UltraLogManager $logger;

    /**
     * ErrorManagerInterface instance for standardized error handling
     * @var ErrorManagerInterface
     */
    protected readonly ErrorManagerInterface $errorManager;

    /**
     * Default variant configurations
     * @var array
     */
    protected array $defaultVariants = [
        'avatar' => [
            'size' => 80,
            'quality' => 85,
            'format' => 'webp'
        ],
        'thumbnail' => [
            'size' => 200,
            'quality' => 85,
            'format' => 'webp'
        ],
        'card' => [
            'size' => 400,
            'quality' => 80,
            'format' => 'webp'
        ],
        'original' => [
            'quality' => 75,
            'format' => 'webp',
            'max_size' => 1920
        ]
    ];

    /**
     * Supported input MIME types for optimization
     * @var array
     */
    protected array $supportedMimeTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/heic',
        'image/heif'
    ];

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger ULM for structured logging
     * @param ErrorManagerInterface $errorManager UEM for standardized error handling
     *
     * @oracode-di-pattern Full dependency injection for testability
     * @oracode-ultra-integrated ULM and UEM properly injected
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Optimize uploaded image into multiple variants
     *
     * @param UploadedFile $uploadedFile Original uploaded file
     * @param string $storageBasePath Base path for storage (e.g., users_files/collections_123/creator_456)
     * @param string $keyFile File key without extension
     * @param array $variants Variant configurations or empty for defaults
     * @param array $disks Storage disks to save to
     *
     * @return array Paths of created variants ['avatar' => 'path/to/avatar.webp', ...]
     *
     * @throws UltraBaseException On optimization failure
     */
    public function optimizeImage(
        UploadedFile $uploadedFile,
        string $storageBasePath,
        string $keyFile,
        array $variants = [],
        array $disks = ['local']
    ): array {
        // Use default variants if none provided (following PILLAR0 - no deductions)
        if (empty($variants)) {
            $variants = $this->defaultVariants;
        }

        $this->logger->info('Starting image optimization', [
            'base_path' => $storageBasePath,
            'key_file' => $keyFile,
            'variants_count' => count($variants),
            'disks' => $disks,
            'file_size' => $uploadedFile->getSize(),
            'mime_type' => $uploadedFile->getMimeType()
        ]);

        try {
            // Validate file type support
            if (!$this->isOptimizationSupported($uploadedFile->getMimeType())) {
                $this->errorManager->handle('IMAGE_OPTIMIZATION_UNSUPPORTED_FORMAT', [
                    'format' => $uploadedFile->getMimeType(),
                    'supported_formats' => 'JPEG, PNG, WebP, GIF'
                ]);
                throw new \Exception("Unsupported image format: {$uploadedFile->getMimeType()}");
            }

            // Check Imagick availability
            if (!extension_loaded('imagick')) {
                $this->errorManager->handle('IMAGE_OPTIMIZATION_IMAGICK_UNAVAILABLE');
                throw new \Exception('Imagick extension not available');
            }

            $processedVariants = [];
            $startTime = microtime(true);

            // Process each variant
            foreach ($variants as $variantName => $config) {
                try {
                    $this->logger->debug("Processing variant: {$variantName}", [
                        'variant_config' => $config,
                        'variant_name' => $variantName
                    ]);

                    $processedPath = $this->processVariant(
                        $uploadedFile,
                        $storageBasePath,
                        $keyFile,
                        $variantName,
                        $config,
                        $disks
                    );

                    $processedVariants[$variantName] = $processedPath;
                } catch (\Exception $e) {
                    $this->errorManager->handle('IMAGE_OPTIMIZATION_VARIANT_CREATION_FAILED', [
                        'variant_type' => $variantName,
                        'dimensions' => isset($config['size']) ? "{$config['size']}x{$config['size']}" : 'original',
                        'error' => $e->getMessage()
                    ]);

                    // Continue with other variants, don't fail completely
                    continue;
                }
            }

            $this->logger->info('Image optimization completed', [
                'total_variants' => count($processedVariants),
                'success_count' => count($processedVariants),
                'processing_time' => microtime(true) - $startTime
            ]);

            return $processedVariants;
        } catch (\Exception $e) {
            $this->errorManager->handle('IMAGE_OPTIMIZATION_PROCESSING_FAILED', [
                'file_path' => $uploadedFile->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Process single image variant
     *
     * @param UploadedFile $uploadedFile Original file
     * @param string $storageBasePath Base storage path
     * @param string $keyFile File key
     * @param string $variantName Variant name (avatar, thumbnail, etc.)
     * @param array $config Variant configuration
     * @param array $disks Storage disks
     *
     * @return string Path to processed file
     *
     * @throws UltraBaseException On processing failure
     */
    protected function processVariant(
        UploadedFile $uploadedFile,
        string $storageBasePath,
        string $keyFile,
        string $variantName,
        array $config,
        array $disks
    ): string {
        try {
            // Create Imagick instance
            $imagick = new \Imagick();
            $imagick->readImageBlob($uploadedFile->get());

            // Get original dimensions
            $originalWidth = $imagick->getImageWidth();
            $originalHeight = $imagick->getImageHeight();

            // Apply variant-specific processing
            if ($variantName === 'original') {
                $this->processOriginalVariant($imagick, $config, $originalWidth, $originalHeight);
            } else {
                $this->processResizedVariant($imagick, $config, $originalWidth, $originalHeight);
            }

            // Set output format and quality
            $outputFormat = $config['format'] ?? 'webp';
            $quality = $config['quality'] ?? 80;

            $imagick->setImageFormat($outputFormat);
            $imagick->setImageCompressionQuality($quality);

            // Strip metadata for privacy
            $imagick->stripImage();

            // Generate file path with optimized extension
            $extension = $this->getOptimizedExtension($uploadedFile->getMimeType());
            $filename = $variantName === 'original'
                ? "{$keyFile}.{$extension}"
                : "{$keyFile}_{$variantName}.{$extension}";

            $fullPath = "{$storageBasePath}/{$filename}";

            // Save to all specified disks
            $imageBlob = $imagick->getImageBlob();
            foreach ($disks as $disk) {
                Storage::disk($disk)->put($fullPath, $imageBlob);
            }

            $imagick->clear();
            $imagick->destroy();

            $this->logger->debug("Variant processed successfully", [
                'variant' => $variantName,
                'path' => $fullPath,
                'size' => strlen($imageBlob),
                'disks' => $disks
            ]);

            return $fullPath;
        } catch (\Exception $e) {
            $this->errorManager->handle('IMAGE_OPTIMIZATION_VARIANT_CREATION_FAILED', [
                'variant_type' => $variantName,
                'dimensions' => isset($config['size']) ? "{$config['size']}x{$config['size']}" : 'original',
                'error' => $e->getMessage()
            ]);

            throw new \Exception("Failed to process image variant '{$variantName}': {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Process original variant (quality optimization only)
     *
     * @param \Imagick $imagick Imagick instance
     * @param array $config Variant configuration
     * @param int $originalWidth Original width
     * @param int $originalHeight Original height
     */
    protected function processOriginalVariant(\Imagick $imagick, array $config, int $originalWidth, int $originalHeight): void {
        $maxSize = $config['max_size'] ?? 1920;

        // Only resize if image is larger than max size
        if ($originalWidth > $maxSize || $originalHeight > $maxSize) {
            if ($originalWidth > $originalHeight) {
                $newWidth = $maxSize;
                $newHeight = intval(($originalHeight * $maxSize) / $originalWidth);
            } else {
                $newHeight = $maxSize;
                $newWidth = intval(($originalWidth * $maxSize) / $originalHeight);
            }

            $imagick->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1);
        }
    }

    /**
     * Process resized variant (maintaining aspect ratio, no cropping)
     *
     * @param \Imagick $imagick Imagick instance
     * @param array $config Variant configuration
     * @param int $originalWidth Original width
     * @param int $originalHeight Original height
     */
    protected function processResizedVariant(\Imagick $imagick, array $config, int $originalWidth, int $originalHeight): void {
        $targetWidth = $config['width'] ?? $config['size'] ?? 300;
        $targetHeight = $config['height'] ?? $config['size'] ?? 300;

        // Resize maintaining aspect ratio (best fit within target dimensions)
        // This will NOT crop the image, just scale it to fit within the box
        $imagick->thumbnailImage($targetWidth, $targetHeight, true);
    }

    /**
     * Get default variant configurations
     *
     * @return array Default variant settings
     */
    public function getDefaultVariants(): array {
        return $this->defaultVariants;
    }

    /**
     * Check if file type is supported for optimization
     *
     * @param string $mimeType MIME type to check
     *
     * @return bool True if supported
     */
    public function isOptimizationSupported(string $mimeType): bool {
        return in_array(strtolower($mimeType), $this->supportedMimeTypes);
    }

    /**
     * Get optimized file extension for given input
     *
     * @param string $originalMimeType Original file MIME type
     *
     * @return string Optimized extension (e.g., 'webp', 'jpg')
     */
    public function getOptimizedExtension(string $originalMimeType): string {
        // For now, default to WebP for better compression
        // Can be made configurable in the future
        return 'webp';
    }

    /**
     * Calculate variant file path from base storage path and key file
     *
     * @param string $storageBasePath Base storage path (e.g., users_files/collections_123/creator_456)
     * @param string $keyFile File key without extension
     * @param string $variantName Variant name (avatar, thumbnail, card, original)
     * @param string $extension File extension (e.g., 'webp')
     *
     * @return string Full path to variant file
     *
     * @oracode-pattern Dynamic path calculation following configuration conventions
     */
    public function getVariantPath(
        string $storageBasePath,
        string $keyFile,
        string $variantName,
        string $extension = 'webp'
    ): string {
        if ($variantName === 'original') {
            return "{$storageBasePath}/{$keyFile}.{$extension}";
        }

        return "{$storageBasePath}/{$keyFile}_{$variantName}.{$extension}";
    }

    /**
     * Get all variant paths for a given file
     *
     * @param string $storageBasePath Base storage path
     * @param string $keyFile File key without extension
     * @param array $variants Variant configurations (if empty, uses defaults)
     *
     * @return array Associative array of variant paths ['avatar' => 'path/to/avatar.webp', ...]
     *
     * @oracode-pattern Convention-based path calculation, no database needed
     */
    public function getAllVariantPaths(
        string $storageBasePath,
        string $keyFile,
        array $variants = []
    ): array {
        if (empty($variants)) {
            $variants = $this->defaultVariants;
        }

        $paths = [];
        $extension = $this->getOptimizedExtension('image/jpeg'); // Default extension

        foreach (array_keys($variants) as $variantName) {
            $paths[$variantName] = $this->getVariantPath(
                $storageBasePath,
                $keyFile,
                $variantName,
                $extension
            );
        }

        return $paths;
    }

    /**
     * Check if optimized variants exist for a file
     *
     * @param string $storageBasePath Base storage path
     * @param string $keyFile File key without extension
     * @param string $disk Storage disk to check
     * @param array $variants Variant configurations (if empty, uses defaults)
     *
     * @return array Array of existing variant paths
     *
     * @oracode-pattern Dynamic existence check using Storage facade
     */
    public function getExistingVariants(
        string $storageBasePath,
        string $keyFile,
        string $disk = 'local',
        array $variants = []
    ): array {
        $allPaths = $this->getAllVariantPaths($storageBasePath, $keyFile, $variants);
        $existingPaths = [];

        foreach ($allPaths as $variantName => $path) {
            if (Storage::disk($disk)->exists($path)) {
                $existingPaths[$variantName] = $path;
            }
        }

        return $existingPaths;
    }
}