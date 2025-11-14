<?php

namespace App\Services\Payment;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Arr;
use Stripe\StripeClient;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: Stripe Connect Account Manager
 * 🎯 Purpose: Gestisce la creazione e il recupero degli account Stripe Connect per i creator
 * 🛡️ MiCA-SAFE: Tutti i fondi vengono trasferiti direttamente ai merchant collegati
 */
class StripeConnectService
{
    private StripeClient $client;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $secretKey = config('algorand.payments.stripe.secret_key');

        if (empty($secretKey)) {
            throw new \RuntimeException('Stripe secret key not configured');
        }

        $this->client = new StripeClient($secretKey);
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;

    /**
     * Ensure that the wallet has an associated Stripe Express account.
     *
     * @return array{account: array<string,mixed>, created: bool}
     */
    public function ensureExpressAccount(Wallet $wallet, User $user): array
    {
        try {
            if ($wallet->stripe_account_id) {
                $account = $this->client->accounts->retrieve($wallet->stripe_account_id, []);

                return [
                    'account' => $account->toArray(),
                    'created' => false,
                ];
            }

            $country = Arr::get($wallet->metadata, 'iban_country_code', 'IT') ?: 'IT';

            $account = $this->client->accounts->create([
                'type' => 'express',
                'country' => strtoupper($country),
                'email' => $user->email,
                'metadata' => [
                    'wallet_id' => (string) $wallet->id,
                    'user_id' => (string) $user->id,
                ],
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'business_type' => 'individual',
                'business_profile' => [
                    'mcc' => '5970', // Artist supply & craft stores (placeholder)
                    'product_description' => 'FlorenceEGI digital art marketplace creator',
                ],
            ]);

            $wallet->update([
                'stripe_account_id' => $account->id,
            ]);

            $this->logger->info('Stripe Connect account created', [
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'stripe_account_id' => $account->id,
            ]);

            return [
                'account' => $account->toArray(),
                'created' => true,
            ];
        } catch (\Throwable $exception) {
            $this->logger->error('Stripe Connect account creation failed', [
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            $this->errorManager->handle('STRIPE_CONNECT_ACCOUNT_FAILED', [
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ], $exception);

            throw $exception;
        }
    }

    /**
     * Retrieve a Stripe account as array data.
     *
     * @return array<string,mixed>|null
     */
    public function retrieveAccount(?string $accountId): ?array
    {
        if (empty($accountId)) {
            return null;
        }

        try {
            $account = $this->client->accounts->retrieve($accountId, []);

            return $account->toArray();
        } catch (\Throwable $exception) {
            $this->logger->error('Stripe Connect account retrieval failed', [
                'stripe_account_id' => $accountId,
                'error' => $exception->getMessage(),
            ]);

            $this->errorManager->handle('STRIPE_CONNECT_ACCOUNT_RETRIEVE_FAILED', [
                'stripe_account_id' => $accountId,
                'error' => $exception->getMessage(),
            ], $exception);

            return null;
        }
    }

    /**
     * Create an onboarding/refresh link for the Express account.
     */
    public function createAccountLink(string $accountId, string $returnUrl, string $refreshUrl): ?string
    {
        try {
            $link = $this->client->accountLinks->create([
                'account' => $accountId,
                'refresh_url' => $refreshUrl,
                'return_url' => $returnUrl,
                'type' => 'account_onboarding',
            ]);

            return $link->url;
        } catch (\Throwable $exception) {
            $this->logger->error('Stripe Connect account link creation failed', [
                'stripe_account_id' => $accountId,
                'error' => $exception->getMessage(),
            ]);

            $this->errorManager->handle('STRIPE_CONNECT_ACCOUNT_LINK_FAILED', [
                'stripe_account_id' => $accountId,
                'error' => $exception->getMessage(),
            ], $exception);

            return null;
        }
    }

    /**
     * Create an Express dashboard login link for the merchant.
     */
    public function createExpressDashboardLoginLink(string $accountId): ?string
    {
        try {
            $link = $this->client->accounts->createLoginLink($accountId, []);

            return $link->url;
        } catch (\Throwable $exception) {
            $this->logger->warning('Stripe Express dashboard link failed', [
                'stripe_account_id' => $accountId,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }
}

