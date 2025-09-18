/**
 * @Oracode Like Service
 * 🎯 Purpose: Manages like/unlike operations for Collections and EGIs
 * 🧱 Core Logic: API calls, state management, error handling with existing UEM
 *
 * @package FlorenceEGI/Services
 * @author Padmin D. Curtis
 * @version 1.0.0
 * @date 2025-05-15
 */

import { UEM_Client_TS_Placeholder as UEM } from './uemClientService';
import { getCsrfTokenTS } from '../utils/csrf';
import { AppConfig, ServerErrorResponse } from '../config/appConfig';

export interface LikeResponse {
    success: boolean;
    is_liked: boolean;
    likes_count: number;
    message: string;
}

export interface LikeableResource {
    type: 'collection' | 'egi';
    id: number;
}

export class LikeService {
    private static instance: LikeService;
    private abortControllers: Map<string, AbortController> = new Map();

    private constructor() {}

    public static getInstance(): LikeService {
        if (!LikeService.instance) {
            LikeService.instance = new LikeService();
        }
        return LikeService.instance;
    }

    /**
     * Toggle like status for a resource
     */
    public async toggleLike(resource: LikeableResource, config: AppConfig): Promise<LikeResponse> {
        const key = `${resource.type}-${resource.id}`;

        // Cancel any pending request for the same resource
        this.cancelRequest(key);

        // Create new abort controller
        const abortController = new AbortController();
        this.abortControllers.set(key, abortController);

        try {
            const url = this.buildLikeUrl(resource, config);

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfTokenTS(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin', // ESSENZIALE per includere i cookie di sessione
                signal: abortController.signal
            });

            if (!response.ok) {
                const errorData: ServerErrorResponse = await response.json().catch(() => ({
                    error: 'LIKE_TOGGLE_FAILED',
                    message: 'Failed to toggle like status'
                }));

                UEM.handleServerErrorResponse(errorData, 'Like operation failed');
                throw new Error(errorData.message);
            }

            const data = await response.json() as LikeResponse;

            if (!data.success) {
                throw new Error(data.message || 'Like operation failed');
            }

            return data;

        } catch (error: any) {
            if (error.name === 'AbortError') {
                console.log(`[LikeService] Request aborted for ${key}`);
                throw error;
            }

            UEM.handleClientError('LIKE_TOGGLE_FAILED', {
                resource_type: resource.type,
                resource_id: resource.id,
                error: error.message
            }, error instanceof Error ? error : undefined);

            throw error;
        } finally {
            this.abortControllers.delete(key);
        }
    }

    /**
     * Build API URL for like operation
     */
    private buildLikeUrl(resource: LikeableResource, config: AppConfig): string {
        const baseUrl = config.routes.api.baseUrl || '/api';

        switch (resource.type) {
            case 'collection':
                return `${baseUrl}/collections/${resource.id}/toggle-like`;
            case 'egi':
                return `${baseUrl}/egis/${resource.id}/toggle-like`;
            default:
                throw new Error(`Unknown resource type: ${resource.type}`);
        }
    }

    /**
     * Cancel pending request
     */
    private cancelRequest(key: string): void {
        const controller = this.abortControllers.get(key);
        if (controller) {
            controller.abort();
            this.abortControllers.delete(key);
        }
    }
}

export default LikeService.getInstance();
