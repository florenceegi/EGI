export class RequestCreateNotificationWallet {
    constructor(options = {}) {
        if (RequestCreateNotificationWallet.instance) {
            console.warn(`⛔ Tentativo di inizializzazione multipla di RequestCreateNotificationWallet ignorato`);
            return RequestCreateNotificationWallet.instance;
        }
        this.options = options || { apiBaseUrl: '/notifications' };
    this.bindEvents();
        console.log('🚀 RequestCreateNotificationWallet initialized');
        RequestCreateNotificationWallet.instance = this;
        return this;
    }

    /**
     * 🌐 Sistema di traduzione intelligente con fallback
     * Prova prima il sistema moderno appTranslate, poi il sistema deprecato
     */
    translate(key, fallback = key) {
        // Usa SOLO il sistema moderno (definito da main/collection.js)
        if (typeof window !== 'undefined' && typeof window.appTranslate === 'function') {
            try {
                    const result = window.appTranslate(key);
                return result ?? fallback;
            } catch (error) {
                console.warn('appTranslate ha generato un errore per la chiave:', key, error);
            }
        } else {
            console.warn('appTranslate non inizializzato. Chiave:', key);
        }
        return fallback;
    }

    bindEvents() {

        console.log("🔍 BindEvent");

        // Event delegation: copre pulsanti renderizzati dinamicamente e click su figli (SVG, path)
        document.addEventListener('click', async (event) => {
            const target = event.target;
            if (!(target instanceof Element)) return;
            const btn = target.closest('.create-wallet-btn');
            if (!btn) return;

            const collectionId = btn.getAttribute('data-collection-id');
            const userIdStr = btn.getAttribute('data-user-id');
            const walletAddress = btn.getAttribute('data-wallet-address') || '';

            const userId = userIdStr ? parseInt(userIdStr, 10) : NaN;

            console.log("🔍 Valori recuperati (delegation):", { collectionId, userId, walletAddress });

            if (!collectionId || isNaN(userId)) {
                console.error("❌ Errore: Manca collectionId o userId nel dataset!");
                return;
            }

            await this.openCreateWalletModal(collectionId, userId, walletAddress);
        });

        // Supporto Livewire: logga al termine del render per diagnosticare
        if (window.Livewire && typeof window.Livewire.hook === 'function') {
            window.Livewire.hook('message.processed', (message, component) => {
                console.debug('🔁 Livewire DOM updated for component', component.fingerprint?.name || '', '— handlers are delegated, no rebind needed');
            });
        }
    }
    async openCreateWalletModal(collectionId, userId, walletAddress = '') {
        const modalHtml = await this.getCreateModalHtml(walletAddress);

        try {
            if (!window.Swal) {
                console.error('❌ SweetAlert2 non trovato su window.Swal. Assicurati che resources/js/app.js sia caricato.');
                alert('SweetAlert non disponibile');
                return;
            }
            const result = await window.Swal.fire({
                title: this.translate('collection.wallet.create_the_wallet'),
                html: modalHtml,
                showCancelButton: true,
                confirmButtonText: this.translate('wallet_create'),
                cancelButtonText: 'Annulla',
                width: 600,
                customClass: {
                    popup: 'bg-gray-800 text-white',
                    title: 'text-white',
                    content: 'text-white'
                },
                preConfirm: () => this.validateAndCollectData(collectionId, userId)
            });

            if (result.isConfirmed && result.value) {
                await this.handleCreateWallet(result.value);
            }
        } catch (error) {
            console.error("❌ Errore durante l'apertura del modal:", error);
        }
    }

    validateAndCollectData(collectionId, userId) {
        const form = document.getElementById('wallet-modal-form');

        if (!form) {
            console.error("❌ Errore: Il form #wallet-modal-form non esiste nel DOM!");
            return null;
        }

        const walletAddress = form.querySelector('#walletAddress')?.value.trim();
        const royaltyMint = parseFloat(form.querySelector('#royaltyMint')?.value) || 0;
        const royaltyRebind = parseFloat(form.querySelector('#royaltyRebind')?.value) || 0;

        if (!walletAddress) {
            console.error("❌ Errore: Indirizzo wallet mancante!");
            if (window.Swal) window.Swal.showValidationMessage(this.translate('collection.wallet.validation.address_required'));
            return null;
        }

        const data = { receiver_id: userId, collection_id: collectionId, wallet: walletAddress, royaltyMint, royaltyRebind };
        console.log("✅ Dati raccolti correttamente:", data);
        return data;
    }

    async getCreateModalHtml(walletAddress = '') {
        console.log("🔍 Caricamento HTML del modale...", this.translate('wallet_address', 'Address'));

        return `
            <form id="wallet-modal-form" class="space-y-4">
                <div class="mb-3">
                    <label for="walletAddress" class="block text-sm font-medium text-gray-300">
                        ${this.translate('wallet_address', 'Address')}
                    </label>
                    <input type="text"
                           id="walletAddress"
                           class="swal2-input bg-gray-700 text-white"
                           style="width: 90%; max-width: 350px; margin: auto; padding: 8px;"
                           value="${walletAddress}"
                           placeholder="${this.translate('collection.wallet.address_placeholder')}">
                </div>

                <div class="mb-3">
                    <label for="royaltyMint" class="block text-sm font-medium text-gray-300">
                        ${this.translate('collection.wallet.royalty_mint')}
                    </label>
                    <input type="number"
                           id="royaltyMint"
                           class="swal2-input bg-gray-700 text-white"
                           style="width: 90%; max-width: 350px; margin: auto; padding: 8px;"
                           step="0.01"
                           placeholder="${this.translate('collection.wallet.royalty_mint_placeholder')}">
                </div>

                <div class="mb-3">
                    <label for="royaltyRebind" class="block text-sm font-medium text-gray-300">
                        ${this.translate('collection.wallet.royalty_rebind')}
                    </label>
                    <input type="number"
                           id="royaltyRebind"
                           class="swal2-input bg-gray-700 text-white"
                           style="width: 90%; max-width: 350px; margin: auto; padding: 8px;"
                           step="0.01"
                           placeholder="${this.translate('collection.wallet.royalty_rebind_placeholder')}">
                </div>
            </form>
        `;
    }

    async handleCreateWallet(data) {
        console.log('Creating wallet with:', data);

        try {
            const response = await fetch(`/collections/${data.collection_id}/wallets/create`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            console.log('Wallet created successfully, response:', response);
            console.log('Wallet created successfully, result:', result);

            if (!response.ok) {
                throw new Error(result.message || 'Error creating wallet');
            } else if (response.ok) {
                this.showSuccess(this.translate('collection.wallet.creation_success'));
                this.updateUI(result.data);
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            console.error('Error creating wallet:', error);
            this.showError(error);
        }
    }

    updateUI(data) {
        console.log('data', data);

        // const btn = document.querySelector(`[data-user-id="${data.receiver_id}"].create-wallet-btn`);
        // if (btn) btn.remove();

        window.location.reload();

        const msg = this.translate('collection.wallet.creation_success_detail');
        if (!window.Swal) return alert(msg);
        window.Swal.fire({
            icon: 'success',
            title: this.translate('collection.wallet.creation_success'),
            text: msg,
            timer: 3000,
            showConfirmButton: false
        });
    }

    showSuccess(message) {
    if (!window.Swal) return alert(message);
    window.Swal.fire({
            icon: 'success',
            title: this.translate('collection.wallet.creation_success'),
            text: message,
            timer: 3000
        });
    }

    showError(message) {
        if (!window.Swal) return alert(message);
        window.Swal.fire({
            icon: 'error',
            title: this.translate('wallet_validation_check_pending_wallet_title'),

            text: message
        });
    }

    async ensureTranslationsLoaded() {
        if (!window.translations || Object.keys(window.translations).length === 0) {
            await this.fetchTranslations();
        }
    }
}

// Inizializzazione
export default RequestCreateNotificationWallet;
