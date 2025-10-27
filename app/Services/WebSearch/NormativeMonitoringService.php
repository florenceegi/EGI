<?php

namespace App\Services\WebSearch;

use App\Models\User;
use App\Services\Gdpr\ConsentService;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NormativeUpdateNotification;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * Normative Monitoring Service - Real-time regulatory updates tracking
 *
 * Monitors Italian and EU regulatory sources for updates relevant to PA operations.
 * Sends GDPR-compliant alerts when new normatives are detected.
 *
 * MONITORED SOURCES:
 * - Gazzetta Ufficiale, Garante Privacy, AGID, Normattiva.it, EUR-Lex
 *
 * GDPR COMPLIANCE (P1 MANDATORY):
 * - Check consent before sending notifications
 * - Audit trail for all monitoring activities
 * - UEM error handling for failures
 * - ULM logging for operations
 *
 * @package App\Services\WebSearch
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Normative Monitoring GDPR-compliant)
 * @date 2025-10-26
 * @purpose Real-time regulatory compliance monitoring for PA with GDPR compliance
 */
class NormativeMonitoringService {
    protected WebSearchService $webSearch;
    protected ConsentService $consentService;
    protected AuditLogService $auditService;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    protected array $monitoringTopics = [
        'gdpr_privacy' => [
            'keywords' => 'Garante Privacy nuove linee guida GDPR 2024 2025',
            'sources' => ['garanteprivacy.it', 'edpb.europa.eu'],
            'notify_roles' => ['dpo', 'pa_admin'],
        ],
        'digital_transformation' => [
            'keywords' => 'AGID decreto digitale PA trasformazione digitale 2024 2025',
            'sources' => ['agid.gov.it', 'innovazione.gov.it'],
            'notify_roles' => ['pa_admin', 'it_manager'],
        ],
        'public_procurement' => [
            'keywords' => 'appalti pubblici normativa ANAC 2024 2025',
            'sources' => ['anticorruzione.it', 'gazzettaufficiale.it'],
            'notify_roles' => ['procurement_officer', 'pa_admin'],
        ],
    ];

