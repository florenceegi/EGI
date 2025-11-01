<?php

/**
 * @package App\Http\Controllers\Upload\Config
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.2.0 (FlorenceEGI - OS3 Compliant)
 * @date 2025-11-01
 * @purpose Global configuration controller - Override vendor to expand JS translations
 *
 * OS3 COMPLIANCE:
 * - NO Facades (dependency injection only)
 * - ULM + UEM properly injected
 * - GDPR audit trail
 */

namespace App\Http\Controllers\Upload\Config;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UploadManager\Services\SizeParser;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Exception;

class GlobalConfigController extends Controller {
    /**
     * Dependencies (OS3 - NO Facades!)
     */
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;
    protected SizeParser $sizeParser;

    /**
     * Constructor with dependency injection (OS3 pattern + GDPR)
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        SizeParser $sizeParser
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->sizeParser = $sizeParser;
    }

    /**
     * Get global upload configuration
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getGlobalConfig(Request $request): JsonResponse {
        try {
            $lang = app()->getLocale();
            $defaultHostingService = getDefaultHostingService() ?? 'default';

            // ULM: Log config request
            $this->logger->info('Global upload config requested', [
                'lang' => $lang,
                'hosting_service' => $defaultHostingService,
                'log_category' => 'UPLOAD_CONFIG_REQUEST'
            ]);

            // Get JS translations and expand them to root level for window.* access
            $jsTranslations = trans('uploadmanager::uploadmanager.js');

            $config = [
                'currentLang' => $lang,
                'availableLangs' => ['it', 'en', 'fr', 'pt', 'es', 'de'],
                'translations' => [
                    'labels' => [
                        'file_upload' => trans('uploadmanager::uploadmanager.file_upload'),
                        'max_file_size_reminder' => trans('uploadmanager::uploadmanager.max_file_size_reminder'),
                        'upload_your_files' => trans('uploadmanager::uploadmanager.upload_your_files'),
                        'save_the_files' => trans('uploadmanager::uploadmanager.save_the_files'),
                        'cancel' => trans('uploadmanager::uploadmanager.cancel'),
                        'return_to_collection' => trans('uploadmanager::uploadmanager.return_to_collection'),
                        'virus_scan_disabled' => trans('uploadmanager::uploadmanager.virus_scan_disabled'),
                        'virus_scan_enabled' => trans('uploadmanager::uploadmanager.virus_scan_enabled'),
                    ],
                ],
                'envMode' => app()->environment(),
                'defaultHostingService' => $defaultHostingService,
                'imagesPath' => config('app.images_path'),
                'sendEmail' => config('error_constants.SEND_EMAIL'),
                'devTeamEmailAddress' => config('app.devteam_email'),
                'URLRedirectToCollection' => config('app.redirect_to_collection'),
                'redirectToUrlAfterUpload' => config('app.redirect_to_url_after_upload'),
                'errorDelTempLocalFileCode' => config('error_constants.ERROR_DELETING_LOCAL_TEMP_FILE'),
                'errorDelTempExtFileCode' => config('error_constants.ERROR_DELETING_EXT_TEMP_FILE'),
                'enableToCreateDirectory' => config('error_constants.UNABLE_TO_CREATE_DIRECTORY'),
                'enableToChangePermissions' => config('error_constants.UNABLE_TO_CHANGE_PERMISSIONS'),
                'settingAttempts' => config('app.setting_attempt'),
                'temporaryFolder' => config('app.bucket_temp_file_folder'),
                'allowedExtensions' => config('AllowedFileType.collection.allowed_extensions'),
                'allowedMimeTypes' => config('AllowedFileType.collection.allowed_mime_types'),
                'postMaxSize' => config('AllowedFileType.collection.post_max_size'),
                'uploadMaxFileSize' => config('AllowedFileType.collection.upload_max_filesize'),
                'maxFileUploads' => config('AllowedFileType.collection.max_file_uploads'),
                'uploadTypePaths' => config('upload-manager.upload_types.paths', [
                    '/uploading/egi' => 'egi',
                    '/uploading/epp' => 'epp',
                    '/uploading/utility' => 'utility',
                ]),
                'defaultUploadType' => config('upload-manager.upload_types.default', 'default'),
            ];

            // Merge JS translations directly into config root for window.* access
            // This way window.uploadProcessingError exists instead of window.translations.js.uploadProcessingError
            $config = array_merge($config, $jsTranslations);

            // GDPR: Audit trail for config access (if user authenticated)
            if ($user = auth()->user()) {
                $this->auditService->logUserAction(
                    $user,
                    'upload_config_accessed',
                    [
                        'lang' => $lang,
                        'hosting_service' => $defaultHostingService,
                    ],
                    GdprActivityCategory::CONTENT_CREATION
                );
            }

            return response()->json($config);
        } catch (Exception $e) {
            // UEM: Handle error (OS3 pattern)
            $this->errorManager->handle('UPLOAD_CONFIG_ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], $e);

            return response()->json([
                'error' => 'Configuration error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check upload authorization
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkUploadAuthorization(Request $request): JsonResponse {
        try {
            $user = auth()->user();

            // ULM: Log authorization check
            $this->logger->info('Upload authorization check', [
                'user_id' => $user?->id,
                'authenticated' => (bool) $user,
                'log_category' => 'UPLOAD_AUTH_CHECK'
            ]);

            if (!$user) {
                return response()->json([
                    'authorized' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            // GDPR: Audit trail for authorization check
            $this->auditService->logUserAction(
                $user,
                'upload_authorization_checked',
                [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
                GdprActivityCategory::CONTENT_CREATION
            );

            return response()->json([
                'authorized' => true,
                'user_id' => $user->id,
            ]);
        } catch (Exception $e) {
            // UEM: Handle error
            $this->errorManager->handle('UPLOAD_AUTH_CHECK_ERROR', [
                'error' => $e->getMessage(),
            ], $e);

            return response()->json([
                'authorized' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}