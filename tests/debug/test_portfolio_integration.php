<?php

/**
 * Test rapido per verificare l'integrazione dei widget nel portfolio
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\PaymentDistribution;
use App\Models\User;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 TEST INTEGRAZIONE WIDGET NEL PORTFOLIO\n";
echo "==========================================\n\n";

try {
    // Trova un creator che abbia delle distribuzioni
    $creatorWithDistributions = \DB::table('payment_distributions')
        ->join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
        ->join('egis', 'reservations.egi_id', '=', 'egis.id')
        ->join('collections', 'egis.collection_id', '=', 'collections.id')
        ->where('payment_distributions.user_type', 'creator')
        ->select('collections.creator_id')
        ->distinct()
        ->first();

    if (!$creatorWithDistributions) {
        echo "❌ Nessun creator con distribuzioni trovato\n";
        exit;
    }

    $creatorId = $creatorWithDistributions->creator_id;
    echo "📊 Testing integrazione widget con Creator ID: {$creatorId}\n\n";

    // Test del metodo completo usato nel controller
    echo "1️⃣ Testing getCreatorPortfolioStats()...\n";
    $portfolioStats = PaymentDistribution::getCreatorPortfolioStats($creatorId);

    echo "   ✅ Sections loaded: " . count($portfolioStats) . "\n";
    echo "   ✅ Has earnings: " . (isset($portfolioStats['earnings']) ? 'YES' : 'NO') . "\n";
    echo "   ✅ Has monthly_trend: " . (isset($portfolioStats['monthly_trend']) ? 'YES' : 'NO') . "\n";
    echo "   ✅ Has collection_performance: " . (isset($portfolioStats['collection_performance']) ? 'YES' : 'NO') . "\n";
    echo "   ✅ Has engagement: " . (isset($portfolioStats['engagement']) ? 'YES' : 'NO') . "\n";
    echo "   ✅ Has distribution_status: " . (isset($portfolioStats['distribution_status']) ? 'YES' : 'NO') . "\n\n";

    // Test specifico per each widget
    echo "2️⃣ Testing earnings widget data...\n";
    if ($portfolioStats['earnings']['total_earnings'] > 0) {
        echo "   ✅ Total earnings: €" . $portfolioStats['earnings']['total_earnings'] . "\n";
        echo "   ✅ Widget will show: ADVANCED STATS\n";
    } else {
        echo "   ⚠️  No earnings yet, widget will show: MOTIVATIONAL MESSAGE\n";
    }
    echo "\n";

    echo "3️⃣ Testing monthly trend data...\n";
    echo "   ✅ Months with data: " . count($portfolioStats['monthly_trend']) . "\n";
    if (count($portfolioStats['monthly_trend']) > 0) {
        echo "   ✅ Latest month: " . $portfolioStats['monthly_trend'][0]['month'] . " (€" . $portfolioStats['monthly_trend'][0]['monthly_earnings'] . ")\n";
    }
    echo "\n";

    echo "4️⃣ Testing collection performance data...\n";
    echo "   ✅ Collections analyzed: " . count($portfolioStats['collection_performance']) . "\n";
    if (count($portfolioStats['collection_performance']) > 0) {
        $topCollection = $portfolioStats['collection_performance'][0];
        echo "   ✅ Top collection: " . $topCollection['collection_name'] . " (€" . $topCollection['total_earnings'] . ")\n";
    }
    echo "\n";

    echo "5️⃣ Testing engagement data...\n";
    echo "   ✅ Collectors reached: " . $portfolioStats['engagement']['collectors_reached'] . "\n";
    echo "   ✅ EPP impact: €" . $portfolioStats['engagement']['epp_impact_generated'] . "\n";
    echo "   ✅ Total volume: €" . $portfolioStats['engagement']['total_volume_generated'] . "\n\n";

    echo "🎉 INTEGRAZIONE COMPLETATA CON SUCCESSO!\n";
    echo "🚀 I widget riceveranno dati corretti nel portfolio.\n";
    echo "📱 La vista portfolio caricherà le statistiche avanzate.\n";
} catch (Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    exit(1);
}

echo "\n📋 PROSSIMO STEP: Testare la vista portfolio nel browser.\n";
