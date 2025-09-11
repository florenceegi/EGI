// File: resources/ts/main.ts (versione unificata con orchestrazione consapevole)

/**
 * 📜 Oracode TypeScript Module: Unified Application Entry Point (FlorenceEGI + Dependencies)
 * @version 5.0.0 (Unified Orchestrated Initialization)
 * @date 2025-07-02
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * 🎯 Purpose: Single point orchestrated initialization respecting dependency order
 * 🛡️ Security: Proactive error handling and dependency validation
 * 🧱 Core Logic: Translations → UEM → Enums → Wallet Modules → Ultra Coordination → FEGI System
 */

// --- ⚙️ IMPORTAZIONI MODULI CORE FEGI ---
import { initializeAppConfig, AppConfig, appTranslate } from './config/appConfig';
import * as DOMElements from './dom/domElements';
import { getCsrfTokenTS } from './utils/csrf';
import { UploadModalManager, UploadModalDomElements } from './ui/uploadModalManager';
import likeUIManager from './ui/likeUIManager';

// --- 🔧 IMPORTAZIONE TYPES PER CUSTOM EVENTS ---
import './types/customEvents';

// --- 🛠️ IMPORTAZIONI FUNZIONALITÀ FEGI ---
import {
    openSecureWalletModal,
    closeSecureWalletModal
} from './features/auth/walletConnect';
import { getAuthStatus } from './features/auth/authService';
import {
    copyWalletAddress,
    handleDisconnect,
    toggleWalletDropdownMenu
} from './features/auth/walletDropdown';
import {
    toggleCollectionListDropdown,
    toggleMobileCollectionListDropdown
} from './features/collections/collectionUI';
import { toggleMobileMenu } from './features/mobile/mobileMenu';
import { updateNavbarUI } from './ui/navbarManager';
import { UEM } from './services/uemClientService';
import reservationFeature from './features/reservations/reservationFeature';
import reservationButtons from './features/reservations/reservationButtons';
import { initPortfolioManager } from './features/portfolio/portfolioManager'; // 🚀 NEW
import { NatanAssistant } from './components/natan-assistant';
import { mountAllCurrentPrices } from './current-price'; // 🔴 Real-time price updates
import { initializeStatsRealTime } from './stats-realtime'; // 📊 Real-time statistics updates
import { autoInitUniversalSearch } from './features/search/universalSearch';

// --- 💰 IMPORTAZIONI SISTEMA MULTI-VALUTA (Enterprise Financial System) ---
import { currencyService } from './services/currencyService';
import { currencyDisplayManager } from './ui/currencyDisplayManager';
import { CurrencySelectorComponent } from './components/currencySelectorComponent';
import { CurrencyDisplayComponent } from './components/CurrencyDisplayComponent';

// --- 📦 IMPORTAZIONI DIPENDENZE ESTERNE (ora gestite da app.js) ---
// jQuery, SweetAlert2, etc. sono già disponibili globalmente via app.js

// --- 🔄 IMPORTAZIONI UTILITIES (per inizializzazione orchestrata) ---
import { fetchTranslations, ensureTranslationsLoaded, getTranslation } from '../js/utils/translations';
import { loadEnums, getEnum, isPendingStatus } from '../js/utils/enums';

// --- 🏦 IMPORTAZIONI MODULI WALLET ---
import {
    RequestCreateNotificationWallet,
    RequestUpdateNotificationWallet,
    RequestWalletDonation,
} from '../js/modules/notifications/init/request-notification-wallet-init';
import { DeleteProposalInvitation } from '../js/modules/notifications/delete-proposal-invitation';
import { DeleteProposalWallet } from '../js/modules/notifications/delete-proposal-wallet';

// --- 🎮 IMPORTAZIONI ANIMAZIONE INDIPENDENTE ---
import { initThreeAnimation } from '../js/sfera-geodetica';

// --- ✨ ISTANZE GLOBALI DEL MODULO MAIN ---
let mainAppConfig: AppConfig;
let mainUploadModalManager: UploadModalManager | null = null;

// --- 🏦 ISTANZE MODULI WALLET (per evitare inizializzazioni multiple) ---
let walletCreateInstance: InstanceType<typeof RequestCreateNotificationWallet> | null = null;
let walletUpdateInstance: InstanceType<typeof RequestUpdateNotificationWallet> | null = null;
let walletDonationInstance: InstanceType<typeof RequestWalletDonation> | null = null;
let deleteProposalInvitationInstance: InstanceType<typeof DeleteProposalInvitation> | null = null;
let deleteProposalWalletInstance: InstanceType<typeof DeleteProposalWallet> | null = null;

/**
 * 📜 Safe Event Binding Helper
 * 🎯 Purpose: Binding sicuro degli eventi con controllo elementi
 * 🛡️ Security: Evita errori quando elementi DOM non esistono per mancanza permessi
 */
