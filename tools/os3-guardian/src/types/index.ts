/**
 * @package OS3 Guardian / Padmin Analyzer
 * @author Padmin D. Curtis (AI Partner OS3)
 * @version 1.0.0 (FlorenceEGI - Type Definitions)
 * @date 2025-10-22
 * @purpose Definizioni TypeScript per il database cognitivo semantico
 */

/**
 * Rappresenta un simbolo (classe, metodo, funzione) nel database cognitivo
 */
export interface SymbolData {
    /** Fully Qualified Class Name (es: App\Services\Gdpr\ConsentService) */
    fqcn: string;

    /** Tipo di simbolo */
    type: 'class' | 'method' | 'function' | 'interface' | 'trait';

    /** Descrizione semantica funzionale */
    summary: string;

    /** File sorgente */
    file: string;

    /** Linea di inizio */
    line: number;

    /** Parametri di input (serializzati JSON) */
    inputs?: string;

    /** Tipo di output/return */
    outputs?: string;

    /** Dipendenze (FQCN separati da virgola) */
    deps?: string;

    /** Vettore semantico (embedding serializzato) */
    vector?: string;

    /** Hash SHA256 del contenuto */
    hash?: string;

    /** Timestamp ultimo aggiornamento */
    updated_at: number;

    /** Metadati aggiuntivi */
    metadata?: Record<string, any>;
}

/**
 * Rappresenta una violazione OS3 rilevata
 */
export interface Violation {
    /** Simbolo che viola la regola */
    symbol: string;

    /** Identificativo regola OS3 (es: REGOLA_ZERO, UEM_FIRST, STATISTICS) */
    rule: string;

    /** Livello di gravità */
    severity: 'P0' | 'P1' | 'P2' | 'P3';

    /** Messaggio descrittivo */
    message: string;

    /** File coinvolto */
    file: string;

    /** Linea */
    line: number;

    /** Timestamp rilevamento */
    timestamp: number;

    /** Suggerimento di fix */
    fix_suggestion?: string;

    /** Stato */
    status: 'open' | 'fixed' | 'ignored';
}

/**
 * Configurazione connessione Redis Stack
 */
export interface RedisConfig {
    host: string;
    port: number;
    password?: string;
    db?: number;
    keyPrefix?: string;
    retryStrategy?: (times: number) => number | void;
}

/**
 * Opzioni per la ricerca vettoriale
 */
export interface SearchOptions {
    /** Numero massimo di risultati */
    limit?: number;

    /** Threshold di similarità minima (0-1) */
    minSimilarity?: number;

    /** Filtri aggiuntivi */
    filters?: {
        type?: SymbolData['type'];
        file?: string;
    };
}

/**
 * Risultato di ricerca vettoriale
 */
export interface SearchResult {
    symbol: SymbolData;
    similarity: number;
}

/**
 * Statistiche del database cognitivo
 */
export interface DBStats {
    totalSymbols: number;
    totalViolations: number;
    lastUpdate: number;
    symbolsByType: Record<string, number>;
    violationsBySeverity: Record<string, number>;
}
