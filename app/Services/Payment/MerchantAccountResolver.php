<?php

namespace App\Services\Payment;

use App\Exceptions\Payment\MerchantAccountNotConfiguredException;
use App\Models\Collection;
use App\Models\Egi;
use App\Models\User;
use App\Models\Wallet;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: Merchant Account Resolver
 * 🎯 Purpose: Retrieve PSP account identifiers for creators without platform custody
 * 🛡️ MiCA-SAFE: Ensures settlements occur on creator-owned PSP accounts
 */
class MerchantAccountResolver {
    public function __construct(
        private readonly UltraLogManager $logger,
        private readonly ErrorManagerInterface $errorManager
    ) {
    }

    /**
     * Resolve merchant PSP account configuration for a given EGI and provider.
     *
     * @param Egi $egi
     * @param string $provider Provider identifier (stripe|paypal)
     * @return array{
     *     provider: string,
     *     collection_id: int,
     *     wallet_id: int,
     *     stripe_account_id?: string,
     *     paypal_merchant_id?: string
     * }
     *
     * @throws MerchantAccountNotConfiguredException
     */
    public function resolveForEgiAndProvider(Egi $egi, string $provider): array {
        $provider = strtolower($provider);
        $collection = $egi->relationLoaded('collection')
            ? $egi->collection
            : $egi->collection()->with('wallets')->first();

        if (!$collection instanceof Collection) {
            throw new MerchantAccountNotConfiguredException($provider, egiId: $egi->id);
        }

        $wallets = $this->collectCandidateWallets($collection);

        $wallet = match ($provider) {
            'stripe' => $wallets->first(function (Wallet $wallet) {
                // Check WalletDestination first (New Architecture)
                $hasDestination = $wallet->destinations->contains(function ($destination) {
                    return $destination->payment_type === \App\Enums\Payment\PaymentTypeEnum::STRIPE->value 
                        && filled($destination->destination_value);
                });
                if ($hasDestination) return true;

                // Fallback (Legacy)
                return filled($wallet->user?->stripe_account_id) || filled($wallet->stripe_account_id);
            }),
            'paypal' => $wallets->first(function (Wallet $wallet) {
                // Check WalletDestination first
                $hasDestination = $wallet->destinations->contains(function ($destination) {
                    return $destination->payment_type === \App\Enums\Payment\PaymentTypeEnum::PAYPAL->value 
                        && filled($destination->destination_value);
                });
                if ($hasDestination) return true;

                return filled($wallet->paypal_merchant_id);
            }),
            default => null,
        };

        if (!$wallet) {
            $this->logger->warning('Merchant PSP configuration missing', [
                'provider' => $provider,
                'collection_id' => $collection->id,
                'egi_id' => $egi->id,
            ]);

            $this->errorManager->handle('MINT_PAYMENT_PROVIDER_UNAVAILABLE', [
                'provider' => $provider,
                'collection_id' => $collection->id,
                'egi_id' => $egi->id,
                'reason' => 'merchant_account_missing',
            ]);

            throw new MerchantAccountNotConfiguredException(
                provider: $provider,
                collectionId: $collection->id,
                egiId: $egi->id
            );
        }

        // Resolve Account ID prioritizing WalletDestination
        $accountId = null;
        if ($provider === 'stripe') {
             $dest = $wallet->destinations->firstWhere('payment_type', \App\Enums\Payment\PaymentTypeEnum::STRIPE->value);
             $accountId = $dest?->destination_value 
                ?? $wallet->user?->stripe_account_id 
                ?? $wallet->stripe_account_id;
        } elseif ($provider === 'paypal') {
             $dest = $wallet->destinations->firstWhere('payment_type', \App\Enums\Payment\PaymentTypeEnum::PAYPAL->value);
             $accountId = $dest?->destination_value 
                ?? $wallet->paypal_merchant_id;
        }

        return array_filter([
            'provider' => $provider,
            'collection_id' => $collection->id,
            'wallet_id' => $wallet->id,
            'stripe_account_id' => $provider === 'stripe' ? $accountId : null,
            'paypal_merchant_id' => $provider === 'paypal' ? $accountId : null,
        ], static fn($value) => $value !== null && $value !== '');
    }