function safeAddEventListener(
    element: Element | null,
    event: string,
    handler: EventListener,
    description?: string
): boolean {
    if (element && typeof element.addEventListener === 'function') {
        element.addEventListener(event, handler);
        // // console.log(`✅ Event listener added: ${description || 'unknown'}`);
        return true;
    } else {
        // // console.log(`⚠️ Element not found, skipping listener: ${description || 'unknown'}`);
        return false;
    }
}

/**
 * 📜 Oracode Function: waitForGlobalDependencies
 * 🎯 Purpose: Aspetta che le dipendenze globali siano disponibili prima di procedere
 * 🛡️ Security: Valida la presenza delle dipendenze critiche
 */
async function waitForGlobalDependencies(): Promise<void> {
    const maxAttempts = 50; // 5 secondi max
    let attempts = 0;

    return new Promise((resolve, reject) => {
        const checkDependencies = () => {
            attempts++;

            if (
                typeof window.$ !== 'undefined' &&
                typeof window.Swal !== 'undefined' &&
                typeof window.jQuery !== 'undefined'
            ) {
                // console.log('Padmin Main: Global dependencies confirmed available.');
                resolve();
            } else if (attempts >= maxAttempts) {
                const missing = [
                    typeof window.$ === 'undefined' ? 'jQuery ($)' : null,
                    typeof window.Swal === 'undefined' ? 'SweetAlert2' : null,
                    typeof window.jQuery === 'undefined' ? 'jQuery (jQuery)' : null
                ].filter(Boolean).join(', ');

                reject(new Error(`Global dependencies not available after ${maxAttempts} attempts: ${missing}`));
            } else {
                setTimeout(checkDependencies, 100);
            }
        };

        checkDependencies();
    });
}

/**
 * 📜 Oracode Function: initializeTranslationsOrchestrated
 * 🎯 Purpose: Inizializza il sistema di traduzioni con gestione orchestrata
 * 🧱 Core Logic: Prima fase dell'orchestrazione - traduzioni base
 */
async function initializeTranslationsOrchestrated(): Promise<void> {
    try {
        await fetchTranslations();
        await ensureTranslationsLoaded();

        // Rende disponibili le funzioni globalmente per compatibilità
        window.getTranslation = getTranslation;
        window.appTranslate = appTranslate; // Sistema moderno di traduzioni
        window.ensureTranslationsLoaded = ensureTranslationsLoaded;

        // console.log('Padmin Main: Translations system initialized successfully.');
    } catch (error) {
        console.error('Padmin Main: Critical error in translations initialization:', error);
        throw new Error(`Translations initialization failed: ${error instanceof Error ? error.message : String(error)}`);
    }
}

/**
 * 📜 Oracode Function: initializeUEMOrchestrated
 * 🎯 Purpose: Inizializza UEM con gestione errori orchestrata
 * 🧱 Core Logic: Seconda fase - sistema errori centralizzato
 */
async function initializeUEMOrchestrated(): Promise<void> {
    try {
        if (UEM && typeof UEM.initialize === 'function') {
            await UEM.initialize();
            // console.log('Padmin Main: UEM Client Service initialized successfully.');
        } else {
            throw new Error('UEM service or initialize method not available');
        }
    } catch (error) {
        console.error('Padmin Main: Critical error in UEM initialization:', error);
        throw new Error(`UEM initialization failed: ${error instanceof Error ? error.message : String(error)}`);
    }
}

/**
 * 📜 Oracode Function: initializeCurrencySystemOrchestrated
 * 🎯 Purpose: Initializes multi-currency system for enterprise financial operations
 * 🛡️ Security: Handles REAL MONEY operations with proper error management
 * 💱 Multi-Currency: Sets up currency display manager and selector components
 */
async function initializeCurrencySystemOrchestrated(): Promise<void> {
    try {
        // console.log('Padmin Main: Initializing Multi-Currency System (Enterprise Financial)...');

        // Initialize currency display manager
        await currencyDisplayManager.initialize();
        // console.log('Padmin Main: Currency display manager initialized successfully.');

        // Initialize currency selector for badge
        const currencyBadge = document.getElementById('currency-badge-desktop');
        if (currencyBadge) {
            const currencySelector = new CurrencySelectorComponent();
            await currencySelector.initialize();
            // console.log('Padmin Main: Currency selector component initialized successfully.');
        } else {
            // console.log('Padmin Main: No currency badge found, skipping selector initialization.');
        }

        // Initialize FAST currency display component with proper async handling
        const currencyDisplay = new CurrencyDisplayComponent();
        await currencyDisplay.initialize();
        // console.log('Padmin Main: FAST Currency display component initialized successfully.');

        // Test currency service connectivity
        const testRate = await currencyService.getExchangeRate('USD');
        if (testRate) {
            // console.log('Padmin Main: Currency service connectivity test successful', { rate: testRate.rate });
        } else {
            console.warn('Padmin Main: Currency service connectivity test failed, but system will continue with fallbacks.');
        }

        // console.log('Padmin Main: Multi-Currency System initialization complete.');

    } catch (error) {
        console.error('Padmin Main: Multi-Currency System initialization error:', error);

        // Log to UEM if available
        if (UEM && typeof UEM.handleClientError === 'function') {
            UEM.handleClientError('CURRENCY_SYSTEM_INIT_ERROR', {
                error: error instanceof Error ? error.message : String(error),
                stack: error instanceof Error ? error.stack : undefined
            });
        }

        // Don't throw - let app continue without currency features
        // console.log('Padmin Main: Continuing without multi-currency features due to initialization error.');
    }
}

