<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;

/**
 * @package App\Http\Controllers\Superadmin
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - SuperAdmin Padmin Analyzer)
 * @date 2025-10-22
 * @purpose SuperAdmin interface for Padmin Analyzer (OS3 Guardian) management
 */
class PadminController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditLogService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditLogService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditLogService = $auditLogService;
        $this->middleware(['auth', 'superadmin']);
    }
    /**
     * Padmin Analyzer - Dashboard
     */
    public function dashboard(Request $request): View|JsonResponse {
        try {
            $this->logger->info('[SuperAdmin] Padmin Dashboard accessed', [
                'admin_id' => auth()->id(),
                'ip' => $request->ip(),
                'ua' => substr((string)$request->userAgent(), 0, 255),
                'section' => 'dashboard',
            ]);

            // Log admin access (middleware 'auth' guarantees user is authenticated)
            $this->auditLogService->logUserAction(
                user: auth()->user(),
                action: 'superadmin_padmin_dashboard_access',
                context: [
                    'route' => 'superadmin.padmin.dashboard',
                ],
                category: GdprActivityCategory::ADMIN_ACCESS
            );

            return view('superadmin.padmin.dashboard', [
                'pageTitle' => 'Padmin Analyzer',
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('UNEXPECTED_ERROR', [
                'controller' => self::class,
                'method' => __METHOD__,
                'admin_id' => auth()->id(),
            ], $e);
        }
    }

    /**
     * Padmin Analyzer - Violations list
     */
    public function violations(Request $request): View|JsonResponse {
        try {
            $this->logger->info('[SuperAdmin] Padmin Violations viewed', [
                'admin_id' => auth()->id(),
                'ip' => $request->ip(),
                'section' => 'violations',
            ]);

            // Log admin access (middleware 'auth' guarantees user is authenticated)
            $this->auditLogService->logUserAction(
                user: auth()->user(),
                action: 'superadmin_padmin_violations_access',
                context: [],
                category: GdprActivityCategory::ADMIN_ACCESS
            );

            return view('superadmin.padmin.violations', [
                'pageTitle' => 'Violazioni Padmin',
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('UNEXPECTED_ERROR', [
                'controller' => self::class,
                'method' => __METHOD__,
                'admin_id' => auth()->id(),
            ], $e);
        }
    }

    /**
     * Padmin Analyzer - Symbols registry
     */
    public function symbols(Request $request): View|JsonResponse {
        try {
            $this->logger->info('[SuperAdmin] Padmin Symbols viewed', [
                'admin_id' => auth()->id(),
                'ip' => $request->ip(),
                'section' => 'symbols',
            ]);

            // Log admin access (middleware 'auth' guarantees user is authenticated)
            $this->auditLogService->logUserAction(
                user: auth()->user(),
                action: 'superadmin_padmin_symbols_access',
                context: [],
                category: GdprActivityCategory::ADMIN_ACCESS
            );

            return view('superadmin.padmin.symbols', [
                'pageTitle' => 'Simboli Padmin',
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('UNEXPECTED_ERROR', [
                'controller' => self::class,
                'method' => __METHOD__,
                'admin_id' => auth()->id(),
            ], $e);
        }
    }

    /**
     * Padmin Analyzer - Semantic search UI
     */
    public function search(Request $request): View|JsonResponse {
        try {
            $this->logger->info('[SuperAdmin] Padmin Search viewed', [
                'admin_id' => auth()->id(),
                'ip' => $request->ip(),
                'section' => 'search',
            ]);

            // Log admin access (middleware 'auth' guarantees user is authenticated)
            $this->auditLogService->logUserAction(
                user: auth()->user(),
                action: 'superadmin_padmin_search_access',
                context: [],
                category: GdprActivityCategory::ADMIN_ACCESS
            );

            return view('superadmin.padmin.search', [
                'pageTitle' => 'Ricerca Semantica',
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('UNEXPECTED_ERROR', [
                'controller' => self::class,
                'method' => __METHOD__,
                'admin_id' => auth()->id(),
            ], $e);
        }
    }

    /**
     * Padmin Analyzer - Statistics UI
     */
    public function statistics(Request $request): View|JsonResponse {
        try {
            $this->logger->info('[SuperAdmin] Padmin Statistics viewed', [
                'admin_id' => auth()->id(),
                'ip' => $request->ip(),
                'section' => 'statistics',
            ]);

            // Log admin access (middleware 'auth' guarantees user is authenticated)
            $this->auditLogService->logUserAction(
                user: auth()->user(),
                action: 'superadmin_padmin_statistics_access',
                context: [],
                category: GdprActivityCategory::ADMIN_ACCESS
            );

            return view('superadmin.padmin.stats', [
                'pageTitle' => 'Statistiche Padmin',
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('UNEXPECTED_ERROR', [
                'controller' => self::class,
                'method' => __METHOD__,
                'admin_id' => auth()->id(),
            ], $e);
        }
    }
}