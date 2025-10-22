<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AiTraitGeneration;
use App\Models\Egi;
use App\Models\User;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: SuperAdmin AI Consultations Management
 * 🎯 Purpose: Manage and monitor all AI consultations (trait generations, descriptions, etc.)
 * 🛡️ Privacy: Full GDPR compliance with audit logging
 * 🧱 Core Logic: UEM error handling + ULM logging + GDPR audit
 *
 * @package App\Http\Controllers\Superadmin
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - SuperAdmin AI Management)
 * @date 2025-10-22
 * @purpose SuperAdmin interface for AI consultations monitoring and management
 */
class SuperadminAiConsultationsController extends Controller
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

    /**
     * Display all AI consultations with filters
     */
    public function index(Request $request): View | RedirectResponse
    {
        try {
            // 1. ULM: Log operation start
            $this->logger->info('SuperAdmin AI Consultations index accessed', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'filters' => $request->only(['status', 'user_id', 'date_from', 'date_to']),
                'log_category' => 'SUPERADMIN_AI_CONSULTATIONS_ACCESS'
            ]);

            $query = AiTraitGeneration::with(['egi', 'user', 'proposals'])
                ->orderBy('created_at', 'desc');

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $consultations = $query->paginate(50);

            // 2. GDPR: Log administrative access
            $this->auditService->logUserAction(
                auth()->user(),
                'superadmin_ai_consultations_access',
                [
                    'page' => 'AI Consultations Index',
                    'filters_applied' => $request->only(['status', 'user_id', 'date_from', 'date_to']),
                    'total_results' => $consultations->total(),
                ],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            // 3. ULM: Log success
            $this->logger->info('SuperAdmin AI Consultations index loaded successfully', [
                'admin_id' => auth()->id(),
                'total_results' => $consultations->total(),
                'log_category' => 'SUPERADMIN_AI_CONSULTATIONS_SUCCESS'
            ]);

            return view('superadmin.ai.consultations.index', [
                'consultations' => $consultations,
                'pageTitle' => 'Gestione Consulenze AI',
            ]);
        } catch (\Exception $e) {
            // 4. ULM: Log error
            $this->logger->error('SuperAdmin AI Consultations index failed', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
                'log_category' => 'SUPERADMIN_AI_CONSULTATIONS_ERROR'
            ]);

            // 5. UEM: Handle error with proper code
            return $this->errorManager->handle('SUPERADMIN_AI_CONSULTATIONS_INDEX_ERROR', [
                'admin_id' => auth()->id(),
                'filters' => $request->only(['status', 'user_id', 'date_from', 'date_to']),
            ], $e);
        }
    }

    /**
     * Display single consultation details
     */
    public function show(AiTraitGeneration $generation): View | RedirectResponse
    {
        try {
            // 1. ULM: Log operation start
            $this->logger->info('SuperAdmin AI Consultation details viewed', [
                'admin_id' => auth()->id(),
                'generation_id' => $generation->id,
                'egi_id' => $generation->egi_id,
                'log_category' => 'SUPERADMIN_AI_CONSULTATION_SHOW'
            ]);

            $generation->load([
                'egi',
                'user',
                'proposals.matchedCategory',
                'proposals.matchedType',
                'proposals.createdCategory',
                'proposals.createdType'
            ]);

            // 2. GDPR: Log administrative access to user data
            $this->auditService->logUserAction(
                auth()->user(),
                'superadmin_ai_consultation_view',
                [
                    'generation_id' => $generation->id,
                    'egi_id' => $generation->egi_id,
                    'target_user_id' => $generation->user_id,
                ],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return view('superadmin.ai.consultations.show', [
                'generation' => $generation,
                'pageTitle' => "Consulenza AI #{$generation->id}",
            ]);
        } catch (\Exception $e) {
            // 3. ULM: Log error
            $this->logger->error('SuperAdmin AI Consultation show failed', [
                'error' => $e->getMessage(),
                'generation_id' => $generation->id ?? null,
                'log_category' => 'SUPERADMIN_AI_CONSULTATION_SHOW_ERROR'
            ]);

            // 4. UEM: Handle error
            return $this->errorManager->handle('SUPERADMIN_AI_CONSULTATIONS_SHOW_ERROR', [
                'admin_id' => auth()->id(),
                'generation_id' => $generation->id ?? null,
            ], $e);
        }
    }

    /**
     * Display consultations for specific EGI
     */
    public function byEgi(Egi $egi): View | RedirectResponse
    {
        try {
            $this->logger->info('SuperAdmin AI Consultations by EGI viewed', [
                'admin_id' => auth()->id(),
                'egi_id' => $egi->id,
                'log_category' => 'SUPERADMIN_AI_CONSULTATIONS_BY_EGI'
            ]);

            $consultations = $egi->aiTraitGenerations()
                ->with(['user', 'proposals'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $this->auditService->logUserAction(
                auth()->user(),
                'superadmin_ai_consultations_by_egi',
                ['egi_id' => $egi->id],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return view('superadmin.ai.consultations.by-egi', [
                'egi' => $egi,
                'consultations' => $consultations,
                'pageTitle' => "Consulenze AI - {$egi->title}",
            ]);
        } catch (\Exception $e) {
            $this->logger->error('SuperAdmin AI Consultations by EGI failed', [
                'error' => $e->getMessage(),
                'egi_id' => $egi->id ?? null,
                'log_category' => 'SUPERADMIN_AI_CONSULTATIONS_BY_EGI_ERROR'
            ]);

            return $this->errorManager->handle('SUPERADMIN_AI_CONSULTATIONS_INDEX_ERROR', [
                'admin_id' => auth()->id(),
                'egi_id' => $egi->id ?? null,
            ], $e);
        }
    }

    /**
     * Display consultations for specific user
     */
    public function byUser(User $user): View | RedirectResponse
    {
        try {
            $this->logger->info('SuperAdmin AI Consultations by User viewed', [
                'admin_id' => auth()->id(),
                'target_user_id' => $user->id,
                'log_category' => 'SUPERADMIN_AI_CONSULTATIONS_BY_USER'
            ]);

            $consultations = AiTraitGeneration::where('user_id', $user->id)
                ->with(['egi', 'proposals'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $this->auditService->logUserAction(
                auth()->user(),
                'superadmin_ai_consultations_by_user',
                [
                    'target_user_id' => $user->id,
                    'data_access_reason' => 'Administrative monitoring',
                ],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return view('superadmin.ai.consultations.by-user', [
                'user' => $user,
                'consultations' => $consultations,
                'pageTitle' => "Consulenze AI - {$user->name}",
            ]);
        } catch (\Exception $e) {
            $this->logger->error('SuperAdmin AI Consultations by User failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? null,
                'log_category' => 'SUPERADMIN_AI_CONSULTATIONS_BY_USER_ERROR'
            ]);

            return $this->errorManager->handle('SUPERADMIN_AI_CONSULTATIONS_INDEX_ERROR', [
                'admin_id' => auth()->id(),
                'target_user_id' => $user->id ?? null,
            ], $e);
        }
    }

    /**
     * Display analytics dashboard
     */
    public function analytics(Request $request): View | RedirectResponse
    {
        try {
            $this->logger->info('SuperAdmin AI Consultations analytics viewed', [
                'admin_id' => auth()->id(),
                'log_category' => 'SUPERADMIN_AI_CONSULTATIONS_ANALYTICS'
            ]);

            $this->auditService->logUserAction(
                auth()->user(),
                'superadmin_ai_consultations_analytics',
                ['page' => 'AI Consultations Analytics'],
                GdprActivityCategory::SYSTEM_INTERACTION
            );

            return view('superadmin.ai.consultations.analytics', [
                'pageTitle' => 'Analytics Consulenze AI',
            ]);
        } catch (\Exception $e) {
            $this->logger->error('SuperAdmin AI Consultations analytics failed', [
                'error' => $e->getMessage(),
                'log_category' => 'SUPERADMIN_AI_CONSULTATIONS_ANALYTICS_ERROR'
            ]);

            return $this->errorManager->handle('SUPERADMIN_AI_CONSULTATIONS_INDEX_ERROR', [
                'admin_id' => auth()->id(),
            ], $e);
        }
    }
}
