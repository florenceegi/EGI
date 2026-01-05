<?php

namespace App\Services\Payment;

use App\Enums\Payment\OrderStatusEnum;
use App\Enums\Payment\TxKindEnum;
use App\Enums\Wallet\WalletRoleEnum;
use App\Models\Egi;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * EgiClaimService
 *
 * Handles the EGI claiming flow: transferring ownership from Treasury
 * to the buyer's personal Algorand wallet.
 *
 * @see /docs/architecture/Contratto Narrativo EGI Claiming.md
 *
 * Flow:
 * 1. User purchases EGI → EGI minted to Treasury, ownership recorded in DB
 * 2. User claims EGI → Algorand wallet created on-demand, EGI transferred
 */
class EgiClaimService
{
    /**
     * Platform Treasury wallet address
     */
    protected string $treasuryAddress;

    public function __construct()
    {
        $this->treasuryAddress = config('app.natan_wallet_address', '');
    }

    /**
     * Check if an EGI is claimable by a user
     *
     * @param Egi $egi
     * @param User $user
     * @return bool
     */
    public function isClaimable(Egi $egi, User $user): bool
    {
        // 1. Check ownership in database
        if ($egi->owner_id !== $user->id) {
            return false;
        }

        // 2. Check EGI is not already claimed
        if ($egi->is_claimed) {
            return false;
        }

        // 3. Check EGI is minted (has ASA ID)
        if (empty($egi->asa_id)) {
            return false;
        }

        // 4. Check associated Order is completed
        $order = Order::where('egi_id', $egi->id)
            ->where('buyer_id', $user->id)
            ->whereIn('status', [
                OrderStatusEnum::MINTED->value,
                OrderStatusEnum::COMPLETED->value,
            ])
            ->first();

        if (!$order) {
            return false;
        }

        return true;
    }

    /**
     * Initiate EGI claim process
     *
     * @param Egi $egi
     * @param User $user
     * @param string|null $destinationWallet Optional custom wallet address
     * @return Order
     */
    public function initiateClaim(Egi $egi, User $user, ?string $destinationWallet = null): Order
    {
        // Validate claimability
        if (!$this->isClaimable($egi, $user)) {
            throw new \InvalidArgumentException('EGI is not claimable by this user');
        }

        // Get or create destination wallet
        $destination = $destinationWallet ?? $this->getOrCreateBuyerWallet($user, $egi->collection_id);

        if (empty($destination)) {
            throw new \RuntimeException('Unable to determine destination wallet for claim');
        }

        // Create claim Order
        return DB::transaction(function () use ($egi, $user, $destination) {
            $order = Order::create([
                'buyer_id' => $user->id,
                'egi_id' => $egi->id,
                'collection_id' => $egi->collection_id,
                'tx_kind' => TxKindEnum::CLAIM->value,
                'status' => OrderStatusEnum::PENDING->value,
                'payment_type' => 'algorand',  // Claims are always on-chain
                'currency' => 'ALGO',
                'amount_cents' => 0,  // Claims are gas-only
                'amount_eur' => 0,
                'metadata' => [
                    'destination_wallet' => $destination,
                    'source_wallet' => $this->treasuryAddress,
                    'asa_id' => $egi->asa_id,
                ],
            ]);

            Log::info('EgiClaimService: Claim order created', [
                'order_id' => $order->id,
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'destination' => $destination,
            ]);

            return $order;
        });
    }

