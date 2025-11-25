import React from 'react';
import HeroSection from './HeroSection';
import Section2Problems from './Section2Problems';
import Section3Examples from './Section3Examples';
import Section4HowItWorks from './Section4HowItWorks';
import Section5AMMk from './Section5AMMk';
import './InformativePage.css';

export default function InformativePage() {
  return (
    <div className="informative-page">
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

      {/* Placeholder per sezioni future */}
      <section className="section-placeholder">
        <div className="container">
          <h2>🚧 Sezioni 6-17 in arrivo...</h2>
          <p>
            Le prossime sezioni includeranno: Blockchain Spiegata Semplice,
            Tokenomics, Storie di Successo, e molto altro.
          </p>
        </div>
      </section>
    </div>
  );
}
