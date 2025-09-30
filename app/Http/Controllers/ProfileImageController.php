<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use App\Enums\Gdpr\GdprActivityCategory;

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - ProfileImageController)
 * @date 2025-09-30
 * @purpose GDPR-compliant profile image management with Ultra Excellence standards
 *
 * @Oracode ULTRA+GDPR Controller: Profile Image Management
 * 🎯 Purpose: Handle user profile image upload, selection, and deletion with full GDPR compliance
 * 🖼️ Media: Uses Spatie Media Library for efficient image management
 * 🛡️ Security: Complete validation, authorization, and audit trail
 * 📊 GDPR: Full consent checking, audit logging, and data subject rights
 * ⚡ UEM: Ultra Error Manager integration for all error handling
 * 🔍 ULM: Complete operation logging with UltraLogManager
 *
 * Features:
 * - GDPR consent verification for profile data changes
 * - Complete audit trail for all image operations
 * - Ultra Error Manager for robust error handling
 * - Security validation and authorization
 * - Configuration-based file validation
 * - Comprehensive logging and monitoring
 */
class ProfileImageController extends \App\Http\Controllers\Controller {

    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;
    protected ConsentService $consentService;

    /**
     * @Oracode Constructor: ULTRA+GDPR Pattern Implementation
     * 🏗️ Purpose: Initialize all required dependencies for GDPR-compliant operation
     * 🛡️ Security: Auth middleware and dependency injection
     * 📊 GDPR: AuditLogService and ConsentService integration
     * ⚡ UEM: ErrorManagerInterface for robust error handling
     * 🔍 ULM: UltraLogManager for comprehensive logging
     *
     * @param UltraLogManager $logger Ultra Log Manager for operation logging
     * @param ErrorManagerInterface $errorManager Ultra Error Manager for error handling
     * @param AuditLogService $auditService GDPR audit trail service
     * @param ConsentService $consentService GDPR consent verification service
     * @return void
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
        $this->middleware('auth');
    }

    /**
     * @Oracode Method: Upload Profile Images - ULTRA+GDPR Pattern
     * 🎯 Purpose: Upload new profile images with complete GDPR compliance and audit trail
     * 🛡️ Security: File validation, authorization, and consent verification
     * 📊 GDPR: Consent check, audit logging, and data subject rights compliance
     * ⚡ UEM: Complete error handling with Ultra Error Manager
     * 🔍 ULM: Comprehensive operation logging
     *
     * @param Request $request HTTP request with profile_image files
     * @return \Illuminate\Http\JsonResponse|RedirectResponse Success/error response
     * @throws \Exception On validation or upload failures
     */
    public function uploadImage(Request $request) {
        try {
            $user = Auth::user();
            $uploadedMedia = [];

            // 1. ULM: Log operation start
            $this->logger->info('Profile image upload initiated', [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'protocol' => $request->secure() ? 'HTTPS' : 'HTTP'
            ]);

            // 2. GDPR: Check consent for profile data processing
            if (!$this->consentService->hasConsent($user, 'allow-personal-data-processing')) {
                $this->logger->warning('Profile image upload attempted without consent', [
                    'user_id' => $user->id,
                    'ip_address' => $request->ip()
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('gdpr.consent_required_profile_images')
                    ], 403);
                }

                return redirect()->route('profile.show')
                    ->withErrors(['consent' => __('gdpr.consent_required_profile_images')]);
            }

            // 3. Request validation
            $request->validate([
                'profile_image' => 'required',
                'profile_image.*' => 'file|mimes:' . implode(',', $this->getAllowedImageExtensions()) . '|max:' . $this->getMaxFileSizeKB()
            ]);

            // 4. File processing
            $files = $request->file('profile_image');
            if (!is_array($files)) {
                $files = [$files];
            }

            // 5. Security validation for each file
            foreach ($files as $file) {
                $this->validateProfileImageFile($file);
            }

            // 6. Process uploads with audit trail
            foreach ($files as $file) {
                $this->logger->info('Profile image: Attempting addMedia', [
                    'user_id' => $user->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'protocol' => $request->secure() ? 'HTTPS' : 'HTTP'
                ]);

                try {
                    $media = $user->addMedia($file)
                        ->toMediaCollection('profile_images');

                    $this->logger->info('Profile image: Media created successfully', [
                        'media_id' => $media->id,
                        'media_file_name' => $media->file_name,
                        'media_collection' => $media->collection_name,
                        'user_id' => $user->id,
                        'protocol' => $request->secure() ? 'HTTPS' : 'HTTP'
                    ]);

                    $uploadedMedia[] = $media;
                } catch (\Exception $e) {
                    $this->logger->error('Profile image: addMedia failed', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'protocol' => $request->secure() ? 'HTTPS' : 'HTTP'
                    ]);
                    throw $e;
                }
            }

