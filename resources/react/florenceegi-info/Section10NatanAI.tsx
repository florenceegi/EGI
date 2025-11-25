import React from 'react';
import './Section10NatanAI.css';

const getTranslation = (key: string, fallback: string): string => {
  const translations = (window as any).florenceEgiTranslations || {};
  return translations[key] || fallback;
};

interface NatanFeature {
  icon: string;
  title: string;
  description: string;
}

export default function Section10NatanAI() {
  const creatorFeatures: NatanFeature[] = [
    {
      icon: '📊',
      title: getTranslation('natan_creator_1_title', 'Analisi Pricing'),
      description: getTranslation('natan_creator_1_desc', 'Analizza collezioni simili e suggerisce prezzi ottimali'),
    },
    {
      icon: '🏷️',
      title: getTranslation('natan_creator_2_title', 'Descrizioni SEO'),
      description: getTranslation('natan_creator_2_desc', 'Genera descrizioni multilingua ottimizzate per la ricerca'),
    },
    {
      icon: '📈',
      title: getTranslation('natan_creator_3_title', 'Trend Real-time'),
      description: getTranslation('natan_creator_3_desc', 'Identifica tendenze di mercato e settori emergenti'),
    },
    {
      icon: '📅',
      title: getTranslation('natan_creator_4_title', 'Timing Ottimale'),
      description: getTranslation('natan_creator_4_desc', 'Suggerisce i momenti migliori per il lancio'),
    },
  ];

  const collectorFeatures: NatanFeature[] = [
    {
      icon: '🤖',
      title: getTranslation('natan_collector_1_title', 'Gusto Personale'),
      description: getTranslation('natan_collector_1_desc', 'Apprende le tue preferenze dalle opere che salvi'),
    },
    {
      icon: '💡',
      title: getTranslation('natan_collector_2_title', 'Suggerimenti'),
      description: getTranslation('natan_collector_2_desc', 'Propone opere coerenti con il tuo stile'),
    },
    {
      icon: '🌟',
      title: getTranslation('natan_collector_3_title', 'Artisti Emergenti'),
      description: getTranslation('natan_collector_3_desc', 'Identifica talenti in crescita prima degli altri'),
    },
    {
      icon: '⏰',
      title: getTranslation('natan_collector_4_title', 'Alert Prezzo'),
      description: getTranslation('natan_collector_4_desc', 'Notifica momenti ideali per acquisto o scambio'),
    },
  ];

  const ethicsFeatures: string[] = [
    getTranslation('natan_ethics_1', 'Ogni suggerimento è SPIEGATO (no black box)'),
    getTranslation('natan_ethics_2', 'Regola Zero: mai dedurre senza dati reali'),
    getTranslation('natan_ethics_3', 'GDPR-compliant: consenso granulare e export dati'),
    getTranslation('natan_ethics_4', 'Audit trail: ogni interazione tracciata'),
  ];

  return (
    <section className="section-10-natan">
      <div className="container">
        <div className="section-header">
          <span className="section-number">10</span>
          <h2>{getTranslation('section10_title', 'NATAN AI')}</h2>
          <p className="section-subtitle">
            {getTranslation('section10_subtitle', "L'AI che espande (non sostituisce) il tuo talento")}
          </p>
        </div>

        {/* NATAN Description */}
        <div className="natan-intro">
          <div className="natan-logo">
            <span>🧠</span>
          </div>
          <p>
            {getTranslation('natan_intro', 
              'NATAN (Neural Assistant for Technical Art Navigation) è l\'intelligenza artificiale integrata che ti aiuta senza mai sostituirti.'
            )}
          </p>
        </div>

        {/* Features Grid */}
        <div className="natan-features">
          {/* For Creators */}
          <div className="feature-column">
            <div className="column-header">
              <span className="column-icon">🎨</span>
              <h3>{getTranslation('natan_for_creators', 'Per Creator')}</h3>
            </div>
            <div className="feature-cards">
              {creatorFeatures.map((feature, index) => (
                <div key={index} className="feature-card">
                  <span className="card-icon">{feature.icon}</span>
                  <div className="card-content">
                    <h4>{feature.title}</h4>
                    <p>{feature.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* For Collectors */}
          <div className="feature-column">
            <div className="column-header">
              <span className="column-icon">💎</span>
              <h3>{getTranslation('natan_for_collectors', 'Per Collector')}</h3>
            </div>
            <div className="feature-cards">
              {collectorFeatures.map((feature, index) => (
                <div key={index} className="feature-card">
                  <span className="card-icon">{feature.icon}</span>
                  <div className="card-content">
                    <h4>{feature.title}</h4>
                    <p>{feature.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Ethics Box */}
        <div className="ethics-box">
          <div className="ethics-header">
            <span className="ethics-icon">🛡️</span>
            <h3>{getTranslation('natan_ethics_title', 'Etica & Trasparenza (Oracode OS3.0)')}</h3>
          </div>
          <ul className="ethics-list">
            {ethicsFeatures.map((feature, index) => (
              <li key={index}>
                <span className="check">✓</span>
                {feature}
              </li>
            ))}
          </ul>
        </div>
      </div>
    </section>
  );
}
