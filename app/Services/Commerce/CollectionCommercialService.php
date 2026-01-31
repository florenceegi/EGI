<?php

namespace App\Services\Commerce;

use App\Models\Collection;
use App\Enums\Commerce\CommercialStatusEnum;
use App\Enums\Commerce\DeliveryPolicyEnum;
use App\Enums\Commerce\ImpactModeEnum;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class CollectionCommercialService
{
    /**
     * Ultra Log Manager instance
     */
    protected UltraLogManager $logger;

    /**
     * Ultra Error Manager instance
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * Constructor - Dependency Injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }
    /**
     * Validate the collection setup for commerce enablement.
     *
     * @param Collection $collection
     * @return bool
     * @throws ValidationException
     */
    public function validateSetup(Collection $collection): bool
    {
        // 1. Check Delivery Policy
        if (!$collection->delivery_policy) {
            throw ValidationException::withMessages(['delivery_policy' => 'Delivery policy is required.']);
        }

        // 2. Check Impact Mode configuration
        $impactMode = $collection->impact_mode;
        // Normalize to string value for comparison
        $impactVal = $impactMode instanceof ImpactModeEnum ? $impactMode->value : $impactMode;

        if (!$impactVal) {
             throw ValidationException::withMessages(['impact_mode' => 'Impact mode is required.']);
        }

        if ($impactVal === ImpactModeEnum::EPP->value && !$collection->epp_project_id) {
            throw ValidationException::withMessages(['epp_project_id' => 'EPP Project is required for EPP impact mode.']);
        }

        if ($impactVal === ImpactModeEnum::SUBSCRIPTION->value && !$collection->subscription_plan_id) {
            throw ValidationException::withMessages(['subscription_plan_id' => 'Subscription plan is required for Subscription impact mode.']);
        }

        // 3. Check Payment Configuration (Delegated to model logic)
        $paymentMethods = $collection->getEffectivePaymentMethods();
        if ($paymentMethods->isEmpty()) {
            throw ValidationException::withMessages(['payment_methods' => 'At least one payment method must be enabled.']);
        }

        return true;
    }

    /**
     * Enable commercial status for the collection.
     *
     * @param Collection $collection
     * @return Collection
     */
    public function enableCommercial(Collection $collection): Collection
    {
        try {
            $this->logger->info('COMMERCE_ENABLE_START', [
                'collection_id' => $collection->id,
                'current_status' => $collection->commercial_status,
                'user_id' => auth()->id(),
            ]);

            $this->validateSetup($collection);

            $collection->update([
                'commercial_status' => CommercialStatusEnum::COMMERCIAL_ENABLED
            ]);

            $this->logger->info('COMMERCE_ENABLE_SUCCESS', [
                'collection_id' => $collection->id,
                'new_status' => $collection->commercial_status,
            ]);

            return $collection;
        } catch (\Exception $e) {
            // UEM: Handle error via standard ErrorManager
            $this->errorManager->handleException($e, [
                'user_id' => auth()->id(),
                'collection_id' => $collection->id,
                'action' => 'enableCommercial'
            ]);
            throw $e;
        }
    }

    /**
     * Update commercial settings (Wizard implementation).
     *
     * @param Collection $collection
     * @param array $data
     * @return Collection
     */
    public function updateSettings(Collection $collection, array $data): Collection
    {
        try {
            $this->logger->info('COMMERCE_SETTINGS_UPDATE_START', [
                'collection_id' => $collection->id,
                'data_keys' => array_keys($data),
                'user_id' => auth()->id(),
            ]);

            $validator = Validator::make($data, [
                'delivery_policy' => ['required', 'string'], // Enum validation handled by model casting or Rule::enum
                'impact_mode' => ['required', 'string'],
                'epp_project_id' => ['required_if:impact_mode,EPP', 'nullable', 'integer'],
                'subscription_plan_id' => ['required_if:impact_mode,SUBSCRIPTION', 'nullable', 'integer'],
            ]);

            if ($validator->fails()) {
                 throw new ValidationException($validator);
            }

            // Determine status: preserve ENABLED if already enabled, otherwise set to CONFIGURED
            $currentStatus = $collection->commercial_status;
            $newStatus = CommercialStatusEnum::CONFIGURED;

            if ($currentStatus === CommercialStatusEnum::COMMERCIAL_ENABLED || 
                ($currentStatus instanceof CommercialStatusEnum && $currentStatus === CommercialStatusEnum::COMMERCIAL_ENABLED)) {
                $newStatus = CommercialStatusEnum::COMMERCIAL_ENABLED;
            }

            $collection->update([
                'delivery_policy' => $data['delivery_policy'],
                'impact_mode' => $data['impact_mode'],
                'commercial_status' => $newStatus,
            ]);

            // Optional: Update EPP/Plan IDs if passed, though they might be separate steps
            if (isset($data['epp_project_id'])) {
                $collection->update(['epp_project_id' => $data['epp_project_id']]);
            }
            if (isset($data['subscription_plan_id'])) {
                $collection->update(['subscription_plan_id' => $data['subscription_plan_id']]);
            }

            $this->logger->info('COMMERCE_SETTINGS_UPDATE_SUCCESS', [
                'collection_id' => $collection->id,
                'status' => $newStatus,
            ]);

            return $collection;
        } catch (\Exception $e) {
            // UEM: Handle error via standard ErrorManager
            $this->errorManager->handleException($e, [
                'user_id' => auth()->id(),
                'collection_id' => $collection->id,
                'data_keys' => array_keys($data),
                'action' => 'updateSettings'
            ]);
            throw $e;
        }
    }
}
