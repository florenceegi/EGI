/**
 * @package OS3Guardian\Database
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (Padmin Analyzer - Redis Stack Integration)
 * @date 2025-10-22
 * @purpose Redis Stack database layer per Padmin Analyzer con RediSearch, RedisJSON, vector embeddings
 */

import Redis from 'ioredis';

// ========================================
// TYPES & INTERFACES
// ========================================

export interface RedisConfig {
    host: string;
    port: number;
    password?: string;
    db?: number;
}

export interface CodeSymbol {
    id: string; // Unique: file:class:method or file:function
    type: 'class' | 'function' | 'method' | 'property' | 'constant';
    name: string;
    filePath: string;
    lineStart: number;
    lineEnd: number;
    signature?: string;
    docblock?: string;
    semanticDescription?: string;
    embedding?: number[]; // Vector embedding per semantic search
    complexity?: number;
    dependencies?: string[]; // Array of symbol IDs this depends on
    usages?: number; // Quante volte usato nel codebase
    createdAt: Date;
    updatedAt: Date;
}

export interface Violation {
    id: string; // UUID
    type: 'P0' | 'P1' | 'P2' | 'P3'; // Priority level
    rule: string; // e.g., "REGOLA_ZERO", "UEM_FIRST", "STATISTICS_NO_LIMIT"
    message: string;
    filePath: string;
    lineNumber?: number;
    symbolId?: string; // Related CodeSymbol ID
    severity: 'critical' | 'error' | 'warning' | 'info';
    autoFixable: boolean;
    fixApplied: boolean;
    detectedAt: Date;
    fixedAt?: Date;
    context?: Record<string, unknown>;
}

export interface ViolationStats {
    total: number;
    byPriority: {
        P0: number;
        P1: number;
        P2: number;
        P3: number;
    };
    bySeverity: {
        critical: number;
        error: number;
        warning: number;
        info: number;
    };
    autoFixable: number;
    fixed: number;
    pending: number;
}

export interface SearchQuery {
    text?: string;
    type?: CodeSymbol['type'];
    filePath?: string;
    limit?: number;
    offset?: number;
}

// ========================================
// REDIS STACK DATABASE CLIENT
// ========================================

export class PadminDB {
    private client: Redis;
    private connected: boolean = false;

    // Redis key prefixes
    private readonly SYMBOL_PREFIX = 'padmin:symbol:';
    private readonly VIOLATION_PREFIX = 'padmin:violation:';
    private readonly INDEX_SYMBOLS = 'padmin:idx:symbols';
    private readonly INDEX_VIOLATIONS = 'padmin:idx:violations';
    private readonly STATS_KEY = 'padmin:stats';

    constructor(config: RedisConfig) {
        this.client = new Redis({
            host: config.host,
            port: config.port,
            password: config.password,
            db: config.db || 0,
            lazyConnect: true,
            retryStrategy: (times: number) => {
                const delay = Math.min(times * 50, 2000);
                return delay;
            },
        });

        this.client.on('error', (err) => {
            console.error('[PadminDB] Redis error:', err);
        });

        this.client.on('connect', () => {
            console.log('[PadminDB] Connected to Redis Stack');
            this.connected = true;
        });

        this.client.on('close', () => {
            console.log('[PadminDB] Disconnected from Redis Stack');
            this.connected = false;
        });
    }

    // ========================================
    // CONNECTION MANAGEMENT
    // ========================================

    async connect(): Promise<void> {
        if (this.connected) return;
        await this.client.connect();
        await this.createIndexes();
    }

    async disconnect(): Promise<void> {
        if (!this.connected) return;
        await this.client.quit();
    }

    isConnected(): boolean {
        return this.connected;
    }

    async ping(): Promise<boolean> {
        try {
            const result = await this.client.ping();
            return result === 'PONG';
        } catch {
            return false;
        }
    }

    // ========================================
    // INDEX CREATION (RediSearch)
    // ========================================

