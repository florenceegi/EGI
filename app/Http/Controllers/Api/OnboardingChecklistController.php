<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OnboardingChecklistService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Controller: OnboardingChecklistController
 * 🎯 Purpose: API endpoints for onboarding checklist management
 * 🚀 Shopify-style: Returns checklist status for AI Sidebar
 * 🛡️ GDPR: Only owner can access their checklist
 *
 * @package App\Http\Controllers\Api
 * @author EGI Team
 * @version 1.0.0
 */
class OnboardingChecklistController extends Controller
{
    /**
     * @var OnboardingChecklistService
     */
    protected OnboardingChecklistService $checklistService;

    /**
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Constructor with dependency injection
     *
     * @param OnboardingChecklistService $checklistService
     * @param UltraLogManager $logger
     */
    public function __construct(
        OnboardingChecklistService $checklistService,
        UltraLogManager $logger
    ) {
        $this->checklistService = $checklistService;
        $this->logger = $logger;
    }

    /**
     * Get checklist for a user
     *
     * @param Request $request
     * @param string $userType 'creator' | 'company' | 'collector'
     * @param int $userId
     * @return JsonResponse
     */
    public function getChecklist(Request $request, string $userType, int $userId): JsonResponse
    {
        // Validate user type
        if (!in_array($userType, ['creator', 'company', 'collector'])) {
            return response()->json([
                'success' => false,
                'error' => __('ai_sidebar.errors.invalid_user_type')
            ], 400);
        }

        // Security: Only owner can access their checklist
        if (!auth()->check() || auth()->id() !== $userId) {
            return response()->json([
                'success' => false,
                'error' => __('ai_sidebar.errors.unauthorized')
            ], 403);
        }

        try {
            $user = User::findOrFail($userId);
            $checklist = $this->checklistService->getChecklist($user, $userType);
            $progress = $this->checklistService->getProgress($user, $userType);

            return response()->json([
                'success' => true,
                'checklist' => $checklist,
                'progress' => $progress
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Checklist fetch failed', [
                'user_id' => $userId,
                'user_type' => $userType,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => __('ai_sidebar.errors.request_failed')
            ], 500);
        }
    }

    /**
     * Refresh checklist (force cache clear)
     *
     * @param Request $request
     * @param string $userType
     * @param int $userId
     * @return JsonResponse
     */
    public function refreshChecklist(Request $request, string $userType, int $userId): JsonResponse
    {
        // Validate user type
        if (!in_array($userType, ['creator', 'company', 'collector'])) {
            return response()->json([
                'success' => false,
                'error' => __('ai_sidebar.errors.invalid_user_type')
            ], 400);
        }

        // Security: Only owner can refresh their checklist
        if (!auth()->check() || auth()->id() !== $userId) {
            return response()->json([
                'success' => false,
                'error' => __('ai_sidebar.errors.unauthorized')
            ], 403);
        }

        try {
            $user = User::findOrFail($userId);
            $checklist = $this->checklistService->refreshChecklist($user, $userType);
            $progress = $this->checklistService->getProgress($user, $userType);

            $this->logger->info('Checklist refreshed', [
                'user_id' => $userId,
                'user_type' => $userType,
                'progress' => $progress
            ]);

            return response()->json([
                'success' => true,
                'checklist' => $checklist,
                'progress' => $progress
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Checklist refresh failed', [
                'user_id' => $userId,
                'user_type' => $userType,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => __('ai_sidebar.errors.request_failed')
            ], 500);
        }
    }

    /**
     * Get progress only (lightweight endpoint)
     *
     * @param Request $request
     * @param string $userType
     * @param int $userId
     * @return JsonResponse
     */
    public function getProgress(Request $request, string $userType, int $userId): JsonResponse
    {
        // Validate user type
        if (!in_array($userType, ['creator', 'company', 'collector'])) {
            return response()->json([
                'success' => false,
                'error' => __('ai_sidebar.errors.invalid_user_type')
            ], 400);
        }

        // Security: Only owner can access their progress
        if (!auth()->check() || auth()->id() !== $userId) {
            return response()->json([
                'success' => false,
                'error' => __('ai_sidebar.errors.unauthorized')
            ], 403);
        }

        try {
            $user = User::findOrFail($userId);
            $progress = $this->checklistService->getProgress($user, $userType);

            return response()->json([
                'success' => true,
                'progress' => $progress
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => __('ai_sidebar.errors.request_failed')
            ], 500);
        }
    }
}
