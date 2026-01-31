<?php

namespace App\Services\Commerce;

use App\Models\Egi;
use App\Enums\Commerce\CommercialStatusEnum;
use App\Enums\Commerce\DeliveryPolicyEnum;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EgiListingService
{
    /**
     * Determine if shipping is required for this EGI (Option B).
     *
     * @param Egi $egi
     * @return bool
     */
    public function shippingRequiredForEgi(Egi $egi): bool
    {
        // 1. Explicitly physical
        if ($egi->is_physical) {
            return true;
        }

        // 2. Utility requires fulfillment
        if ($egi->utility && $egi->utility->requires_fulfillment) {
            return true;
        }

        return false;
    }

    /**
     * Validate if EGI can be sold.
     *
     * @param Egi $egi
     * @return bool
     * @throws ValidationException
     */
    public function validateSellable(Egi $egi): bool
    {
        // 1. Collection must be commercial enabled
        if ($egi->collection->commercial_status !== CommercialStatusEnum::COMMERCIAL_ENABLED) {
            throw ValidationException::withMessages(['collection' => 'Collection is not enabled for commerce.']);
        }

        // 2. Delivery Policy Enforcement
        $policy = $egi->collection->delivery_policy;
        
        if ($policy === DeliveryPolicyEnum::DIGITAL_ONLY && $egi->is_physical) {
            throw ValidationException::withMessages(['is_physical' => 'Collection policy is DIGITAL ONLY. Physical items not allowed.']);
        }

        if ($policy === DeliveryPolicyEnum::PHYSICAL_REQUIRED && !$egi->is_physical) {
             // Exception: If utility provides physical aspect, it might be allowed?
             // Spec says: "se collection.delivery_policy == PHYSICAL_REQUIRED e egi.is_physical == false -> blocca"
             throw ValidationException::withMessages(['is_physical' => 'Collection policy is PHYSICAL REQUIRED. Item must be physical.']);
        }

        // 3. Shipping Profile Completeness
        if ($this->shippingRequiredForEgi($egi)) {
            $profile = $egi->shipping_profile;
            if (empty($profile) || empty($profile['weight_g']) || empty($profile['dimensions_mm'])) {
                throw ValidationException::withMessages(['shipping_profile' => 'Shipping profile is incomplete but required.']);
            }
        }

        // 4. Sale Mode Check
        if ($egi->sale_mode === 'not_for_sale' && $egi->is_sellable) {
            // If marked sellable, it must have a valid mode
             throw ValidationException::withMessages(['sale_mode' => 'Sale mode must be Fixed Price or Auction.']);
        }

        return true;
    }

    /**
     * Create/Update Listing (Wizard B).
     *
     * @param Egi $egi
     * @param array $data
     * @return Egi
     */
    public function updateListing(Egi $egi, array $data): Egi
    {
        // Custom validation for Shipping Profile structure
        $shippingRules = [];
        if (($data['is_physical'] ?? false) || ($egi->utility && $egi->utility->requires_fulfillment)) {
            $shippingRules = [
                'shipping_profile' => 'required|array',
                'shipping_profile.weight_g' => 'required|numeric|min:1',
                'shipping_profile.dimensions_mm' => 'required|array',
                'shipping_profile.dimensions_mm.l' => 'required|numeric|min:1',
                'shipping_profile.dimensions_mm.w' => 'required|numeric|min:1',
                'shipping_profile.dimensions_mm.h' => 'required|numeric|min:1',
            ];
        }

        $validator = Validator::make($data, array_merge([
            'sale_mode' => 'required|in:fixed_price,auction,not_for_sale',
            'price' => 'required_if:sale_mode,fixed_price|numeric|min:0',
            'is_physical' => 'boolean',
        ], $shippingRules));

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $updateData = [
            'sale_mode' => $data['sale_mode'],
            'price' => $data['price'] ?? $egi->price,
            'is_physical' => $data['is_physical'] ?? false,
        ];

        if (array_key_exists('shipping_profile', $data)) {
            $updateData['shipping_profile'] = $data['shipping_profile'];
        }

        // Auction fields
        if ($data['sale_mode'] === 'auction') {
            $updateData['auction_minimum_price'] = $data['auction_minimum_price'] ?? null;
            $updateData['auction_start'] = $data['auction_start'] ?? null;
            $updateData['auction_end'] = $data['auction_end'] ?? null;
        }

        $egi->update($updateData);

        // Attempt validation state update (e.g. mark sellable if all good)
        // If validation passes, we can set is_sellable = true
        try {
            if ($this->validateSellable($egi)) {
                $egi->update(['is_sellable' => true]);
            }
        } catch (ValidationException $e) {
            // If not fully valid yet, ensure is_sellable is false
            $egi->update(['is_sellable' => false]);
            // Re-throw or return validation errors if this was a "publish" attempt
        }

        return $egi;
    }
}
