export class RequestCreateNotificationWallet {
    constructor(options = {}) {
        if (RequestCreateNotificationWallet.instance) {
            console.warn(
                `⛔ Tentativo di inizializzazione multipla di RequestCreateNotificationWallet ignorato`
            );
            return RequestCreateNotificationWallet.instance;
        }
        this.options = options || { apiBaseUrl: "/notifications" };
        this.bindEvents();
        console.log("🚀 RequestCreateNotificationWallet initialized");
        RequestCreateNotificationWallet.instance = this;
        return this;
    }

    /**
     * 🌐 Sistema di traduzione intelligente con fallback
     * Prova prima il sistema moderno appTranslate, poi il sistema deprecato
     */
    translate(key, fallback = key) {
        // Usa SOLO il sistema moderno (definito da main/collection.js)
        if (
            typeof window !== "undefined" &&
            typeof window.appTranslate === "function"
        ) {
            try {
                const result = window.appTranslate(key);
                return result ?? fallback;
            } catch (error) {
                console.warn(
                    "appTranslate ha generato un errore per la chiave:",
                    key,
                    error
                );
            }
        } else {
            console.warn("appTranslate non inizializzato. Chiave:", key);
        }
        return fallback;
    }

    bindEvents() {
        console.log("🔍 BindEvent");

        // Event delegation: copre pulsanti renderizzati dinamicamente e click su figli (SVG, path)
        document.addEventListener("click", async (event) => {
            const target = event.target;
            if (!(target instanceof Element)) return;
            const btn = target.closest(".create-wallet-btn");
            if (!btn) return;

            const collectionId = btn.getAttribute("data-collection-id");
            const userIdStr = btn.getAttribute("data-user-id");
            const walletAddress = btn.getAttribute("data-wallet-address") || "";

            const userId = userIdStr ? parseInt(userIdStr, 10) : NaN;

            console.log("🔍 Valori recuperati (delegation):", {
                collectionId,
                userId,
                walletAddress,
            });

            if (!collectionId || isNaN(userId)) {
                console.error(
                    "❌ Errore: Manca collectionId o userId nel dataset!"
                );
                return;
            }

            await this.openCreateWalletModal(
                collectionId,
                userId,
                walletAddress
            );
        });

        // Supporto Livewire: logga al termine del render per diagnosticare
        if (window.Livewire && typeof window.Livewire.hook === "function") {
            window.Livewire.hook("message.processed", (message, component) => {
                console.debug(
                    "🔁 Livewire DOM updated for component",
                    component.fingerprint?.name || "",
                    "— handlers are delegated, no rebind needed"
                );
            });
        }
    }
    async openCreateWalletModal(collectionId, userId, walletAddress = "") {
        const modalHtml = await this.getCreateModalHtml(walletAddress);

        try {
            if (!window.Swal) {
                console.error(
                    "❌ SweetAlert2 non trovato su window.Swal. Assicurati che resources/js/app.js sia caricato."
                );
                alert("SweetAlert non disponibile");
                return;
            }
            const result = await window.Swal.fire({
                title: this.translate(
                    "wallet_create_the_wallet",
                    "Crea un nuovo wallet"
                ),
                html: modalHtml,
                showCancelButton: true,
                confirmButtonText: this.translate(
                    "wallet_create",
                    "Aggiungi Wallet"
                ),
                cancelButtonText: this.translate("cancel", "Annulla"),
                width: 700,
                padding: "2rem",
                background: "#1f2937",
                color: "#fff",
                customClass: {
                    popup: "rounded-2xl shadow-2xl border border-gray-700",
                    title: "text-2xl font-bold text-white mb-6",
                    htmlContainer: "text-left",
                    confirmButton:
                        "bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg transition-all duration-200 hover:scale-[1.02]",
                    cancelButton:
                        "bg-gray-700 hover:bg-gray-600 text-gray-300 font-semibold px-6 py-2.5 rounded-lg transition-all duration-200",
                },
                buttonsStyling: false,
                preConfirm: () =>
                    this.validateAndCollectData(collectionId, userId),
            });

            if (result.isConfirmed && result.value) {
                await this.handleCreateWallet(result.value);
            }
        } catch (error) {
            console.error("❌ Errore durante l'apertura del modal:", error);
        }
    }

