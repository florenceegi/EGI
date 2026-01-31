<?php

namespace App\Services\Commerce;

use App\Models\Collection;
use App\Enums\Commerce\CommercialStatusEnum;
use App\Enums\Commerce\DeliveryPolicyEnum;
use App\Enums\Commerce\ImpactModeEnum;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CollectionCommercialService
{
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
        if (!$collection->impact_mode) {
             throw ValidationException::withMessages(['impact_mode' => 'Impact mode is required.']);
        }

        if ($collection->impact_mode === ImpactModeEnum::EPP && !$collection->epp_project_id) {
            throw ValidationException::withMessages(['epp_project_id' => 'EPP Project is required for EPP impact mode.']);
        }

        if ($collection->impact_mode === ImpactModeEnum::SUBSCRIPTION && !$collection->subscription_plan_id) {
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
        $this->validateSetup($collection);

        $collection->update([
            'commercial_status' => CommercialStatusEnum::COMMERCIAL_ENABLED
        ]);

        return $collection;
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
        $validator = Validator::make($data, [
            'delivery_policy' => ['required', 'string'], // Enum validation handled by model casting or Rule::enum
            'impact_mode' => ['required', 'string'],
            'epp_project_id' => ['required_if:impact_mode,EPP', 'nullable', 'integer'],
            'subscription_plan_id' => ['required_if:impact_mode,SUBSCRIPTION', 'nullable', 'integer'],
        ]);

        if ($validator->fails()) {
             throw new ValidationException($validator);
        }

        $collection->update([
            'delivery_policy' => $data['delivery_policy'],
            'impact_mode' => $data['impact_mode'],
            'commercial_status' => CommercialStatusEnum::CONFIGURED, // Mark as configured, user must explicitly enable
        ]);

        // Optional: Update EPP/Plan IDs if passed, though they might be separate steps
        if (isset($data['epp_project_id'])) {
            $collection->update(['epp_project_id' => $data['epp_project_id']]);
        }
        if (isset($data['subscription_plan_id'])) {
            $collection->update(['subscription_plan_id' => $data['subscription_plan_id']]);
        }

        return $collection;
    }
}
