<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$p = \App\Models\EppProject::find(1);
if ($p) {
    echo "Project: {$p->name}\n";
    echo "epp_user_id: " . ($p->epp_user_id ?? 'NULL') . "\n";
    
    // Se è NULL, cerchiamo di assegnarlo a Natan (ID 1) se Natan esiste
    if (is_null($p->epp_user_id)) {
        echo "Fixing orphaned project...\n";
        $p->epp_user_id = 1; // Assign to Natan
        $p->save();
        echo "Assigned to User ID 1.\n";
    }
} else {
    echo "Project 1 not found.\n";
}







