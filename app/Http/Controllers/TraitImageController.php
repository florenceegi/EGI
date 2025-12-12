<?php

namespace App\Http\Controllers;

use App\Models\EgiTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Exception;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * Trait Image Controller
 * Handles image upload and management for individual traits
 *
 * @package FlorenceEGI\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (Trait Image System)
 * @date 2025-09-01
 */
class TraitImageController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(UltraLogManager $logger, ErrorManagerInterface $errorManager) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Upload image for a trait
     */
    public function uploadImage(Request $request): JsonResponse {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'trait_id' => 'required|exists:egi_traits,id',
                'trait_image' => 'required|image|mimes:jpeg,png,webp,gif|max:5120', // 5MB max
                'image_alt_text' => 'nullable|string|max:255',
                'image_description' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find trait
            $trait = EgiTrait::findOrFail($request->trait_id);

            // Remove existing image if any
            $trait->clearMediaCollection('trait_images');

            // Add new image
            $mediaItem = $trait->addMediaFromRequest('trait_image')
                ->toMediaCollection('trait_images');

            // Update trait metadata
            $trait->update([
                'image_alt_text' => $request->image_alt_text,
                'image_description' => $request->image_description,
                'image_updated_at' => now(),
            ]);

            // Refresh trait to get updated attributes
            $trait->refresh();

            return response()->json([
                'success' => true,
                'message' => __('traits.upload_success'),
                'image_url' => $trait->image_url,
                'thumbnail_url' => $trait->thumbnail_url,
                'modal_image_url' => $trait->modal_image_url,
                'image_alt_text' => $trait->image_alt_text,
                'image_description' => $trait->image_description,
                'data' => [
                    'image_url' => $trait->image_url,
                    'thumbnail_url' => $trait->thumbnail_url,
                    'modal_image_url' => $trait->modal_image_url,
                    'image_alt_text' => $trait->image_alt_text,
                    'image_description' => $trait->image_description,
                ]
            ]);
        } catch (Exception $e) {
            return $this->errorManager->handle('TRAIT_IMAGE_UPLOAD_ERROR', [
                'trait_id' => $request->trait_id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Delete image for a trait
     */
    public function deleteImage(EgiTrait $trait): JsonResponse {
        try {
            // Check if trait has image
            if (!$trait->getFirstMedia('trait_images')) {
                return response()->json([
                    'success' => false,
                    'message' => __('traits.no_image_to_delete')
                ], 404);
            }

            // Remove image
            $trait->clearMediaCollection('trait_images');

            // Clear image metadata
            $trait->update([
                'image_alt_text' => null,
                'image_description' => null,
                'image_updated_at' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('traits.delete_success')
            ]);
        } catch (Exception $e) {
            return $this->errorManager->handle('TRAIT_IMAGE_DELETE_ERROR', [
                'trait_id' => $trait->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Get trait image information
     */
    public function getImageInfo(EgiTrait $trait): JsonResponse {
        try {
            $media = $trait->getFirstMedia('trait_images');

            return response()->json([
                'success' => true,
                'data' => [
                    'has_image' => !is_null($media),
                    'image_url' => $trait->image_url,
                    'thumbnail_url' => $trait->thumbnail_url,
                    'modal_image_url' => $trait->modal_image_url,
                    'image_alt_text' => $trait->image_alt_text,
                    'image_description' => $trait->image_description,
                    'image_updated_at' => $trait->image_updated_at,
                    'media_info' => $media ? [
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'human_readable_size' => $media->human_readable_size,
                    ] : null
                ]
            ]);
        } catch (Exception $e) {
            return $this->errorManager->handle('TRAIT_IMAGE_INFO_ERROR', [
                'trait_id' => $trait->id,
                'error' => $e->getMessage()
            ], $e);
        }
    }
}
