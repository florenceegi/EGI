/**
 * @package OS3 Guardian / Padmin Analyzer
 * @author Padmin D. Curtis (AI Partner OS3)
 * @version 1.0.0 (FlorenceEGI - PadminDB Kernel)
 * @date 2025-10-22
 * @purpose Gestisce il database cognitivo semantico di FlorenceEGI (Redis Stack)
 */

import Redis from 'ioredis';
import type {
    SymbolData,
    Violation,
    RedisConfig,
    SearchOptions,
    SearchResult,
    DBStats
} from '../types/index.js';

/**
 * PadminDB - Database Cognitivo Semantico
 *
 * Gestisce l'indicizzazione, la ricerca e il tracking di:
 * - Simboli del codice (classi, metodi, funzioni)
 * - Violazioni OS3 rilevate
 * - Relazioni e dipendenze semantiche
 * - Vettori per ricerca concettuale
 *
 * Backend: Redis Stack (con supporto futuro per Postgres + pgvector)
 *
 * @example
 * ```typescript
 * const db = new PadminDB();
 * await db.connect();
 * await db.saveSymbol('App\\Services\\ConsentService', {
 *   fqcn: 'App\\Services\\ConsentService',
 *   type: 'class',
 *   summary: 'Gestisce il consenso GDPR degli utenti',
 *   file: 'app/Services/Gdpr/ConsentService.php',
 *   line: 10,
 *   updated_at: Date.now()
 * });
 * ```
 */
export class PadminDB {
    private client: Redis | null = null;
    private config: RedisConfig;
    private connected: boolean = false;
    private reconnectAttempts: number = 0;
    private readonly MAX_RECONNECT_ATTEMPTS = 5;
    private readonly TTL_SYMBOL = 86400; // 24 ore in secondi
    private readonly TTL_VIOLATION = 604800; // 7 giorni in secondi

    /**
     * Costruttore PadminDB
     *
     * @param config - Configurazione connessione Redis (opzionale, usa env di default)
     */
    constructor(config?: Partial<RedisConfig>) {
        this.config = {
            host: config?.host || process.env.REDIS_HOST || 'localhost',
            port: config?.port || parseInt(process.env.REDIS_PORT || '6379'),
            password: config?.password || process.env.REDIS_PASSWORD,
            db: config?.db || parseInt(process.env.REDIS_DB || '0'),
            keyPrefix: config?.keyPrefix || 'padmin:',
            retryStrategy: config?.retryStrategy || this.defaultRetryStrategy.bind(this)
        };

        this.log('info', 'PadminDB initialized', { config: this.sanitizeConfig() });
    }

    /**
     * Strategia di retry per la connessione Redis
     *
     * @param times - Numero di tentativi falliti
     * @returns Millisecondi da attendere prima del prossimo tentativo, o void per fermarsi
     */
    private defaultRetryStrategy(times: number): number | void {
        if (times > this.MAX_RECONNECT_ATTEMPTS) {
            this.log('error', 'Max reconnection attempts reached', { attempts: times });
            return undefined; // Stop retrying
        }

        const delay = Math.min(times * 1000, 10000); // Max 10 secondi
        this.log('warn', 'Retrying connection', { attempt: times, delayMs: delay });
        return delay;
    }

    /**
     * Sanitizza la config per il logging (rimuove password)
     */
    private sanitizeConfig(): Record<string, any> {
        return {
            host: this.config.host,
            port: this.config.port,
            db: this.config.db,
            keyPrefix: this.config.keyPrefix,
            hasPassword: !!this.config.password
        };
    }

    /**
     * Logger ULM-style (simulato)
     *
     * In produzione, questo si integrerà con UltraLogManager di Laravel
     *
     * @param level - Livello di log
     * @param message - Messaggio
     * @param context - Contesto aggiuntivo
     */
    private log(level: 'info' | 'warn' | 'error' | 'debug', message: string, context?: Record<string, any>): void {
        const timestamp = new Date().toISOString();
        const logEntry = {
            timestamp,
            level: level.toUpperCase(),
            service: 'PadminDB',
            message,
            ...context
        };

        // In produzione, inviare a UltraLogManager via HTTP API
        console.log(JSON.stringify(logEntry));
    }

