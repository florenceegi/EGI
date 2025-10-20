/**
 *
 * Extracted from reservationService.ts as part of SOLID refactoring
 * Handles modal UI, form interaction, and accessibility features
 *
 * @author Fabio Cherici
 * @extracted 2025-01-22 - Phase 12 SOLID Migration
 */

// All'inizio di ReservationFormModal.ts
import { UEM_Client_TS_Placeholder as UEM } from '../../uemClientService';
import { getAppConfig, appTranslate } from '../../../config/appConfig';
import { getCsrfTokenTS } from '../../../utils/csrf';
import { getAuthStatus } from '../../../features/auth/authService';
import { getAlgoExchangeRate, getCachedAlgoRate, setCachedAlgoRate } from '../ExchangeRateService';
import { ReservationModalUI } from '../ui/ReservationModalUI';
import type {
    ReservationFormData,
    ReservationResponse,
} from '../../../types/reservationTypes';

// Per usare la funzione reserveEgi
import { reserveEgi } from '../../reservationService';

/**
 * 📜 ReservationFormModal Class
 * 🎯 Purpose: Manages the reservation modal UI and form interaction
 *
 * @accessibility-trait Manages focus trap in modal for keyboard navigation
 * @privacy-safe Handles minimal contact data with user consent
 */
export class ReservationFormModal {
    private egiId: number;
    private modal: HTMLElement | null = null;
    private form: HTMLFormElement | null = null;
    private closeButton: HTMLElement | null = null;
    private offerInput: HTMLInputElement | null = null;
    private algoEquivalentText: HTMLElement | null = null;
    private submitButton: HTMLButtonElement | null = null;
    private lastFocusedElement: HTMLElement | null = null;

    /**
     * Initialize a new ReservationFormModal instance
     *
     * @param egiId The ID of the EGI being reserved
     */
    constructor(egiId: number) {
        this.egiId = egiId;
        this.initModal();
    }

    /**
     * Initialize the modal by creating the necessary DOM elements
     *
     * @private
     */
    private async initModal(): Promise<void> {
        // Create modal if it doesn't exist yet
        if (!document.getElementById('reservation-modal')) {
            const modalHtml = this.generateModalHTML();
            const modalContainer = document.createElement('div');
            modalContainer.innerHTML = modalHtml;
            document.body.appendChild(modalContainer.firstElementChild as HTMLElement);
        }

        // Cache DOM elements
        this.modal = document.getElementById('reservation-modal');
        this.form = document.getElementById('reservation-form') as HTMLFormElement;
        this.closeButton = document.getElementById('close-reservation-modal');
        this.offerInput = document.getElementById('offer_amount_fiat') as HTMLInputElement;
        this.algoEquivalentText = document.getElementById('algo-equivalent-text');
        this.submitButton = document.querySelector('#reservation-form button[type="submit"]') as HTMLButtonElement;

        // Set up event listeners
        this.setupEventListeners();

        // Fetch current ALGO rate
        await this.updateAlgoRate();
    }

    /**
     * Set up event listeners for the modal
     *
     * @private
     */
    private setupEventListeners(): void {
        // Close button click
        this.closeButton?.addEventListener('click', () => this.close());

        // Click outside to close
        this.modal?.addEventListener('click', (e: MouseEvent) => {
            if (e.target === this.modal) {
                this.close();
            }
        });

        // Escape key to close
        document.addEventListener('keydown', (e: KeyboardEvent) => {
            if (e.key === 'Escape' && this.isOpen()) {
                this.close();
            }
        });

        // Update ALGO equivalent when offer amount changes
        this.offerInput?.addEventListener('input', () => this.updateAlgoEquivalent());

        // Validate numeric input for offer amount
        this.offerInput?.addEventListener('input', (e: Event) => {
            const target = e.target as HTMLInputElement;
            let value = target.value;

            // Remove any non-numeric characters except decimal point
            value = value.replace(/[^0-9.]/g, '');

            // Ensure only one decimal point
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            // Limit to 2 decimal places
            if (parts[1] && parts[1].length > 2) {
                value = parts[0] + '.' + parts[1].substring(0, 2);
            }

            // Update the input value if it changed
            if (target.value !== value) {
                target.value = value;
            }
        });

        // Form submission
        this.form?.addEventListener('submit', (e: Event) => this.handleSubmit(e));
    }

