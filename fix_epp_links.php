<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// 1. Link Frangette's collection (ID 3) to Project 1
$col = \App\Models\Collection::where('creator_id', 3)->first(); // Frangette
if ($col) {
    echo "Found Frangette's collection: {$col->collection_name}\n";
    if (is_null($col->epp_project_id)) {
        $col->epp_project_id = 1; // Rimini Clear
        $col->save();
        echo "✅ Linked collection {$col->id} to project 1.\n";
    } else {
        echo "Collection already linked to project {$col->epp_project_id}.\n";
    }
} else {
    echo "Frangette's collection not found.\n";
}

// 2. Check Natan's collection (ID 1) just in case
$colNatan = \App\Models\Collection::where('creator_id', 1)->first(); // Natan
if ($colNatan) {
    echo "Found Natan's collection: {$colNatan->collection_name}\n";
     if (is_null($colNatan->epp_project_id)) {
        $colNatan->epp_project_id = 1; // Rimini Clear
        $colNatan->save();
        echo "✅ Linked Natan's collection {$colNatan->id} to project 1.\n";
    }
}