/**
 * 📜 Oracode Function: initializeEnumsOrchestrated
 * 🎯 Purpose: Inizializza sistema enum con disponibilità globale
 * 🧱 Core Logic: Terza fase - enum e utilities di stato
 */
async function initializeEnumsOrchestrated(): Promise<void> {
    try {
        await loadEnums();

        // Rende disponibili le funzioni globalmente per compatibilità
        window.getEnum = getEnum;
        window.isPendingStatus = isPendingStatus;

        // console.log('Padmin Main: Enums system initialized successfully.');
    } catch (error) {
        console.error('Padmin Main: Critical error in enums initialization:', error);
        throw new Error(`Enums initialization failed: ${error instanceof Error ? error.message : String(error)}`);
    }
}

/**
 * 📜 Oracode Function: initializeWalletModulesOrchestrated
 * 🎯 Purpose: Inizializza tutti i moduli wallet evitando inizializzazioni multiple
 * 🧱 Core Logic: Quarta fase - moduli wallet e notifiche
 */
async function initializeWalletModulesOrchestrated(): Promise<void> {
    try {
        // RequestCreateNotificationWallet
        if (!walletCreateInstance) {
            walletCreateInstance = new RequestCreateNotificationWallet({ apiBaseUrl: '/notifications' });
            // console.log('Padmin Main: RequestCreateNotificationWallet initialized.');
        }

        // RequestUpdateNotificationWallet
        if (!walletUpdateInstance) {
            walletUpdateInstance = new RequestUpdateNotificationWallet({ apiBaseUrl: '/notifications' });
            // console.log('Padmin Main: RequestUpdateNotificationWallet initialized.');
        }

        // RequestWalletDonation
        if (!walletDonationInstance) {
            walletDonationInstance = new RequestWalletDonation({ apiBaseUrl: '/notifications' });
            // console.log('Padmin Main: RequestWalletDonation initialized.');
        }

        // DeleteProposalInvitation
        if (!deleteProposalInvitationInstance) {
            deleteProposalInvitationInstance = new DeleteProposalInvitation({ apiBaseUrl: '/notifications' });
            // console.log('Padmin Main: DeleteProposalInvitation initialized.');
        }

        // DeleteProposalWallet
        if (!deleteProposalWalletInstance) {
            deleteProposalWalletInstance = new DeleteProposalWallet({ apiBaseUrl: '/notifications' });
            // console.log('Padmin Main: DeleteProposalWallet initialized.');
        }

        // console.log('Padmin Main: All wallet modules initialized successfully.');
    } catch (error) {
        console.error('Padmin Main: Error in wallet modules initialization:', error);
        UEM.handleClientError('CLIENT_INIT_FAIL_WALLET_MODULES', {
            originalError: error instanceof Error ? error.message : String(error)
        });
        throw error;
    }
}

/**
 * 📜 Oracode Function: initializeThreeAnimationIndependent
 * 🎯 Purpose: Inizializza animazione Three.js se necessaria (indipendente)
 * 🧱 Core Logic: Processo indipendente - animazione sfera geodetica
 */
function initializeThreeAnimationIndependent(): void {
    try {
        // Controlla se ci sono elementi necessari per l'animazione sulla pagina
        if (document.getElementById('dynamic-3d-container') && document.getElementById('webgl-canvas')) {
            initThreeAnimation();
            // console.log('Padmin Main: Three.js animation initialized independently.');
        } else {
            // console.log('Padmin Main: Three.js animation elements not found - skipping initialization.');
        }
    } catch (error) {
        console.error('Padmin Main: Error in Three.js animation initialization:', error);
        // Non blocca l'applicazione - l'animazione è indipendente
    }
}

/**
 * 📜 Oracode Function: initializeFEGISystemOrchestrated
 * 🎯 Purpose: Inizializza il sistema FEGI completo (include UploadModalManager dopo DOM confirmed)
 * 🧱 Core Logic: Sesta fase - DOM → Config → UploadModalManager → Eventi → UI
 */
