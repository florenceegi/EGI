<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Biography;
use App\Helpers\FegiAuth;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\User;

/**
 * @Oracode Web Controller for Biography Display Pages
 * 🎯 Purpose: Serve Biography pages with hybrid authentication support and SEO optimization
 * 🧱 Core Logic: Strong/weak auth awareness, public/private access control, GDPR audit
 * 🛡️ Security: FegiAuth access control, input validation, comprehensive audit logging
 *
 * @package App\Http\Controllers\Web
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI Biography Web Display)
 * @date 2025-07-03
 * @purpose Display biography pages with hybrid authentication and GDPR compliance
 */
class BiographyWebController extends Controller
{
    /**
     * @Oracode Ultra Dependencies + GDPR Audit
     */
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }

    /**
     * @Oracode Display Biography Listing Page
     * 🎯 Purpose: Show biography listing with hybrid authentication awareness
     * 🧱 Core Logic: Public biographies for guests, own+public for authenticated users
     * 🛡️ Security: Access level determination via FegiAuth, GDPR audit logging
     *
     * @param Request $request HTTP request with pagination and filters
     * @return View Biography listing page
     *
     * @throws \Ultra\ErrorManager\Exceptions\UltraErrorException When listing fails
     */
    public function index(Request $request): View
    {
        $authType = FegiAuth::getAuthType();
        $userId = FegiAuth::id();
        $walletAddress = FegiAuth::getWallet();

        $this->logger->info('Biography listing page requested', [
            'user_id' => $userId,
            'auth_type' => $authType,
            'wallet' => $walletAddress,
            'ip_address' => $request->ip(),
            'page' => $request->get('page', 1),
            'user_agent' => $request->userAgent()
        ]);

        try {
            $user = FegiAuth::user();

            // Build query based on authentication status
            $query = Biography::with(['user:id,name,email', 'publishedChapters'])
                ->withCount(['chapters', 'publishedChapters']);

            if (FegiAuth::check()) {
                // Authenticated user (weak or strong): own biographies + public ones
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('is_public', true);
                });
                $viewType = $authType; // 'weak' or 'strong'
                $accessLevel = 'authenticated';
            } else {
                // Guest: only public biographies
                $query->where('is_public', true);
                $viewType = 'guest';
                $accessLevel = 'public';
            }

            // Apply filtering and sorting
            $sortBy = $request->get('sort', 'updated_at');
            $sortDirection = $request->get('direction', 'desc');
            $perPage = min($request->get('per_page', 12), 50); // Max 50 per page

            // Validate sort field for security
            $allowedSortFields = ['updated_at', 'created_at', 'title'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'updated_at';
            }

            $biographies = $query->orderBy($sortBy, $sortDirection)
                ->paginate($perPage)
                ->withQueryString();

            // GDPR audit for page access (if authenticated)
            if (FegiAuth::check()) {
                $this->auditService->logUserAction(
                    $user,
                    'biography_listing_viewed',
                    [
                        'view_type' => $viewType,
                        'auth_type' => $authType,
                        'access_level' => $accessLevel,
                        'biographies_count' => $biographies->count(),
                        'total_available' => $biographies->total(),
                        'page' => $request->get('page', 1),
                        'per_page' => $perPage,
                        'sort_by' => $sortBy,
                        'wallet_address' => $walletAddress
                    ],
                    GdprActivityCategory::DATA_ACCESS
                );
            }

            $this->logger->info('Biography listing rendered successfully', [
                'user_id' => $userId,
                'auth_type' => $authType,
                'biographies_count' => $biographies->count(),
                'total_biographies' => $biographies->total(),
                'view_type' => $viewType,
                'access_level' => $accessLevel
            ]);

            return view('biography.index', [
                'biographies' => $biographies,
                'viewType' => $viewType,
                'authType' => $authType,
                'accessLevel' => $accessLevel,
                'currentSort' => $sortBy,
                'currentDirection' => $sortDirection,
                'canCreateBiography' => FegiAuth::can('create_biography'),
                'isAuthenticated' => FegiAuth::check(),
                'walletAddress' => $walletAddress,
                'title' => __('biography.listing_page_title'),
                'metaDescription' => __('biography.listing_meta_description')
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Biography listing page failed', [
                'user_id' => $userId,
                'auth_type' => $authType,
                'error' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
                'requested_page' => $request->get('page', 1),
                'request_data' => $request->only(['sort', 'direction', 'per_page'])
            ]);

            // UEM handles error display based on configuration (msg_to: sweet-alert, toast, etc.)
            $this->errorManager->handle('BIOGRAPHY_INDEX_FAILED', [
                'user_id' => $userId,
                'auth_type' => $authType,
                'requested_page' => $request->get('page', 1),
                'request_params' => $request->only(['sort', 'direction', 'per_page'])
            ], $e);


            // Fallback in caso di eccezione (non dovrebbe mai essere raggiunto)
            return view('errors.500');
        }
    }

    /**
     * @Oracode Display Single Biography Page
     * 🎯 Purpose: Show biography with chapters and hybrid authentication awareness
     * 🧱 Core Logic: Access control for private biographies, chapter filtering by publication status
     * 🛡️ Security: Owner verification, permission checks, comprehensive audit logging
     *
     * @param Request $request HTTP request
     * @param Biography $biography Biography instance from route model binding
     * @return View Biography detail page
     *
     * @throws \Ultra\ErrorManager\Exceptions\UltraErrorException When access denied or display fails
     */
    public function show(Request $request, $creator_id): View | RedirectResponse | Response
    {
        $authType = FegiAuth::getAuthType();
        $userId = FegiAuth::id();
        $walletAddress = FegiAuth::getWallet();

        $biography = Biography::where('user_id', $creator_id)->first();
        $biographyOwner = User::findOrFail($creator_id); // Proprietario della biografia

        // Check if biography exists - show graceful message instead of 404
        if (!$biography) {
            $this->logger->info('Biography not found for user', [
                'creator_id' => $creator_id,
                'viewer_id' => $userId,
                'auth_type' => $authType
            ]);

            return view('biography.show', [
                'user' => $biographyOwner,
                'biography' => null,
                'isOwner' => FegiAuth::check() && $biographyOwner->id === $userId,
                'authType' => $authType
            ]);
        }

        $this->logger->info('Biography page requested', [
            'user_id' => $userId,
            'auth_type' => $authType,
            'biography_id' => $biography->id,
            'biography_slug' => $biography->slug,
            'biography_title' => $biography->title,
            'is_public' => $biography->is_public,
            'biography_type' => $biography->type,
            'owner_id' => $biography->user_id,
            'wallet' => $walletAddress,
            'ip_address' => $request->ip()
        ]);

        try {
            $currentUser = FegiAuth::user(); // Utente attualmente autenticato

            // Access control validation
            if (!$biography->is_public && (!FegiAuth::check() || $biography->user_id !== $userId)) {
                // Log security event for unauthorized access attempt
                $this->auditService->logSecurityEvent(
                    $currentUser ?? new \stdClass(),
                    'unauthorized_biography_access',
                    [
                        'biography_id' => $biography->id,
                        'biography_title' => $biography->title,
                        'owner_id' => $biography->user_id,
                        'attempted_by_user_id' => $userId,
                        'attempted_action' => 'view_private_biography',
                        'auth_type' => $authType,
                        'wallet_address' => $walletAddress,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ],
                    'medium'
                );

                $this->errorManager->handle('BIOGRAPHY_ACCESS_DENIED', [
                    'user_id' => $userId,
                    'auth_type' => $authType,
                    'biography_id' => $biography->id,
                    'owner_id' => $biography->user_id,
                    'operation' => 'view',
                    'is_public' => $biography->is_public
                ], new \Illuminate\Auth\Access\AuthorizationException());
            }

            // Determine ownership and access type
            $isOwner = FegiAuth::check() && $biography->user_id === $userId;
            $accessType = $isOwner ? 'owner' : 'public';

            // Load chapters based on access level
            if ($isOwner) {
                // Owner: all chapters with media
                $chapters = $biography->chapters()
                    ->with(['media'])
                    ->timelineOrdered()
                    ->get();
            } else {
                // Public access: published chapters only with media
                $chapters = $biography->publishedChapters()
                    ->with(['media'])
                    ->timelineOrdered()
                    ->get();
            }

            // ========== FIX MEDIA SPATIE ==========

            // Load user relationship correctly
            $biographyMedia = $biography->getMedia('main_gallery'); // Ensure media is loaded

            // Se vuoi, puoi ciclare:
            foreach($biographyMedia as $media) {
                $this->logger->info('Biography media URL', ['url' => $media->getUrl()]);
            }

            // dd([
            //     'media_count' => $biographyMedia->count(),
            //     'media_array' => $biographyMedia->toArray(),
            //     'media_class' => get_class($biographyMedia),
            // ]);

            // Force reload media using Spatie methods
            $biography->refresh(); // Refresh the model from database

            // Alternative: Load media directly without using relationships
            $biographyMediaIds = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('model_type', Biography::class)
                ->where('model_id', $biography->id)
                ->pluck('id');

            // Calculate reading time
            $estimatedReadingTime = $biography->getEstimatedReadingTime();

            // Prepare navigation data for chapters
            $chapterNavigation = $chapters->map(function ($chapter, $index) {
                return [
                    'id' => $chapter->id,
                    'title' => $chapter->title,
                    'slug' => $chapter->slug,
                    'date_range' => $chapter->dateRangeDisplay,
                    'type' => $chapter->chapter_type,
                    'position' => $index + 1
                ];
            });

            // GDPR audit logging (if authenticated)
            if (FegiAuth::check()) {
                $this->auditService->logUserAction(
                    $currentUser,
                    'biography_viewed',
                    [
                        'entity_type' => 'Biography',
                        'entity_id' => $biography->id,
                        'biography_title' => $biography->title,
                        'biography_type' => $biography->type,
                        'access_type' => $accessType,
                        'auth_type' => $authType,
                        'chapters_viewed' => $chapters->count(),
                        'total_chapters' => $biography->chapters()->count(),
                        'estimated_reading_time' => $estimatedReadingTime,
                        'wallet_address' => $walletAddress,
                        'referrer' => $request->header('referer')
                    ],
                    GdprActivityCategory::DATA_ACCESS
                );
            }

            $this->logger->info('Biography page rendered successfully', [
                'user_id' => $userId,
                'auth_type' => $authType,
                'biography_id' => $biography->id,
                'chapters_count' => $chapters->count(),
                'access_type' => $accessType,
                'is_owner' => $isOwner,
                'reading_time' => $estimatedReadingTime,
                // DEBUG INFO
                'media_direct_count' => $biographyMediaIds->count(),
                'biography_refresh_attempted' => true,
                'biography' => $biography

            ]);

            return view('biography.show', [
                'user' => $biographyOwner, // ← FIX: Proprietario della biografia, NON utente corrente
                'biography' => $biography,
                'chapters' => $chapters,
                'chapterNavigation' => $chapterNavigation,
                'isOwner' => $isOwner,
                'authType' => $authType,
                'accessType' => $accessType,
                'estimatedReadingTime' => $estimatedReadingTime,
                'canEditBiography' => $isOwner && FegiAuth::can('edit_biography'),
                'canCreateChapter' => $isOwner && FegiAuth::can('create_chapter'),
                'canManageChapters' => $isOwner && FegiAuth::can('manage_chapters'),
                'walletAddress' => $walletAddress,
                'isAuthenticated' => FegiAuth::check(),
                'title' => $biography->title,
                'metaDescription' => $biography->contentPreview,
                'canonicalUrl' => route('biography.public.show', $biography->slug),
                'biographyMedia' => $biographyMedia,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Biography page rendering failed', [
                'user_id' => $userId,
                'auth_type' => $authType,
                'biography_id' => $biography->id,
                'error' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // UEM handles error display based on configuration (msg_to: sweet-alert, toast, etc.)
            $this->errorManager->handle('BIOGRAPHY_SHOW_FAILED', [
                'user_id' => $userId,
                'auth_type' => $authType,
                'biography_id' => $biography->id,
                'biography_slug' => $biography->slug,
                'is_public' => $biography->is_public
            ], $e);

            // Fallback in caso di eccezione
            return response('Errore interno', 500);
        }
    }
}