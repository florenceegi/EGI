<?php

declare(strict_types=1);

namespace Ultra\EgiModule\Contracts;

use App\Models\Collection;
use App\Models\User;

/**
 * 📜 Oracode Interface: WalletServiceInterface
 *
 * Defines the contract for wallet management operations.
 *
 * @package     Ultra\EgiModule\Contracts
 * @author      Padmin D. Curtis (Generated for Fabio Cherici)
 * @copyright   2024 Fabio Cherici
 * @license     MIT
 * @version     1.0.0
 * @since       2025-04-29
 *
 * @purpose     🎯 Provides a consistent API for managing wallet operations, including creation,
 *              wallet attachment to collections, and quota validation.
 *
 * @context     🧩 Used across the application when wallet operations need to be performed.
 *
 * @feature     🗝️ Default wallet creation for collections
 * @feature     🗝️ Wallet quota validation
 * @feature     🗝️ Royalty management
 *
 * @signal      🚦 Returns operation status via boolean values
 * @signal      🚦 Methods handle their own error reporting through injected dependencies
 *
 * @dependency  🤝 App\Models\Collection (implied by implementations)
 * @dependency  🤝 App\Models\User (implied by implementations)
 *
 * @privacy     🛡️ `@privacy-purpose`: Methods manage wallet data including addresses
 * @privacy     🛡️ `@privacy-consideration`: Wallet addresses may be considered personal financial data
 *
 * @testing     🧪 Interface methods should be tested for both success and failure scenarios
 *
 * @rationale   💡 Centralizes wallet management for consistent handling and testing.
 */
interface WalletServiceInterface
{
    /**
     * 🎯 Attaches default wallets to a collection.
     *
     * @param Collection $collection The collection to attach wallets to
     * @param User $user The owner/creator of the collection
     * @return void
     *
     * @privacy-purpose Wallet creation for royalty management
     */
    public function attachDefaultWalletsToCollection(Collection $collection, User $user): void;

    /**
     * 🎯 Attaches EPP-specific wallet to a collection (100% royalties).
     *
     * Creates a SINGLE wallet for EPP user with 100% mint and rebind royalties.
     *
     * @param Collection $collection The EPP collection to attach wallet to
     * @param User $user The EPP user (owner of the collection)
     * @return void
     *
     * @privacy-purpose Wallet creation for EPP-exclusive royalty management
     */
    public function attachEppWalletToCollection(Collection $collection, User $user): void;

    /**
     * 🔍 Validates if a user has sufficient royalty quota.
     *
     * @param int $userId User ID to check quota for
     * @param int $collectionId Collection ID to find the wallet
     * @param float $requiredMintQuota Minimum mint quota required
     * @param float $requiredRebindQuota Minimum rebind quota required
     * @return bool True if quota is sufficient, false otherwise
     *
     * @privacy-purpose Validate available royalty quota
     */
    public function hasUserSufficientQuota(
        int $userId,
        int $collectionId,
        float $requiredMintQuota,
        float $requiredRebindQuota
    ): bool;
}
