<?php

declare(strict_types=1);

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\Process\Process;
use App\Models\AutomaticBackupConfig;

/**
 * @package App\Http\Controllers\Superadmin
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI)
 * @date 2025-11-03
 * @purpose Controller per interfaccia admin Migration Orchestrator
 * 
 * CONTESTO: Gestione centralizzata migration database condiviso EGI + NATAN_LOC
 * PERCORSO FILE: app/Http/Controllers/Superadmin/MigrationOrchestratorController.php
 */
class MigrationOrchestratorController extends Controller
{
    private const ORCHESTRATOR_PATH = '/home/fabio/migration_orchestrator/migrate_shared.php';
    private const BACKUPS_PATH = '/home/fabio/migration_orchestrator/backups';
    private const PROJECTS = [
        'EGI' => [
            'name' => 'FlorenceEGI',
            'path' => '/home/fabio/EGI',
            'color' => 'blue',
        ],
        'NATAN' => [
            'name' => 'NATAN_LOC',
            'path' => '/home/fabio/NATAN_LOC/laravel_backend',
            'color' => 'purple',
        ],
    ];

    /**
     * Display migration orchestrator admin interface
     */
    public function index(Request $request): View
    {
        $projects = [];
        foreach (self::PROJECTS as $key => $config) {
            $projects[$key] = array_merge($config, [
                'status' => $this->getMigrationStatus($key),
                'migration_count' => $this->getMigrationCount($key),
            ]);
        }

        $backups = $this->getBackupsList();

        $backupConfig = AutomaticBackupConfig::getConfig();

        return view('superadmin.migration-orchestrator.index', [
            'pageTitle' => __('menu.superadmin_migration_orchestrator'),
            'projects' => $projects,
            'backups' => $backups,
            'orchestrator_exists' => file_exists(self::ORCHESTRATOR_PATH),
            'backupConfig' => $backupConfig,
        ]);
    }

    /**
     * Get migration status for a project (API)
     */
    public function status(string $project): JsonResponse
    {
        if (!isset(self::PROJECTS[$project])) {
            return response()->json(['error' => 'Progetto non valido'], 400);
        }

        $status = $this->getMigrationStatus($project);
        $count = $this->getMigrationCount($project);

        return response()->json([
            'project' => $project,
            'status' => $status,
            'migration_count' => $count,
        ]);
    }

    /**
     * Execute migration command (API)
     */
    public function execute(Request $request): JsonResponse
    {
        $request->validate([
            'project' => 'required|in:EGI,NATAN',
            'command' => 'required|string',
            'args' => 'nullable|array',
        ]);

        $project = $request->input('project');
        $command = $request->input('command');
        $args = $request->input('args', []);

        // Safety check: prevent destructive commands without explicit confirmation
        $destructiveCommands = ['refresh', 'reset', 'fresh'];
        $commandBase = $this->extractCommandBase($command);
        
        if (in_array($commandBase, $destructiveCommands)) {
            $confirmed = $request->input('confirmed', false);
            if (!$confirmed) {
                return response()->json([
                    'error' => 'Comando distruttivo richiede conferma',
                    'requires_confirmation' => true,
                    'command' => $command,
                ], 400);
            }
        }

        if (!file_exists(self::ORCHESTRATOR_PATH)) {
            return response()->json(['error' => 'Orchestrator non trovato'], 500);
        }

        // Build command
        $fullCommand = ['php', self::ORCHESTRATOR_PATH, $project, $command];
        $fullCommand = array_merge($fullCommand, $args);

        // Execute with timeout
        $process = new Process($fullCommand);
        $process->setTimeout(300); // 5 minutes
        $process->run();

        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();
        $exitCode = $process->getExitCode();

        return response()->json([
            'success' => $exitCode === 0,
            'exit_code' => $exitCode,
            'output' => $output,
            'error' => $errorOutput ?: null,
            'command' => implode(' ', $fullCommand),
        ]);
    }

    /**
     * Get list of available backups
     */
    public function backups(): JsonResponse
    {
        return response()->json([
            'backups' => $this->getBackupsList(),
        ]);
    }

