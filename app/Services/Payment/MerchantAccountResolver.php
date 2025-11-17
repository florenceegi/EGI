<?php

namespace App\Services\Payment;

use App\Exceptions\Payment\MerchantAccountNotConfiguredException;
use App\Models\Collection;
use App\Models\Egi;
use App\Models\Wallet;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: Merchant Account Resolver
 * 🎯 Purpose: Retrieve PSP account identifiers for creators without platform custody
 * 🛡️ MiCA-SAFE: Ensures settlements occur on creator-owned PSP accounts
 */
class MerchantAccountResolver
{
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
    public function resolveForEgiAndProvider(Egi $egi, string $provider): array
    {
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
                return filled($wallet->stripe_account_id);
            }),
            'paypal' => $wallets->first(function (Wallet $wallet) {
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

        return array_filter([
            'provider' => $provider,
            'collection_id' => $collection->id,
            'wallet_id' => $wallet->id,
            'stripe_account_id' => $wallet->stripe_account_id,
            'paypal_merchant_id' => $wallet->paypal_merchant_id,
        ], static fn ($value) => $value !== null && $value !== '');
    }

    /**
     * Collect wallets associated with the collection and its owner.
     *
     * @param Collection $collection
     * @return \Illuminate\Support\Collection<int, Wallet>
     */
    private function collectCandidateWallets(Collection $collection)
    {
        $wallets = $collection->relationLoaded('wallets')
            ? $collection->wallets
            : $collection->wallets()->get();

        $ownerWallets = $collection->owner?->relationLoaded('wallets')
            ? $collection->owner->wallets
            : ($collection->owner?->wallets()->get() ?? collect());

        return $wallets
            ->merge($ownerWallets)
            ->unique('id')
            ->sortByDesc(fn (Wallet $wallet) => $wallet->updated_at?->timestamp ?? 0)
            ->values();
    }
}