    private async createIndexes(): Promise<void> {
        try {
            // Symbols index (per full-text search)
            await this.client.call(
                'FT.CREATE',
                this.INDEX_SYMBOLS,
                'ON',
                'JSON',
                'PREFIX',
                '1',
                this.SYMBOL_PREFIX,
                'SCHEMA',
                '$.type',
                'AS',
                'type',
                'TAG',
                '$.name',
                'AS',
                'name',
                'TEXT',
                'SORTABLE',
                '$.filePath',
                'AS',
                'filePath',
                'TEXT',
                '$.semanticDescription',
                'AS',
                'description',
                'TEXT',
                '$.complexity',
                'AS',
                'complexity',
                'NUMERIC',
                'SORTABLE',
                '$.usages',
                'AS',
                'usages',
                'NUMERIC',
                'SORTABLE',
                '$.createdAt',
                'AS',
                'createdAt',
                'NUMERIC',
                'SORTABLE'
            );
            console.log('[PadminDB] Symbols index created');
        } catch (err: any) {
            if (!err.message?.includes('Index already exists')) {
                console.error('[PadminDB] Failed to create symbols index:', err);
            }
        }

        try {
            // Violations index
            await this.client.call(
                'FT.CREATE',
                this.INDEX_VIOLATIONS,
                'ON',
                'JSON',
                'PREFIX',
                '1',
                this.VIOLATION_PREFIX,
                'SCHEMA',
                '$.type',
                'AS',
                'type',
                'TAG',
                '$.rule',
                'AS',
                'rule',
                'TAG',
                '$.severity',
                'AS',
                'severity',
                'TAG',
                '$.filePath',
                'AS',
                'filePath',
                'TEXT',
                '$.autoFixable',
                'AS',
                'autoFixable',
                'TAG',
                '$.fixApplied',
                'AS',
                'fixApplied',
                'TAG',
                '$.detectedAt',
                'AS',
                'detectedAt',
                'NUMERIC',
                'SORTABLE'
            );
            console.log('[PadminDB] Violations index created');
        } catch (err: any) {
            if (!err.message?.includes('Index already exists')) {
                console.error('[PadminDB] Failed to create violations index:', err);
            }
        }
    }

    // ========================================
    // CODE SYMBOLS CRUD
    // ========================================

    async saveSymbol(symbol: CodeSymbol): Promise<void> {
        const key = `${this.SYMBOL_PREFIX}${symbol.id}`;
        await this.client.call('JSON.SET', key, '$', JSON.stringify(symbol));
    }

    async getSymbol(id: string): Promise<CodeSymbol | null> {
        const key = `${this.SYMBOL_PREFIX}${id}`;
        const result = await this.client.call('JSON.GET', key);
        if (!result) return null;
        return JSON.parse(result as string);
    }

    async deleteSymbol(id: string): Promise<boolean> {
        const key = `${this.SYMBOL_PREFIX}${id}`;
        const deleted = await this.client.del(key);
        return deleted > 0;
    }

    async searchSymbols(query: SearchQuery): Promise<CodeSymbol[]> {
        const limit = query.limit || 50;
        const offset = query.offset || 0;

        let searchQuery = '*';
        const conditions: string[] = [];

        if (query.type) {
            conditions.push(`@type:{${query.type}}`);
        }

        if (query.text) {
            conditions.push(`@name:${query.text}*`);
        }

        if (query.filePath) {
            conditions.push(`@filePath:${query.filePath}*`);
        }

        if (conditions.length > 0) {
            searchQuery = conditions.join(' ');
        }

        try {
            const results = await this.client.call(
                'FT.SEARCH',
                this.INDEX_SYMBOLS,
                searchQuery,
                'LIMIT',
                offset.toString(),
                limit.toString(),
                'SORTBY',
                'usages',
                'DESC'
            );

            if (!Array.isArray(results) || results.length < 2) {
                return [];
            }

            // Results format: [total, doc1Key, doc1Data, doc2Key, doc2Data, ...]
            const symbols: CodeSymbol[] = [];
            for (let i = 2; i < results.length; i += 2) {
                const docData = results[i];
                if (Array.isArray(docData)) {
                    // Find JSON field
                    const jsonIndex = docData.indexOf('$');
                    if (jsonIndex !== -1 && jsonIndex + 1 < docData.length) {
                        const jsonStr = docData[jsonIndex + 1];
                        symbols.push(JSON.parse(jsonStr));
                    }
                }
            }

            return symbols;
        } catch (err) {
            console.error('[PadminDB] Search symbols error:', err);
            return [];
        }
    }

