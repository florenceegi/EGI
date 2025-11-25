import React from 'react';
import './Section14Roadmap.css';

const getTranslation = (key: string, fallback: string): string => {
  const translations = (window as any).florenceEgiTranslations || {};
  return translations[key] || fallback;
};

export default function Section14Roadmap() {
  return (
    <section className="section-14-roadmap">
      <div className="container">
        <div className="section-header">
          <span className="section-number">14</span>
          <h2>{getTranslation('section14_title', 'Roadmap')}</h2>
          <p className="section-subtitle">
            {getTranslation('section14_subtitle', 'Il nostro percorso di sviluppo')}
          </p>
        </div>

        <div className="coming-soon-box">
          <div className="coming-soon-icon">🚀</div>
          <h3>{getTranslation('roadmap_coming_soon', 'Prossimamente')}</h3>
          <p className="coming-soon-text">
            {getTranslation('roadmap_description', 
              'Stiamo lavorando alla roadmap dettagliata di FlorenceEGI. Annunceremo le prossime funzionalità e milestone non appena saranno definite.'
            )}
          </p>
          <div className="current-focus">
            <span className="focus-label">{getTranslation('roadmap_focus_label', 'Focus attuale:')}</span>
            <span className="focus-text">{getTranslation('roadmap_focus_text', 'Lancio piattaforma e onboarding primi creator')}</span>
          </div>
        </div>

        <div className="stay-updated">
          <p>
            {getTranslation('roadmap_stay_updated', 
              'Vuoi essere tra i primi a conoscere le novità? Iscriviti alla nostra newsletter o seguici sui social.'
            )}
          </p>
        </div>
      </div>
    </section>
  );
}
