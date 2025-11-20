<?php

use App\Models\Collection;
use App\Models\User;
use App\Models\Egi;
use App\Models\EppProject;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "🔍 EPP User Type Debug\n";
echo "======================\n";

$targetIds = [1, 3];
$users = User::whereIn('id', $targetIds)->get();

foreach ($users as $user) {
    echo "User: {$user->name} (ID: {$user->id})\n";
    echo "  usertype: " . ($user->usertype ?? 'NULL') . "\n";
    
    // Check if owns EPP Projects
    $projects = EppProject::where('epp_user_id', $user->id)->get();
    echo "  Owned EPP Projects: " . $projects->count() . "\n";
    
    // Check collections creator
    $colls = Collection::where('creator_id', $user->id)->get();
    foreach($colls as $c) {
        echo "    Collection: {$c->collection_name} (epp_project_id: " . ($c->epp_project_id ?? 'NULL') . ")\n";
    }
}
