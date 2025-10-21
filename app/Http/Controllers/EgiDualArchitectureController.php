<?php

namespace App\Http\Controllers;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Helpers\FegiAuth;
use App\Models\Egi;
use App\Services\EgiPreMintManagementService;
use App\Services\Gdpr\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * EgiDualArchitectureController OS3.0 - UEM/ULM/GDPR Compliant
 *
 * Handles Auto-Mint and Pre-Mint actions for the Dual Architecture EGI system.
 * Supports ASA (classic) and SmartContract (living) minting workflows.
 * Implements SOLID principles with proper service layer separation.
 *
 * @Oracode Controller: Dual Architecture EGI Management
 * 🎯 Purpose: Coordinate Pre-Mint operations with full GDPR audit trail
 * 🛡️ Privacy: Tracks all EGI state changes with creator identification
 * 🧱 Core Logic: Validates requests, delegates to service, logs actions
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - Dual Architecture + GDPR)
 * @date 2025-10-21
 * @purpose Orchestrate creator-driven minting and pre-mint workflows with GDPR compliance
 */
class EgiDualArchitectureController extends Controller
{
    protected EgiPreMintManagementService $preMintManagementService;
    protected AuditLogService $auditService;
    protected ErrorManagerInterface $errorManager;
    protected UltraLogManager $logger;