    validateAndCollectData(collectionId, userId) {
        const form = document.getElementById("wallet-modal-form");

        if (!form) {
            console.error(
                "❌ Errore: Il form #wallet-modal-form non esiste nel DOM!"
            );
            return null;
        }

        const walletAddress = form
            .querySelector("#walletAddress")
            ?.value.trim();
        const royaltyMint =
            parseFloat(form.querySelector("#royaltyMint")?.value) || 0;
        const royaltyRebind =
            parseFloat(form.querySelector("#royaltyRebind")?.value) || 0;

        if (!walletAddress) {
            console.error("❌ Errore: Indirizzo wallet mancante!");
            if (window.Swal)
                window.Swal.showValidationMessage(
                    this.translate(
                        "wallet_validation_address_required",
                        "L'indirizzo del wallet è obbligatorio."
                    )
                );
            return null;
        }

        const data = {
            receiver_id: userId,
            collection_id: collectionId,
            wallet: walletAddress,
            royaltyMint,
            royaltyRebind,
        };
        console.log("✅ Dati raccolti correttamente:", data);
        return data;
    }

    async getCreateModalHtml(walletAddress = "") {
        console.log(
            "🔍 Caricamento HTML del modale...",
            this.translate("wallet_address_label", "Indirizzo Wallet")
        );

        return `
            <div class="text-left">
                <!-- Header Info Box -->
                <div class="mb-6 rounded-lg border border-blue-600/30 bg-blue-900/20 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-blue-200 leading-relaxed">
                            ${this.translate(
                                "wallet_add_external_description",
                                "Aggiungi un wallet Algorand esistente per questo utente. Puoi incollare manualmente l'indirizzo o connettere tramite PeraWallet."
                            )}
                        </p>
                    </div>
                </div>

                <!-- Connection Methods -->
                <div class="mb-6 grid grid-cols-2 gap-3">
                    <!-- Manual Input (Active) -->
                    <button type="button" id="manualInputBtn" 
                        class="flex flex-col items-center gap-2 rounded-lg border-2 border-blue-500 bg-blue-900/30 px-4 py-3 text-center transition-all hover:bg-blue-900/50">
                        <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span class="text-sm font-semibold text-blue-300">Incolla Address</span>
                    </button>

                    <!-- PeraWallet (Coming Soon) -->
                    <button type="button" id="peraWalletBtn" disabled
                        class="flex flex-col items-center gap-2 rounded-lg border-2 border-gray-600 bg-gray-700/30 px-4 py-3 text-center opacity-50 cursor-not-allowed">
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                        </svg>
                        <span class="text-sm font-semibold text-gray-400">Connetti PeraWallet</span>
                        <span class="text-xs text-gray-500">(Prossimamente)</span>
                    </button>
                </div>

                <!-- Form -->
                <form id="wallet-modal-form" class="space-y-4">
                    <!-- Wallet Address -->
                    <div>
                        <label for="walletAddress" class="mb-2 block text-sm font-semibold text-gray-200">
                            ${this.translate(
                                "wallet_address_label",
                                "Indirizzo Algorand"
                            )} <span class="text-red-400">*</span>
                        </label>
                        <input type="text"
                               id="walletAddress"
                               class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 font-mono text-sm text-white placeholder-gray-500 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50"
                               value="${walletAddress}"
                               placeholder="${this.translate(
                                   "wallet_address_placeholder",
                                   "Inserisci l'indirizzo del wallet"
                               )}"
                               maxlength="58">
                        <p class="mt-1.5 text-xs text-gray-400">
                            ${this.translate(
                                "wallet_address_hint",
                                "L'indirizzo deve essere di 58 caratteri (formato Base32)"
                            )}
                        </p>
                    </div>

                    <!-- Royalties Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Royalty Mint -->
                        <div>
                            <label for="royaltyMint" class="mb-2 block text-sm font-semibold text-gray-200">
                                ${this.translate(
                                    "wallet_royalty_mint_label",
                                    "Royalty Mint"
                                )} <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input type="number"
                                       id="royaltyMint"
                                       class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 pr-10 text-white transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50"
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       placeholder="${this.translate(
                                           "wallet_royalty_mint_placeholder",
                                           "Inserisci la percentuale"
                                       )}">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 font-medium">%</span>
                            </div>
                        </div>

                        <!-- Royalty Rebind -->
                        <div>
                            <label for="royaltyRebind" class="mb-2 block text-sm font-semibold text-gray-200">
                                ${this.translate(
                                    "wallet_royalty_rebind_label",
                                    "Royalty Rebind"
                                )} <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input type="number"
                                       id="royaltyRebind"
                                       class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 pr-10 text-white transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50"
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       placeholder="${this.translate(
                                           "wallet_royalty_rebind_placeholder",
                                           "Inserisci la percentuale"
                                       )}">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 font-medium">%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Warning: Royalty Deduction -->
                    <div class="rounded-lg border border-yellow-600/40 bg-yellow-900/20 p-4">
                        <div class="flex items-start gap-3">
                            <svg class="h-5 w-5 flex-shrink-0 text-yellow-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-sm text-yellow-200 leading-relaxed">
                                ${this.translate(
                                    "wallet_royalty_deduction_warning",
                                    "Attenzione: Le royalty verranno sottratte dalla tua quota disponibile. Verifica di avere abbastanza quota prima di procedere."
                                )}
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        `;
    }

