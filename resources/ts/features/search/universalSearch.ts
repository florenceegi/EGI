/*
 * Universal Search Modal (Vanilla) – implementazione completa
 * Funzioni: suggerimenti (EGI, Collections, Creators), facets (traits), filtri tipi, user types, scorciatoie tastiera.
 */

type SuggestionsData = {
    suggestions: {
        egis: Array<{ id: number; title: string }>;
        collections: Array<{ id: number; collection_name: string }>;
        creators: Array<{ id: number; name: string; nick_name?: string }>;
    };
    facets: Record<string, Record<string, number>>;
};

interface USState {
    q: string;
    types: Set<string>;
    traits: Set<string>;
    userTypes: Set<string>;
    suggestions: SuggestionsData['suggestions'];
    facets: SuggestionsData['facets'];
    openFacets: boolean;
    fetchedFacets: boolean;
    loading: boolean;
}

const PANEL_ENDPOINT = '/search/panel';
const DEBOUNCE_MS = 260;

let state: USState;
let modalEl: HTMLElement | null;
let overlayEl: HTMLElement | null;
let inputEl: HTMLInputElement | null;
let suggestionsEl: HTMLElement | null;
let facetsEl: HTMLElement | null;
let filtersBtn: HTMLElement | null;
let goBtn: HTMLElement | null;
let closeBtn: HTMLElement | null;
let debounceTimer: number | undefined;

function initState() {
    state = {
        q: '',
        types: new Set(['egi', 'collection', 'creator']),
        traits: new Set(),
        userTypes: new Set(),
        suggestions: { egis: [], collections: [], creators: [] },
        facets: {},
        openFacets: false,
        fetchedFacets: false,
        loading: false,
    };
}

function esc(str: string) { return str.replace(/[&<>"']/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", "\"": "&quot;", "'": "&#39;" }[c] as string)); }
function limit(str: string, m: number) { return str.length > m ? str.slice(0, m - 1) + '…' : str; }

function renderSuggestions() {
    if (!suggestionsEl) return;
    if (state.q.length < 2) { suggestionsEl.classList.add('hidden'); suggestionsEl.innerHTML = ''; return; }
    const total = state.suggestions.egis.length + state.suggestions.collections.length + state.suggestions.creators.length;
    if (!total) { suggestionsEl.classList.add('hidden'); suggestionsEl.innerHTML = ''; return; }
    suggestionsEl.classList.remove('hidden');
    const block = (title: string, color: string, items: any[], tpl: (it: any) => string) => items.length ? `<div><h4 class="mb-1 text-xs font-semibold tracking-wide ${color} uppercase">${title}</h4><ul class="space-y-1">${items.map(tpl).join('')}</ul></div>` : '';
    suggestionsEl.innerHTML = `<div class="p-4 space-y-4 max-h-80 overflow-y-auto">
    ${block('EGI', 'text-purple-300', state.suggestions.egis, it => `<li><a href="/egis/${it.id}" class="block px-2 py-1 rounded hover:bg-gray-700/60">#${it.id} — ${esc(limit(it.title, 50))}</a></li>`)}
    ${block('Collections', 'text-amber-300', state.suggestions.collections, it => `<li><a href="/collections/${it.id}/members" class="block px-2 py-1 rounded hover:bg-gray-700/60">${esc(limit(it.collection_name, 60))}</a></li>`)}
    ${block('Creators', 'text-cyan-300', state.suggestions.creators, it => `<li><a href="/creator/${it.id}" class="block px-2 py-1 rounded hover:bg-gray-700/60">${esc(limit(it.name, 50))}</a></li>`)}
    <div class="pt-2 border-t border-gray-700/40"><button data-us-open-results class="w-full px-3 py-2 text-xs font-semibold text-white rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500">Apri pagina risultati</button></div>
  </div>`;
    suggestionsEl.querySelector('[data-us-open-results]')?.addEventListener('click', navigateResults);
}

function renderFacets() {
    if (!facetsEl) return;
    if (!state.openFacets) { facetsEl.classList.add('hidden'); return; }
    facetsEl.classList.remove('hidden');
    const typesHtml = ['egi', 'collection', 'creator'].map(t => `<button data-us-type="${t}" class="px-3 py-1 text-[11px] font-medium rounded-full border ${state.types.has(t) ? 'bg-purple-600 text-white border-purple-500' : 'bg-gray-700/50 text-gray-300 border-gray-600 hover:bg-gray-700'}">${t}</button>`).join('');
    const userTypes = ['creator', 'collector', 'patron', 'trader'];
    const userTypeHtml = userTypes.map(u => `<button data-us-user="${u}" class="px-3 py-1 text-[10px] font-medium rounded-full border ${state.userTypes.has(u) ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-gray-700/50 text-gray-300 border-gray-600 hover:bg-gray-700'}">${u}</button>`).join('');
    const facetSections = Object.entries(state.facets).map(([group, vals]) => {
        const buttons = Object.entries(vals).map(([v, c]) => `<button data-us-trait="${esc(v)}" title="${esc(v)} (${c})" class="px-2 py-1 text-[10px] rounded-md border ${state.traits.has(v) ? 'bg-emerald-600 text-white border-emerald-500' : 'bg-gray-700/40 text-gray-300 border-gray-600 hover:bg-gray-700'}">${esc(limit(v, 14))} <span class="text-[9px] opacity-70">${c}</span></button>`).join('');
        return `<details class="group" ${group === Object.keys(state.facets)[0] ? 'open' : ''}><summary class="flex items-center justify-between cursor-pointer select-none px-2 py-1"><span class="text-[11px] font-semibold text-purple-300 group-open:text-purple-200">${esc(group)}</span><span class="text-[10px] text-gray-500">${Object.keys(vals).length}</span></summary><div class="mt-2 grid grid-cols-2 gap-2 px-2 pb-2">${buttons}</div></details>`;
    }).join('');
    facetsEl.innerHTML = `<div class="p-4 space-y-4 bg-gray-900/60">
    <div><h4 class="mb-2 text-xs font-semibold tracking-wide text-gray-300 uppercase">Tipi</h4><div class="flex flex-wrap gap-2">${typesHtml}</div></div>
    <div><h4 class="mb-2 text-xs font-semibold tracking-wide text-gray-300 uppercase">User Types</h4><div class="flex flex-wrap gap-2">${userTypeHtml}</div></div>
    <div><h4 class="mb-2 text-xs font-semibold tracking-wide text-gray-300 uppercase">Traits</h4><div class="space-y-3 max-h-60 overflow-y-auto">${facetSections || '<p class="text-[11px] text-gray-500 px-2">Nessuna facet.</p>'}</div></div>
    <div class="flex justify-end gap-2 pt-2 border-t border-gray-700/40"><button data-us-close-facets class="px-3 py-1 text-xs font-medium text-gray-300 rounded-lg bg-gray-700/60 hover:bg-gray-700">Chiudi</button><button data-us-open-results class="px-4 py-1 text-xs font-semibold text-white rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500">Risultati</button></div>
  </div>`;
    facetsEl.querySelectorAll<HTMLButtonElement>('[data-us-type]').forEach(b => b.onclick = () => { toggle(state.types, b.getAttribute('data-us-type')!); renderFacets(); });
    facetsEl.querySelectorAll<HTMLButtonElement>('[data-us-user]').forEach(b => b.onclick = () => { toggle(state.userTypes, b.getAttribute('data-us-user')!); renderFacets(); });
    facetsEl.querySelectorAll<HTMLButtonElement>('[data-us-trait]').forEach(b => b.onclick = () => { toggle(state.traits, b.getAttribute('data-us-trait')!); renderFacets(); });
    facetsEl.querySelector('[data-us-close-facets]')?.addEventListener('click', () => { state.openFacets = false; renderFacets(); });
    facetsEl.querySelectorAll('[data-us-open-results]').forEach(btn => btn.addEventListener('click', navigateResults));
}

function toggle(set: Set<string>, val: string) { set.has(val) ? set.delete(val) : set.add(val); }

async function fetchPanel() {
    if (state.q.length < 2) { state.suggestions = { egis: [], collections: [], creators: [] }; renderSuggestions(); return; }
    try {
        const r = await fetch(`${PANEL_ENDPOINT}?q=${encodeURIComponent(state.q)}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!r.ok) throw new Error('panel request failed');
        const data: SuggestionsData = await r.json();
        state.suggestions = data.suggestions;
        if (!state.fetchedFacets && data.facets) { state.facets = data.facets; state.fetchedFacets = true; if (state.openFacets) renderFacets(); }
        renderSuggestions();
    } catch (e) { console.error('UniversalSearch error', e); }
}

