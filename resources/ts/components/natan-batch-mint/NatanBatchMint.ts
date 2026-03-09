/**
 * @package     resources/ts/components/natan-batch-mint
 * @author      Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version     1.0.0 (FlorenceEGI - NatanBatchMint)
 * @date        2026-03-09
 * @purpose     Orchestrate multi-file EGI creation via conversational NATAN interface.
 *              Uses File System Access API (showOpenFilePicker) for direct filesystem
 *              access with explicit user consent. Falls back to standard file input
 *              when the API is not supported (e.g. Firefox).
 *
 *              FLOW:
 *              Step 1 → Prezzo (uguale per tutti)
 *              Step 2 → Titolo base
 *              Step 3 → Selezione file (FS picker o fallback)
 *              Step 4 → Conferma riepilogo
 *              Step 5 → Inietta nel form esistente + chiudi
 *
 *              DESIGN PRINCIPLES (OS3.0):
 *              - Zero modifiche al codice upload manager esistente
 *              - Consenso esplicito: picker OS sempre visibile all'utente
 *              - Nessun dato inviato prima della conferma (Step 4)
 *              - L'utente mantiene controllo totale (può annullare in ogni step)
 */

// ─────────────────────────────────────────────────────────────────────────────
// Types
// ─────────────────────────────────────────────────────────────────────────────

interface BatchMintState {
    price: number | null;
    titleBase: string;
    files: File[];
}

type StepId = 1 | 2 | 3 | 4 | 5;

// ─────────────────────────────────────────────────────────────────────────────
// NatanBatchMint
// ─────────────────────────────────────────────────────────────────────────────

export class NatanBatchMint {

    private readonly MODAL_ID = 'natan-batch-mint-modal';
    private state: BatchMintState = { price: null, titleBase: '', files: [] };

    constructor() {
        // Auto-fill form if sessionStorage has preload data (from sidebar redirect flow)
        this.checkSessionPreload();
    }

    // ── Public API ───────────────────────────────────────────────────────────

    public open(): void {
        this.state = { price: null, titleBase: '', files: [] };
        this.createModal();
        this.renderStep(1);
    }

    public close(): void {
        document.getElementById(this.MODAL_ID)?.remove();
    }

