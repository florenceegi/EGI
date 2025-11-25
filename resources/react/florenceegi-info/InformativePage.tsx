import React from 'react';
import { AnimationProvider } from './AnimationContext';
// Audio: usa il sistema condiviso
import { AudioProvider, AudioControls } from '../shared/audio';
import { FLORENCEEGI_AUDIO_CONFIG } from './florenceEgiAudioConfig';
import AnimationToggle from './AnimationToggle';
import HeroSection from './HeroSection';
import Section2Problems from './Section2Problems';
import Section3Examples from './Section3Examples';
import Section4HowItWorks from './Section4HowItWorks';
import Section5AMMk from './Section5AMMk';
import Section6Technology from './Section6Technology';
import Section7PaymentMethods from './Section7PaymentMethods';
import Section8Compliance from './Section8Compliance';
import Section9Ecosystem from './Section9Ecosystem';
import Section10NatanAI from './Section10NatanAI';
import Section11Governance from './Section11Governance';
import Section12Pricing from './Section12Pricing';
import Section13UseCases from './Section13UseCases';
import Section14Roadmap from './Section14Roadmap';
import Section15FAQ from './Section15FAQ';
import Section16CTA from './Section16CTA';
import Section17Footer from './Section17Footer';
import './InformativePage.css';

export default function InformativePage() {
  return (
    <AnimationProvider>
      <AudioProvider config={FLORENCEEGI_AUDIO_CONFIG}>
        <div className="informative-page">
          {/* Controlli Animazione (destra) */}
          <AnimationToggle />

          {/* Controlli Audio (sinistra) - Sistema condiviso */}
          <AudioControls theme="glass" />

          {/* Section 1: Hero con animazione 3D + testo */}
          <HeroSection />

          {/* Section 2: 12 Problemi risolti */}
          <Section2Problems />

          {/* Section 3: Esempi cosa puoi EGIZZARE */}
          <Section3Examples />

          {/* Section 4: Come Funziona (3 Step) */}
          <Section4HowItWorks />

          {/* Section 5: Asset Market Maker (AMMk) - IL TUO SUPERPOTERE */}
          <Section5AMMk />

          {/* Section 6: Technology - Stack Enterprise, Esperienza Consumer */}
          <Section6Technology />

          {/* Section 7: 4 Modi di Pagare */}
          <Section7PaymentMethods />

          {/* Section 8: Compliance Totale */}
          <Section8Compliance />

          {/* Section 9: Ecosistema Virtuoso */}
          <Section9Ecosystem />

          {/* Section 10: Natan AI - Il tuo assistente */}
          <Section10NatanAI />

          {/* Section 11: Governance - La struttura che protegge */}
          <Section11Governance />

          {/* Section 12: Pricing - Fee trasparenti */}
          <Section12Pricing />

          {/* Section 13: Use Cases - Storie di successo */}
          <Section13UseCases />

          {/* Section 14: Roadmap 2026 */}
          <Section14Roadmap />

          {/* Section 15: FAQ - Domande frequenti */}
          <Section15FAQ />

          {/* Section 16: CTA Finale */}
          <Section16CTA />

          {/* Section 17: Footer */}
          <Section17Footer />
        </div>
      </AudioProvider>
    </AnimationProvider>
  );
}