    /**
     * Resolve merchant PSP account configuration for a given user and provider.
     *
     * @param User $user
     * @param string $provider Provider identifier (stripe|paypal)
     * @return array{
     *     provider: string,
     *     wallet_id: int,
     *     stripe_account_id?: string,
     *     paypal_merchant_id?: string
     * }
     *
     * @throws MerchantAccountNotConfiguredException
     */
    public function resolveForUserAndProvider(User $user, string $provider): array {
        $provider = strtolower($provider);

        $wallets = $this->collectUserWallets($user);

        $wallet = match ($provider) {
            'stripe' => $wallets->first(function (Wallet $wallet) use ($user) {
                $hasDestination = $wallet->destinations->contains(function ($destination) {
                    return $destination->payment_type === \App\Enums\Payment\PaymentTypeEnum::STRIPE->value
                        && filled($destination->destination_value);
                });
                if ($hasDestination) return true;

                return filled($user->stripe_account_id) || filled($wallet->stripe_account_id);
            }),
            'paypal' => $wallets->first(function (Wallet $wallet) {
                $hasDestination = $wallet->destinations->contains(function ($destination) {
                    return $destination->payment_type === \App\Enums\Payment\PaymentTypeEnum::PAYPAL->value
                        && filled($destination->destination_value);
                });
                if ($hasDestination) return true;

                return filled($wallet->paypal_merchant_id);
            }),
            default => null,
        };

        if (!$wallet) {
            $this->logger->warning('Merchant PSP configuration missing (user)', [
                'provider' => $provider,
                'user_id' => $user->id,
            ]);

            $this->errorManager->handle('MINT_PAYMENT_PROVIDER_UNAVAILABLE', [
                'provider' => $provider,
                'user_id' => $user->id,
                'reason' => 'merchant_account_missing',
            ]);

            throw new MerchantAccountNotConfiguredException(provider: $provider);
        }

        $accountId = null;
        if ($provider === 'stripe') {
            $dest = $wallet->destinations->firstWhere('payment_type', \App\Enums\Payment\PaymentTypeEnum::STRIPE->value);
            $accountId = $dest?->destination_value
                ?? $user->stripe_account_id
                ?? $wallet->stripe_account_id;
        } elseif ($provider === 'paypal') {
            $dest = $wallet->destinations->firstWhere('payment_type', \App\Enums\Payment\PaymentTypeEnum::PAYPAL->value);
            $accountId = $dest?->destination_value
                ?? $wallet->paypal_merchant_id;
        }

        return array_filter([
            'provider' => $provider,
            'wallet_id' => $wallet->id,
            'stripe_account_id' => $provider === 'stripe' ? $accountId : null,
            'paypal_merchant_id' => $provider === 'paypal' ? $accountId : null,
        ], static fn($value) => $value !== null && $value !== '');
    }

    /**
     * Validate ALL collection wallets for payment acceptance (MULTI-WALLET SPLIT PAYMENT)
     *
     * This method validates EVERY wallet in the collection to ensure split payments will succeed.
     * Returns true ONLY if ALL wallets with PSP accounts are valid and enabled.
     *
     * @param Egi $egi
     * @param string $provider Provider identifier (stripe|paypal)
     * @return array{
     *     provider: string,
     *     all_valid: bool,
     *     can_accept_payments: bool,
     *     total_wallets: int,
     *     valid_wallets: int,
     *     invalid_wallets: array,
     *     provider_enabled: bool,
     *     errors: array
     * }
     */
    public function validateAllCollectionWallets(Egi $egi, string $provider): array {
        $provider = strtolower($provider);
        $collection = $egi->relationLoaded('collection')
            ? $egi->collection
            : $egi->collection()->with('wallets')->first();

        if (!$collection instanceof Collection) {
            return [
                'provider' => $provider,
                'all_valid' => false,
                'can_accept_payments' => false,
                'total_wallets' => 0,
                'valid_wallets' => 0,
                'invalid_wallets' => [],
                'provider_enabled' => false,
                'errors' => ['collection_not_found'],
            ];
        }

        // Check if provider is enabled in .env
        $providerEnabled = match ($provider) {
            'stripe' => (bool) config('algorand.payments.stripe_enabled', false),
            'paypal' => (bool) config('algorand.payments.paypal_enabled', false),
            default => false,
        };

        if (!$providerEnabled) {
            $this->logger->info('Provider not enabled in configuration', [
                'provider' => $provider,
                'collection_id' => $collection->id,
            ]);

            return [
                'provider' => $provider,
                'all_valid' => false,
                'can_accept_payments' => false,
                'total_wallets' => 0,
                'valid_wallets' => 0,
                'invalid_wallets' => [],
                'provider_enabled' => false,
                'errors' => ['provider_disabled'],
            ];
        }

        $wallets = $this->collectCandidateWallets($collection);

        // Filter wallets configured for this provider via WalletDestination
        $providerWallets = $wallets->filter(function (Wallet $wallet) use ($provider) {
             $paymentType = match ($provider) {
                'stripe' => \App\Enums\Payment\PaymentTypeEnum::STRIPE,
                'paypal' => \App\Enums\Payment\PaymentTypeEnum::PAYPAL,
                default => null,
            };

            if (!$paymentType) return false;

            return $wallet->destinations->contains(function ($destination) use ($paymentType) {
                return $destination->payment_type === $paymentType->value && !empty($destination->destination_value);
            });
        });

        if ($providerWallets->isEmpty()) {
            $this->logger->warning('No wallets configured for provider', [
                'provider' => $provider,
                'collection_id' => $collection->id,
                'egi_id' => $egi->id,
            ]);

            return [
                'provider' => $provider,
                'all_valid' => false,
                'can_accept_payments' => false,
                'total_wallets' => $providerWallets->count(),
                'valid_wallets' => 0,
                'invalid_wallets' => [],
                'provider_enabled' => $providerEnabled,
                'errors' => ['no_wallets_configured'],
            ];
        }

        // Validate EACH wallet with provider API
        $validWallets = [];
        $invalidWallets = [];
        $errors = [];

        foreach ($providerWallets as $wallet) {
            $validation = $this->validateSingleWallet($wallet, $provider);

            if ($validation['valid']) {
                $validWallets[] = [
                    'wallet_id' => $wallet->id,
                    'platform_role' => $wallet->platform_role,
                    'account_id' => $validation['account_id'],
                ];
            } else {
                $invalidWallets[] = [
                    'wallet_id' => $wallet->id,
                    'platform_role' => $wallet->platform_role,
                    'account_id' => $validation['account_id'],
                    'error' => $validation['error'],
                ];
                $errors[] = $validation['error'];
            }
        }

        $allValid = empty($invalidWallets);

        $this->logger->info('Collection wallets validation completed', [
            'provider' => $provider,
            'collection_id' => $collection->id,
            'egi_id' => $egi->id,
            'total_wallets' => $providerWallets->count(),
            'valid_wallets' => count($validWallets),
            'invalid_wallets' => count($invalidWallets),
            'all_valid' => $allValid,
        ]);

        return [
            'provider' => $provider,
            'all_valid' => $allValid,
            'can_accept_payments' => $allValid && $providerEnabled,
            'total_wallets' => $providerWallets->count(),
            'valid_wallets' => count($validWallets),
            'invalid_wallets' => $invalidWallets,
            'provider_enabled' => $providerEnabled,
            'errors' => array_unique($errors),
        ];
    }

