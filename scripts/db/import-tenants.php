<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';

/** @var ConsoleKernel $kernel */
$kernel = $app->make(ConsoleKernel::class);
$kernel->bootstrap();

$file = $argv[1] ?? __DIR__ . '/../../database/data/tenants.json';

if (!is_file($file)) {
    fwrite(STDERR, "File non trovato: {$file}\n");
    exit(1);
}

$data = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

if (!is_array($data)) {
    fwrite(STDERR, "JSON non valido in {$file}\n");
    exit(1);
}

$updated = 0;
foreach ($data as $row) {
    if (!is_array($row) || !array_key_exists('id', $row)) {
        continue;
    }

    $id = $row['id'];
    $payload = $row;
    unset($payload['id']);

    foreach ($payload as $key => $value) {
        if (is_array($value) || is_object($value)) {
            $payload[$key] = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

    DB::table('tenants')->updateOrInsert(['id' => $id], $payload);
    $updated++;
}

echo "Import/aggiornamento completato. Record processati: {$updated}\n";
