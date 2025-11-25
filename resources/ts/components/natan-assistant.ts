// resources/ts/components/natan-assistant.ts

/**
 * Natan Butler Assistant UI Component
 * @description Maggiordomo digitale che accoglie gli utenti e fornisce assistenza personalizzata
 * @version 3.0.0 - Butler Mode
 *
 * NUOVE FUNZIONALITÀ MAGGIORDOMO:
 * - Modal di benvenuto prominente all'arrivo sulla pagina
 * - Interfaccia conversazionale "Cosa posso fare per te?"
 * - Opzioni di azione dirette (Esplora, Impara, Inizia, Business)
 * - Pulsante di chiusura per utenti esperti
 * - Dismissal permanente con localStorage
 * - Auto-dismiss dopo 30 secondi di inattività
 * - Animazioni fluide e design elegante
 * - Completamente responsivo
 *
 * COME USARE:
 * - Il maggiordomo appare automaticamente per nuovi utenti
 * - Può essere chiuso temporaneamente o permanentemente
 * - Per testing: natan.resetButler() o natan.showButlerManually()
 *
 * COMPATIBILITÀ:
 * - Mantiene tutte le funzionalità precedenti
 * - Fallback elegante se elementi DOM non esistono
 * - Supporta sia il nuovo che il vecchio comportamento
 */

/**
 * @Oracode Import delle categorie accordion
 * 🎯 Purpose: Assicura che butlerCategories sia disponibile
 * 📥 Input: None
 * 📤 Output: butlerCategories imported and available
 *
 * Nota: Questo deve essere aggiunto all'inizio del file natan-assistant.ts
 */

import { butlerCategories, type ButlerCategory, type ButlerSubOption } from './butler-options';
import { assistantOptions } from './assistant-options';
import { appTranslate, getAppConfig } from '../config/appConfig';

export class NatanAssistant {
    private sections: { id: string, element: HTMLElement, suggestion: string }[] = [];
    private currentSection: string | null = null;
    private suggestionTimeout: number | null = null;
    private isThinking: boolean = false;
    private isOpen: boolean = false;
    private currentOpenContentId: string | null = null;
    private toggleButton: HTMLElement | null = null;
    private menuElement: HTMLElement | null = null;
    private debugMode: boolean = true;
    private isProcessingToggle: boolean = false;

    // Nuove proprietà per il comportamento da maggiordomo
    private butlerModal: HTMLElement | null = null;
    private hasGreeted: boolean = false;
    private dismissTimeout: number | null = null;