    /**
     * Create backup manually
     */
    public function createBackup(Request $request): JsonResponse
    {
        $request->validate([
            'label' => 'nullable|string|max:100',
        ]);

        $backupFile = $this->createDatabaseBackup($request->input('label'));

        if (!$backupFile) {
            return response()->json([
                'success' => false,
                'error' => 'Impossibile creare backup',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'backup' => [
                'filename' => basename($backupFile),
                'path' => $backupFile,
                'size' => File::size($backupFile),
                'size_human' => $this->formatBytes(File::size($backupFile)),
                'created_at' => date('Y-m-d H:i:s', File::lastModified($backupFile)),
            ],
            'message' => 'Backup creato con successo',
        ]);
    }

    /**
     * Restore backup
     */
    public function restoreBackup(Request $request): JsonResponse
    {
        $request->validate([
            'backup_path' => 'required|string',
            'confirmed' => 'required|boolean|accepted',
        ]);

        if (!$request->input('confirmed')) {
            return response()->json([
                'error' => 'Conferma richiesta per ripristinare backup',
            ], 400);
        }

        $backupPath = $request->input('backup_path');

        if (!file_exists($backupPath)) {
            return response()->json([
                'error' => 'File backup non trovato',
            ], 404);
        }

        // Get database config from EGI (they share the same DB)
        $envFile = self::PROJECTS['EGI']['path'] . '/.env';
        if (!file_exists($envFile)) {
            return response()->json([
                'error' => 'File .env non trovato',
            ], 500);
        }

        $env = $this->parseEnvFile($envFile);
        $dbName = $env['DB_DATABASE'] ?? 'EGI';
        $dbUser = $env['DB_USERNAME'] ?? 'fabio';
        $dbPassword = $env['DB_PASSWORD'] ?? '';
        $dbHost = $env['DB_HOST'] ?? 'localhost';
        $dbPort = $env['DB_PORT'] ?? '3306';

        // Create backup before restore (safety)
        $safetyBackup = $this->createDatabaseBackup('pre-restore-safety');

        // Restore backup
        $command = sprintf(
            'mysql -h %s -P %s -u %s -p%s %s < %s 2>&1',
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbPassword),
            escapeshellarg($dbName),
            escapeshellarg($backupPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            return response()->json([
                'success' => false,
                'error' => 'Errore durante ripristino backup',
                'output' => implode("\n", $output),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Backup ripristinato con successo',
            'safety_backup' => basename($safetyBackup),
        ]);
    }

    /**
     * Delete backup
     */
    public function deleteBackup(Request $request): JsonResponse
    {
        $request->validate([
            'backup_path' => 'required|string',
        ]);

        $backupPath = $request->input('backup_path');

        // Security: only allow deletion from backups directory
        if (strpos($backupPath, self::BACKUPS_PATH) !== 0) {
            return response()->json([
                'error' => 'Percorso non valido',
            ], 403);
        }

        if (!file_exists($backupPath)) {
            return response()->json([
                'error' => 'File backup non trovato',
            ], 404);
        }

        if (!File::delete($backupPath)) {
            return response()->json([
                'error' => 'Impossibile eliminare backup',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Backup eliminato con successo',
        ]);
    }

    /**
     * Create database backup
     */
    private function createDatabaseBackup(?string $label = null): ?string
    {
        $timestamp = date('Y-m-d_H-i-s');
        $backupDir = self::BACKUPS_PATH;

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $labelPart = $label ? '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $label) : '';
        $backupFile = "$backupDir/egi_shared_backup{$labelPart}_{$timestamp}.sql";

        // Get database config from EGI
        $envFile = self::PROJECTS['EGI']['path'] . '/.env';
        if (!file_exists($envFile)) {
            return null;
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
            \Log::error('Backup creation failed', [
                'command' => $command,
                'output' => implode("\n", $output),
                'return_code' => $returnCode,
            ]);
            return null;
        }

        return $backupFile;
    }

    /**
     * Parse .env file
     */
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

    /**
     * Update automatic backup configuration
     */
    public function updateBackupConfig(Request $request): JsonResponse
    {
        $request->validate([
            'is_enabled' => 'required|boolean',
            'interval_type' => 'required|in:hours,days,weeks',
            'interval_value' => 'required|integer|min:1',
            'start_time' => 'required|date_format:H:i',
            'retention_days' => 'required|integer|min:1|max:365',
            'max_backups' => 'required|integer|min:1|max:100',
        ]);

        $config = AutomaticBackupConfig::getConfig();
        $config->fill($request->only([
            'is_enabled',
            'interval_type',
            'interval_value',
            'start_time',
            'retention_days',
            'max_backups',
        ]));

        // Calcola prossimo backup se abilitato
        if ($config->is_enabled) {
            $config->next_backup_at = $this->calculateNextBackupTime($config);
        } else {
            $config->next_backup_at = null;
        }

        $config->save();

        return response()->json([
            'success' => true,
            'message' => 'Configurazione backup aggiornata',
            'config' => $config,
        ]);
    }

    /**
     * Get automatic backup configuration
     */
    public function getBackupConfig(): JsonResponse
    {
        $config = AutomaticBackupConfig::getConfig();

        return response()->json([
            'config' => $config,
        ]);
    }

    /**
     * Calculate next backup time
     */
    private function calculateNextBackupTime(AutomaticBackupConfig $config): \Carbon\Carbon
    {
        $now = now();
        $interval = $config->interval_value;

        // Se non c'è ancora un backup, usa start_time di oggi o domani
        if (!$config->last_backup_at) {
            $startTime = \Carbon\Carbon::createFromTimeString($config->start_time);
            if ($startTime->isPast()) {
                $next = $now->copy()->addDay()->setTimeFromTimeString($config->start_time);
            } else {
                $next = $now->copy()->setTimeFromTimeString($config->start_time);
            }
        } else {
            // Calcola dal prossimo intervallo
            switch ($config->interval_type) {
                case 'hours':
                    $next = $now->copy()->addHours($interval);
                    break;
                case 'days':
                    $next = $now->copy()->addDays($interval)->setTimeFromTimeString($config->start_time);
                    break;
                case 'weeks':
                    $next = $now->copy()->addWeeks($interval)->setTimeFromTimeString($config->start_time);
                    break;
                default:
                    $next = $now->copy()->addHours(24);
            }
        }

        return $next;
    }

    /**
     * Get migration status for project
     */
    private function getMigrationStatus(string $project): array
    {
        if (!isset(self::PROJECTS[$project])) {
            return ['error' => 'Progetto non valido'];
        }

        $projectPath = self::PROJECTS[$project]['path'];
        $artisanPath = $projectPath . '/artisan';

        if (!file_exists($artisanPath)) {
            return ['error' => 'Progetto non trovato'];
        }

        try {
            $process = new Process(['php', $artisanPath, 'migrate:status'], $projectPath);
            $process->setTimeout(30);
            $process->run();

            if ($process->getExitCode() !== 0) {
                return ['error' => 'Errore esecuzione migrate:status'];
            }

            $output = $process->getOutput();
            
            // Parse output
            $lines = explode("\n", trim($output));
            $pending = 0;
            $ran = 0;

            foreach ($lines as $line) {
                if (strpos($line, 'Pending') !== false) {
                    $pending++;
                } elseif (strpos($line, 'Ran') !== false) {
                    $ran++;
                }
            }

            return [
                'pending' => $pending,
                'ran' => $ran,
                'total' => $pending + $ran,
                'raw_output' => $output,
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get migration count for project
     */
    private function getMigrationCount(string $project): int
    {
        if (!isset(self::PROJECTS[$project])) {
            return 0;
        }

        $migrationsPath = self::PROJECTS[$project]['path'] . '/database/migrations';
        
        if (!is_dir($migrationsPath)) {
            return 0;
        }

        $files = File::glob($migrationsPath . '/*.php');
        return count($files);
    }

    /**
     * Get list of backups
     */
    private function getBackupsList(): array
    {
        if (!is_dir(self::BACKUPS_PATH)) {
            return [];
        }

        $files = File::glob(self::BACKUPS_PATH . '/*.sql');
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'path' => $file,
                'size' => File::size($file),
                'size_human' => $this->formatBytes(File::size($file)),
                'modified' => File::lastModified($file),
                'modified_human' => date('Y-m-d H:i:s', File::lastModified($file)),
            ];
        }

        // Sort by modified time (newest first)
        usort($backups, function ($a, $b) {
            return $b['modified'] <=> $a['modified'];
        });

        return $backups;
    }

    /**
     * Extract command base from full command
     */
    private function extractCommandBase(string $command): string
    {
        $parts = explode(':', $command);
        return end($parts);
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
