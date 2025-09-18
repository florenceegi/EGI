<?php

namespace App\Services\Coa;

use App\Models\Coa;
use App\Models\CoaEvent;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Service: CoA Certificate Revocation
 * 🎯 Purpose: Manage revocation of CoA certificates with audit trail
 * 🛡️ Privacy: Handles GDPR-compliant certificate revocation with full logging
 * 🧱 Core Logic: Manages certificate invalidation while preserving historical records
 *
 * @package App\Services\Coa
 * @author AI Assistant following FlorenceEGI patterns
 * @version 1.0.0 (CoA Pro System)
 * @date 2025-09-18
 * @purpose Certificate lifecycle management for authenticity system
 */
class CoaRevocationService {
    /**
     * Logger instance for audit trail
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Error manager for robust error handling
     * @var ErrorManagerInterface
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * Audit logging service
     * @var AuditLogService
     */
    protected AuditLogService $auditService;

    /**
     * Available revocation reasons
     */
    public const REVOCATION_REASONS = [
        'error' => 'Data error in certificate',
        'fraud' => 'Fraudulent information detected',
        'request' => 'Owner request',
        'update' => 'System upgrade',
        'reissue' => 'Certificate re-issuance',
        'legal' => 'Legal requirement',
        'technical' => 'Technical issue'
    ];

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param AuditLogService $auditService
     * @privacy-safe All injected services handle GDPR compliance
     */
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
     * Revoke a CoA certificate
     *
     * @param Coa $coa The CoA to revoke
     * @param string $reason Reason for revocation
     * @param string|null $additionalNotes Additional notes about revocation
     * @return bool True if revocation successful
     * @privacy-safe Revokes only authenticated user's certificates
     *
     * @oracode-dimension governance
     * @value-flow Invalidates certificate while preserving audit trail
     * @community-impact Maintains certificate integrity through proper revocation
     * @transparency-level High - complete revocation process logging
     * @narrative-coherence Links revocation to certificate lifecycle
     */
    public function revokeCertificate(Coa $coa, string $reason, ?string $additionalNotes = null): bool {
        try {
            $user = Auth::user();

            // Security check - user must own the CoA's EGI
            if ($coa->egi->user_id !== $user->id) {
                $this->errorManager->handle('COA_REVOKE_UNAUTHORIZED', [
                    'user_id' => $user->id,
                    'coa_id' => $coa->id,
                    'egi_owner_id' => $coa->egi->user_id,
                    'ip_address' => request()->ip(),
                    'timestamp' => now()->toIso8601String()
                ], new \Illuminate\Auth\Access\AuthorizationException('User cannot revoke CoA they do not own'));

                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            // Check if CoA is already revoked
            if ($coa->status === 'revoked') {
                $this->logger->warning('[CoA Revoke] CoA already revoked', [
                    'user_id' => $user->id,
                    'coa_id' => $coa->id,
                    'serial' => $coa->serial,
                    'existing_revoke_reason' => $coa->revoke_reason
                ]);

                throw new \Exception('Certificate is already revoked.');
            }

            // Validate revocation reason
            if (!array_key_exists($reason, self::REVOCATION_REASONS)) {
                throw new \Exception('Invalid revocation reason provided.');
            }

            $this->logger->info('[CoA Revoke] Starting certificate revocation', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'reason' => $reason,
                'additional_notes' => $additionalNotes
            ]);

            // Use database transaction
            $success = DB::transaction(function () use ($coa, $reason, $additionalNotes, $user) {
                return $this->revokeCoaInTransaction($coa, $reason, $additionalNotes, $user);
            });

            if ($success) {
                $this->logger->info('[CoA Revoke] Certificate revoked successfully', [
                    'user_id' => $user->id,
                    'coa_id' => $coa->id,
                    'serial' => $coa->serial,
                    'reason' => $reason,
                    'revoked_at' => $coa->fresh()->revoked_at
                ]);

                // Log audit trail
                $this->auditService->logUserAction($user, 'coa_revoked', [
                    'coa_id' => $coa->id,
                    'serial' => $coa->serial,
                    'reason' => $reason,
                    'reason_description' => self::REVOCATION_REASONS[$reason],
                    'additional_notes' => $additionalNotes
                ], GdprActivityCategory::GDPR_ACTIONS);
            }

            return $success;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e; // Re-throw auth exceptions
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_REVOKE_CERTIFICATE_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'reason' => $reason,
                'error' => $e->getMessage(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Revoke CoA within database transaction
     *
     * @param Coa $coa
     * @param string $reason
     * @param string|null $additionalNotes
     * @param \App\Models\User $user
     * @return bool
     * @privacy-safe Internal transaction method
     */
    protected function revokeCoaInTransaction(Coa $coa, string $reason, ?string $additionalNotes, $user): bool {
        // 1. Update CoA status
        $revokeReasonText = self::REVOCATION_REASONS[$reason];
        if ($additionalNotes) {
            $revokeReasonText .= ": {$additionalNotes}";
        }

        $updated = $coa->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoke_reason' => $revokeReasonText,
        ]);

        if (!$updated) {
            throw new \Exception('Failed to update CoA revocation status');
        }

        // 2. Create revocation event
        $this->createCoaEvent($coa, 'coa_revoked', [
            'revocation_reason' => $reason,
            'reason_description' => self::REVOCATION_REASONS[$reason],
            'revoked_by' => $user->name,
            'additional_notes' => $additionalNotes,
            'notification_sent' => false // Will be updated when notification is sent
        ]);

        $this->logger->info('[CoA Revoke] CoA revoked in transaction', [
            'coa_id' => $coa->id,
            'serial' => $coa->serial,
            'reason' => $reason,
            'revoked_at' => $coa->revoked_at,
            'revoke_reason' => $coa->revoke_reason
        ]);

        return true;
    }

    /**
     * Check if a CoA can be revoked
     *
     * @param Coa $coa The CoA to check
     * @return array Validation result with status and messages
     * @privacy-safe Validation only for user's own certificates
     */
    public function canRevokeCoa(Coa $coa): array {
        try {
            $user = Auth::user();

            // Security check
            if ($coa->egi->user_id !== $user->id) {
                return [
                    'can_revoke' => false,
                    'reason' => 'unauthorized',
                    'message' => 'You can only revoke your own certificates.'
                ];
            }

            // Check if already revoked
            if ($coa->status === 'revoked') {
                return [
                    'can_revoke' => false,
                    'reason' => 'already_revoked',
                    'message' => 'Certificate is already revoked.',
                    'revoked_at' => $coa->revoked_at,
                    'revoke_reason' => $coa->revoke_reason
                ];
            }

            // Check status
            if ($coa->status !== 'valid') {
                return [
                    'can_revoke' => false,
                    'reason' => 'invalid_status',
                    'message' => "Certificate status '{$coa->status}' cannot be revoked.",
                    'current_status' => $coa->status
                ];
            }

            return [
                'can_revoke' => true,
                'reason' => 'valid',
                'message' => 'Certificate can be revoked.',
                'available_reasons' => self::REVOCATION_REASONS
            ];
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_REVOKE_VALIDATION_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'can_revoke' => false,
                'reason' => 'error',
                'message' => 'An error occurred while validating the certificate.',
                'error' => true
            ];
        }
    }