    async getSymbolsByFile(filePath: string): Promise<CodeSymbol[]> {
        return this.searchSymbols({ filePath });
    }

    async getMostUsedSymbols(limit: number = 10): Promise<CodeSymbol[]> {
        return this.searchSymbols({ limit });
    }

    // ========================================
    // VIOLATIONS CRUD
    // ========================================

    async saveViolation(violation: Violation): Promise<void> {
        const key = `${this.VIOLATION_PREFIX}${violation.id}`;
        await this.client.call('JSON.SET', key, '$', JSON.stringify(violation));
        await this.updateViolationStats();
    }

    async getViolation(id: string): Promise<Violation | null> {
        const key = `${this.VIOLATION_PREFIX}${id}`;
        const result = await this.client.call('JSON.GET', key);
        if (!result) return null;
        return JSON.parse(result as string);
    }

    async deleteViolation(id: string): Promise<boolean> {
        const key = `${this.VIOLATION_PREFIX}${id}`;
        const deleted = await this.client.del(key);
        if (deleted > 0) {
            await this.updateViolationStats();
        }
        return deleted > 0;
    }

    async markViolationFixed(id: string): Promise<boolean> {
        const violation = await this.getViolation(id);
        if (!violation) return false;

        violation.fixApplied = true;
        violation.fixedAt = new Date();

        await this.saveViolation(violation);
        return true;
    }

    async getViolations(filters?: {
        type?: Violation['type'];
        severity?: Violation['severity'];
        fixApplied?: boolean;
        limit?: number;
        offset?: number;
    }): Promise<Violation[]> {
        const limit = filters?.limit || 100;
        const offset = filters?.offset || 0;

        let searchQuery = '*';
        const conditions: string[] = [];

        if (filters?.type) {
            conditions.push(`@type:{${filters.type}}`);
        }

        if (filters?.severity) {
            conditions.push(`@severity:{${filters.severity}}`);
        }

        if (filters?.fixApplied !== undefined) {
            conditions.push(`@fixApplied:{${filters.fixApplied}}`);
        }

        if (conditions.length > 0) {
            searchQuery = conditions.join(' ');
        }

        try {
            const results = await this.client.call(
                'FT.SEARCH',
                this.INDEX_VIOLATIONS,
                searchQuery,
                'LIMIT',
                offset.toString(),
                limit.toString(),
                'SORTBY',
                'detectedAt',
                'DESC'
            );

            if (!Array.isArray(results) || results.length < 2) {
                return [];
            }

            const violations: Violation[] = [];
            for (let i = 2; i < results.length; i += 2) {
                const docData = results[i];
                if (Array.isArray(docData)) {
                    const jsonIndex = docData.indexOf('$');
                    if (jsonIndex !== -1 && jsonIndex + 1 < docData.length) {
                        const jsonStr = docData[jsonIndex + 1];
                        violations.push(JSON.parse(jsonStr));
                    }
                }
            }

            return violations;
        } catch (err) {
            console.error('[PadminDB] Get violations error:', err);
            return [];
        }
    }

    async getViolationsByFile(filePath: string): Promise<Violation[]> {
        // Custom search per filePath
        try {
            const results = await this.client.call(
                'FT.SEARCH',
                this.INDEX_VIOLATIONS,
                `@filePath:${filePath}*`,
                'SORTBY',
                'detectedAt',
                'DESC'
            );

            if (!Array.isArray(results) || results.length < 2) {
                return [];
            }

            const violations: Violation[] = [];
            for (let i = 2; i < results.length; i += 2) {
                const docData = results[i];
                if (Array.isArray(docData)) {
                    const jsonIndex = docData.indexOf('$');
                    if (jsonIndex !== -1 && jsonIndex + 1 < docData.length) {
                        const jsonStr = docData[jsonIndex + 1];
                        violations.push(JSON.parse(jsonStr));
                    }
                }
            }

            return violations;
        } catch (err) {
            console.error('[PadminDB] Get violations by file error:', err);
            return [];
        }
    }

