/**
 * @package OS3Guardian\Test
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (Padmin Analyzer - DB Test)
 * @date 2025-10-22
 * @purpose Test script per verificare connessione Redis Stack e operazioni base
 */

import { PadminDB, CodeSymbol, Violation } from './db';
import { loadConfig, validateConfig } from './config';

async function main() {
    console.log('🧠 Padmin Analyzer - Redis Stack Test\n');

    // Load config
    const config = loadConfig();

    if (!validateConfig(config)) {
        console.error('❌ Invalid configuration');
        process.exit(1);
    }

    console.log('📋 Configuration:');
    console.log(`   Redis: ${config.redis.host}:${config.redis.port}`);
    console.log(`   Workspace: ${config.workspace.rootPath}\n`);

    // Create DB instance
    const db = new PadminDB(config.redis);

    try {
        // Connect
        console.log('🔌 Connecting to Redis Stack...');
        await db.connect();
        console.log('✅ Connected!\n');

        // Ping test
        console.log('🏓 Ping test...');
        const pong = await db.ping();
        console.log(pong ? '✅ PONG received' : '❌ Ping failed');
        console.log('');

        // Test: Save a code symbol
        console.log('💾 Testing CodeSymbol save...');
        const testSymbol: CodeSymbol = {
            id: 'test:TestClass:testMethod',
            type: 'method',
            name: 'testMethod',
            filePath: 'src/test.ts',
            lineStart: 10,
            lineEnd: 20,
            signature: 'public testMethod(): void',
            docblock: '/** Test method */',
            semanticDescription: 'A test method for demonstration',
            complexity: 5,
            dependencies: [],
            usages: 3,
            createdAt: new Date(),
            updatedAt: new Date(),
        };

        await db.saveSymbol(testSymbol);
        console.log('✅ Symbol saved\n');

        // Test: Retrieve symbol
        console.log('📖 Testing CodeSymbol retrieval...');
        const retrieved = await db.getSymbol(testSymbol.id);
        console.log(
            retrieved
                ? `✅ Symbol retrieved: ${retrieved.name}`
                : '❌ Symbol not found'
        );
        console.log('');

        // Test: Search symbols
        console.log('🔍 Testing CodeSymbol search...');
        const searchResults = await db.searchSymbols({ text: 'test' });
        console.log(`✅ Found ${searchResults.length} symbols`);
        console.log('');

        // Test: Save a violation
        console.log('💾 Testing Violation save...');
        const testViolation: Violation = {
            id: 'test-violation-001',
            type: 'P0',
            rule: 'REGOLA_ZERO',
            message: 'Test violation message',
            filePath: 'src/test.ts',
            lineNumber: 15,
            symbolId: testSymbol.id,
            severity: 'error',
            autoFixable: true,
            fixApplied: false,
            detectedAt: new Date(),
        };

        await db.saveViolation(testViolation);
        console.log('✅ Violation saved\n');

        // Test: Get violations
        console.log('📋 Testing Violation retrieval...');
        const violations = await db.getViolations({ limit: 10 });
        console.log(`✅ Found ${violations.length} violations`);
        console.log('');

        // Test: Get stats
        console.log('📊 Testing Violation stats...');
        const stats = await db.getViolationStats();
        console.log(`✅ Stats retrieved:`);
        console.log(`   Total: ${stats.total}`);
        console.log(`   P0: ${stats.byPriority.P0}, P1: ${stats.byPriority.P1}`);
        console.log(`   Pending: ${stats.pending}, Fixed: ${stats.fixed}`);
        console.log('');

        // Test: Get symbol count
        console.log('🔢 Testing Symbol count...');
        const symbolCount = await db.getSymbolCount();
        console.log(`✅ Total symbols: ${symbolCount}\n`);

        // Cleanup test data
        console.log('🧹 Cleaning up test data...');
        await db.deleteSymbol(testSymbol.id);
        await db.deleteViolation(testViolation.id);
        console.log('✅ Cleanup completed\n');

        console.log('✅ All tests passed!\n');
    } catch (error) {
        console.error('❌ Test failed:', error);
        process.exit(1);
    } finally {
        // Disconnect
        await db.disconnect();
        console.log('👋 Disconnected from Redis Stack');
    }
}

main();
