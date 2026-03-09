/**
 * @package Resources\Ts\Components
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - Extended Assistant Actions)
 * @date 2025-07-07
 * @purpose Azioni estese per assistente Natan con supporto tooltip, modal e tour guidati
 */

import { getTranslation } from '../../js/utils/translations';

export class AssistantActions {

    // === AZIONI LEGACY (mantenute per compatibilità) ===
    static handleCreateEgiContextual() {
        // Usa NatanBatchMint se disponibile (sidebar-first flow)
        if ((window as any).natanBatchMint) {
            (window as any).natanBatchMint.open();
            return;
        }
        // Fallback: click sul pulsante contestuale se presente nel DOM
        const btn = document.querySelector('.js-create-egi-contextual-button') as HTMLButtonElement | null;
        if (btn) {
            btn.click();
        } else {
            alert(getTranslation('assistant.create_egi_contextual'));
        }
    }

    static handleCreateArtwork() {
        alert(getTranslation('assistant.create_artwork'));
    }

    static handleBuyArtwork() {
        alert(getTranslation('assistant.buy_artwork'));
    }

    static handleWhatIsEGI() {
        alert(getTranslation('assistant.what_is_egi'));
    }

    static handleGuidedTour() {
        alert(getTranslation('assistant.guided_tour'));
    }

    static handleCustomEGI() {
        AssistantActions.goToUnderConstruction('custom_egi');
    }

    static handleWhitePaper() {
        alert(getTranslation('assistant.white_paper'));
    }

    static handleLetMeGuide() {
        alert(getTranslation('assistant.let_me_guide'));
    }

    // === NUOVE AZIONI PER ACCORDION ===

    /**
     * Crea un tooltip esplicativo posizionato sull'elemento target
     * @param target - Elemento HTML su cui posizionare il tooltip
     * @param message - Messaggio da mostrare
     * @param duration - Durata in millisecondi (default: 4000)
     */
    static createExplanationTooltip(target: HTMLElement, message: string, duration: number = 4000): void {
        // Rimuovi eventuali tooltip esistenti
        const existingTooltip = document.getElementById('natan-explanation-tooltip');
        if (existingTooltip) {
            existingTooltip.remove();
        }

        // Crea il tooltip
        const tooltip = document.createElement('div');
        tooltip.id = 'natan-explanation-tooltip';
        tooltip.className = 'fixed z-[10000] max-w-sm p-4 text-sm font-medium text-white bg-gray-900 border border-emerald-600/30 rounded-lg shadow-lg pointer-events-none';
        tooltip.innerHTML = `
            <div class="flex items-start gap-3">
                <span class="text-emerald-400 text-lg">🎩</span>
                <div>
                    <div class="font-semibold text-emerald-300 mb-1">Natan ti spiega:</div>
                    <div class="text-gray-200">${message}</div>
                </div>
            </div>
            <div class="absolute w-3 h-3 bg-gray-900 border-l border-t border-emerald-600/30 transform rotate-45 -bottom-1.5 left-6"></div>
        `;

        document.body.appendChild(tooltip);

        // Posiziona il tooltip sopra l'elemento target
        const targetRect = target.getBoundingClientRect();
        const tooltipRect = tooltip.getBoundingClientRect();

        const top = targetRect.top + window.scrollY - tooltipRect.height - 12;
        const left = Math.max(16, Math.min(
            targetRect.left + (targetRect.width / 2) - (tooltipRect.width / 2),
            window.innerWidth - tooltipRect.width - 16
        ));

        tooltip.style.top = `${top}px`;
        tooltip.style.left = `${left}px`;

        // Anima l'entrata
        tooltip.style.opacity = '0';
        tooltip.style.transform = 'translateY(10px)';
        tooltip.style.transition = 'all 0.3s ease-out';

        setTimeout(() => {
            tooltip.style.opacity = '1';
            tooltip.style.transform = 'translateY(0)';
        }, 50);

        // Evidenzia l'elemento target
        target.classList.add('natan-spotlight-element');

        // Rimuovi dopo la durata specificata
        setTimeout(() => {
            tooltip.style.opacity = '0';
            tooltip.style.transform = 'translateY(-10px)';
            target.classList.remove('natan-spotlight-element');

            setTimeout(() => {
                tooltip.remove();
            }, 300);
        }, duration);
    }

