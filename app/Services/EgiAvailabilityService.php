<?php

namespace App\Services;

use App\Models\Egi;
use App\Models\User;
use App\Models\Reservation;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use App\Enums\Gdpr\GdprActivityCategory;

/**
 * @Oracode Service: EGI Availability Check            $this->auditService->logActivity(
                $user,
                'egi_availability_checked',
                [
                    'egi_id' => $egi->id,
                    'egi_title' => $egi->title,
                    'can_mint' => $result['can_mint'],
                    'can_reserve' => $result['can_reserve'],
                    'available_actions' => $result['available_actions'],
                    'recommended_action' => $result['recommended_action']
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );h Orchestration
 * 🎯 Purpose: Orchestrate business logic for dual-path EGI availability (Mint vs Reservation)
 * 🧱 Core Logic: Centralized availability checks, action determination, permission validation
 * 🛡️ Security: Permission-based access control, GDPR audit trail, MiCA-SAFE compliance
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Phase 2 Expansion)
 * @date 2025-10-09
 * @purpose Dual path availability service for mint/reservation coexistence
 *
 * Business Rules:
 * - EGI non mintato può essere PRENOTATO O MINTATO
 * - EGI già mintato → nessuna azione disponibile
 * - EGI prenotato da user → solo MINT disponibile
 * - EGI prenotato da altri → MINT disponibile (se permesso)
 * - Creator non può prenotare/mintare il proprio EGI
 *
 * MiCA-SAFE Compliance:
 * - Solo mint service (no crypto custody per users)
 * - FIAT payments only
 * - Treasury wallet platform management
 */
class EgiAvailabilityService {

    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private ConsentService $consentService;