    /**
     * Connessione a Redis Stack
     *
     * Stabilisce la connessione con retry automatico e gestione errori.
     * DEVE essere chiamato prima di qualsiasi operazione.
     *
     * @throws Error se la connessione fallisce dopo MAX_RECONNECT_ATTEMPTS
     * @returns Promise<void>
     */
    async connect(): Promise<void> {
        if (this.connected && this.client) {
            this.log('debug', 'Already connected to Redis');
            return;
        }

        this.log('info', 'Connecting to Redis Stack', { config: this.sanitizeConfig() });

        try {
            this.client = new Redis({
                host: this.config.host,
                port: this.config.port,
                password: this.config.password,
                db: this.config.db,
                keyPrefix: this.config.keyPrefix,
                retryStrategy: this.config.retryStrategy,
                lazyConnect: false,
                enableReadyCheck: true,
                maxRetriesPerRequest: 3
            });

            // Event handlers
            this.client.on('connect', () => {
                this.log('info', 'Redis connection established');
                this.connected = true;
                this.reconnectAttempts = 0;
            });

            this.client.on('error', (err) => {
                this.log('error', 'Redis error', { error: err.message, stack: err.stack });
                this.connected = false;
            });

            this.client.on('close', () => {
                this.log('warn', 'Redis connection closed');
                this.connected = false;
            });

            this.client.on('reconnecting', (delay: number) => {
                this.reconnectAttempts++;
                this.log('info', 'Redis reconnecting', { attempt: this.reconnectAttempts, delayMs: delay });
            });

            // Attendi connessione effettiva
            await this.client.ping();
            this.log('info', 'Redis PING successful - connection ready');

        } catch (error) {
            this.log('error', 'Failed to connect to Redis', {
                error: error instanceof Error ? error.message : String(error)
            });
            throw new Error(`PadminDB connection failed: ${error instanceof Error ? error.message : String(error)}`);
        }
    }

    /**
     * Disconnessione da Redis
     *
     * Chiude gracefully la connessione e rilascia le risorse.
     *
     * @returns Promise<void>
     */
    async disconnect(): Promise<void> {
        if (!this.client) {
            this.log('debug', 'No active connection to disconnect');
            return;
        }

        this.log('info', 'Disconnecting from Redis');

        try {
            await this.client.quit();
            this.client = null;
            this.connected = false;
            this.log('info', 'Redis disconnected successfully');
        } catch (error) {
            this.log('error', 'Error during disconnect', {
                error: error instanceof Error ? error.message : String(error)
            });
            // Force disconnect (safe)
            try {
                this.client?.disconnect();
            } catch (e) {
                // ignore
            }
            this.client = null;
            this.connected = false;
        }
    }

    /**
     * Verifica se la connessione è attiva
     *
     * @returns boolean
     */
    isConnected(): boolean {
        return this.connected && this.client !== null;
    }

    /**
     * Valida la connessione attiva, altrimenti throw
     *
     * @throws Error se non connesso
     */
    private ensureConnected(): void {
        if (!this.isConnected()) {
            throw new Error('PadminDB not connected. Call connect() first.');
        }
    }

