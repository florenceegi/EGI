<?php

namespace App\Http\Controllers;

use App\Models\Egi;
use App\Services\PreMintEgiService;
use App\Services\EgiMintingOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * EgiDualArchitectureController
 *
 * Controller per gestire le azioni Auto-Mint e Pre-Mint
 * relative alla Dual Architecture EGI (ASA / SmartContract)
 *
 * @package App\Http\Controllers
 * @version 1.0.0 - Dual Architecture
 */
class EgiDualArchitectureController extends Controller
{
    /**
     * @param PreMintEgiService $preMintService
     * @param EgiMintingOrchestrator $mintOrchestrator
     * @param ErrorManagerInterface $errorManager
     * @param UltraLogManager $logManager
     */
    public function __construct(
        protected PreMintEgiService $preMintService,
        protected EgiMintingOrchestrator $mintOrchestrator,
        protected ErrorManagerInterface $errorManager,
        protected UltraLogManager $logManager
    ) {
        $this->middleware('auth');
    }

    /**
     * Abilita Auto-Mint per un EGI Pre-Mint
     *
     * Permette al Creator di abilitare il minting automatico
     * del proprio EGI quando raggiunge lo stato Pre-Mint.
     *
     * @param Request $request
     * @param Egi $egi
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableAutoMint(Request $request, Egi $egi)
    {
        try {
            // GDPR: Verifica che l'utente sia il creator
            if ($egi->user_id !== Auth::id()) {
                return $this->errorManager->handleError(
                    'DUAL_ARCH_AUTO_MINT_UNAUTHORIZED',
                    ['egi_id' => $egi->id, 'user_id' => Auth::id()]
                );
            }

            // ULM: Log dell'operazione
            $this->logManager->log('info', 'Auto-Mint enable request', [
                'egi_id' => $egi->id,
                'user_id' => Auth::id(),
                'current_type' => $egi->egi_type,
            ]);

            // Valida che l'EGI sia in stato Pre-Mint
            if ($egi->egi_type !== 'PreMint') {
                return $this->errorManager->handleError(
                    'DUAL_ARCH_NOT_PRE_MINT',
                    ['egi_id' => $egi->id, 'current_type' => $egi->egi_type]
                );
            }

            // Valida il tipo di mint richiesto (ASA o SmartContract)
            $mintType = $request->input('mint_type', 'ASA');
            if (!in_array($mintType, ['ASA', 'SmartContract'])) {
                return $this->errorManager->handleError(
                    'DUAL_ARCH_INVALID_MINT_TYPE',
                    ['mint_type' => $mintType]
                );
            }

            // Feature flag check per SmartContract
            if ($mintType === 'SmartContract' && !config('egi_living.feature_flags.smart_contract_enabled', false)) {
                return $this->errorManager->handleError(
                    'DUAL_ARCH_SMART_CONTRACT_DISABLED',
                    ['egi_id' => $egi->id]
                );
            }

            // Abilita auto-mint
            $egi->update([
                'auto_mint_enabled' => true,
                'mint_availability' => $mintType,
            ]);

            // ULM: Log successo
            $this->logManager->log('info', 'Auto-Mint enabled successfully', [
                'egi_id' => $egi->id,
                'mint_type' => $mintType,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('egi_dual_arch.auto_mint_enabled'),
                'data' => [
                    'egi_id' => $egi->id,
                    'auto_mint_enabled' => true,
                    'mint_type' => $mintType,
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handleError(
                'DUAL_ARCH_AUTO_MINT_FAILED',
                ['egi_id' => $egi->id, 'exception' => $e->getMessage()]
            );
        }
    }

    /**
     * Disabilita Auto-Mint per un EGI Pre-Mint
     *
     * @param Egi $egi
     * @return \Illuminate\Http\JsonResponse
     */
    public function disableAutoMint(Egi $egi)
    {
        try {
            // GDPR: Verifica che l'utente sia il creator
            if ($egi->user_id !== Auth::id()) {
                return $this->errorManager->handleError(
                    'DUAL_ARCH_AUTO_MINT_UNAUTHORIZED',
                    ['egi_id' => $egi->id, 'user_id' => Auth::id()]
                );
            }

            // ULM: Log dell'operazione
            $this->logManager->log('info', 'Auto-Mint disable request', [
                'egi_id' => $egi->id,
                'user_id' => Auth::id(),
            ]);

            $egi->update([
                'auto_mint_enabled' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('egi_dual_arch.auto_mint_disabled'),
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handleError(
                'DUAL_ARCH_AUTO_MINT_FAILED',
                ['egi_id' => $egi->id, 'exception' => $e->getMessage()]
            );
        }
    }

    /**
     * Richiede analisi AI per un EGI Pre-Mint
     *
     * Invia una richiesta di analisi AI per un EGI in stato Pre-Mint,
     * utile per testing e promozione prima del minting on-chain.
     *
     * @param Egi $egi
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestPreMintAnalysis(Egi $egi)
    {
        try {
            // GDPR: Verifica che l'utente sia il creator
            if ($egi->user_id !== Auth::id()) {
                return $this->errorManager->handleError(
                    'DUAL_ARCH_AUTO_MINT_UNAUTHORIZED',
                    ['egi_id' => $egi->id, 'user_id' => Auth::id()]
                );
            }

            // ULM: Log dell'operazione
            $this->logManager->log('info', 'Pre-Mint AI analysis request', [
                'egi_id' => $egi->id,
                'user_id' => Auth::id(),
            ]);

            // Valida che l'EGI sia in stato Pre-Mint
            if ($egi->egi_type !== 'PreMint') {
                return $this->errorManager->handleError(
                    'DUAL_ARCH_NOT_PRE_MINT',
                    ['egi_id' => $egi->id, 'current_type' => $egi->egi_type]
                );
            }

            // Richiedi analisi AI tramite PreMintService
            $result = $this->preMintService->requestAiAnalysis($egi);

            return response()->json([
                'success' => true,
                'message' => __('egi_dual_arch.ai_analysis_requested'),
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handleError(
                'DUAL_ARCH_AI_ANALYSIS_FAILED',
                ['egi_id' => $egi->id, 'exception' => $e->getMessage()]
            );
        }
    }

    /**
     * Promuove un EGI Pre-Mint a on-chain (ASA o SmartContract)
     *
     * Esegue il minting effettivo on-chain dell'EGI Pre-Mint,
     * convertendolo in ASA (classico) o SmartContract (living).
     *
     * @param Request $request
     * @param Egi $egi
     * @return \Illuminate\Http\JsonResponse
     */
    public function promoteToOnChain(Request $request, Egi $egi)
    {
        try {
            // GDPR: Verifica che l'utente sia il creator
            if ($egi->user_id !== Auth::id()) {
                return $this->errorManager->handleError(
                    'DUAL_ARCH_AUTO_MINT_UNAUTHORIZED',
                    ['egi_id' => $egi->id, 'user_id' => Auth::id()]
                );
            }

            // ULM: Log dell'operazione
            $this->logManager->log('info', 'Pre-Mint promotion to on-chain', [
                'egi_id' => $egi->id,
                'user_id' => Auth::id(),
                'target_type' => $request->input('target_type'),
            ]);

            // Valida che l'EGI sia in stato Pre-Mint
            if ($egi->egi_type !== 'PreMint') {
                return $this->errorManager->handleError(
                    'DUAL_ARCH_NOT_PRE_MINT',
                    ['egi_id' => $egi->id, 'current_type' => $egi->egi_type]
                );
            }

            // Valida il tipo target
            $targetType = $request->input('target_type', 'ASA');
            if (!in_array($targetType, ['ASA', 'SmartContract'])) {
                return $this->errorManager->handleError(
                    'DUAL_ARCH_INVALID_MINT_TYPE',
                    ['mint_type' => $targetType]
                );
            }

            // Feature flag check per SmartContract
            if ($targetType === 'SmartContract' && !config('egi_living.feature_flags.smart_contract_enabled', false)) {
                return $this->errorManager->handleError(
                    'DUAL_ARCH_SMART_CONTRACT_DISABLED',
                    ['egi_id' => $egi->id]
                );
            }

            // Promuovi tramite PreMintService
            $result = $this->preMintService->promoteToOnChain($egi, $targetType);

            return response()->json([
                'success' => true,
                'message' => __('egi_dual_arch.promotion_initiated'),
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handleError(
                'DUAL_ARCH_PROMOTION_FAILED',
                ['egi_id' => $egi->id, 'exception' => $e->getMessage()]
            );
        }
    }
}
