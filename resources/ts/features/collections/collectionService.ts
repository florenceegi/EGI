// File: resources/ts/features/collections/collectionService.ts

/**
 * 📜 Oracode TypeScript Module: CollectionApiService
 * Gestisce le chiamate API relative alle collection dell'utente.
 *
 * @version 1.0.0
 * @date 2025-05-10
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

import { AppConfig, UserAccessibleCollections, ServerErrorResponse, appTranslate } from '../../config/appConfig';
import { getCsrfTokenTS } from '../../utils/csrf';
import { UEM_Client_TS_Placeholder as UEM } from '../../services/uemClientService';

export async function fetchUserAccessibleCollectionsAPI(config: AppConfig): Promise<UserAccessibleCollections | null> {
    try {
        const response = await fetch('/api/user/accessible-collections', {
            method: 'GET',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfTokenTS() },
        });

        if (!response.ok) {
            const errorData: ServerErrorResponse = await response.json().catch(() => ({
                error: 'HTTP_ERROR', message: appTranslate('errorFetchCollectionsHttp', config.translations, { status: response.status, statusText: response.statusText })
            }));
            UEM.handleServerErrorResponse(errorData, appTranslate('errorFetchCollections', config.translations, {}));
            return null;
        }
        return await response.json() as UserAccessibleCollections;
    } catch (error: any) {
        console.error("Padmin API Error: Error fetching accessible collections:", error.message);
        UEM.handleClientError('CLIENT_API_COLLECTIONS_FETCH_FAIL', { error: error.message }, error, appTranslate('errorFetchCollectionsGeneric', config.translations, {}));
        return null;
    }
}

/**
 * Fetch collections where the user can create EGI (filtered by role permission).
 */
export async function fetchEgiCreatableCollectionsAPI(config: AppConfig): Promise<Array<{ id: number; collection_name: string }> | null> {
    try {
        const response = await fetch('/api/user/egi-creatable-collections', {
            method: 'GET',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfTokenTS() },
        });

        if (!response.ok) {
            const errorData: ServerErrorResponse = await response.json().catch(() => ({
                error: 'HTTP_ERROR', message: appTranslate('errorFetchCollectionsHttp', config.translations, { status: response.status, statusText: response.statusText })
            }));
            UEM.handleServerErrorResponse(errorData, appTranslate('errorFetchCollections', config.translations, {}));
            return null;
        }
        const json = await response.json();
        return (json.eligible_collections as Array<{ id: number; collection_name: string }>) || [];
    } catch (error: any) {
        console.error('Padmin API Error: Error fetching egi-creatable collections:', error.message);
        UEM.handleClientError('CLIENT_API_EGI_CREATABLE_COLLECTIONS_FETCH_FAIL', { error: error.message }, error, appTranslate('errorFetchCollectionsGeneric', config.translations, {}));
        return null;
    }
}

export async function setCurrentUserCollectionAPI(
    config: AppConfig,
    collectionId: number
): Promise<{ id: number; name: string; can_edit: boolean } | null> {

    const route = '/api/user/set-current-collection/' + collectionId.toString();

    try {
        const response = await fetch(route, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfTokenTS(), 'Content-Type': 'application/json' },
        });

        const data: ServerErrorResponse | { current_collection_id: number; current_collection_name: string; can_edit_current_collection?: boolean } = await response.json();

        if (!response.ok) {
            UEM.handleServerErrorResponse(data as ServerErrorResponse, appTranslate('errorSetCurrentCollectionHttp', config.translations, { code: response.status }));
            return null;
        }
        const successData = data as { current_collection_id: number; current_collection_name: string; can_edit_current_collection?: boolean };
        return {
            id: successData.current_collection_id,
            name: successData.current_collection_name,
            can_edit: successData.can_edit_current_collection !== undefined ? successData.can_edit_current_collection : false,
        };
    } catch (error: any) {
        console.error("Padmin API Error: Error setting current collection:", error.message);
        UEM.handleClientError('CLIENT_API_COLLECTION_SET_FAIL', { collectionId, error: error.message }, error, appTranslate('errorSetCurrentCollectionGeneric', config.translations, {}));
        return null;
    }
}
