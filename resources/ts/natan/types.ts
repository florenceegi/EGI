/**
 * N.A.T.A.N. TypeScript Type Definitions
 * 
 * @package resources/ts/natan
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-09
 * @purpose Type definitions for N.A.T.A.N. AI Document Intelligence system
 */

/**
 * EgiAct - Administrative act with AI-extracted metadata
 */
export interface EgiAct {
    id: number;
    document_id: string;
    tipo_atto: string;
    numero_atto: string | null;
    data_atto: string;
    oggetto: string;
    ente: string | null;
    direzione: string | null;
    responsabile: string | null;
    importo: number | null;
    categoria: string[];
    metadata_json: Record<string, any>;
    hash_firma: string | null;
    blockchain_tx: string | null;
    qr_code: string | null;
    processing_status: ProcessingStatus;
    ai_tokens_used: number | null;
    ai_cost: number | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

/**
 * Processing status enum
 */
export type ProcessingStatus = 'pending' | 'completed' | 'failed';

/**
 * Acts filter criteria
 */
export interface ActsFilter {
    tipo?: string;
    ente?: string;
    direzione?: string;
    data_from?: string;
    data_to?: string;
    importo_min?: number;
    importo_max?: number;
    search?: string;
}

/**
 * API response wrapper
 */
export interface ApiResponse<T> {
    data: T;
    meta?: PaginationMeta;
    message?: string;
    success?: boolean;
}

/**
 * Pagination metadata
 */
export interface PaginationMeta {
    current_page: number;
    total: number;
    per_page: number;
    last_page: number;
    from?: number;
    to?: number;
}

/**
 * Statistics data
 */
export interface Stats {
    total_acts: number;
    by_tipo: Record<string, number>;
    by_month: MonthlyCount[];
    avg_importo: number;
    total_ai_cost: number;
    recent_acts_7d?: number;
    acts_this_month?: number;
    avg_processing_time?: number;
}

/**
 * Monthly count data point
 */
export interface MonthlyCount {
    month: string;
    count: number;
}

/**
 * Job upload response
 */
export interface JobUploadResponse {
    job_id: string;
    status: string;
    estimated_time?: number;
    message?: string;
}

/**
 * Job status response
 */
export interface JobStatusResponse {
    status: ProcessingStatus;
    act?: EgiAct;
    error?: string;
    file?: string;
    created_at?: string;
}

/**
 * Filter options for dropdowns
 */
export interface FilterOptions {
    tipi_atto: string[];
    enti: string[];
    direzioni: string[];
}

/**
 * Upload event detail
 */
export interface UploadEventDetail {
    jobId: string;
    filename: string;
    status: ProcessingStatus;
}

/**
 * Act processed event detail
 */
export interface ActProcessedEventDetail {
    act: EgiAct;
}

/**
 * Custom event types
 */
export interface NatanEvents {
    'natan:upload-started': CustomEvent<UploadEventDetail>;
    'natan:upload-completed': CustomEvent<UploadEventDetail>;
    'natan:upload-failed': CustomEvent<UploadEventDetail>;
    'natan:act-processed': CustomEvent<ActProcessedEventDetail>;
    'natan:show-detail': CustomEvent<EgiAct>;
    'natan:show-qr': CustomEvent<string>;
}

/**
 * Sorting configuration
 */
export interface SortConfig {
    field: string;
    direction: 'asc' | 'desc';
}

/**
 * Toast notification type
 */
export type ToastType = 'info' | 'success' | 'error' | 'warning';

/**
 * Toast notification options
 */
export interface ToastOptions {
    message: string;
    type: ToastType;
    duration?: number;
    position?: 'top-right' | 'top-left' | 'bottom-right' | 'bottom-left';
}