/**
 * 📜 Oracode Function: initializeFEGISystemOrchestrated
 * 🎯 Purpose: Inizializza il sistema FEGI completo (include UploadModalManager dopo DOM confirmed)
 * 🧱 Core Logic: Sesta fase - DOM → Config → UploadModalManager → Eventi → UI
 */
async function initializeFEGISystemOrchestrated(): Promise<void> {
    try {
        // 1. Inizializza i riferimenti DOM
        DOMElements.initializeDOMReferences();

        // 2. Setup listener per apertura wallet modal
        document.addEventListener('open-wallet-modal', () => {
            openSecureWalletModal(mainAppConfig, DOMElements, null);
        });

        // 3. Carica configurazione dal server
        mainAppConfig = await initializeAppConfig();
        // // console.log(`${appTranslate('padminGreeting', mainAppConfig?.translations || { padminGreeting: 'Padmin' })} FEGI Configuration loaded successfully.`);

        // 4. Conferma riferimenti DOM
        DOMElements.confirmDOMReferencesLoaded();
        // console.log('Padmin Main: DOM references confirmation check complete.');

        // 5. Inizializza UploadModalManager (dopo conferma DOM)
        if (DOMElements.uploadModalEl && DOMElements.uploadModalCloseButtonEl && DOMElements.uploadModalContentEl) {
            const uploadModalDOMElements: UploadModalDomElements = {
                modal: DOMElements.uploadModalEl,
                closeButton: DOMElements.uploadModalCloseButtonEl,
                modalContent: DOMElements.uploadModalContentEl
            };
            mainUploadModalManager = new UploadModalManager(uploadModalDOMElements, mainAppConfig.csrf_token);
            // console.log('Padmin Main: UploadModalManager initialized.');
        } else {
            const missingElements = [
                !DOMElements.uploadModalEl ? '#upload-modal' : null,
                !DOMElements.uploadModalCloseButtonEl ? '#close-upload-modal' : null,
                !DOMElements.uploadModalContentEl ? '#upload-container' : null,
            ].filter(Boolean).join(', ');
            console.error(`Padmin Main: Cannot initialize UploadModalManager - DOM elements missing: ${missingElements}`);
            UEM.handleClientError('CLIENT_INIT_FAIL_UPLOAD_MODAL_MAIN_TS', { reason: `DOM elements missing for UploadModal: ${missingElements}` });
        }

        // 6. Setup event listeners (inclusi quelli FEGI)
        setupEventListeners();

        // 7. Aggiorna UI navbar
        updateNavbarUI(mainAppConfig, DOMElements, UEM);
        // console.log('Padmin Main: Initial navbar UI update performed.');

        // 8. Inizializza il sistema di like
        if (likeUIManager && typeof likeUIManager.initialize === 'function') {
            likeUIManager.initialize(mainAppConfig);
            // console.log('Padmin Main: Like system initialized.');
        } else {
            console.warn('Padmin Main: likeUIManager or its initialize method not found.');
        }

        // 9. Inizializza il sistema di prenotazione
        if (reservationFeature && typeof reservationFeature.initialize === 'function') {
            // console.log('Padmin Main: Reservation feature initialized.');
        } else {
            console.warn('Padmin Main: reservationFeature or its initialize method not found.');
        }

        // 10. Inizializza i bottoni di prenotazione
        if (reservationButtons && typeof reservationButtons.initialize === 'function') {
            await reservationButtons.initialize();
            // console.log('Padmin Main: Reservation buttons initialized.');
        } else {
            console.warn('Padmin Main: reservationButtons or its initialize method not found.');
        }

        // 🚀 11. Inizializza Portfolio Manager (NEW)
        try {
            const portfolioManager = initPortfolioManager();
            (window as any).portfolioManager = portfolioManager;
            // console.log('Padmin Main: Portfolio manager initialized successfully.');
        } catch (error) {
            console.warn('Padmin Main: Portfolio manager initialization failed:', error);
        }

        // 12. Inizializza Natan Assistant
        // try {
        //     if (typeof NatanAssistant === 'function') {
        //         const natanAssistant = new NatanAssistant();
        //         (window as any).natan = natanAssistant;
        //         (window as any).natanAssistant = natanAssistant;
        //         (window as any).testButlerModal = () => {
        //             // console.log('🎩 TEST: Forcing butler modal display from global function');
        //             natanAssistant.forceShowModal();
        //         };
        //         (window as any).testButler = () => {
        //             // console.log('🎩 TEST: Complete butler test with state reset');
        //             natanAssistant.testButler();
        //         };
        //         (window as any).resetButler = () => {
        //             // console.log('🎩 TEST: Resetting butler state');
        //             natanAssistant.resetButler();
        //         };
        //         // console.log('Padmin Main: Natan Assistant initialized.');
        //         // console.log('🎩 Available test functions:');
        //         // console.log('- window.testButlerModal() - Force show modal');
        //         // console.log('- window.testButler() - Complete test with reset');
        //         // console.log('- window.resetButler() - Reset state only');
        //     } else {
        //         console.warn('Padmin Main: NatanAssistant is not a constructor or function.');
        //     }
        // } catch (error) {
        //     console.error('Padmin Main: Error initializing Natan Assistant:', error);
        //     UEM.handleClientError('CLIENT_INIT_FAIL_NATAN_TS', { originalError: error instanceof Error ? error.message : String(error) });
        // }

        // 12. Setup FEGI-specific custom event listeners
        setupFegiCustomEvents();

        // console.log('Padmin Main: FEGI System initialized successfully.');

    } catch (error) {
        console.error('Padmin Main: Critical error in FEGI system initialization:', error);
        throw error;
    }
}