    /**
     * Load and display EGI information in the modal
     *
     * @private
     */
    private async loadEgiInfo(): Promise<void> {
        const infoSection = document.getElementById('egi-info-section');
        if (!infoSection) return;

        try {
            // Fetch EGI modal information from our new endpoint
            // Use the route from config with proper parameter replacement
            let url;
            try {
                const config = getAppConfig();
                if (config.routes?.api?.egiModalInfo) {
                    url = config.routes.api.egiModalInfo.replace(':egiId', this.egiId.toString());
                } else {
                    throw new Error('Route not found in config');
                }
            } catch (e) {
                // Fallback to hardcoded URL if route helper fails
                url = `/api/egis/${this.egiId}/modal-info`;
            }
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfTokenTS()
                }
            });

            if (!response.ok) {
                console.error('Response not OK:', response.status, response.statusText);
                throw new Error('HTTP error: ' + response.status);
            }

            const result = await response.json();

            if (result && result.success && result.data) {
                const data = result.data;
                let egiInfoHTML = '';

                // Mostra il titolo dell'EGI se disponibile
                if (data.title) {
                    egiInfoHTML += `
                        <div class="mb-3">
                            <h3 class="text-lg font-semibold text-gray-800">${data.title}</h3>
                        </div>
                    `;
                }

                // Mostra il prezzo corrente
                if (data.has_reservations && data.current_price) {
                    egiInfoHTML += `
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-green-700">Offerta Attuale più Alta:</span>
                            <span class="text-lg font-bold text-green-800">€${parseFloat(data.current_price).toFixed(2)}</span>
                        </div>
                    `;
                } else if (data.base_price) {
                    egiInfoHTML += `
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-amber-700">Prezzo Base:</span>
                            <span class="text-lg font-bold text-amber-800">€${parseFloat(data.base_price).toFixed(2)}</span>
                        </div>
                    `;
                }

                // Mostra informazioni sull'attivatore se esiste
                if (data.activator) {
                    egiInfoHTML += `
                        <div class="border-t border-green-200 pt-3">
                            <span class="text-sm font-medium text-green-700">Attuale Co Creatore:</span>
                            <div class="flex items-center gap-2 mt-1">
                    `;

                    if (data.activator.type === 'commissioner') {
                        // Mostra nome e avatar per commissioner
                        egiInfoHTML += `
                            ${data.activator.avatar ?
                                `<img src="${data.activator.avatar}" alt="Avatar" class="w-6 h-6 rounded-full">` :
                                `<div class="w-6 h-6 bg-amber-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>`
                            }
                            <span class="text-sm text-green-800 font-medium">${data.activator.name}</span>
                        `;
                    } else {
                        // Mostra solo icona e wallet per utenti anonimi
                        egiInfoHTML += `
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-green-800">Attivatore</span>
                                <span class="text-xs text-green-600 font-mono">${data.activator.wallet}</span>
                            </div>
                        `;
                    }

                    egiInfoHTML += `
                            </div>
                        </div>
                    `;
                }

                infoSection.innerHTML = egiInfoHTML;
            } else {
                // Errore nel caricamento
                infoSection.innerHTML = `
                    <div class="text-center text-amber-600">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.081 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <p class="text-sm">Impossibile caricare le informazioni dell'EGI</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading EGI info:', error);
            infoSection.innerHTML = `
                <div class="text-center text-red-600">
                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm">Errore nel caricamento delle informazioni</p>
                </div>
            `;
        }
    }

    /**
     * Open the reservation modal
     *
     * @returns {void}
     */
    public open(): void {
        if (!this.modal) return;

        // Save last focused element for accessibility
        this.lastFocusedElement = document.activeElement as HTMLElement;

        // Show modal
        this.modal.classList.remove('hidden');
        this.modal.classList.add('flex', 'items-center', 'justify-center');

        // Set focus on the offer input
        this.offerInput?.focus();

        // Prevent background scrolling
        document.body.style.overflow = 'hidden';

        // Load EGI information
        this.loadEgiInfo();
    }

    /**
     * Close the reservation modal
     *
     * @returns {void}
     */
    public close(): void {
        if (!this.modal) return;

        // Hide modal
        this.modal.classList.add('hidden');
        this.modal.classList.remove('flex', 'items-center', 'justify-center');

        // Restore focus to the element that was focused before the modal opened
        if (this.lastFocusedElement) {
            (this.lastFocusedElement as HTMLElement).focus();
        }

        // Restore background scrolling
        document.body.style.overflow = '';

        // Reset form
        this.form?.reset();
    }

    /**
     * Check if the modal is currently open
     *
     * @returns {boolean} True if the modal is open
     */
    public isOpen(): boolean {
        return this.modal ? !this.modal.classList.contains('hidden') : false;
    }

    /**
     * Handle form submission
     *
     * @param {Event} e The submit event
     * @private
     */
    private async handleSubmit(e: Event): Promise<void> {
        e.preventDefault();

        if (!this.form) return;

        try {
            // Disable submit button to prevent double submission
            if (this.submitButton) {
                this.submitButton.disabled = true;
                this.submitButton.innerHTML = '<svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
            }

            // Get form data
            const formData = new FormData(this.form);
            const data: ReservationFormData = {
                offer_amount_fiat: parseFloat(formData.get('offer_amount_fiat') as string),
                terms_accepted: formData.get('terms_accepted') === 'on',
                contact_data: {}
            };

            // Add contact data if present
            if (formData.get('contact_data[name]')) {
                data.contact_data!.name = formData.get('contact_data[name]') as string;
            }

            if (formData.get('contact_data[email]')) {
                data.contact_data!.email = formData.get('contact_data[email]') as string;
            }

            if (formData.get('contact_data[message]')) {
                data.contact_data!.message = formData.get('contact_data[message]') as string;
            }

            // If no contact data was provided, set to undefined
            if (Object.keys(data.contact_data!).length === 0) {
                data.contact_data = undefined;
            }

            // Submit reservation
            const response = await reserveEgi(this.egiId, data);

            // Handle response
            if (response.success) {
                // Close modal
                this.close();

                // Show success message
                // 🎯 AGGIORNA LA CARD IMMEDIATAMENTE PRIMA DI SWEETALERT!
                this.updateEgiDisplay(response);

                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'success',
                        title: appTranslate('reservation.success_title'),
                        text: response.message,
                        confirmButtonText: appTranslate('reservation.view_certificate'),
                        showCancelButton: true,
                        cancelButtonText: appTranslate('reservation.close')
                    }).then((result: { isConfirmed: boolean }) => {
                        // Card già aggiornata sopra!

                        if (result.isConfirmed && response.certificate) {
                            window.location.href = response.certificate.url;
                        }
                    });
                } else {
                    // Update EGI display immediately if no SweetAlert
                    // this.updateEgiDisplay(response); // Già chiamata sopra!

                    alert(response.message);
                    if (response.certificate) {
                        window.location.href = response.certificate.url;
                    }
                }
            } else {
                // Show error using UEM
                if (UEM && typeof UEM.handleClientError === 'function') {
                    UEM.handleClientError(
                        response.error_code || 'RESERVATION_UNKNOWN_ERROR',
                        { egiId: this.egiId },
                        undefined,
                        response.message
                    );
                } else {
                    // Fallback if UEM is not available
                    alert(response.message || 'An error occurred during reservation.');
                }
            }
        } catch (error) {
            console.error('Reservation submission error:', error);

            // Show error using UEM
            if (UEM && typeof UEM.handleClientError === 'function') {
                UEM.handleClientError('RESERVATION_SUBMISSION_ERROR', { error }, error instanceof Error ? error : undefined);
            } else {
                // Fallback if UEM is not available
                alert('An error occurred during reservation submission.');
            }
        } finally {
            // Re-enable submit button
            if (this.submitButton) {
                this.submitButton.disabled = false;
                this.submitButton.innerHTML = appTranslate('reservation.form.submit_button');
            }
        }
    }

    /**
     * Update the ALGO equivalent text based on the current EUR amount
     *
     * @private
     */
    private updateAlgoEquivalent(): void {
        if (!this.offerInput || !this.algoEquivalentText) return;

        const currentAlgoRate = getCachedAlgoRate();
        if (!currentAlgoRate) return;

        const eurAmount = parseFloat(this.offerInput.value) || 0;
        const algoAmount = (eurAmount / currentAlgoRate).toFixed(8);

        this.algoEquivalentText.textContent = appTranslate('reservation.form.algo_equivalent', { amount: algoAmount });
    }

    /**
     * Fetch the current ALGO/EUR exchange rate
     *
     * @private
     */
    private async updateAlgoRate(): Promise<void> {
        try {
            // Use the exchange rate if already fetched
            const cachedRate = getCachedAlgoRate();
            if (cachedRate !== null) {
                this.updateAlgoEquivalent();
                return;
            }

            // Otherwise fetch the current rate
            const rate = await getAlgoExchangeRate();
            if (rate !== null) {
                setCachedAlgoRate(rate);
                this.updateAlgoEquivalent();
            }
        } catch (error) {
            console.error('Failed to fetch ALGO exchange rate:', error);

            // Use fallback rate
            setCachedAlgoRate(0.2); // 1 EUR = 5 ALGO (fallback)
            this.updateAlgoEquivalent();
        }
    }

    /**
     * Update EGI display after successful reservation
     * 🎯 SEMPLIFICATO: Usa il sistema di aggiornamento automatico esistente!
     *
     * @private
     * @param response The reservation response
     */
    private updateEgiDisplay(response: ReservationResponse): void {
        try {
            // 🎯 TROVA TUTTI GLI ELEMENTI CON LO STESSO EGI ID!
            const allEgiElements = document.querySelectorAll(`[data-egi-id="${this.egiId}"]`);

            if (allEgiElements.length === 0) {
                console.error('❌ NESSUN ELEMENTO TROVATO per ID:', this.egiId);
                return;
            }

            // 🎯 AGGIORNA TUTTI GLI ELEMENTI CON LO STESSO EGI ID
            Array.from(allEgiElements).forEach((element, cardIndex) => {
                // 🎯 SKIP i bottoni - processiamo solo le card vere
                if (element.tagName === 'BUTTON') {
                    return;
                }

                // 🎯 Ora element è sicuramente una card (ARTICLE)
                const egiCard = element;

                // 🎯 GESTIONE SPECIFICA PER EGI-CARD-LIST
                const isEgiCardList = egiCard.classList.contains('egi-card-list') ||
                    egiCard.querySelector('.egi-card-list') ||
                    egiCard.closest('.egi-card-list');

                if (isEgiCardList) {
                    const modalUI = new ReservationModalUI();
                    modalUI.handleEgiCardListUpdate(egiCard, response);
                    return; // Skip normal processing per egi-card-list
                }

                // 💰 AGGIORNA PREZZO - USA DATA-PRICE-DISPLAY SPECIFICO
                if (response.data?.reservation?.offer_amount_fiat) {
                    const newPrice = parseFloat(response.data.reservation.offer_amount_fiat.toString()).toFixed(2);

                    // 🎯 USA IL SELETTORE DATA-PRICE-DISPLAY SPECIFICO
                    const priceElements = egiCard.querySelectorAll('[data-price-display]');

                    let priceUpdated = false;
                    Array.from(priceElements).forEach((el, idx) => {
                        if (el instanceof HTMLElement) {
                            el.textContent = `€${newPrice}`;
                            priceUpdated = true;

                            // Evidenziazione visiva
                            el.style.backgroundColor = '#fef3c7';
                            el.style.fontWeight = 'bold';
                            el.style.color = '#d97706';
                            setTimeout(() => {
                                el.style.backgroundColor = '';
                                el.style.fontWeight = '';
                                el.style.color = '';
                            }, 2000);
                        }
                    });

                    if (!priceUpdated) {
                        // Fallback con metodo precedente se il data-attribute non è ancora renderizzato
                        const fallbackElements = egiCard.querySelectorAll('.currency-display');

                        Array.from(fallbackElements).forEach((el, idx) => {
                            if (el instanceof HTMLElement && el.textContent?.includes('€')) {
                                el.textContent = `€${newPrice}`;
                                priceUpdated = true;

                                // Evidenziazione visiva
                                el.style.backgroundColor = '#fef3c7';
                                el.style.fontWeight = 'bold';
                                el.style.color = '#d97706';
                                setTimeout(() => {
                                    el.style.backgroundColor = '';
                                    el.style.fontWeight = '';
                                    el.style.color = '';
                                }, 2000);
                            }
                        });
                    }
                }

                // 👤 AGGIORNA ATTIVATORE - USA DATA-ACTIVATOR-NAME SPECIFICO
                const activatorElements = egiCard.querySelectorAll('[data-activator-name]');

                let activatorUpdated = false;

                // 📋 PRENDI I DATI DELL'UTENTE DALLA RESPONSE
                const userDetails = response.data?.user;

                // ✅ FIX: NON SOVRASCRIVERE SE IL BROADCAST HA GIÀ AGGIORNATO
                // Controlla se c'è già un nome valido (non generico) dall'aggiornamento broadcast
                const currentActivatorText = activatorElements.length > 0
                    ? (activatorElements[0] as HTMLElement).textContent?.trim()
                    : '';

                const hasValidActivatorFromBroadcast = currentActivatorText &&
                    currentActivatorText !== 'Utente' &&
                    currentActivatorText !== 'Anonymous' &&
                    currentActivatorText !== '';

                if (hasValidActivatorFromBroadcast) {
                    return; // Exit early, non aggiornare l'attivatore
                }

                // 🎯 CALCOLA IL NOME DELL'ATTIVATORE (solo se non già aggiornato dal broadcast)
                let userName = 'Utente'; // Fallback generico
                let isGenericName = true;

                if (userDetails?.name) {
                    userName = `${userDetails.name}`;
                    isGenericName = false;
                } else if (userDetails?.wallet) {
                    userName = userDetails.wallet.substring(0, 12) + '...';
                    isGenericName = false;
                } else {
                    // 🔄 Fallback: prova a prendere l'utente autenticato attuale
                    const currentUser = (window as any).user || (window as any).Laravel?.user;
                    if (currentUser?.name && currentUser?.last_name) {
                        userName = `${currentUser.name} ${currentUser.last_name}`;
                        isGenericName = false;
                    }
                }

                if (activatorElements.length > 0) {
                    // ✅ Aggiorna elementi esistenti
                    Array.from(activatorElements).forEach((el, idx) => {
                        if (el instanceof HTMLElement) {
                            el.textContent = userName;
                            activatorUpdated = true;

                            // Evidenziazione visiva
                            el.style.backgroundColor = '#dcfce7';
                            el.style.fontWeight = 'bold';
                            el.style.border = '1px solid #16a34a';

                            setTimeout(() => {
                                el.style.backgroundColor = '';
                                el.style.fontWeight = '';
                                el.style.border = '';
                            }, 3000);
                        }
                    });
                } else {
                    // 🆕 PER EGI-CARD: AGGIUNGI SOTTOSEZIONE ATTIVATORE DENTRO IL BOX PREZZO

                    // 👤 Determina se è un commissioner e avatar - DEVONO ESSERE QUI!
                    const isCommissioner = userDetails?.is_commissioner || false;
                    const avatarUrl = userDetails?.avatar || null;

                    // Cerca il div del prezzo (quello con border-green-500/30 e bg-gradient-to-r)
                    const priceSection = egiCard.querySelector('.border-green-500\\/30');

                    if (priceSection) {
                        // Controlla se esiste già una sottosezione attivatore
                        const existingActivatorSection = priceSection.querySelector('[data-activator-section]');

                        if (existingActivatorSection) {
                            // Aggiorna la sezione esistente
                            const activatorNameSpan = existingActivatorSection.querySelector('[data-activator-name]');
                            const activatorAvatar = existingActivatorSection.querySelector('.activator-avatar');

                            if (activatorNameSpan) {
                                activatorNameSpan.textContent = userName;
                            }

                            if (activatorAvatar && avatarUrl) {
                                activatorAvatar.outerHTML = `<img src="${avatarUrl}" alt="${userName}" class="object-cover w-4 h-4 border rounded-full border-white/20 activator-avatar">`;
                            }

                        } else {
                            // Crea nuova sottosezione attivatore
                            const activatorSubsection = document.createElement('div');
                            activatorSubsection.className = 'flex items-center gap-2 pt-2 border-t border-green-500/20';
                            activatorSubsection.setAttribute('data-activator-section', 'true');

                            // Avatar - ora usiamo sempre l'avatar dal backend
                            let avatarElement = '';
                            if (avatarUrl) {
                                avatarElement = `<img src="${avatarUrl}" alt="${userName}" class="object-cover w-4 h-4 border rounded-full border-white/20 activator-avatar">`;
                            } else {
                                // Fallback solo se non c'è avatar dal backend (caso molto raro)
                                avatarElement = `
                                    <div class="flex items-center justify-center flex-shrink-0 w-4 h-4 bg-green-600 rounded-full activator-avatar">
                                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                `;
                            }

                            activatorSubsection.innerHTML = `
                                ${avatarElement}
                                <span class="text-xs text-green-200 truncate">
                                    Attivatore: <span class="font-semibold" data-activator-name>${userName}</span>
                                </span>
                            `;

                            // Aggiungi la sottosezione alla fine del box prezzo
                            priceSection.appendChild(activatorSubsection);
                        }

                        activatorUpdated = true;
                    }
                }

                // 🎯 AGGIORNA BOTTONE DA "ATTIVALO" A "RILANCIA"
                const reserveButton = egiCard.querySelector('.reserve-button');

                if (reserveButton) {
                    // Aggiorna l'HTML del bottone completamente per "Rilancia"
                    reserveButton.innerHTML = `
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                        Rilancia
                    `;

                    // Cambia le classi CSS per il colore (da purple a amber/orange)
                    reserveButton.className = reserveButton.className
                        .replace(/bg-gradient-to-r from-purple-500 to-purple-600/, 'bg-gradient-to-r from-amber-500 to-orange-600')
                        .replace(/hover:from-purple-600 hover:to-purple-700/, 'hover:from-amber-600 hover:to-orange-700');

                } else {
                    // 🎯 PROVA A TROVARE IL BOTTONE CON METODI ALTERNATIVI
                    const allButtons = egiCard.querySelectorAll('button');
                    const buttonByText = Array.from(allButtons).find(btn =>
                        btn.textContent?.includes(appTranslate('reservation.button.reserve')) ||
                        btn.textContent?.includes('Reserve') ||
                        btn.textContent?.includes('Bid') ||
                        btn.innerHTML.includes(appTranslate('reservation.button.reserve'))
                    );

                    if (buttonByText) {
                        // Aggiorna questo bottone
                        buttonByText.innerHTML = `
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            Rilancia
                        `;

                        // Cambia le classi CSS per il colore
                        buttonByText.className = buttonByText.className
                            .replace(/bg-gradient-to-r from-purple-500 to-purple-600/, 'bg-gradient-to-r from-amber-500 to-orange-600')
                            .replace(/hover:from-purple-600 hover:to-purple-700/, 'hover:from-amber-600 hover:to-orange-700');
                    }
                }

                // 🎯 AGGIUNGI SEZIONE CONTEGGIO PRENOTAZIONI
                // Trova dove inserire la sezione (dopo le info del creator e collection)
                const collectionInfo = egiCard.querySelector('[data-collection-info]') ||
                    egiCard.querySelector('.flex.items-center.gap-2:has(.text-purple-500)') ||
                    egiCard.querySelector('.flex.items-center.gap-2:has(svg):has(.text-purple-300)');

                // Se non trova la collection info, cerca dopo le info del creator
                const insertAfter = collectionInfo ||
                    egiCard.querySelector('[data-creator-info]') ||
                    egiCard.querySelector('.flex.items-center.gap-2:has(.text-blue-500)');

                if (insertAfter && !egiCard.querySelector('[data-reservation-count]')) {
                    const reservationSection = document.createElement('div');
                    reservationSection.className = 'flex items-center gap-2 p-2 mb-2 border rounded-lg border-gray-700/50 bg-gray-800/50';
                    reservationSection.setAttribute('data-reservation-count', 'true');

                    reservationSection.innerHTML = `
                        <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 rounded-full bg-gradient-to-r from-green-500 to-emerald-500">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 00-2 2v6a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-1a1 1 0 100-2 2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-xs font-medium text-gray-300">
                                1 ${appTranslate('reservation.badge.reservation_singular')}
                            </span>
                        </div>
                    `;

                    // Inserisci dopo l'elemento trovato
                    insertAfter.parentNode?.insertBefore(reservationSection, insertAfter.nextSibling);

                    // Evidenziazione visiva per la nuova sezione
                    reservationSection.style.backgroundColor = '#dcfce7';
                    reservationSection.style.borderColor = '#16a34a';
                    setTimeout(() => {
                        reservationSection.style.backgroundColor = '';
                        reservationSection.style.borderColor = '';
                    }, 3000);

                } else if (egiCard.querySelector('[data-reservation-count]')) {
                    const existingSection = egiCard.querySelector('[data-reservation-count] .text-gray-300');
                    if (existingSection && existingSection instanceof HTMLElement) {
                        const currentCount = existingSection.textContent?.match(/(\d+)/)?.[1] || '0';
                        const newCount = parseInt(currentCount) + 1;
                        const labelKey = newCount === 1 ? 'reservation.badge.reservation_singular' : 'reservation.badge.reservation_plural';
                        existingSection.textContent = `${newCount} ${appTranslate(labelKey)}`;

                        // Evidenziazione visiva
                        existingSection.style.backgroundColor = '#dcfce7';
                        existingSection.style.fontWeight = 'bold';
                        setTimeout(() => {
                            existingSection.style.backgroundColor = '';
                            existingSection.style.fontWeight = '';
                        }, 2000);
                    }
                }

                // ✅ USA LA FUNZIONE DI REFRESH AUTOMATICO ESISTENTE
                // Simile a quella in collection-badge.blade.php che aggiorna ogni 5 secondi
                setTimeout(() => {
                    // ✅ USA GLI EVENTI CHE IL COLLECTION-BADGE GIÀ ASCOLTA!
                    // 1. collection-changed event
                    const collectionChangedEvent = new CustomEvent('collection-changed', {
                        detail: {
                            egiId: this.egiId,
                            reason: 'reservation-completed'
                        }
                    });
                    document.dispatchEvent(collectionChangedEvent);

                    // 2. collection-updated event
                    const collectionUpdatedEvent = new CustomEvent('collection-updated', {
                        detail: {
                            egiId: this.egiId,
                            reason: 'reservation-completed'
                        }
                    });
                    document.dispatchEvent(collectionUpdatedEvent);

                    // Forza anche il refresh della pagina se necessario per aggiornare le cifre
                    if (typeof window !== 'undefined' && window.location) {
                        setTimeout(() => {
                            // NO RELOAD! Questa è una SPA, non PHP anni 90!
                        }, 2000); // Aspetta 2 secondi prima del refresh
                    }

                }, 1000); // Aspetta 1 secondo per permettere al server di processare
            }); // CHIUDI IL FOREACH
        } catch (error) {
            console.error('❌ Errore nell\'aggiornamento EGI:', error);
        }
    }

    /**
     * Generate the HTML for the reservation modal
     *
     * @private
     * @returns {string} The modal HTML
     */
    private generateModalHTML(): string {

        const egiId = this.egiId;

        const authStatus = getAuthStatus(getAppConfig());

        if (authStatus === 'disconnected') {
            // Mostra messaggio o apri modal wallet connect
            if (window.Swal) {
                window.Swal.fire({
                    icon: 'info',
                    title: appTranslate('reservation.unauthorized'),
                    text: appTranslate('reservation.auth_required'),
                    confirmButtonText: appTranslate('wallet_connect_button'),
                    confirmButtonColor: '#3085d6'
                }).then((result: any) => {
                    if (result.isConfirmed) {
                        // Trigger apertura modale wallet
                        document.dispatchEvent(new CustomEvent('open-wallet-modal'));
                    }
                });
            }
            return '';
        }


        return `
        <div id="reservation-modal" class="fixed inset-0 z-[100] backdrop-blur-sm bg-black/60 bg-opacity-60 hidden" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1" aria-labelledby="reservation-modal-title">
            <div class="relative bg-gradient-to-b from-white to-amber-50 rounded-xl shadow-2xl max-w-2xl w-11/12 md:w-3/4 lg:w-2/5 max-h-[90vh] overflow-y-auto border border-amber-200" role="document" style="border-image: linear-gradient(45deg, #D4A574, #2D5016) 1;">
                <button id="close-reservation-modal" class="absolute w-8 h-8 flex items-center justify-center text-2xl leading-none text-amber-700 top-4 right-4 hover:text-amber-900 hover:bg-amber-100 rounded-full transition-all duration-200" aria-label="${appTranslate('reservation.form.close_button')}">&times;</button>

                <!-- Header con stile rinascimentale -->
                <div class="bg-gradient-to-r from-amber-600 to-amber-700 text-white p-6 rounded-t-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <h2 id="reservation-modal-title" class="text-xl font-bold">${appTranslate('reservation.form.title')}</h2>
                    </div>
                </div>

                <!-- Contenuto principale con padding elegante -->
                <div class="p-6 md:p-8">
                    <!-- Sezione informazioni EGI -->
                    <div id="egi-info-section" class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg">
                        <div class="animate-pulse">
                            <div class="h-4 bg-green-200 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-green-100 rounded w-1/2"></div>
                        </div>
                    </div>

                                    <!-- Form di prenotazione -->
                    <form id="reservation-form" method="POST" action="#" class="space-y-6">
                        <input type="hidden" name="_token" value="${getCsrfTokenTS()}">

                        <div>
                            <label for="offer_amount_fiat" class="block text-sm font-medium text-gray-800 mb-2">
                                <span class="text-amber-700 font-semibold">${appTranslate('reservation.form.offer_amount_label')}</span>
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-amber-600 font-medium text-lg">€</span>
                                </div>
                                <input type="text" name="offer_amount_fiat" id="offer_amount_fiat"
                                       class="block w-full pl-12 pr-12 py-3 text-lg border-2 border-amber-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white placeholder-gray-400 transition-all duration-200"
                                       placeholder="${appTranslate('reservation.form.offer_amount_placeholder')}"
                                       pattern="[0-9]+(\.[0-9]{1,2})?" inputmode="decimal" required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-amber-600 text-sm font-medium">EUR</span>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-green-700 font-medium" id="algo-equivalent-text">
                                ${appTranslate('reservation.form.algo_equivalent', { amount: '0.00' })}
                            </p>
                        </div>

                        <div class="flex items-start p-4 bg-amber-50 border border-amber-200 rounded-lg">
                            <div class="flex items-center h-5">
                                <input id="terms_accepted" name="terms_accepted" type="checkbox" required
                                       class="focus:ring-amber-500 h-4 w-4 text-amber-600 border-amber-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms_accepted" class="font-medium text-gray-800">
                                    ${appTranslate('reservation.form.terms_accepted')}
                                </label>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-lg shadow-lg text-lg font-semibold text-white bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-200 transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                ${appTranslate('reservation.form.submit_button')}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>`;
    }
}
