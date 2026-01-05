<?php

namespace App\Services\Payment;

use App\Enums\Payment\PaymentTypeEnum;
use App\Models\Collection;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletDestination;
use Illuminate\Support\Collection as LaravelCollection;
use Illuminate\Support\Facades\Log;

/**
 * PaymentAvailabilityResolver Service
 *
 * Determines which payment types are available for a given EGI purchase
 * based on collection settings, merchant capabilities, and wallet destinations.
 *
 * @see /docs/architecture/Contratto Operativo (PaymentTypes, Wallet Split, Resolver).md
 */
class PaymentAvailabilityResolver
{
    /**
     * Get all available payment types for a collection
     *
     * @param Collection $collection
     * @return LaravelCollection<PaymentTypeEnum>
     */
    public function getAvailablePaymentTypes(Collection $collection): LaravelCollection
    {
        $available = collect();

        foreach (PaymentTypeEnum::cases() as $paymentType) {
            if ($this->isPaymentTypeAvailable($collection, $paymentType)) {
                $available->push($paymentType);
            }
        }

        Log::debug('PaymentAvailabilityResolver: Available payment types', [
            'collection_id' => $collection->id,
            'available' => $available->map(fn($t) => $t->value)->toArray(),
        ]);

        return $available;
    }

    /**
     * Check if a specific payment type is available for a collection
     *
     * @param Collection $collection
     * @param PaymentTypeEnum $paymentType
     * @return bool
     */
    public function isPaymentTypeAvailable(Collection $collection, PaymentTypeEnum $paymentType): bool
    {
        // 1. Check collection-level settings
        if (!$this->isCollectionPaymentEnabled($collection, $paymentType)) {
            return false;
        }

        // 2. Check merchant capabilities
        if (!$this->hasMerchantCapability($collection, $paymentType)) {
            return false;
        }

        // 3. Check wallet destinations for all stakeholders
        if (!$this->hasValidWalletDestinations($collection, $paymentType)) {
            return false;
        }

        return true;
    }

    /**
     * Check if collection has this payment type enabled
     *
     * @param Collection $collection
     * @param PaymentTypeEnum $paymentType
     * @return bool
     */
    protected function isCollectionPaymentEnabled(Collection $collection, PaymentTypeEnum $paymentType): bool
    {
        // Collection metadata may have explicit settings
        $settings = $collection->payment_settings ?? [];

        // Check for explicit disable
        $disabledTypes = $settings['disabled_payment_types'] ?? [];
        if (in_array($paymentType->value, $disabledTypes, true)) {
            return false;
        }

        // Check for required types (if specified, only those are enabled)
        $enabledTypes = $settings['enabled_payment_types'] ?? null;
        if ($enabledTypes !== null && !in_array($paymentType->value, $enabledTypes, true)) {
            return false;
        }

        return true;
    }

    /**
     * Check if collection owner has merchant capability for payment type
     *
     * @param Collection $collection
     * @param PaymentTypeEnum $paymentType
     * @return bool
     */
    protected function hasMerchantCapability(Collection $collection, PaymentTypeEnum $paymentType): bool
    {
        $owner = $collection->user;
        if (!$owner) {
            return false;
        }

        return match ($paymentType) {
            PaymentTypeEnum::STRIPE => $this->hasStripeCapability($owner, $collection),
            PaymentTypeEnum::PAYPAL => $this->hasPayPalCapability($owner, $collection),
            PaymentTypeEnum::BANK_TRANSFER => true, // Always available if IBAN provided
            PaymentTypeEnum::ALGORAND => true, // Always available if wallet exists
        };
    }