    /**
     * Salva o aggiorna un simbolo nel database cognitivo
     *
     * Schema Redis:
     * Key: symbol:{fqcn}
     * Type: Hash
     * TTL: 24 ore (auto-refresh su update)
     *
     * @param fqcn - Fully Qualified Class Name
     * @param data - Dati del simbolo
     * @returns Promise<void>
     * @throws Error se validazione fallisce o operazione Redis fallisce
     *
     * @example
     * ```typescript
     * await db.saveSymbol('App\\Services\\ConsentService', {
     *   fqcn: 'App\\Services\\ConsentService',
     *   type: 'class',
     *   summary: 'Gestisce consensi GDPR',
     *   file: 'app/Services/Gdpr/ConsentService.php',
     *   line: 15,
     *   updated_at: Date.now()
     * });
     * ```
     */
    async saveSymbol(fqcn: string, data: SymbolData): Promise<void> {
        this.ensureConnected();

        // Validazione input
        if (!fqcn || typeof fqcn !== 'string') {
            throw new Error('Invalid FQCN: must be a non-empty string');
        }

        if (!data.type || !['class', 'method', 'function', 'interface', 'trait'].includes(data.type)) {
            throw new Error(`Invalid symbol type: ${data.type}`);
        }

        if (!data.summary || typeof data.summary !== 'string') {
            throw new Error('Invalid summary: must be a non-empty string');
        }

        this.log('info', 'Saving symbol', { fqcn, type: data.type });

        try {
            const key = `symbol:${fqcn}`;

            // Prepara dati per Redis Hash
            const hashData: Record<string, string> = {
                fqcn: data.fqcn,
                type: data.type,
                summary: data.summary,
                file: data.file,
                line: String(data.line),
                updated_at: String(data.updated_at)
            };

            // Campi opzionali
            if (data.inputs) hashData.inputs = data.inputs;
            if (data.outputs) hashData.outputs = data.outputs;
            if (data.deps) hashData.deps = data.deps;
            if (data.vector) hashData.vector = data.vector;
            if (data.hash) hashData.hash = data.hash;
            if (data.metadata) hashData.metadata = JSON.stringify(data.metadata);

            // Salva in Redis
            await this.client!.hset(key, hashData);

            // Imposta TTL
            await this.client!.expire(key, this.TTL_SYMBOL);

            // Aggiungi a set di simboli per tipo (per statistics)
            await this.client!.sadd(`symbols:by_type:${data.type}`, fqcn);

            // Aggiungi a sorted set globale (ordinato per timestamp)
            await this.client!.zadd('symbols:all', data.updated_at, fqcn);

            this.log('info', 'Symbol saved successfully', { fqcn, key });

        } catch (error) {
            this.log('error', 'Failed to save symbol', {
                fqcn,
                error: error instanceof Error ? error.message : String(error)
            });
            throw new Error(`Failed to save symbol ${fqcn}: ${error instanceof Error ? error.message : String(error)}`);
        }
    }

    /**
     * Recupera un simbolo dal database cognitivo
     *
     * @param fqcn - Fully Qualified Class Name
     * @returns Promise<SymbolData | null> - Dati simbolo o null se non trovato
     * @throws Error se operazione Redis fallisce
     *
     * @example
     * ```typescript
     * const symbol = await db.getSymbol('App\\Services\\ConsentService');
     * if (symbol) {
     *   console.log(symbol.summary);
     * }
     * ```
     */
    async getSymbol(fqcn: string): Promise<SymbolData | null> {
        this.ensureConnected();

        if (!fqcn || typeof fqcn !== 'string') {
            throw new Error('Invalid FQCN: must be a non-empty string');
        }

        this.log('debug', 'Retrieving symbol', { fqcn });

        try {
            const key = `symbol:${fqcn}`;

            const data = await this.client!.hgetall(key);

            if (!data || Object.keys(data).length === 0) {
                this.log('debug', 'Symbol not found', { fqcn });
                return null;
            }

            // Ensure mandatory fields exist; otherwise treat as not found
            if (!data.fqcn || !data.summary || !data.file || !data.line || !data.updated_at) {
                this.log('warn', 'Symbol hash missing mandatory fields, skipping', { fqcn, fields: Object.keys(data) });
                return null;
            }

            // Ricostruisci SymbolData con fallback sicuri
            const symbol: SymbolData = {
                fqcn: String(data.fqcn),
                type: (data.type as SymbolData['type']) || 'class',
                summary: String(data.summary),
                file: String(data.file),
                line: parseInt(data.line || '0'),
                updated_at: parseInt(data.updated_at || '0'),
                inputs: data.inputs,
                outputs: data.outputs,
                deps: data.deps,
                vector: data.vector,
                hash: data.hash,
                metadata: data.metadata ? JSON.parse(data.metadata) : undefined
            };

            this.log('debug', 'Symbol retrieved successfully', { fqcn });
            return symbol;

        } catch (error) {
            this.log('error', 'Failed to retrieve symbol', {
                fqcn,
                error: error instanceof Error ? error.message : String(error)
            });
            throw new Error(`Failed to retrieve symbol ${fqcn}: ${error instanceof Error ? error.message : String(error)}`);
        }
    }

