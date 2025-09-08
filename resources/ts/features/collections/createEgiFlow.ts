// File: resources/ts/features/collections/createEgiFlow.ts

import { AppConfig, appTranslate } from '../../config/appConfig';
import * as DOMElements from '../../dom/domElements';
import { fetchEgiCreatableCollectionsAPI, setCurrentUserCollectionAPI } from './collectionService';

function createModalContainer(): HTMLDivElement {
    const modal = document.createElement('div');
    modal.id = 'egi-collection-select-modal';
    modal.className = 'fixed inset-0 z-[10000] flex items-center justify-center bg-black/60';
    modal.innerHTML = `
      <div class="bg-gray-900 text-gray-100 border border-emerald-600/30 rounded-2xl w-full max-w-md shadow-2xl">
        <div class="px-5 py-4 border-b border-gray-800 flex items-center justify-between">
          <h3 class="text-lg font-semibold">${appTranslate('selectGalleryForEgi', (window as any).mainAppConfig?.translations || {})}</h3>
          <button type="button" class="p-1 rounded hover:bg-gray-800" data-action="close">
            <span class="material-symbols-outlined" aria-hidden="true">close</span>
            <span class="sr-only">${appTranslate('close', (window as any).mainAppConfig?.translations || {})}</span>
          </button>
        </div>
        <div class="p-4">
          <p class="text-sm text-gray-300 mb-3">${appTranslate('chooseGalleryToAddEgi', (window as any).mainAppConfig?.translations || {})}</p>
          <div id="egi-collection-list" class="space-y-2 max-h-64 overflow-y-auto"></div>
          <div id="egi-collection-empty" class="hidden text-sm text-gray-400">${appTranslate('noEligibleGalleries', (window as any).mainAppConfig?.translations || {})}</div>
          <div id="egi-collection-error" class="hidden text-sm text-red-400">${appTranslate('errorLoadingGalleries', (window as any).mainAppConfig?.translations || {})}</div>
        </div>
      </div>
    `;
    return modal;
}

function renderList(modal: HTMLDivElement, items: Array<{ id: number; collection_name: string }>, onSelect: (id: number) => void) {
    const list = modal.querySelector<HTMLDivElement>('#egi-collection-list')!;
    list.innerHTML = '';
    items.forEach(c => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'w-full text-left px-3 py-2 rounded bg-gray-800 hover:bg-gray-700 border border-gray-700';
        btn.textContent = c.collection_name;
        btn.addEventListener('click', () => onSelect(c.id));
        list.appendChild(btn);
    });
}

export async function handleCreateEgiFlow(config: AppConfig, DOM: typeof DOMElements): Promise<void> {
    const data = await fetchEgiCreatableCollectionsAPI(config);
    if (data === null) return; // error already handled

    // 0 idonee => messaggio e chiama fallback (potrebbe guidare a creare collection)
    if (data.length === 0) {
        if (window.Swal) {
            window.Swal.fire({
                icon: 'info',
                title: appTranslate('noGalleryEligibleTitle', config.translations),
                text: appTranslate('noGalleryEligibleText', config.translations),
                confirmButtonText: appTranslate('ok', config.translations),
            });
        } else {
            alert(appTranslate('noGalleryEligibleText', config.translations));
        }
        return;
    }

    // 1 idonea => se non è già current, setCurrent, poi apri modal upload
    if (data.length === 1) {
        const only = data[0];
        const currentId = config.initialUserData.current_collection_id;
        if (!currentId || currentId !== only.id) {
            const newDetails = await setCurrentUserCollectionAPI(config, only.id);
            if (newDetails) {
                config.initialUserData.current_collection_id = newDetails.id;
                config.initialUserData.current_collection_name = newDetails.name;
                config.initialUserData.can_edit_current_collection = newDetails.can_edit;
                document.dispatchEvent(new CustomEvent('collection-changed', { detail: newDetails }));
            }
        }
        document.dispatchEvent(new CustomEvent('openUploadModal', { detail: { type: 'egi' } }));
        return;
    }

    // >1 idonee => modale di selezione; onSelect => set current e apri upload
    const modal = createModalContainer();
    document.body.appendChild(modal);
    const close = () => modal.remove();
    modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
    modal.querySelector('[data-action="close"]')?.addEventListener('click', close);

    renderList(modal, data, async (id) => {
        const newDetails = await setCurrentUserCollectionAPI(config, id);
        if (newDetails) {
            config.initialUserData.current_collection_id = newDetails.id;
            config.initialUserData.current_collection_name = newDetails.name;
            config.initialUserData.can_edit_current_collection = newDetails.can_edit;
            document.dispatchEvent(new CustomEvent('collection-changed', { detail: newDetails }));
        }
        close();
        document.dispatchEvent(new CustomEvent('openUploadModal', { detail: { type: 'egi' } }));
    });
}
