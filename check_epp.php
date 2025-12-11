<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    if (Schema::hasTable('epp_projects')) {
        $projects = DB::table('epp_projects')->get();
        echo "Count: " . $projects->count() . "\n";
        foreach ($projects as $project) {
            echo "ID: " . $project->id . " - Name: " . ($project->name ?? 'N/A') . "\n";
        }
    } else {
        echo "Table epp_projects does not exist.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