    /**
     * Ricerca simboli simili tramite similarità vettoriale
     *
     * NOTA: Implementazione mockup con cosine similarity simulato.
     * In produzione, usare RediSearch + vector similarity o pgvector.
     *
     * @param vector - Vettore di embedding (serializzato JSON array)
     * @param options - Opzioni di ricerca
     * @returns Promise<SearchResult[]> - Array di risultati ordinati per similarità
     * @throws Error se operazione fallisce
     *
     * @example
     * ```typescript
     * const vector = JSON.stringify([0.1, 0.2, 0.3, ...]); // 1536 dimensioni
     * const results = await db.findSimilarSymbols(vector, { limit: 5, minSimilarity: 0.8 });
     * results.forEach(r => console.log(r.symbol.fqcn, r.similarity));
     * ```
     */
    async findSimilarSymbols(vector: string, options: SearchOptions = {}): Promise<SearchResult[]> {
        this.ensureConnected();

        const limit = options.limit || 10;
        const minSimilarity = options.minSimilarity || 0.0;

        this.log('info', 'Finding similar symbols', { limit, minSimilarity, hasFilters: !!options.filters });

        try {
            // Recupera tutti i simboli (in produzione, usare RediSearch query)
            const allSymbolKeys = await this.client!.zrange('symbols:all', 0, -1);

            if (allSymbolKeys.length === 0) {
                this.log('debug', 'No symbols in database');
                return [];
            }

            const results: SearchResult[] = [];
            const queryVector = JSON.parse(vector) as number[];

            // Per ogni simbolo, calcola similarità (MOCK - in produzione usare vector search nativo)
            for (const fqcn of allSymbolKeys) {
                const symbol = await this.getSymbol(fqcn);

                if (!symbol || !symbol.vector) continue;

                // Applica filtri
                if (options.filters) {
                    if (options.filters.type && symbol.type !== options.filters.type) continue;
                    if (options.filters.file && !symbol.file.includes(options.filters.file)) continue;
                }

                // Calcola similarità coseno (MOCK)
                const symbolVector = JSON.parse(symbol.vector) as number[];
                const similarity = this.cosineSimilarity(queryVector, symbolVector);

                if (similarity >= minSimilarity) {
                    results.push({ symbol, similarity });
                }
            }

            // Ordina per similarità decrescente e limita
            results.sort((a, b) => b.similarity - a.similarity);
            const limitedResults = results.slice(0, limit);

            this.log('info', 'Similar symbols found', { count: limitedResults.length, total: allSymbolKeys.length });
            return limitedResults;

        } catch (error) {
            this.log('error', 'Failed to find similar symbols', {
                error: error instanceof Error ? error.message : String(error)
            });
            throw new Error(`Failed to find similar symbols: ${error instanceof Error ? error.message : String(error)}`);
        }
    }

    /**
     * Calcola similarità coseno tra due vettori (MOCK implementation)
     *
     * @param vecA - Primo vettore
     * @param vecB - Secondo vettore
     * @returns number - Similarità tra 0 e 1
     */
    private cosineSimilarity(vecA: number[], vecB: number[]): number {
        if (!vecA || !vecB) return 0;
        if (vecA.length !== vecB.length) return 0;

        let dotProduct = 0;
        let normA = 0;
        let normB = 0;

        for (let i = 0; i < vecA.length; i++) {
            const a = vecA[i] ?? 0;
            const b = vecB[i] ?? 0;
            dotProduct += a * b;
            normA += a * a;
            normB += b * b;
        }

        if (normA === 0 || normB === 0) return 0;

        return dotProduct / (Math.sqrt(normA) * Math.sqrt(normB));
    }

