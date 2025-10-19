<?php

namespace App\Services;

use App\Models\Egi;
use App\Models\EgiBlockchain;
use App\Models\EgiSmartContract;
use App\Models\User;
use App\Enums\EgiType;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 * @purpose Orchestrator for EGI minting - routes to ASA or SmartContract based on egi_type
 */
class EgiMintingOrchestrator
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private EgiMintingService $asaMintingService;
    private EgiSmartContractService $smartContractService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        EgiMintingService $asaMintingService,
        EgiSmartContractService $smartContractService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->asaMintingService = $asaMintingService;
        $this->smartContractService = $smartContractService;
    }

    /**
     * Mint EGI based on its type (ASA or SmartContract)
     * Factory pattern: routes to appropriate service based on egi_type
     *
     * @param Egi $egi EGI model instance
     * @param User $user User requesting mint operation
     * @param array $options Minting options (metadata, trigger_interval, etc.)
     * @return EgiBlockchain|EgiSmartContract
     * @throws \Exception
     */
    public function mint(Egi $egi, User $user, array $options = []): EgiBlockchain|EgiSmartContract
    {
        try {
            $this->logger->info('EGI minting orchestrator called', [
                'egi_id' => $egi->id,
                'egi_type' => $egi->egi_type,
                'user_id' => $user->id,
                'log_category' => 'MINTING_ORCHESTRATOR'
            ]);

            // Determine EGI type (default to ASA for backward compatibility)
            $egiType = EgiType::from($egi->egi_type ?? EgiType::ASA->value);

            // Route to appropriate service
            return match ($egiType) {
                EgiType::ASA => $this->mintAsASA($egi, $user, $options),
                EgiType::SMART_CONTRACT => $this->mintAsSmartContract($egi, $user, $options),
                EgiType::PRE_MINT => throw new \Exception('Pre-Mint EGIs cannot be minted directly. Convert to ASA or SmartContract first.'),
            };
        } catch (\Exception $e) {
            $this->errorManager->handle('EGI_MINTING_ORCHESTRATOR_FAILED', [
                'egi_id' => $egi->id,
                'egi_type' => $egi->egi_type ?? 'unknown',
                'user_id' => $user->id,
                'error_message' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Mint EGI as ASA (classic Algorand Standard Asset)
     *
     * @param Egi $egi
     * @param User $user
     * @param array $options
     * @return EgiBlockchain
     */
    private function mintAsASA(Egi $egi, User $user, array $options): EgiBlockchain
    {
        $this->logger->info('Minting EGI as ASA (classic)', [
            'egi_id' => $egi->id,
            'user_id' => $user->id,
            'log_category' => 'MINT_ASA'
        ]);

        // Ensure egi_type is set to ASA
        if ($egi->egi_type !== EgiType::ASA->value) {
            $egi->update(['egi_type' => EgiType::ASA->value]);
        }

        // Delegate to existing EgiMintingService (backward compatible)
        return $this->asaMintingService->mintEgi($egi, $user, $options);
    }

    /**
     * Mint EGI as SmartContract (living EGI)
     *
     * @param Egi $egi
     * @param User $user
     * @param array $options
     * @return EgiSmartContract
     */
    private function mintAsSmartContract(Egi $egi, User $user, array $options): EgiSmartContract
    {
        $this->logger->info('Minting EGI as SmartContract (living)', [
            'egi_id' => $egi->id,
            'user_id' => $user->id,
            'log_category' => 'MINT_SMART_CONTRACT'
        ]);

        // Check feature flag
        if (!config('egi_living.feature_flags.smart_contract_mint_enabled')) {
            throw new \Exception('SmartContract minting is not yet enabled. Please use ASA minting.');
        }

        // Check if user has active subscription for EGI Vivente
        $this->validateLivingSubscription($egi, $user);

        // Deploy SmartContract
        return $this->smartContractService->deploySmartContract($egi, $user, $options);
    }

    /**
     * Validate user has active Living subscription for this EGI
     *
     * @param Egi $egi
     * @param User $user
     * @throws \Exception
     */
    private function validateLivingSubscription(Egi $egi, User $user): void
    {
        // Check if EGI has living features enabled
        if (!$egi->egi_living_enabled) {
            throw new \Exception('EGI Vivente features are not enabled for this EGI. Please purchase a subscription first.');
        }

        // Check if there's an active subscription
        if (!$egi->egi_living_subscription_id) {
            throw new \Exception('No active Living subscription found for this EGI.');
        }

        // Load subscription and verify it's active
        $subscription = $egi->livingSubscription;
        if (!$subscription || $subscription->status !== 'active') {
            throw new \Exception('Living subscription is not active. Please renew your subscription.');
        }
    }

    /**
     * Get available minting types for an EGI
     *
     * @param Egi $egi
     * @param User $user
     * @return array Available types with eligibility info
     */
    public function getAvailableMintingTypes(Egi $egi, User $user): array
    {
        return [
            'ASA' => [
                'available' => true,
                'name' => 'EGI Classico (ASA)',
                'description' => 'Asset statico su blockchain',
                'price' => 0, // Included in base price
                'is_premium' => false,
            ],
            'SmartContract' => [
                'available' => config('egi_living.feature_flags.smart_contract_mint_enabled'),
                'name' => 'EGI Vivente (SmartContract)',
                'description' => 'Asset intelligente con AI',
                'price' => config('egi_living.subscription_plans.one_time.price_eur'),
                'is_premium' => true,
                'requires_subscription' => true,
            ],
        ];
    }
}
