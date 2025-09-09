// resources/js/app.js

console.log('Inizializzazione di app.js (inizio)'); // Debugging

import './bootstrap';
console.log('bootstrap importato.'); // Debugging

// Importa il polyfill whatwg-fetch
import 'whatwg-fetch';
console.log('Polyfill whatwg-fetch importato.'); // Debugging

// Importa SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal; // Sintassi JS standard
console.log('SweetAlert2 importato e globale.'); // Debugging

// Importa utils (translations, enums)
import { fetchTranslations, ensureTranslationsLoaded } from './utils/translations';
import { loadEnums, getEnum, isPendingStatus } from './utils/enums';
console.log('Utils per translations e enums importati.'); // Debugging

import { appTranslate, initializeAppConfig } from '../ts/config/appConfig'

// Importa la gestione del modale
// import { initializeModal } from '../ts/open-close-modal';


// Importa jQuery
import $ from 'jquery';
window.$ = window.jQuery = $; // Sintassi JS standard
console.log('jQuery importato e globale.'); // Debugging

// Listener per i moduli Wallet (dal tuo codice originale)
let walletCreateInstance = null;
let walletUpdateInstance = null;
let walletDonationInstance = null;
import {
    RequestCreateNotificationWallet,
    RequestUpdateNotificationWallet,
    RequestWalletDonation,
} from './modules/notifications/init/request-notification-wallet-init';
document.addEventListener('DOMContentLoaded', async () => {
    // Assicura che AppConfig (e quindi le traduzioni per appTranslate) sia pronto
    if (typeof window.appConfigInitPromise === 'function') {
        // nothing
    }
    if (window.__appConfigInitPromise) {
        try { await window.__appConfigInitPromise; } catch {}
    }

    // Inizializza il modale
    // initializeModal();

    if (!walletCreateInstance) {
        walletCreateInstance = new RequestCreateNotificationWallet({ apiBaseUrl: '/notifications' });
        console.log(`🔍 Inizializzazione unica RequestCreateNotificationWallet (DOMContentLoaded)`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di RequestCreateNotificationWallet ignorato`);
    }

    if (!walletUpdateInstance) {
        walletUpdateInstance = new RequestUpdateNotificationWallet({ apiBaseUrl: '/notifications' });
        console.log(`🔍 Inizializzazione unica RequestUpdateNotificationWallet (DOMContentLoaded)`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di RequestUpdateNotificationWallet ignorato`);
    }

    if (!walletDonationInstance) {
        walletDonationInstance = new RequestWalletDonation({ apiBaseUrl: '/notifications' }); // Corretto nome classe? Era RequestWalletDonation
        console.log(`🔍 Inizializzazione unica RequestWalletDonation (DOMContentLoaded)`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di RequestWalletDonation ignorato`);
    }
});

// Listener per DeleteProposalInvitation (dal tuo codice originale)
let deleteProposalInvitationInstance = null;
import { DeleteProposalInvitation } from './modules/notifications/delete-proposal-invitation';
document.addEventListener('DOMContentLoaded', async () => {
    if (window.__appConfigInitPromise) {
        try { await window.__appConfigInitPromise; } catch {}
    }
    if (!deleteProposalInvitationInstance) {
        deleteProposalInvitationInstance = new DeleteProposalInvitation({ apiBaseUrl: '/notifications' });
        console.log(`🔍 Inizializzazione unica DeleteProposalInvitation (DOMContentLoaded)`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di DeleteProposalInvitation ignorato`);
    }
});

// Listener per DeleteProposalWallet (dal tuo codice originale)
let deleteProposalWalletInstance = null;
import { DeleteProposalWallet } from './modules/notifications/delete-proposal-wallet';
document.addEventListener('DOMContentLoaded', async () => {
    if (window.__appConfigInitPromise) {
        try { await window.__appConfigInitPromise; } catch {}
    }
    if (!deleteProposalWalletInstance) {
        deleteProposalWalletInstance = new DeleteProposalWallet({ apiBaseUrl: '/notifications' });
        console.log(`🔍 Inizializzazione unica DeleteProposalWallet (DOMContentLoaded)`);
    } else {
        console.warn(`⛔ Tentativo di inizializzazione multipla di DeleteProposalWallet ignorato`);
    }
});



// Inizializza AppConfig (che contiene anche le translations usate da appTranslate)
// E rende disponibile appTranslate globalmente solo dopo l'inizializzazione
// Pre-inizializza AppConfig il prima possibile e rendi disponibile appTranslate quando pronto
window.__appConfigInitPromise = (async () => {
    try {
        await initializeAppConfig();
        window.appTranslate = appTranslate; // Sistema moderno basato su AppConfig
        console.log('✅ AppConfig inizializzato: appTranslate pronto.');
    } catch (e) {
        console.error('❌ Impossibile inizializzare AppConfig per le traduzioni:', e);
        // Espone comunque la funzione per evitare crash, ma senza config tradurrà a fallback
        window.appTranslate = appTranslate;
    }
})();

// Carica le traduzioni legacy in window.translations (compatibilità, non usato da appTranslate)
fetchTranslations();
window.ensureTranslationsLoaded = ensureTranslationsLoaded;
console.log('Traduzioni legacy avviate (compatibilità).');


// Import dei moduli notifiche (senza inizializzazioni dirette mostrate nel listener DOMContentLoaded fornito)
import './notification'; // Questo modulo esegue codice subito?
import './modules/notifications/init/notification-response-init'; // Questo modulo esegue codice subito?
// Se questi moduli devono essere inizializzati DOPO DOMContentLoaded, dovrai spostare le loro importazioni
// o chiamare le loro funzioni di inizializzazione dentro un listener DOMContentLoaded come gli altri.


console.log('app.js execution finished (initial phase - after imports).'); // Debugging
// Removed invalid type import in JS: AppConfig is a TS type, not a runtime export


// // Documentazione: di window.fetch polyfill
// Documentazione: https://github.com/github/fetch;