    /**
     * Constructor with dependency injection
     *
     * @param EgiPreMintManagementService $preMintManagementService Service for Pre-Mint operations
     * @param AuditLogService $auditService GDPR audit logging service
     * @param ErrorManagerInterface $errorManager Ultra error manager
     * @param UltraLogManager $logger Ultra logging manager
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        EgiPreMintManagementService $preMintManagementService,
        AuditLogService $auditService,
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger
    ) {
        $this->preMintManagementService = $preMintManagementService;
        $this->auditService = $auditService;
        $this->errorManager = $errorManager;
        $this->logger = $logger;
    }

    /**
     * Enable Auto-Mint for a Pre-Mint EGI
     *
     * @Oracode Method: Auto-Mint Activation (Controller)
     * 🎯 Purpose: Validate request and delegate to service
     * 📥 Input: HTTP request for Pre-Mint mode activation
     * 📤 Output: JSON response confirming activation or error
     * 🔒 Security: Creator-only authorization
     * 🪵 Logging: ULM tracks controller-level operation
     * 🛡️ Privacy: GDPR audit via service layer
     *
     * @param Request $request HTTP request
     * @param Egi $egi Target EGI instance (route model binding)
     * @return JsonResponse Success response or UEM-handled error
     * @throws never UEM handles all exceptions
     * @privacy-safe Delegates to service with full GDPR audit
     */
    public function enableAutoMint(Request $request, Egi $egi): JsonResponse
    {
        // 1. ULM: Log controller operation start
        $this->logger->info('[DUAL_ARCH_CTRL] Auto-Mint enable request received', [
            'egi_id' => $egi->id,
            'user_id' => FegiAuth::id(),
            'current_egi_type' => $egi->egi_type,
            'ip_address' => $request->ip(),
            'log_category' => 'DUAL_ARCH_CONTROLLER_REQUEST'
        ]);

        try {
            // 2. 🔒 Authorization: Verify user is the creator
            $user = FegiAuth::user();
            if ($egi->user_id !== $user->id) {
                return $this->errorManager->handle('DUAL_ARCH_AUTO_MINT_UNAUTHORIZED', [
                    'egi_id' => $egi->id,
                    'egi_creator_id' => $egi->user_id,
                    'requesting_user_id' => $user->id,
                    'operation' => 'enable_auto_mint',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            // 3. ✅ Validate EGI is NOT minted (egi_type must be NULL)
            if (!is_null($egi->egi_type)) {
                return $this->errorManager->handle('DUAL_ARCH_NOT_PRE_MINT', [
                    'egi_id' => $egi->id,
                    'current_type' => $egi->egi_type ?? 'NULL',
                    'required_type' => 'NULL (not minted)',
                    'operation' => 'enable_auto_mint',
                    'user_id' => $user->id,
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            // 4. Prepare request metadata for service
            $requestMetadata = [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            // 5. Delegate to service (SOLID: separation of concerns)
            $result = $this->preMintManagementService->enablePreMintMode($egi, $user, $requestMetadata);

            // 6. ULM: Log controller success
            $this->logger->info('[DUAL_ARCH_CTRL] Auto-Mint enabled successfully', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'log_category' => 'DUAL_ARCH_CONTROLLER_SUCCESS'
            ]);

            return response()->json([
                'success' => true,
                'message' => __('egi_dual_arch.auto_mint_enabled'),
                'data' => $result
            ]);

        } catch (\Exception $e) {
            // 7. UEM: Handle all exceptions with full context
            return $this->errorManager->handle('DUAL_ARCH_AUTO_MINT_FAILED', [
                'egi_id' => $egi->id,
                'operation' => 'enable_auto_mint',
                'exception_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'user_id' => FegiAuth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toIso8601String()
            ], $e);
        }
    }

    /**
     * Disable Auto-Mint for a Pre-Mint EGI
     *
     * @Oracode Method: Auto-Mint Deactivation
     * 🎯 Purpose: Allow creator to disable self-minting option
     * 📥 Input: EGI instance via route binding
     * 📤 Output: JSON confirmation or UEM-handled error
     * 🔒 Security: Creator-only authorization
     *
     * @param Request $request HTTP request for IP/UA logging
     * @param Egi $egi Target EGI instance
     * @return JsonResponse Success response or UEM-handled error
     * @throws never UEM handles all exceptions
     */
    public function disableAutoMint(Request $request, Egi $egi): JsonResponse
    {
        $this->logger->info('[DUAL_ARCH_CTRL] Auto-Mint disable request', [
            'egi_id' => $egi->id,
            'user_id' => FegiAuth::id(),
            'log_category' => 'DUAL_ARCH_CONTROLLER_REQUEST'
        ]);

        try {
            $user = FegiAuth::user();
            if ($egi->user_id !== $user->id) {
                return $this->errorManager->handle('DUAL_ARCH_AUTO_MINT_UNAUTHORIZED', [
                    'egi_id' => $egi->id,
                    'egi_creator_id' => $egi->user_id,
                    'requesting_user_id' => $user->id,
                    'operation' => 'disable_auto_mint',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            $requestMetadata = [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            $result = $this->preMintManagementService->disablePreMintMode($egi, $user, $requestMetadata);

            return response()->json([
                'success' => true,
                'message' => __('egi_dual_arch.auto_mint_disabled'),
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('DUAL_ARCH_AUTO_MINT_FAILED', [
                'egi_id' => $egi->id,
                'operation' => 'disable_auto_mint',
                'exception_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'user_id' => FegiAuth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toIso8601String()
            ], $e);
        }
    }

    /**
     * Request AI analysis for a Pre-Mint EGI
     *
     * @Oracode Method: Pre-Mint AI Analysis Request
     * 🎯 Purpose: Trigger N.A.T.A.N AI analysis on virtual EGI before on-chain mint
     * 📥 Input: EGI instance via route binding
     * 📤 Output: JSON with analysis request confirmation or UEM error
     * 🔒 Security: Creator-only authorization, Pre-Mint state validation
     * 🤖 AI Integration: Delegates to PreMintEgiService for N.A.T.A.N orchestration
     *
     * @param Request $request HTTP request for IP/UA logging
     * @param Egi $egi Target Pre-Mint EGI
     * @return JsonResponse Success with analysis data or UEM-handled error
     * @throws never UEM handles all exceptions
     */
    public function requestPreMintAnalysis(Request $request, Egi $egi): JsonResponse
    {
        $this->logger->info('[DUAL_ARCH] Pre-Mint AI analysis request', [
            'egi_id' => $egi->id,
            'user_id' => FegiAuth::id(),
            'ip_address' => $request->ip()
        ]);

        try {
            // 🔒 Authorization: Verify user is the creator
            if ($egi->user_id !== FegiAuth::id()) {
                return $this->errorManager->handle('DUAL_ARCH_AUTO_MINT_UNAUTHORIZED', [
                    'egi_id' => $egi->id,
                    'egi_creator_id' => $egi->user_id,
                    'requesting_user_id' => FegiAuth::id(),
                    'operation' => 'request_ai_analysis',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            // ✅ Validate EGI is NOT minted (egi_type must be NULL)
            if (!is_null($egi->egi_type)) {
                return $this->errorManager->handle('DUAL_ARCH_NOT_PRE_MINT', [
                    'egi_id' => $egi->id,
                    'current_type' => $egi->egi_type ?? 'NULL',
                    'required_type' => 'NULL (not minted)',
                    'operation' => 'request_ai_analysis',
                    'user_id' => FegiAuth::id(),
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            $requestMetadata = [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            $user = FegiAuth::user();
            $result = $this->preMintManagementService->requestAiAnalysis($egi, $user, $requestMetadata);

            return response()->json([
                'success' => true,
                'message' => __('egi_dual_arch.ai_analysis_requested'),
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('DUAL_ARCH_AI_ANALYSIS_FAILED', [
                'egi_id' => $egi->id,
                'operation' => 'request_ai_analysis',
                'exception_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'user_id' => FegiAuth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toIso8601String()
            ], $e);
        }
    }

    /**
     * Promote Pre-Mint EGI to on-chain (ASA or SmartContract)
     *
     * @Oracode Method: Pre-Mint to On-Chain Promotion
     * 🎯 Purpose: Execute actual blockchain minting of virtual EGI
     * 📥 Input: HTTP request with target_type (ASA|SmartContract)
     * 📤 Output: JSON with blockchain transaction data or UEM error
     * 🔒 Security: Creator-only, feature flag validation, Pre-Mint state check
     * ⛓️ Blockchain: Delegates to PreMintService for Algorand interaction
     *
     * @param Request $request HTTP request containing target_type
     * @param Egi $egi Target Pre-Mint EGI
     * @return JsonResponse Success with blockchain data or UEM-handled error
     * @throws never UEM handles all exceptions
     */
    public function promoteToOnChain(Request $request, Egi $egi): JsonResponse
    {
        $this->logger->info('[DUAL_ARCH] Pre-Mint promotion to on-chain', [
            'egi_id' => $egi->id,
            'user_id' => FegiAuth::id(),
            'target_type' => $request->input('target_type'),
            'ip_address' => $request->ip()
        ]);

        try {
            // 🔒 Authorization: Verify user is the creator
            if ($egi->user_id !== FegiAuth::id()) {
                return $this->errorManager->handle('DUAL_ARCH_AUTO_MINT_UNAUTHORIZED', [
                    'egi_id' => $egi->id,
                    'egi_creator_id' => $egi->user_id,
                    'requesting_user_id' => FegiAuth::id(),
                    'operation' => 'promote_to_onchain',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            // ✅ Validate EGI is NOT minted (egi_type must be NULL)
            if (!is_null($egi->egi_type)) {
                return $this->errorManager->handle('DUAL_ARCH_NOT_PRE_MINT', [
                    'egi_id' => $egi->id,
                    'current_type' => $egi->egi_type ?? 'NULL',
                    'required_type' => 'NULL (not minted)',
                    'operation' => 'promote_to_onchain',
                    'user_id' => FegiAuth::id(),
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            // ✅ Validate target type
            $validated = $request->validate([
                'target_type' => 'required|string|in:ASA,SmartContract'
            ]);

            $targetType = $validated['target_type'];

            // 🚩 Feature flag check for SmartContract
            if ($targetType === 'SmartContract' && !config('egi_living.feature_flags.smart_contract_mint_enabled', false)) {
                return $this->errorManager->handle('DUAL_ARCH_SMART_CONTRACT_DISABLED', [
                    'egi_id' => $egi->id,
                    'requested_type' => $targetType,
                    'feature_flag' => 'smart_contract_mint_enabled',
                    'user_id' => FegiAuth::id(),
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            $requestMetadata = [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            $user = FegiAuth::user();
            $result = $this->preMintManagementService->promoteToOnChain($egi, $targetType, $user, $requestMetadata);

            return response()->json([
                'success' => true,
                'message' => __('egi_dual_arch.promotion_initiated'),
                'data' => $result
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorManager->handle('VALIDATION_ERROR', [
                'egi_id' => $egi->id,
                'operation' => 'promote_to_onchain',
                'validation_errors' => $e->errors(),
                'user_id' => FegiAuth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ], $e);
        } catch (\Exception $e) {
            return $this->errorManager->handle('DUAL_ARCH_PROMOTION_FAILED', [
                'egi_id' => $egi->id,
                'operation' => 'promote_to_onchain',
                'exception_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'user_id' => FegiAuth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toIso8601String()
            ], $e);
        }
    }
}