/**
 * 📜 Oracode Function: initializeApplicationOrchestrated
 * 🎯 Funzione principale di inizializzazione orchestrata dell'applicazione
 * 🛡️ Security: Gestione proattiva errori con rollback e user feedback
 * 🧱 Core Logic: Sequenza orchestrata - Dependencies → Translations → UEM → Enums → Wallet → Ultra → FEGI
 */
async function initializeApplicationOrchestrated(): Promise<void> {
    try {
        // console.log('Padmin Main: Starting orchestrated initialization sequence...');

        // FASE 0: Aspetta dipendenze globali (da app.js)
        // console.log('Padmin Main: Phase 0 - Waiting for global dependencies...');
        await waitForGlobalDependencies();

        // FASE 1: Traduzioni (primo requisito)
        // console.log('Padmin Main: Phase 1 - Initializing translations...');
        await initializeTranslationsOrchestrated();

        // FASE 2: UEM (secondo requisito)
        // console.log('Padmin Main: Phase 2 - Initializing UEM...');
        await initializeUEMOrchestrated();

        // FASE 3: Enum (terzo requisito)
        // console.log('Padmin Main: Phase 3 - Initializing enums...');
        await initializeEnumsOrchestrated();

        // FASE 4: Moduli Wallet (quarto requisito)
        // console.log('Padmin Main: Phase 4 - Initializing wallet modules...');
        await initializeWalletModulesOrchestrated();

        // FASE 5: Ultra Upload Manager (preparazione, NON inizializzazione completa)
        // console.log('Padmin Main: Phase 5 - Preparing Ultra Upload Manager...');
        // NON chiamiamo initializeUltraUploadManager() qui - troppo presto!
        // console.log('Padmin Main: Ultra Upload Manager preparation complete (deferred initialization).');

        // FASE 6: Sistema Multi-Valuta (Enterprise Financial System)
        // console.log('Padmin Main: Phase 6 - Initializing Multi-Currency System...');
        await initializeCurrencySystemOrchestrated();

        // FASE 6.1: Real-time Price Updates
        // console.log('Padmin Main: Phase 6.1 - Initializing real-time price updates...');
        mountAllCurrentPrices();

        // FASE 6.2: Real-time Statistics Updates
        // console.log('Padmin Main: Phase 6.2 - Initializing real-time statistics updates...');
        initializeStatsRealTime();

        // FASE 6.3: Universal Search (vanilla)
        autoInitUniversalSearch();

        // FASE 7: Sistema FEGI (include UploadModalManager + Ultra Upload Manager on-demand)
        // console.log('Padmin Main: Phase 7 - Initializing FEGI system...');
        await initializeFEGISystemOrchestrated();

        // FASE INDIPENDENTE: Animazione Three.js (se necessaria)
        initializeThreeAnimationIndependent();

        // FASE FINALE: Aggiunge classe page-loaded per transizioni
        document.body.classList.add('page-loaded');

        // Setup listeners per transizioni pagina
        document.querySelectorAll<HTMLAnchorElement>('a[href^="/"]').forEach((link: HTMLAnchorElement) => {
            link.addEventListener('click', (e: Event) => {
                const anchor = e.currentTarget as HTMLAnchorElement | null;
                if (anchor && anchor.hostname === window.location.hostname) {
                    e.preventDefault();
                    document.body.classList.add('page-transitioning');
                    setTimeout(() => {
                        window.location.href = anchor.href;
                    }, 300);
                }
            });
        });

        // console.log('Padmin Main: 🚀 ORCHESTRATED INITIALIZATION SEQUENCE COMPLETE - FlorenceEGI Ready! 🚀');

    } catch (error) {
        console.error('Padmin Main: 💥 CRITICAL ORCHESTRATED INITIALIZATION ERROR:', error);

        // Feedback utente con fallback
        const errorTitle = 'Application Error';
        const errorText = 'A critical error occurred while starting the application. Please try refreshing the page.';

        if (window.Swal) {
            window.Swal.fire({
                icon: 'error',
                title: errorTitle,
                text: errorText,
                confirmButtonColor: '#ef4444'
            });
        } else {
            alert(`${errorTitle}\n${errorText}`);
        }

        // Log per debugging
        if (UEM && typeof UEM.handleClientError === 'function') {
            UEM.handleClientError('CLIENT_INIT_CRITICAL_FAILURE', {
                originalError: error instanceof Error ? error.message : String(error),
                stack: error instanceof Error ? error.stack : undefined
            });
        }
    }
}

