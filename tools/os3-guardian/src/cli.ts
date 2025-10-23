#!/usr/bin/env node

/**
 * @package @florenceegi/os3-guardian
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Padmin Analyzer CLI)
 * @date 2025-01-22
 * @purpose Command-line interface for Padmin Analyzer operations
 *
 * USAGE:
 * node dist/cli.js <command> [options]
 *
 * COMMANDS:
 * violations:stats               - Get violation statistics
 * violations:list [options]      - List violations with optional filters
 * violations:get --id=<id>       - Get single violation by ID
 * violations:fix --id=<id>       - Mark violation as fixed
 * symbols:search [options]       - Search code symbols
 * symbols:get --id=<id>          - Get single symbol by ID
 * symbols:count                  - Get symbol count
 * health:ping                    - Check Redis Stack connection
 *
 * OPTIONS:
 * --priority=<P0|P1|P2|P3>      - Filter by priority
 * --severity=<critical|error|warning|info> - Filter by severity
 * --type=<violation_type>       - Filter by violation type
 * --isFixed                     - Filter fixed violations (true if present)
 * --limit=<number>              - Limit results
 * --text=<search_text>          - Full-text search query
 * --filePath=<path>             - Filter by file path
 *
 * EXIT CODES:
 * 0 - Success
 * 1 - Invalid command/arguments
 * 2 - Database connection error
 * 3 - Operation failed
 */

import { getPadminDB } from './index.js';

// Parse command-line arguments
const args = process.argv.slice(2);
const command = args[0];

if (!command) {
    console.error(JSON.stringify({
        success: false,
        error: 'No command specified. Use: node cli.js <command> [options]'
    }));
    process.exit(1);
}

// Parse options
const options: Record<string, string | boolean> = {};
for (let i = 1; i < args.length; i++) {
    const arg = args[i];

    if (arg && arg.startsWith('--')) {
        const parts = arg.slice(2).split('=');
        const key = parts[0];
        const value = parts[1];

        if (!key) continue;

        if (value === undefined) {
            // Flag without value (e.g., --isFixed)
            options[key] = true;
        } else {
            // Option with value (e.g., --priority=P0)
            options[key] = value;
        }
    }
}

// Main execution
(async () => {
    try {
        const db = getPadminDB();
        await db.connect();

        let result: any;

        switch (command) {
            // ===== VIOLATIONS =====
            case 'violations:stats':
                result = await db.getViolationStats();
                console.log(JSON.stringify({ success: true, data: result }));
                break;

            case 'violations:list': {
                const filters: any = {};
                if (options.priority) filters.priority = options.priority;
                if (options.severity) filters.severity = options.severity;
                if (options.type) filters.type = options.type;
                if (options.isFixed !== undefined) filters.isFixed = Boolean(options.isFixed);
                if (options.limit) filters.limit = parseInt(options.limit as string, 10);

                const violations = await db.getViolations(filters);
                console.log(JSON.stringify({ success: true, data: violations }));
                break;
            }

            case 'violations:get': {
                if (!options.id) {
                    throw new Error('Missing required option: --id=<violation_id>');
                }

                const violation = await db.getViolation(options.id as string);
                if (!violation) {
                    console.log(JSON.stringify({ success: false, error: 'Violation not found' }));
                    process.exit(3);
                }

                console.log(JSON.stringify({ success: true, data: violation }));
                break;
            }

            case 'violations:fix': {
                if (!options.id) {
                    throw new Error('Missing required option: --id=<violation_id>');
                }

                await db.markViolationFixed(options.id as string);
                console.log(JSON.stringify({ success: true, data: { id: options.id, fixed: true } }));
                break;
            }

            // ===== SYMBOLS =====
            case 'symbols:search': {
                const query: any = {};
                if (options.text) query.text = options.text;
                if (options.type) query.type = options.type;
                if (options.filePath) query.filePath = options.filePath;
                if (options.limit) query.limit = parseInt(options.limit as string, 10);

                const symbols = await db.searchSymbols(query);
                console.log(JSON.stringify({ success: true, data: symbols }));
                break;
            }

            case 'symbols:get': {
                if (!options.id) {
                    throw new Error('Missing required option: --id=<symbol_id>');
                }

                const symbol = await db.getSymbol(options.id as string);
                if (!symbol) {
                    console.log(JSON.stringify({ success: false, error: 'Symbol not found' }));
                    process.exit(3);
                }

                console.log(JSON.stringify({ success: true, data: symbol }));
                break;
            }

            case 'symbols:count': {
                const count = await db.getSymbolCount();
                console.log(JSON.stringify({ success: true, data: { count } }));
                break;
            }

            // ===== HEALTH =====
            case 'health:ping': {
                const pong = await db.ping();
                console.log(JSON.stringify({ success: pong, data: { status: pong ? 'OK' : 'FAILED' } }));
                break;
            }

            // ===== UNKNOWN COMMAND =====
            default:
                console.error(JSON.stringify({
                    success: false,
                    error: `Unknown command: ${command}. See --help for available commands.`
                }));
                process.exit(1);
        }

        await db.disconnect();
        process.exit(0);

    } catch (error: any) {
        // Handle database connection errors
        if (error.message?.includes('ECONNREFUSED') || error.message?.includes('Redis')) {
            console.error(JSON.stringify({
                success: false,
                error: 'Redis Stack connection failed. Ensure service is running on port 6381.',
                details: error.message
            }));
            process.exit(2);
        }

        // Handle other errors
        console.error(JSON.stringify({
            success: false,
            error: error.message || 'Unknown error',
            stack: error.stack
        }));
        process.exit(3);
    }
})();