            // 7. Set first image as current if no previous images
            if ($user->getAllProfileImages()->count() === count($uploadedMedia)) {
                $user->setCurrentProfileImage($uploadedMedia[0]);
            }

            // 8. GDPR: Log audit trail for profile data modification
            $this->auditService->logUserAction(
                $user,
                'Profile images uploaded',
                [
                    'uploaded_count' => count($uploadedMedia),
                    'media_ids' => collect($uploadedMedia)->pluck('id')->toArray(),
                    'file_names' => collect($uploadedMedia)->pluck('file_name')->toArray(),
                    'is_first_images' => $user->getAllProfileImages()->count() === count($uploadedMedia),
                    'current_profile_photo_path' => $user->fresh()->profile_photo_path
                ],
                GdprActivityCategory::PROFILE_UPDATE
            );

            // 9. ULM: Log successful completion
            $this->logger->info('Profile images upload completed successfully', [
                'user_id' => $user->id,
                'media_count' => count($uploadedMedia),
                'media_ids' => collect($uploadedMedia)->pluck('id')->toArray(),
                'protocol' => $request->secure() ? 'HTTPS' : 'HTTP'
            ]);

            // 10. Success response
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
            // UEM: Handle validation errors
            $this->errorManager->handle('PROFILE_IMAGE_UPLOAD_VALIDATION_ERROR', [
                'user_id' => Auth::id(),
                'validation_errors' => $e->errors(),
                'ip_address' => $request->ip()
            ], $e);

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
            // UEM: Handle all other exceptions
            $this->errorManager->handle('PROFILE_IMAGE_UPLOAD_ERROR', [
                'user_id' => Auth::id(),
                'protocol' => $request->secure() ? 'HTTPS' : 'HTTP',
                'ip_address' => $request->ip()
            ], $e);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('profile.image_upload_failed')
                ], 500);
            }

            return redirect()->route('profile.show')
                ->with('error', __('profile.image_upload_failed'));
        }
    }

    /**
     * @Oracode Helper: Get Allowed Image Extensions
     * 🎯 Purpose: Get allowed file extensions from configuration
     * 📤 Returns: Array of allowed extensions (without dots)
     */
    private function getAllowedImageExtensions(): array {
        $allAllowedTypes = config('AllowedFileType.collection.allowed_mime_types', []);
        $allowedImageTypes = array_filter($allAllowedTypes, function ($mimeType) {
            return strpos($mimeType, 'image/') === 0;
        });

        // Convert MIME types to extensions
        $extensions = [];
        foreach ($allowedImageTypes as $mimeType) {
            switch ($mimeType) {
                case 'image/jpeg':
                    $extensions = array_merge($extensions, ['jpg', 'jpeg']);
                    break;
                case 'image/png':
                    $extensions[] = 'png';
                    break;
                case 'image/webp':
                    $extensions[] = 'webp';
                    break;
                case 'image/avif':
                    $extensions[] = 'avif';
                    break;
            }
        }

        return array_unique($extensions);
    }

    /**
     * @Oracode Helper: Get Maximum File Size in KB
     * 🎯 Purpose: Get max file size from configuration in KB for validation
     * 📤 Returns: Integer KB limit
     */
    private function getMaxFileSizeKB(): int {
        $maxFileSize = config(
            'AllowedFileType.collection.size_limits.image',
            config('AllowedFileType.collection.upload_max_filesize', 10 * 1024 * 1024)
        );

        return (int) ($maxFileSize / 1024); // Convert bytes to KB
    }

    /**
     * @Oracode Helper: Validate Profile Image File
     * 🎯 Purpose: Comprehensive file validation with security checks
     * 🛡️ Security: MIME type, size, and file integrity validation
     *
     * @param \Illuminate\Http\UploadedFile $file File to validate
     * @throws \Exception On validation failure
     */
    private function validateProfileImageFile($file): void {
        if (!$file->isValid()) {
            throw new \Exception('Invalid file uploaded');
        }

        // Get allowed MIME types from config
        $allAllowedTypes = config('AllowedFileType.collection.allowed_mime_types', []);
        $allowedImageTypes = array_filter($allAllowedTypes, function ($mimeType) {
            return strpos($mimeType, 'image/') === 0;
        });

        // Get max file size from config
        $maxFileSize = config(
            'AllowedFileType.collection.size_limits.image',
            config('AllowedFileType.collection.upload_max_filesize', 10 * 1024 * 1024)
        );

        if (!in_array($file->getMimeType(), $allowedImageTypes)) {
            throw new \Exception('Invalid file type. Only allowed image formats accepted.');
        }

        if ($file->getSize() > $maxFileSize) {
            $maxSizeMB = round($maxFileSize / (1024 * 1024), 1);
            throw new \Exception("File too large. Maximum {$maxSizeMB}MB allowed.");
        }
    }

    /**
     * @Oracode Method: Set Current Profile Image - ULTRA+GDPR Pattern
     * 🎯 Purpose: Set specific image as current profile photo with GDPR compliance
     * 🛡️ Security: Authorization validation and media ownership check
     * 📊 GDPR: Consent verification and audit trail for profile changes
     * ⚡ UEM: Complete error handling with Ultra Error Manager
     *
     * @param Request $request Request with media_id parameter
     * @return RedirectResponse Success/error redirect response
     */
    public function setCurrentImage(Request $request): RedirectResponse {
        try {
            $user = Auth::user();

            // 1. ULM: Log operation start
            $this->logger->info('Set current profile image initiated', [
                'user_id' => $user->id,
                'media_id' => $request->media_id,
                'ip_address' => $request->ip()
            ]);

            // 2. GDPR: Check consent for profile data processing
            if (!$this->consentService->hasConsent($user, 'allow-personal-data-processing')) {
                $this->logger->warning('Set current profile image attempted without consent', [
                    'user_id' => $user->id,
                    'media_id' => $request->media_id
                ]);

                return redirect()->route('profile.show')
                    ->withErrors(['consent' => __('gdpr.consent_required_profile_changes')]);
            }

            // 3. Request validation
            $request->validate([
                'media_id' => 'required|integer|exists:media,id',
            ]);

            // 4. Get and verify media ownership
            $media = Media::findOrFail($request->media_id);
            if ($media->model_id !== $user->id || $media->collection_name !== 'profile_images') {
                throw new \Exception('Unauthorized access to media');
            }

            // 5. Store previous state for audit
            $previousProfilePhotoPath = $user->profile_photo_path;

            // 6. Update current profile image
            $user->setCurrentProfileImage($media);

            // 7. GDPR: Log audit trail
            $this->auditService->logUserAction(
                $user,
                'Current profile image changed',
                [
                    'new_media_id' => $media->id,
                    'new_file_name' => $media->file_name,
                    'previous_profile_photo_path' => $previousProfilePhotoPath,
                    'current_profile_photo_path' => $user->fresh()->profile_photo_path
                ],
                GdprActivityCategory::PROFILE_UPDATE
            );

            // 8. ULM: Log success
            $this->logger->info('Current profile image updated successfully', [
                'user_id' => $user->id,
                'media_id' => $media->id,
                'file_name' => $media->file_name
            ]);

            return redirect()->route('profile.show')
                ->with('success', __('profile.current_image_updated'));
        } catch (\Exception $e) {
            // 9. UEM: Error handling
            $this->errorManager->handle('PROFILE_SET_CURRENT_IMAGE_ERROR', [
                'user_id' => Auth::id(),
                'media_id' => $request->media_id,
                'ip_address' => $request->ip()
            ], $e);

            return redirect()->route('profile.show')
                ->with('error', __('profile.failed_to_update_current_image'));
        }
    }



    /**
     * @Oracode Method: Delete Profile Image - ULTRA+GDPR Pattern
     * 🎯 Purpose: Delete profile image with GDPR compliance and audit trail
     * 🛡️ Security: Authorization validation and media ownership verification
     * 📊 GDPR: Consent check, audit logging, and data deletion compliance
     * ⚡ UEM: Complete error handling with Ultra Error Manager
     *
     * @param Request $request Request with media_id to delete
     * @return RedirectResponse Success/error redirect response
     */
    public function deleteImage(Request $request): RedirectResponse {
        try {
            $user = Auth::user();

            // 1. ULM: Log operation start
            $this->logger->info('Profile image deletion initiated', [
                'user_id' => $user->id,
                'media_id' => $request->media_id,
                'ip_address' => $request->ip()
            ]);

            // 2. GDPR: Check consent for profile data processing
            if (!$this->consentService->hasConsent($user, 'allow-personal-data-processing')) {
                $this->logger->warning('Profile image deletion attempted without consent', [
                    'user_id' => $user->id,
                    'media_id' => $request->media_id
                ]);

                return redirect()->route('profile.show')
                    ->withErrors(['consent' => __('gdpr.consent_required_profile_changes')]);
            }

            // 3. Request validation
            $request->validate([
                'media_id' => 'required|integer|exists:media,id',
            ]);

            // 4. Get and verify media ownership
            $media = Media::findOrFail($request->media_id);
            if ($media->model_id !== $user->id || $media->collection_name !== 'profile_images') {
                throw new \Exception('Unauthorized access to media');
            }

            // 5. Store deletion information for audit
            $deletionInfo = [
                'deleted_media_id' => $media->id,
                'deleted_file_name' => $media->file_name,
                'was_current' => $user->profile_photo_path === $media->file_name,
                'remaining_images_count' => $user->getAllProfileImages()->count() - 1
            ];

            // 6. Handle current image logic
            $isCurrent = $user->profile_photo_path === $media->file_name;

            // 7. Delete the media
            $media->delete();

            // 8. Update current image if needed
            if ($isCurrent) {
                $nextImage = $user->getAllProfileImages()->first();
                if ($nextImage) {
                    $user->setCurrentProfileImage($nextImage);
                    $deletionInfo['new_current_file_name'] = $nextImage->file_name;
                } else {
                    $user->update(['profile_photo_path' => null]);
                    $deletionInfo['new_current_file_name'] = null;
                }
            }

            // 9. GDPR: Log audit trail for data deletion
            $this->auditService->logUserAction(
                $user,
                'Profile image deleted',
                $deletionInfo,
                GdprActivityCategory::DATA_DELETION
            );

            // 10. ULM: Log successful deletion
            $this->logger->info('Profile image deleted successfully', [
                'user_id' => $user->id,
                'deleted_media_id' => $deletionInfo['deleted_media_id'],
                'was_current' => $deletionInfo['was_current'],
                'remaining_count' => $deletionInfo['remaining_images_count']
            ]);

            return redirect()->route('profile.show')
                ->with('success', __('profile.image_deleted_successfully'));
        } catch (\Exception $e) {
            // 11. UEM: Error handling
            $this->errorManager->handle('PROFILE_IMAGE_DELETE_ERROR', [
                'user_id' => Auth::id(),
                'media_id' => $request->media_id,
                'ip_address' => $request->ip()
            ], $e);

            return redirect()->route('profile.show')
                ->with('error', __('profile.failed_to_delete_image'));
        }
    }

    /**
     * @Oracode Method: Upload Creator Banner - ULTRA+GDPR Pattern
     * 🎯 Purpose: Upload creator banner images with GDPR compliance
     * 🛡️ Security: File validation and authorization
     * 📊 GDPR: Consent verification and audit trail
     * ⚡ UEM: Complete error handling
     *
     * @param Request $request Request with banner_image files
     * @return \Illuminate\Http\JsonResponse|RedirectResponse Success/error response
     */
    public function uploadBanner(Request $request) {
        try {
            $user = Auth::user();
            $uploadedMedia = [];

            // 1. ULM: Log operation start
            $this->logger->info('Banner upload initiated', [
                'user_id' => $user->id,
                'ip_address' => $request->ip()
            ]);

            // 2. GDPR: Check consent for profile data processing
            if (!$this->consentService->hasConsent($user, 'allow-personal-data-processing')) {
                return $this->handleConsentError($request, 'banner_upload');
            }

            // 3. Request validation
            $request->validate([
                'banner_image' => 'required',
                'banner_image.*' => 'file|mimes:' . implode(',', $this->getAllowedImageExtensions()) . '|max:' . $this->getMaxFileSizeKB()
            ]);

            // 4. File processing
            $files = $request->file('banner_image');
            if (!is_array($files)) {
                $files = [$files];
            }

            // 5. Process uploads
            foreach ($files as $file) {
                $this->validateProfileImageFile($file);

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

                $this->logger->info('Banner uploaded successfully', [
                    'user_id' => $user->id,
                    'media_id' => $media->id,
                    'file_name' => $media->file_name
                ]);
            }

            // 6. GDPR: Log audit trail
            $this->auditService->logUserAction(
                $user,
                'Banner images uploaded',
                [
                    'uploaded_count' => count($uploadedMedia),
                    'media_ids' => collect($uploadedMedia)->pluck('id')->toArray()
                ],
                GdprActivityCategory::PROFILE_UPDATE
            );

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
            // UEM: Error handling
            $this->errorManager->handle('PROFILE_BANNER_UPLOAD_ERROR', [
                'user_id' => Auth::id(),
                'ip_address' => $request->ip()
            ], $e);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('profile.banner_upload_failed')
                ], 422);
            }

            return redirect()->back()
                ->with('error', __('profile.banner_upload_failed'));
        }
    }

    /**
     * @Oracode Method: Set Current Banner - ULTRA+GDPR Pattern
     * 🎯 Purpose: Set current banner with GDPR compliance
     *
     * @param Request $request Request with media_id
     * @return RedirectResponse Success/error response
     */
    public function setCurrentBanner(Request $request): RedirectResponse {
        try {
            $user = Auth::user();

            if (!$this->consentService->hasConsent($user, 'allow-personal-data-processing')) {
                return $this->handleConsentError($request, 'set_banner');
            }

            $mediaId = $request->input('media_id');
            if (!$mediaId) {
                throw new \Exception(__('profile.no_media_id_provided'));
            }

            $banner = $user->getMedia('banner_images')->where('id', $mediaId)->first();
            if (!$banner) {
                throw new \Exception(__('profile.banner_not_found'));
            }

            $user->setCurrentBanner($banner);

            $this->auditService->logUserAction(
                $user,
                'Current banner updated',
                ['media_id' => $mediaId, 'file_name' => $banner->file_name],
                GdprActivityCategory::PROFILE_UPDATE
            );

            $this->logger->info('Current banner updated successfully', [
                'user_id' => $user->id,
                'media_id' => $mediaId
            ]);

            return redirect()->back()
                ->with('success', __('profile.set_as_banner_success'));
        } catch (\Exception $e) {
            $this->errorManager->handle('PROFILE_SET_CURRENT_BANNER_ERROR', [
                'user_id' => Auth::id(),
                'media_id' => $request->input('media_id'),
                'ip_address' => $request->ip()
            ], $e);

            return redirect()->back()
                ->with('error', __('profile.banner_operation_failed'));
        }
    }

    /**
     * @Oracode Method: Delete Banner - ULTRA+GDPR Pattern
     * 🎯 Purpose: Delete banner with GDPR compliance
     *
     * @param Request $request Request with media_id
     * @return RedirectResponse Success/error response
     */
    public function deleteBanner(Request $request): RedirectResponse {
        try {
            $user = Auth::user();

            if (!$this->consentService->hasConsent($user, 'allow-personal-data-processing')) {
                return $this->handleConsentError($request, 'delete_banner');
            }

            $mediaId = $request->input('media_id');
            if (!$mediaId) {
                throw new \Exception(__('profile.no_media_id_provided'));
            }

            $banner = $user->getMedia('banner_images')->where('id', $mediaId)->first();
            if (!$banner) {
                throw new \Exception(__('profile.banner_not_found'));
            }

            $fileName = $banner->file_name;

            // Check and clear current banner if needed
            $currentBanner = $user->getCurrentBanner();
            if ($currentBanner && $currentBanner->getCustomProperty('source_media_id') == $mediaId) {
                $currentBanner->delete();
            }

            $banner->delete();

            $this->auditService->logUserAction(
                $user,
                'Banner image deleted',
                ['deleted_media_id' => $mediaId, 'file_name' => $fileName],
                GdprActivityCategory::DATA_DELETION
            );

            $this->logger->info('Banner deleted successfully', [
                'user_id' => $user->id,
                'media_id' => $mediaId
            ]);

            return redirect()->back()
                ->with('success', __('profile.banner_deleted_successfully'));
        } catch (\Exception $e) {
            $this->errorManager->handle('PROFILE_BANNER_DELETE_ERROR', [
                'user_id' => Auth::id(),
                'media_id' => $request->input('media_id'),
                'ip_address' => $request->ip()
            ], $e);

            return redirect()->back()
                ->with('error', __('profile.banner_operation_failed'));
        }
    }

    /**
     * @Oracode Helper: Handle Consent Errors
     * 🎯 Purpose: Centralized consent error handling
     *
     * @param Request $request Current request
     * @param string $operation Operation being attempted
     * @return RedirectResponse|JsonResponse Error response
     */
    private function handleConsentError(Request $request, string $operation) {
        $this->logger->warning("Operation {$operation} attempted without consent", [
            'user_id' => Auth::id(),
            'ip_address' => $request->ip()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => __('gdpr.consent_required_profile_changes')
            ], 403);
        }

        return redirect()->back()
            ->withErrors(['consent' => __('gdpr.consent_required_profile_changes')]);
    }
}