#!/usr/bin/env php
<?php
// Audit script: scans for ErrorManager->handle() payloads that include nested arrays (e.g., 'context' => [ ... ])
// Non-blocking: always exits 0. Prints a concise report with file:line and a short hint.

declare(strict_types=1);

function iterPhpFiles(string $dir): \Generator {
    $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS));
    foreach ($rii as $file) {
        if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
            yield $file->getPathname();
        }
    }
}

function findSuspiciousHandles(string $path): array {
    $lines = @file($path);
    if ($lines === false) return [];
    $results = [];

    foreach ($lines as $i => $line) {
        if (strpos($line, 'errorManager->handle(') !== false || strpos($line, 'error_manager->handle(') !== false) {
            // Look ahead a small window to catch array literal
            $snippet = '';
            for ($j = $i; $j < min($i + 25, count($lines)); $j++) {
                $snippet .= $lines[$j];
            }

            // Heuristics: detect `'context' => [`, or any `'=>' [` inside the first argument array
            $hasArrayContext = \preg_match("/'context'\s*=>\s*\[/i", $snippet) === 1
                || \preg_match('/"context"\s*=>\s*\[/i', $snippet) === 1;

            $hasAnyNested = \preg_match("/=>\s*\[/", $snippet) === 1; // any nested array in payload

            if ($hasArrayContext || $hasAnyNested) {
                $results[] = [
                    'line' => $i + 1,
                    'context' => trim($line),
                    'reason' => $hasArrayContext ? "'context' nested array" : 'nested array literal in payload',
                ];
            }
        }
    }

    return $results;
}

$root = realpath(__DIR__ . '/../../');
$appDir = $root . '/app';

$total = 0;
$filesWithFindings = 0;
$report = [];

foreach (iterPhpFiles($appDir) as $file) {
    $findings = findSuspiciousHandles($file);
    if (!empty($findings)) {
        $filesWithFindings++;
        $total += count($findings);
        $report[$file] = $findings;
    }
}

// Print report
echo "\n=== ErrorManager Payload Audit (non-blocking) ===\n";
echo "Root: $root\n";
echo "Scanned: app/\n";
echo "Files with findings: $filesWithFindings\n";
echo "Total findings: $total\n\n";

foreach ($report as $file => $items) {
    echo "- $file\n";
    foreach ($items as $f) {
        $l = str_pad((string)$f['line'], 5, ' ', STR_PAD_LEFT);
        echo "  L$l  {$f['reason']}\n";
    }
}

if ($total > 0) {
    echo "\nHint: passa una struttura flat (es. context_file/context_rule) o usa ErrorContextNormalizer prima di chiamare handle().\n";
    echo "Questo audit è informativo: non fallisce la pipeline.\n";
}

exit(0);
