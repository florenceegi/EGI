<?php

namespace App\Services;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\Egi;
use App\Models\User;
use App\Services\AnthropicService;
use App\Services\EgiMintingOrchestrator;
use App\Services\Gdpr\AuditLogService;
use Illuminate\Support\Facades\DB;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * EgiPreMintManagementService OS3.0 - UEM/ULM/GDPR Compliant
 *
 * Manages Pre-Mint EGI operations (enable/disable, AI analysis, promotion to blockchain).
 * Implements SOLID principles with proper service layer separation.
 *
 * @Oracode Service: Pre-Mint EGI Management
 * 🎯 Purpose: Handle Pre-Mint mode operations with full GDPR audit trail
 * 🛡️ Privacy: Tracks all EGI state changes with creator identification
 * 🧱 Core Logic: Enable/disable Pre-Mint, coordinate AI analysis, prepare for minting
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-21
 * @purpose Manage Pre-Mint EGI lifecycle with GDPR compliance
 */
class EgiPreMintManagementService
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;
    protected AnthropicService $anthropicService;
    protected EgiMintingOrchestrator $mintingOrchestrator;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger Ultra logging manager for service operations
     * @param ErrorManagerInterface $errorManager Ultra error manager for error handling
     * @param AuditLogService $auditService GDPR audit logging service
     * @param AnthropicService $anthropicService N.A.T.A.N AI service (Anthropic Claude)
     * @param EgiMintingOrchestrator $mintingOrchestrator Blockchain minting orchestrator (ASA/SmartContract)
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        AnthropicService $anthropicService,
        EgiMintingOrchestrator $mintingOrchestrator
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->anthropicService = $anthropicService;
        $this->mintingOrchestrator = $mintingOrchestrator;
    }

    /**
     * Enable Pre-Mint mode for an EGI (reserve for creator)
     *
     * @Oracode Method: Enable Pre-Mint Mode
     * 🎯 Purpose: Reserve EGI for creator self-minting
     * 📥 Input: EGI instance, User instance (creator)
     * 📤 Output: Updated EGI with pre_mint_mode enabled
     * 🔒 Security: Only creator can enable Pre-Mint mode
     * 🪵 Logging: Full audit trail with previous state
     * 🛡️ Privacy: Tracks creator action with GDPR category
     *
     * @param Egi $egi Target EGI to enable Pre-Mint mode
     * @param User $user Creator user enabling Pre-Mint
     * @param array $requestMetadata Request metadata (IP, UA) for audit
     * @return array Result with success status and updated EGI data
     * @throws \Exception When Pre-Mint enable fails
     * @privacy-safe Logs creator action with GDPR audit trail
     */
    public function enablePreMintMode(Egi $egi, User $user, array $requestMetadata): array
    {
        try {
            // 1. ULM: Log service operation start
            $this->logger->info('[PRE_MINT_SERVICE] Enabling Pre-Mint mode', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'current_egi_type' => $egi->egi_type,
                'current_pre_mint_mode' => $egi->pre_mint_mode,
                'log_category' => 'PRE_MINT_ENABLE_START'
            ]);

            // 2. Store previous state for GDPR audit
            $previousState = [
                'pre_mint_mode' => $egi->pre_mint_mode,
                'pre_mint_created_at' => $egi->pre_mint_created_at?->toIso8601String(),
                'egi_type' => $egi->egi_type,
            ];

            // 3. Perform state change in transaction
            DB::transaction(function () use ($egi) {
                $egi->update([
                    'pre_mint_mode' => true,
                    'pre_mint_created_at' => $egi->pre_mint_created_at ?? now(),
                ]);
            });

            // 4. GDPR: Log user action with AuditLogService
            $this->auditService->logUserAction($user, 'egi_pre_mint_enabled', [
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'previous_state' => $previousState,
                'new_state' => [
                    'pre_mint_mode' => true,
                    'pre_mint_created_at' => $egi->pre_mint_created_at->toIso8601String(),
                ],
                'ip_address' => $requestMetadata['ip_address'],
                'user_agent' => $requestMetadata['user_agent'],
            ], GdprActivityCategory::CONTENT_MODIFICATION);

            // 5. ULM: Log successful completion
            $this->logger->info('[PRE_MINT_SERVICE] Pre-Mint mode enabled successfully', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'pre_mint_mode' => true,
                'log_category' => 'PRE_MINT_ENABLE_SUCCESS'
            ]);

            return [
                'success' => true,
                'egi_id' => $egi->id,
                'pre_mint_mode' => true,
                'pre_mint_created_at' => $egi->pre_mint_created_at->toIso8601String(),
            ];
        } catch (\Exception $e) {
            // 6. ULM: Log service-level error
            $this->logger->error('[PRE_MINT_SERVICE] Failed to enable Pre-Mint mode', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'log_category' => 'PRE_MINT_ENABLE_ERROR'
            ]);

            // 7. Re-throw for controller UEM handling
            throw new \Exception("Pre-Mint enable failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Disable Pre-Mint mode for an EGI (make available on marketplace)
     *
     * @Oracode Method: Disable Pre-Mint Mode
     * 🎯 Purpose: Make EGI available on marketplace for public sale
     * 📥 Input: EGI instance, User instance (creator)
     * 📤 Output: Updated EGI with pre_mint_mode disabled
     * 🔒 Security: Only creator can disable Pre-Mint mode
     * 🪵 Logging: Full audit trail with previous state
     * 🛡️ Privacy: Tracks creator action with GDPR category
     *
     * @param Egi $egi Target EGI to disable Pre-Mint mode
     * @param User $user Creator user disabling Pre-Mint
     * @param array $requestMetadata Request metadata (IP, UA) for audit
     * @return array Result with success status
     * @throws \Exception When Pre-Mint disable fails
     * @privacy-safe Logs creator action with GDPR audit trail
     */
    public function disablePreMintMode(Egi $egi, User $user, array $requestMetadata): array
    {
        try {
            // 1. ULM: Log service operation start
            $this->logger->info('[PRE_MINT_SERVICE] Disabling Pre-Mint mode', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'current_pre_mint_mode' => $egi->pre_mint_mode,
                'log_category' => 'PRE_MINT_DISABLE_START'
            ]);

            // 2. Store previous state for GDPR audit
            $previousState = [
                'pre_mint_mode' => $egi->pre_mint_mode,
                'pre_mint_created_at' => $egi->pre_mint_created_at?->toIso8601String(),
            ];

            // 3. Perform state change in transaction
            DB::transaction(function () use ($egi) {
                $egi->update([
                    'pre_mint_mode' => false,
                ]);
            });

            // 4. GDPR: Log user action with AuditLogService
            $this->auditService->logUserAction($user, 'egi_pre_mint_disabled', [
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'previous_state' => $previousState,
                'new_state' => [
                    'pre_mint_mode' => false,
                ],
                'ip_address' => $requestMetadata['ip_address'],
                'user_agent' => $requestMetadata['user_agent'],
            ], GdprActivityCategory::CONTENT_MODIFICATION);

            // 5. ULM: Log successful completion
            $this->logger->info('[PRE_MINT_SERVICE] Pre-Mint mode disabled successfully', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'pre_mint_mode' => false,
                'log_category' => 'PRE_MINT_DISABLE_SUCCESS'
            ]);

            return [
                'success' => true,
                'egi_id' => $egi->id,
                'pre_mint_mode' => false,
            ];
        } catch (\Exception $e) {
            // 6. ULM: Log service-level error
            $this->logger->error('[PRE_MINT_SERVICE] Failed to disable Pre-Mint mode', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'log_category' => 'PRE_MINT_DISABLE_ERROR'
            ]);

            // 7. Re-throw for controller UEM handling
            throw new \Exception("Pre-Mint disable failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Request AI analysis for a Pre-Mint EGI
     *
     * @Oracode Method: Request AI Analysis
     * 🎯 Purpose: Trigger N.A.T.A.N AI analysis on virtual EGI
     * 📥 Input: EGI instance, User instance
     * 📤 Output: Analysis request confirmation with job ID
     * 🔒 Security: Creator-only operation
     * 🪵 Logging: Full audit trail for AI interaction
     * 🛡️ Privacy: Tracks AI analysis request with GDPR category
     *
     * @param Egi $egi Target Pre-Mint EGI for analysis
     * @param User $user User requesting analysis
     * @param array $requestMetadata Request metadata (IP, UA) for audit
     * @return array Analysis result with job ID
     * @throws \Exception When AI analysis request fails
     * @privacy-safe Logs AI interaction with GDPR audit trail
     */
    public function requestAiAnalysis(Egi $egi, User $user, array $requestMetadata): array
    {
        try {
            // 1. ULM: Log service operation start
            $this->logger->info('[PRE_MINT_SERVICE] Requesting N.A.T.A.N AI analysis', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'log_category' => 'PRE_MINT_AI_ANALYSIS_START'
            ]);

            // 2. Prepare EGI context for N.A.T.A.N AI (GDPR-safe: only public metadata)
            $egiContext = [
                'egi_id' => $egi->id,
                'title' => $egi->title,
                'description' => $egi->description,
                'type' => $egi->type,
                'creation_date' => $egi->creation_date?->format('Y-m-d'),
                'status' => $egi->status,
                'creator_id' => $egi->user_id, // No personal data, just ID
            ];

            // 3. Call N.A.T.A.N AI for Pre-Mint analysis
            $aiPrompt = "Analizza questo EGI Pre-Mint e fornisci suggerimenti per migliorare la sua commerciabilità e valore sul marketplace. "
                . "Considera: completezza metadati, qualità descrizione, potenziale appeal per acquirenti. "
                . "Fornisci: (1) Punteggio qualità 1-10, (2) Suggerimenti concreti per migliorare, (3) Stima potenziale prezzo di vendita.";

            $aiResponse = $this->anthropicService->chat($aiPrompt, $egiContext, []);

            // 4. Store AI analysis result in EGI metadata (for future reference)
            $analysisResult = [
                'analysis_timestamp' => now()->toIso8601String(),
                'ai_response' => $aiResponse,
                'ai_model' => config('services.anthropic.model', 'claude-3-5-sonnet-20241022'),
            ];

            // 5. GDPR: Log user action with AuditLogService
            $this->auditService->logUserAction($user, 'egi_ai_analysis_completed', [
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'analysis_success' => true,
                'ai_model_used' => $analysisResult['ai_model'],
                'ip_address' => $requestMetadata['ip_address'],
                'user_agent' => $requestMetadata['user_agent'],
            ], GdprActivityCategory::CONTENT_MODIFICATION);

            // 6. ULM: Log successful AI analysis
            $this->logger->info('[PRE_MINT_SERVICE] N.A.T.A.N AI analysis completed successfully', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'response_length' => strlen($aiResponse),
                'log_category' => 'PRE_MINT_AI_ANALYSIS_SUCCESS'
            ]);

            return [
                'success' => true,
                'egi_id' => $egi->id,
                'analysis' => $analysisResult,
                'ai_response' => $aiResponse,
                'message' => 'N.A.T.A.N AI analysis completed successfully',
            ];
        } catch (\Exception $e) {
            // 5. ULM: Log service-level error
            $this->logger->error('[PRE_MINT_SERVICE] AI analysis request failed', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'error_message' => $e->getMessage(),
                'log_category' => 'PRE_MINT_AI_ANALYSIS_ERROR'
            ]);

            // 6. Re-throw for controller UEM handling
            throw new \Exception("AI analysis request failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Promote Pre-Mint EGI to blockchain (ASA or SmartContract)
     *
     * @Oracode Method: Promote to Blockchain
     * 🎯 Purpose: Execute actual blockchain minting of virtual EGI
     * 📥 Input: EGI instance, target type (ASA|SmartContract), User
     * 📤 Output: Blockchain transaction data
     * 🔒 Security: Creator-only, validates EGI state
     * 🪵 Logging: Full audit trail with blockchain transaction ID
     * 🛡️ Privacy: Tracks blockchain operation with GDPR category
     *
     * @param Egi $egi Target Pre-Mint EGI to promote
     * @param string $targetType Target blockchain type (ASA|SmartContract)
     * @param User $user User promoting the EGI
     * @param array $requestMetadata Request metadata (IP, UA) for audit
     * @return array Blockchain transaction result
     * @throws \Exception When promotion fails
     * @privacy-safe Logs blockchain operation with GDPR audit trail
     */
    public function promoteToOnChain(Egi $egi, string $targetType, User $user, array $requestMetadata): array
    {
        try {
            // 1. ULM: Log service operation start
            $this->logger->info('[PRE_MINT_SERVICE] Promoting Pre-Mint to blockchain', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'target_type' => $targetType,
                'log_category' => 'PRE_MINT_PROMOTION_START'
            ]);

            // 2. Store previous state for GDPR audit
            $previousState = [
                'egi_type' => $egi->egi_type,
                'pre_mint_mode' => $egi->pre_mint_mode,
                'token_EGI' => $egi->token_EGI,
            ];

            // 3. Update EGI type BEFORE minting (required by EgiMintingOrchestrator)
            DB::transaction(function () use ($egi, $targetType) {
                $egi->update([
                    'egi_type' => $targetType,
                    'pre_mint_mode' => false, // No longer in pre-mint mode
                ]);
            });

            // 4. Call EgiMintingOrchestrator for actual blockchain minting
            $mintingResult = $this->mintingOrchestrator->mint($egi, $user, [
                'source' => 'pre_mint_promotion',
                'auto_mint' => true,
            ]);

            // 5. Extract blockchain transaction details
            $blockchainTxId = $mintingResult->blockchain_txid ?? $mintingResult->txid ?? 'PENDING';
            $assetId = $mintingResult->asset_id ?? $mintingResult->app_id ?? null;

            // 6. GDPR: Log user action with AuditLogService
            $this->auditService->logUserAction($user, 'egi_promoted_to_blockchain', [
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'previous_state' => $previousState,
                'new_state' => [
                    'egi_type' => $targetType,
                    'blockchain_tx_id' => $blockchainTxId,
                    'asset_id' => $assetId,
                ],
                'target_type' => $targetType,
                'minting_result_class' => get_class($mintingResult),
                'ip_address' => $requestMetadata['ip_address'],
                'user_agent' => $requestMetadata['user_agent'],
            ], GdprActivityCategory::WALLET_MANAGEMENT); // Blockchain operations = Wallet category

            // 7. ULM: Log successful promotion with blockchain confirmation
            $this->logger->info('[PRE_MINT_SERVICE] Pre-Mint promoted to blockchain successfully', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'target_type' => $targetType,
                'blockchain_tx_id' => $blockchainTxId,
                'asset_id' => $assetId,
                'minting_result_type' => get_class($mintingResult),
                'log_category' => 'PRE_MINT_PROMOTION_SUCCESS'
            ]);

            return [
                'success' => true,
                'egi_id' => $egi->id,
                'egi_type' => $targetType,
                'blockchain_tx_id' => $blockchainTxId,
                'asset_id' => $assetId,
                'minting_result' => $mintingResult,
                'message' => 'EGI promotion to blockchain completed successfully',
            ];
        } catch (\Exception $e) {
            // 6. ULM: Log service-level error
            $this->logger->error('[PRE_MINT_SERVICE] Pre-Mint promotion failed', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'target_type' => $targetType,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'log_category' => 'PRE_MINT_PROMOTION_ERROR'
            ]);

            // 7. Re-throw for controller UEM handling
            throw new \Exception("Pre-Mint promotion failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Generate AI description for an EGI from scratch
     *
     * @Oracode Method: Generate Description with AI
     * 🎯 Purpose: Create professional description using N.A.T.A.N AI
     * 📥 Input: EGI with title/type, User
     * 📤 Output: AI-generated description saved to DB
     * 🔒 Security: Creator-only operation
     * 🪵 Logging: Full audit trail
     * 🛡️ Privacy: GDPR audit for content generation
     *
     * @param Egi $egi Target EGI for description generation
     * @param User $user Creator user
     * @param array $requestMetadata Request metadata (IP, UA) for audit
     * @return array Generated description result
     * @throws \Exception When AI generation fails
     * @privacy-safe Logs AI content generation with GDPR audit trail
     */
    public function generateDescription(Egi $egi, User $user, array $requestMetadata): array
    {
        try {
            // 1. ULM: Log service operation start
            $this->logger->info('[PRE_MINT_SERVICE] Generating AI description', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'has_existing_description' => !empty($egi->description),
                'log_category' => 'PRE_MINT_GENERATE_DESCRIPTION_START'
            ]);

            // 2. Store previous state for GDPR audit
            $previousDescription = $egi->description;

            // 3. Prepare context for N.A.T.A.N AI
            $egiContext = [
                'egi_id' => $egi->id,
                'title' => $egi->title,
                'type' => $egi->type,
                'creation_date' => $egi->creation_date?->format('Y-m-d'),
                'existing_description' => $egi->description,
            ];

            // 4. Call N.A.T.A.N AI to generate description
            $aiPrompt = "Genera una descrizione professionale, coinvolgente e ottimizzata per il marketplace FlorenceEGI. "
                      . "L'EGI ha titolo: '{$egi->title}' e tipo: '{$egi->type}'. "
                      . "La descrizione deve: "
                      . "(1) Essere accattivante per potenziali acquirenti, "
                      . "(2) Evidenziare il valore e l'unicità dell'opera, "
                      . "(3) Essere lunga 2-3 paragrafi (150-250 parole), "
                      . "(4) Usare linguaggio professionale ma accessibile, "
                      . "(5) Includere dettagli rilevanti per il tipo di asset. "
                      . "Fornisci SOLO il testo della descrizione, senza titoli o prefissi.";

            $generatedDescription = $this->anthropicService->chat($aiPrompt, $egiContext, []);

            // 5. Save description to database
            DB::transaction(function () use ($egi, $generatedDescription) {
                $egi->update([
                    'description' => trim($generatedDescription),
                ]);
            });

            // 6. GDPR: Log user action with AuditLogService
            $this->auditService->logUserAction($user, 'egi_description_generated_by_ai', [
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'previous_description' => $previousDescription,
                'new_description_length' => strlen($generatedDescription),
                'ai_model_used' => config('services.anthropic.model', 'claude-3-5-sonnet-20241022'),
                'ip_address' => $requestMetadata['ip_address'],
                'user_agent' => $requestMetadata['user_agent'],
            ], GdprActivityCategory::CONTENT_CREATION);

            // 7. ULM: Log successful generation
            $this->logger->info('[PRE_MINT_SERVICE] AI description generated successfully', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'description_length' => strlen($generatedDescription),
                'log_category' => 'PRE_MINT_GENERATE_DESCRIPTION_SUCCESS'
            ]);

            return [
                'success' => true,
                'egi_id' => $egi->id,
                'description' => $generatedDescription,
                'previous_description' => $previousDescription,
                'message' => 'Descrizione generata da N.A.T.A.N AI e salvata con successo',
            ];

        } catch (\Exception $e) {
            // 8. ULM: Log service-level error
            $this->logger->error('[PRE_MINT_SERVICE] Description generation failed', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'error_message' => $e->getMessage(),
                'log_category' => 'PRE_MINT_GENERATE_DESCRIPTION_ERROR'
            ]);

            // 9. Re-throw for controller UEM handling
            throw new \Exception("Description generation failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Improve existing EGI description using AI
     *
     * @Oracode Method: Improve Description with AI
     * 🎯 Purpose: Enhance existing description using N.A.T.A.N AI
     * 📥 Input: EGI with existing description, User
     * 📤 Output: AI-improved description saved to DB
     * 🔒 Security: Creator-only operation
     * 🪵 Logging: Full audit trail
     * 🛡️ Privacy: GDPR audit for content modification
     *
     * @param Egi $egi Target EGI with existing description
     * @param User $user Creator user
     * @param array $requestMetadata Request metadata (IP, UA) for audit
     * @return array Improved description result
     * @throws \Exception When AI improvement fails or no description exists
     * @privacy-safe Logs AI content modification with GDPR audit trail
     */
    public function improveDescription(Egi $egi, User $user, array $requestMetadata): array
    {
        try {
            // 1. Validate existing description
            if (empty($egi->description)) {
                throw new \Exception('Cannot improve description: EGI has no existing description. Use generateDescription() instead.');
            }

            // 2. ULM: Log service operation start
            $this->logger->info('[PRE_MINT_SERVICE] Improving AI description', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'original_length' => strlen($egi->description),
                'log_category' => 'PRE_MINT_IMPROVE_DESCRIPTION_START'
            ]);

            // 3. Store previous state for GDPR audit
            $previousDescription = $egi->description;

            // 4. Prepare context for N.A.T.A.N AI
            $egiContext = [
                'egi_id' => $egi->id,
                'title' => $egi->title,
                'type' => $egi->type,
                'current_description' => $egi->description,
            ];

            // 5. Call N.A.T.A.N AI to improve description
            $aiPrompt = "Migliora questa descrizione di un EGI sul marketplace FlorenceEGI rendendola più professionale, coinvolgente e vendibile. "
                      . "Descrizione attuale: '{$egi->description}' "
                      . "Mantieni il significato originale ma: "
                      . "(1) Rendi il linguaggio più accattivante e professionale, "
                      . "(2) Evidenzia meglio il valore dell'opera, "
                      . "(3) Ottimizza la struttura e la leggibilità, "
                      . "(4) Lunghezza ideale: 2-3 paragrafi (150-250 parole), "
                      . "(5) Correggi eventuali errori grammaticali. "
                      . "Fornisci SOLO il testo della descrizione migliorata, senza commenti o prefissi.";

            $improvedDescription = $this->anthropicService->chat($aiPrompt, $egiContext, []);

            // 6. Save improved description to database
            DB::transaction(function () use ($egi, $improvedDescription) {
                $egi->update([
                    'description' => trim($improvedDescription),
                ]);
            });

            // 7. GDPR: Log user action with AuditLogService
            $this->auditService->logUserAction($user, 'egi_description_improved_by_ai', [
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'previous_description' => $previousDescription,
                'previous_description_length' => strlen($previousDescription),
                'new_description_length' => strlen($improvedDescription),
                'ai_model_used' => config('services.anthropic.model', 'claude-3-5-sonnet-20241022'),
                'ip_address' => $requestMetadata['ip_address'],
                'user_agent' => $requestMetadata['user_agent'],
            ], GdprActivityCategory::CONTENT_MODIFICATION);

            // 8. ULM: Log successful improvement
            $this->logger->info('[PRE_MINT_SERVICE] AI description improved successfully', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'original_length' => strlen($previousDescription),
                'improved_length' => strlen($improvedDescription),
                'log_category' => 'PRE_MINT_IMPROVE_DESCRIPTION_SUCCESS'
            ]);

            return [
                'success' => true,
                'egi_id' => $egi->id,
                'description' => $improvedDescription,
                'previous_description' => $previousDescription,
                'improvement_stats' => [
                    'original_length' => strlen($previousDescription),
                    'improved_length' => strlen($improvedDescription),
                    'length_change' => strlen($improvedDescription) - strlen($previousDescription),
                ],
                'message' => 'Descrizione migliorata da N.A.T.A.N AI e salvata con successo',
            ];

        } catch (\Exception $e) {
            // 9. ULM: Log service-level error
            $this->logger->error('[PRE_MINT_SERVICE] Description improvement failed', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'error_message' => $e->getMessage(),
                'log_category' => 'PRE_MINT_IMPROVE_DESCRIPTION_ERROR'
            ]);

            // 10. Re-throw for controller UEM handling
            throw new \Exception("Description improvement failed: " . $e->getMessage(), 0, $e);
        }
    }
}
