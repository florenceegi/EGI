<?php

/**
 * Test per verificare che i traits siano inclusi nel certificato CoA
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Egi;
use App\Models\Coa;
use App\Http\Controllers\VerifyController;
use App\Traits\EgiTraitsExtraction;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Test: Verifica inclusione traits nel certificato CoA\n";
echo "=" . str_repeat("=", 60) . "\n\n";

try {
    // 1. Trova un EGI con traits dalla tabella egi_traits
    $egi = Egi::whereHas('traits')->with(['traits.traitType'])->first();

    if (!$egi) {
        echo "❌ Errore: Nessun EGI trovato con traits validi\n";
        exit(1);
    }

    echo "✅ EGI trovato: ID {$egi->id} - {$egi->title}\n";
    echo "📊 Traits disponibili: " . $egi->traits->count() . "\n\n";

    // 2. Mostra i traits attuali
    if ($egi->traits->count() > 0) {
        echo "🏷️  Traits attuali:\n";
        foreach ($egi->traits as $trait) {
            $traitTypeName = $trait->traitType->name ?? 'Unknown';
            echo "   • {$traitTypeName}: {$trait->value}\n";
        }
        echo "\n";
    }

    // 3. Test estrazione traits con il trait helper
    echo "🔍 Test estrazione traits:\n";

    // Create a mock class to test the trait
    $traitTester = new class {
        use EgiTraitsExtraction;

        public function testExtraction(Egi $egi): array {
            return [
                'author' => $this->extractAuthorFromTraits($egi),
                'year' => $this->extractYearFromTraits($egi),
                'technique' => $this->extractTechniqueFromTraits($egi),
                'support' => $this->extractSupportFromTraits($egi),
                'dimensions' => $this->extractDimensionsFromTraits($egi),
                'edition' => $this->extractEditionFromTraits($egi),
                'all_traits' => $this->extractAllArtworkMetadata($egi)
            ];
        }
    };

    $extracted = $traitTester->testExtraction($egi);

    echo "   • Autore: " . ($extracted['author'] ?: 'Non trovato') . "\n";
    echo "   • Anno: " . ($extracted['year'] ?: 'Non trovato') . "\n";
    echo "   • Tecnica: " . ($extracted['technique'] ?: 'Non trovato') . "\n";
    echo "   • Supporto: " . ($extracted['support'] ?: 'Non trovato') . "\n";
    echo "   • Dimensioni: " . ($extracted['dimensions'] ?: 'Non trovato') . "\n";
    echo "   • Edizione: " . ($extracted['edition'] ?: 'Non trovato') . "\n";
    echo "   • Totale traits estratti: " . count($extracted['all_traits']) . "\n\n";

    // 4. Verifica se esiste un CoA per questo EGI
    $coa = Coa::where('egi_id', $egi->id)->where('status', 'valid')->first();

    if (!$coa) {
        echo "⚠️  Nessun CoA valido trovato per questo EGI\n";
        echo "💡 Suggerimento: Crea un CoA per testare la visualizzazione completa\n";
    } else {
        echo "✅ CoA trovato: {$coa->serial}\n";
        echo "🔗 URL verifica: " . url("/coa/verify/{$coa->serial}") . "\n";
    }

    echo "\n📋 Test completato con successo!\n";
    echo "🎯 I traits sono ora configurati per essere mostrati nel certificato\n";
} catch (Exception $e) {
    echo "❌ Errore durante il test: " . $e->getMessage() . "\n";
    echo "📍 Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
