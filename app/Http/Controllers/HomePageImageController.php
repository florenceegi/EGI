<?php

namespace App\Http\Controllers;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Controllers)
 * @date 2026-02-04
 * @purpose Handle multi-image upload for Creator/Company/Collector home pages
 */
class HomePageImageController extends Controller
{
    /**
     * @var UltraLogManager
     */
    private UltraLogManager $logger;

    /**
     * @var ErrorManagerInterface
     */
    private ErrorManagerInterface $errorManager;

    /**
     * @var ConsentService
     */
    private ConsentService $consentService;

    /**
     * @var AuditLogService
     */
    private AuditLogService $auditService;

    /**
     * @var int Maximum file size in KB
     */
    private const MAX_FILE_SIZE_KB = 10240; // 10MB

    /**
     * @var array Allowed image extensions
     */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

    /**
     * Constructor with dependency injection
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        ConsentService $consentService,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->consentService = $consentService;
        $this->auditService = $auditService;
    }

    /**
     * @Oracode Method: Upload Creator Banner Images - ULTRA+GDPR Pattern
     * 🎯 Purpose: Upload multiple creator banner images with GDPR compliance
     * 🛡️ Security: File validation, authorization, consent verification
     * 📊 GDPR: Consent check, audit logging
     * ⚡ UEM: Complete error handling
     *
     * @param Request $request HTTP request with creator_banner files
     * @return JsonResponse|RedirectResponse Success/error response
     */
    public function uploadCreatorBanner(Request $request)
    {
        try {
            $user = Auth::user();
            $uploadedMedia = [];

            // 1. ULM: Log operation start
            $this->logger->info('Creator banner upload initiated', [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // 2. GDPR: Check consent
            if (!$this->consentService->hasConsent($user, 'allow-personal-data-processing')) {
                $this->logger->warning('Creator banner upload attempted without consent', [
                    'user_id' => $user->id,
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('gdpr.consent_required_profile_images')
                    ], 403);
                }

                return redirect()->back()
                    ->withErrors(['consent' => __('gdpr.consent_required_profile_images')]);
            }

            // 3. Request validation
            $request->validate([
                'creator_banners' => 'required',
                'creator_banners*' => 'file|mimes:' . implode(',', self::ALLOWED_EXTENSIONS) . '|max:' . self::MAX_FILE_SIZE_KB
            ]);

            // 4. File processing
            $files = $request->file('creator_banners');
            if (!is_array($files)) {
                $files = [$files];
            }

            // 5. Process uploads
            foreach ($files as $file) {
                $this->logger->info('Creator banner: Attempting addMedia', [
                    'user_id' => $user->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);

                $media = $user->addMedia($file)
                    ->toMediaCollection('creator_banners');

                $uploadedMedia[] = $media;
            }

            // 6. Set first image as current if no previous banners
            if ($user->getAllCreatorBanners()->count() === count($uploadedMedia)) {
                $user->setCurrentCreatorBanner($uploadedMedia[0]);
            }

            // 7. GDPR: Log audit trail
            $this->auditService->logUserAction(
                $user,
                'Creator banners uploaded',
                [
                    'uploaded_count' => count($uploadedMedia),
                    'media_ids' => collect($uploadedMedia)->pluck('id')->toArray(),
                ],
                GdprActivityCategory::PERSONAL_DATA_UPDATE
            );

            // 8. ULM: Log success
            $this->logger->info('Creator banners upload completed', [
                'user_id' => $user->id,
                'media_count' => count($uploadedMedia),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('profile.banners_uploaded_successfully'),
                    'uploaded_count' => count($uploadedMedia)
                ]);
            }

            return redirect()->back()
                ->with('success', __('profile.banners_uploaded_successfully'));

        } catch (\Exception $e) {
            // 9. UEM: Error handling
            $this->errorManager->handle('CREATOR_BANNER_UPLOAD_ERROR', [
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
            ], $e);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('profile.banner_upload_failed')
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('profile.banner_upload_failed'));
        }
    }