    /**
     * Execute the on-chain claim transfer
     *
     * @param Order $order
     * @return bool
     */
    public function executeClaim(Order $order): bool
    {
        if ($order->tx_kind !== TxKindEnum::CLAIM->value) {
            throw new \InvalidArgumentException('Order is not a claim transaction');
        }

        $egi = $order->egi;
        $metadata = $order->metadata ?? [];
        $destination = $metadata['destination_wallet'] ?? null;

        if (!$destination || !$egi) {
            $order->markAsFailed('Missing destination wallet or EGI');
            return false;
        }

        try {
            // Execute on-chain transfer from Treasury to buyer
            // This would integrate with AlgorandService
            $txId = $this->executeAlgorandTransfer($egi, $destination);

            // Update order and EGI
            DB::transaction(function () use ($order, $egi, $destination, $txId) {
                $order->update([
                    'claimed' => true,
                    'claimed_at' => now(),
                    'claim_tx_id' => $txId,
                    'status' => OrderStatusEnum::COMPLETED->value,
                ]);

                $egi->update([
                    'is_claimed' => true,
                    'claimed_at' => now(),
                    'holder_wallet' => $destination,
                ]);
            });

            Log::info('EgiClaimService: Claim executed successfully', [
                'order_id' => $order->id,
                'egi_id' => $egi->id,
                'tx_id' => $txId,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('EgiClaimService: Claim execution failed', [
                'order_id' => $order->id,
                'egi_id' => $egi->id,
                'error' => $e->getMessage(),
            ]);

            $order->markAsFailed($e->getMessage());
            return false;
        }
    }

    /**
     * Get or create buyer's Algorand wallet for claiming
     *
     * @param User $user
     * @param int $collectionId
     * @return string|null Algorand wallet address
     */
    protected function getOrCreateBuyerWallet(User $user, int $collectionId): ?string
    {
        // Check for existing buyer wallet for this collection
        $existingWallet = Wallet::where('user_id', $user->id)
            ->where('collection_id', $collectionId)
            ->where('platform_role', WalletRoleEnum::BUYER->value)
            ->whereNotNull('wallet')
            ->first();

        if ($existingWallet) {
            return $existingWallet->wallet;
        }

        // Check for user's default Algorand wallet (any collection)
        $defaultWallet = Wallet::where('user_id', $user->id)
            ->whereNotNull('wallet')
            ->where('wallet', '!=', '')
            ->first();

        if ($defaultWallet) {
            return $defaultWallet->wallet;
        }

        // Create new wallet on-demand
        // This would integrate with AlgorandService to create wallet
        return $this->createBuyerWallet($user, $collectionId);
    }

    /**
     * Create a new Algorand wallet for the buyer
     *
     * @param User $user
     * @param int $collectionId
     * @return string|null
     */
    protected function createBuyerWallet(User $user, int $collectionId): ?string
    {
        try {
            // Integration with AlgorandService would happen here
            // For now, this is a placeholder that would:
            // 1. Generate new Algorand keypair
            // 2. Encrypt mnemonic
            // 3. Store in Wallet model
            
            Log::info('EgiClaimService: Wallet creation initiated', [
                'user_id' => $user->id,
                'collection_id' => $collectionId,
            ]);

            // This should return the generated address
            // Actual implementation would call AlgorandService
            return null;

        } catch (\Exception $e) {
            Log::error('EgiClaimService: Wallet creation failed', [
                'user_id' => $user->id,
                'collection_id' => $collectionId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Execute Algorand ASA transfer
     *
     * @param Egi $egi
     * @param string $destination
     * @return string Transaction ID
     */
    protected function executeAlgorandTransfer(Egi $egi, string $destination): string
    {
        // This would integrate with AlgorandService
        // Placeholder implementation
        
        // The actual implementation would:
        // 1. Get Treasury wallet credentials
        // 2. Create ASA transfer transaction
        // 3. Sign with Treasury key
        // 4. Submit to Algorand network
        // 5. Wait for confirmation
        // 6. Return transaction ID

        throw new \RuntimeException(
            'Algorand transfer not implemented - integrate with AlgorandService'
        );
    }

    /**
     * Get pending claims for a user
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingClaims(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Egi::where('owner_id', $user->id)
            ->where('is_claimed', false)
            ->whereNotNull('asa_id')
            ->get();
    }

    /**
     * Get claim history for a user
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getClaimHistory(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Order::where('buyer_id', $user->id)
            ->where('tx_kind', TxKindEnum::CLAIM->value)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
