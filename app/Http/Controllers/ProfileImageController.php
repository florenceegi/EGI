<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @Oracode Controller: Profile Image Management
 * 🎯 Purpose: Handle user profile image upload, selection, and deletion
 * 🖼️ Media: Uses Spatie Media Library for efficient image management
 * 🛡️ Security: Validates file uploads and user permissions
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI Profile Images)
 * @date 2025-01-07
 */
class ProfileImageController extends \App\Http\Controllers\Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        // Authorization handled by route middleware
    }

    /**
     * Upload new profile images (single or multiple)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|RedirectResponse
     */
    public function uploadImage(Request $request) {
        try {
            $user = Auth::user();
            $uploadedMedia = [];

            // Simple validation - just check if files exist
            if (!$request->hasFile('profile_image')) {
                throw new \Exception('No files uploaded');
            }

            $files = $request->file('profile_image');
            if (!is_array($files)) {
                $files = [$files];
            }

            // Validate each file
            foreach ($files as $file) {
                if (!$file->isValid()) {
                    throw new \Exception('Invalid file uploaded');
                }

                if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'])) {
                    throw new \Exception('Invalid file type. Only JPG, PNG, WebP allowed.');
                }

                if ($file->getSize() > 10 * 1024 * 1024) { // 10MB
                    throw new \Exception('File too large. Maximum 10MB allowed.');
                }
            }

            // Upload each image
            foreach ($files as $file) {
                $media = $user->addMedia($file)
                    ->toMediaCollection('profile_images');

                $uploadedMedia[] = $media;
            }

            // If this is the first image(s), set the first one as current
            if ($user->getAllProfileImages()->count() === count($uploadedMedia)) {
                $user->setCurrentProfileImage($uploadedMedia[0]);
            }

            Log::info('Profile images uploaded', [
                'user_id' => $user->id,
                'media_count' => count($uploadedMedia),
                'media_ids' => collect($uploadedMedia)->pluck('id')->toArray()
            ]);

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('profile.image_uploaded_successfully'),
                    'uploaded_count' => count($uploadedMedia)
                ]);
            }

            return redirect()->route('profile.show')
                ->with('success', __('profile.image_uploaded_successfully'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Profile image validation failed', [
                'user_id' => Auth::id(),
                'errors' => $e->errors()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('profile.validation_failed'),
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->route('profile.show')
                ->withErrors($e->errors())
                ->with('error', __('profile.validation_failed'));
        } catch (\Exception $e) {
            Log::error('Profile image upload failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('profile.image_upload_failed') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('profile.show')
                ->with('error', __('profile.image_upload_failed'));
        }
    }

    /**
     * Set a specific image as the current profile image
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function setCurrentImage(Request $request): RedirectResponse {
        $request->validate([
            'media_id' => 'required|integer|exists:media,id',
        ]);

        try {
            $user = Auth::user();
            $media = Media::findOrFail($request->media_id);

            // Verify the media belongs to the user
            if ($media->model_id !== $user->id || $media->collection_name !== 'profile_images') {
                throw new \Exception('Unauthorized access to media');
            }

            $user->setCurrentProfileImage($media);

            Log::info('Current profile image updated', [
                'user_id' => $user->id,
                'media_id' => $media->id
            ]);

            return redirect()->route('profile.show')
                ->with('success', __('profile.current_image_updated'));
        } catch (\Exception $e) {
            Log::error('Failed to set current profile image', [
                'user_id' => Auth::id(),
                'media_id' => $request->media_id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('profile.show')
                ->with('error', __('profile.failed_to_update_current_image'));
        }
    }



    /**
     * Delete a profile image
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteImage(Request $request): RedirectResponse {
        $request->validate([
            'media_id' => 'required|integer|exists:media,id',
        ]);

        try {
            $user = Auth::user();
            $media = Media::findOrFail($request->media_id);

            // Verify the media belongs to the user
            if ($media->model_id !== $user->id || $media->collection_name !== 'profile_images') {
                throw new \Exception('Unauthorized access to media');
            }

            // Check if this is the current profile image
            $isCurrent = $user->profile_photo_path === $media->file_name;

            // Delete the media
            $media->delete();

            // If this was the current image, set another one as current (if available)
            if ($isCurrent) {
                $nextImage = $user->getAllProfileImages()->first();
                if ($nextImage) {
                    $user->setCurrentProfileImage($nextImage);
                } else {
                    // Clear the profile_photo_path if no images left
                    $user->update(['profile_photo_path' => null]);
                }
            }

            Log::info('Profile image deleted', [
                'user_id' => $user->id,
                'media_id' => $media->id,
                'was_current' => $isCurrent
            ]);

            return redirect()->route('profile.show')
                ->with('success', __('profile.image_deleted_successfully'));
        } catch (\Exception $e) {
            Log::error('Failed to delete profile image', [
                'user_id' => Auth::id(),
                'media_id' => $request->media_id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('profile.show')
                ->with('error', __('profile.failed_to_delete_image'));
        }
    }

    /**
     * Upload creator banner image
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|RedirectResponse
     */
    public function uploadBanner(Request $request)
    {
        try {
            $user = Auth::user();
            $uploadedMedia = [];

            // Simple validation - just check if files exist
            if (!$request->hasFile('banner_image')) {
                throw new \Exception('No files uploaded');
            }

            $files = $request->file('banner_image');
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                // Basic file validation
                if (!$file->isValid()) {
                    throw new \Exception('Invalid file upload');
                }

                // Size validation (max 10MB)
                if ($file->getSize() > 10 * 1024 * 1024) {
                    throw new \Exception('File too large. Maximum size is 10MB.');
                }

                // MIME type validation
                $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    throw new \Exception('Invalid file type. Only JPEG, PNG, WebP, and AVIF are allowed.');
                }

                // Add to banner_images collection (allows multiple)
                $media = $user->addMedia($file)
                    ->toMediaCollection('banner_images');

                $uploadedMedia[] = [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'url' => $media->getUrl(),
                    'banner_url' => $media->getUrl('banner'),
                    'banner_mobile_url' => $media->getUrl('banner_mobile'),
                ];

                Log::info('Banner uploaded successfully', [
                    'user_id' => $user->id,
                    'media_id' => $media->id,
                    'file_name' => $media->file_name
                ]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('profile.banner_uploaded_successfully'),
                    'media' => $uploadedMedia
                ]);
            }

            return redirect()->back()
                ->with('success', __('profile.banner_uploaded_successfully'));
        } catch (\Exception $e) {
            Log::error('Banner upload failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Set current banner image
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function setCurrentBanner(Request $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            $mediaId = $request->input('media_id');

            if (!$mediaId) {
                throw new \Exception(__('profile.no_media_id_provided'));
            }

            // Find the banner image
            $banner = $user->getMedia('banner_images')->where('id', $mediaId)->first();

            if (!$banner) {
                throw new \Exception(__('profile.banner_not_found'));
            }

            // Set as current banner
            $user->setCurrentBanner($banner);

            Log::info('Current banner updated successfully', [
                'user_id' => $user->id,
                'media_id' => $mediaId
            ]);

            return redirect()->back()
                ->with('success', __('profile.set_as_banner_success'));

        } catch (\Exception $e) {
            Log::error('Failed to set current banner', [
                'user_id' => Auth::id(),
                'media_id' => $request->input('media_id'),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete specific banner image
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteBanner(Request $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            $mediaId = $request->input('media_id');

            if (!$mediaId) {
                throw new \Exception(__('profile.no_media_id_provided'));
            }

            // Find the banner image
            $banner = $user->getMedia('banner_images')->where('id', $mediaId)->first();

            if (!$banner) {
                throw new \Exception(__('profile.banner_not_found'));
            }

            // Check if this is the current banner and clear it if needed
            $currentBanner = $user->getCurrentBanner();
            if ($currentBanner && $currentBanner->getCustomProperty('source_media_id') == $mediaId) {
                $currentBanner->delete();
            }

            // Delete the banner
            $banner->delete();

            Log::info('Banner deleted successfully', [
                'user_id' => $user->id,
                'media_id' => $mediaId
            ]);

            return redirect()->back()
                ->with('success', __('profile.banner_deleted_successfully'));

        } catch (\Exception $e) {
            Log::error('Banner deletion failed', [
                'user_id' => Auth::id(),
                'media_id' => $request->input('media_id'),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
