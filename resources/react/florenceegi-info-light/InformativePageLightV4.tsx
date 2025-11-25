import React from 'react';
import { AnimationProvider } from '../florenceegi-info/AnimationContext';
import { AudioProvider, AudioControls } from '../shared/audio';
import { FLORENCEEGI_AUDIO_CONFIG } from '../florenceegi-info/florenceEgiAudioConfig';
import AnimationToggle from '../florenceegi-info/AnimationToggle';

// ═══════════════════════════════════════════════════════
// V4 SECTIONS - Nuovi contenuti approvati
// ═══════════════════════════════════════════════════════
import HeroV4 from './sections/HeroV4';
import EgizzareV4 from './sections/EgizzareV4';
import WhatIsEGIV4 from './sections/WhatIsEGIV4';
import TransparencyV4 from './sections/TransparencyV4';
import BlockchainSimpleV4 from './sections/BlockchainSimpleV4';
import ProblemsV4 from './sections/ProblemsV4';
import InvoicesV4 from './sections/InvoicesV4';
import WhoCanUseV4 from './sections/WhoCanUseV4';
import CTAFinalV4 from './sections/CTAFinalV4';

import './InformativePageLight.css';

/**
 * FlorenceEGI - Pagina Informativa V4
 *
 * NUOVA VERSIONE con contenuti riscritti per il target:
 * "Casalinga di 55 anni che capisce cosa fa la piattaforma
 *  MA ANCHE vede il vero potenziale: attività gratuita, blockchain, AI"
 *
 * Struttura V4:
 * 1. Hero - Frase madre + motto "Se Esiste, EGIZZALO"
 * 2. Egizzare - Definizione del verbo + esempi pratici
 * 3. What is EGI - Ecological Goods Invent (E/G/I breakdown)
 * 4. Transparency - Percentuali chiare (68%/20%/10%/2%)
 * 5. Blockchain Simple - "Non te ne accorgi" + modalità esperto
 * 6. Problems - 8 problemi reali risolti
 * 7. Invoices - Documentazione fiscale
 * 8. CTA - Call to action finale
 *
 * NOTE: Altri blocchi da aggiungere dopo approvazione visual:
 * - Chi può usare FlorenceEGI
 * - Come funziona (3 step)
 * - Egili (moneta interna)
 * - Chi c'è dietro
 * - FAQ
 */
export default function InformativePageLightV4() {
    return (
        <AnimationProvider>
            <AudioProvider config={FLORENCEEGI_AUDIO_CONFIG}>
                <div className="informative-page-light informative-page-light--v4">
                    {/* Controlli Animazione (destra) */}
                    <AnimationToggle />

                    {/* Controlli Audio (sinistra) */}
                    <AudioControls theme="glass" />

                    {/* ═══════════════════════════════════════════════════════
                        BLOCCO 1: HERO - Apertura Potente
                        "FlorenceEGI è la piattaforma dove chiunque può crearsi
                         la propria attività digitale..."
                    ═══════════════════════════════════════════════════════ */}
                    <HeroV4 />

                    {/* ═══════════════════════════════════════════════════════
                        BLOCCO 2: EGIZZARE - Il Verbo
                        "Egizzare significa trasformare qualcosa in un EGI"
                    ═══════════════════════════════════════════════════════ */}
                    <EgizzareV4 />

                    {/* ═══════════════════════════════════════════════════════
                        BLOCCO 3: WHAT IS EGI
                        "Ecological Goods Invent"
                    ═══════════════════════════════════════════════════════ */}
                    <WhatIsEGIV4 />

                    {/* ═══════════════════════════════════════════════════════
                        BLOCCO 4: TRASPARENZA
                        68% Creator / 20% EPP / 10% Natan / 2% Frangette
                    ═══════════════════════════════════════════════════════ */}
                    <TransparencyV4 />

                    {/* ═══════════════════════════════════════════════════════
                        BLOCCO 5: BLOCKCHAIN SEMPLICE
                        "Non ti accorgerai nemmeno che sotto c'è la blockchain"
                    ═══════════════════════════════════════════════════════ */}
                    <BlockchainSimpleV4 />

                    {/* ═══════════════════════════════════════════════════════
                        BLOCCO 6: 8 PROBLEMI RISOLTI
                        Royalties, Copyright, Pagamenti sicuri, Marketing, etc.
                    ═══════════════════════════════════════════════════════ */}
                    <ProblemsV4 />

                    {/* ═══════════════════════════════════════════════════════
                        BLOCCO 7: FATTURE E FISCO
                        "Documentazione fiscale disponibile"
                    ═══════════════════════════════════════════════════════ */}
                    <InvoicesV4 />

                    {/* ═══════════════════════════════════════════════════════
                        BLOCCO 8: CHI PUÒ USARE
                        Creator, Collector, EPP
                    ═══════════════════════════════════════════════════════ */}
                    <WhoCanUseV4 />

                    {/* ═══════════════════════════════════════════════════════
                        BLOCCO 9: CTA FINALE
                        Narrativo con esempi concreti + valore legale blockchain
                    ═══════════════════════════════════════════════════════ */}
                    <CTAFinalV4 />

                    {/* ═══════════════════════════════════════════════════════
                        TODO: Blocchi da aggiungere
                        - Come funziona in 3 passi
                        - Egili: la moneta interna
                        - Chi c'è dietro FlorenceEGI
                        - FAQ
                    ═══════════════════════════════════════════════════════ */}
                </div>
            </AudioProvider>
        </AnimationProvider>
    );
}
