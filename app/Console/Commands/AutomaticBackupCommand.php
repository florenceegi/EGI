<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AutomaticBackupConfig;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI)
 * @date 2025-11-03
 * @purpose Comando per eseguire backup automatico del database
 * 
 * CONTESTO: Sistema backup automatico con cron job
 * PERCORSO FILE: app/Console/Commands/AutomaticBackupCommand.php
 */
class AutomaticBackupCommand extends Command
{
    protected $signature = 'backup:automatic';
    protected $description = 'Esegue backup automatico del database se configurato';

    private const BACKUPS_PATH = '/home/fabio/migration_orchestrator/backups';

    public function handle(): int
    {
        $config = AutomaticBackupConfig::first();

        if (!$config || !$config->is_enabled) {
            $this->info('Backup automatico disabilitato.');
            return 0;
        }

        // Verifica se è il momento di fare il backup
        if ($config->next_backup_at && now()->lt($config->next_backup_at)) {
            $this->info('Backup non ancora necessario. Prossimo backup: ' . $config->next_backup_at);
            return 0;
        }

        $this->info('Inizio backup automatico...');

        try {
            $backupFile = $this->createBackup();

            if (!$backupFile) {
                throw new \Exception('Impossibile creare backup');
            }

            // Aggiorna configurazione
            $config->last_backup_at = now();
            $config->next_backup_at = $this->calculateNextBackup($config);
            $config->last_error = null;
            $config->save();

            // Pulisci backup vecchi
            $this->cleanupOldBackups($config);

            $this->info('✅ Backup creato con successo: ' . basename($backupFile));
            Log::info('Automatic backup created', ['file' => basename($backupFile)]);

            return 0;
        } catch (\Exception $e) {
            $config->last_error = $e->getMessage();
            $config->save();

            $this->error('❌ Errore backup: ' . $e->getMessage());
            Log::error('Automatic backup failed', ['error' => $e->getMessage()]);

            return 1;
        }
    }

    private function createBackup(): ?string
    {
        $timestamp = date('Y-m-d_H-i-s');
        $backupDir = self::BACKUPS_PATH;

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $backupFile = "$backupDir/auto_backup_{$timestamp}.sql";

        // Get database config
        $envFile = base_path('.env');
        if (!file_exists($envFile)) {
            throw new \Exception('File .env non trovato');
        }

        $env = $this->parseEnvFile($envFile);
        $dbName = $env['DB_DATABASE'] ?? 'EGI';
        $dbUser = $env['DB_USERNAME'] ?? 'fabio';
        $dbPassword = $env['DB_PASSWORD'] ?? '';
        $dbHost = $env['DB_HOST'] ?? 'localhost';
        $dbPort = $env['DB_PORT'] ?? '3306';

        // Create backup with mysqldump
        $command = sprintf(
            'mysqldump -h %s -P %s -u %s -p%s --single-transaction --quick --lock-tables=false %s > %s 2>&1',
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbPassword),
            escapeshellarg($dbName),
            escapeshellarg($backupFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('mysqldump fallito: ' . implode("\n", $output));
        }

        return $backupFile;
    }

    private function calculateNextBackup(AutomaticBackupConfig $config): \Carbon\Carbon
    {
        $now = now();
        $interval = $config->interval_value;

        switch ($config->interval_type) {
            case 'hours':
                return $now->copy()->addHours($interval);
            case 'days':
                return $now->copy()->addDays($interval)->setTimeFromTimeString($config->start_time);
            case 'weeks':
                return $now->copy()->addWeeks($interval)->setTimeFromTimeString($config->start_time);
            default:
                return $now->copy()->addHours(24);
        }
    }

    private function cleanupOldBackups(AutomaticBackupConfig $config): void
    {
        if (!is_dir(self::BACKUPS_PATH)) {
            return;
        }

        $files = File::glob(self::BACKUPS_PATH . '/auto_backup_*.sql');
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'path' => $file,
                'time' => File::lastModified($file),
            ];
        }

        // Ordina per data (più recenti prima)
        usort($backups, fn($a, $b) => $b['time'] <=> $a['time']);

        // Rimuovi backup oltre retention
        $cutoffTime = now()->subDays($config->retention_days)->timestamp;
        foreach ($backups as $backup) {
            if ($backup['time'] < $cutoffTime) {
                File::delete($backup['path']);
                $this->info('Rimosso backup vecchio: ' . basename($backup['path']));
            }
        }

        // Mantieni solo max_backups più recenti
        $toKeep = $config->max_backups;
        $toRemove = array_slice($backups, $toKeep);

        foreach ($toRemove as $backup) {
            if (File::exists($backup['path'])) {
                File::delete($backup['path']);
                $this->info('Rimosso backup (limite): ' . basename($backup['path']));
            }
        }
    }

    private function parseEnvFile(string $file): array
    {
        $env = [];
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, '#') === 0 || empty($line)) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $env[trim($key)] = trim($value, " \t\n\r\0\x0B'\"");
            }
        }

        return $env;
    }
}
