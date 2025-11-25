import React from 'react';
import { AnimationProvider } from '../florenceegi-info/AnimationContext';
import { AudioProvider, AudioControls } from '../shared/audio';
import { FLORENCEEGI_AUDIO_CONFIG } from '../florenceegi-info/florenceEgiAudioConfig';
import AnimationToggle from '../florenceegi-info/AnimationToggle';

// Sezioni sempre visibili
import HeroSectionLight from './sections/HeroSectionLight';
import HowItWorksLight from './sections/HowItWorksLight';
import ExamplesLight from './sections/ExamplesLight';
import PaymentMethodsLight from './sections/PaymentMethodsLight';
import EcosystemLight from './sections/EcosystemLight';
import CTAFinalLight from './sections/CTAFinalLight';

// Sezioni collapsibili
import ProblemsCollapsible from './sections/ProblemsCollapsible';
import TechStackCollapsible from './sections/TechStackCollapsible';
import ComplianceGovernanceCollapsible from './sections/ComplianceGovernanceCollapsible';
import PricingCollapsible from './sections/PricingCollapsible';

import './InformativePageLight.css';

/**
 * FlorenceEGI - Pagina Informativa Light
 * 
 * Versione compatta della pagina informativa completa.
 * 
 * SEMPRE VISIBILI:
 * - Hero + payoff "Se Esiste, Egizzalo…"
 * - 3 click / 0 complicazioni
 * - "Qualsiasi cosa esista può diventare un EGI"
 * - 4 modi di pagare
 * - Ecosistema virtuoso
 * - CTA finale "Sei un creator / collector / azienda?"
 * 
 * COLLAPSIBILI:
 * - Cosa risolviamo davvero (12 problemi) - primi 4-6 visibili
 * - Stack Enterprise / 5 Engine - dettagli in collapse
 * - Compliance / Governance - 3 bullet visibili, resto in collapse
 * - Pricing - fee 10% visibile, dettagli in collapse
 */
export default function InformativePageLight() {
    return (
        <AnimationProvider>
            <AudioProvider config={FLORENCEEGI_AUDIO_CONFIG}>
                <div className="informative-page-light">
                    {/* Controlli Animazione (destra) */}
                    <AnimationToggle />

                    {/* Controlli Audio (sinistra) */}
                    <AudioControls theme="glass" />

                    {/* ═══════════════════════════════════════════════════════
                        SEZIONI SEMPRE VISIBILI
                    ═══════════════════════════════════════════════════════ */}

                    {/* 1. Hero con payoff */}
                    <HeroSectionLight />

                    {/* 2. 3 Click, 0 Complicazioni */}
                    <HowItWorksLight />

                    {/* 3. Qualsiasi cosa può diventare un EGI */}
                    <ExamplesLight />

                    {/* ═══════════════════════════════════════════════════════
                        SEZIONI COLLAPSIBILI
                    ═══════════════════════════════════════════════════════ */}

                    {/* 4. Cosa risolviamo davvero (primi 4-6 visibili) */}
                    <ProblemsCollapsible />

                    {/* 5. Stack Enterprise / 5 Engine */}
                    <TechStackCollapsible />

                    {/* 6. Compliance + Governance */}
                    <ComplianceGovernanceCollapsible />

                    {/* 7. Pricing trasparente */}
                    <PricingCollapsible />

                    {/* ═══════════════════════════════════════════════════════
                        SEZIONI SEMPRE VISIBILI (BOTTOM)
                    ═══════════════════════════════════════════════════════ */}

                    {/* 8. 4 Modi di Pagare */}
                    <PaymentMethodsLight />

                    {/* 9. Ecosistema Virtuoso */}
                    <EcosystemLight />

                    {/* 10. CTA Finale */}
                    <CTAFinalLight />
                </div>
            </AudioProvider>
        </AnimationProvider>
    );
}
