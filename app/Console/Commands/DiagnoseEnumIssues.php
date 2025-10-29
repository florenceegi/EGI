<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Diagnose Potential Enum-Related Issues
 *
 * Scans codebase for patterns that may cause TypeError with PHP 8.4
 * strict enum handling.
 *
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Code Quality)
 * @date 2025-10-29
 * @purpose Proactive error detection for enum handling
 */
class DiagnoseEnumIssues extends Command
{
    protected $signature = 'diagnose:enums {--fix : Auto-fix issues where possible}';
    protected $description = 'Scan codebase for potential enum-related TypeError issues';

    private array $issues = [];
    private int $filesScanned = 0;

    public function handle(): int
    {
        $this->info('🔍 Scanning codebase for enum-related issues...');
        $this->newLine();

        // Scan Services
        $this->scanDirectory('app/Services', 'Services');
        
        // Scan Controllers
        $this->scanDirectory('app/Http/Controllers', 'Controllers');

        // Scan Views (Blade)
        $this->scanBladeViews();

        $this->newLine();
        $this->info("📊 Scanned {$this->filesScanned} files");
        $this->newLine();

        if (empty($this->issues)) {
            $this->components->success('✅ No potential enum issues found!');
            return Command::SUCCESS;
        }

        $this->displayIssues();

        if ($this->option('fix')) {
            $this->warn('🔧 Auto-fix not implemented yet. Manual review required.');
        }

        return Command::SUCCESS;
    }

    private function scanDirectory(string $path, string $label): void
    {
        $this->line("Scanning {$label}...");

        $files = File::allFiles(base_path($path));

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') continue;

            $this->filesScanned++;
            $content = file_get_contents($file->getPathname());
            $relativePath = str_replace(base_path() . '/', '', $file->getPathname());

            $this->checkEnumAsArrayKey($content, $relativePath);
            $this->checkCollectionReturnType($content, $relativePath);
        }
    }

    private function scanBladeViews(): void
    {
        $this->line("Scanning Blade views...");

        $files = File::allFiles(base_path('resources/views'));

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') continue;

            $this->filesScanned++;
            $content = file_get_contents($file->getPathname());
            $relativePath = str_replace(base_path() . '/', '', $file->getPathname());

            $this->checkEnumInBladeOutput($content, $relativePath);
            $this->checkUndefinedVariables($content, $relativePath);
        }
    }

    private function checkEnumAsArrayKey(string $content, string $file): void
    {
        // Pattern: $something[$variable->category] where category might be enum
        if (preg_match_all('/\$[\w]+\[\$[\w]+->category\]/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $this->issues[] = [
                    'type' => 'ENUM_AS_ARRAY_KEY',
                    'severity' => 'HIGH',
                    'file' => $file,
                    'line' => $line,
                    'pattern' => $match[0],
                    'suggestion' => 'Convert enum to value before using as array key'
                ];
            }
        }
    }

    private function checkCollectionReturnType(string $content, string $file): void
    {
        // Pattern: ): Collection { ... ->map(
        if (preg_match_all('/function\s+\w+\([^)]*\)\s*:\s*Collection\s*\{[^}]*->map\(/s', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                
                // Check if file imports both Collection types
                $hasEloquentCollection = str_contains($content, 'use Illuminate\Database\Eloquent\Collection');
                $hasSupportCollection = str_contains($content, 'use Illuminate\Support\Collection');

                if ($hasEloquentCollection && !$hasSupportCollection) {
                    $this->issues[] = [
                        'type' => 'COLLECTION_TYPE_MISMATCH',
                        'severity' => 'HIGH',
                        'file' => $file,
                        'line' => $line,
                        'pattern' => 'Method returns Collection but uses ->map()',
                        'suggestion' => 'Import Support\Collection - map() returns Support\Collection not Eloquent\Collection'
                    ];
                }
            }
        }
    }

    private function checkEnumInBladeOutput(string $content, string $file): void
    {
        // Pattern: {{ $category }} in foreach over config array
        if (preg_match_all('/@foreach\(\$availableCategories[^)]*\)[\s\S]{0,200}\{\{\s*\$category\s*\}\}/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $this->issues[] = [
                    'type' => 'ENUM_IN_BLADE_OUTPUT',
                    'severity' => 'HIGH',
                    'file' => $file,
                    'line' => $line,
                    'pattern' => '{{ $category }} expects string but may receive array/enum',
                    'suggestion' => 'Use array_keys() in controller or access specific property'
                ];
            }
        }
    }

    private function checkUndefinedVariables(string $content, string $file): void
    {
        // Extract variables used in blade
        preg_match_all('/\$(\w+)/', $content, $varMatches);
        $usedVars = array_unique($varMatches[1]);

        // Common variables that should be passed but sometimes aren't
        $commonMissing = ['availableCategories', 'availableActionTypes', 'activityStats'];

        foreach ($commonMissing as $varName) {
            if (in_array($varName, $usedVars)) {
                // Variable is used - potential issue if not passed from controller
                $this->issues[] = [
                    'type' => 'POTENTIALLY_UNDEFINED_VAR',
                    'severity' => 'MEDIUM',
                    'file' => $file,
                    'line' => '?',
                    'pattern' => "\${$varName} used but may not be passed from controller",
                    'suggestion' => 'Verify controller passes this variable'
                ];
            }
        }
    }

    private function displayIssues(): void
    {
        // Group by severity
        $high = array_filter($this->issues, fn($i) => $i['severity'] === 'HIGH');
        $medium = array_filter($this->issues, fn($i) => $i['severity'] === 'MEDIUM');

        if (!empty($high)) {
            $this->error('🚨 HIGH SEVERITY ISSUES (' . count($high) . ')');
            $this->newLine();

            foreach ($high as $issue) {
                $this->components->twoColumnDetail(
                    "<fg=red>{$issue['type']}</>",
                    "{$issue['file']}:{$issue['line']}"
                );
                $this->line("   Pattern: {$issue['pattern']}");
                $this->line("   💡 {$issue['suggestion']}");
                $this->newLine();
            }
        }

        if (!empty($medium)) {
            $this->warn('⚠️  MEDIUM SEVERITY ISSUES (' . count($medium) . ')');
            $this->newLine();

            foreach (array_slice($medium, 0, 10) as $issue) {
                $this->components->twoColumnDetail(
                    "<fg=yellow>{$issue['type']}</>",
                    "{$issue['file']}"
                );
                $this->line("   💡 {$issue['suggestion']}");
                $this->newLine();
            }

            if (count($medium) > 10) {
                $this->line('   ... and ' . (count($medium) - 10) . ' more medium issues');
            }
        }
    }
}