// 📋 ELEMENTI COINVOLTI NEI PERMESSI (dall'analisi di domElements.ts + nav-links.blade.php):
//
// @can('create_EGI') controlla:
// - collectionListDropdownButtonEl (#collection-list-dropdown-button) - DESKTOP
// - collectionListDropdownMenuEl (#collection-list-dropdown-menu) - DESKTOP
// - mobileCollectionListDropdownButtonEl (#mobile-collection-list-dropdown-button) - MOBILE
// - mobileCollectionListDropdownMenuEl (#mobile-collection-list-dropdown-menu) - MOBILE
// - createEgiContextualButtonEl (.js-create-egi-contextual-button) - ENTRAMBI
// - walletDropdownButtonEl (#wallet-dropdown-button) - WALLET UI
// - walletCopyAddressButtonEl (#wallet-copy-address) - WALLET UI
// - walletDisconnectButtonEl (#wallet-disconnect) - WALLET UI
// - mobileCopyAddressButtonEl (#mobile-copy-address) - MOBILE WALLET
// - mobileDisconnectButtonEl (#mobile-disconnect) - MOBILE WALLET
//
// @can('create_collection') controlla:
// - Elementi con data-action="open-create-collection-modal" (gestiti da createCollectionGuestButtonsEl)

// 🔄 MODIFICA LA FUNZIONE setupEventListeners ESISTENTE
// Sostituisci la sezione problematica con questa versione safe:

/**
 * 📜 Oracode Function: setupEventListeners (VERSIONE SAFE per PERMESSI)
 * 🎯 Associa tutti gli event listener agli elementi DOM interattivi.
 * 🛡️ Security: Safe binding per elementi che possono non esistere per mancanza permessi
 */
