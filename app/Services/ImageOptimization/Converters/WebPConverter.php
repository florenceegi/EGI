<?php

namespace App\Services\ImageOptimization\Converters;

use App\Services\ImageOptimization\Contracts\ImageConverterInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Exception;

/**
 * @Oracode Converter: WebPConverter
 * 🎯 Purpose: Convert images to WebP format with optimizations
 * 📥 Input: Source image path and conversion config
 * 📤 Output: Optimized WebP image
 * 🧱 Core Logic: Uses Imagick for WebP conversion with quality optimization
 *
 * @package App\Services\ImageOptimization\Converters
 * @version 1.0.0
 */
class WebPConverter implements ImageConverterInterface {
    protected UltraLogManager $logger;
    protected string $logChannel = 'image_optimization';

    public function __construct(UltraLogManager $logger) {
        $this->logger = $logger;
    }

    /**
     * Convert image to WebP format
     */
    public function convert(string $sourcePath, string $outputPath, array $config): array {
        $logContext = [
            'converter' => 'WebP',
            'source' => $sourcePath,
            'output' => $outputPath,
            'config' => $config
        ];

        $this->logger->info('[WebPConverter] Starting WebP conversion', $logContext);

        try {
            // Verify Imagick availability
            if (!extension_loaded('imagick') || !class_exists('Imagick')) {
                throw new Exception('Imagick extension not available for WebP conversion');
            }

            // Get source file from storage
            $sourceContent = $this->getSourceContent($sourcePath);

            // Create Imagick instance
            $imagick = new \Imagick();
            $imagick->readImageBlob($sourceContent);

            // Apply transformations
            $this->applyTransformations($imagick, $config);

            // Set WebP format and quality
            $imagick->setImageFormat('webp');
            $quality = $config['quality'] ?? $this->getOptimalQuality($config);
            $imagick->setImageCompressionQuality($quality);

            // Generate WebP content
            $webpContent = $imagick->getImageBlob();

            // Save to storage disks
            $savedInfo = $this->saveToStorageDisks($outputPath, $webpContent);

            // Cleanup
            $imagick->clear();
            $imagick->destroy();

            $result = [
                'success' => true,
                'format' => 'webp',
                'path' => $outputPath,
                'size' => strlen($webpContent),
                'quality' => $quality,
                'saved_to' => $savedInfo
            ];

            $this->logger->info(
                '[WebPConverter] WebP conversion completed',
                array_merge($logContext, ['result' => $result])
            );

            return $result;
        } catch (Exception $e) {
            $this->logger->error(
                '[WebPConverter] WebP conversion failed',
                array_merge($logContext, ['error' => $e->getMessage()])
            );
            throw $e;
        }
    }

    /**
     * Check if can handle source format
     */
    public function canHandle(string $mimeType): bool {
        return in_array($mimeType, $this->getSupportedFormats());
    }

    /**
     * Get supported input formats
     */
    public function getSupportedFormats(): array {
        return [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/bmp',
            'image/tiff',
            'image/heic',
            'image/heif'
        ];
    }

    /**
     * Get source file content from storage
     */
    protected function getSourceContent(string $sourcePath): string {
        $storageDisks = Config::get('egi.storage.disks', ['public']);

        foreach ($storageDisks as $disk) {
            if (Storage::disk($disk)->exists($sourcePath)) {
                $content = Storage::disk($disk)->get($sourcePath);
                if ($content !== false) {
                    return $content;
                }
            }
        }

        throw new Exception("Source file not found on any storage disk: {$sourcePath}");
    }

    /**
     * Apply image transformations based on config
     */
    protected function applyTransformations(\Imagick $imagick, array $config): void {
        // Handle resizing
        if (isset($config['width']) || isset($config['height'])) {
            $this->resizeImage($imagick, $config);
        }

        // Handle circular crop for avatar
        if (!empty($config['circle'])) {
            $this->applCircularCrop($imagick);
        }

        // Apply general optimizations
        $this->applyOptimizations($imagick, $config);
    }

    /**
     * Resize image maintaining aspect ratio (no cropping)
     */
    protected function resizeImage(\Imagick $imagick, array $config): void {
        $width = $config['width'] ?? null;
        $height = $config['height'] ?? null;

        if ($width && $height) {
            // Resize maintaining aspect ratio, fitting within the box (no crop)
            $imagick->thumbnailImage($width, $height, true);
        } elseif ($width || $height) {
            // Resize maintaining aspect ratio
            $imagick->thumbnailImage($width, $height, true);
        }
    }

    /**
     * Apply circular crop for avatar images
     */
    protected function applCircularCrop(\Imagick $imagick): void {
        $width = $imagick->getImageWidth();
        $height = $imagick->getImageHeight();
        $size = min($width, $height);

        // Create circular mask
        $mask = new \Imagick();
        $mask->newImage($size, $size, new \ImagickPixel('transparent'));
        $draw = new \ImagickDraw();
        $draw->setFillColor('white');
        $draw->circle($size / 2, $size / 2, $size / 2, 0);
        $mask->drawImage($draw);

        // Apply mask
        $imagick->cropThumbnailImage($size, $size);
        $imagick->compositeImage($mask, \Imagick::COMPOSITE_DSTIN, 0, 0);

        $mask->clear();
        $mask->destroy();
    }

    /**
     * Apply general optimizations
     */
    protected function applyOptimizations(\Imagick $imagick, array $config): void {
        // Strip metadata to reduce file size
        $imagick->stripImage();

        // Set color space for better compression
        $imagick->setImageColorspace(\Imagick::COLORSPACE_SRGB);
    }

    /**
     * Get optimal quality based on variant type
     */
    protected function getOptimalQuality(array $config): int {
        // Return different quality based on image size/purpose
        $width = $config['width'] ?? 1000;

        if ($width <= 100) {
            return 75; // Avatar - lower quality for small size
        } elseif ($width <= 250) {
            return 80; // Thumbnail
        } elseif ($width <= 500) {
            return 85; // Card
        } else {
            return 90; // Original optimization
        }
    }

    /**
     * Save content to configured storage disks
     */
    protected function saveToStorageDisks(string $path, string $content): array {
        $storageDisks = Config::get('egi.storage.disks', ['public']);
        $savedInfo = [];
        $errors = [];

        foreach ($storageDisks as $disk) {
            try {
                $visibility = Config::get("egi.storage.visibility.{$disk}", 'public');
                $success = Storage::disk($disk)->put($path, $content, $visibility);

                if ($success) {
                    $savedInfo[$disk] = Storage::disk($disk)->url($path);
                } else {
                    $errors[$disk] = 'Storage::put returned false';
                }
            } catch (\Exception $e) {
                $errors[$disk] = $e->getMessage();
            }
        }

        if (empty($savedInfo)) {
            throw new Exception('Failed to save to any storage disk: ' . json_encode($errors));
        }

        return $savedInfo;
    }
}