    /**
     * @Oracode Method: Set Current Creator Banner - ULTRA+GDPR Pattern
     * 🎯 Purpose: Set specific banner as current with GDPR compliance
     * 🛡️ Security: Authorization validation, media ownership check
     * 📊 GDPR: Audit trail for profile changes
     *
     * @param Request $request Request with media_id parameter
     * @return RedirectResponse|JsonResponse Success/error response
     */
    public function setCurrentCreatorBanner(Request $request)
    {
        try {
            $user = Auth::user();

            // 1. ULM: Log operation start
            $this->logger->info('Set current creator banner initiated', [
                'user_id' => $user->id,
                'media_id' => $request->media_id,
            ]);

            // 2. Request validation
            $request->validate([
                'media_id' => 'required|integer|exists:media,id',
            ]);

            // 3. Get and verify media ownership
            $media = Media::findOrFail($request->media_id);
            if ($media->model_id !== $user->id || $media->collection_name !== 'creator_banners') {
                throw new \Exception('Unauthorized access to media');
            }

            // 4. Update current creator banner
            $user->setCurrentCreatorBanner($media);

            // 5. GDPR: Log audit trail
            $this->auditService->logUserAction(
                $user,
                'Current creator banner changed',
                [
                    'new_media_id' => $media->id,
                    'new_file_name' => $media->file_name,
                ],
                GdprActivityCategory::PERSONAL_DATA_UPDATE
            );

            // 6. ULM: Log success
            $this->logger->info('Current creator banner updated', [
                'user_id' => $user->id,
                'media_id' => $media->id,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('profile.current_banner_updated')
                ]);
            }

            return redirect()->back()
                ->with('success', __('profile.current_banner_updated'));

        } catch (\Exception $e) {
            // 7. UEM: Error handling
            $this->errorManager->handle('CREATOR_BANNER_SET_CURRENT_ERROR', [
                'user_id' => Auth::id(),
                'media_id' => $request->media_id,
            ], $e);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('profile.failed_to_update_current_banner')
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('profile.failed_to_update_current_banner'));
        }
    }

    /**
     * @Oracode Method: Delete Creator Banner - ULTRA+GDPR Pattern
     * 🎯 Purpose: Delete creator banner with GDPR compliance
     * 🛡️ Security: Authorization validation, media ownership verification
     * 📊 GDPR: Audit logging for data deletion
     *
     * @param Request $request Request with media_id to delete
     * @return RedirectResponse|JsonResponse Success/error response
     */
    public function deleteCreatorBanner(Request $request)
    {
        try {
            $user = Auth::user();

            // 1. ULM: Log operation start
            $this->logger->info('Creator banner deletion initiated', [
                'user_id' => $user->id,
                'media_id' => $request->media_id,
            ]);

            // 2. Request validation
            $request->validate([
                'media_id' => 'required|integer|exists:media,id',
            ]);

            // 3. Get and verify media ownership
            $media = Media::findOrFail($request->media_id);
            if ($media->model_id !== $user->id || $media->collection_name !== 'creator_banners') {
                throw new \Exception('Unauthorized access to media');
            }

            // 4. Store deletion information for audit
            $deletionInfo = [
                'deleted_media_id' => $media->id,
                'deleted_file_name' => $media->file_name,
                'remaining_banners_count' => $user->getAllCreatorBanners()->count() - 1
            ];

            // 5. Check if this is the current banner
            $currentBanner = $user->getCurrentCreatorBanner();
            $isCurrent = $currentBanner && $currentBanner->getCustomProperty('source_media_id') == $media->id;

            // 6. Delete the media
            $media->delete();

            // 7. Update current banner if needed
            if ($isCurrent) {
                // Delete current banner reference
                if ($currentBanner) {
                    $currentBanner->delete();
                }

                // Set next banner as current if available
                $nextBanner = $user->getAllCreatorBanners()->first();
                if ($nextBanner) {
                    $user->setCurrentCreatorBanner($nextBanner);
                    $deletionInfo['new_current_file_name'] = $nextBanner->file_name;
                } else {
                    $deletionInfo['new_current_file_name'] = null;
                }
            }

            // 8. GDPR: Log audit trail
            $this->auditService->logUserAction(
                $user,
                'Creator banner deleted',
                $deletionInfo,
                GdprActivityCategory::DATA_DELETION
            );

            // 9. ULM: Log success
            $this->logger->info('Creator banner deleted successfully', [
                'user_id' => $user->id,
                'deleted_media_id' => $deletionInfo['deleted_media_id'],
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('profile.banner_deleted_successfully')
                ]);
            }

            return redirect()->back()
                ->with('success', __('profile.banner_deleted_successfully'));

        } catch (\Exception $e) {
            // 10. UEM: Error handling
            $this->errorManager->handle('CREATOR_BANNER_DELETE_ERROR', [
                'user_id' => Auth::id(),
                'media_id' => $request->media_id,
            ], $e);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('profile.failed_to_delete_banner')
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('profile.failed_to_delete_banner'));
        }
    }
}
