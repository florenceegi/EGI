<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$migration = '2025_11_21_115931_add_collection_subscription_to_ai_credits_transactions';
$deleted = DB::table('migrations')->where('migration', $migration)->delete();

if ($deleted) {
    echo "Successfully deleted migration record: $migration\n";
} else {
    echo "Migration record not found (or already deleted): $migration\n";
}