function debounced() { window.clearTimeout(debounceTimer); debounceTimer = window.setTimeout(fetchPanel, DEBOUNCE_MS); }

function navigateResults() {
    const p = new URLSearchParams();
    if (state.q) p.set('q', state.q);
    if (state.types.size && state.types.size < 3) p.set('types', Array.from(state.types).join(','));
    state.traits.forEach(t => p.append('traits[]', t));
    state.userTypes.forEach(u => p.append('user_types[]', u));
    window.location.href = '/search/results?' + p.toString();
}

function openModal() {
    if (!modalEl) return;
    modalEl.classList.remove('hidden');
    modalEl.setAttribute('aria-hidden', 'false');
    requestAnimationFrame(() => overlayEl && overlayEl.classList.remove('opacity-0'));
    inputEl?.focus();
}
function closeModal() {
    if (!modalEl) return;
    modalEl.setAttribute('aria-hidden', 'true');
    overlayEl && overlayEl.classList.add('opacity-0');
    setTimeout(() => modalEl && modalEl.classList.add('hidden'), 140);
    state.openFacets = false; renderFacets();
}

function bindShortcuts() {
    document.addEventListener('keydown', e => {
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); openModal(); }
        else if (e.key === 'Escape') { closeModal(); }
        else if (e.key === 'Enter' && document.activeElement === inputEl) { navigateResults(); }
    });
}

export function autoInitUniversalSearch() {
    modalEl = document.getElementById('universal-search-modal');
    if (!modalEl || (modalEl as any)._init) return;
    (modalEl as any)._init = true;
    initState();
    overlayEl = modalEl.querySelector('[data-us-overlay]');
    inputEl = modalEl.querySelector('#universal-search-input');
    suggestionsEl = modalEl.querySelector('#universal-search-suggestions');
    facetsEl = modalEl.querySelector('#universal-search-facets');
    filtersBtn = modalEl.querySelector('[data-us-filters]');
    goBtn = modalEl.querySelector('[data-us-go]');
    closeBtn = modalEl.querySelector('[data-us-close]');

    inputEl?.addEventListener('input', () => { state.q = inputEl!.value.trim(); debounced(); });
    filtersBtn?.addEventListener('click', () => { state.openFacets = !state.openFacets; renderFacets(); });
    goBtn?.addEventListener('click', navigateResults);
    closeBtn?.addEventListener('click', closeModal);
    overlayEl?.addEventListener('click', closeModal);
    bindShortcuts();
    // Retrocompatibilità con vecchio trigger custom event
    window.addEventListener('universal-search-open', openModal);
    fetchPanel();

    (window as any).UniversalSearch = { open: openModal, close: closeModal, state: () => state };
}

if (document.readyState !== 'loading') autoInitUniversalSearch();
else document.addEventListener('DOMContentLoaded', autoInitUniversalSearch);
