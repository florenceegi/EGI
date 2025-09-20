<?php
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require 'bootstrap/app.php';
$app->boot();

use App\Services\Coa\VocabularyService;
use App\Models\VocabularyTerm;

$service = app(VocabularyService::class);

// Test single term transformation
$term = VocabularyTerm::where('slug', 'material-gold-leaf')->first();
if ($term) {
    echo "Database name: " . $term->name . "\n";
    echo "Slug: " . $term->slug . "\n";

    // Test translation directly
    $translationKey = "coa_vocabulary.{$term->slug}";
    $translated = __($translationKey, [], 'it');
    echo "Translation key: " . $translationKey . "\n";
    echo "Translation result: " . $translated . "\n";
    echo "Is same as key? " . ($translated === $translationKey ? 'YES' : 'NO') . "\n";

    // Test search method
    $results = $service->searchTermsCollection('oro', null, 'it');
    $foundTerm = $results->where('slug', 'material-gold-leaf')->first();
    if ($foundTerm) {
        echo "\nTransformed term:\n";
        echo "Name: " . $foundTerm->name . "\n";
        echo "Description: " . substr($foundTerm->description, 0, 50) . "...\n";
    }
} else {
    echo "Term not found\n";
}
