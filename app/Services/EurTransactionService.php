<?php

namespace App\Services;

use App\Models\User;
use App\Models\EgiliMerchantPurchase;
use App\Models\EgiBlockchain;
use App\Models\PaymentDistribution;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - EUR Transaction History)
 * @date 2025-11-21
 * @purpose Manages EUR transaction history for statements (expenses and income)
 */
class EurTransactionService {
    /**
     * Get all EUR transactions for a user (expenses + income)
     *
     * @param User $user
     * @param \Carbon\Carbon|null $startDate
     * @param \Carbon\Carbon|null $endDate
     * @return Collection Unified collection of EUR transactions
     */
    public function getTransactionHistory(User $user, $startDate = null, $endDate = null): Collection {
        $transactions = collect();

        // 1. Get Egili purchases (EXPENSES)
        $egiliPurchases = $this->getEgiliPurchases($user, $startDate, $endDate);
        $transactions = $transactions->merge($egiliPurchases);

        // 2. Get EGI mints (EXPENSES)
        $egiMints = $this->getEgiMints($user, $startDate, $endDate);
        $transactions = $transactions->merge($egiMints);

        // 3. Get payment distributions (INCOME) - what user received from sales
        $distributions = $this->getPaymentDistributions($user, $startDate, $endDate);
        $transactions = $transactions->merge($distributions);

        // Sort by date descending
        return $transactions->sortByDesc('transaction_date')->values();
    }

    /**
     * Get Egili purchases (user spent EUR to buy Egili)
     *
     * @param User $user
     * @param \Carbon\Carbon|null $startDate
     * @param \Carbon\Carbon|null $endDate
     * @return Collection
     */
    private function getEgiliPurchases(User $user, $startDate, $endDate): Collection {
        $query = EgiliMerchantPurchase::where('user_id', $user->id)
            ->where('payment_status', 'completed');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->get()->map(function ($purchase) {
            // Beneficiary: Platform (Natan, user_id 1) receives Egili purchase payments
            $platformUser = \App\Models\User::find(1);

            return [
                'id' => 'egili_' . $purchase->id,
                'transaction_date' => $purchase->created_at,
                'type' => 'egili_purchase',
                'description' => 'Acquisto Egili',
                'merchant' => $this->formatMerchant($purchase->payment_provider, $purchase->payment_method),
                'beneficiary' => $platformUser ? $platformUser->name : 'FlorenceEGI Platform',
                'amount_eur' => $purchase->total_price_eur,
                'operation' => 'expense', // User spent money
                'reference' => $purchase->order_reference,
                'metadata' => [
                    'egili_amount' => $purchase->egili_amount,
                    'payment_method' => $purchase->payment_method,
                    'payment_provider' => $purchase->payment_provider,
                    'payment_reference' => $purchase->payment_transaction_id,
                ],
                'model' => $purchase,
            ];
        });
    }

    /**
     * Get EGI mints (user spent EUR to mint EGI)
     *
     * IMPORTANT: Only includes EUR payments (excludes EGILI payments which go to EGILI statement)
     * For split payments, this returns MULTIPLE rows (one per beneficiary)
     * to show the user WHERE their money went (transparency)
     *
     * @param User $user
     * @param \Carbon\Carbon|null $startDate
     * @param \Carbon\Carbon|null $endDate
     * @return Collection
     */
    private function getEgiMints(User $user, $startDate, $endDate): Collection {
        $query = EgiBlockchain::where('buyer_user_id', $user->id)
            ->whereNotNull('paid_amount')
            ->where('paid_amount', '>', 0)
            // CRITICAL: Only include EUR payments, exclude EGILI payments (EGL)
            // EGILI payments go to the EGILI statement, not EUR statement
            ->where(function ($q) {
                $q->where('paid_currency', 'EUR')
                    ->orWhereNull('paid_currency'); // Legacy records without currency field
            });

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $transactions = collect();

        foreach ($query->with(['egi', 'egi.user'])->get() as $mint) {
            // Check if this mint has split payment distributions
            $distributions = PaymentDistribution::where('egi_id', $mint->egi_id)
                ->where('created_at', '>=', $mint->created_at->subMinutes(5)) // Same transaction window
                ->where('created_at', '<=', $mint->created_at->addMinutes(5))
                ->get();

            if ($distributions->isNotEmpty()) {
                // SPLIT PAYMENT: Show ONE row with breakdown in description
                // Build breakdown string: "Yuri (€2,550) • Oceano Blu (€750) • ..."
                $breakdownParts = [];
                foreach ($distributions->unique('user_id') as $distribution) {
                    $beneficiaryUser = \App\Models\User::find($distribution->user_id);
                    $breakdownParts[] = sprintf(
                        '%s (€%s)',
                        $beneficiaryUser ? $beneficiaryUser->name : 'N/A',
                        number_format($distribution->amount_eur, 2, ',', '.')
                    );
                }
                $breakdown = implode(' • ', $breakdownParts);

                $transactions->push([
                    'id' => 'mint_' . $mint->id,
                    'transaction_date' => $mint->created_at,
                    'type' => 'egi_mint_split',
                    'description' => sprintf(
                        'Mint EGI: %s',
                        $mint->egi->title ?? '#' . $mint->egi_id
                    ),
                    'merchant' => $this->formatMerchant($mint->payment_provider, 'fiat'),
                    'beneficiary' => 'Split Payment', // Indicates multiple beneficiaries
                    'amount_eur' => $mint->paid_amount, // Total amount
                    'operation' => 'expense',
                    'reference' => $mint->payment_reference ?? $mint->tx_id,
                    'metadata' => [
                        'egi_id' => $mint->egi_id,
                        'egi_title' => $mint->egi->title ?? null,
                        'payment_provider' => $mint->payment_provider,
                        'payment_reference' => $mint->payment_reference,
                        'tx_id' => $mint->tx_id,
                        'is_split_payment' => true,
                        'split_breakdown' => $breakdown,
                        'split_count' => $distributions->unique('user_id')->count(),
                    ],
                    'model' => $mint,
                ]);
            } else {
                // NO SPLIT: Show single row with total (legacy or direct payment)
                $creator = optional($mint->egi)->user ?? null;
                $beneficiary = $creator ? $creator->name : 'N/A';

                $transactions->push([
                    'id' => 'mint_' . $mint->id,
                    'transaction_date' => $mint->created_at,
                    'type' => 'egi_mint',
                    'description' => 'Mint EGI: ' . ($mint->egi->title ?? '#' . $mint->egi_id),
                    'merchant' => $this->formatMerchant($mint->payment_provider, 'fiat'),
                    'beneficiary' => $beneficiary,
                    'amount_eur' => $mint->paid_amount,
                    'operation' => 'expense',
                    'reference' => $mint->payment_reference ?? $mint->tx_id,
                    'metadata' => [
                        'egi_id' => $mint->egi_id,
                        'egi_title' => $mint->egi->title ?? null,
                        'creator_name' => $beneficiary,
                        'payment_provider' => $mint->payment_provider,
                        'payment_reference' => $mint->payment_reference,
                        'tx_id' => $mint->tx_id,
                        'is_split_payment' => false,
                    ],
                    'model' => $mint,
                ]);
            }
        }

        return $transactions;
    }