    constructor() {
        //// console.log('🎩 [NATAN BUTLER] Constructor called - versione 3.0.0');
        this.debug('NatanAssistant Butler constructor called');

        // hasGreeted serve solo per tracking, NON blocca la visualizzazione
        this.hasGreeted = localStorage.getItem('natan_has_greeted') === 'true';

        //// console.log('🎩 [NATAN BUTLER] State check:', {
        //     hasGreeted: this.hasGreeted,
        //     willShowModal: !this.hasGreeted, // SOLO hasGreeted blocca la visualizzazione
        //     localStorage_greeted: localStorage.getItem('natan_has_greeted')
        // });

        // Ottieni riferimenti DOM principali - cerca prima i nuovi ID con suffisso, poi fallback ai vecchi
        this.toggleButton = document.getElementById('natan-assistant-toggle-desktop') ||
            document.getElementById('natan-assistant-toggle-mobile') ||
            document.getElementById('natan-assistant-toggle-global') || // per layout globali
            document.getElementById('natan-assistant-toggle'); // fallback per compatibilità

        this.menuElement = document.getElementById('natan-assistant-menu-desktop') ||
            document.getElementById('natan-assistant-menu-mobile') ||
            document.getElementById('natan-assistant-menu-global') || // per layout globali
            document.getElementById('natan-assistant-menu'); // fallback per compatibilità

        //// console.log('🎯 [INIT] Elements search results:', {
        //     desktopToggle: !!document.getElementById('natan-assistant-toggle-desktop'),
        //     mobileToggle: !!document.getElementById('natan-assistant-toggle-mobile'),
        //     oldToggle: !!document.getElementById('natan-assistant-toggle'),
        //     desktopMenu: !!document.getElementById('natan-assistant-menu-desktop'),
        //     mobileMenu: !!document.getElementById('natan-assistant-menu-mobile'),
        //     oldMenu: !!document.getElementById('natan-assistant-menu'),
        //     finalToggle: !!this.toggleButton,
        //     finalMenu: !!this.menuElement,
        //     toggleButtonId: this.toggleButton?.id,
        //     menuElementId: this.menuElement?.id,
        //     screenWidth: window.innerWidth
        // });

        //// console.log('🎩 [NATAN BUTLER] DOM elements:', {
        //     toggleButton: !!this.toggleButton,
        //     menuElement: !!this.menuElement
        // });

        if (!this.toggleButton || !this.menuElement) {
            this.debug('ERROR: Critical DOM elements not found', {
                toggleButton: !!this.toggleButton,
                menuElement: !!this.menuElement
            });
            if (!this.hasGreeted) {
                this.createButlerModal();
            }
            return;
        }

        // Inizializza struttura e funzionalità
        this.setupToggle();
        this.addStyles();
        this.initLearnMoreButtons();
        if (!this.hasGreeted) {
            this.createButlerModal();
        }

        //// console.log('🎩 [NATAN BUTLER] All systems initialized!');

        // Protezione aggiuntiva per eventi esterni
        document.addEventListener('click', (e) => {
            // Ignora click su elementi natan
            if (e.target instanceof Element &&
                (e.target.closest('#natan-assistant-container') ||
                    e.target.closest('#natan-suggestion') ||
                    e.target.closest('#natan-butler-modal') ||
                    e.target.id === 'natan-assistant-toggle' ||
                    e.target.closest('#natan-assistant-toggle'))) {
                return;
            }

            // Se il menu è aperto (visibile) ma isOpen è false (stato incoerente)
            // o viceversa, ripristina lo stato corretto
            if (this.menuElement) {
                const menuVisible = !this.menuElement.classList.contains('hidden');
                if (menuVisible !== this.isOpen) {
                    this.debug('Detected state inconsistency, fixing isOpen state');
                    this.isOpen = menuVisible;
                }
            }
        }, true); // Fase di capturing per catturare prima di altri handler

        // Aggiungi reset automatico periodico dello stato
        setInterval(() => {
            this.resetStateIfNeeded();
        }, 2000);

        // Inizializza funzionalità di assistenza contestuale in differita
        window.addEventListener('load', () => {
            setTimeout(() => {
                this.initSections();
                this.initScrollObserver();
                this.initUserActionListeners();
                this.initHoverSuggestions();
                this.checkUserHistory();

                // Mostra il maggiordomo di benvenuto (sempre, tranne se dismesso)
                //// console.log('🎩 [NATAN BUTLER] Checking conditions for auto-show:', {
                //     hasGreeted: this.hasGreeted,
                //     shouldShow: !this.hasGreeted // Solo il hasGreeted blocca la visualizzazione
                // });

                if (!this.hasGreeted) {
                    //// console.log('🎩 [NATAN BUTLER] ✅ Conditions met - Will show butler welcome in 2 seconds');
                    setTimeout(() => {
                        //console.log('🎩 [NATAN BUTLER] 🚀 Executing auto-show now!');
                        this.showButlerWelcome();
                    }, 2000);
                } else {
                    //// console.log('🎩 [NATAN BUTLER] ❌ Conditions NOT met for auto-show');
                    //// console.log('🎩 [NATAN BUTLER] - Reason: Butler was dismissed permanently');
                }
            }, 1000);
        });

        // BACKUP: Se window.load non funziona, prova dopo DOMContentLoaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    if (!this.hasGreeted) {
                        //// console.log('🎩 [NATAN BUTLER] DOMContentLoaded - Showing butler');
                        this.showButlerWelcome();
                    }
                }, 1000);
            });
        } else if (!this.hasGreeted) {
            // Documento già carico, mostra subito se non dismesso
            setTimeout(() => {
                //// console.log('🎩 [NATAN BUTLER] Document already loaded - Showing butler');
                this.showButlerWelcome();
            }, 2000);
        }

        this.debug('NatanAssistant Butler initialization complete');
    }

    /**
     * Resetta lo stato se necessario per riparare incoerenze
     */
    private resetStateIfNeeded(): void {
        if (!this.menuElement) return;

        // Controlla se lo stato visibile del menu corrisponde alla proprietà isOpen
        const menuVisible = !this.menuElement.classList.contains('hidden');

        if (menuVisible !== this.isOpen) {
            this.debug('Fixing state inconsistency in reset');
            this.isOpen = menuVisible;
        }

        // Se il menu è visivamente chiuso ma state dice aperto, reset
        if (!menuVisible && this.isOpen) {
            this.debug('Menu visually closed but state is open - resetting');
            this.isOpen = false;
        }

        // Assicurati che il menu abbia lo stile display corretto
        if (menuVisible && this.menuElement.style.display !== 'flex') {
            this.menuElement.style.display = 'flex';
        } else if (!menuVisible && this.menuElement.style.display !== 'none') {
            this.menuElement.style.display = 'none';
        }

        // Reset isProcessingToggle se fermo da troppo tempo
        if (this.isProcessingToggle) {
            this.debug('Resetting stuck isProcessingToggle flag');
            this.isProcessingToggle = false;
        }
    }

    /**
     * Utility di debug
     */
    private debug(...args: any[]): void {
        if (this.debugMode) {
            //console.log('[🎩 NatanButler]', ...args);
        }
    }

    /**
     * Configura l'interazione toggle principale
     */
    private setupToggle(): void {
        if (!this.toggleButton || !this.menuElement) {
            this.debug('ERROR: setupToggle - Missing critical elements');
            return;
        }

        this.debug('Setting up toggle button and menu');

        // Clona il toggle per rimuovere eventuali listener precedenti
        const newToggleButton = this.toggleButton.cloneNode(true) as HTMLElement;
        if (this.toggleButton.parentNode) {
            this.toggleButton.parentNode.replaceChild(newToggleButton, this.toggleButton);
            this.toggleButton = newToggleButton;
            this.debug('Cloned toggle button to remove existing listeners');
        }

        // Assicurati che il menu sia nascosto inizialmente
        if (!this.menuElement.classList.contains('hidden')) {
            this.menuElement.classList.add('hidden');
            this.debug('Added missing hidden class to menu');
        }

        // Imposta anche display: none per sicurezza
        this.menuElement.style.display = 'none';
        this.isOpen = false; // Assicurati che isOpen sia inizialmente false

        // CRUCIALE: Aggiungi il listener in capturing phase e con stopImmediatePropagation
        this.toggleButton.addEventListener('click', (e: Event) => {
            // Cast dell'evento generico a MouseEvent
            const mouseEvent = e as MouseEvent;

            this.debug('Toggle button clicked');

            // Previeni doppi click o race conditions
            if (this.isProcessingToggle) {
                this.debug('Ignoring click - already processing toggle');
                mouseEvent.stopImmediatePropagation();
                mouseEvent.stopPropagation();
                mouseEvent.preventDefault();
                return;
            }

            this.isProcessingToggle = true;

            // FONDAMENTALE: Ferma la propagazione prima di tutto
            mouseEvent.stopImmediatePropagation();
            mouseEvent.stopPropagation();
            mouseEvent.preventDefault();

            // Gestisci il toggle
            this.handleToggleClick(mouseEvent);
        }, true); // true = capturing phase

        // NUOVO: Aggiungi listener anche agli altri pulsanti se esistono
        const allButtons = [
            document.getElementById('natan-assistant-toggle-desktop'),
            document.getElementById('natan-assistant-toggle-mobile'),
            document.getElementById('natan-assistant-toggle-global')
        ].filter(btn => btn && btn !== this.toggleButton);

        allButtons.forEach(button => {
            if (!button) return;

            console.log('🎯 [SETUP] Adding listener to additional button:', button.id);

            button.addEventListener('click', (e: Event) => {
                const mouseEvent = e as MouseEvent;

                // Determina quale menu aprire in base al pulsante
                let targetMenuId = '';
                if (button.id.includes('desktop')) {
                    targetMenuId = 'natan-assistant-menu-desktop';
                } else if (button.id.includes('mobile')) {
                    targetMenuId = 'natan-assistant-menu-mobile';
                } else if (button.id.includes('global')) {
                    targetMenuId = 'natan-assistant-menu-global';
                }

                const targetMenu = document.getElementById(targetMenuId);
                //console.log('🎯 [CLICK] Additional button clicked:', button.id, 'Target menu:', targetMenuId, 'Found:', !!targetMenu);

                if (!targetMenu) {
                    //console.log('🎯 [CLICK] Target menu not found for button:', button.id);
                    return;
                }

                // Previeni doppi click
                if (this.isProcessingToggle) {
                    //console.log('🎯 [CLICK] Ignoring click - already processing toggle');
                    mouseEvent.stopImmediatePropagation();
                    mouseEvent.stopPropagation();
                    mouseEvent.preventDefault();
                    return;
                }

                this.isProcessingToggle = true;

                // Ferma propagazione
                mouseEvent.stopImmediatePropagation();
                mouseEvent.stopPropagation();
                mouseEvent.preventDefault();

                // Gestisci il toggle con il menu specifico
                this.handleToggleClick(mouseEvent, targetMenu);
            }, true);
        });

        // Aggiungi gestori click per i menu item
        document.querySelectorAll('.natan-item').forEach(item => {
            item.addEventListener('click', (e: Event) => {
                const mouseEvent = e as MouseEvent;
                mouseEvent.stopImmediatePropagation();
                mouseEvent.stopPropagation();
                mouseEvent.preventDefault();
                this.handleMenuItemClick(mouseEvent);
            }, true); // Fase di capturing
        });

        // Handler per click all'esterno - RICONFIGURATO
        document.addEventListener('click', (e: Event) => {
            const mouseEvent = e as MouseEvent;

            // CRUCIALE: Non processare se stiamo già elaborando un toggle
            if (this.isProcessingToggle) {
                this.debug('Ignoring outside click - toggle processing in progress');
                return;
            }

            // CRUCIALE: Ignora se il click è sul toggle o un suo discendente
            if (mouseEvent.target instanceof Node &&
                (this.toggleButton?.contains(mouseEvent.target) || mouseEvent.target === this.toggleButton)) {
                this.debug('Outside click was on toggle button - IGNORING');
                return;
            }

            this.handleOutsideClick(mouseEvent);
        });
    }

    /**
     * Gestisce il click sul pulsante toggle principale
     */
    private handleToggleClick(e: MouseEvent, targetMenu?: HTMLElement): void {
        this.debug('handleToggleClick executing');

        // Usa il menu passato come parametro, altrimenti usa quello di default
        const menuToUse = targetMenu || this.menuElement;

        if (!menuToUse) {
            this.debug('ERROR: Menu element not found');
            return;
        }

        //console.log('🎯 [TOGGLE] Using menu:', menuToUse.id, 'Screen width:', window.innerWidth);

        try {
            // Assicurati che non siamo in uno stato incoerente prima di procedere
            this.resetStateIfNeeded();

            const isHidden = menuToUse.classList.contains('hidden');
            this.debug('Current menu state - hidden:', isHidden, 'display:', menuToUse.style.display);

            if (isHidden) {
                // APERTURA MENU
                this.debug('OPENING MENU:', menuToUse.id);
                menuToUse.classList.remove('hidden');
                menuToUse.style.display = 'flex';
                this.isOpen = true; // Imposta isOpen a true quando si apre

                // GENERA I BOTTONI QUANDO IL MENU VIENE APERTO
                renderAssistantOptions();

                // Force reflow per essere sicuri che le modifiche siano applicate
                void menuToUse.offsetHeight;

                // Log immediato DOM dopo apertura
                this.debug('Menu after opening - hidden class:',
                    menuToUse.classList.contains('hidden'),
                    'display:', menuToUse.style.display,
                    'computed display:', window.getComputedStyle(menuToUse).display
                );

                // Anima entrata elementi - solo nel menu specifico
                setTimeout(() => {
                    this.debug('Animating menu items in:', menuToUse.id);
                    menuToUse.querySelectorAll('.natan-item').forEach((item, index) => {
                        setTimeout(() => {
                            (item as HTMLElement).classList.remove('translate-x-20', 'opacity-0');
                        }, index * 50);
                    });
                }, 100);
            } else {
                // CHIUSURA MENU
                this.debug('CLOSING MENU:', menuToUse.id);

                // Reset menu items - solo nel menu specifico
                menuToUse.querySelectorAll('.natan-item').forEach(item => {
                    (item as HTMLElement).classList.add('translate-x-20', 'opacity-0');
                });

                // Chiudi menu con leggero ritardo per animazione
                setTimeout(() => {
                    if (menuToUse) {
                        this.debug('Actually hiding menu after animation:', menuToUse.id);
                        menuToUse.classList.add('hidden');
                        menuToUse.style.display = 'none';
                        this.isOpen = false; // FONDAMENTALE: Imposta isOpen a false quando si chiude

                        // Chiudi anche eventuali tooltip aperti
                        this.closeAllContent();
                    }
                }, 300);
            }
        } catch (error) {
            this.debug('ERROR in handleToggleClick:', error);
            // In caso di errore, reset allo stato chiuso
            if (menuToUse) {
                menuToUse.classList.add('hidden');
                menuToUse.style.display = 'none';
            }
            this.isOpen = false;
        } finally {
            // Reset sempre il flag di processing quando terminato
            setTimeout(() => {
                this.isProcessingToggle = false;
            }, 500);
        }
    }

    /**
     * Gestisce il click su un elemento del menu
     */
    private handleMenuItemClick(e: MouseEvent): void {
        this.debug('Menu item clicked');

        const item = e.currentTarget as HTMLElement;
        const id = item.id.replace('natan-item-', '');
        const contentBox = document.getElementById(`natan-content-${id}`);

        if (!contentBox) {
            this.debug('Content box not found for id:', id);
            return;
        }

        const isExpanded = item.getAttribute('aria-expanded') === 'true';
        this.debug('Item expanded state:', isExpanded);

        // Chiudi altri content box aperti
        document.querySelectorAll('[id^="natan-content-"]').forEach(box => {
            if (box !== contentBox) {
                box.classList.add('hidden');
                this.debug('Hiding other content box:', box.id);
            }
        });

        document.querySelectorAll('.natan-item').forEach(menuItem => {
            if (menuItem !== item) {
                menuItem.setAttribute('aria-expanded', 'false');
            }
        });

        // Toggle questo content box
        item.setAttribute('aria-expanded', (!isExpanded).toString());

        if (isExpanded) {
            contentBox.classList.add('hidden');
            this.currentOpenContentId = null;
            this.debug('Hiding content box:', contentBox.id);
        } else {
            contentBox.classList.remove('hidden');
            this.currentOpenContentId = id;
            this.debug('Showing content box:', contentBox.id);

            // Mostra thinking effect quando si apre un content
            this.showThinking(500);

            // Spotlight se appropriato
            setTimeout(() => {
                const spotlightSelector = contentBox.getAttribute('data-spotlight');
                if (spotlightSelector) {
                    this.debug('Content has spotlight selector:', spotlightSelector);
                    this.spotlight(spotlightSelector, 4000);
                }
            }, 700);
        }
    }

    /**
     * Gestisce click esterno per chiudere menu
     */
    private handleOutsideClick(e: MouseEvent): void {
        this.debug('handleOutsideClick processing');

        const container = document.getElementById('natan-assistant-container');
        const suggestionEl = document.getElementById('natan-suggestion');

        // Ignora click su suggerimenti o toggle
        if (e.target instanceof Element &&
            (e.target.closest('#natan-suggestion') ||
                e.target.id === 'natan-assistant-toggle' ||
                e.target.closest('#natan-assistant-toggle'))) {
            this.debug('Outside click on suggestion or toggle - ignoring');
            return;
        }

        // Ignora click strani con target indefinito
        if (!(e.target instanceof Element)) {
            this.debug('Outside click with non-Element target - ignoring');
            return;
        }

        if (!container || !this.menuElement) {
            this.debug('Container or menu element not found');
            return;
        }

        // Verifica se il menu è aperto e il click è fuori dal container
        if (!this.menuElement.classList.contains('hidden') &&
            !container.contains(e.target as Node)) {

            this.debug('Valid outside click detected - closing menu');

            // Reset menu items
            document.querySelectorAll('.natan-item').forEach(item => {
                (item as HTMLElement).classList.add('translate-x-20', 'opacity-0');
            });

            // Chiudi menu
            setTimeout(() => {
                if (this.menuElement) {
                    this.menuElement.classList.add('hidden');
                    this.menuElement.style.display = 'none';
                    this.isOpen = false; // IMPORTANTE: Aggiorna lo stato
                    this.debug('Menu hidden after outside click');
                }
                this.closeAllContent();
            }, 200);
        } else {
            this.debug('Outside click - no action needed');
        }
    }

    /**
     * Chiude tutti i content box
     */
    private closeAllContent(): void {
        this.debug('Closing all content boxes');

        document.querySelectorAll('[id^="natan-content-"]').forEach(box => {
            box.classList.add('hidden');
        });

        document.querySelectorAll('.natan-item').forEach(item => {
            item.setAttribute('aria-expanded', 'false');
        });

        this.currentOpenContentId = null;
    }

    /**
     * Mostra un suggerimento contestuale
     */
    private showSuggestion(text: string, sectionId: string): void {
        this.debug('Showing suggestion:', text, 'for section:', sectionId);

        // Crea o aggiorna un elemento suggerimento
        let suggestionEl = document.getElementById('natan-suggestion');

        if (!suggestionEl) {
            suggestionEl = document.createElement('div');
            suggestionEl.id = 'natan-suggestion';
            document.getElementById('natan-assistant-container')?.appendChild(suggestionEl);
            this.debug('Created new suggestion element');
        }

        // Adatta posizione e dimensioni per mobile
        if (this.isMobile()) {
            suggestionEl.className = 'absolute top-[-50px] right-0 px-3 py-1.5 text-xs font-medium text-emerald-300 bg-gray-900 border border-emerald-600/30 rounded-full transition-all duration-300 transform translate-y-0 opacity-0 max-w-[180px] whitespace-normal';
        } else {
            suggestionEl.className = 'absolute top-0 px-4 py-2 text-sm font-medium transition-all duration-300 transform translate-y-0 bg-gray-900 border rounded-full opacity-0 right-16 text-emerald-300 border-emerald-600/30 whitespace-nowrap';
        }

        // Imposta il testo e mostra il suggerimento
        suggestionEl.textContent = text;
        suggestionEl.setAttribute('data-section', sectionId);

        // Anima il suggerimento
        setTimeout(() => {
            suggestionEl.classList.remove('translate-y-0', 'opacity-0');
            suggestionEl.classList.add('translate-y-[-120%]', 'opacity-1');
            this.debug('Suggestion animated in');

            // Nascondi dopo 5 secondi
            setTimeout(() => {
                suggestionEl.classList.remove('translate-y-[-120%]', 'opacity-1');
                suggestionEl.classList.add('translate-y-0', 'opacity-0');
                this.debug('Suggestion animated out');
            }, 5000);
        }, 50);

        // Flag per prevenire doppi clic
        let hasHandledClick = false;

        // Aggiungi click handler per mostrare informazioni rilevanti
        suggestionEl.addEventListener('click', (e) => {
            this.debug('Suggestion clicked');

            // Previeni doppi clic nello stesso evento
            if (hasHandledClick) {
                this.debug('Ignoring duplicate suggestion click');
                e.stopPropagation();
                e.preventDefault();
                return;
            }

            hasHandledClick = true;

            // Blocca propagazione per sicurezza
            e.stopPropagation();
            e.preventDefault();

            // Apri SOLO l'assistente, senza aprire automaticamente i content box
            if (!this.isOpen) {
                this.debug('Opening assistant from suggestion (menu only)');
                // NON chiamare toggleAssistant qui, ma gestisci l'apertura direttamente
                if (this.menuElement && this.menuElement.classList.contains('hidden')) {
                    this.menuElement.classList.remove('hidden');
                    this.menuElement.style.display = 'flex';
                    this.isOpen = true;

                    // Anima menu items
                    setTimeout(() => {
                        document.querySelectorAll('.natan-item').forEach((item, index) => {
                            setTimeout(() => {
                                (item as HTMLElement).classList.remove('translate-x-20', 'opacity-0');
                            }, index * 50);
                        });

                        // INSERISCI QUI IL MIGLIORAMENTO AGGIUNTIVO
                        // Highlight suggerito all'apertura del menu
                        setTimeout(() => {
                            // Trova l'item rilevante
                            let relevantItemId = '';
                            switch (sectionId) {
                                case 'hero':
                                    relevantItemId = 'natan-item-what-is-egi';
                                    break;
                                case 'stats':
                                case 'impact':
                                    relevantItemId = 'natan-item-how-impact-works';
                                    break;
                                case 'creator':
                                    relevantItemId = 'natan-item-granular-business';
                                    break;
                                case 'galleries':
                                case 'collections':
                                case 'onboarding':
                                    relevantItemId = 'natan-item-start-without-crypto';
                                    break;
                            }

                            if (relevantItemId) {
                                const relevantItem = document.getElementById(relevantItemId);
                                if (relevantItem) {
                                    this.debug('Highlighting relevant item:', relevantItemId);
                                    // Aggiungi e rimuovi una classe per far lampeggiare leggermente l'elemento
                                    relevantItem.classList.add('natan-item-highlight');

                                    setTimeout(() => {
                                        relevantItem.classList.remove('natan-item-highlight');
                                    }, 1500);
                                }
                            }
                        }, 500);
                    }, 100);
                }
            }

            // Reset flag dopo un timeout
            setTimeout(() => {
                hasHandledClick = false;
            }, 500);
        });
    }

    /**
     * Attiva o disattiva l'assistente programmaticamente
     */
    private toggleAssistant(): void {
        this.debug('toggleAssistant called programmatically');

        if (!this.toggleButton || !this.menuElement || this.isProcessingToggle) {
            this.debug('Toggle not possible now');
            return;
        }

        // Non usare click() per evitare problemi di propagazione
        // Chiama direttamente handleToggleClick con un evento sintetico
        this.handleToggleClick(new MouseEvent('click'));
    }

    /**
     * Apre o chiude un box di contenuto specifico
     */
    private toggleContentBox(id: string): void {
        this.debug('toggleContentBox called for:', id);

        const contentBox = document.getElementById(`natan-content-${id}`);
        const button = document.getElementById(`natan-item-${id}`);

        if (!contentBox || !button) {
            this.debug('ERROR: Content box or button not found for id:', id);
            return;
        }

        // Se c'è già un content box aperto e non è questo, chiudilo
        if (this.currentOpenContentId && this.currentOpenContentId !== id) {
            const openBox = document.getElementById(`natan-content-${this.currentOpenContentId}`);
            const openButton = document.getElementById(`natan-item-${this.currentOpenContentId}`);

            if (openBox && openButton) {
                openBox.classList.add('hidden');
                openButton.setAttribute('aria-expanded', 'false');
                this.debug('Closed previously open content:', this.currentOpenContentId);
            }
        }

        // Toggle stato corrente
        const isExpanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', (!isExpanded).toString());
        this.debug('Set aria-expanded:', !isExpanded);

        if (!isExpanded) {
            // Aggiungi effetto "thinking" quando si apre un contenuto
            this.showThinking(500);

            // Posiziona il content box in modo diverso su mobile
            if (this.isMobile() && contentBox) {
                contentBox.classList.add('bottom-auto', 'top-full', 'right-0', 'mt-2', '-mb-0');
                contentBox.classList.remove('bottom-full', 'mb-2');
                this.debug('Adjusted content box position for mobile');

                // Sposta l'arrow del tooltip
                const arrow = contentBox.querySelector('div[class*="absolute bottom-0"]');
                if (arrow && arrow instanceof HTMLElement) {
                    arrow.className = 'absolute top-0 right-6 w-3 h-3 -mt-1.5 bg-gray-900 border-l border-t border-emerald-600/30 transform rotate-45';
                    this.debug('Repositioned arrow for mobile');
                }
            }

            // Ritardo breve prima di mostrare il contenuto
            setTimeout(() => {
                contentBox.classList.remove('hidden');
                this.currentOpenContentId = id;
                this.debug('Content box displayed');

                // Aggiungi spotlight per elementi specifici basati sull'ID
                setTimeout(() => {
                    // Prendi il selettore dallo spotlight-data attribute
                    const spotlightSelector = contentBox.getAttribute('data-spotlight');
                    if (spotlightSelector) {
                        this.debug('Applying spotlight from data-attribute:', spotlightSelector);
                        this.spotlight(spotlightSelector, 4000);
                    } else {
                        // Fallback per spotlight basato su ID
                        this.debug('No spotlight selector in data-attribute, using fallback based on ID');
                        switch (id) {
                            case 'what-is-egi':
                                // Spotlight sulla prima collezione EGI
                                this.spotlight('.collection-card-nft:first-child', 4000);
                                break;
                            case 'how-impact-works':
                                // Spotlight sugli elementi di impatto
                                this.spotlight('.nft-stats-section [data-counter]', 4000);
                                break;
                            case 'start-without-crypto':
                                // Spotlight sul pulsante di registrazione
                                this.spotlight('#register-link-desktop, #register-link-mobile', 4000);
                                break;
                            case 'granular-business':
                                // Spotlight su una collezione business (se esiste)
                                const businessCards = document.querySelectorAll('.collection-card-nft');
                                if (businessCards.length > 1) {
                                    this.spotlight('.collection-card-nft:nth-child(2)', 4000);
                                }
                                break;
                        }
                    }
                }, 700);
            }, 500);
        } else {
            contentBox.classList.add('hidden');
            this.currentOpenContentId = null;
            this.debug('Content box hidden');
        }
    }

    /**
     * Mostra effetto "thinking" sull'immagine di Natan
     */
    private showThinking(duration: number = 1500): void {
        if (this.isThinking) {
            this.debug('Already in thinking state, ignoring');
            return;
        }

        this.debug('Showing thinking effect for duration:', duration);
        this.isThinking = true;

        const natanImage = this.toggleButton?.querySelector('img');
        if (natanImage) {
            natanImage.classList.add('natan-thinking');
            this.debug('Added natan-thinking class to image');

            setTimeout(() => {
                natanImage.classList.remove('natan-thinking');
                this.isThinking = false;
                this.debug('Removed natan-thinking class from image');
            }, duration);
        } else {
            this.debug('ERROR: Natan image not found');
            this.isThinking = false; // Reset flag in case of error
        }
    }

    /**
     * Evidenzia un elemento nella pagina
     */
    private spotlight(selector: string, duration: number = 3000): void {
        this.debug('Spotlight called for:', selector, 'duration:', duration);

        const elements = document.querySelectorAll(selector);
        if (!elements || elements.length === 0) {
            this.debug('No elements found for selector:', selector);
            return;
        }

        // Crea overlay spotlight se non esiste
        let spotlightOverlay = document.getElementById('natan-spotlight-overlay');
        if (!spotlightOverlay) {
            spotlightOverlay = document.createElement('div');
            spotlightOverlay.id = 'natan-spotlight-overlay';
            spotlightOverlay.className = 'fixed inset-0 z-40 transition-opacity duration-300 bg-black bg-opacity-50 opacity-0 pointer-events-none';
            document.body.appendChild(spotlightOverlay);
            this.debug('Created spotlight overlay');
        }

        // Prendi il primo elemento se ci sono più selettori
        const element = elements[0] as HTMLElement;
        this.debug('Spotlighting element:', element);

        // Crea highlight se non esiste
        let highlight = document.getElementById('natan-highlight');
        if (!highlight) {
            highlight = document.createElement('div');
            highlight.id = 'natan-highlight';
            highlight.className = 'absolute z-50 transition-all duration-300 border-2 rounded-md shadow-lg opacity-0 pointer-events-none border-emerald-400 shadow-emerald-400/30';
            document.body.appendChild(highlight);
            this.debug('Created spotlight highlight');
        }

        // Posiziona l'highlight sull'elemento
        const rect = element.getBoundingClientRect();
        highlight.style.top = `${rect.top - 4 + window.scrollY}px`;
        highlight.style.left = `${rect.left - 4}px`;
        highlight.style.width = `${rect.width + 8}px`;
        highlight.style.height = `${rect.height + 8}px`;

        this.debug('Positioned highlight at:', {
            top: rect.top - 4 + window.scrollY,
            left: rect.left - 4,
            width: rect.width + 8,
            height: rect.height + 8
        });

        // Mostra l'overlay e l'highlight
        spotlightOverlay.classList.remove('opacity-0');
        highlight.classList.remove('opacity-0');
        this.debug('Made spotlight and overlay visible');

        // Aggiungi pulsazione all'highlight
        highlight.style.animation = 'natan-highlight-pulse 2s ease-in-out infinite';

        // Rimuovi dopo la durata specificata
        setTimeout(() => {
            spotlightOverlay.classList.add('opacity-0');
            highlight.classList.add('opacity-0');
            this.debug('Hiding spotlight after duration');

            setTimeout(() => {
                highlight.style.animation = '';
                this.debug('Removed highlight animation');
            }, 300);
        }, duration);
    }

    /**
     * Rileva se il dispositivo è mobile
     */
    private isMobile(): boolean {
        return window.innerWidth < 768;
    }

    /**
     * Mostra pulse di benvenuto
     */
    private showWelcomePulse(): void {
        this.debug('Showing welcome pulse');

        if (!this.toggleButton) {
            this.debug('ERROR: Toggle button not found');
            return;
        }

        this.toggleButton.classList.add('natan-welcome-pulse');
        this.debug('Added welcome-pulse class');

        setTimeout(() => {
            this.toggleButton?.classList.remove('natan-welcome-pulse');
            this.debug('Removed welcome-pulse class');
        }, 4500);
    }

    /**
     * Inizializza i pulsanti "Scopri di più"
     */
    private initLearnMoreButtons(): void {
        this.debug('Initializing learn more buttons');

        document.querySelectorAll('[data-action]').forEach(button => {
            if (!(button instanceof HTMLElement)) return;

            // Skip se è un pulsante già gestito da altro codice
            if (button.hasAttribute('data-action-initialized')) {
                this.debug('Button already initialized:', button);
                return;
            }

            button.setAttribute('data-action-initialized', 'true');

            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                this.debug('Learn more button clicked:', button.dataset);

                const action = button.dataset.action;
                const target = button.dataset.target;

                if (!action || !target) {
                    this.debug('Missing action or target:', action, target);
                    return;
                }

                switch (action) {
                    case 'spotlight':
                        this.debug('Executing spotlight action for:', target);
                        this.spotlight(target, 4000);
                        break;
                    case 'navigate':
                        this.debug('Executing navigate action to:', target);
                        // ATTESA: Non chiudere immediatamente il menu
                        setTimeout(() => {
                            window.location.href = target;
                        }, 300);
                        break;
                    default:
                        this.debug('Unknown action:', action);
                }

                // MODIFICA CRUCIALE: Non chiudere immediatamente il content
                // Aspetta un po' prima di chiudere
                setTimeout(() => {
                    this.debug('Closing content after learn more action');
                    this.closeAllContent();
                }, 500);
            });

            this.debug('Learn more button initialized:', button.dataset);
        });
    }

    /**
     * Aggiunge gli stili CSS necessari se non esistono
     */
    private addStyles(): void {
        this.debug('Adding required styles');

        if (!document.getElementById('natan-thinking-styles')) {
            const styleEl = document.createElement('style');
            styleEl.id = 'natan-thinking-styles';
            styleEl.textContent = `
                @keyframes natan-thinking {
                    0%, 100% { transform: scale(1); filter: brightness(1); }
                    25% { transform: scale(1.05) rotate(-2deg); filter: brightness(1.1); }
                    75% { transform: scale(1.05) rotate(2deg); filter: brightness(1.1); }
                }

                .natan-thinking {
                    animation: natan-thinking 0.8s ease-in-out infinite;
                }

                @keyframes natan-highlight-pulse {
                    0%, 100% { box-shadow: 0 0 0 rgba(16, 185, 129, 0.4); }
                    50% { box-shadow: 0 0 15px 2px rgba(16, 185, 129, 0.6); }
                }

                @keyframes natan-welcome-pulse {
                    0%, 100% { transform: scale(1); box-shadow: 0 0 0 rgba(16, 185, 129, 0.4); }
                    50% { transform: scale(1.1); box-shadow: 0 0 15px 5px rgba(16, 185, 129, 0.6); }
                }

                .natan-welcome-pulse {
                    animation: natan-welcome-pulse 1.5s ease-in-out 3;
                }

                @keyframes natan-typing {
                    0% { width: 0; }
                    100% { width: 100%; }
                }

                .natan-typing-effect {
                    overflow: hidden;
                    white-space: nowrap;
                    animation: natan-typing 2s ease-in-out;
                }

                #natan-assistant-menu:not(.hidden) {
                    display: flex !important;
                }

                /* Style per l'overlay di spotlight */
                #natan-spotlight-overlay {
                    backdrop-filter: blur(2px);
                }

                /* Style per l'highlight di spotlight */
                #natan-highlight {
                    box-shadow: 0 0 20px 5px rgba(16, 185, 129, 0.4);
                }

                /* Miglioramenti per il suggerimento contestuale */
                #natan-suggestion {
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                    position: absolute;
                    z-index: 60;
                    cursor: pointer;
                }

                /* Classe per il miglioramento highlight */
                .natan-item-highlight {
                    border-color: rgba(16, 185, 129, 0.7) !important;
                    box-shadow: 0 0 8px rgba(16, 185, 129, 0.5) !important;
                    transform: scale(1.05) !important;
                }

                /* NUOVI STILI PER IL MAGGIORDOMO */
                #natan-butler-modal {
                    backdrop-filter: blur(4px);
                    z-index: 9999;
                }

                .natan-butler-container {
                    transform: scale(0.9);
                    opacity: 0;
                    transition: all 0.3s ease-out;
                }

                .natan-butler-option {
                    transition: all 0.2s ease;
                }

                .natan-butler-option:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
                }

                .natan-butler-option:active {
                    transform: translateY(0);
                }

                /* Animazione per l'avatar del maggiordomo */
                @keyframes natan-butler-float {
                    0%, 100% { transform: translateY(0px) rotate(0deg); }
                    33% { transform: translateY(-2px) rotate(1deg); }
                    66% { transform: translateY(-1px) rotate(-1deg); }
                }

                .natan-butler-container .w-24 {
                    animation: natan-butler-float 3s ease-in-out infinite;
                }

                /* Responsività per mobile */
                @media (max-width: 640px) {
                    .natan-butler-container {
                        margin: 1rem;
                        max-width: calc(100vw - 2rem);
                    }

                    #natan-butler-modal .space-y-3 {
                        gap: 0.5rem;
                    }

                    .natan-butler-option {
                        padding: 0.75rem;
                    }
                }

                /* Effetti di hover migliorati */
                #natan-butler-close:hover {
                    transform: scale(1.1);
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
                }

                /* Gradiente animato per il background del modal */
                .natan-butler-container {
                    position: relative;
                    overflow: hidden;
                }

                .natan-butler-container::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 1px;
                    background: linear-gradient(90deg,
                        transparent,
                        rgba(16, 185, 129, 0.5),
                        transparent
                    );
                    animation: natan-shimmer 3s ease-in-out infinite;
                }

                @keyframes natan-shimmer {
                    0%, 100% { opacity: 0; transform: translateX(-100%); }
                    50% { opacity: 1; transform: translateX(100%); }
                }
            `;
            document.head.appendChild(styleEl);
            this.debug('Added required styles to document head');
        } else {
            this.debug('Styles already exist');
        }
    }

    /**
     * Inizializza le sezioni principali della pagina
     */
    private initSections(): void {
        this.debug('Initializing page sections');

        // Mappa le sezioni principali della pagina con suggerimenti contestuali
        const sectionMappings = [
            {
                selector: '#hero-section',
                id: 'hero',
                suggestion: "Scopri cos'è un EGI 👆"
            },
            {
                selector: '.nft-stats-section',
                id: 'stats',
                suggestion: "Ti interessa l'impatto ambientale? 🌱"
            },
            {
                selector: 'section[aria-labelledby="latest-galleries-heading"]',
                id: 'galleries',
                suggestion: "Vuoi creare la tua galleria? 🎨"
            },
            {
                selector: 'section[aria-labelledby="environmental-impact-heading"]',
                id: 'impact',
                suggestion: "Ecco come funziona l'impatto 🌍"
            },
            {
                selector: 'section[aria-labelledby="creator-cta-heading"]',
                id: 'creator',
                suggestion: "Scopri il business granulare 💼"
            }
        ];

        // Popola l'array delle sezioni con elementi DOM reali
        this.sections = sectionMappings
            .map(mapping => {
                const element = document.querySelector(mapping.selector);
                if (element) {
                    this.debug('Found section:', mapping.id, 'for selector:', mapping.selector);
                    return {
                        id: mapping.id,
                        element: element as HTMLElement,
                        suggestion: mapping.suggestion
                    };
                } else {
                    this.debug('Section not found for selector:', mapping.selector);
                    return null;
                }
            })
            .filter(section => section !== null) as { id: string, element: HTMLElement, suggestion: string }[];

        this.debug('Initialized sections:', this.sections.length);
    }

    /**
     * Inizializza l'observer per le sezioni visibili
     */
    private initScrollObserver(): void {
        this.debug('Initializing scroll observer');

        // Usa Intersection Observer per rilevare quando le sezioni sono visibili
        if (!('IntersectionObserver' in window)) {
            this.debug('IntersectionObserver not supported in this browser');
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                // Trova la sezione corrispondente
                const section = this.sections.find(s => s.element === entry.target);
                if (!section) return;

                // Se la sezione è visibile e non è quella corrente
                if (entry.isIntersecting && this.currentSection !== section.id) {
                    this.currentSection = section.id;
                    this.debug('Section now visible:', section.id);

                    // Mostra un suggerimento contestuale dopo un breve ritardo
                    // ma solo se l'assistente non è già aperto
                    if (!this.isOpen) {
                        // Cancella eventuali timeout precedenti
                        if (this.suggestionTimeout !== null) {
                            window.clearTimeout(this.suggestionTimeout);
                            this.debug('Cleared previous suggestion timeout');
                        }

                        // Imposta un nuovo timeout per mostrare il suggerimento
                        this.suggestionTimeout = window.setTimeout(() => {
                            this.debug('Showing suggestion for section:', section.id);
                            this.showSuggestion(section.suggestion, section.id);
                        }, 2000); // Mostra dopo 2 secondi nella sezione
                    }
                }
            });
        }, {
            threshold: 0.3 // Trigger quando almeno il 30% della sezione è visibile
        });

        // Osserva tutte le sezioni
        this.sections.forEach(section => {
            observer.observe(section.element);
            this.debug('Observing section:', section.id);
        });
    }

    /**
     * Inizializza listener per azioni utente
     */
    private initUserActionListeners(): void {
        this.debug('Initializing user action listeners');

        // Reagisci quando l'utente clicca su CTA importanti
        document.querySelectorAll('a[href*="register"], a[href*="connect-wallet"], [data-action*="connect"]').forEach(el => {
            el.addEventListener('click', () => {
                // Memorizza che l'utente ha mostrato interesse per la registrazione/connessione
                localStorage.setItem('natan_user_interested', 'true');
                this.debug('User showed interest in registration/connection');

                // Suggerisci l'assistenza appropriata dopo un breve ritardo
                setTimeout(() => {
                    if (!this.isOpen) {
                        this.debug('Showing onboarding suggestion');
                        this.showSuggestion("Posso aiutarti a iniziare! 👋", "onboarding");
                    }
                }, 1000);
            });
        });

        // Reagisci quando l'utente visita una collezione
        document.querySelectorAll('a[href*="collections"], .collection-card-nft a').forEach(el => {
            el.addEventListener('click', () => {
                // Memorizza che l'utente ha mostrato interesse per le collezioni
                localStorage.setItem('natan_viewed_collections', 'true');
                this.debug('User showed interest in collections');
            });
        });
    }

    /**
     * Inizializza suggerimenti al passaggio del mouse
     */
    private initHoverSuggestions(): void {
        this.debug('Initializing hover suggestions');

        // Elementi importanti da monitorare per hover
        const hoverSelectors = [
            // Login/Register e Connect buttons
            'a[href*="register"], a[href*="login"], [data-action*="connect-modal"]',
            // Collezioni e EGI cards
            '.collection-card-nft, a[href*="collections"]',
            // Contatori impatto
            '.nft-stats-section [data-counter]',
            // EPP sections
            'section[aria-labelledby="environmental-impact-heading"] article'
        ];

        hoverSelectors.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            this.debug(`Found ${elements.length} elements for hover selector: ${selector}`);

            elements.forEach(element => {
                // Aggiungi event listeners per mouseenter/mouseleave
                element.addEventListener('mouseenter', () => {
                    // Determina il tipo di elemento per personalizzare il suggerimento
                    let suggestion = '';
                    let sectionId = '';

                    if (element.matches('a[href*="register"], a[href*="login"]')) {
                        suggestion = "Registrati per creare la tua galleria 🎨";
                        sectionId = "register";
                    } else if (element.matches('[data-action*="connect-modal"]')) {
                        suggestion = "Connetti il wallet per iniziare 💼";
                        sectionId = "wallet";
                    } else if (element.matches('.collection-card-nft, a[href*="collections"]')) {
                        suggestion = "Esplora questa collezione di EGI 🔍";
                        sectionId = "collections";
                    } else if (element.matches('.nft-stats-section [data-counter]')) {
                        suggestion = "Impatto reale verificabile 🌱";
                        sectionId = "impact";
                    } else if (element.matches('section[aria-labelledby="environmental-impact-heading"] article')) {
                        suggestion = "Scopri come contribuiamo all'ambiente 🌍";
                        sectionId = "epp";
                    }

                    if (suggestion) {
                        this.debug('Showing quick suggestion on hover:', suggestion);
                        this.showQuickSuggestion(suggestion, sectionId, element as HTMLElement);
                    }
                });

                element.addEventListener('mouseleave', () => {
                    this.hideQuickSuggestion();
                });
            });
        });
    }

    /**
     * Mostra un suggerimento rapido al passaggio del mouse
     */
    private showQuickSuggestion(text: string, sectionId: string, element: HTMLElement): void {
        // Nascondi eventuali suggerimenti esistenti
        this.hideQuickSuggestion();
        this.debug('Showing quick suggestion:', text);

        // Crea l'elemento del suggerimento
        const suggestionEl = document.createElement('div');
        suggestionEl.id = 'natan-quick-suggestion';
        suggestionEl.className = 'absolute bg-gray-900/95 text-xs font-medium text-emerald-300 px-3 py-1.5 rounded-full border border-emerald-600/30 shadow-lg z-[10000] transition-opacity duration-200 opacity-0 pointer-events-none';
        suggestionEl.textContent = text;
        document.body.appendChild(suggestionEl);
        this.debug('Created quick suggestion element');

        // Posiziona il suggerimento vicino all'elemento
        const rect = element.getBoundingClientRect();
        const top = rect.top + window.scrollY - 30; // Sopra l'elemento
        const left = rect.left + rect.width / 2; // Centrato

        suggestionEl.style.top = `${top}px`;
        suggestionEl.style.left = `${left}px`;
        suggestionEl.style.transform = 'translateX(-50%)';
        this.debug('Positioned quick suggestion at:', { top, left });

        // Rendi visibile con un breve ritardo
        setTimeout(() => {
            suggestionEl.style.opacity = '1';
            this.debug('Made quick suggestion visible');
        }, 50);
    }

    /**
     * Nasconde i suggerimenti rapidi
     */
    private hideQuickSuggestion(): void {
        const suggestionEl = document.getElementById('natan-quick-suggestion');
        if (suggestionEl) {
            this.debug('Hiding quick suggestion');
            suggestionEl.style.opacity = '0';
            setTimeout(() => {
                suggestionEl.remove();
                this.debug('Removed quick suggestion element');
            }, 200);
        }
    }

    /**
     * Verifica lo storico dell'utente
     */
    private checkUserHistory(): void {
        this.debug('Checking user history');

        // Controlla se l'utente ha già interagito con il sito
        const hasViewedCollections = localStorage.getItem('natan_viewed_collections') === 'true';
        const isInterested = localStorage.getItem('natan_user_interested') === 'true';

        this.debug('User history:', { hasViewedCollections, isInterested });

        // Personalizza il comportamento di Natan in base alla storia dell'utente
        if (hasViewedCollections && !this.hasOpenedAssistant()) {
            // L'utente ha visto collezioni ma non ha ancora usato l'assistente
            setTimeout(() => {
                this.debug('Showing collection suggestion based on history');
                this.showSuggestion("Ti interessa creare la tua collezione? 🎨", "collections");
            }, 3000);
        } else if (isInterested) {
            // L'utente ha mostrato interesse a registrarsi/connettersi
            setTimeout(() => {
                this.debug('Showing onboarding suggestion based on history');
                this.showSuggestion("Hai bisogno di aiuto per iniziare? 👋", "onboarding");
            }, 3000);
        }
    }

    /**
     * Verifica se l'assistente è stato aperto
     */
    private hasOpenedAssistant(): boolean {
        const hasOpened = localStorage.getItem('natan_assistant_opened') === 'true';
        this.debug('Has user opened assistant before?', hasOpened);
        return hasOpened;
    }

    /**
     * @Oracode Crea il modal del maggiordomo con categorie accordion
     * 🎯 Purpose: Modal di benvenuto con struttura accordion per guidare l'utente
     * 📥 Input: None
     * 📤 Output: Modal DOM element with accordion categories
     *
     * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
     * @version 3.0.0 (FlorenceEGI - Accordion Categories Implementation)
     * @date 2025-07-07
     * @brand-compliant Usa Oro Fiorentino #D4A574, Verde Rinascita #2D5016, Blu Algoritmo #1B365D
     */
    private createButlerModal(): void {
        //console.log('🎩 [NATAN BUTLER] createButlerModal called');
        this.debug('Creating butler modal with accordion categories');

        // Rimuovi eventuali modal esistenti
        const existingModal = document.getElementById('natan-butler-modal');
        if (existingModal) {
            //console.log('🎩 [NATAN BUTLER] Removing existing modal');
            existingModal.remove();
        }

        // Crea il modal del maggiordomo
        this.butlerModal = document.createElement('div');
        this.butlerModal.id = 'natan-butler-modal';
        this.butlerModal.className = 'natan-butler-modal-base';

        // Applica stili CSS di base inline
        this.butlerModal.style.cssText = `
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            z-index: 999999;
            background-color: rgba(0, 0, 0, 0.7);
            align-items: center;
            justify-content: center;
        `;

        // Carica la configurazione per le traduzioni
        let config;
        try {
            config = getAppConfig();
        } catch (error) {
            console.warn('App config not loaded yet, cannot create butler modal with translations');
            return;
        }

        // Genera il contenuto accordion
        const t = (key: string) => appTranslate(key, config.translations);
        const categoriesHtml = this.generateAccordionCategories(t);

        const headerTitle = t('assistant.header.title');
        const headerSubtitle = t('assistant.header.subtitle');
        const welcomeMsg = t('assistant.welcome');
        const dismissLabel = t('assistant.dismiss');
        const closeAria = t('assistant.close_aria');

        // --- FINE LOCALIZZAZIONE STRINGHE BUTLER ---

        // --- CHECKBOX AUTO-OPEN ---
        const autoOpenLabel = t('assistant.auto_open_label');
        const autoOpenHint = t('assistant.auto_open_hint');
        const autoOpenCheckboxHtml = `
            <div style="margin-top: 1rem; text-align: center;">
                <label style="color: #D4A574; font-size: 0.85rem;">
                    <input type="checkbox" id="natan-auto-open-checkbox" />
                    ${autoOpenLabel}
                </label>
                <div style="font-size: 0.75rem; color: #aaa; margin-top: 0.25rem;">
                    ${autoOpenHint}
                </div>
            </div>
        `;

        this.butlerModal.innerHTML = `
            <div class="natan-butler-container" style="
                background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
                border: 1px solid rgba(212, 165, 116, 0.3);
                border-radius: 16px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 25px 50px -12px rgba(212, 165, 116, 0.1);
                max-width: 32rem;
                margin: 0 1rem;
                overflow: hidden;
                width: 100%;
                max-height: 90vh;
                position: relative;
            ">
                <!-- Header con Natan e pulsante chiudi -->
                <div style="
                    background: linear-gradient(90deg, rgba(212, 165, 116, 0.2) 0%, rgba(212, 165, 116, 0.1) 100%);
                    padding: 1.5rem;
                    text-align: center;
                    border-bottom: 1px solid rgba(212, 165, 116, 0.3);
                    position: relative;
                ">
                    <button id="natan-butler-close" style="
                        position: absolute;
                        top: 1rem;
                        right: 1rem;
                        width: 2rem;
                        height: 2rem;
                        background: rgba(55, 65, 81, 0.5);
                        border: none;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        cursor: pointer;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='rgba(75, 85, 99, 0.5)'" onmouseout="this.style.backgroundColor='rgba(55, 65, 81, 0.5)'" aria-label="${closeAria}">
                        <svg style="width: 1rem; height: 1rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <!-- Avatar di Natan -->
                    <div style="
                        width: 6rem;
                        height: 6rem;
                        margin: 0 auto 1rem auto;
                        border-radius: 50%;
                        background: linear-gradient(135deg, #D4A574 0%, #E6B885 100%);
                        padding: 4px;
                    ">
                        <div style="
                            width: 100%;
                            height: 100%;
                            border-radius: 50%;
                            background: #111827;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        ">
                            <div style="
                                width: 4rem;
                                height: 4rem;
                                border-radius: 50%;
                                background: rgba(212, 165, 116, 0.2);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                <span style="font-size: 2rem;">🎩</span>
                            </div>
                        </div>
                    </div>

                    <h2 style="font-size: 1.25rem; font-weight: bold; color: white; margin-bottom: 0.5rem;">${headerTitle}</h2>
                    <p style="color: #D4A574; font-size: 0.875rem;">${headerSubtitle}</p>
                </div>

                <!-- Messaggio di benvenuto -->
                <div class="natan-content" style="padding: 1.5rem; max-height: 60vh; overflow-y: auto;">
                    <div class="natan-welcome-message" style="margin-bottom: 1.5rem;">
                        <span class="natan-welcome-text" style="color: #ecfdf5; font-size: 0.875rem; line-height: 1.5;">${welcomeMsg}</span>
                    </div>

                    <!-- Accordion Categories -->
                    <div id="natan-accordion-container">
                        ${categoriesHtml}
                    </div>
                </div>

                <!-- Footer -->
                <div style="
                    margin-top: 1.5rem;
                    padding: 1rem 1.5rem;
                    border-top: 1px solid rgba(212, 165, 116, 0.2);
                    text-align: center;
                    background: rgba(212, 165, 116, 0.05);
                ">
                    ${autoOpenCheckboxHtml}
                </div>
            </div>
        `;

        document.body.appendChild(this.butlerModal);
        //console.log('🎩 [NATAN BUTLER] Modal appended to body');

        // Aggiungi event listeners
        this.setupButlerEventListeners();

        // Inizializza la checkbox auto-open
        setTimeout(() => {
            const cb = document.getElementById('natan-auto-open-checkbox') as HTMLInputElement | null;
            if (cb) {
                cb.checked = (window as any).natanAssistantAutoOpen !== false;
                cb.addEventListener('change', function () {
                    let csrf = (window as any).Laravel?.csrfToken;
                    if (!csrf) {
                        const meta = document.querySelector('meta[name="csrf-token"]');
                        csrf = meta ? meta.getAttribute('content') : '';
                    }
                    fetch('/api/assistant/auto-open', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({ auto_open: cb.checked })
                    }).then(() => {
                        // Aggiorna la variabile JS locale per coerenza immediata
                        (window as any).natanAssistantAutoOpen = cb.checked;
                    });
                });
            }
        }, 0);

        //console.log('🎩 [NATAN BUTLER] Butler modal created with accordion');
        this.debug('Butler modal created with accordion categories');
    }

    /**
     * @Oracode Genera HTML per le categorie accordion
     * 🎯 Purpose: Crea la struttura HTML delle categorie accordion
     * 📥 Input: Funzione traduzione
     * 📤 Output: HTML string delle categorie
     *
     * @param t - Funzione di traduzione
     * @returns HTML string delle categorie accordion
     */
    private generateAccordionCategories(t: Function): string {
        return butlerCategories.map((category, index) => {
            const isFirst = index === 0;
            const subOptionsHtml = category.subOptions.map(subOption => `
                <button class="natan-sub-option" data-action="${subOption.key}" style="
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    width: 100%;
                    padding: 0.75rem;
                    text-align: left;
                    background: rgba(55, 65, 81, 0.3);
                    border: 1px solid rgba(75, 85, 99, 0.3);
                    border-radius: 8px;
                    color: #ecfdf5;
                    font-size: 0.875rem;
                    transition: all 0.2s ease;
                    cursor: pointer;
                " onmouseover="this.style.background='rgba(75, 85, 99, 0.4)'; this.style.borderColor='rgba(212, 165, 116, 0.3)'"
                onmouseout="this.style.background='rgba(55, 65, 81, 0.3)'; this.style.borderColor='rgba(75, 85, 99, 0.3)'">
                    <span style="font-size: 1rem;">${subOption.icon}</span>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: #ecfdf5;">${t(subOption.label)}</div>
                        <div style="font-size: 0.75rem; color: rgba(212, 165, 116, 0.7); margin-top: 0.25rem;">${t(subOption.description)}</div>
                    </div>
                </button>
            `).join('');

            return `
                <div class="natan-accordion-category" style="border: 1px solid rgba(75, 85, 99, 0.3); border-radius: 8px; margin-bottom: 0.75rem; overflow: hidden;">
                    <!-- Category Header -->
                    <button class="natan-category-header" data-category="${category.key}" style="
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        width: 100%;
                        padding: 1rem;
                        text-align: left;
                        background: rgba(75, 85, 99, 0.2);
                        border: none;
                        color: #ecfdf5;
                        font-weight: 500;
                        cursor: pointer;
                        transition: all 0.2s ease;
                    " onmouseover="this.style.background='rgba(75, 85, 99, 0.3)'"
                    onmouseout="this.style.background='rgba(75, 85, 99, 0.2)'"
                    aria-expanded="${isFirst ? 'true' : 'false'}"
                    aria-controls="category-content-${category.key}">
                        <span style="font-size: 1.25rem;">${category.icon}</span>
                        <div style="flex: 1;">
                            <div style="color: #ecfdf5;">${t(category.label)}</div>
                            <div style="font-size: 0.75rem; color: rgba(212, 165, 116, 0.7); font-weight: normal; margin-top: 0.25rem;">${t(category.description)}</div>
                        </div>
                        <svg class="natan-chevron" style="
                            width: 1rem;
                            height: 1rem;
                            color: #D4A574;
                            transition: transform 0.2s ease;
                            transform: ${isFirst ? 'rotate(180deg)' : 'rotate(0deg)'};
                        " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Category Content -->
                    <div id="category-content-${category.key}"
                        class="natan-category-content"
                        style="
                            display: ${isFirst ? 'block' : 'none'};
                            padding: ${isFirst ? '1rem' : '0'};
                            background: rgba(17, 24, 39, 0.3);
                            transition: all 0.3s ease;
                        "
                        aria-labelledby="category-header-${category.key}">
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            ${subOptionsHtml}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    /**
         * @Oracode Configura gli event listener per il modal del maggiordomo con accordion
         * 🎯 Purpose: Setup event handlers per accordion categories e sub-options
         * 📥 Input: None
         * 📤 Output: Configured event listeners on modal elements
         *
         * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
         * @version 3.0.0 (FlorenceEGI - Accordion Event Handling)
         * @date 2025-07-07
         */
    private setupButlerEventListeners(): void {
        if (!this.butlerModal) return;

        this.debug('Setting up butler event listeners with accordion support');

        // Pulsante chiudi (X)
        const closeButton = this.butlerModal.querySelector('#natan-butler-close');
        closeButton?.addEventListener('click', () => {
            this.hideButlerModal();
        });

        // Pulsante "Non mostrare più"
        const dismissButton = this.butlerModal.querySelector('#natan-butler-dismiss');
        dismissButton?.addEventListener('click', () => {
            this.dismissButlerPermanently();
        });

        // === ACCORDION CATEGORY HEADERS ===
        this.butlerModal.querySelectorAll('.natan-category-header').forEach(categoryHeader => {
            categoryHeader.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleAccordionToggle(e.currentTarget as HTMLElement);
            });
        });

        // === SUB-OPTIONS DELLE CATEGORIE ===
        this.butlerModal.querySelectorAll('.natan-sub-option').forEach(optionBtn => {
            optionBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const actionKey = (e.currentTarget as HTMLElement).getAttribute('data-action');
                if (actionKey) {
                    this.handleAccordionSubOption(actionKey);
                }
            });
        });

        // Click esterno per chiudere
        this.butlerModal.addEventListener('click', (e) => {
            if (e.target === this.butlerModal) {
                this.hideButlerModal();
            }
        });

        // Escape per chiudere
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !this.butlerModal?.classList.contains('hidden')) {
                this.hideButlerModal();
            }
        });

        this.debug('Butler event listeners configured with accordion support');
    }

    /**
     * @Oracode Gestisce il toggle delle categorie accordion
     * 🎯 Purpose: Espande/contrae le categorie accordion
     * 📥 Input: Header element cliccato
     * 📤 Output: Accordion state toggled
     *
     * @param headerElement - Elemento header della categoria
     */
    private handleAccordionToggle(headerElement: HTMLElement): void {
        const categoryKey = headerElement.getAttribute('data-category');
        if (!categoryKey) return;

        const contentElement = document.getElementById(`category-content-${categoryKey}`);
        const chevronElement = headerElement.querySelector('.natan-chevron') as HTMLElement;

        if (!contentElement || !chevronElement) return;

        const isExpanded = headerElement.getAttribute('aria-expanded') === 'true';
        this.debug(`Accordion toggle for category: ${categoryKey}, currently expanded: ${isExpanded}`);

        // Chiudi tutte le altre categorie (accordion singolo)
        this.butlerModal?.querySelectorAll('.natan-category-header').forEach(otherHeader => {
            if (otherHeader !== headerElement) {
                const otherCategoryKey = otherHeader.getAttribute('data-category');
                const otherContentElement = document.getElementById(`category-content-${otherCategoryKey}`);
                const otherChevronElement = otherHeader.querySelector('.natan-chevron') as HTMLElement;

                if (otherContentElement && otherChevronElement) {
                    otherHeader.setAttribute('aria-expanded', 'false');
                    otherContentElement.style.display = 'none';
                    otherContentElement.style.padding = '0';
                    otherChevronElement.style.transform = 'rotate(0deg)';
                }
            }
        });

        // Toggle categoria corrente
        if (isExpanded) {
            // Chiudi
            headerElement.setAttribute('aria-expanded', 'false');
            contentElement.style.display = 'none';
            contentElement.style.padding = '0';
            chevronElement.style.transform = 'rotate(0deg)';
            this.debug(`Closed accordion category: ${categoryKey}`);
        } else {
            // Apri
            headerElement.setAttribute('aria-expanded', 'true');
            contentElement.style.display = 'block';
            contentElement.style.padding = '1rem';
            chevronElement.style.transform = 'rotate(180deg)';
            this.debug(`Opened accordion category: ${categoryKey}`);
        }
    }

    /**
     * @Oracode Gestisce il click su una sub-option dell'accordion
     * 🎯 Purpose: Esegue l'azione associata alla sub-option selezionata
     * 📥 Input: Action key della sub-option
     * 📤 Output: Action executed and modal handled
     *
     * @param actionKey - Chiave dell'azione da eseguire
     */
    private handleAccordionSubOption(actionKey: string): void {
        this.debug(`Accordion sub-option selected: ${actionKey}`);

        // Trova la sub-option corrispondente nelle categorie
        let selectedSubOption: ButlerSubOption | null = null;
        let parentCategory: ButlerCategory | null = null;

        for (const category of butlerCategories) {
            const subOption = category.subOptions.find(opt => opt.key === actionKey);
            if (subOption) {
                selectedSubOption = subOption;
                parentCategory = category;
                break;
            }
        }

        if (!selectedSubOption || !parentCategory) {
            this.debug(`Sub-option not found: ${actionKey}`);
            return;
        }

        // Nascondi il modal prima di eseguire l'azione
        this.hideButlerModal();

        // Segna che l'utente ha interagito con il maggiordomo
        localStorage.setItem('natan_assistant_opened', 'true');

        // Log dell'azione per analytics
        //console.log('🎩 [NATAN BUTLER] Sub-option action executed:', {
        //     category: parentCategory.key,
        //     action: actionKey,
        //     timestamp: new Date().toISOString()
        // });

        // Esegui l'azione dopo un breve delay per permettere l'animazione di chiusura
        setTimeout(() => {
            try {
                selectedSubOption!.action();
                this.debug(`Successfully executed action: ${actionKey}`);
            } catch (error) {
                this.debug(`Error executing action ${actionKey}:`, error);
                // Fallback: mostra alert con messaggio generico
                alert(`Azione non ancora disponibile: ${selectedSubOption!.label}`);
            }
        }, 300);

        // Mostra suggerimento di follow-up dopo azioni che rimangono sulla stessa pagina
        const navigationActions = ['why_cant_buy_egis', 'become_patron', 'discover_archetypes', 'create_collection'];
        if (!navigationActions.includes(actionKey)) {
            setTimeout(() => {
                if (!this.isOpen && this.toggleButton) {
                    this.showSuggestion("Posso aiutarti con altro! 😊", "follow-up");
                }
            }, 5000);
        }
    }

    /**
     * Mostra il modal di benvenuto del maggiordomo
     */
    private showButlerWelcome(): void {
        //console.log('🎩 [NATAN BUTLER] showButlerWelcome called');
        //console.log('🎩 [NATAN BUTLER] butlerModal exists:', !!this.butlerModal);
        //console.log('🎩 [NATAN BUTLER] isButlerDismissed:', this.hasGreeted);

        if (!this.butlerModal || this.hasGreeted) {
            this.debug('Butler modal not available or dismissed');
            return;
        }

        this.debug('Showing butler welcome modal');
        //console.log('🎩 [NATAN BUTLER] About to show modal');

        // FORZA BRUTALMENTE la visualizzazione del modal
        this.butlerModal.className = 'natan-butler-modal-visible';

        // Applica stili CSS inline super aggressivi
        this.butlerModal.style.cssText = `
            display: flex !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 999999 !important;
            background-color: rgba(0, 0, 0, 0.7) !important;
            align-items: center !important;
            justify-content: center !important;
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
        `;

        //console.log('🎩 [NATAN BUTLER] Modal should now be visible');
        //console.log('🎩 [NATAN BUTLER] Modal classes:', this.butlerModal.className);
        //console.log('🎩 [NATAN BUTLER] Modal display:', this.butlerModal.style.display);

        // DEBUG AGGRESSIVO: Verifica che il modal sia effettivamente visibile
        setTimeout(() => {
            const modalElement = document.getElementById('natan-butler-modal');
            if (modalElement) {
                const computedStyle = window.getComputedStyle(modalElement);
                //console.log('🎩 [NATAN BUTLER] DEBUG Computed styles:');
                //console.log('- display:', computedStyle.display);
                //console.log('- visibility:', computedStyle.visibility);
                //console.log('- opacity:', computedStyle.opacity);
                //console.log('- z-index:', computedStyle.zIndex);
                //console.log('- position:', computedStyle.position);
                //console.log('- width:', computedStyle.width);
                //console.log('- height:', computedStyle.height);
                //console.log('🎩 [NATAN BUTLER] Modal rect:', modalElement.getBoundingClientRect());
            }
        }, 100);

        // Anima l'entrata
        const container = this.butlerModal.querySelector('.natan-butler-container');
        if (container) {
            //console.log('🎩 [NATAN BUTLER] Container found, animating');
            (container as HTMLElement).style.transform = 'scale(0.9)';
            (container as HTMLElement).style.opacity = '0';

            setTimeout(() => {
                (container as HTMLElement).style.transition = 'all 0.3s ease-out';
                (container as HTMLElement).style.transform = 'scale(1)';
                (container as HTMLElement).style.opacity = '1';
                //console.log('🎩 [NATAN BUTLER] Animation applied');
            }, 50);
        } else {
            //console.log('🎩 [NATAN BUTLER] ERROR: Container not found!');
        }

        // Anima il testo di benvenuto
        setTimeout(() => {
            const welcomeText = this.butlerModal?.querySelector('.natan-welcome-text');
            if (welcomeText) {
                welcomeText.classList.add('natan-typing-effect');
            }
        }, 300);

        // Marca come salutato
        this.hasGreeted = true;
        localStorage.setItem('natan_has_greeted', 'true');

        // Auto-nascondi dopo 60 secondi se non interagisce (aumentato da 30s)
        this.dismissTimeout = window.setTimeout(() => {
            //console.log('🎩 [NATAN BUTLER] Auto-dismissing modal after 60 seconds');
            this.hideButlerModal();
        }, 60000);
    }

    /**
     * Nasconde il modal del maggiordomo
     */
    private hideButlerModal(): void {
        if (!this.butlerModal) return;

        //console.log('🎩 [NATAN BUTLER] Hiding butler modal');
        this.debug('Hiding butler modal');

        // Cancella timeout di auto-dismiss
        if (this.dismissTimeout) {
            clearTimeout(this.dismissTimeout);
            this.dismissTimeout = null;
        }

        // Anima l'uscita
        const container = this.butlerModal.querySelector('.natan-butler-container');
        if (container) {
            (container as HTMLElement).style.transition = 'all 0.2s ease-in';
            (container as HTMLElement).style.transform = 'scale(0.9)';
            (container as HTMLElement).style.opacity = '0';
        }

        // CRUCIALE: Disabilita immediatamente i pointer events per permettere interazione con la pagina
        this.butlerModal.style.pointerEvents = 'none';

        // Nascondi completamente dopo l'animazione
        setTimeout(() => {
            if (this.butlerModal) {
                // Rimuovi completamente gli stili inline che forzano la visualizzazione
                this.butlerModal.style.cssText = 'display: none !important;';

                // Aggiungi anche la classe hidden per sicurezza
                this.butlerModal.classList.add('hidden');

                //console.log('🎩 [NATAN BUTLER] Modal completely hidden');
                this.debug('Butler modal completely hidden and page interaction restored');
            }
        }, 200);
    }

    /**
     * Dismissal permanente del maggiordomo
     */
    private dismissButlerPermanently(): void {
        this.debug('Butler dismissed permanently');

        this.hasGreeted = true;
        localStorage.setItem('natan_has_greeted', 'true');

        this.hideButlerModal();
    }

    /**
     * Gestisce le azioni del maggiordomo
     */
    private handleButlerAction(action: string): void {
        //console.log('🎩 [NATAN BUTLER] Butler action selected:', action);
        this.debug('Butler action selected:', action);

        // Nascondi il modal
        this.hideButlerModal();

        // Esegui l'azione appropriata
        switch (action) {
            case 'explore':
                //console.log('🎩 [NATAN BUTLER] Navigating to collections page');
                // Naviga alla pagina delle collezioni
                window.location.href = '/home/collections';
                break;

            case 'learn':
                //console.log('🎩 [NATAN BUTLER] Showing impact information');
                // Scrolla alla sezione impatto o stats
                const impactSection = document.querySelector('.nft-stats-section');
                if (impactSection) {
                    impactSection.scrollIntoView({ behavior: 'smooth' });
                    setTimeout(() => {
                        this.spotlight('.nft-stats-section [data-counter]', 4000);
                    }, 1000);
                } else {
                    // Se non c'è la sezione, naviga a una pagina informativa
                    //console.log('🎩 [NATAN BUTLER] Impact section not found, could navigate to info page');
                }
                break;

            case 'start':
                //console.log('🎩 [NATAN BUTLER] Highlighting registration options');
                // Spotlight sui pulsanti di registrazione
                this.spotlight('#register-link-desktop, #register-link-mobile', 4000);
                break;

            case 'business':
                //console.log('🎩 [NATAN BUTLER] Showing business opportunities');
                // Scrolla alla sezione creator
                const creatorSection = document.querySelector('section[aria-labelledby="creator-cta-heading"]');
                if (creatorSection) {
                    creatorSection.scrollIntoView({ behavior: 'smooth' });
                    setTimeout(() => {
                        this.spotlight('section[aria-labelledby="creator-cta-heading"] .cta-button', 4000);
                    }, 1000);
                } else {
                    // Se non c'è la sezione, potremmo navigare a una pagina dedicata
                    //console.log('🎩 [NATAN BUTLER] Creator section not found, could navigate to business page');
                }
                break;

            default:
            //console.log('🎩 [NATAN BUTLER] Unknown action:', action);
        }

        // Segna che l'utente ha interagito con il maggiordomo
        localStorage.setItem('natan_assistant_opened', 'true');

        // Apri l'assistente normale dopo un momento per ulteriore aiuto (solo se rimaniamo sulla stessa pagina)
        if (action !== 'explore') {
            setTimeout(() => {
                if (!this.isOpen && this.toggleButton) {
                    this.showSuggestion("Posso aiutarti con altro! 😊", "follow-up");
                }
            }, 3000);
        }
    }

    /**
     * Reset completo del maggiordomo (utile per testing)
     */
    public async resetButler(): Promise<void> {
        //console.log('🎩 [NATAN BUTLER] Resetting butler state');
        this.debug('Resetting butler state');

        localStorage.removeItem('natan_has_greeted');
        localStorage.removeItem('natan_assistant_opened');

        this.hasGreeted = false;

        //console.log('🎩 [NATAN BUTLER] Butler state reset. Reload page to see modal automatically.');

        // Rimostra il maggiordomo dopo 2 secondi
        setTimeout(async () => {
            this.createButlerModal();
            this.showButlerWelcome();
        }, 2000);
    }

    /**
     * Funzione combinata per test completo e debug
     */
    public testButler(): void {
        //console.log('🎩 [NATAN BUTLER] === COMPLETE BUTLER TEST ===');
        //console.log('Current state:', {
        //     hasGreeted: this.hasGreeted,
        //     butlerModal: !!this.butlerModal,
        //     localStorage_greeted: localStorage.getItem('natan_has_greeted')
        // });

        // Reset stato e forza visualizzazione
        this.resetButler();
    }

    /**
     * Mostra il maggiordomo anche se già salutato (per test)
     */
    public async showButlerManually(): Promise<void> {
        //console.log('🎩 [NATAN BUTLER] showButlerManually called');
        this.debug('Showing butler manually');

        // Se il modal non esiste, crealo
        if (!this.butlerModal) {
            this.createButlerModal();
        }

        // Forza lo stato per mostrare il modal
        this.hasGreeted = false;

        this.showButlerWelcome();
    }

    /**
     * Forza la creazione e visualizzazione immediata del modal (SUPER DEBUG)
     */
    public async forceShowModal(): Promise<void> {
        //console.log('🎩 [NATAN BUTLER] FORCE SHOW MODAL - Creating modal from scratch');

        // Rimuovi qualsiasi modal esistente
        const existing = document.getElementById('natan-butler-modal');
        if (existing) {
            existing.remove();
        }

        this.butlerModal = null;
        this.hasGreeted = false;
        this.createButlerModal();
        setTimeout(() => {
            this.showButlerWelcome();
        }, 100);
    }
}

// Funzione per generare dinamicamente i bottoni delle opzioni nella modale
function renderAssistantOptions() {
    //console.log('🎯 [RENDER OPTIONS] renderAssistantOptions called - Screen width:', window.innerWidth);

    // Cerca entrambi i menu (desktop e mobile)
    const desktopMenu = document.getElementById('natan-assistant-menu-desktop');
    const mobileMenu = document.getElementById('natan-assistant-menu-mobile');
    const oldMenu = document.getElementById('natan-assistant-menu'); // fallback

    //console.log('🎯 [RENDER OPTIONS] Menu search results:', {
    //     desktopMenu: !!desktopMenu,
    //     mobileMenu: !!mobileMenu,
    //     oldMenu: !!oldMenu,
    //     screenWidth: window.innerWidth
    // });

    const allMenus = [desktopMenu, mobileMenu, oldMenu].filter(menu => menu !== null);

    if (allMenus.length === 0) {
        //console.log('🎯 [RENDER OPTIONS] No menu elements found');
        return;
    }

    console.log(`🎯 [RENDER OPTIONS] Found ${allMenus.length} menu(s), processing all`);

    // Carica la configurazione
    let config;
    try {
        config = getAppConfig();
        //console.log('🎯 [RENDER OPTIONS] Config loaded successfully');
    } catch (error) {
        console.warn('🎯 [RENDER OPTIONS] App config not loaded yet, cannot render assistant options', error);
        return;
    }

    // Renderizza per tutti i menu trovati
    allMenus.forEach((menu, menuIndex) => {
        if (!menu) return;

        const menuType = menu.id.includes('desktop') ? 'desktop' : menu.id.includes('mobile') ? 'mobile' : 'legacy';
        console.log(`🎯 [RENDER OPTIONS] Processing menu ${menuIndex + 1}: ${menu.id} (${menuType})`);

        menu.innerHTML = '';

        console.log(`🎯 [RENDER OPTIONS] Processing ${assistantOptions.length} options for ${menuType} menu`);
        assistantOptions.forEach((option, index) => {
            console.log(`🎯 [RENDER OPTIONS] Creating button ${index + 1} for ${menuType}:`, option.key);
            const btn = document.createElement('button');
            btn.className = 'flex items-center justify-end gap-2 py-2 pl-5 pr-3 text-sm font-medium transition-all duration-300 bg-gray-900 border rounded-full shadow-md border-emerald-600/30 text-emerald-300 hover:bg-gray-800 hover:border-emerald-500/50 natan-item';

            // Usa appTranslate con la configurazione caricata
            try {
                btn.textContent = appTranslate(option.label, config.translations);
                console.log(`🎯 [RENDER OPTIONS] Translation successful for ${menuType}`, option.label, '→', btn.textContent);
            } catch (translateError) {
                console.error(`🎯 [RENDER OPTIONS] Translation failed for ${menuType}`, option.label, ':', translateError);
                btn.textContent = option.label; // Fallback
            }

            btn.onclick = option.action;
            btn.setAttribute('data-key', option.key);
            menu.appendChild(btn);
            console.log(`🎯 [RENDER OPTIONS] Button appended to ${menuType} menu:`, option.key);
        });
        console.log(`🎯 [RENDER OPTIONS] Completed ${menuType} menu - total buttons:`, menu.children.length);
    });

    //console.log('🎯 [RENDER OPTIONS] All menus processed successfully');
}

// Chiamata al render dopo il caricamento della pagina
window.addEventListener('DOMContentLoaded', renderAssistantOptions);
