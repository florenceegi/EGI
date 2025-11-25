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

          {/* Placeholder per sezioni future */}
          <section className="section-placeholder">
            <div className="container">
              <h2>🚧 Sezioni 7-17 in arrivo...</h2>
              <p>
                Le prossime sezioni includeranno: 4 Modi di Pagare,
                Compliance Totale, Ecosistema Virtuoso, e molto altro.
              </p>
            </div>
          </section>
        </div>
      </AudioProvider>
    </AnimationProvider>
  );
}
