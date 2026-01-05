<?php

namespace App\Console\Commands;

use App\Models\EgiBlockchain;
use App\Models\PaymentDistribution;
use App\Services\Payment\StripePaymentSplitService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Stripe Event Polling Command
 * 
 * Bypasses webhook delivery issues by polling Stripe API directly.
 * Processes unhandled payment_intent.succeeded events.
 * 
 * @author Automated Fix for Firewall/Fail2Ban blocking
 */
class ProcessStripeEventsCommand extends Command
{
    protected $signature = 'stripe:process-events {--limit=10 : Number of events to fetch}';
    protected $description = 'Poll and process unhandled Stripe payment events (Webhook bypass)';

    public function __construct(
        private StripePaymentSplitService $splitService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('🔄 Polling Stripe for unprocessed events...');
        
        $secret = config('services.stripe.secret') ?: env('STRIPE_SECRET_KEY');
        if (!$secret) {
            $this->error('❌ Stripe Secret Key not configured.');
            return self::FAILURE;
        }

        $stripe = new \Stripe\StripeClient($secret);
        $limit = (int) $this->option('limit');
        $processed = 0;
        $skipped = 0;

        try {
            $events = $stripe->events->all([
                'type' => 'payment_intent.succeeded',
                'limit' => $limit,
            ]);

            foreach ($events->data as $event) {
                $pi = $event->data->object;
                $metadata = $pi->metadata->toArray();
                
                // Check if this is an EGI mint event
                if (!isset($metadata['egi_blockchain_id']) || !isset($metadata['requires_split'])) {
                    continue;
                }
                
                $egiBlockchainId = (int) $metadata['egi_blockchain_id'];
                
                // Check if already processed
                $exists = PaymentDistribution::where('egi_blockchain_id', $egiBlockchainId)->exists();
                if ($exists) {
                    $skipped++;
                    $this->line("  ⏭️ Mint #$egiBlockchainId already processed. Skipping.");
                    continue;
                }
                
                // Process this event
                $this->info("  ➡️ Processing Mint #$egiBlockchainId (Event: {$event->id})...");
                
                try {
                    $blockchain = EgiBlockchain::find($egiBlockchainId);
                    if (!$blockchain) {
                        $this->warn("  ⚠️ Blockchain record not found for ID $egiBlockchainId");
                        continue;
                    }
                    
                    $collection = $blockchain->egi->collection;
                    $grossAmountEur = $pi->amount / 100;
                    
                    $this->splitService->splitPaymentToWallets(
                        $pi->id,
                        $collection,
                        $grossAmountEur,
                        $metadata
                    );
                    
                    $processed++;
                    $this->info("  ✅ Mint #$egiBlockchainId processed successfully!");
                    
                    Log::info('Stripe Event Polling: Processed event', [
                        'event_id' => $event->id,
                        'egi_blockchain_id' => $egiBlockchainId,
                        'amount' => $grossAmountEur,
                    ]);
                    
                } catch (\Throwable $e) {
                    $this->error("  ❌ Failed to process Mint #$egiBlockchainId: " . $e->getMessage());
                    Log::error('Stripe Event Polling: Failed to process event', [
                        'event_id' => $event->id,
                        'egi_blockchain_id' => $egiBlockchainId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            $this->newLine();
            $this->info("📊 Summary: $processed processed, $skipped skipped.");
            
            return self::SUCCESS;
            
        } catch (\Throwable $e) {
            $this->error('❌ Stripe API Error: ' . $e->getMessage());
            Log::error('Stripe Event Polling: API Error', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }
    }
}
