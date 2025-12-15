<?php

namespace App\Models\Traits;

/**
 * @Oracode Trait: Wallet Management (Integration with existing wallet system)
 * 🎯 Purpose: Integrates with existing wallet table and management
 * 🛡️ Privacy: Handles wallet data with appropriate security measures
 * 🧱 Core Logic: Provides wallet access methods compatible with existing system
 */
trait HasWalletManagement
{
    /**
     * Get primary wallet (uses existing wallet field)
     */
    public function getPrimaryWallet(): ?string
    {
        return $this->wallet;
    }

    /**
     * Get wallet balance (uses existing wallet_balance field)
     */
    public function getWalletBalance(): float
    {
        return (float) $this->wallet_balance;
    }

    /**
     * Check if user has connected wallet
     */
    public function hasConnectedWallet(): bool
    {
        return !empty($this->wallet);
    }

    /**
     * Update wallet balance
     */
    public function updateWalletBalance(float $balance): bool
    {
        return $this->forceFill(['wallet_balance' => $balance])->save();
    }

    /**
     * Connect wallet address
     */
    public function connectWallet(string $address): bool
    {
        return $this->forceFill(['wallet' => $address])->save();
    }

    /**
     * Disconnect wallet
     */
    public function disconnectWallet(): bool
    {
        return $this->forceFill([
            'wallet' => null,
            'personal_secret' => null,
            'wallet_balance' => 0
        ])->save();
    }

    /**
     * Get wallet connection status
     */
    public function getWalletStatus(): array
    {
        return [
            'connected' => $this->hasConnectedWallet(),
            'address' => $this->wallet,
            'balance' => $this->getWalletBalance(),
            'has_secret' => !empty($this->personal_secret)
        ];
    }

    /**
     * Validate wallet address format (Algorand)
     */
    public function isValidAlgorandAddress(string $address): bool
    {
        // Basic Algorand address validation
        return strlen($address) === 58 && ctype_alnum($address);
    }
}