    /**
     * Mostra modal con la storia di Natan
     */
    static showNatanStoryModal(): void {
        // Rimuovi modal esistente se presente
        const existingModal = document.getElementById('natan-story-modal');
        if (existingModal) {
            existingModal.remove();
        }

        // Crea il modal
        const modal = document.createElement('div');
        modal.id = 'natan-story-modal';
        modal.className = 'fixed inset-0 z-[10000] flex items-center justify-center bg-black bg-opacity-50';

        modal.innerHTML = `
            <div class="bg-gray-900 border border-emerald-600/30 rounded-2xl p-8 max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center">
                        <span class="text-3xl">🎩</span>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">Chi è Natan?</h2>
                    <p class="text-emerald-300">Il maggiordomo digitale di FlorenceEGI</p>
                </div>

                <div class="prose prose-emerald prose-invert max-w-none space-y-4 text-gray-200">
                    <p>
                        <strong class="text-emerald-300">Natan</strong> non è solo un assistente digitale. È l'incarnazione digitale
                        dell'ospitalità fiorentina, progettato per accogliere ogni visitatore nel mondo di FlorenceEGI con la stessa
                        eleganza e competenza di un maggiordomo rinascimentale.
                    </p>

                    <p>
                        Il suo nome deriva da <em>Nathan</em>, che significa "dono" in ebraico, perché Natan è letteralmente
                        il dono di FlorenceEGI a chiunque voglia scoprire il <strong class="text-emerald-300">Nuovo Rinascimento Ecologico Digitale</strong>.
                    </p>

                    <p>
                        Natan conosce ogni angolo della piattaforma, ogni meccanica del mercato virtuoso, ogni storia dei nostri creator e mecenati.
                        Non si limita a rispondere alle domande: <strong class="text-emerald-300">anticipa i bisogni</strong> e guida
                        verso le scoperte più significative.
                    </p>

                    <p class="text-center">
                        <em class="text-emerald-400">"Il mio compito non è vendere, ma rivelare.
                        Non spiegare, ma far vivere l'esperienza del Rinascimento."</em>
                    </p>
                </div>

                <div class="mt-8 text-center">
                    <button onclick="document.getElementById('natan-story-modal').remove()"
                            class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">
                        Grazie, Natan! 🎩
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Anima l'entrata
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.style.opacity = '1';
        }, 50);

        // Chiudi con Escape
        const handleEscape = (e: KeyboardEvent) => {
            if (e.key === 'Escape') {
                modal.remove();
                document.removeEventListener('keydown', handleEscape);
            }
        };
        document.addEventListener('keydown', handleEscape);

        // Chiudi cliccando fuori
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    /**
     * Avvia tour guidato interattivo della piattaforma
     */
    static startGuidedTour(): void {
        // Per ora, implementazione semplificata che evidenzia le sezioni principali
        const tourSteps = [
            {
                selector: '#hero-section',
                title: 'Benvenuto in FlorenceEGI',
                message: 'Qui inizia il Nuovo Rinascimento Ecologico Digitale'
            },
            {
                selector: '.nft-stats-section',
                title: 'Impatto in Tempo Reale',
                message: 'Ogni transazione genera valore per progetti ambientali reali'
            },
            {
                selector: '.collections-section',
                title: 'Le Collezioni EGI',
                message: 'Scopri le opere che uniscono arte e sostenibilità'
            }
        ];

        let currentStep = 0;

        const showTourStep = (stepIndex: number) => {
            if (stepIndex >= tourSteps.length) {
                alert('Tour completato! Ora conosci FlorenceEGI 🎉');
                return;
            }

            const step = tourSteps[stepIndex];
            const element = document.querySelector(step.selector) as HTMLElement;

            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });
                setTimeout(() => {
                    this.createExplanationTooltip(
                        element,
                        `${step.title}: ${step.message}`,
                        3000
                    );
                }, 1000);

                setTimeout(() => showTourStep(stepIndex + 1), 4000);
            } else {
                showTourStep(stepIndex + 1);
            }
        };

        showTourStep(0);
    }

    /**
     * Apre assistenza personalizzata (placeholder per futura implementazione)
     */
    static openPersonalAssistance(): void {
        const message = `
            Ciao! Sono Natan, il tuo maggiordomo personale.

            Per ora puoi esplorarmi attraverso le categorie che ti ho mostrato,
            ma presto avrò una chat interattiva dove potrai farmi qualsiasi domanda
            su FlorenceEGI, gli EGI, i mecenati e tutto il Rinascimento Ecologico Digitale.

            Nel frattempo, dimmi: cosa ti incuriosisce di più?
            Posso guidarti verso la sezione giusta! 🎩
        `;

        alert(message);
    }

    /**
     * Utility per aggiungere stili CSS dinamici se non esistono
     */
    static ensureTooltipStyles(): void {
        if (!document.getElementById('natan-tooltip-styles')) {
            const style = document.createElement('style');
            style.id = 'natan-tooltip-styles';
            style.textContent = `
                .natan-spotlight-element {
                    position: relative;
                    z-index: 1000;
                    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.3), 0 0 20px rgba(16, 185, 129, 0.2);
                    border-radius: 8px;
                    transition: all 0.3s ease;
                }

                #natan-explanation-tooltip {
                    backdrop-filter: blur(8px);
                    background: rgba(17, 24, 39, 0.95) !important;
                }

                @keyframes natan-tooltip-pulse {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.02); }
                }

                #natan-explanation-tooltip:hover {
                    animation: natan-tooltip-pulse 2s ease-in-out infinite;
                }
            `;
            document.head.appendChild(style);
        }
    }

    static goToUnderConstruction(key: string) {
        window.location.href = `/under-construction/${key}`;
    }
}

// Assicura che gli stili siano disponibili quando la classe viene importata
document.addEventListener('DOMContentLoaded', () => {
    AssistantActions.ensureTooltipStyles();
});