    // ========================================
    // STATISTICS
    // ========================================

    private async updateViolationStats(): Promise<void> {
        const allViolations = await this.getViolations({ limit: 10000 });

        const stats: ViolationStats = {
            total: allViolations.length,
            byPriority: { P0: 0, P1: 0, P2: 0, P3: 0 },
            bySeverity: { critical: 0, error: 0, warning: 0, info: 0 },
            autoFixable: 0,
            fixed: 0,
            pending: 0,
        };

        for (const v of allViolations) {
            // Type-safe priority increment
            if (v.type === 'P0') stats.byPriority.P0++;
            else if (v.type === 'P1') stats.byPriority.P1++;
            else if (v.type === 'P2') stats.byPriority.P2++;
            else if (v.type === 'P3') stats.byPriority.P3++;

            // Type-safe severity increment
            if (v.severity === 'critical') stats.bySeverity.critical++;
            else if (v.severity === 'error') stats.bySeverity.error++;
            else if (v.severity === 'warning') stats.bySeverity.warning++;
            else if (v.severity === 'info') stats.bySeverity.info++;

            if (v.autoFixable) stats.autoFixable++;
            if (v.fixApplied) stats.fixed++;
            else stats.pending++;
        }

        await this.client.set(this.STATS_KEY, JSON.stringify(stats));
    }

    async getViolationStats(): Promise<ViolationStats> {
        const cached = await this.client.get(this.STATS_KEY);
        if (cached) {
            return JSON.parse(cached);
        }

        // Rebuild stats
        await this.updateViolationStats();
        const stats = await this.client.get(this.STATS_KEY);
        return stats ? JSON.parse(stats) : this.getEmptyStats();
    }

    private getEmptyStats(): ViolationStats {
        return {
            total: 0,
            byPriority: { P0: 0, P1: 0, P2: 0, P3: 0 },
            bySeverity: { critical: 0, error: 0, warning: 0, info: 0 },
            autoFixable: 0,
            fixed: 0,
            pending: 0,
        };
    }

    async getSymbolCount(): Promise<number> {
        const keys = await this.client.keys(`${this.SYMBOL_PREFIX}*`);
        return keys.length;
    }

    // ========================================
    // BULK OPERATIONS
    // ========================================

    async bulkSaveSymbols(symbols: CodeSymbol[]): Promise<void> {
        const pipeline = this.client.pipeline();

        for (const symbol of symbols) {
            const key = `${this.SYMBOL_PREFIX}${symbol.id}`;
            pipeline.call('JSON.SET', key, '$', JSON.stringify(symbol));
        }

        await pipeline.exec();
    }

    async clearAllSymbols(): Promise<void> {
        const keys = await this.client.keys(`${this.SYMBOL_PREFIX}*`);
        if (keys.length > 0) {
            await this.client.del(...keys);
        }
    }

    async clearAllViolations(): Promise<void> {
        const keys = await this.client.keys(`${this.VIOLATION_PREFIX}*`);
        if (keys.length > 0) {
            await this.client.del(...keys);
        }
        await this.client.del(this.STATS_KEY);
    }

    async flushAll(): Promise<void> {
        await this.client.flushdb();
    }
}

// ========================================
// FACTORY & SINGLETON
// ========================================

let instance: PadminDB | null = null;

export function createPadminDB(config: RedisConfig): PadminDB {
    return new PadminDB(config);
}

export function getPadminDB(config?: RedisConfig): PadminDB {
    if (!instance) {
        if (!config) {
            throw new Error(
                'PadminDB: Config required for first initialization'
            );
        }
        instance = new PadminDB(config);
    }
    return instance;
}

export function resetPadminDBInstance(): void {
    if (instance) {
        instance.disconnect().catch(console.error);
        instance = null;
    }
}