    async handleCreateWallet(data) {
        console.log("Creating wallet with:", data);

        try {
            const response = await fetch(
                `/collections/${data.collection_id}/wallets/create`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                        Accept: "application/json",
                    },
                    body: JSON.stringify(data),
                }
            );

            const result = await response.json();
            console.log("Wallet created successfully, response:", response);
            console.log("Wallet created successfully, result:", result);

            if (!response.ok) {
                throw new Error(result.message || "Error creating wallet");
            } else if (response.ok) {
                this.showSuccess(
                    this.translate("collection.wallet.creation_success")
                );
                this.updateUI(result.data);
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            console.error("Error creating wallet:", error);
            this.showError(error);
        }
    }

    updateUI(data) {
        console.log("data", data);

        // const btn = document.querySelector(`[data-user-id="${data.receiver_id}"].create-wallet-btn`);
        // if (btn) btn.remove();

        window.location.reload();

        const msg = this.translate(
            "wallet_creation_success_detail",
            "Il wallet è stato creato con successo."
        );
        if (!window.Swal) return alert(msg);
        window.Swal.fire({
            icon: "success",
            title: this.translate(
                "wallet_creation_success",
                "Wallet creato con successo."
            ),
            text: msg,
            timer: 3000,
            showConfirmButton: false,
        });
    }

    showSuccess(message) {
        if (!window.Swal) return alert(message);
        window.Swal.fire({
            icon: "success",
            title: this.translate(
                "wallet_creation_success",
                "Wallet creato con successo."
            ),
            text: message,
            timer: 3000,
        });
    }

    showError(message) {
        if (!window.Swal) return alert(message);
        window.Swal.fire({
            icon: "error",
            title: this.translate(
                "wallet_validation_check_pending_wallet_title",
                "Errore di validazione"
            ),

            text: message,
        });
    }

    async ensureTranslationsLoaded() {
        if (
            !window.translations ||
            Object.keys(window.translations).length === 0
        ) {
            await this.fetchTranslations();
        }
    }
}

// Inizializzazione
export default RequestCreateNotificationWallet;