    /**
     * Validate a single wallet's PSP account
     *
     * @param Wallet $wallet
     * @param string $provider
     * @return array{valid: bool, account_id: string|null, error: string|null}
     */
    private function validateSingleWallet(Wallet $wallet, string $provider): array {
        try {
            if ($provider === 'stripe') {
                // Retrieve Stripe Account ID from WalletDestinations
                $destination = $wallet->destinations->firstWhere('payment_type', \App\Enums\Payment\PaymentTypeEnum::STRIPE->value);
                $accountId = $destination?->destination_value;
                
                // FALLBACK: Legacy check for User model (to be removed once fully migrated)
                if (empty($accountId)) {
                    $accountId = $wallet->user?->stripe_account_id ?? $wallet->stripe_account_id;
                    if (!$accountId && $wallet->collection && $wallet->collection->owner) {
                        $accountId = $wallet->collection->owner->stripe_account_id;
                    }
                }

                if (empty($accountId)) {
                    return ['valid' => false, 'account_id' => null, 'error' => 'missing_account_id'];
                }

                $stripeSecret = config('algorand.payments.stripe.secret_key');

                if (empty($stripeSecret)) {
                    return ['valid' => false, 'account_id' => $accountId, 'error' => 'stripe_secret_not_configured'];
                }

                $stripeClient = new \Stripe\StripeClient($stripeSecret);
                $account = $stripeClient->accounts->retrieve($accountId);

                $this->logger->debug('Stripe account validated', [
                    'wallet_id' => $wallet->id,
                    'account_id' => $accountId,
                    'charges_enabled' => $account->charges_enabled ?? false,
                    'details_submitted' => $account->details_submitted ?? false,
                ]);

                if ($account->charges_enabled ?? false) {
                    return ['valid' => true, 'account_id' => $accountId, 'error' => null];
                }

                return ['valid' => false, 'account_id' => $accountId, 'error' => 'charges_disabled'];
            }
// ... (rest of method)

            if ($provider === 'paypal') {
                // PayPal validation not yet implemented
                return ['valid' => false, 'account_id' => $wallet->paypal_merchant_id, 'error' => 'paypal_not_implemented'];
            }

            return ['valid' => false, 'account_id' => null, 'error' => 'unsupported_provider'];
        } catch (\Exception $e) {
            $this->logger->warning('Wallet validation failed', [
                'wallet_id' => $wallet->id,
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return ['valid' => false, 'account_id' => null, 'error' => 'verification_failed'];
        }
    }

    /**
     * Collect wallets associated with the collection and its owner.
     *
     * @param Collection $collection
     * @return \Illuminate\Support\Collection<int, Wallet>
     */
    private function collectCandidateWallets(Collection $collection) {
        $wallets = $collection->relationLoaded('wallets')
            ? $collection->wallets
            : $collection->wallets()->with('destinations')->get();

        $ownerWallets = $collection->owner?->relationLoaded('wallets')
            ? $collection->owner->wallets
            : ($collection->owner?->wallets()->with('destinations')->get() ?? collect());

        return $wallets
            ->merge($ownerWallets)
            ->unique('id')
            ->sortByDesc(fn(Wallet $wallet) => $wallet->updated_at?->timestamp ?? 0)
            ->values();
    }

    /**
     * Collect wallets associated with a user (primary + all wallets).
     *
     * @param User $user
     * @return \Illuminate\Support\Collection<int, Wallet>
     */
    private function collectUserWallets(User $user) {
        $wallets = $user->relationLoaded('wallets')
            ? $user->wallets
            : $user->wallets()->with('destinations')->get();

        return $wallets
            ->unique('id')
            ->sortByDesc(fn(Wallet $wallet) => $wallet->updated_at?->timestamp ?? 0)
            ->values();
    }
}
