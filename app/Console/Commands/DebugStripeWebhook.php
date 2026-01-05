<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Payment\StripeRealPaymentService;
use Stripe\StripeClient;

class DebugStripeWebhook extends Command
{
    protected $signature = 'debug:stripe-webhook {payment_intent_id?}';
    protected $description = 'Simulate a Stripe Webhook for a specific PaymentIntent to trigger Split Logic';

    public function handle(StripeRealPaymentService $paymentService)
    {
        $piId = $this->argument('payment_intent_id');
        $secretKey = config('algorand.payments.stripe.secret_key');
        
        if (empty($secretKey)) {
            $this->error('Stripe Secret Key not configured.');
            return;
        }

        $stripe = new StripeClient($secretKey);

        if (!$piId) {
            $this->info('Fetching latest PaymentIntent from Stripe...');
            $pis = $stripe->paymentIntents->all(['limit' => 1, 'expand' => ['data.latest_charge.balance_transaction']]);
            
            if (empty($pis->data)) {
                $this->error('No PaymentIntents found in Stripe account.');
                return;
            }
            
            $pi = $pis->data[0];
            $piId = $pi->id;
        } else {
            $pi = $stripe->paymentIntents->retrieve($piId, ['expand' => ['latest_charge.balance_transaction']]);
        }

        $this->info("------------------------------------------------");
        $this->info("Simulating Webhook for PaymentIntent: {$piId}");
        $this->info("Amount: " . ($pi->amount / 100) . " " . strtoupper($pi->currency));
        $this->info("Status: {$pi->status}");
        
        // Metadata Check
        $metadata = $pi->metadata->toArray();
        $this->info("Metadata: " . json_encode($metadata, JSON_PRETTY_PRINT));

        if (!isset($metadata['requires_split']) || $metadata['requires_split'] !== 'true') {
            $this->warn("WARNING: 'requires_split' is NOT set to 'true' in metadata. The webhook might ignore this.");
        }

        // Construct Webhook Payload
        // We need to mimic the structure exactly as Stripe sends it
        $payload = [
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => $pi->toArray()
            ]
        ];

        $this->info("------------------------------------------------");
        $this->info("Injecting Payload into StripeRealPaymentService::processPaymentWebhook...");

        try {
            $result = $paymentService->processPaymentWebhook($payload);
            $this->info("Service Result: " . json_encode($result, JSON_PRETTY_PRINT));
            $this->info("------------------------------------------------");
            $this->info("✅ Webhook Simulation Completed.");
            $this->info("Check 'payment_distributions' table and Stripe Dashboard for transfers.");
        } catch (\Exception $e) {
            $this->error("❌ Error during simulation: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
}
