/**
 * @package OS3 Guardian / Padmin Analyzer
 * @author Padmin D. Curtis (AI Partner OS3)
 * @version 1.0.0 (FlorenceEGI - Entry Point)
 * @date 2025-10-22
 * @purpose Entry point principale per OS3 Guardian / Padmin Analyzer
 */

// Export database module
export {
    PadminDB,
    createPadminDB,
    getPadminDB,
    resetPadminDBInstance,
} from './db';

// Export types
export type {
    RedisConfig,
    CodeSymbol,
    Violation,
    ViolationStats,
    SearchQuery,
} from './db';

// Export configuration
export { loadConfig, validateConfig } from './config';
export type { PadminConfig } from './config';

// Version info
export const VERSION = '1.0.0';
export const AUTHOR = 'Padmin D. Curtis (AI Partner OS3.0)';
export const BUILD_DATE = '2025-10-22';