    /**
     * Get revocation history for a CoA
     *
     * @param Coa $coa The CoA to get history for
     * @return array Revocation history data
     * @privacy-safe Returns history only for user's own certificates
     */
    public function getRevocationHistory(Coa $coa): array {
        try {
            $user = Auth::user();

            // Security check
            if ($coa->egi->user_id !== $user->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
            }

            $history = [
                'coa_id' => $coa->id,
                'serial' => $coa->serial,
                'current_status' => $coa->status,
                'is_revoked' => $coa->status === 'revoked',
                'revoked_at' => $coa->revoked_at,
                'revoke_reason' => $coa->revoke_reason,
                'events' => []
            ];

            // Get revocation-related events
            $events = CoaEvent::where('coa_id', $coa->id)
                ->whereIn('event_type', ['coa_revoked', 'coa_reissued'])
                ->orderBy('occurred_at', 'desc')
                ->get();

            foreach ($events as $event) {
                $history['events'][] = [
                    'id' => $event->id,
                    'type' => $event->event_type,
                    'description' => $event->description,
                    'occurred_at' => $event->occurred_at,
                    'event_data' => $event->event_data,
                    'user_id' => $event->user_id
                ];
            }

            $this->logger->info('[CoA Revoke] Revocation history retrieved', [
                'user_id' => $user->id,
                'coa_id' => $coa->id,
                'is_revoked' => $history['is_revoked'],
                'events_count' => count($history['events'])
            ]);

            return $history;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_REVOKE_HISTORY_ERROR', [
                'user_id' => Auth::id(),
                'coa_id' => $coa->id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            return [
                'coa_id' => $coa->id,
                'error' => true,
                'message' => 'Failed to retrieve revocation history'
            ];
        }
    }

