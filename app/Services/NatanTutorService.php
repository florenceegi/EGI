<?php

namespace App\Services;

use App\Models\User;
use App\Models\Egi;
use App\Models\Collection;
use App\Models\Reservation;
use App\Services\EgiliService;
use App\Services\MintService;
use App\Services\ReservationService;
use Illuminate\Support\Facades\DB;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * 📜 Oracode Service: NatanTutorService
 *
 * Natan Tutor operational assistant - executes platform actions
 * on behalf of users while consuming Egili credits.
 *
 * Two Modes:
 * - TUTORING: Step-by-step guidance with explanations (higher cost)
 * - EXPERT: Direct action execution shortcuts (lower cost)
 *
 * @package     App\Services
 * @author      Padmin D. Curtis (for Fabio Cherici)
 * @copyright   2025 Fabio Cherici
 * @license     MIT
 * @version     1.0.0
 *
 * @purpose     🎯 Executes platform operations with Egili consumption
 * @context     🧩 Works with EgiliService, MintService, ReservationService
 *
 * @feature     🗝️ Navigation assistance (explain pages, guide to features)
 * @feature     🗝️ Mint assistance (guide through mint process)
 * @feature     🗝️ Reservation assistance (guide through booking)
 * @feature     🗝️ Purchase assistance (guide through Egili/feature purchase)
 * @feature     🗝️ Expert shortcuts (execute actions directly)
 */
class NatanTutorService {
    /**
     * Action categories with Egili costs
     */
    protected const ACTION_CATEGORIES = [
        'basic' => ['navigate', 'explain'],
        'advanced' => ['search', 'filter', 'recommendation'],
        'premium' => ['mint', 'reserve', 'purchase', 'collection_create'],
    ];

    /**
     * @var EgiliService
     */
    protected EgiliService $egiliService;

    /**
     * @var ErrorManagerInterface
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Constructor
     */
    public function __construct(
        EgiliService $egiliService,
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger
    ) {
        $this->egiliService = $egiliService;
        $this->errorManager = $errorManager;
        $this->logger = $logger;
    }

    // ====================================
    // === EGILI COST MANAGEMENT ===
    // ====================================

    /**
     * Get cost for an action
     *
     * @param string $action Action identifier (navigate, explain, mint, etc.)
     * @param string $mode 'tutoring' or 'expert'
     * @return int Cost in Egili
     */
    public function getActionCost(string $action, string $mode = 'tutoring'): int {
        $baseCost = config("natan-tutor.actions.{$action}.cost", 5);

        // Expert mode has a multiplier (usually 0.6-0.8)
        if ($mode === 'expert') {
            $multiplier = config('natan-tutor.expert_mode.cost_multiplier', 0.7);
            return (int) ceil($baseCost * $multiplier);
        }

        return $baseCost;
    }

    /**
     * Check if user can afford an action
     *
     * @param User $user
     * @param string $action
     * @param string $mode
     * @return bool
     */
    public function canAffordAction(User $user, string $action, string $mode = 'tutoring'): bool {
        $cost = $this->getActionCost($action, $mode);
        return $this->egiliService->canSpend($user, $cost);
    }

