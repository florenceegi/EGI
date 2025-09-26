<?php

namespace App\Services\Coa;

use App\Models\Coa;
use App\Models\CoaEvent;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Chain of Custody Service
 *
 * Manages the complete audit trail and chain of custody for CoA certificates.
 * Tracks all signature events, PDF operations, and validation activities.
 *
 * @package FlorenceEGI\Services\Coa
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI CoA Pro System)
 * @date 2025-09-26
 */
class ChainOfCustodyService
{
    /**
     * Log author signature event
     */
    public function logAuthorSignature(
        Coa $coa,
        array $signatureInfo,
        ?User $actor = null,
        ?string $reason = null
    ): CoaEvent {
        $event = CoaEvent::createAuthorSigned(
            $coa->id,
            $signatureInfo,
            $actor?->id,
            $reason
        );

        Log::info('[Chain of Custody] Author signature logged', [
            'coa_id' => $coa->id,
            'serial' => $coa->serial,
            'actor_id' => $actor?->id,
            'signature_info' => $signatureInfo,
            'event_id' => $event->id,
        ]);

        return $event;
    }

    /**
     * Log inspector signature event
     */
    public function logInspectorSignature(
        Coa $coa,
        array $signatureInfo,
        ?User $actor = null,
        ?string $reason = null
    ): CoaEvent {
        $event = CoaEvent::createInspectorSigned(
            $coa->id,
            $signatureInfo,
            $actor?->id,
            $reason
        );

        Log::info('[Chain of Custody] Inspector signature logged', [
            'coa_id' => $coa->id,
            'serial' => $coa->serial,
            'actor_id' => $actor?->id,
            'signature_info' => $signatureInfo,
            'event_id' => $event->id,
        ]);

        return $event;
    }

    /**
     * Log PDF regeneration event
     */
    public function logPdfRegeneration(
        Coa $coa,
        array $fileInfo,
        array $signatureStatus,
        ?User $actor = null
    ): CoaEvent {
        $event = CoaEvent::createPdfRegenerated(
            $coa->id,
            $fileInfo,
            $signatureStatus,
            $actor?->id
        );

        Log::info('[Chain of Custody] PDF regeneration logged', [
            'coa_id' => $coa->id,
            'serial' => $coa->serial,
            'actor_id' => $actor?->id,
            'file_info' => $fileInfo,
            'signature_status' => $signatureStatus,
            'event_id' => $event->id,
        ]);

        return $event;
    }

    /**
     * Log PDF download event
     */
    public function logPdfDownload(
        Coa $coa,
        array $downloadInfo,
        ?User $actor = null
    ): CoaEvent {
        $event = CoaEvent::createPdfDownloaded(
            $coa->id,
            $downloadInfo,
            $actor?->id
        );

        Log::info('[Chain of Custody] PDF download logged', [
            'coa_id' => $coa->id,
            'serial' => $coa->serial,
            'actor_id' => $actor?->id,
            'download_info' => $downloadInfo,
            'event_id' => $event->id,
        ]);

        return $event;
    }

    /**
     * Log signature validation event
     */
    public function logSignatureValidation(
        Coa $coa,
        array $validationResult,
        ?User $actor = null
    ): CoaEvent {
        $event = CoaEvent::createSignatureValidated(
            $coa->id,
            $validationResult,
            $actor?->id
        );

        Log::info('[Chain of Custody] Signature validation logged', [
            'coa_id' => $coa->id,
            'serial' => $coa->serial,
            'actor_id' => $actor?->id,
            'validation_result' => $validationResult,
            'event_id' => $event->id,
        ]);

        return $event;
    }

    /**
     * Get complete chain of custody for a CoA
     */
    public function getChainOfCustody(Coa $coa): array
    {
        $events = $coa->events()
            ->with('actor')
            ->orderBy('created_at', 'asc')
            ->get();

        $chain = [];
        $signatureCount = 0;
        $pdfOperations = 0;
        $validations = 0;

        foreach ($events as $event) {
            $chain[] = [
                'id' => $event->id,
                'type' => $event->type,
                'description' => $event->getDisplayDescription(),
                'icon' => $event->getIcon(),
                'color_class' => $event->getColorClass(),
                'timestamp' => $event->created_at,
                'actor' => $event->actor?->name ?? 'System',
                'payload' => $event->payload,
                'affects_validity' => $event->affectsValidity(),
                'is_signature_event' => $event->isSignatureEvent(),
                'is_pdf_event' => $event->isPdfEvent(),
            ];

            // Count different event types
            if ($event->isSignatureEvent()) {
                $signatureCount++;
            }
            if ($event->isPdfEvent()) {
                $pdfOperations++;
            }
            if ($event->type === CoaEvent::TYPE_SIGNATURE_VALIDATED) {
                $validations++;
            }
        }

        return [
            'coa_id' => $coa->id,
            'serial' => $coa->serial,
            'status' => $coa->status,
            'chain' => $chain,
            'statistics' => [
                'total_events' => count($chain),
                'signature_events' => $signatureCount,
                'pdf_operations' => $pdfOperations,
                'validations' => $validations,
                'last_event' => $events->last()?->created_at,
            ],
        ];
    }

