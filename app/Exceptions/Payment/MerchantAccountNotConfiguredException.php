<?php

namespace App\Exceptions\Payment;

use RuntimeException;

/**
 * @Oracode Exception: Merchant Account Not Configured
 * 🎯 Purpose: Signal missing PSP onboarding data for creator wallets
 * 🛡️ MiCA-SAFE: Prevents platform from processing payments without direct settlement accounts
 */
class MerchantAccountNotConfiguredException extends RuntimeException
{
    /**
     * @param string $provider PSP provider identifier (stripe|paypal)
     * @param int|null $collectionId Related collection ID
     * @param int|null $egiId Related EGI ID
     */
    public function __construct(string $provider, ?int $collectionId = null, ?int $egiId = null)
    {
        $message = "Merchant account not configured for provider {$provider}.";

        if ($collectionId !== null) {
            $message .= " Collection ID: {$collectionId}.";
        }

        if ($egiId !== null) {
            $message .= " EGI ID: {$egiId}.";
        }

        parent::__construct($message);
    }
}