    /**
     * Check if owner has valid Stripe Connect account
     */
    protected function hasStripeCapability(User $owner, Collection $collection): bool
    {
        // Check Creator wallet for Stripe account
        $creatorWallet = $collection->wallets()
            ->where('platform_role', 'Creator')
            ->first();

        if ($creatorWallet && !empty($creatorWallet->stripe_account_id)) {
            return true;
        }

        // Check WalletDestination for Stripe
        if ($creatorWallet) {
            $stripeDestination = WalletDestination::where('wallet_id', $creatorWallet->id)
                ->where('payment_type', PaymentTypeEnum::STRIPE->value)
                ->where('is_verified', true)
                ->first();

            if ($stripeDestination) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if owner has valid PayPal merchant account
     */
    protected function hasPayPalCapability(User $owner, Collection $collection): bool
    {
        $creatorWallet = $collection->wallets()
            ->where('platform_role', 'Creator')
            ->first();

        if ($creatorWallet && !empty($creatorWallet->paypal_merchant_id)) {
            return true;
        }

        // Check WalletDestination for PayPal
        if ($creatorWallet) {
            $paypalDestination = WalletDestination::where('wallet_id', $creatorWallet->id)
                ->where('payment_type', PaymentTypeEnum::PAYPAL->value)
                ->where('is_verified', true)
                ->first();

            if ($paypalDestination) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if all stakeholder wallets have valid destinations for payment type
     *
     * @param Collection $collection
     * @param PaymentTypeEnum $paymentType
     * @return bool
     */
    protected function hasValidWalletDestinations(Collection $collection, PaymentTypeEnum $paymentType): bool
    {
        $wallets = $collection->wallets;

        if ($wallets->isEmpty()) {
            return false;
        }

        foreach ($wallets as $wallet) {
            // Skip wallets with 0% royalty (they don't need destinations)
            if ((float) $wallet->royalty_mint <= 0 && (float) $wallet->royalty_rebind <= 0) {
                continue;
            }

            if (!$this->walletHasValidDestination($wallet, $paymentType)) {
                Log::debug('PaymentAvailabilityResolver: Wallet missing destination', [
                    'wallet_id' => $wallet->id,
                    'platform_role' => $wallet->platform_role,
                    'payment_type' => $paymentType->value,
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a specific wallet has a valid destination for payment type
     *
     * @param Wallet $wallet
     * @param PaymentTypeEnum $paymentType
     * @return bool
     */
    public function walletHasValidDestination(Wallet $wallet, PaymentTypeEnum $paymentType): bool
    {
        // First check legacy fields on Wallet model
        $legacyValue = $this->getLegacyDestination($wallet, $paymentType);
        if (!empty($legacyValue)) {
            return $paymentType->validateDestination($legacyValue);
        }

        // Then check WalletDestination table
        $destination = WalletDestination::where('wallet_id', $wallet->id)
            ->where('payment_type', $paymentType->value)
            ->first();

        if (!$destination) {
            return false;
        }

        return $destination->isValidForPayment();
    }

    /**
     * Get destination from legacy Wallet fields
     */
    protected function getLegacyDestination(Wallet $wallet, PaymentTypeEnum $paymentType): ?string
    {
        return match ($paymentType) {
            PaymentTypeEnum::STRIPE => $wallet->stripe_account_id,
            PaymentTypeEnum::PAYPAL => $wallet->paypal_merchant_id,
            PaymentTypeEnum::BANK_TRANSFER => $wallet->iban_encrypted,
            PaymentTypeEnum::ALGORAND => $wallet->wallet,
        };
    }

    /**
     * Get the destination value for a wallet and payment type
     *
     * @param Wallet $wallet
     * @param PaymentTypeEnum $paymentType
     * @return string|null
     */
    public function getDestinationForWallet(Wallet $wallet, PaymentTypeEnum $paymentType): ?string
    {
        // First try WalletDestination table
        $destination = WalletDestination::where('wallet_id', $wallet->id)
            ->where('payment_type', $paymentType->value)
            ->first();

        if ($destination) {
            return $destination->decrypted_value;
        }

        // Fallback to legacy fields
        return $this->getLegacyDestination($wallet, $paymentType);
    }

    /**
     * Get formatted payment options for UI display
     *
     * @param Collection $collection
     * @return array
     */
    public function getPaymentOptionsForUI(Collection $collection): array
    {
        $available = $this->getAvailablePaymentTypes($collection);

        return $available->map(function (PaymentTypeEnum $type) {
            return [
                'value' => $type->value,
                'label' => $type->label(),
                'icon' => $type->icon(),
                'is_fiat' => $type->isFiat(),
                'is_crypto' => $type->isCrypto(),
            ];
        })->toArray();
    }

    /**
     * Validate that a payment can proceed with given type
     *
     * @param Collection $collection
     * @param PaymentTypeEnum $paymentType
     * @return array{valid: bool, errors: array}
     */
    public function validatePaymentPreconditions(Collection $collection, PaymentTypeEnum $paymentType): array
    {
        $errors = [];

        if (!$this->isCollectionPaymentEnabled($collection, $paymentType)) {
            $errors[] = "Payment type {$paymentType->label()} is not enabled for this collection";
        }

        if (!$this->hasMerchantCapability($collection, $paymentType)) {
            $errors[] = "Collection owner does not have {$paymentType->label()} merchant capability";
        }

        if (!$this->hasValidWalletDestinations($collection, $paymentType)) {
            $errors[] = "Not all stakeholders have valid {$paymentType->label()} destinations";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