    /**
     * Constructor with GDPR/Ultra compliance
     *
     * @param UltraLogManager $logger Ultra logging manager for audit trails
     * @param ErrorManagerInterface $errorManager Ultra error manager for error handling
     * @param AuditLogService $auditService GDPR audit logging service
     * @param ConsentService $consentService GDPR consent management service
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
    }

    /**
     * Check comprehensive availability of an EGI for a specific user.
     *
     * Returns detailed availability status for both mint and reservation actions.
     *
     * @param Egi $egi The EGI to check
     * @param User|null $user The user checking availability (null = guest)
     * @return array Availability status array
     *
     * @example Return structure:
     * [
     *     'can_mint' => bool,
     *     'can_reserve' => bool,
     *     'can_rebind' => bool,           // 🆕 Secondary market
     *     'is_rebind' => bool,            // 🆕 Is this a rebind (secondary sale)?
     *     'mint_reason' => string|null,
     *     'reserve_reason' => string|null,
     *     'is_creator' => bool,
     *     'is_owner' => bool,             // 🆕 Is user the current owner?
     *     'is_reserved_by_user' => bool,
     *     'has_pending_reservation' => bool,
     *     'winning_reservation' => Reservation|null,
     *     'user_reservation' => Reservation|null,
     *     'available_actions' => ['mint', 'reserve', 'rebind'],
     *     'recommended_action' => 'mint'|'reserve'|'rebind'|null
     * ]
     */
    public function checkAvailability(Egi $egi, ?User $user = null): array {
        try {
            // ULM: Log check start
            $this->logger->info('EGI_AVAILABILITY_CHECK_START', [
                'egi_id' => $egi->id,
                'user_id' => $user?->id,
                'egi_status' => $egi->status,
                'is_minted' => $egi->isMinted()
            ]);

            // Initialize response structure
            $result = [
                'can_mint' => false,
                'can_reserve' => false,
                'can_rebind' => false,      // 🆕 Secondary market
                'is_rebind' => false,       // 🆕 Is this a rebind?
                'mint_reason' => null,
                'reserve_reason' => null,
                'is_creator' => false,
                'is_owner' => false,        // 🆕 Is user the current owner?
                'is_reserved_by_user' => false,
                'has_pending_reservation' => false,
                'winning_reservation' => null,
                'user_reservation' => null,
                'available_actions' => [],
                'recommended_action' => null
            ];

            // Check if user is authenticated
            if (!$user) {
                $result['mint_reason'] = 'authentication_required';
                $result['reserve_reason'] = 'authentication_required';

                $this->logger->info('EGI_AVAILABILITY_CHECK_GUEST', [
                    'egi_id' => $egi->id,
                    'result' => 'guest_no_actions'
                ]);

                return $result;
            }

            // Check if user is the creator
            $result['is_creator'] = $egi->user_id === $user->id;

            // 🆕 Check if user is the current owner (for rebind - cannot buy own EGI)
            $result['is_owner'] = $egi->owner_id === $user->id;

            if ($result['is_creator']) {
                $result['mint_reason'] = 'own_egi_cannot_mint';
                $result['reserve_reason'] = 'own_egi_cannot_reserve';

                $this->logger->info('EGI_AVAILABILITY_CHECK_CREATOR', [
                    'egi_id' => $egi->id,
                    'user_id' => $user->id,
                    'result' => 'creator_no_actions'
                ]);

                return $result;
            }

            // 🆕 Check if user is the current owner (cannot rebind own EGI)
            if ($result['is_owner']) {
                $result['mint_reason'] = 'own_egi_cannot_rebind';
                $result['reserve_reason'] = 'own_egi_cannot_rebind';

                $this->logger->info('EGI_AVAILABILITY_CHECK_OWNER', [
                    'egi_id' => $egi->id,
                    'user_id' => $user->id,
                    'result' => 'owner_no_actions'
                ]);

                return $result;
            }

            // 🆕 Check if this is a REBIND scenario (EGI minted and for sale)
            $result['is_rebind'] = $egi->canBeRebind();

            // Get reservation status
            $result['has_pending_reservation'] = $egi->hasPendingReservation();
            $result['is_reserved_by_user'] = $egi->isReservedByUser($user);
            $result['winning_reservation'] = $egi->getWinningReservation();
            $result['user_reservation'] = $egi->getUserReservation($user);

            // 🆕 Check REBIND availability (secondary market)
            if ($result['is_rebind']) {
                $rebindCheck = $this->checkRebindAvailability($egi, $user, $result);
                $result['can_rebind'] = $rebindCheck['can_rebind'];
                // For rebind, we don't check mint/reserve (different flow)
                $result['mint_reason'] = 'egi_is_rebind';
                $result['reserve_reason'] = 'egi_is_rebind';
            } else {
                // Check MINT availability (primary market)
                $mintCheck = $this->checkMintAvailability($egi, $user, $result);
                $result['can_mint'] = $mintCheck['can_mint'];
                $result['mint_reason'] = $mintCheck['reason'];

                // Check RESERVE availability (primary market)
                $reserveCheck = $this->checkReserveAvailability($egi, $user, $result);
                $result['can_reserve'] = $reserveCheck['can_reserve'];
                $result['reserve_reason'] = $reserveCheck['reason'];
            }

            // Build available actions list
            if ($result['can_rebind']) {
                $result['available_actions'][] = 'rebind';
            }
            if ($result['can_mint']) {
                $result['available_actions'][] = 'mint';
            }
            if ($result['can_reserve']) {
                $result['available_actions'][] = 'reserve';
            }

            // Determine recommended action
            $result['recommended_action'] = $this->determineRecommendedAction($result);

            // ULM: Log check completion
            $this->logger->info('EGI_AVAILABILITY_CHECK_COMPLETE', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'can_mint' => $result['can_mint'],
                'can_reserve' => $result['can_reserve'],
                'can_rebind' => $result['can_rebind'],
                'is_rebind' => $result['is_rebind'],
                'available_actions' => $result['available_actions']
            ]);

            return $result;
        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('EGI_AVAILABILITY_CHECK_FAILED', [
                'egi_id' => $egi->id,
                'user_id' => $user?->id,
                'error' => $e->getMessage()
            ], $e);

            // Return safe default (no actions available)
            return [
                'can_mint' => false,
                'can_reserve' => false,
                'can_rebind' => false,
                'is_rebind' => false,
                'mint_reason' => 'check_error',
                'reserve_reason' => 'check_error',
                'is_creator' => false,
                'is_owner' => false,
                'is_reserved_by_user' => false,
                'has_pending_reservation' => false,
                'winning_reservation' => null,
                'user_reservation' => null,
                'available_actions' => [],
                'recommended_action' => null
            ];
        }
    }

    /**
     * Get available actions for an EGI and user.
     *
     * Simplified method that returns only the list of available actions.
     *
     * @param Egi $egi The EGI to check
     * @param User|null $user The user checking availability
     * @return array Array of available action strings ['mint', 'reserve', 'rebind']
     */
    public function getAvailableActions(Egi $egi, ?User $user = null): array {
        $availability = $this->checkAvailability($egi, $user);
        return $availability['available_actions'];
    }

    /**
     * Check if user can rebind this EGI (secondary market purchase).
     *
     * @param Egi $egi The EGI to check
     * @param User $user The user
     * @param array $context Context data from main availability check
     * @return array ['can_rebind' => bool, 'reason' => string|null]
     */
    private function checkRebindAvailability(Egi $egi, User $user, array $context): array {
        // Check basic EGI rebindability
        if (!$egi->canBeRebind()) {
            $this->logger->warning('EGI_REBIND_CHECK_FAILED_NOT_REBINDABLE', [
                'egi_id' => $egi->id,
                'is_minted' => $egi->isMinted(),
                'is_published' => $egi->is_published,
                'price' => $egi->price
            ]);
            return ['can_rebind' => false, 'reason' => 'egi_not_rebindable'];
        }

        // User cannot rebind their own EGI (they are the seller, not buyer)
        if ($egi->owner_id === $user->id) {
            $this->logger->info('EGI_REBIND_CHECK_SKIPPED_IS_OWNER', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'reason' => 'user_is_owner'
            ]);
            return ['can_rebind' => false, 'reason' => 'user_is_owner'];
        }

        // Check user has accepted platform services
        if (!$this->consentService->hasConsent($user, 'platform-services')) {
            $this->logger->warning('EGI_REBIND_CHECK_FAILED_CONSENT', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'reason' => 'platform_services_consent_missing'
            ]);
            return ['can_rebind' => false, 'reason' => 'missing_consent'];
        }

        // All checks passed
        $this->logger->info('EGI_REBIND_CHECK_PASSED', [
            'egi_id' => $egi->id,
            'user_id' => $user->id
        ]);
        return ['can_rebind' => true, 'reason' => null];
    }

    /**
     * Check if user can mint this EGI.
     *
     * @param Egi $egi The EGI to check
     * @param User $user The user
     * @param array $context Context data from main availability check
     * @return array ['can_mint' => bool, 'reason' => string|null]
     */
    private function checkMintAvailability(Egi $egi, User $user, array $context): array {
        // Check basic EGI mintability
        if (!$egi->canBeMinted()) {
            $this->logger->warning('EGI_MINT_CHECK_FAILED_NOT_MINTABLE', [
                'egi_id' => $egi->id,
                'is_minted' => $egi->isMinted(),
                'status' => $egi->status,
                'is_published' => $egi->is_published
            ]);

            if ($egi->isMinted()) {
                return ['can_mint' => false, 'reason' => 'already_minted'];
            }
            if ($egi->status === 'draft') {
                return ['can_mint' => false, 'reason' => 'egi_draft'];
            }
            return ['can_mint' => false, 'reason' => 'egi_not_mintable'];
        }

        // Check user has accepted platform services (implies consent for all operations)
        if (!$this->consentService->hasConsent($user, 'platform-services')) {
            $this->logger->warning('EGI_MINT_CHECK_FAILED_CONSENT', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'reason' => 'platform_services_consent_missing'
            ]);
            return ['can_mint' => false, 'reason' => 'missing_consent'];
        }

        // All checks passed
        $this->logger->info('EGI_MINT_CHECK_PASSED', [
            'egi_id' => $egi->id,
            'user_id' => $user->id
        ]);
        return ['can_mint' => true, 'reason' => null];
    }

    /**
     * Check if user can reserve this EGI.
     *
     * @param Egi $egi The EGI to check
     * @param User $user The user
     * @param array $context Context data from main availability check
     * @return array ['can_reserve' => bool, 'reason' => string|null]
     */
    private function checkReserveAvailability(Egi $egi, User $user, array $context): array {
        // Check basic EGI reservability
        if (!$egi->canBeReserved()) {
            $this->logger->warning('EGI_RESERVE_CHECK_FAILED_NOT_RESERVABLE', [
                'egi_id' => $egi->id,
                'is_minted' => $egi->isMinted(),
                'status' => $egi->status,
                'is_published' => $egi->is_published
            ]);

            if ($egi->isMinted()) {
                return ['can_reserve' => false, 'reason' => 'already_minted'];
            }
            if ($egi->status === 'draft') {
                return ['can_reserve' => false, 'reason' => 'egi_draft'];
            }
            return ['can_reserve' => false, 'reason' => 'egi_not_reservable'];
        }

        // User already has reservation → can only mint, not reserve again
        if ($context['is_reserved_by_user']) {
            $this->logger->info('EGI_RESERVE_CHECK_ALREADY_RESERVED', [
                'egi_id' => $egi->id,
                'user_id' => $user->id
            ]);
            return ['can_reserve' => false, 'reason' => 'user_already_reserved'];
        }

        // Check user has accepted platform services (implies consent for all operations)
        if (!$this->consentService->hasConsent($user, 'platform-services')) {
            $this->logger->warning('EGI_RESERVE_CHECK_FAILED_CONSENT', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'reason' => 'platform_services_consent_missing'
            ]);
            return ['can_reserve' => false, 'reason' => 'missing_consent'];
        }

        // All checks passed
        $this->logger->info('EGI_RESERVE_CHECK_PASSED', [
            'egi_id' => $egi->id,
            'user_id' => $user->id
        ]);
        return ['can_reserve' => true, 'reason' => null];
    }

    /**
     * Determine the recommended action for the user.
     *
     * Priority logic:
     * - If user has reservation → recommend MINT
     * - If both available → recommend RESERVE (lower commitment)
     * - If only one available → recommend that one
     * - If none available → return null
     *
     * @param array $availability The availability result array
     * @return string|null 'mint', 'reserve', or null
     */
    private function determineRecommendedAction(array $availability): ?string {
        // Rebind available (secondary market) → recommend rebind
        if ($availability['can_rebind']) {
            return 'rebind';
        }

        // User has reservation → recommend mint to complete purchase
        if ($availability['is_reserved_by_user'] && $availability['can_mint']) {
            return 'mint';
        }

        // Both available → recommend reserve (lower commitment, user can decide later)
        if ($availability['can_mint'] && $availability['can_reserve']) {
            return 'reserve';
        }

        // Only mint available
        if ($availability['can_mint']) {
            return 'mint';
        }

        // Only reserve available
        if ($availability['can_reserve']) {
            return 'reserve';
        }

        // No actions available
        return null;
    }

    /**
     * Log availability check for GDPR audit trail.
     *
     * Called when user views EGI detail page or checks availability.
     *
     * @param Egi $egi The EGI checked
     * @param User $user The user checking
     * @param array $result The availability check result
     * @return void
     */
    public function logAvailabilityCheck(Egi $egi, User $user, array $result): void {
        try {
            $this->auditService->logUserAction(
                $user,
                'egi_availability_checked',
                [
                    'egi_id' => $egi->id,
                    'egi_title' => $egi->title,
                    'can_mint' => $result['can_mint'],
                    'can_reserve' => $result['can_reserve'],
                    'can_rebind' => $result['can_rebind'],
                    'available_actions' => $result['available_actions'],
                    'recommended_action' => $result['recommended_action']
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );
        } catch (\Exception $e) {
            // Silent fail for audit logging (non-blocking)
            $this->logger->error('AVAILABILITY_CHECK_AUDIT_LOG_FAILED', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Batch check availability for multiple EGIs.
     *
     * Optimized method for checking multiple EGIs at once (e.g., for lists/grids).
     *
     * @param \Illuminate\Support\Collection $egis Collection of Egi models
     * @param User|null $user The user checking availability
     * @return array Associative array [egi_id => availability_result]
     */
    public function batchCheckAvailability(\Illuminate\Support\Collection $egis, ?User $user = null): array {
        $results = [];

        foreach ($egis as $egi) {
            $results[$egi->id] = $this->checkAvailability($egi, $user);
        }

        $this->logger->info('EGI_BATCH_AVAILABILITY_CHECK', [
            'user_id' => $user?->id,
            'egi_count' => $egis->count(),
            'results_count' => count($results)
        ]);

        return $results;
    }
}
