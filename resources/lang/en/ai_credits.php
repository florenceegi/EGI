<?php

/**
 * @package Resources\Lang\En
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Credits Cost Tracking)
 * @date 2025-10-28
 * @purpose AI Credits system translations for cost tracking, balance display, and payment flows
 */

return [
    // === BALANCE DISPLAY ===
    'balance' => [
        'title' => 'AI Credits Balance',
        'current' => 'Current Balance',
        'credits' => 'credits',
        'low_balance' => 'Low balance',
        'no_credits' => 'No credits available',
        'refresh' => 'Refresh balance',
    ],

    // === COST PREVIEW MODAL ===
    'preview' => [
        'modal_title' => 'AI Analysis Cost Preview',
        'estimated_cost' => 'Estimated Cost',
        'your_balance' => 'Your Balance',
        'balance_after' => 'Balance After Analysis',
        'acts_to_analyze' => 'Acts to Analyze',
        'chunks_required' => 'Processing Chunks',
        'tokens_estimated' => 'Estimated Tokens',
        'cost_breakdown' => 'Cost Breakdown',
        'input_tokens' => 'Input Tokens',
        'output_tokens' => 'Output Tokens (estimated)',
        'total_cost' => 'Total Cost',
        'rate_info' => 'Exchange Rate',
        'rate_source' => 'Source: European Central Bank',
        'rate_updated' => 'Updated',
        'insufficient_title' => 'Insufficient Credits',
        'insufficient_message' => 'You need :required credits but have only :balance available.',
        'purchase_credits' => 'Purchase Credits',
        'cancel' => 'Cancel',
        'proceed' => 'Proceed with Analysis',
        'loading' => 'Calculating cost...',
    ],

    // === REAL-TIME COST DISPLAY ===
    'realtime' => [
        'panel_title' => 'AI Processing Cost',
        'current_cost' => 'Current Cost',
        'estimated_final' => 'Estimated Final',
        'tokens_used' => 'Tokens Used',
        'input' => 'Input',
        'output' => 'Output',
        'chunk_cost' => 'Chunk :number Cost',
        'total_so_far' => 'Total So Far',
    ],

    // === FINAL COST SUMMARY ===
    'summary' => [
        'title' => 'Analysis Cost Summary',
        'final_cost' => 'Final Cost',
        'credits_deducted' => 'Credits Deducted',
        'balance_before' => 'Balance Before',
        'balance_after' => 'Balance After',
        'tokens_breakdown' => 'Tokens Breakdown',
        'total_input_tokens' => 'Total Input Tokens',
        'total_output_tokens' => 'Total Output Tokens',
        'total_tokens' => 'Total Tokens',
        'cost_per_chunk' => 'Cost per Chunk',
        'chunks_processed' => 'Chunks Processed',
        'transaction_id' => 'Transaction ID',
        'timestamp' => 'Completed At',
        'close' => 'Close',
        'view_transaction' => 'View Transaction Details',
    ],

    // === ERRORS ===
    'errors' => [
        'insufficient_credits' => 'Insufficient AI credits. You have :balance credits, but :required are needed.',
        'deduction_failed' => 'Failed to deduct credits. Please try again or contact support.',
        'calculation_failed' => 'Cost calculation failed. Please try again.',
        'estimation_failed' => 'Unable to estimate cost. Proceeding may consume credits.',
        'exchange_rate_unavailable' => 'Exchange rate unavailable. Using fallback rate.',
        'generic' => 'An error occurred while processing credits.',
    ],

    // === SUCCESS MESSAGES ===
    'success' => [
        'credits_deducted' => 'Credits deducted successfully.',
        'credits_refunded' => 'Credits refunded: :amount credits returned to your balance.',
        'balance_updated' => 'Balance updated successfully.',
    ],

    // === TOOLTIPS ===
    'tooltips' => [
        'what_are_credits' => 'AI Credits are used to power AI analysis. Each query consumes credits based on document complexity.',
        'how_calculated' => 'Cost is calculated from API tokens: :input_price per 1M input tokens + :output_price per 1M output tokens.',
        'exchange_rate' => 'Prices are in USD, converted to EUR using ECB official rate updated daily.',
        'chunks_explanation' => 'Large document sets are split into chunks to ensure quality analysis within AI limits.',
        'refund_policy' => 'If analysis fails, credits are automatically refunded to your balance.',
    ],

    // === PURCHASE FLOW ===
    'purchase' => [
        'title' => 'Purchase AI Credits',
        'select_package' => 'Select Package',
        'package_basic' => 'Basic',
        'package_standard' => 'Standard',
        'package_premium' => 'Premium',
        'package_enterprise' => 'Enterprise',
        'credits_amount' => ':amount credits',
        'price' => ':price EUR',
        'best_value' => 'Best Value',
        'most_popular' => 'Most Popular',
        'proceed_to_payment' => 'Proceed to Payment',
        'payment_methods' => 'Accepted: Card, PayPal, Bank Transfer',
    ],

    // === TRANSACTION HISTORY ===
    'transactions' => [
        'title' => 'AI Credits Transactions',
        'date' => 'Date',
        'type' => 'Type',
        'amount' => 'Amount',
        'balance' => 'Balance',
        'description' => 'Description',
        'status' => 'Status',
        'type_deduction' => 'Analysis',
        'type_purchase' => 'Purchase',
        'type_refund' => 'Refund',
        'type_bonus' => 'Bonus',
        'status_completed' => 'Completed',
        'status_pending' => 'Pending',
        'status_failed' => 'Failed',
        'no_transactions' => 'No transactions yet',
        'view_all' => 'View All Transactions',
    ],
];
