<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: SuperAdmin AI Statistics Management
 * 🎯 Purpose: Monitor and analyze AI usage statistics
 * 🛡️ Privacy: Full GDPR compliance with audit logging
 * 🧱 Core Logic: UEM error handling + ULM logging + GDPR audit
 *
 * @package App\Http\Controllers\Superadmin
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - SuperAdmin AI Management)
 * @date 2025-10-22
 * @purpose SuperAdmin interface for AI statistics monitoring
 */
class SuperadminAiStatisticsController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }

    public function index(): View | RedirectResponse
    {
        try {
            // 1. ULM: Log access
            $this->logger->info('SuperAdmin AI Statistics page accessed', [
                'admin_id' => auth()->id(),
                'log_category' => 'SUPERADMIN_AI_STATISTICS_ACCESS'
            ]);

            // 2. GDPR: Log administrative access
            $this->auditService->logUserAction(
                auth()->user(),
                'superadmin_ai_statistics_access',
                ['page' => 'AI Statistics Dashboard'],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return view('superadmin.ai.statistics.index', [
                'pageTitle' => 'Statistiche AI',
            ]);
        } catch (\Exception $e) {
            // 3. ULM: Log error
            $this->logger->error('SuperAdmin AI Statistics index failed', [
                'error' => $e->getMessage(),
                'log_category' => 'SUPERADMIN_AI_STATISTICS_ERROR'
            ]);

            // 4. UEM: Handle error
            return $this->errorManager->handle('SUPERADMIN_AI_STATISTICS_INDEX_ERROR', [
                'admin_id' => auth()->id(),
            ], $e);
        }
    }

    public function usage(): View
    {
        return view('superadmin.ai.statistics.usage', [
            'pageTitle' => 'AI Usage Statistics',
        ]);
    }

    public function performance(): View
    {
        return view('superadmin.ai.statistics.performance', [
            'pageTitle' => 'AI Performance Metrics',
        ]);
    }
}