    /**
     * If sessionStorage has preload data (set by sidebar redirect flow),
     * auto-fill form fields and show a toast. Called on every page init.
     */
    public checkSessionPreload(): void {
        const raw = sessionStorage.getItem('__natanMintPreload');
        if (!raw) return;

        const titleInput = document.querySelector<HTMLInputElement>('#egi-title');
        const priceInput = document.querySelector<HTMLInputElement>('#egi-floor-price');
        if (!titleInput || !priceInput) return;

        try {
            const preload = JSON.parse(raw) as { price: number; titleBase: string; fileCount: number; fileNames: string[] };
            sessionStorage.removeItem('__natanMintPreload');

            if (preload.titleBase) {
                titleInput.value = preload.titleBase;
                titleInput.dispatchEvent(new Event('input', { bubbles: true }));
                titleInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
            if (preload.price !== null && preload.price !== undefined) {
                priceInput.value = Number(preload.price).toFixed(2);
                priceInput.dispatchEvent(new Event('input', { bubbles: true }));
                priceInput.dispatchEvent(new Event('change', { bubbles: true }));
            }

            this.showPreloadToast(preload);
        } catch { /* malformed preload — ignore */ }
    }

    // ── Modal Shell ──────────────────────────────────────────────────────────

    private createModal(): void {
        document.getElementById(this.MODAL_ID)?.remove();

        const overlay = document.createElement('div');
        overlay.id = this.MODAL_ID;
        overlay.className = [
            'fixed inset-0 z-[9999] flex items-center justify-center',
            'bg-black/80 backdrop-blur-sm p-4',
        ].join(' ');

        overlay.innerHTML = `
            <div class="relative w-full max-w-md rounded-2xl border border-purple-500/40
                        bg-gradient-to-br from-gray-900 via-purple-950 to-gray-900
                        shadow-2xl p-6 text-white">

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl" role="img" aria-label="NATAN">🤖</span>
                        <div>
                            <h3 class="text-lg font-bold text-white leading-tight">NATAN</h3>
                            <p class="text-xs text-purple-300">Assistente Batch EGI</p>
                        </div>
                    </div>
                    <button id="natan-batch-close"
                            class="text-gray-400 hover:text-white transition-colors text-xl font-bold
                                   focus:outline-none focus:ring-2 focus:ring-purple-500 rounded"
                            aria-label="Chiudi assistente NATAN">✕</button>
                </div>

                <!-- Step content -->
                <div id="natan-batch-content"></div>

                <!-- Step indicator -->
                <div class="flex justify-center gap-1.5 mt-6" aria-hidden="true">
                    ${[1, 2, 3, 4].map(i => `
                        <div id="natan-step-dot-${i}"
                             class="w-2 h-2 rounded-full bg-gray-600 transition-colors"></div>
                    `).join('')}
                </div>
            </div>`;

        document.body.appendChild(overlay);

        document.getElementById('natan-batch-close')
            ?.addEventListener('click', () => this.close());

        // Close on overlay click (outside the card)
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) this.close();
        });
    }

    // ── Step Router ──────────────────────────────────────────────────────────

    private renderStep(step: StepId): void {
        // Update step dots
        ([1, 2, 3, 4] as const).forEach(i => {
            const dot = document.getElementById(`natan-step-dot-${i}`);
            if (!dot) return;
            if (i < step)       dot.className = 'w-2 h-2 rounded-full transition-colors bg-purple-400';
            else if (i === step) dot.className = 'w-2 h-2 rounded-full transition-colors bg-white';
            else                 dot.className = 'w-2 h-2 rounded-full transition-colors bg-gray-600';
        });

        const content = document.getElementById('natan-batch-content');
        if (!content) return;

        const dispatch: Record<StepId, (el: HTMLElement) => void | Promise<void>> = {
            1: (el) => this.step1Price(el),
            2: (el) => this.step2Title(el),
            3: (el) => this.step3Files(el),
            4: (el) => this.step4Confirm(el),
            5: (el) => this.step5Done(el),
        };

        dispatch[step]?.(content);
    }

    // ── Step 1 — Prezzo ──────────────────────────────────────────────────────

    private step1Price(el: HTMLElement): void {
        el.innerHTML = `
            <p class="text-purple-300 text-xs font-medium mb-1 uppercase tracking-wide">Passo 1 di 4</p>
            <p class="text-white font-medium mb-1">Che prezzo vuoi assegnare alle opere?</p>
            <p class="text-xs text-gray-400 mb-4">Sarà uguale per tutti i file caricati.</p>

            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold select-none">€</span>
                <input id="natan-input-price"
                       type="number" step="0.01" min="0"
                       placeholder="Es: 200.00"
                       aria-label="Prezzo per opera in euro"
                       class="w-full pl-8 pr-3 py-2.5 bg-gray-800 border border-gray-600
                              rounded-lg text-white placeholder-gray-500 text-sm
                              focus:outline-none focus:ring-2 focus:ring-purple-500
                              [appearance:textfield]
                              [&::-webkit-outer-spin-button]:appearance-none
                              [&::-webkit-inner-spin-button]:appearance-none">
            </div>
            <p id="natan-price-error" class="text-red-400 text-xs mt-1 hidden" role="alert">
                Inserisci un prezzo valido (≥ 0).
            </p>

            <button id="natan-step1-next"
                    class="mt-5 w-full py-2.5 rounded-full bg-purple-600 hover:bg-purple-500
                           font-semibold text-sm transition-colors
                           focus:outline-none focus:ring-2 focus:ring-purple-400">
                Avanti →
            </button>`;

        const input = document.getElementById('natan-input-price') as HTMLInputElement;
        const next  = document.getElementById('natan-step1-next');
        const err   = document.getElementById('natan-price-error');

        // Pre-fill if returning from step 2
        if (this.state.price !== null) input.value = String(this.state.price);

        const tryNext = () => {
            const val = parseFloat(input.value);
            if (isNaN(val) || val < 0) { err?.classList.remove('hidden'); return; }
            err?.classList.add('hidden');
            this.state.price = val;
            this.renderStep(2);
        };

        input.addEventListener('keydown', (e) => { if (e.key === 'Enter') tryNext(); });
        next?.addEventListener('click', tryNext);
        input.focus();
    }

    // ── Step 2 — Titolo Base ─────────────────────────────────────────────────

    private step2Title(el: HTMLElement): void {
        el.innerHTML = `
            <p class="text-purple-300 text-xs font-medium mb-1 uppercase tracking-wide">Passo 2 di 4</p>
            <p class="text-white font-medium mb-1">Come si chiamano queste opere?</p>
            <p class="text-xs text-gray-400 mb-4">
                Sarà il titolo condiviso di tutti i file caricati.
            </p>

            <input id="natan-input-title"
                   type="text" maxlength="200"
                   placeholder="Es: Venezia, Natura Morta, Serie Autunnale…"
                   aria-label="Titolo delle opere"
                   class="w-full px-3 py-2.5 bg-gray-800 border border-gray-600
                          rounded-lg text-white placeholder-gray-500 text-sm
                          focus:outline-none focus:ring-2 focus:ring-purple-500">
            <p id="natan-title-error" class="text-red-400 text-xs mt-1 hidden" role="alert">
                Inserisci un titolo.
            </p>

            <div class="flex gap-3 mt-5">
                <button id="natan-step2-back"
                        class="flex-1 py-2.5 rounded-full bg-gray-700 hover:bg-gray-600
                               font-semibold text-sm transition-colors
                               focus:outline-none focus:ring-2 focus:ring-gray-400">
                    ← Indietro
                </button>
                <button id="natan-step2-next"
                        class="flex-1 py-2.5 rounded-full bg-purple-600 hover:bg-purple-500
                               font-semibold text-sm transition-colors
                               focus:outline-none focus:ring-2 focus:ring-purple-400">
                    Avanti →
                </button>
            </div>`;

        const input = document.getElementById('natan-input-title') as HTMLInputElement;
        const next  = document.getElementById('natan-step2-next');
        const back  = document.getElementById('natan-step2-back');
        const err   = document.getElementById('natan-title-error');

        if (this.state.titleBase) input.value = this.state.titleBase;

        const tryNext = () => {
            const val = input.value.trim();
            if (!val) { err?.classList.remove('hidden'); return; }
            err?.classList.add('hidden');
            this.state.titleBase = val;
            this.renderStep(3);
        };

        input.addEventListener('keydown', (e) => { if (e.key === 'Enter') tryNext(); });
        back?.addEventListener('click', () => this.renderStep(1));
        next?.addEventListener('click', tryNext);
        input.focus();
    }

    // ── Step 3 — Selezione File ──────────────────────────────────────────────

    private step3Files(el: HTMLElement): void {
        const hasNativeApi = 'showOpenFilePicker' in window;
        const count = this.state.files.length;

        el.innerHTML = `
            <p class="text-purple-300 text-xs font-medium mb-1 uppercase tracking-wide">Passo 3 di 4</p>
            <p class="text-white font-medium mb-1">Dammi accesso ai tuoi file.</p>
            <p class="text-xs text-gray-400 mb-4">
                Nessun dato viene inviato prima della tua conferma finale.
            </p>

            ${count > 0 ? `
                <div class="mb-3 px-3 py-2 rounded-lg bg-green-900/30 border border-green-500/30
                            text-green-300 text-sm flex items-center gap-2">
                    <span>✅</span>
                    <span>
                        <strong>${count}</strong> ${count === 1 ? 'file selezionato' : 'file selezionati'}
                    </span>
                </div>
            ` : ''}

            <button id="natan-step3-pick"
                    class="w-full py-3 rounded-xl border-2 border-dashed border-purple-500/60
                           hover:border-purple-400 hover:bg-purple-900/20
                           font-semibold text-sm transition-all text-purple-200
                           focus:outline-none focus:ring-2 focus:ring-purple-400">
                🗂 &nbsp;${hasNativeApi ? 'Apri Esplora File' : 'Seleziona File'}
            </button>

            ${!hasNativeApi ? `
                <p class="text-xs text-gray-500 mt-1.5 text-center">
                    Il tuo browser non supporta l'accesso diretto al filesystem.<br>
                    Si aprirà la finestra di selezione standard.
                </p>
            ` : ''}

            <div class="flex gap-3 mt-5">
                <button id="natan-step3-back"
                        class="flex-1 py-2.5 rounded-full bg-gray-700 hover:bg-gray-600
                               font-semibold text-sm transition-colors
                               focus:outline-none focus:ring-2 focus:ring-gray-400">
                    ← Indietro
                </button>
                <button id="natan-step3-next"
                        class="flex-1 py-2.5 rounded-full bg-purple-600 hover:bg-purple-500
                               font-semibold text-sm transition-colors
                               focus:outline-none focus:ring-2 focus:ring-purple-400
                               ${count === 0 ? 'opacity-40 cursor-not-allowed' : ''}"
                        ${count === 0 ? 'disabled aria-disabled="true"' : ''}>
                    Avanti →
                </button>
            </div>`;

        document.getElementById('natan-step3-back')
            ?.addEventListener('click', () => this.renderStep(2));

        document.getElementById('natan-step3-next')
            ?.addEventListener('click', () => {
                if (this.state.files.length > 0) this.renderStep(4);
            });

        document.getElementById('natan-step3-pick')
            ?.addEventListener('click', async () => {
                const files = await this.pickFiles();
                if (files.length > 0) {
                    this.state.files = files;
                    this.renderStep(3); // Re-render to show count + enable "Avanti"
                }
            });
    }

    /**
     * Open file picker.
     * Tries File System Access API first (Chrome/Edge), falls back to hidden input.
     */
    private async pickFiles(): Promise<File[]> {
        if ('showOpenFilePicker' in window) {
            try {
                const handles: FileSystemFileHandle[] = await (window as any).showOpenFilePicker({
                    multiple: true,
                    excludeAcceptAllOption: false,
                    types: [{
                        description: 'Immagini e Media',
                        accept: {
                            'image/*': ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg'],
                            'video/*': ['.mp4', '.mov', '.webm'],
                            'audio/*': ['.mp3', '.wav', '.ogg'],
                        },
                    }],
                });
                return await Promise.all(handles.map(h => h.getFile()));
            } catch {
                // User cancelled the picker — not an error
                return [];
            }
        }

        // Fallback: temporary hidden input (no DOM side-effects)
        return new Promise((resolve) => {
            const input = document.createElement('input');
            input.type = 'file';
            input.multiple = true;
            input.accept = 'image/*,video/*,audio/*';
            input.style.display = 'none';
            input.onchange = () => {
                resolve(Array.from(input.files ?? []));
                input.remove();
            };
            document.body.appendChild(input);
            input.click();
        });
    }

    // ── Step 4 — Conferma ────────────────────────────────────────────────────

    private step4Confirm(el: HTMLElement): void {
        const { price, titleBase, files } = this.state;
        const n = files.length;
        const collectionName = this.detectCollectionName();

        el.innerHTML = `
            <p class="text-purple-300 text-xs font-medium mb-1 uppercase tracking-wide">Passo 4 di 4 — Riepilogo</p>
            <p class="text-white font-medium mb-4">Tutto pronto. Confermo?</p>

            <div class="space-y-2 mb-4">
                <div class="flex items-start gap-2 text-sm">
                    <span class="text-green-400 mt-0.5 shrink-0">✅</span>
                    <span class="text-gray-300">
                        <strong class="text-white">${n}</strong>
                        ${n === 1 ? 'file' : 'file'} selezionat${n === 1 ? 'o' : 'i'}
                    </span>
                </div>
                <div class="flex items-start gap-2 text-sm">
                    <span class="text-green-400 mt-0.5 shrink-0">✅</span>
                    <span class="text-gray-300">
                        Titolo: <strong class="text-white">${this.escapeHtml(titleBase)}</strong>
                    </span>
                </div>
                <div class="flex items-start gap-2 text-sm">
                    <span class="text-green-400 mt-0.5 shrink-0">✅</span>
                    <span class="text-gray-300">
                        Prezzo: <strong class="text-white">€${Number(price).toFixed(2)}</strong>
                        per opera
                    </span>
                </div>
                <div class="flex items-start gap-2 text-sm">
                    <span class="text-blue-400 mt-0.5 shrink-0">📁</span>
                    <span class="text-gray-300">
                        Destinazione: <strong class="text-white">${this.escapeHtml(collectionName)}</strong>
                    </span>
                </div>
            </div>

            <div class="flex gap-3 mt-5">
                <button id="natan-step4-back"
                        class="flex-1 py-2.5 rounded-full bg-gray-700 hover:bg-gray-600
                               font-semibold text-sm transition-colors
                               focus:outline-none focus:ring-2 focus:ring-gray-400">
                    ← Modifica
                </button>
                <button id="natan-step4-confirm"
                        class="flex-1 py-2.5 rounded-full bg-green-600 hover:bg-green-500
                               font-semibold text-sm transition-colors
                               focus:outline-none focus:ring-2 focus:ring-green-400">
                    ✓ Procedi
                </button>
            </div>`;

        document.getElementById('natan-step4-back')
            ?.addEventListener('click', () => this.renderStep(3));

        document.getElementById('natan-step4-confirm')
            ?.addEventListener('click', () => {
                // If the upload form is on the current page → inject directly
                if (document.querySelector('#egi-title')) {
                    this.injectIntoForm();
                    this.renderStep(5);
                } else {
                    // Sidebar flow: store state and redirect to upload page
                    this.handleRedirectFlow();
                }
            });
    }

    // ── Step 5 — Done ────────────────────────────────────────────────────────

    private step5Done(el: HTMLElement): void {
        const n = this.state.files.length;

        el.innerHTML = `
            <div class="text-center py-2">
                <div class="text-5xl mb-4" role="img" aria-label="completato">✅</div>
                <p class="text-white font-semibold text-base mb-2">Form pronto!</p>
                <p class="text-gray-300 text-sm mb-1">
                    Ho caricato <strong>${n}</strong> ${n === 1 ? 'file' : 'file'} nel form.
                </p>
                <p class="text-gray-400 text-xs mb-5">
                    Controlla le anteprime qui sotto,
                    poi clicca <strong class="text-white">💾 Salva i file</strong>
                    per finalizzare la creazione degli EGI.
                </p>
                <button id="natan-step5-close"
                        class="w-full py-2.5 rounded-full bg-purple-600 hover:bg-purple-500
                               font-semibold text-sm transition-colors
                               focus:outline-none focus:ring-2 focus:ring-purple-400">
                    Chiudi — vado a verificare
                </button>
            </div>`;

        document.getElementById('natan-step5-close')
            ?.addEventListener('click', () => this.close());

        // Auto-close after 5s so user can see the form
        setTimeout(() => this.close(), 5000);
    }

    // ── Sidebar → Redirect flow ─────────────────────────────────────────────

    /**
     * Called when the user is NOT on the upload form page.
     * Stores price+title in sessionStorage, shows a redirect step.
     */
    private handleRedirectFlow(): void {
        sessionStorage.setItem('__natanMintPreload', JSON.stringify({
            price: this.state.price,
            titleBase: this.state.titleBase,
            fileCount: this.state.files.length,
            fileNames: this.state.files.map(f => f.name),
        }));

        const content = document.getElementById('natan-batch-content');
        if (!content) return;

        const n = this.state.files.length;
        const uploadUrl = (window as any).__natanUploadUrl || '/egi/upload';

        content.innerHTML = `
            <div class="text-center py-2">
                <div class="text-4xl mb-3" role="img" aria-label="pronto">🚀</div>
                <p class="text-white font-semibold text-base mb-2">Quasi pronto!</p>
                <p class="text-gray-300 text-sm mb-1">Titolo e prezzo sono stati salvati.</p>
                <p class="text-gray-400 text-xs mb-4">
                    Vai alla pagina di upload: verranno pre-compilati automaticamente.<br>
                    Ri-seleziona i <strong class="text-white">${n}</strong> file già scelti.
                </p>
                ${n > 0 ? `
                <div class="mb-4 text-left bg-gray-800/60 rounded-lg px-3 py-2 border border-gray-700">
                    <p class="text-xs text-gray-500 mb-1">File da ri-selezionare:</p>
                    <ul class="text-xs text-gray-300 space-y-0.5">
                        ${this.state.files.slice(0, 4).map(f => `<li class="truncate">• ${this.escapeHtml(f.name)}</li>`).join('')}
                        ${n > 4 ? `<li class="text-gray-500">... e altri ${n - 4} file</li>` : ''}
                    </ul>
                </div>` : ''}
                <a href="${uploadUrl}"
                   class="block w-full text-center py-2.5 rounded-full bg-purple-600 hover:bg-purple-500
                          font-semibold text-sm transition-colors
                          focus:outline-none focus:ring-2 focus:ring-purple-400">
                    Vai al form di upload →
                </a>
                <button id="natan-redirect-cancel"
                        class="mt-2 w-full py-2 text-gray-400 hover:text-white text-sm transition-colors">
                    Annulla
                </button>
            </div>`;

        document.getElementById('natan-redirect-cancel')
            ?.addEventListener('click', () => this.close());
    }

    /**
     * Shows a non-blocking toast when form fields are auto-filled from sessionStorage.
     */
    private showPreloadToast(preload: { price: number; titleBase: string; fileCount: number }): void {
        const toast = document.createElement('div');
        toast.id = 'natan-preload-toast';
        toast.className = 'fixed bottom-20 right-6 z-[9998] bg-purple-900 border border-purple-500/40 rounded-xl px-4 py-3 text-white shadow-xl max-w-xs animate-fade-in';
        toast.innerHTML = `
            <div class="flex items-start gap-2">
                <span class="text-purple-300 text-xl">🤖</span>
                <div>
                    <p class="font-semibold text-purple-200 text-xs uppercase tracking-wide mb-1">NATAN ha precompilato</p>
                    <p class="text-gray-200 text-xs">Titolo e prezzo già inseriti.</p>
                    <p class="text-gray-400 text-xs mt-0.5">Ri-seleziona i ${preload.fileCount} file.</p>
                </div>
                <button onclick="this.closest('#natan-preload-toast')?.remove()"
                        class="ml-2 text-gray-400 hover:text-white text-lg font-bold leading-none">×</button>
            </div>`;
        document.body.appendChild(toast);
        setTimeout(() => toast?.remove(), 6000);
    }

    // ── Core: inject into existing upload form ───────────────────────────────

    /**
     * Fills the existing form fields (title, price, files input) without
     * touching any upload manager internals. Dispatches native DOM events
     * so the upload manager reacts as if the user filled the form manually.
     */
    private injectIntoForm(): void {
        const titleInput = document.querySelector<HTMLInputElement>('#egi-title');
        const priceInput = document.querySelector<HTMLInputElement>('#egi-floor-price');
        const filesInput = document.querySelector<HTMLInputElement>('#files');

        if (titleInput && this.state.titleBase) {
            titleInput.value = this.state.titleBase;
            titleInput.dispatchEvent(new Event('input', { bubbles: true }));
            titleInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (priceInput && this.state.price !== null) {
            priceInput.value = Number(this.state.price).toFixed(2);
            priceInput.dispatchEvent(new Event('input', { bubbles: true }));
            priceInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (filesInput && this.state.files.length > 0) {
            const dt = new DataTransfer();
            this.state.files.forEach(f => dt.items.add(f));
            filesInput.files = dt.files;
            // Dispatch `change` — the upload manager listens to this to show previews
            filesInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Try to detect the current collection name from the DOM.
     * create-collection-modal.js fires a 'collection-created' window event
     * with collectionName — we store it on window.__natanCollection when it occurs.
     */
    private detectCollectionName(): string {
        const fromWindow = (window as any).__natanCollection?.name;
        if (fromWindow) return fromWindow;

        // Fallback: look for a visible collection label in the page
        const label = document.querySelector<HTMLElement>('[data-collection-name]')
            ?.dataset.collectionName;
        if (label) return label;

        return 'Collezione corrente';
    }

    private escapeHtml(str: string): string {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Bootstrap
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Listen for collection-created events from create-collection-modal.js
 * so NATAN can report the correct collection name in the confirm step.
 */
window.addEventListener('collection-created', (e: Event) => {
    const detail = (e as CustomEvent).detail;
    if (detail?.collectionName) {
        (window as any).__natanCollection = { name: detail.collectionName };
    }
});

/**
 * Auto-init on every page where this script is loaded.
 * Exposes instance globally as window.natanBatchMint for sidebar integration.
 * Safe to load on any page — checkSessionPreload() is a no-op if form is absent.
 */
function initNatanBatchMint(): void {
    const instance = new NatanBatchMint();
    (window as any).natanBatchMint = instance;
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNatanBatchMint);
} else {
    initNatanBatchMint();
}
