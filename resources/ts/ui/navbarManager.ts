// File: resources/ts/ui/navbarManager.ts

/**
 * 📜 Oracode TypeScript Module: NavbarUIManager
 * Gestisce l'aggiornamento dinamico dell'interfaccia utente della navbar principale
 * in base allo stato di autenticazione dell'utente e alla collection corrente.
 * Orchestra la visibilità dei menu, dei bottoni e del badge della collection.
 *
 * @version 1.0.1 (Padmin Corrected Translation Calls, No Placeholders, Refined Logic)
 * @date 2025-05-11
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

import { AppConfig, appTranslate } from '../config/appConfig';
import * as DOMElements from '../dom/domElements';
import { getAuthStatus, getConnectedWalletAddress } from '../features/auth/authService';
import { initializeUserCollectionState, resetCollectionStateOnLogout } from '../features/collections/collectionUI';
import { UEM_Client_TS_Placeholder as UEM } from '../services/uemClientService'; // Se serve per log specifici, altrimenti non necessario qui

/**
 * 📜 Oracode Function: updateNavbarUI
 * 🎯 Aggiorna l'intera interfaccia della Navbar e coordina l'inizializzazione/reset
 *    dello stato delle collection in base allo stato di autenticazione.
 *    Questa è la funzione centrale per mantenere la coerenza visiva della navbar.
 *
 * @export
 * @param {AppConfig} config L'oggetto di configurazione dell'applicazione.
 * @param {typeof DOMElements} DOM Collezione dei riferimenti agli elementi DOM.
 */
