<?php

namespace App\Traits;

use App\Models\Collection;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

trait HasCreateDefaultCollectionWallets
{
    /**
     * Genera i wallet di default per una collection.
     *
     * @param  Collection  $collection
     * @param  string  $wallet_creator
     */
    public function generateDefaultWallets(Collection $collection, ?string $wallet_creator='', $creator_id): void
    {
        // 1. Determine Profile
        $profile = $collection->profile_type ?? 'contributor';

        // 2. Get Fee Structures
        $mintFees = \App\Enums\Fees\FeeStructureEnum::fromProfile($profile, 'mint')->getDistribution();
        $rebindFees = \App\Enums\Fees\FeeStructureEnum::fromProfile($profile, 'rebind')->getDistribution();

        DB::transaction(function () use ($collection, $wallet_creator, $creator_id, $mintFees, $rebindFees) {
            
            // 3. Iterate over mandated roles in the Fee Structure
            // merging keys from both to ensure we cover all required wallets
            $roles = array_unique(array_merge(array_keys($mintFees), array_keys($rebindFees)));

            foreach ($roles as $roleName) {
                $mintPercent = $mintFees[$roleName] ?? 0.0;
                $rebindPercent = $rebindFees[$roleName] ?? 0.0;

                // Skip if both are 0 (unless we want to force wallet creation for completeness?)
                // User requirement implies strict adherence to the Enum. 
                // Creating 0% wallet is fine, it just won't receive funds but exists for structure.
                
                $address = '';
                $userId = null;
                $roleEnum = \App\Enums\Wallet\WalletRoleEnum::tryFrom($roleName);

                if (!$roleEnum) {
                    // Fallback or skip if enum doesn't match string (shouldn't happen with correct FeeEnum)
                    if ($roleName === 'Creator') {
                         $address = $wallet_creator;
                         $userId = $creator_id;
                    } else {
                        continue; 
                    }
                } else {
                    // Handle role-specific wallet creation
                    if ($roleEnum === \App\Enums\Wallet\WalletRoleEnum::CREATOR) {
                        $address = $wallet_creator;
                        $userId = $creator_id;
                    } elseif ($roleEnum === \App\Enums\Wallet\WalletRoleEnum::EPP) {
                        // EPP: Only create wallet if collection has an assigned EPP project
                        // EPP is dynamically assigned per-collection by the user, not auto-created
                        if ($collection->epp_project_id) {
                            $collection->loadMissing('eppProject.eppUser');
                            
                            if ($collection->eppProject && $collection->eppProject->eppUser) {
                                $eppUser = $collection->eppProject->eppUser;
                                $address = $eppUser->wallet ?? '';
                                $userId = $eppUser->id;
                            } else {
                                // No valid EPP user found - skip wallet creation
                                continue;
                            }
                        } else {
                            // No EPP project assigned - skip EPP wallet creation entirely
                            // User will assign EPP project later if desired
                            continue;
                        }
                    } else {
                        // Platform roles (Natan, Frangette) - use enum defaults
                        $address = $roleEnum->getWalletAddress();
                        $userId = $roleEnum->getUserId();
                    }
                }

                $this->createWallet(
                    $roleName, 
                    $address, 
                    (string)$mintPercent, 
                    (string)$rebindPercent, 
                    $collection, 
                    $userId
                );
            }
        });
    }

    /**
     * Crea un wallet per una collection.
     *
     * @param  string  $role
     * @param  string  $address
     * @param  string  $royalty_mint
     * @param  string  $royalty_rebind
     * @param  Collection  $collection
     */
    protected function createWallet(string $role, ?string $address='', string $royalty_mint, string $royalty_rebind, Collection $collection, $id): void
    {
        Wallet::create([
            'collection_id' => $collection->id,
            'user_id' => $id,
            'platform_role' => $role,
            'wallet' => $address,
            'royalty_mint' => $royalty_mint,
            'royalty_rebind' => $royalty_rebind,
        ]);
    }
}
