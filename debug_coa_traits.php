<?php

/**
 * Test debug per verificare dati passati al template certificato
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Coa;
use App\Http\Controllers\VerifyController;
use App\Traits\EgiTraitsExtraction;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Debug: Dati passati al template certificato\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    // Trova il CoA di test
    $coa = Coa::where('serial', 'TEST-69-20250918185740')
        ->with(['egi.traits.traitType', 'egi.user'])
        ->first();

    if (!$coa) {
        echo "❌ CoA non trovato\n";
        exit(1);
    }

    echo "✅ CoA trovato: {$coa->serial}\n";
    echo "📄 EGI ID: {$coa->egi->id}\n";
    echo "🎨 Titolo: {$coa->egi->title}\n\n";

    // Test dell'estrazione traits
    $traitTester = new class {
        use EgiTraitsExtraction;

        public function testData(Coa $coa): array {
            $egi = $coa->egi;

            return [
                'artwork' => [
                    'name' => $egi->title ?? $egi->name ?? 'Unknown Artwork',
                    'author' => $this->extractAuthorFromTraits($egi),
                    'year' => $this->extractYearFromTraits($egi),
                    'technique' => $this->extractTechniqueFromTraits($egi),
                    'support' => $this->extractSupportFromTraits($egi),
                    'dimensions' => $this->extractDimensionsFromTraits($egi),
                    'edition' => $this->extractEditionFromTraits($egi),
                    'traits' => $this->extractAllArtworkMetadata($egi),
                    'internal_id' => $egi->id
                ]
            ];
        }
    };

    $data = $traitTester->testData($coa);

    echo "🎯 Dati artwork estratti:\n";
    echo "   • Name: " . $data['artwork']['name'] . "\n";
    echo "   • Author: " . $data['artwork']['author'] . "\n";
    echo "   • Year: " . ($data['artwork']['year'] ?: 'NULL') . "\n";
    echo "   • Technique: " . ($data['artwork']['technique'] ?: 'NULL') . "\n";
    echo "   • Support: " . ($data['artwork']['support'] ?: 'NULL') . "\n";
    echo "   • Dimensions: " . ($data['artwork']['dimensions'] ?: 'NULL') . "\n";
    echo "   • Edition: " . ($data['artwork']['edition'] ?: 'NULL') . "\n";
    echo "   • Internal ID: " . $data['artwork']['internal_id'] . "\n\n";

    echo "🏷️  Traits estratti (array):\n";
    $traits = $data['artwork']['traits'];
    echo "   • Count: " . count($traits) . "\n";

    if (count($traits) > 0) {
        foreach ($traits as $trait) {
            echo "   • {$trait['trait_type']}: {$trait['value']}\n";
        }
    } else {
        echo "   ❌ NESSUN TRAIT ESTRATTO!\n";
    }

    echo "\n🔍 Debug condizione template:\n";
    echo "   • isset(\$artwork['traits']): " . (isset($data['artwork']['traits']) ? 'TRUE' : 'FALSE') . "\n";
    echo "   • count(\$artwork['traits']): " . count($data['artwork']['traits']) . "\n";
    echo "   • count(\$artwork['traits']) > 0: " . (count($data['artwork']['traits']) > 0 ? 'TRUE' : 'FALSE') . "\n";

    echo "\n📋 La condizione template dovrebbe essere: ";
    echo (isset($data['artwork']['traits']) && count($data['artwork']['traits']) > 0) ? "✅ TRUE (sezione visibile)" : "❌ FALSE (sezione nascosta)";
    echo "\n";
} catch (Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "\n";
    echo "📍 Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