export function updateNavbarUI(config: AppConfig, DOM: typeof DOMElements, uem: typeof UEM): void {
    // console.log('--- DEBUG NAVBAR_MANAGER: Funzione updateNavbarUI chiamata. Ispeziono il parametro uem ricevuto:', uem); // <-- AGGIUNGI QUESTO
    // console.log('🔄 updateNavbarUI called with auth status:', getAuthStatus(config));
    // console.log('🔄 config.isAuthenticatedByBackend:', config.isAuthenticated);


    const authStatus = getAuthStatus(config);
    const walletAddressRaw = getConnectedWalletAddress(config);
    // CRITICAL FIX: Ensure walletAddress is a string before using substring
    // Sometimes it might be an object or other type due to payment system changes
    const walletAddress = (typeof walletAddressRaw === 'string' ? walletAddressRaw : (walletAddressRaw ? String(walletAddressRaw) : null));
    const shortAddress = walletAddress && typeof walletAddress === 'string' && walletAddress.length > 0
        ? `${walletAddress.substring(0, 6)}...${walletAddress.substring(walletAddress.length - 4)}`
        : appTranslate('walletDefaultText', config.translations);

    // --- Cache degli elementi DOM usati frequentemente in questa funzione ---
    const {
        connectWalletButtonStdEl, walletDropdownContainerEl, walletDisplayTextEl, walletDropdownButtonEl,
        loginLinkDesktopEl, registerLinkDesktopEl,
        connectWalletButtonMobileEl, mobileAuthButtonsContainerEl,
        collectionListDropdownContainerEl, genericCollectionsLinkDesktopEl, // genericCollectionsLinkMobileEl è gestito sotto
        mobileMenuEl // Necessario per trovare il link mobile Collections
    } = DOM;

    // 1. Gestione UI Wallet (Desktop)
    if (connectWalletButtonStdEl && walletDropdownContainerEl && walletDisplayTextEl && walletDropdownButtonEl) {
        const showWalletDropdown = authStatus === 'logged-in' || authStatus === 'connected';
        connectWalletButtonStdEl.classList.toggle('hidden', showWalletDropdown);
        walletDropdownContainerEl.classList.toggle('hidden', !showWalletDropdown);

        if (showWalletDropdown) {
            walletDisplayTextEl.textContent = shortAddress;

            // Aggiorna anche l'indirizzo wallet mobile
            if (DOM.mobileWalletAddressEl) {
                DOM.mobileWalletAddressEl.textContent = shortAddress;
            }

            const labelKey = authStatus === 'logged-in' ? 'walletAriaLabelLoggedIn' : 'walletAriaLabelConnected';
            const statusTextKey = authStatus === 'logged-in' ? 'loggedInStatus' : 'connectedStatusWeak';
            const label = appTranslate(labelKey, config.translations, {
                shortAddress: shortAddress,
                status: appTranslate(statusTextKey, config.translations)
            });
            walletDropdownButtonEl.setAttribute('aria-label', label);

            const isLoggedIn = authStatus === 'logged-in';
            walletDropdownButtonEl.classList.toggle('bg-green-600', isLoggedIn);
            walletDropdownButtonEl.classList.toggle('hover:bg-green-700', isLoggedIn);
            walletDropdownButtonEl.classList.toggle('text-white', isLoggedIn);

            walletDropdownButtonEl.classList.toggle('bg-indigo-600', !isLoggedIn && authStatus === 'connected');
            walletDropdownButtonEl.classList.toggle('hover:bg-indigo-700', !isLoggedIn && authStatus === 'connected');
            walletDropdownButtonEl.classList.toggle('text-white', !isLoggedIn && authStatus === 'connected');
        }
    }
    if (loginLinkDesktopEl) loginLinkDesktopEl.style.display = authStatus === 'logged-in' ? 'none' : 'inline-flex';
    if (registerLinkDesktopEl) registerLinkDesktopEl.style.display = authStatus === 'logged-in' ? 'none' : 'inline-flex';

    // 2. Gestione UI Wallet (Mobile)
    if (connectWalletButtonMobileEl) {
        connectWalletButtonMobileEl.style.display = authStatus === 'disconnected' ? 'inline-flex' : 'none';
    }

    // Mobile wallet info container - mostra quando connesso (wallet o auth)
    if (DOM.mobileWalletInfoContainerEl) {
        DOM.mobileWalletInfoContainerEl.style.display =
            (authStatus === 'connected' || authStatus === 'logged-in') ? 'block' : 'none';
    }

    // Mobile dashboard link - mostra quando connesso (wallet o auth)
    if (DOM.mobileDashboardLinkEl) {
        DOM.mobileDashboardLinkEl.style.display =
            (authStatus === 'connected' || authStatus === 'logged-in') ? 'flex' : 'none';
    }

    if (mobileAuthButtonsContainerEl) {
        mobileAuthButtonsContainerEl.style.display = authStatus === 'logged-in' ? 'none' : 'flex';
    }

    // 3. Gestione UI "Collection List" Dropdown e Link "Collections" Generici
    // Trova il link "Collections" nel menu mobile (potrebbe non essere sempre genericCollectionsLinkMobileEl se la struttura cambia)
    const mobileCollectionsLink = mobileMenuEl?.querySelector<HTMLAnchorElement>(`a[href="${config.routes.homeCollectionsIndex}"]:not([data-action])`);


    if (collectionListDropdownContainerEl && genericCollectionsLinkDesktopEl) {
        if (authStatus === 'logged-in') {
            collectionListDropdownContainerEl.style.display = 'block'; // o 'relative' o il display corretto
            genericCollectionsLinkDesktopEl.style.display = 'none';
            if (mobileCollectionsLink) mobileCollectionsLink.style.display = 'none';
            else if (DOM.genericCollectionsLinkMobileEl) DOM.genericCollectionsLinkMobileEl.style.display = 'none'; // Fallback

            // Mostra dropdown mobile "Le mie Collezioni" per utenti loggati
            if (DOM.mobileCollectionListDropdownButtonEl) {
                DOM.mobileCollectionListDropdownButtonEl.style.display = 'flex';
            }

            // Chiama l'inizializzazione dello stato delle collection (carica dati per dropdown e badge)
            // Passa UEM anche se non direttamente usato qui, per coerenza con la firma di initializeUserCollectionState
            initializeUserCollectionState(config, DOM, UEM);
        } else { // Utente 'connected' o 'disconnected'
            collectionListDropdownContainerEl.style.display = 'none';
            resetCollectionStateOnLogout(DOM); // Pulisce UI e stato del dropdown/badge delle collection

            // Nascondi dropdown mobile "Le mie Collezioni" per utenti non loggati
            if (DOM.mobileCollectionListDropdownButtonEl) {
                DOM.mobileCollectionListDropdownButtonEl.style.display = 'none';
            }

            genericCollectionsLinkDesktopEl.style.display = 'inline-flex';
            if (mobileCollectionsLink) mobileCollectionsLink.style.display = 'block'; // o il suo display originale
            else if (DOM.genericCollectionsLinkMobileEl) DOM.genericCollectionsLinkMobileEl.style.display = 'block'; // Fallback
        }
    } else {
        console.warn("Padmin NavbarManager: Core DOM elements for Collection List/Generic Links not all found. UI may be inconsistent.");
    }
        // console.log(`Padmin NavbarManager: UI Updated. Auth Status: ${authStatus}`);

    initButlerMenuButton();
}

export function initButlerMenuButton() {
    const butlerBtn = document.getElementById('open-butler-assistant');
    if (butlerBtn) {
        butlerBtn.addEventListener('click', () => {
            if (window.natanAssistant && typeof window.natanAssistant.showButlerManually === 'function') {
                window.natanAssistant.showButlerManually();
            }
        });
    }
}