    /**
     * Registra una violazione OS3 rilevata
     *
     * Schema Redis:
     * Key: violation:{timestamp}:{symbol}
     * Type: Hash
     * Inoltre aggiunge a sorted set violations:recent per query temporali
     *
     * @param symbol - FQCN del simbolo che viola
     * @param rule - Identificativo regola violata
     * @param severity - Livello di gravità (P0-P3)
     * @param message - Messaggio descrittivo
     * @param metadata - Metadati aggiuntivi (file, line, fix_suggestion)
     * @returns Promise<void>
     * @throws Error se validazione fallisce o operazione Redis fallisce
     *
     * @example
     * ```typescript
     * await db.recordViolation(
     *   'App\\Services\\StatisticsService',
     *   'STATISTICS',
     *   'P0',
     *   'Uso di ->take() senza parametrizzazione',
     *   { file: 'app/Services/StatisticsService.php', line: 42 }
     * );
     * ```
     */
    async recordViolation(
        symbol: string,
        rule: string,
        severity: 'P0' | 'P1' | 'P2' | 'P3',
        message: string,
        metadata?: { file?: string; line?: number; fix_suggestion?: string }
    ): Promise<void> {
        this.ensureConnected();

        // Validazione input
        if (!symbol || typeof symbol !== 'string') {
            throw new Error('Invalid symbol: must be a non-empty string');
        }

        if (!rule || typeof rule !== 'string') {
            throw new Error('Invalid rule: must be a non-empty string');
        }

        if (!['P0', 'P1', 'P2', 'P3'].includes(severity)) {
            throw new Error(`Invalid severity: ${severity}`);
        }

        if (!message || typeof message !== 'string') {
            throw new Error('Invalid message: must be a non-empty string');
        }

        this.log('warn', 'Recording violation', { symbol, rule, severity });

        try {
            const timestamp = Date.now();
            const key = `violation:${timestamp}:${symbol.replace(/\\/g, '.')}`;

            const violationData: Record<string, string> = {
                symbol,
                rule,
                severity,
                message,
                timestamp: String(timestamp),
                status: 'open',
                file: metadata?.file || '',
                line: metadata?.line ? String(metadata.line) : '0',
                fix_suggestion: metadata?.fix_suggestion || ''
            };

            // Salva violation
            await this.client!.hset(key, violationData);
            await this.client!.expire(key, this.TTL_VIOLATION);

            // Aggiungi a sorted set per query temporali
            await this.client!.zadd('violations:recent', timestamp, key);

            // Aggiungi a set per severity
            await this.client!.sadd(`violations:by_severity:${severity}`, key);

            // Aggiungi a set per rule
            await this.client!.sadd(`violations:by_rule:${rule}`, key);

            // Incrementa contatore violazioni per simbolo
            await this.client!.hincrby(`stats:violations:by_symbol`, symbol, 1);

            this.log('warn', 'Violation recorded successfully', { key, symbol, rule, severity });

        } catch (error) {
            this.log('error', 'Failed to record violation', {
                symbol,
                rule,
                error: error instanceof Error ? error.message : String(error)
            });
            throw new Error(`Failed to record violation: ${error instanceof Error ? error.message : String(error)}`);
        }
    }

    /**
     * Recupera violazioni recenti
     *
     * @param limit - Numero massimo di risultati (default: 100)
     * @param severity - Filtra per severità (opzionale)
     * @returns Promise<Violation[]> - Array di violazioni ordinate per timestamp decrescente
     * @throws Error se operazione fallisce
     *
     * @example
     * ```typescript
     * const violations = await db.getRecentViolations(50, 'P0');
     * violations.forEach(v => console.log(v.symbol, v.message));
     * ```
     */
    async getRecentViolations(limit: number = 100, severity?: 'P0' | 'P1' | 'P2' | 'P3'): Promise<Violation[]> {
        this.ensureConnected();

        this.log('info', 'Retrieving recent violations', { limit, severity });

        try {
            let violationKeys: string[];

            if (severity) {
                // Filtra per severity
                violationKeys = await this.client!.smembers(`violations:by_severity:${severity}`);
                // Ordina per timestamp (parsing dalla key)
                violationKeys.sort((a, b) => {
                    const partsA = a.split(':');
                    const partsB = b.split(':');
                    const tsA = partsA[1] ? parseInt(partsA[1]) : 0;
                    const tsB = partsB[1] ? parseInt(partsB[1]) : 0;
                    return tsB - tsA; // Decrescente
                });
                violationKeys = violationKeys.slice(0, limit);
            } else {
                // Recupera da sorted set (già ordinato per timestamp)
                violationKeys = await this.client!.zrevrange('violations:recent', 0, limit - 1);
            }

            const violations: Violation[] = [];

            for (const key of violationKeys) {
                const data = await this.client!.hgetall(key);

                if (!data || Object.keys(data).length === 0) continue;

                // Skip if mandatory fields missing
                if (!data.symbol || !data.rule || !data.message || !data.file) continue;

                violations.push({
                    symbol: String(data.symbol),
                    rule: String(data.rule),
                    severity: (data.severity as Violation['severity']) || 'P3',
                    message: String(data.message),
                    file: String(data.file),
                    line: parseInt(data.line || '0') || 0,
                    timestamp: parseInt(data.timestamp || '0'),
                    fix_suggestion: data.fix_suggestion || undefined,
                    status: (data.status as Violation['status']) || 'open'
                });
            } this.log('info', 'Violations retrieved', { count: violations.length });
            return violations;

        } catch (error) {
            this.log('error', 'Failed to retrieve violations', {
                error: error instanceof Error ? error.message : String(error)
            });
            throw new Error(`Failed to retrieve violations: ${error instanceof Error ? error.message : String(error)}`);
        }
    }