    /**
     * Consume Egili for an action
     *
     * @param User $user
     * @param string $action
     * @param string $mode
     * @param array|null $metadata Additional context
     * @return bool Success
     */
    protected function consumeEgiliForAction(
        User $user,
        string $action,
        string $mode = 'tutoring',
        ?array $metadata = null
    ): bool {
        $cost = $this->getActionCost($action, $mode);

        try {
            $this->egiliService->spend(
                $user,
                $cost,
                "Natan Tutor: {$action}",
                'natan_tutor',
                array_merge($metadata ?? [], [
                    'action' => $action,
                    'mode' => $mode,
                    'timestamp' => now()->toIso8601String(),
                ])
            );

            $this->logger->info('Natan Tutor action charged', [
                'user_id' => $user->id,
                'action' => $action,
                'mode' => $mode,
                'cost' => $cost,
                'log_category' => 'NATAN_TUTOR_CHARGE'
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->warning('Natan Tutor action charge failed', [
                'user_id' => $user->id,
                'action' => $action,
                'error' => $e->getMessage(),
                'log_category' => 'NATAN_TUTOR_CHARGE_FAILED'
            ]);

            return false;
        }
    }

    // ====================================
    // === NAVIGATION ASSISTANCE ===
    // ====================================

    /**
     * Navigate to a specific page/feature
     *
     * @param User $user
     * @param string $destination Page identifier or route
     * @param string $mode
     * @return array Response with navigation info
     */
    public function navigateTo(User $user, string $destination, string $mode = 'tutoring'): array {
        if (!$this->consumeEgiliForAction($user, 'navigate', $mode, ['destination' => $destination])) {
            return [
                'success' => false,
                'error' => 'insufficient_egili',
                'message' => __('natan-tutor.errors.insufficient_egili'),
                'cost' => $this->getActionCost('navigate', $mode),
            ];
        }

        // Get navigation route
        $route = $this->getRouteForDestination($destination);

        $response = [
            'success' => true,
            'action' => 'navigate',
            'destination' => $destination,
            'route' => $route,
            'cost_charged' => $this->getActionCost('navigate', $mode),
        ];

        // In tutoring mode, add explanation
        if ($mode === 'tutoring') {
            $response['explanation'] = $this->getNavigationExplanation($destination);
            $response['related_features'] = $this->getRelatedFeatures($destination);
        }

        return $response;
    }

    /**
     * Explain a feature or page
     *
     * @param User $user
     * @param string $feature Feature identifier
     * @param string $mode
     * @return array Response with explanation
     */
    public function explainFeature(User $user, string $feature, string $mode = 'tutoring'): array {
        if (!$this->consumeEgiliForAction($user, 'explain', $mode, ['feature' => $feature])) {
            return [
                'success' => false,
                'error' => 'insufficient_egili',
                'message' => __('natan-tutor.errors.insufficient_egili'),
            ];
        }

        return [
            'success' => true,
            'action' => 'explain',
            'feature' => $feature,
            'explanation' => $this->getFeatureExplanation($feature),
            'tips' => $this->getFeatureTips($feature),
            'next_steps' => $this->getSuggestedNextSteps($feature),
            'cost_charged' => $this->getActionCost('explain', $mode),
        ];
    }

    // ====================================
    // === MINT ASSISTANCE ===
    // ====================================

    /**
     * Guide user through mint process (tutoring mode)
     *
     * @param User $user
     * @param array $mintData Preliminary mint data
     * @param string $mode
     * @return array Response with guidance
     */
    public function assistMint(User $user, array $mintData, string $mode = 'tutoring'): array {
        if (!$this->consumeEgiliForAction($user, 'mint', $mode, ['mint_data' => $mintData])) {
            return [
                'success' => false,
                'error' => 'insufficient_egili',
                'message' => __('natan-tutor.errors.insufficient_egili'),
            ];
        }

        // Validate mint data
        $validation = $this->validateMintData($mintData);

        $response = [
            'success' => true,
            'action' => 'mint_assist',
            'validation' => $validation,
            'cost_charged' => $this->getActionCost('mint', $mode),
        ];

        if ($mode === 'tutoring') {
            $response['steps'] = $this->getMintSteps();
            $response['current_step'] = $this->determineMintStep($mintData);
            $response['tips'] = $this->getMintTips($mintData);
            $response['estimated_cost'] = $this->estimateMintCost($mintData);
        }

        // Expert mode: prepare mint data
        if ($mode === 'expert') {
            $response['ready_to_mint'] = $validation['valid'];
            $response['mint_payload'] = $validation['valid'] ? $this->prepareMintPayload($mintData) : null;
        }

        return $response;
    }

    // ====================================
    // === RESERVATION ASSISTANCE ===
    // ====================================

    /**
     * Guide user through reservation process
     *
     * @param User $user
     * @param int $egiId EGI to reserve
     * @param string $mode
     * @return array Response with guidance
     */
    public function assistReservation(User $user, int $egiId, string $mode = 'tutoring'): array {
        if (!$this->consumeEgiliForAction($user, 'reserve', $mode, ['egi_id' => $egiId])) {
            return [
                'success' => false,
                'error' => 'insufficient_egili',
                'message' => __('natan-tutor.errors.insufficient_egili'),
            ];
        }

        // Load EGI
        $egi = Egi::with(['collection', 'owner'])->find($egiId);

        if (!$egi) {
            return [
                'success' => false,
                'error' => 'egi_not_found',
                'message' => __('natan-tutor.errors.egi_not_found'),
            ];
        }

        $response = [
            'success' => true,
            'action' => 'reserve_assist',
            'egi' => [
                'id' => $egi->id,
                'title' => $egi->title,
                'price' => $egi->price,
                'available' => $egi->isAvailableForReservation(),
            ],
            'cost_charged' => $this->getActionCost('reserve', $mode),
        ];

        if ($mode === 'tutoring') {
            $response['steps'] = $this->getReservationSteps();
            $response['explanation'] = $this->getReservationExplanation($egi);
            $response['what_happens_next'] = __('natan-tutor.reservation.what_happens_next');
        }

        if ($mode === 'expert') {
            $response['reservation_url'] = route('reservations.create', ['egi' => $egiId]);
            $response['pre_filled'] = true;
        }

        return $response;
    }

    // ====================================
    // === PURCHASE ASSISTANCE ===
    // ====================================

    /**
     * Guide user through AI Package purchase (which credits Egili)
     *
     * ToS v3.0.0: il prodotto acquistato è il Pacchetto Servizi AI, non gli Egili.
     *
     * @param User $user
     * @param int $amount Egili quantity guidance (derived from package selection)
     * @param string $mode
     * @return array Response with guidance
     */
    public function assistAiPackagePurchase(User $user, int $amount, string $mode = 'tutoring'): array {
        if (!$this->consumeEgiliForAction($user, 'purchase', $mode, ['amount' => $amount])) {
            return [
                'success' => false,
                'error' => 'insufficient_egili',
                'message' => __('natan-tutor.errors.insufficient_egili'),
            ];
        }

        $packages = $this->getEgiliPackages();
        $recommendedPackage = $this->recommendPackage($amount, $packages);

        $response = [
            'success' => true,
            'action' => 'purchase_assist',
            'packages' => $packages,
            'recommended' => $recommendedPackage,
            'current_balance' => $this->egiliService->getBalance($user),
            'cost_charged' => $this->getActionCost('purchase', $mode),
        ];

        if ($mode === 'tutoring') {
            $response['explanation'] = __('natan-tutor.purchase.egili_explanation');
            $response['value_proposition'] = __('natan-tutor.purchase.value_proposition');
        }

        if ($mode === 'expert') {
            // B6: route 'egili.purchase.pricing' — pagina selezione pacchetto AI
            $response['purchase_url'] = route('egili.purchase.pricing');
        }

        return $response;
    }

    // ====================================
    // === COLLECTION ASSISTANCE ===
    // ====================================

    /**
     * Guide user through collection creation
     *
     * @param User $user
     * @param array $collectionData
     * @param string $mode
     * @return array Response with guidance
     */
    public function assistCollectionCreate(User $user, array $collectionData, string $mode = 'tutoring'): array {
        if (!$this->consumeEgiliForAction($user, 'collection_create', $mode, ['collection_data' => $collectionData])) {
            return [
                'success' => false,
                'error' => 'insufficient_egili',
                'message' => __('natan-tutor.errors.insufficient_egili'),
            ];
        }

        $validation = $this->validateCollectionData($collectionData);

        $response = [
            'success' => true,
            'action' => 'collection_create_assist',
            'validation' => $validation,
            'cost_charged' => $this->getActionCost('collection_create', $mode),
        ];

        if ($mode === 'tutoring') {
            $response['steps'] = $this->getCollectionCreationSteps();
            $response['tips'] = $this->getCollectionTips();
            $response['naming_suggestions'] = $this->suggestCollectionNames($collectionData);
        }

        if ($mode === 'expert' && $validation['valid']) {
            $response['create_url'] = route('collections.create');
            $response['pre_filled_data'] = $collectionData;
        }

        return $response;
    }

    // ====================================
    // === USER STATE / RECOMMENDATIONS ===
    // ====================================

    /**
     * Get current user state and recommendations
     *
     * @param User $user
     * @return array User state and suggestions
     */
    public function getUserState(User $user): array {
        $egiliBalance = $this->egiliService->getBalance($user);
        $balanceBreakdown = $this->egiliService->getBalanceBreakdown($user);

        return [
            'user_id' => $user->id,
            'egili_balance' => $egiliBalance,
            'balance_breakdown' => $balanceBreakdown,
            'available_actions' => $this->getAvailableActions($user, $egiliBalance),
            'recommended_actions' => $this->getRecommendedActions($user),
            'tutoring_mode_available' => config('natan-tutor.tutoring_mode.enabled', true),
            'expert_mode_available' => config('natan-tutor.expert_mode.enabled', true),
        ];
    }

    /**
     * Get actions user can afford
     *
     * @param User $user
     * @param int $balance Current balance
     * @return array Available actions with costs
     */
    protected function getAvailableActions(User $user, int $balance): array {
        $actions = config('natan-tutor.actions', []);
        $available = [];

        foreach ($actions as $action => $config) {
            $tutoringCost = $config['cost'] ?? 5;
            $expertCost = (int) ceil($tutoringCost * config('natan-tutor.expert_mode.cost_multiplier', 0.7));

            $available[$action] = [
                'name' => $config['name'] ?? $action,
                'description' => $config['description'] ?? '',
                'tutoring_cost' => $tutoringCost,
                'expert_cost' => $expertCost,
                'can_afford_tutoring' => $balance >= $tutoringCost,
                'can_afford_expert' => $balance >= $expertCost,
            ];
        }

        return $available;
    }

    /**
     * Get personalized recommendations based on user activity
     *
     * @param User $user
     * @return array Recommended actions
     */
    protected function getRecommendedActions(User $user): array {
        $recommendations = [];

        // Check if user has any EGIs (through owned collections)
        $egiCount = \App\Models\Egi::whereIn('collection_id', $user->ownedCollections()->pluck('id'))->count();
        if ($egiCount === 0) {
            $recommendations[] = [
                'action' => 'mint',
                'reason' => __('natan-tutor.recommendations.no_egis'),
                'priority' => 'high',
            ];
        }

        // Check if user has explored collections
        $collectionsViewed = $user->collectionViews ?? 0;
        if ($collectionsViewed < 5) {
            $recommendations[] = [
                'action' => 'navigate',
                'destination' => 'explore',
                'reason' => __('natan-tutor.recommendations.explore_collections'),
                'priority' => 'medium',
            ];
        }

        // Check balance status
        $balance = $this->egiliService->getBalance($user);
        if ($balance < 50) {
            $recommendations[] = [
                'action' => 'purchase',
                'reason' => __('natan-tutor.recommendations.low_balance'),
                'priority' => 'low',
            ];
        }

        return $recommendations;
    }

    // ====================================
    // === HELPER METHODS ===
    // ====================================

    protected function getRouteForDestination(string $destination): ?string {
        $routes = [
            'home' => 'welcome',
            'dashboard' => 'dashboard',
            'collections' => 'collections.index',
            'explore' => 'explore.index',
            'mint' => 'egis.create',
            'profile' => 'profile.show',
            'wallet' => 'wallet.show',
            'egili' => 'egili.index',
            'settings' => 'settings.index',
        ];

        return $routes[$destination] ?? null;
    }

    protected function getNavigationExplanation(string $destination): string {
        return __("natan-tutor.navigation.{$destination}.explanation", [], __("natan-tutor.navigation.default_explanation"));
    }

    protected function getRelatedFeatures(string $destination): array {
        $related = config("natan-tutor.navigation.{$destination}.related", []);
        return $related;
    }

    protected function getFeatureExplanation(string $feature): string {
        return __("natan-tutor.features.{$feature}.explanation", [], __("natan-tutor.features.default_explanation"));
    }

    protected function getFeatureTips(string $feature): array {
        return __("natan-tutor.features.{$feature}.tips", [], []);
    }

    protected function getSuggestedNextSteps(string $feature): array {
        return config("natan-tutor.features.{$feature}.next_steps", []);
    }

    protected function validateMintData(array $mintData): array {
        $errors = [];

        if (empty($mintData['title'])) {
            $errors['title'] = __('validation.required', ['attribute' => 'title']);
        }

        if (empty($mintData['description'])) {
            $errors['description'] = __('validation.required', ['attribute' => 'description']);
        }

        if (empty($mintData['media'])) {
            $errors['media'] = __('validation.required', ['attribute' => 'media']);
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    protected function getMintSteps(): array {
        return [
            ['step' => 1, 'title' => __('natan-tutor.mint.step1_title'), 'description' => __('natan-tutor.mint.step1_desc')],
            ['step' => 2, 'title' => __('natan-tutor.mint.step2_title'), 'description' => __('natan-tutor.mint.step2_desc')],
            ['step' => 3, 'title' => __('natan-tutor.mint.step3_title'), 'description' => __('natan-tutor.mint.step3_desc')],
            ['step' => 4, 'title' => __('natan-tutor.mint.step4_title'), 'description' => __('natan-tutor.mint.step4_desc')],
        ];
    }

    protected function determineMintStep(array $mintData): int {
        if (empty($mintData['title'])) return 1;
        if (empty($mintData['media'])) return 2;
        if (empty($mintData['description'])) return 3;
        return 4;
    }

    protected function getMintTips(array $mintData): array {
        return [
            __('natan-tutor.mint.tip_title'),
            __('natan-tutor.mint.tip_media'),
            __('natan-tutor.mint.tip_description'),
        ];
    }

    protected function estimateMintCost(array $mintData): array {
        return [
            'gas_fee' => 0.001, // Algorand transaction fee
            'platform_fee' => config('egili.mint_fee', 10),
            'currency' => 'ALGO',
        ];
    }

    protected function prepareMintPayload(array $mintData): array {
        return [
            'title' => $mintData['title'] ?? '',
            'description' => $mintData['description'] ?? '',
            'media_url' => $mintData['media'] ?? '',
            'attributes' => $mintData['attributes'] ?? [],
        ];
    }

    protected function getReservationSteps(): array {
        return [
            ['step' => 1, 'title' => __('natan-tutor.reserve.step1_title'), 'description' => __('natan-tutor.reserve.step1_desc')],
            ['step' => 2, 'title' => __('natan-tutor.reserve.step2_title'), 'description' => __('natan-tutor.reserve.step2_desc')],
            ['step' => 3, 'title' => __('natan-tutor.reserve.step3_title'), 'description' => __('natan-tutor.reserve.step3_desc')],
        ];
    }

    protected function getReservationExplanation(Egi $egi): string {
        return __('natan-tutor.reserve.explanation', [
            'title' => $egi->title,
            'price' => $egi->price,
        ]);
    }

    protected function getEgiliPackages(): array {
        return config('egili.packages', [
            ['id' => 'starter', 'amount' => 5000, 'price_eur' => 50, 'bonus' => 0],
            ['id' => 'plus', 'amount' => 10000, 'price_eur' => 95, 'bonus' => 500],
            ['id' => 'pro', 'amount' => 25000, 'price_eur' => 225, 'bonus' => 2500],
        ]);
    }

    protected function recommendPackage(int $desiredAmount, array $packages): ?array {
        foreach ($packages as $package) {
            if ($package['amount'] >= $desiredAmount) {
                return $package;
            }
        }
        return end($packages) ?: null;
    }

    protected function validateCollectionData(array $data): array {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = __('validation.required', ['attribute' => 'name']);
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    protected function getCollectionCreationSteps(): array {
        return [
            ['step' => 1, 'title' => __('natan-tutor.collection.step1_title'), 'description' => __('natan-tutor.collection.step1_desc')],
            ['step' => 2, 'title' => __('natan-tutor.collection.step2_title'), 'description' => __('natan-tutor.collection.step2_desc')],
            ['step' => 3, 'title' => __('natan-tutor.collection.step3_title'), 'description' => __('natan-tutor.collection.step3_desc')],
        ];
    }

    protected function getCollectionTips(): array {
        return [
            __('natan-tutor.collection.tip_name'),
            __('natan-tutor.collection.tip_description'),
            __('natan-tutor.collection.tip_cover'),
        ];
    }

    protected function suggestCollectionNames(array $data): array {
        // In future, could use AI to generate suggestions
        return [
            __('natan-tutor.collection.suggestion_personal'),
            __('natan-tutor.collection.suggestion_art'),
            __('natan-tutor.collection.suggestion_memories'),
        ];
    }
}
