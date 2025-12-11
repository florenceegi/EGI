<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- VERIFICATION START ---\n";

// 1. Check Project
$project = DB::table('epp_projects')->where('name', 'Rimini Clear')->first();
echo "Project 'Rimini Clear': " . ($project ? "FOUND (ID: {$project->id})" : "MISSING") . "\n";

// 1.1 Check User 2
$user2 = DB::table('users')->where('id', 2)->first();
echo "User ID 2 (EPP): " . ($user2 ? "FOUND" : "MISSING") . "\n";

// 2. Check Collection
$collection = DB::table('collections')->where('creator_id', 3)->first();
if ($collection) {
    echo "Collection (Creator 3): FOUND (ID: {$collection->id})\n";
    echo " > epp_project_id: " . ($collection->epp_project_id ?? 'NULL') . "\n";
    echo " > is_published: " . $collection->is_published . "\n";
    echo " > status: " . $collection->status . "\n";
} else {
    echo "Collection (Creator 3): MISSING\n";
}

echo "--- VERIFICATION END ---\n";