    /**
     * Batch revoke multiple CoAs with same reason
     *
     * @param array $coaIds Array of CoA IDs to revoke
     * @param string $reason Reason for revocation
     * @param string|null $additionalNotes Additional notes
     * @return array Results of batch revocation
     * @privacy-safe Revokes only user's own certificates
     */
    public function batchRevokeCertificates(array $coaIds, string $reason, ?string $additionalNotes = null): array {
        try {
            $user = Auth::user();

            $this->logger->info('[CoA Revoke] Starting batch revocation', [
                'user_id' => $user->id,
                'coa_count' => count($coaIds),
                'reason' => $reason
            ]);

            $results = [
                'total' => count($coaIds),
                'successful' => 0,
                'failed' => 0,
                'results' => []
            ];

            foreach ($coaIds as $coaId) {
                try {
                    $coa = Coa::findOrFail($coaId);

                    $success = $this->revokeCertificate($coa, $reason, $additionalNotes);

                    if ($success) {
                        $results['successful']++;
                        $results['results'][$coaId] = [
                            'status' => 'success',
                            'serial' => $coa->serial
                        ];
                    } else {
                        $results['failed']++;
                        $results['results'][$coaId] = [
                            'status' => 'failed',
                            'error' => 'Revocation returned false'
                        ];
                    }
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['results'][$coaId] = [
                        'status' => 'error',
                        'error' => $e->getMessage()
                    ];
                }
            }

            $this->logger->info('[CoA Revoke] Batch revocation completed', [
                'user_id' => $user->id,
                'total' => $results['total'],
                'successful' => $results['successful'],
                'failed' => $results['failed']
            ]);

            // Log audit trail for batch operation
            $this->auditService->logUserAction($user, 'coa_batch_revoked', [
                'reason' => $reason,
                'total_count' => $results['total'],
                'successful_count' => $results['successful'],
                'failed_count' => $results['failed'],
                'coa_ids' => $coaIds
            ], GdprActivityCategory::GDPR_ACTIONS);

            return $results;
        } catch (\Exception $e) {
            $this->errorManager->handle('COA_BATCH_REVOKE_ERROR', [
                'user_id' => Auth::id(),
                'coa_ids' => $coaIds,
                'reason' => $reason,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], $e);

            throw $e;
        }
    }

    /**
     * Create a CoA event record
     *
     * @param Coa $coa
     * @param string $eventType
     * @param array $eventData
     * @return CoaEvent
     * @privacy-safe Creates audit event for user's own CoA
     */
    protected function createCoaEvent(Coa $coa, string $eventType, array $eventData = []): CoaEvent {
        $baseData = [
            'timestamp' => now()->toIso8601String(),
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        $event = CoaEvent::create([
            'coa_id' => $coa->id,
            'user_id' => Auth::id(),
            'event_type' => $eventType,
            'description' => $this->getEventDescription($eventType, $eventData),
            'event_data' => array_merge($baseData, $eventData),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'occurred_at' => now(),
        ]);

        $this->logger->info('[CoA Event] Event created', [
            'coa_id' => $coa->id,
            'event_id' => $event->id,
            'event_type' => $eventType,
            'user_id' => Auth::id()
        ]);

        return $event;
    }

    /**
     * Generate human-readable description for event types
     *
     * @param string $eventType
     * @param array $eventData
     * @return string
     * @privacy-safe Generates description from event metadata
     */
    protected function getEventDescription(string $eventType, array $eventData): string {
        return match ($eventType) {
            'coa_revoked' => 'Certificate revoked: ' . ($eventData['reason_description'] ?? 'Unknown reason'),
            default => "CoA event: {$eventType}"
        };
    }

    /**
     * Get available revocation reasons
     *
     * @return array Available reasons with descriptions
     * @privacy-safe Returns static configuration data
     */
    public function getAvailableReasons(): array {
        return self::REVOCATION_REASONS;
    }
}
