<?php

namespace App\Console\Commands;

use App\Contracts\IpfsServiceInterface;
use Illuminate\Console\Command;

/**
 * Test IPFS Upload Command
 * 
 * Verifica che la configurazione Pinata sia corretta
 * e che l'upload su IPFS funzioni.
 */
class TestIpfsUpload extends Command
{
    protected $signature = 'ipfs:test {file? : Path to file to upload (optional, uses test image if not provided)}';
    protected $description = 'Test IPFS upload functionality with Pinata';

    public function __construct(
        private readonly IpfsServiceInterface $ipfsService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('🚀 Testing IPFS Upload Service');
        $this->newLine();

        // Check if service is enabled
        if (!$this->ipfsService->isEnabled()) {
            $this->error('❌ IPFS service is DISABLED');
            $this->warn('Set IPFS_ENABLED=true in your .env file');
            return Command::FAILURE;
        }

        $this->info('✅ IPFS service is enabled');

        // Get file to upload
        $filePath = $this->argument('file');
        
        if (!$filePath) {
            // Create a test file
            $filePath = storage_path('app/ipfs_test_' . time() . '.txt');
            file_put_contents($filePath, 'IPFS Test Upload from FlorenceEGI - ' . now()->toIso8601String());
            $this->info("📄 Created test file: {$filePath}");
            $isTestFile = true;
        } else {
            if (!file_exists($filePath)) {
                $this->error("❌ File not found: {$filePath}");
                return Command::FAILURE;
            }
            $isTestFile = false;
        }

        $this->info("📤 Uploading to IPFS via Pinata...");
        $this->newLine();

        // Perform upload
        $result = $this->ipfsService->upload($filePath, [
            'source' => 'ipfs:test command',
            'uploaded_at' => now()->toIso8601String(),
        ]);

        // Clean up test file
        if ($isTestFile && file_exists($filePath)) {
            unlink($filePath);
        }

        // Show results
        if ($result['success']) {
            $this->info('✅ Upload SUCCESSFUL!');
            $this->newLine();
            
            $this->table(
                ['Property', 'Value'],
                [
                    ['CID', $result['cid']],
                    ['Gateway URL', $result['gateway_url'] ?? 'N/A'],
                    ['Public URL', 'https://ipfs.io/ipfs/' . $result['cid']],
                ]
            );

            $this->newLine();
            $this->info('🔗 You can view your file at:');
            $this->line($result['gateway_url'] ?? 'https://ipfs.io/ipfs/' . $result['cid']);
            
            return Command::SUCCESS;
        } else {
            $this->error('❌ Upload FAILED');
            $this->error('Error: ' . ($result['error'] ?? 'Unknown error'));
            
            $this->newLine();
            $this->warn('Common issues:');
            $this->line('  - Invalid PINATA_JWT token');
            $this->line('  - Expired API credentials');
            $this->line('  - Network connectivity issues');
            $this->line('  - File size exceeds free plan limit (100MB)');
            
            return Command::FAILURE;
        }
    }
}