    /**
     * Accoda un simbolo per aggiornamento asincrono
     *
     * Schema Redis:
     * Key: queue:update
     * Type: List (FIFO)
     *
     * Usato per aggiornamenti batch o asincroni del database cognitivo.
     *
     * @param symbol - FQCN del simbolo da aggiornare
     * @returns Promise<void>
     * @throws Error se operazione fallisce
     *
     * @example
     * ```typescript
     * await db.queueUpdate('App\\Services\\ConsentService');
     * ```
     */
    async queueUpdate(symbol: string): Promise<void> {
        this.ensureConnected();

        if (!symbol || typeof symbol !== 'string') {
            throw new Error('Invalid symbol: must be a non-empty string');
        }

        this.log('debug', 'Queueing symbol for update', { symbol });

        try {
            await this.client!.rpush('queue:update', symbol);
            this.log('debug', 'Symbol queued successfully', { symbol });

        } catch (error) {
            this.log('error', 'Failed to queue symbol', {
                symbol,
                error: error instanceof Error ? error.message : String(error)
            });
            throw new Error(`Failed to queue symbol: ${error instanceof Error ? error.message : String(error)}`);
        }
    }

    /**
     * Recupera il prossimo simbolo dalla coda di aggiornamento
     *
     * @returns Promise<string | null> - FQCN del simbolo o null se coda vuota
     * @throws Error se operazione fallisce
     *
     * @example
     * ```typescript
     * const symbol = await db.dequeueUpdate();
     * if (symbol) {
     *   // Processa aggiornamento...
     * }
     * ```
     */
    async dequeueUpdate(): Promise<string | null> {
        this.ensureConnected();

        try {
            const symbol = await this.client!.lpop('queue:update');

            if (symbol) {
                this.log('debug', 'Symbol dequeued', { symbol });
            }

            return symbol;

        } catch (error) {
            this.log('error', 'Failed to dequeue symbol', {
                error: error instanceof Error ? error.message : String(error)
            });
            throw new Error(`Failed to dequeue symbol: ${error instanceof Error ? error.message : String(error)}`);
        }
    }

