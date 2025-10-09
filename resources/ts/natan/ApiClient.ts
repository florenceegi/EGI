/**
 * N.A.T.A.N. API Client
 * 
 * @package resources/ts/natan
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-09
 * @purpose HTTP client for N.A.T.A.N. API endpoints with authentication
 */

import type {
    EgiAct,
    ActsFilter,
    ApiResponse,
    Stats,
    JobUploadResponse,
    JobStatusResponse,
    FilterOptions,
    PaginationMeta
} from './types';

/**
 * N.A.T.A.N. API Client
 * 
 * Handles all HTTP requests to N.A.T.A.N. endpoints with CSRF protection
 */
export class NatanApiClient {
    private baseUrl: string;
    private token: string;

    /**
     * Constructor
     * 
     * @param baseUrl Base API URL (default: /api/natan)
     * @param token CSRF token (auto-detected from meta tag if not provided)
     */
    constructor(baseUrl: string = '/api/natan', token: string = '') {
        this.baseUrl = baseUrl;
        this.token = token || this.getTokenFromMeta();
    }

    /**
     * Get CSRF token from meta tag
     * 
     * @returns CSRF token string
     */
    private getTokenFromMeta(): string {
        const meta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
        return meta?.content || '';
    }

    /**
     * Generic fetch wrapper with authentication
     * 
     * @param endpoint API endpoint (without base URL)
     * @param options Fetch options
     * @returns API response
     * @throws Error if request fails
     */
    private async fetch<T>(
        endpoint: string,
        options: RequestInit = {}
    ): Promise<ApiResponse<T>> {
        const headers = new Headers(options.headers);

        if (this.token) {
            headers.set('X-CSRF-TOKEN', this.token);
        }

        headers.set('Accept', 'application/json');

        if (!options.body || !(options.body instanceof FormData)) {
            headers.set('Content-Type', 'application/json');
        }

        const response = await fetch(`${this.baseUrl}${endpoint}`, {
            ...options,
            headers,
        });

        if (!response.ok) {
            const error = await response.json().catch(() => ({
                message: 'Network error'
            }));
            throw new Error(error.message || `HTTP ${response.status}`);
        }

        return response.json();
    }

    /**
     * Upload document for AI analysis
     * 
     * @param file File to upload
     * @returns Job upload response with job_id
     */
    async uploadDocument(file: File): Promise<JobUploadResponse> {
        const formData = new FormData();
        formData.append('file', file);

        const response = await fetch(`${this.baseUrl}/analyze`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.token,
                'Accept': 'application/json',
            },
            body: formData,
        });

        if (!response.ok) {
            const error = await response.json().catch(() => ({
                message: 'Upload failed'
            }));
            throw new Error(error.message || `Upload failed: ${response.status}`);
        }

        return response.json();
    }

    /**
     * Get paginated list of acts with filters
     * 
     * @param filters Filter criteria
     * @param page Page number (default: 1)
     * @param perPage Results per page (default: 20)
     * @param sort Sort field (default: created_at)
     * @param direction Sort direction (default: desc)
     * @returns Paginated acts list
     */
    async getActs(
        filters: ActsFilter = {},
        page: number = 1,
        perPage: number = 20,
        sort: string = 'created_at',
        direction: 'asc' | 'desc' = 'desc'
    ): Promise<ApiResponse<EgiAct[]>> {
        const params = new URLSearchParams({
            page: String(page),
            per_page: String(perPage),
            sort,
            direction,
            ...Object.fromEntries(
                Object.entries(filters)
                    .filter(([_, v]) => v !== undefined && v !== '')
                    .map(([k, v]) => [k, String(v)])
            ),
        });

        return this.fetch<EgiAct[]>(`/acts?${params}`);
    }

    /**
     * Get single act by ID
     * 
     * @param id Act ID
     * @returns Act detail
     */
    async getAct(id: number): Promise<ApiResponse<EgiAct>> {
        return this.fetch<EgiAct>(`/acts/${id}`);
    }

    /**
     * Search acts by full-text query
     * 
     * @param query Search query (min 3 characters)
     * @param page Page number (default: 1)
     * @returns Search results
     */
    async searchActs(query: string, page: number = 1): Promise<ApiResponse<EgiAct[]>> {
        if (query.length < 3) {
            throw new Error('Query must be at least 3 characters');
        }

        const params = new URLSearchParams({
            q: query,
            page: String(page)
        });

        return this.fetch<EgiAct[]>(`/search?${params}`);
    }

    /**
     * Get statistics
     * 
     * @returns Statistics data
     */
    async getStats(): Promise<ApiResponse<Stats>> {
        return this.fetch<Stats>('/stats');
    }

    /**
     * Get filter options for dropdowns
     * 
     * @returns Available filter options
     */
    async getFilters(): Promise<ApiResponse<FilterOptions>> {
        return this.fetch<FilterOptions>('/filters');
    }

    /**
     * Poll job status
     * 
     * @param jobId Job UUID
     * @returns Job status
     */
    async getJobStatus(jobId: string): Promise<JobStatusResponse> {
        const response = await this.fetch<JobStatusResponse>(`/jobs/${jobId}`);
        return response.data || response as any;
    }

    /**
     * Refresh CSRF token
     * 
     * Updates the token from meta tag (useful after token rotation)
     */
    refreshToken(): void {
        this.token = this.getTokenFromMeta();
    }
}

/**
 * Create singleton instance
 */
let apiClientInstance: NatanApiClient | null = null;

/**
 * Get or create API client singleton
 * 
 * @returns NatanApiClient instance
 */
export function getNatanApiClient(): NatanApiClient {
    if (!apiClientInstance) {
        apiClientInstance = new NatanApiClient();
    }
    return apiClientInstance;
}

/**
 * Export default instance for convenience
 */
export default getNatanApiClient();

