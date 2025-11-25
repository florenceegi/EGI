/**
 * 🎓 Natan Tutor API Client
 *
 * Gestisce le chiamate HTTP verso il backend Laravel.
 */

export interface UserState {
    balance: number;
    mode: string;
    available_actions: AvailableAction[];
    recommendations: string[];
    recent_actions: RecentAction[];
}

export interface AvailableAction {
    code: string;
    name: string;
    description: string;
    cost: number;
    category: string;
    can_afford: boolean;
}

export interface RecentAction {
    action: string;
    cost: number;
    created_at: string;
}

export interface ActionCost {
    action: string;
    mode: string;
    cost: number;
}

export interface CanAffordResult {
    success: boolean;
    can_afford: boolean;
    action: string;
    mode: string;
    cost: number;
    current_balance: number;
}

export interface TutorResponse {
    success: boolean;
    data?: any;
    error?: string;
    message?: string;
    cost_charged?: number;
    new_balance?: number;
}

export class NatanTutorAPI {
    private baseUrl: string = '/api/natan-tutor';
    private csrfToken: string = '';

    constructor() {
        this.csrfToken = this.getCSRFToken();
    }

    /**
     * Get CSRF token from meta tag
     */
    private getCSRFToken(): string {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta?.getAttribute('content') || '';
    }

    /**
     * Make an API request
     */
    private async request<T>(
        method: 'GET' | 'POST',
        endpoint: string,
        data?: Record<string, any>
    ): Promise<T> {
        const url = method === 'GET' && data
            ? `${this.baseUrl}${endpoint}?${new URLSearchParams(data as any).toString()}`
            : `${this.baseUrl}${endpoint}`;

        const options: RequestInit = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        };

        if (method === 'POST' && data) {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(url, options);

        if (!response.ok) {
            if (response.status === 401) {
                throw new Error('Non autenticato. Effettua il login.');
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response.json();
    }

    /**
     * Get user state (balance, available actions, recommendations)
     */
    async getUserState(): Promise<TutorResponse & { data?: UserState }> {
        return this.request<TutorResponse & { data?: UserState }>('GET', '/state');
    }

    /**
     * Get action cost
     */
    async getActionCost(action: string, mode: string = 'tutoring'): Promise<ActionCost> {
        return this.request<ActionCost>('GET', '/cost', { action, mode });
    }

    /**
     * Check if user can afford an action
     */
    async canAfford(action: string, mode: string = 'tutoring'): Promise<CanAffordResult> {
        return this.request<CanAffordResult>('GET', '/can-afford', { action, mode });
    }

    /**
     * Navigate to a destination
     */
    async navigate(destination: string, mode: string = 'tutoring'): Promise<TutorResponse> {
        return this.request<TutorResponse>('POST', '/navigate', { destination, mode });
    }

    /**
     * Explain a feature
     */
    async explain(feature: string, mode: string = 'tutoring'): Promise<TutorResponse> {
        return this.request<TutorResponse>('POST', '/explain', { feature, mode });
    }

    /**
     * Assist with mint
     */
    async assistMint(mintData: Record<string, any>, mode: string = 'tutoring'): Promise<TutorResponse> {
        return this.request<TutorResponse>('POST', '/assist/mint', { mint_data: mintData, mode });
    }

    /**
     * Assist with reservation
     */
    async assistReservation(egiId: number, mode: string = 'tutoring'): Promise<TutorResponse> {
        return this.request<TutorResponse>('POST', '/assist/reservation', { egi_id: egiId, mode });
    }

    /**
     * Assist with Egili purchase
     */
    async assistPurchase(amount: number, mode: string = 'tutoring'): Promise<TutorResponse> {
        return this.request<TutorResponse>('POST', '/assist/purchase', { amount, mode });
    }

    /**
     * Assist with collection creation
     */
    async assistCollectionCreate(collectionData: Record<string, any>, mode: string = 'tutoring'): Promise<TutorResponse> {
        return this.request<TutorResponse>('POST', '/assist/collection', { collection_data: collectionData, mode });
    }
}

export default NatanTutorAPI;