    /**
     * Get payment distributions (user received EUR from sales)
     *
     * @param User $user
     * @param \Carbon\Carbon|null $startDate
     * @param \Carbon\Carbon|null $endDate
     * @return Collection
     */
    private function getPaymentDistributions(User $user, $startDate, $endDate): Collection {
        $query = PaymentDistribution::where('user_id', $user->id)
            ->where('amount_eur', '>', 0);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->with('egi')->get()->map(function ($distribution) use ($user) {
            $description = 'Vendita EGI';
            if ($distribution->egi) {
                $description .= ': ' . $distribution->egi->title;
            }
            if ($distribution->platform_role) {
                $description .= ' (' . ucfirst($distribution->platform_role) . ')';
            }

            // Beneficiary: Current user received this distribution
            return [
                'id' => 'dist_' . $distribution->id,
                'transaction_date' => $distribution->created_at,
                'type' => 'egi_sale_distribution',
                'description' => $description,
                'merchant' => 'FlorenceEGI Platform',
                'beneficiary' => $user->name, // User received this money
                'amount_eur' => $distribution->amount_eur,
                'operation' => 'income', // User received money
                'reference' => 'DIST-' . str_pad($distribution->id, 6, '0', STR_PAD_LEFT),
                'metadata' => [
                    'egi_id' => $distribution->egi_id,
                    'egi_title' => $distribution->egi->title ?? null,
                    'platform_role' => $distribution->platform_role,
                    'percentage' => $distribution->percentage,
                    'reservation_id' => $distribution->reservation_id,
                ],
                'model' => $distribution,
            ];
        });
    }

    /**
     * Format merchant name from payment provider
     *
     * @param string|null $provider
     * @param string|null $method
     * @return string
     */
    private function formatMerchant(?string $provider, ?string $method): string {
        if (!$provider) {
            return 'N/A';
        }

        $merchantMap = [
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'coinbase' => 'Coinbase Commerce',
            'manual' => 'Pagamento Manuale',
        ];

        $merchant = $merchantMap[strtolower($provider)] ?? ucfirst($provider);

        if ($method === 'crypto') {
            $merchant .= ' (Crypto)';
        }

        return $merchant;
    }

    /**
     * Calculate EUR summary for a period
     *
     * @param Collection $transactions
     * @return array
     */
    public function calculateSummary(Collection $transactions): array {
        $totalExpenses = $transactions
            ->where('operation', 'expense')
            ->sum('amount_eur');

        $totalIncome = $transactions
            ->where('operation', 'income')
            ->sum('amount_eur');

        $netBalance = $totalIncome - $totalExpenses;

        return [
            'total_expenses' => round($totalExpenses, 2),
            'total_income' => round($totalIncome, 2),
            'net_balance' => round($netBalance, 2),
            'transaction_count' => $transactions->count(),
            'expense_count' => $transactions->where('operation', 'expense')->count(),
            'income_count' => $transactions->where('operation', 'income')->count(),
        ];
    }
}
