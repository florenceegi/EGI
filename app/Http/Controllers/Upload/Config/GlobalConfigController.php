<?php

/**
 * @package App\Http\Controllers\Upload\Config
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.3.0 (FlorenceEGI - OS3 Compliant + Complete)
 * @date 2025-11-01
 * @purpose Global configuration controller - Override vendor to expand JS translations
 * 
 * OS3 COMPLIANCE:
 * - NO ULTRA Facades (dependency injection only)
 * - ULM + UEM + GDPR properly injected
 * - GDPR audit trail for operations
 */

namespace App\Http\Controllers\Upload\Config;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UploadManager\Services\SizeParser;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Exception;

class GlobalConfigController extends Controller
{
    /**
     * The logging channel name
     */
    protected string $channel = 'upload';
    
    /**
     * Dependencies (OS3 pattern)
     */
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;
    protected SizeParser $sizeParser;

    /**
     * Constructor with dependency injection (OS3 + GDPR)
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
     */
    public function getGlobalConfig(Request $request): JsonResponse
    {
        try {
            $lang = app()->getLocale();
            $defaultHostingService = getDefaultHostingService() ?? 'default';

            // ULM: Log config request
            $this->logger->info('Global upload config requested', [
                'lang' => $lang,
                'hosting_service' => $defaultHostingService,
                'log_category' => 'UPLOAD_CONFIG_REQUEST'
            ]);
            
            Log::channel($this->channel)->info('Default Hosting Service: ' . $defaultHostingService);

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
            $config = array_merge($config, $jsTranslations);

            // GDPR: Audit trail (if user authenticated)
            if ($user = auth()->user()) {
                $this->auditService->logUserAction(
                    $user,
                    'upload_config_accessed',
                    ['lang' => $lang, 'hosting_service' => $defaultHostingService],
                    GdprActivityCategory::CONTENT_CREATION
                );
            }

            return response()->json($config);
            
        } catch (Exception $e) {
            Log::channel($this->channel)->error('Error in getGlobalConfig: ' . $e->getMessage());
            
            // UEM: Handle error
            return $this->errorManager->handle('UPLOAD_CONFIG_ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], $e);
        }
    }

    /**
     * Get upload limits (server vs app config)
     */
    public function getUploadLimits(): JsonResponse
    {
        // Server limits (php.ini)
        $serverPostMaxSize = $this->sizeParser->parse(ini_get('post_max_size'));
        $serverUploadMaxFilesize = $this->sizeParser->parse(ini_get('upload_max_filesize'));
        $serverMaxFileUploads = (int)ini_get('max_file_uploads');

        Log::channel($this->channel)->info('Raw Server Limits: ', [
            'post_max_size' => $serverPostMaxSize,
            'upload_max_filesize' => $serverUploadMaxFilesize,
            'max_file_uploads' => $serverMaxFileUploads
        ]);

        // Application limits (config)
        $appMaxTotalSize = $this->sizeParser->parse(config('upload-manager.max_total_size', ini_get('post_max_size')));
        $appMaxFileSize = $this->sizeParser->parse(config('upload-manager.max_file_size', ini_get('upload_max_filesize')));
        $appMaxFiles = (int)config('upload-manager.max_files', ini_get('max_file_uploads'));

        $effectiveTotalSize = min($serverPostMaxSize, $appMaxTotalSize);
        $effectiveFileSize = min($serverUploadMaxFilesize, $appMaxFileSize);
        $effectiveMaxFiles = min($serverMaxFileUploads, $appMaxFiles);

        Log::channel($this->channel)->info('Effective Limits: ', [
            'max_total_size' => $effectiveTotalSize,
            'max_file_size' => $effectiveFileSize,
            'max_files' => $effectiveMaxFiles
        ]);

        return response()->json([
            'max_total_size' => $effectiveTotalSize,
            'max_file_size' => $effectiveFileSize,
            'max_files' => $effectiveMaxFiles,
            'max_total_size_formatted' => $this->formatSize($effectiveTotalSize),
            'max_file_size_formatted' => $this->formatSize($effectiveFileSize),
        ]);
    }

    /**
     * Check upload authorization
     */
    public function checkUploadAuthorization(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // ULM: Log authorization check
            $this->logger->info('Upload authorization check', [
                'user_id' => $user?->id,
                'authenticated' => (bool) $user,
                'log_category' => 'UPLOAD_AUTH_CHECK'
            ]);
            
            Log::channel($this->channel)->info('CheckUploadAuthorization', [
                'user_id' => $user ? $user->id : 'guest',
                'ip' => $request->ip()
            ]);

            if (!$user) {
                $response = [
                    'authorized' => false,
                    'reason' => trans('uploadmanager::uploadmanager.unauthenticated'),
                    'redirect' => route('login'),
                ];
                
                Log::channel($this->channel)->warning('UnauthorizedAccessAttempt', [
                    'ip' => $request->ip(),
                    'response' => $response
                ]);
                
                return response()->json($response, 401);
            }

            // Email verification check (commented out as per original)
            // if (!$user->hasVerifiedEmail()) {
            //     return response()->json([
            //         'authorized' => false,
            //         'reason' => trans('uploadmanager::uploadmanager.email_not_verified'),
            //         'redirect' => route('verification.notice'),
            //     ], 403);
            // }

            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'upload_authorization_checked',
                [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
                GdprActivityCategory::CONTENT_CREATION
            );

            Log::channel($this->channel)->info('AuthorizationSuccess', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'authorized' => true,
                'user_id' => $user->id,
            ], 200);
            
        } catch (Exception $e) {
            Log::channel($this->channel)->error('AuthorizationCheckFailed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // UEM: Handle error
            return $this->errorManager->handle('UPLOAD_AUTH_CHECK_ERROR', [
                'error' => $e->getMessage(),
            ], $e);
        }
    }

    /**
     * Format bytes to human-readable size
     */
    private function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
