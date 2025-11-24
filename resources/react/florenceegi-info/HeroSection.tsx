import React from 'react';
import HeroAnimation3D from './HeroAnimation3D';
import './HeroSection.css';

export default function HeroSection() {
  const t = window.florenceEgiTranslations?.hero;

  if (!t) {
    return <div>Loading...</div>;
  }

  return (
    <section className="hero-section">
      {/* Background: Animazione 3D esistente */}
      <div className="hero-animation-background">
        <HeroAnimation3D />
      </div>

      {/* Overlay: Testo con glassmorphism */}
      <div className="hero-content-overlay">
        <div className="hero-text-container">
          <h1 className="hero-platform-name">
            {t.platform_name}
          </h1>

          <p className="hero-platform-tagline">
            {t.platform_tagline}
          </p>

          <h2
            className="hero-headline"
            dangerouslySetInnerHTML={{ __html: t.headline_html }}
          />

          {(t.what_we_do || t.how_simple) && (
            <div className="hero-explanation">
              {t.what_we_do && (
                <div className="explanation-item">
                  <span className="explanation-icon">🎯</span>
                  <p className="explanation-text">{t.what_we_do}</p>
                </div>
              )}
              {t.how_simple && (
                <div className="explanation-item">
                  <span className="explanation-icon">⚡</span>
                  <p className="explanation-text">{t.how_simple}</p>
                </div>
              )}
            </div>
          )}

          <div className="hero-cta-buttons">
            <button className="btn-primary">
              {t.cta_primary} →
            </button>
            <button className="btn-secondary">
              {t.cta_secondary}
            </button>
          </div>

          <div className="hero-features">
            <div className="feature-badge">
              <span className="icon">✅</span>
              <span className="text">Arte & Design</span>
            </div>
            <div className="feature-badge">
              <span className="icon">✅</span>
              <span className="text">Esperienze</span>
            </div>
            <div className="feature-badge">
              <span className="icon">✅</span>
              <span className="text">Documenti</span>
            </div>
            <div className="feature-badge">
              <span className="icon">🌱</span>
              <span className="text">Green Projects</span>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