function setupEventListeners(): void {
    // console.log('Padmin Main: Attempting to setup FEGI event listeners with safe binding...');

    // --- MODALE CONNESSIONE FEGI WALLET (sempre presenti) ---
    safeAddEventListener(
        DOMElements.connectWalletButtonStdEl,
        'click',
        () => openSecureWalletModal(mainAppConfig, DOMElements, null),
        'Connect Wallet Button Desktop'
    );

    safeAddEventListener(
        DOMElements.connectWalletButtonMobileEl,
        'click',
        () => openSecureWalletModal(mainAppConfig, DOMElements, null),
        'Connect Wallet Button Mobile'
    );

    safeAddEventListener(
        DOMElements.closeConnectWalletButtonEl,
        'click',
        () => closeSecureWalletModal(DOMElements),
        'Close Connect Wallet Button'
    );

    safeAddEventListener(
        DOMElements.connectWalletModalEl,
        'click',
        (e: Event) => {
            if (e.target === DOMElements.connectWalletModalEl) closeSecureWalletModal(DOMElements);
        },
        'Connect Wallet Modal Background'
    );

    // --- AZIONI CREATE EGI/COLLECTION (sempre presenti ma logica condizionale) ---
    DOMElements.createEgiGuestButtonsEl?.forEach((btn, index) => {
        safeAddEventListener(
            btn,
            'click',
            () => {
                const authStatus = getAuthStatus(mainAppConfig);
                if (authStatus === 'logged-in' || authStatus === 'connected') {
                    // Pre-check flow for selecting collection and setting current_collection_id
                    import('./features/collections/createEgiFlow').then(m => m.handleCreateEgiFlow(mainAppConfig, DOMElements));
                } else {
                    openSecureWalletModal(mainAppConfig, DOMElements, 'create-egi');
                }
            },
            `Create EGI Guest Button ${index}`
        );
    });

    // --- PULSANTE CONTESTUALE CREATE EGI (protetto da @can('create_EGI')) ---
    safeAddEventListener(
        DOMElements.createEgiContextualButtonEl,
        'click',
        (event) => {
            const authStatus = getAuthStatus(mainAppConfig);
            if (authStatus === 'logged-in' || authStatus === 'connected') {
                import('./features/collections/createEgiFlow').then(m => m.handleCreateEgiFlow(mainAppConfig, DOMElements));
            } else {
                openSecureWalletModal(mainAppConfig, DOMElements, 'create-egi');
            }
        },
        'Create EGI Contextual Button (permission-protected)'
    );

    // --- LISTENER ALTERNATIVO per tutti i pulsanti con la classe (protetti da @can('create_EGI')) ---
    const allEgiButtons = document.querySelectorAll('.js-create-egi-contextual-button');
    allEgiButtons.forEach((button, index) => {
        safeAddEventListener(
            button,
            'click',
            (event) => {
                const authStatus = getAuthStatus(mainAppConfig);
                if (authStatus === 'logged-in' || authStatus === 'connected') {
                    import('./features/collections/createEgiFlow').then(m => m.handleCreateEgiFlow(mainAppConfig, DOMElements));
                } else {
                    openSecureWalletModal(mainAppConfig, DOMElements, 'create-egi');
                }
            },
            `EGI Contextual Button ${index} (permission-protected)`
        );
    });

    // --- CREATE COLLECTION (protetto da @can('create_collection')) ---
    DOMElements.createCollectionGuestButtonsEl?.forEach((btn, index) => {
        safeAddEventListener(
            btn,
            'click',
            () => {
                const authStatus = getAuthStatus(mainAppConfig);
                if (authStatus === 'logged-in') {
                    window.location.href = mainAppConfig.routes.collectionsCreate;
                } else if (authStatus === 'connected') {
                    // Mostra messaggio per registrazione completa
                    if (window.Swal) {
                        window.Swal.fire({
                            icon: 'info',
                            title: appTranslate('registrationRequiredTitle', mainAppConfig.translations),
                            text: appTranslate('registrationRequiredTextCollections', mainAppConfig.translations),
                            confirmButtonText: appTranslate('registerNowButton', mainAppConfig.translations),
                            showCancelButton: true,
                            cancelButtonText: appTranslate('laterButton', mainAppConfig.translations),
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#aaa'
                        }).then((result: { isConfirmed: boolean }) => {
                            if (result.isConfirmed) {
                                window.location.href = mainAppConfig.routes.register;
                            }
                        });
                    } else {
                        alert(appTranslate('registrationRequiredTextCollections', mainAppConfig.translations));
                        window.location.href = mainAppConfig.routes.register;
                    }
                } else {
                    openSecureWalletModal(mainAppConfig, DOMElements, 'create-collection');
                }
            },
            `Create Collection Guest Button ${index} (permission-protected)`
        );
    });

    // --- DROPDOWN WALLET DESKTOP (protetto da @can('create_EGI')) ---
    safeAddEventListener(
        DOMElements.walletDropdownButtonEl,
        'click',
        () => toggleWalletDropdownMenu(mainAppConfig, DOMElements, UEM),
        'Wallet Dropdown Button (permission-protected)'
    );

    safeAddEventListener(
        DOMElements.walletCopyAddressButtonEl,
        'click',
        () => copyWalletAddress(mainAppConfig, DOMElements, UEM),
        'Wallet Copy Address Button (permission-protected)'
    );

    safeAddEventListener(
        DOMElements.walletDisconnectButtonEl,
        'click',
        () => {
            handleDisconnect(mainAppConfig, DOMElements, UEM, () => {
                updateNavbarUI(mainAppConfig, DOMElements, UEM);
                if (reservationFeature && typeof reservationFeature.updateReservationButtonStates === 'function') {
                    reservationFeature.updateReservationButtonStates();
                }
            });
        },
        'Wallet Disconnect Button (permission-protected)'
    );

    // --- MOBILE WALLET BUTTONS (protetti da @can('create_EGI')) - PROBLEMA PRINCIPALE RISOLTO ---
    safeAddEventListener(
        DOMElements.mobileCopyAddressButtonEl,
        'click',
        () => copyWalletAddress(mainAppConfig, DOMElements, UEM),
        'Mobile Copy Address Button (permission-protected)'
    );

    safeAddEventListener(
        DOMElements.mobileDisconnectButtonEl,
        'click',
        () => {
            handleDisconnect(mainAppConfig, DOMElements, UEM, () => {
                updateNavbarUI(mainAppConfig, DOMElements, UEM);
                if (reservationFeature && typeof reservationFeature.updateReservationButtonStates === 'function') {
                    reservationFeature.updateReservationButtonStates();
                }
            });
        },
        'Mobile Disconnect Button (permission-protected)'
    );

    // --- DROPDOWN COLLECTION LIST DESKTOP (protetto da @can('create_EGI')) ---
    safeAddEventListener(
        DOMElements.collectionListDropdownButtonEl,
        'click',
        () => toggleCollectionListDropdown(mainAppConfig, DOMElements, UEM),
        'Collection List Dropdown Button (permission-protected)'
    );

    // --- MOBILE DROPDOWN COLLECTION LIST (protetto da @can('create_EGI')) ---
    safeAddEventListener(
        DOMElements.mobileCollectionListDropdownButtonEl,
        'click',
        () => toggleMobileCollectionListDropdown(mainAppConfig, DOMElements, UEM),
        'Mobile Collection List Dropdown Button (permission-protected)'
    );

    // --- MENU MOBILE (sempre presente) ---
    safeAddEventListener(
        DOMElements.mobileMenuButtonEl,
        'click',
        () => {
            // console.log('Padmin Main: Mobile menu button clicked.');
            toggleMobileMenu(DOMElements, mainAppConfig);
        },
        'Mobile Menu Button'
    );

    // console.log('Padmin Main: FEGI Event listeners setup with safe binding complete.');
}