    /**
     * Get signature timeline for a CoA
     */
    public function getSignatureTimeline(Coa $coa): array
    {
        $signatureEvents = $coa->events()
            ->whereIn('type', [
                CoaEvent::TYPE_AUTHOR_SIGNED,
                CoaEvent::TYPE_INSPECTOR_SIGNED,
                CoaEvent::TYPE_SIGNATURE_VALIDATED,
            ])
            ->with('actor')
            ->orderBy('created_at', 'asc')
            ->get();

        $timeline = [];
        foreach ($signatureEvents as $event) {
            $timeline[] = [
                'id' => $event->id,
                'type' => $event->type,
                'description' => $event->getDisplayDescription(),
                'icon' => $event->getIcon(),
                'color_class' => $event->getColorClass(),
                'timestamp' => $event->created_at,
                'actor' => $event->actor?->name ?? 'System',
                'signature_info' => $event->payload['signature_info'] ?? null,
                'validation_result' => $event->payload['validation_result'] ?? null,
            ];
        }

        return [
            'coa_id' => $coa->id,
            'serial' => $coa->serial,
            'timeline' => $timeline,
            'total_signatures' => count($timeline),
        ];
    }

    /**
     * Get PDF operations timeline for a CoA
     */
    public function getPdfOperationsTimeline(Coa $coa): array
    {
        $pdfEvents = $coa->events()
            ->whereIn('type', [
                CoaEvent::TYPE_PDF_REGENERATED,
                CoaEvent::TYPE_PDF_DOWNLOADED,
            ])
            ->with('actor')
            ->orderBy('created_at', 'asc')
            ->get();

        $timeline = [];
        foreach ($pdfEvents as $event) {
            $timeline[] = [
                'id' => $event->id,
                'type' => $event->type,
                'description' => $event->getDisplayDescription(),
                'icon' => $event->getIcon(),
                'color_class' => $event->getColorClass(),
                'timestamp' => $event->created_at,
                'actor' => $event->actor?->name ?? 'System',
                'file_info' => $event->payload['file_info'] ?? null,
                'download_info' => $event->payload['download_info'] ?? null,
                'signature_status' => $event->payload['signature_status'] ?? null,
            ];
        }

        return [
            'coa_id' => $coa->id,
            'serial' => $coa->serial,
            'timeline' => $timeline,
            'total_operations' => count($timeline),
        ];
    }

    /**
     * Validate chain of custody integrity
     */
    public function validateChainIntegrity(Coa $coa): array
    {
        $events = $coa->events()->orderBy('created_at', 'asc')->get();
        $issues = [];
        $warnings = [];

        // Check for missing events
        $hasIssued = $events->where('type', CoaEvent::TYPE_ISSUED)->count() > 0;
        if (!$hasIssued) {
            $issues[] = 'Missing ISSUED event';
        }

        // Check signature sequence
        $authorSigned = $events->where('type', CoaEvent::TYPE_AUTHOR_SIGNED)->count() > 0;
        $inspectorSigned = $events->where('type', CoaEvent::TYPE_INSPECTOR_SIGNED)->count() > 0;

        if ($inspectorSigned && !$authorSigned) {
            $warnings[] = 'Inspector signature found without author signature';
        }

        // Check for orphaned PDF operations
        $pdfRegenerations = $events->where('type', CoaEvent::TYPE_PDF_REGENERATED)->count();
        $pdfDownloads = $events->where('type', CoaEvent::TYPE_PDF_DOWNLOADED)->count();

        if ($pdfDownloads > $pdfRegenerations) {
            $warnings[] = 'More PDF downloads than regenerations detected';
        }

        // Check timestamp consistency
        $lastEvent = $events->last();
        if ($lastEvent && $lastEvent->created_at > now()) {
            $issues[] = 'Future timestamp detected in chain';
        }

        return [
            'coa_id' => $coa->id,
            'serial' => $coa->serial,
            'is_valid' => empty($issues),
            'issues' => $issues,
            'warnings' => $warnings,
            'total_events' => $events->count(),
            'signature_events' => $events->whereIn('type', [
                CoaEvent::TYPE_AUTHOR_SIGNED,
                CoaEvent::TYPE_INSPECTOR_SIGNED,
            ])->count(),
            'pdf_operations' => $events->whereIn('type', [
                CoaEvent::TYPE_PDF_REGENERATED,
                CoaEvent::TYPE_PDF_DOWNLOADED,
            ])->count(),
        ];
    }

    /**
     * Log signature removal event
     */
    public function logSignatureRemoval(
        Coa $coa,
        User $actor,
        string $role,
        array $signatureInfo,
        ?string $reason = null
    ): CoaEvent {
        $event = CoaEvent::createSignatureRemoved(
            $coa,
            $actor,
            $role,
            $signatureInfo,
            $reason
        );

        Log::info('Chain of Custody: Signature removed', [
            'coa_id' => $coa->id,
            'serial' => $coa->serial,
            'actor_id' => $actor->id,
            'role' => $role,
            'event_id' => $event->id,
            'signature_info' => $signatureInfo,
            'reason' => $reason
        ]);

        return $event;
    }
}
