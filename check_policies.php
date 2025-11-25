<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Privacy Policies count: " . App\Models\PrivacyPolicy::count() . "\n";
echo "\nPolicy list:\n";
foreach (App\Models\PrivacyPolicy::all() as $policy) {
    echo "- [{$policy->id}] {$policy->title} ({$policy->language}) v{$policy->version}\n";
}