    public function __construct(
        WebSearchService $webSearch,
        ConsentService $consentService,
        AuditLogService $auditService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->webSearch = $webSearch;
        $this->consentService = $consentService;
        $this->auditService = $auditService;
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Check for new normative updates (GDPR-compliant)
     */
    public function checkForUpdates(): array {
        // ULM: Log start
        $this->logger->info('[NormativeMonitoring] Starting scheduled check');

        $updatesFound = 0;
        $topicsWithUpdates = [];
        $notificationsSent = 0;

        try {
            foreach ($this->monitoringTopics as $topicKey => $topic) {
                $updates = $this->checkTopic($topicKey, $topic);

                if (!empty($updates['new_items'])) {
                    $updatesFound += count($updates['new_items']);
                    $topicsWithUpdates[] = [
                        'topic' => $topicKey,
                        'new_items' => $updates['new_items'],
                    ];

                    // GDPR: Send notifications with consent check
                    $sent = $this->notifyUsersGdprCompliant($topicKey, $updates['new_items'], $topic['notify_roles']);
                    $notificationsSent += $sent;
                }
            }

            // ULM: Log success
            $this->logger->info('[NormativeMonitoring] Check completed successfully', [
                'updates_found' => $updatesFound,
                'topics_affected' => count($topicsWithUpdates),
                'notifications_sent' => $notificationsSent,
            ]);

            return [
                'updates_found' => $updatesFound,
                'topics' => $topicsWithUpdates,
                'notifications_sent' => $notificationsSent,
            ];
        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('NORMATIVE_MONITORING_FAILED', [
                'context' => 'scheduled_check',
                'updates_found' => $updatesFound,
            ], $e);

            return [
                'updates_found' => 0,
                'topics' => [],
                'notifications_sent' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check single topic for updates
     */
    protected function checkTopic(string $topicKey, array $topic): array {
        $cacheKey = "normative_monitoring:{$topicKey}";
        $previousResults = Cache::get($cacheKey, []);

        // ULM: Log topic check
        $this->logger->debug('[NormativeMonitoring] Checking topic', ['topic' => $topicKey]);

        $searchResponse = $this->webSearch->search(
            $topic['keywords'],
            'legal',
            10
        );

        if (!$searchResponse['success']) {
            // ULM: Log warning (NON error, perché non è critico)
            $this->logger->warning('[NormativeMonitoring] Search failed for topic', [
                'topic' => $topicKey,
                'error' => $searchResponse['error'] ?? 'unknown',
            ]);
            return ['new_items' => []];
        }

        $currentResults = $searchResponse['results'];
        $newItems = $this->detectNewItems($currentResults, $previousResults);

        // Cache results
        Cache::put($cacheKey, $currentResults, now()->addDay());
        Cache::put("{$cacheKey}:last_check", now()->toIso8601String(), now()->addWeek());

        return [
            'new_items' => $newItems,
            'all_results' => $currentResults,
        ];
    }

    /**
     * Detect new items by comparing URLs
     */
    protected function detectNewItems(array $currentResults, array $previousResults): array {
        if (empty($previousResults)) {
            return []; // First run: no alerts
        }

        $previousUrls = array_column($previousResults, 'url');
        $newItems = [];

        foreach ($currentResults as $result) {
            if (!in_array($result['url'], $previousUrls, true)) {
                $newItems[] = $result;
            }
        }

        return $newItems;
    }

    /**
     * Notify users with GDPR compliance (consent check + audit trail)
     */
    protected function notifyUsersGdprCompliant(string $topicKey, array $newItems, array $roles): int {
        $users = User::role($roles)->get();

        if ($users->isEmpty()) {
            return 0;
        }

        $notificationsSent = 0;

        foreach ($users as $user) {
            try {
                // 1. ULM: Log intent
                $this->logger->info('[NormativeMonitoring] Preparing notification', [
                    'user_id' => $user->id,
                    'topic' => $topicKey,
                    'items_count' => count($newItems),
                ]);

                // 2. GDPR: Check consent
                if (!$this->consentService->hasConsent($user, 'allow-normative-alerts')) {
                    $this->logger->info('[NormativeMonitoring] Notification skipped - no consent', [
                        'user_id' => $user->id,
                        'topic' => $topicKey,
                    ]);
                    continue; // Skip user without consent
                }

                // 3. Send notification
                Notification::send($user, new NormativeUpdateNotification([
                    'topic' => $topicKey,
                    'items' => $newItems,
                    'count' => count($newItems),
                ]));

                // 4. GDPR: Audit trail
                $this->auditService->logUserAction(
                    $user,
                    'normative_alert_sent',
                    [
                        'topic' => $topicKey,
                        'items_count' => count($newItems),
                        'notification_type' => 'normative_update',
                    ],
                    GdprActivityCategory::COMMUNICATION_SENT
                );

                // 5. ULM: Log success
                $this->logger->info('[NormativeMonitoring] Notification sent successfully', [
                    'user_id' => $user->id,
                    'topic' => $topicKey,
                ]);

                $notificationsSent++;
            } catch (\Exception $e) {
                // 6. UEM: Error handling (NON logger->error!)
                $this->errorManager->handle('NORMATIVE_NOTIFICATION_FAILED', [
                    'user_id' => $user->id,
                    'topic' => $topicKey,
                    'items_count' => count($newItems),
                ], $e);
                // Continua con altri utenti
            }
        }

        return $notificationsSent;
    }

    /**
     * Get monitoring status (for dashboard)
     */
    public function getMonitoringStatus(): array {
        $status = [];

        foreach ($this->monitoringTopics as $topicKey => $topic) {
            $cacheKey = "normative_monitoring:{$topicKey}";
            $cached = Cache::get($cacheKey, []);

            $status[$topicKey] = [
                'name' => ucwords(str_replace('_', ' ', $topicKey)),
                'keywords' => $topic['keywords'],
                'sources' => $topic['sources'],
                'last_check' => Cache::get("{$cacheKey}:last_check"),
                'results_count' => count($cached),
                'notify_roles' => $topic['notify_roles'],
            ];
        }

        return $status;
    }
}
