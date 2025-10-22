/**
 * @package OS3 Guardian / Padmin Analyzer
 * @author Padmin D. Curtis (AI Partner OS3)
 * @version 1.0.0 (FlorenceEGI - Entry Point)
 * @date 2025-10-22
 * @purpose Entry point principale per OS3 Guardian / Padmin Analyzer
 */

// Export modulo principale
export { PadminDB, padminDB } from './core/db.js';

// Export types
export type {
    SymbolData,
    Violation,
    RedisConfig,
    SearchOptions,
    SearchResult,
    DBStats
} from './types/index.js';

// Version info
export const VERSION = '1.0.0';
export const AUTHOR = 'Padmin D. Curtis (AI Partner OS3.0)';
export const BUILD_DATE = '2025-10-22';