/**
 * 📜 Oracode Function: setupFegiCustomEvents
 * 🎯 Setup custom events specifici per il sistema FEGI
 * 🔧 TypeScript: Now properly typed with extended DocumentEventMap
 */
function setupFegiCustomEvents(): void {
    // Event listener per apertura upload modal da walletConnect.ts
    document.addEventListener('openUploadModal', (event) => {
        const customEvent = event as CustomEvent;
        const { type } = customEvent.detail;
        if (mainUploadModalManager && type) {
            mainUploadModalManager.openModal(type);
            // // console.log(`Padmin Main: Upload modal opened via custom event for type: ${type}`);
        }
    });

    // Event listener per aggiornamenti UI dopo connessione FEGI
    document.addEventListener('fegiConnectionComplete', (event) => {
        const customEvent = event as CustomEvent;
        updateNavbarUI(mainAppConfig, DOMElements, UEM);
        if (reservationFeature && typeof reservationFeature.updateReservationButtonStates === 'function') {
            reservationFeature.updateReservationButtonStates();
        }
        // console.log('Padmin Main: UI updated after FEGI connection');

        // Opzionalmente usa i dati dell'evento
        if (customEvent.detail?.walletAddress) {
            // // console.log(`Padmin Main: Connected wallet: ${customEvent.detail.walletAddress}`);
        }
    });

    // console.log('Padmin Main: FEGI custom events setup complete.');
}

// --- 🌍 FUNZIONI GLOBALI PER COMPATIBILITÀ JAVASCRIPT ---
// Queste funzioni vengono chiamate dal menu mobile e altri script vanilla JS

/**
 * 📜 Oracode Global Function: openCreateCollectionModal
 * 🎯 Funzione globale per aprire la creazione collezione da JavaScript vanilla
 * 💡 Usa la stessa logica dei bottoni con data-action="open-create-collection-modal"
 */
(window as any).openCreateCollectionModal = () => {
    console.log('Global openCreateCollectionModal called');

    if (!mainAppConfig) {
        console.error('Main app config not initialized');
        return;
    }

    const authStatus = getAuthStatus(mainAppConfig);

    if (authStatus === 'logged-in') {
        // Utente loggato: apri il modal invece di navigare
        const modal = document.getElementById('create-collection-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');

            // Anima l'apertura del modal
            const container = document.getElementById('create-collection-modal-container');
            if (container) {
                setTimeout(() => {
                    container.classList.remove('scale-95', 'opacity-0');
                    container.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            // Focus sul primo input
            const firstInput = modal.querySelector('input') as HTMLInputElement;
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 100);
            }
        } else {
            console.error('create-collection-modal element not found');
            // Fallback: prova a trovare un bottone con data-action e triggerarlo
            const triggerButton = document.querySelector('[data-action="open-create-collection-modal"]') as HTMLElement;
            if (triggerButton) {
                triggerButton.click();
            }
        }
    } else if (authStatus === 'connected') {
        // Wallet connesso ma non registrato: chiedi registrazione
        if (window.Swal) {
            window.Swal.fire({
                icon: 'info',
                title: appTranslate('registrationRequiredTitle', mainAppConfig.translations),
                text: appTranslate('registrationRequiredTextCollections', mainAppConfig.translations),
                confirmButtonText: appTranslate('registerNowButton', mainAppConfig.translations),
                showCancelButton: true,
                cancelButtonText: appTranslate('laterButton', mainAppConfig.translations),
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#aaa'
            }).then((result: { isConfirmed: boolean }) => {
                if (result.isConfirmed) {
                    window.location.href = mainAppConfig.routes.register;
                }
            });
        } else {
            alert(appTranslate('registrationRequiredTextCollections', mainAppConfig.translations));
            window.location.href = mainAppConfig.routes.register;
        }
    } else {
        // Nessuna connessione: apri modal wallet
        openSecureWalletModal(mainAppConfig, DOMElements, 'create-collection');
    }
};

/**
 * 📜 Oracode Global Function: createCollectionFlow
 * 🎯 Alias per openCreateCollectionModal (compatibilità multipla)
 */
(window as any).createCollectionFlow = (window as any).openCreateCollectionModal;

// console.log('Padmin Main: Global functions for collection creation setup complete.');

// --- 🚀 PUNTO DI INGRESSO ORCHESTRATO DELL'APPLICAZIONE ---
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApplicationOrchestrated);
} else {
    initializeApplicationOrchestrated();
}
