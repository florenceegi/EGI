<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$migration = '2025_11_19_234915_fix_frangette_collection_visibility_and_link';
$deleted = DB::table('migrations')->where('migration', $migration)->delete();

if ($deleted) {
    echo "Successfully deleted migration record: $migration\n";
} else {
    echo "Migration record not found (or already deleted): $migration\n";
}
