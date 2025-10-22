/**
 * @package OS3 Guardian / Padmin Analyzer
 * @author Padmin D. Curtis (AI Partner OS3)
 * @version 1.0.0 (FlorenceEGI - Example Usage)
 * @date 2025-10-22
 * @purpose Esempio pratico di utilizzo del PadminDB
 */

import { padminDB } from './index.js';

/**
 * Esempio completo di utilizzo del database cognitivo
 */
async function main() {
    console.log('🧠 Padmin Analyzer - Database Cognitivo Demo\n');

    try {
        // 1. CONNESSIONE
        console.log('📡 Connecting to Redis Stack...');
        await padminDB.connect();
        console.log('✅ Connected!\n');

        // 2. SALVA SIMBOLI
        console.log('💾 Saving symbols...');

        await padminDB.saveSymbol('App\\Services\\Gdpr\\ConsentService', {
            fqcn: 'App\\Services\\Gdpr\\ConsentService',
            type: 'class',
            summary: 'Gestisce il consenso GDPR degli utenti per il trattamento dati personali',
            file: 'app/Services/Gdpr/ConsentService.php',
            line: 15,
            inputs: JSON.stringify(['User', 'string']),
            outputs: 'bool',
            deps: 'App\\Models\\User,App\\Models\\Consent',
            updated_at: Date.now()
        });

        await padminDB.saveSymbol('App\\Services\\Gdpr\\AuditLogService', {
            fqcn: 'App\\Services\\Gdpr\\AuditLogService',
            type: 'class',
            summary: 'Registra tutte le attività GDPR in audit trail per compliance',
            file: 'app/Services/Gdpr/AuditLogService.php',
            line: 20,
            inputs: JSON.stringify(['User', 'GdprActivityCategory', 'string', 'array']),
            outputs: 'UserActivity',
            deps: 'App\\Models\\User,App\\Models\\UserActivity',
            updated_at: Date.now()
        });

        await padminDB.saveSymbol('App\\Services\\StatisticsService', {
            fqcn: 'App\\Services\\StatisticsService',
            type: 'class',
            summary: 'Calcola statistiche aggregate per dashboard amministrativa',
            file: 'app/Services/StatisticsService.php',
            line: 10,
            outputs: 'Collection',
            updated_at: Date.now()
        });

        console.log('✅ Symbols saved!\n');

        // 3. RECUPERA SIMBOLO
        console.log('🔍 Retrieving symbol...');
        const consent = await padminDB.getSymbol('App\\Services\\Gdpr\\ConsentService');
        if (consent) {
            console.log(`  FQCN: ${consent.fqcn}`);
            console.log(`  Type: ${consent.type}`);
            console.log(`  Summary: ${consent.summary}`);
            console.log(`  File: ${consent.file}:${consent.line}`);
            console.log(`  Deps: ${consent.deps}\n`);
        }

        // 4. REGISTRA VIOLAZIONI
        console.log('⚠️ Recording violations...');

        await padminDB.recordViolation(
            'App\\Services\\StatisticsService',
            'STATISTICS',
            'P0',
            'Uso di ->take(10) senza parametrizzazione esplicita nel method signature',
            {
                file: 'app/Services/StatisticsService.php',
                line: 42,
                fix_suggestion: 'Modificare method signature: public function getTopEgis(?int $limit = null): Collection'
            }
        );

        await padminDB.recordViolation(
            'App\\Http\\Controllers\\ProfileController',
            'UEM_FIRST',
            'P0',
            'Sostituito errorManager->handle() con logger->error()',
            {
                file: 'app/Http/Controllers/ProfileController.php',
                line: 87,
                fix_suggestion: 'Ripristinare errorManager->handle() e aggiungere ULM per debug'
            }
        );

        await padminDB.recordViolation(
            'App\\Services\\UserService',
            'GDPR',
            'P1',
            'Modifica dati personali senza check consenso',
            {
                file: 'app/Services/UserService.php',
                line: 125,
                fix_suggestion: 'Aggiungere: if (!$this->consentService->hasConsent($user, "allow-personal-data-processing")) return;'
            }
        );

        console.log('✅ Violations recorded!\n');

        // 5. RECUPERA VIOLAZIONI
        console.log('📋 Recent violations (P0 only):');
        const p0Violations = await padminDB.getRecentViolations(10, 'P0');

        p0Violations.forEach((v, i) => {
            console.log(`\n  ${i + 1}. [${v.severity}] ${v.rule}`);
            console.log(`     Symbol: ${v.symbol}`);
            console.log(`     Message: ${v.message}`);
            console.log(`     Location: ${v.file}:${v.line}`);
            if (v.fix_suggestion) {
                console.log(`     Fix: ${v.fix_suggestion}`);
            }
        });

        console.log('\n');

        // 6. QUEUE UPDATES
        console.log('📤 Queueing symbols for update...');
        await padminDB.queueUpdate('App\\Services\\ConsentService');
        await padminDB.queueUpdate('App\\Services\\AuditLogService');
        console.log('✅ Symbols queued!\n');

        // 7. DEQUEUE
        console.log('📥 Processing queue...');
        let symbol = await padminDB.dequeueUpdate();
        while (symbol) {
            console.log(`  Processing: ${symbol}`);
            symbol = await padminDB.dequeueUpdate();
        }
        console.log('✅ Queue processed!\n');

        // 8. STATISTICHE
        console.log('📊 Database Statistics:');
        const stats = await padminDB.getStats();

        console.log(`  Total Symbols: ${stats.totalSymbols}`);
        console.log(`  Total Violations: ${stats.totalViolations}`);
        console.log(`  Last Update: ${new Date(stats.lastUpdate).toISOString()}`);

        console.log('\n  Symbols by Type:');
        Object.entries(stats.symbolsByType).forEach(([type, count]) => {
            if (count > 0) {
                console.log(`    ${type}: ${count}`);
            }
        });

        console.log('\n  Violations by Severity:');
        Object.entries(stats.violationsBySeverity).forEach(([severity, count]) => {
            if (count > 0) {
                console.log(`    ${severity}: ${count}`);
            }
        });

        console.log('\n');

        // 9. DISCONNESSIONE
        console.log('🔌 Disconnecting...');
        await padminDB.disconnect();
        console.log('✅ Disconnected!\n');

        console.log('🎉 Demo completed successfully!');

    } catch (error) {
        console.error('❌ Error:', error instanceof Error ? error.message : String(error));

        // Cleanup
        if (padminDB.isConnected()) {
            await padminDB.disconnect();
        }

        process.exit(1);
    }
}

// Esegui demo
main();
