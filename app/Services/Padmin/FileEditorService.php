<?php

declare(strict_types=1);

namespace App\Services\Padmin;

use Ultra\UltraLogManager\UltraLogManager;

/**
 * Servizio per applicare fix ai file con backup/rollback
 *
 * @package App\Services\Padmin
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-23
 */
class FileEditorService
{
    protected UltraLogManager $logger;
    protected string $backupDir;

    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
        $this->backupDir = storage_path('app/padmin-backups');

        // Crea directory backup se non esiste
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    /**
     * Applica fix a un file con backup automatico
     */
    public function applyFix(string $filePath, string $oldCode, string $newCode): array
    {
        $absolutePath = base_path($filePath);

        $this->logger->info('[FileEditorService] Applying fix', [
            'file' => $filePath,
            'old_code_length' => strlen($oldCode),
            'new_code_length' => strlen($newCode)
        ]);

        // Verifica file esiste
        if (!file_exists($absolutePath)) {
            return [
                'success' => false,
                'error' => 'File not found: ' . $filePath
            ];
        }

        // Crea backup
        $backupPath = $this->createBackup($absolutePath);

        try {
            // Leggi file
            $content = file_get_contents($absolutePath);

            // Replace code
            $newContent = str_replace($oldCode, $newCode, $content);

            // Verifica che il replace abbia avuto effetto
            if ($newContent === $content) {
                throw new \Exception('Code replacement failed - old code not found in file');
            }

            // Valida sintassi PHP
            if (!$this->validatePhpSyntax($newContent)) {
                throw new \Exception('Syntax error in fixed code');
            }

            // Scrivi file
            file_put_contents($absolutePath, $newContent);

            $this->logger->info('[FileEditorService] Fix applied successfully', [
                'file' => $filePath,
                'backup' => $backupPath
            ]);

            return [
                'success' => true,
                'backup_path' => $backupPath,
                'message' => 'Fix applied successfully'
            ];

        } catch (\Exception $e) {
            // Rollback
            $this->restoreBackup($backupPath, $absolutePath);

            $this->logger->error('[FileEditorService] Fix failed, rolled back', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'rolled_back' => true
            ];
        }
    }

    /**
     * Crea backup di un file
     */
    private function createBackup(string $filePath): string
    {
        $timestamp = date('Y-m-d_H-i-s');
        $filename = basename($filePath);
        $backupPath = $this->backupDir . '/' . $filename . '.' . $timestamp . '.backup';

        copy($filePath, $backupPath);

        return $backupPath;
    }

    /**
     * Ripristina backup
     */
    private function restoreBackup(string $backupPath, string $originalPath): void
    {
        if (file_exists($backupPath)) {
            copy($backupPath, $originalPath);
        }
    }

    /**
     * Valida sintassi PHP
     */
    private function validatePhpSyntax(string $code): bool
    {
        // Scrivi temporaneamente il codice in un file temp
        $tempFile = tempnam(sys_get_temp_dir(), 'padmin_syntax_check_');
        file_put_contents($tempFile, $code);

        // Esegui php -l per check sintassi
        $output = [];
        $returnVar = 0;
        exec('php -l ' . escapeshellarg($tempFile) . ' 2>&1', $output, $returnVar);

        // Rimuovi temp file
        unlink($tempFile);

        // Return var 0 = syntax OK
        return $returnVar === 0;
    }

    /**
     * Lista tutti i backup disponibili
     */
    public function listBackups(): array
    {
        $backups = [];
        $files = glob($this->backupDir . '/*.backup');

        foreach ($files as $file) {
            $backups[] = [
                'path' => $file,
                'filename' => basename($file),
                'created_at' => date('Y-m-d H:i:s', filemtime($file)),
                'size' => filesize($file)
            ];
        }

        // Ordina per data decrescente
        usort($backups, fn($a, $b) => $b['created_at'] <=> $a['created_at']);

        return $backups;
    }

    /**
     * Pulisci backup vecchi (> 7 giorni)
     */
    public function cleanOldBackups(int $daysToKeep = 7): int
    {
        $deleted = 0;
        $cutoffTime = time() - ($daysToKeep * 24 * 60 * 60);
        $files = glob($this->backupDir . '/*.backup');

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
                $deleted++;
            }
        }

        $this->logger->info('[FileEditorService] Cleaned old backups', [
            'deleted' => $deleted,
            'days_kept' => $daysToKeep
        ]);

        return $deleted;
    }
}
