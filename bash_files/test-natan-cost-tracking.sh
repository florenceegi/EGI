#!/bin/bash

# Test N.A.T.A.N. Cost Tracking Fix
# Verifica che i tokens vengano salvati correttamente

echo "🧪 Testing N.A.T.A.N. Cost Tracking Fix..."
echo ""

# 1. Check ultima chat message
echo "📊 Step 1: Verifica ultimo messaggio N.A.T.A.N..."
php artisan tinker --execute="
\$msg = \App\Models\NatanChatMessage::latest()->first();
if (\$msg) {
    echo '✅ Ultimo messaggio trovato:\n';
    echo '  - ID: ' . \$msg->id . '\n';
    echo '  - AI Model: ' . \$msg->ai_model . '\n';
    echo '  - Tokens Input: ' . (\$msg->tokens_input ?? 'NULL ❌') . '\n';
    echo '  - Tokens Output: ' . (\$msg->tokens_output ?? 'NULL ❌') . '\n';
    echo '  - Created: ' . \$msg->created_at . '\n';

    if (\$msg->tokens_input > 0 && \$msg->tokens_output > 0) {
        echo '\n🎉 SUCCESS! Tokens salvati correttamente!\n';
        exit(0);
    } else {
        echo '\n⚠️  WARNING: Tokens NULL. Fai una nuova query N.A.T.A.N. per testare la fix.\n';
        exit(1);
    }
} else {
    echo '⚠️  Nessun messaggio trovato. Fai una query N.A.T.A.N. per testare.\n';
    exit(1);
}
"

echo ""
echo "📈 Step 2: Verifica statistiche dashboard..."
php artisan tinker --execute="
\$service = app(\App\Services\AI\AiCostCalculatorService::class);
\$stats = \$service->getCurrentMonthSpending();

echo '✅ Statistiche mese corrente:\n';
echo '  - Total Cost: $' . \$stats['totals']['cost'] . '\n';
echo '  - Total Messages: ' . \$stats['totals']['messages'] . '\n';
echo '  - Total Tokens: ' . number_format(\$stats['totals']['tokens']) . '\n';
echo '  - Avg Cost/Message: $' . \$stats['totals']['avg_cost_per_message'] . '\n';

echo '\n📊 By Provider:\n';
foreach (\$stats['by_provider'] as \$provider) {
    echo '  - ' . \$provider['provider'] . ': $' . round(\$provider['cost'], 4) . ' (' . \$provider['messages'] . ' msgs)\n';
}

if (\$stats['totals']['cost'] > 0) {
    echo '\n🎉 SUCCESS! Dashboard mostra costi reali!\n';
} else {
    echo '\n⚠️  Dashboard ancora a $0. Potrebbero servire nuove query post-fix.\n';
}
"

echo ""
echo "✅ Test completato!"
echo ""
echo "📝 Note:"
echo "  - Se vedi tokens NULL, fai una nuova query N.A.T.A.N."
echo "  - La fix funziona SOLO per query DOPO il deploy."
echo "  - Query vecchie (pre-fix) hanno tokens_input/output = NULL."
echo ""
echo "🔗 Dashboard: http://localhost/pa/ai-costs"
