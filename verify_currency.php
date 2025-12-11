<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- VERIFICATION START ---\n";

$exists = Schema::hasColumn('ai_credits_transactions', 'currency');
$colType = Schema::getColumnType('ai_credits_transactions', 'currency');

// For more precision on length (Schema::getColumnType only indicates 'string')
$driver = DB::getDriverName();
echo "Driver: $driver\n";

if ($driver === 'pgsql') {
    $info = DB::select("
        SELECT character_maximum_length 
        FROM information_schema.columns 
        WHERE table_name = 'ai_credits_transactions' 
        AND column_name = 'currency'
    ");
    $length = $info[0]->character_maximum_length ?? 'N/A';
    echo "Column 'currency' Length: $length\n";
} else {
    echo "Column 'currency' Type: $colType\n";
}

echo "--- VERIFICATION END ---\n";
