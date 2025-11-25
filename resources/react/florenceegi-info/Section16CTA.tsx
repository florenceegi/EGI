import React from 'react';
import './Section16CTA.css';

const getTranslation = (key: string, fallback: string): string => {
  const translations = (window as any).florenceEgiTranslations || {};
  return translations[key] || fallback;
};

export default function Section16CTA() {
  return (
    <section className="section-16-cta">
      <div className="container">
        <div className="cta-content">
          <h2>{getTranslation('section16_title', 'Il Rinascimento Digitale Inizia con Te')}</h2>
          
          <p className="cta-intro">
            {getTranslation('cta_intro', 'Unisciti ai primi creator e collector che stanno costruendo il futuro dell\'arte digitale sostenibile.')}
          </p>

          <p className="cta-question">{getTranslation('cta_question', 'Cosa aspetti?')}</p>
        </div>

        <div className="cta-cards">
          {/* Creator Card */}
          <div className="cta-card creator-card">
            <span className="card-icon">🎨</span>
            <h3>{getTranslation('cta_creator_title', 'Sei un Creator?')}</h3>
            <ul className="card-steps">
              <li>{getTranslation('cta_creator_1', 'Crea Account Gratuito')}</li>
              <li>{getTranslation('cta_creator_2', 'Carica Prima Opera (5 min)')}</li>
              <li>{getTranslation('cta_creator_3', 'EGIZZA e Inizia a Guadagnare')}</li>
            </ul>
            <a href="/register" className="cta-button creator-button">
              {getTranslation('cta_creator_btn', 'Inizia Gratis')} →
            </a>
          </div>

          {/* Collector Card */}
          <div className="cta-card collector-card">
            <span className="card-icon">💎</span>
            <h3>{getTranslation('cta_collector_title', 'Sei un Collector?')}</h3>
            <ul className="card-steps">
              <li>{getTranslation('cta_collector_1', 'Esplora Marketplace')}</li>
              <li>{getTranslation('cta_collector_2', 'Trova la Tua Opera')}</li>
              <li>{getTranslation('cta_collector_3', 'Compra con Carta (o Crypto)')}</li>
            </ul>
            <a href="/marketplace" className="cta-button collector-button">
              {getTranslation('cta_collector_btn', 'Esplora Opere')} →
            </a>
          </div>

          {/* Business Card */}
          <div className="cta-card business-card">
            <span className="card-icon">🏢</span>
            <h3>{getTranslation('cta_business_title', "Sei un'Azienda?")}</h3>
            <ul className="card-steps">
              <li>{getTranslation('cta_business_1', 'Richiedi Demo AMMk')}</li>
              <li>{getTranslation('cta_business_2', 'Crea il Tuo Marketplace')}</li>
              <li>{getTranslation('cta_business_3', 'Scala con Tecnologia Enterprise')}</li>
            </ul>
            <a href="/contact" className="cta-button business-button">
              {getTranslation('cta_business_btn', 'Richiedi Demo')} →
            </a>
          </div>
        </div>
      </div>
    </section>
  );
}