    /**
     * Recupera statistiche del database cognitivo
     *
     * @returns Promise<DBStats> - Statistiche aggregate
     * @throws Error se operazione fallisce
     *
     * @example
     * ```typescript
     * const stats = await db.getStats();
     * console.log(`Total symbols: ${stats.totalSymbols}`);
     * console.log(`P0 violations: ${stats.violationsBySeverity.P0}`);
     * ```
     */
    async getStats(): Promise<DBStats> {
        this.ensureConnected();

        this.log('info', 'Retrieving database statistics');

        try {
            // Conta simboli totali
            const totalSymbols = await this.client!.zcard('symbols:all');

            // Conta simboli per tipo
            const symbolTypes = ['class', 'method', 'function', 'interface', 'trait'];
            const symbolsByType: Record<string, number> = {};

            for (const type of symbolTypes) {
                const count = await this.client!.scard(`symbols:by_type:${type}`);
                symbolsByType[type] = count;
            }

            // Conta violazioni per severity
            const severities: Array<'P0' | 'P1' | 'P2' | 'P3'> = ['P0', 'P1', 'P2', 'P3'];
            const violationsBySeverity: Record<string, number> = {};
            let totalViolations = 0;

            for (const severity of severities) {
                const count = await this.client!.scard(`violations:by_severity:${severity}`);
                violationsBySeverity[severity] = count;
                totalViolations += count;
            }

            // Timestamp ultimo aggiornamento (max timestamp da symbols:all)
            const recentSymbols = await this.client!.zrevrange('symbols:all', 0, 0, 'WITHSCORES');
            const lastUpdate = recentSymbols.length > 1 ? parseInt(recentSymbols[1] || '0') : 0;

            const stats: DBStats = {
                totalSymbols,
                totalViolations,
                lastUpdate,
                symbolsByType,
                violationsBySeverity
            };

            this.log('info', 'Statistics retrieved', stats);
            return stats;

        } catch (error) {
            this.log('error', 'Failed to retrieve statistics', {
                error: error instanceof Error ? error.message : String(error)
            });
            throw new Error(`Failed to retrieve statistics: ${error instanceof Error ? error.message : String(error)}`);
        }
    }

    /**
     * Elimina un simbolo dal database cognitivo
     *
     * @param fqcn - Fully Qualified Class Name
     * @returns Promise<boolean> - true se eliminato, false se non trovato
     * @throws Error se operazione fallisce
     *
     * @example
     * ```typescript
     * const deleted = await db.deleteSymbol('App\\Services\\OldService');
     * ```
     */
    async deleteSymbol(fqcn: string): Promise<boolean> {
        this.ensureConnected();

        if (!fqcn || typeof fqcn !== 'string') {
            throw new Error('Invalid FQCN: must be a non-empty string');
        }

        this.log('info', 'Deleting symbol', { fqcn });

        try {
            const key = `symbol:${fqcn}`;

            // Verifica esistenza
            const exists = await this.client!.exists(key);

            if (!exists) {
                this.log('debug', 'Symbol not found for deletion', { fqcn });
                return false;
            }

            // Recupera tipo prima di eliminare
            const type = await this.client!.hget(key, 'type');

            // Elimina hash
            await this.client!.del(key);

            // Rimuovi da set e sorted set
            await this.client!.zrem('symbols:all', fqcn);
            if (type) {
                await this.client!.srem(`symbols:by_type:${type}`, fqcn);
            }

            this.log('info', 'Symbol deleted successfully', { fqcn });
            return true;

        } catch (error) {
            this.log('error', 'Failed to delete symbol', {
                fqcn,
                error: error instanceof Error ? error.message : String(error)
            });
            throw new Error(`Failed to delete symbol ${fqcn}: ${error instanceof Error ? error.message : String(error)}`);
        }
    }

    /**
     * Flush completo del database (USE WITH CAUTION!)
     *
     * Elimina TUTTI i dati del database cognitivo.
     * Da usare solo in sviluppo o per reset completo.
     *
     * @returns Promise<void>
     * @throws Error se operazione fallisce
     *
     * @example
     * ```typescript
     * await db.flushAll(); // DANGER: cancella tutto!
     * ```
     */
    async flushAll(): Promise<void> {
        this.ensureConnected();

        this.log('warn', '🚨 FLUSHING ALL DATABASE - THIS WILL DELETE ALL DATA');

        try {
            await this.client!.flushdb();
            this.log('warn', 'Database flushed successfully');

        } catch (error) {
            this.log('error', 'Failed to flush database', {
                error: error instanceof Error ? error.message : String(error)
            });
            throw new Error(`Failed to flush database: ${error instanceof Error ? error.message : String(error)}`);
        }
    }
}

/**
 * Singleton instance per uso immediato
 *
 * @example
 * ```typescript
 * import { padminDB } from './db.js';
 *
 * await padminDB.connect();
 * await padminDB.saveSymbol(...);
 * ```
 */
export const padminDB = new PadminDB();

/**
 * Export types per uso esterno
 */
export type {
    SymbolData,
    Violation,
    RedisConfig,
    SearchOptions,
    SearchResult,
    DBStats
} from '../types/index.js';
