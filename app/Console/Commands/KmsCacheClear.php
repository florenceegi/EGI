<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Security\KmsHealthCheck;

/**
 * Clear KMS Health Check Cache
 *
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - KMS Testing)
 * @date 2025-10-29
 * @purpose Invalidate KMS health check cache after config changes
 */
class KmsCacheClear extends Command
{
    protected $signature = 'kms:cache:clear';
    protected $description = 'Clear KMS health check cache';

    public function handle(KmsHealthCheck $kmsHealth): int
    {
        $kmsHealth->invalidateCache();
        $this->components->info('KMS health check cache cleared');
        $this->info('Next wallet operation will re-test KMS configuration.');
        return Command::SUCCESS;
    }
}
