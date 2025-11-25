import React from 'react';
import { getTranslation } from '../../florenceegi-info/utils/translations';
import './HeroSectionLight.css';

/**
 * Hero Section Light
 * Sempre visibile - Payoff "Se Esiste, EGIZZALO. Se lo EGIZZI, Vale."
 */
export default function HeroSectionLight() {
    return (
        <section className="hero-light">
            <div className="hero-light__container">
                {/* Badge piattaforma */}
                <div className="hero-light__badge">
                    <span className="hero-light__badge-icon">⚡</span>
                    <span>{getTranslation('hero.platform_name', 'FlorenceEGI')}</span>
                </div>

                {/* Headline principale */}
                <h1 
                    className="hero-light__headline"
                    dangerouslySetInnerHTML={{
                        __html: getTranslation(
                            'hero.headline_html',
                            'Se Esiste, <span class="text-gold">EGIZZALO</span>.<br>Se lo EGIZZI, <span class="text-gold">Vale</span>.'
                        )
                    }}
                />

                {/* Sottotitolo */}
                <p className="hero-light__subheadline">
                    {getTranslation(
                        'hero.subheadline_light',
                        'Trasforma qualsiasi opera, prodotto o esperienza in un certificato digitale. Vendi come su un e-commerce, ma con la sicurezza della blockchain.'
                    )}
                </p>

                {/* CTA Buttons */}
                <div className="hero-light__cta-group">
                    <a href="/register" className="btn-light btn-light--primary">
                        {getTranslation('hero.cta_primary', 'Inizia a EGIZZARE')}
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                    <a href="#how-it-works" className="btn-light btn-light--secondary">
                        {getTranslation('hero.cta_secondary', 'Scopri Come Funziona')}
                    </a>
                </div>

                {/* Trust indicators */}
                <div className="hero-light__trust">
                    <div className="hero-light__trust-item">
                        <span className="hero-light__trust-icon">🌱</span>
                        <span>Algorand Zero CO₂</span>
                    </div>
                    <div className="hero-light__trust-item">
                        <span className="hero-light__trust-icon">🇪🇺</span>
                        <span>MiCA Compliant</span>
                    </div>
                    <div className="hero-light__trust-item">
                        <span className="hero-light__trust-icon">🔒</span>
                        <span>GDPR by Design</span>
                    </div>
                </div>
            </div>

            {/* Scroll indicator */}
            <div className="hero-light__scroll">
                <span>{getTranslation('hero.scroll_text', 'Esplora')}</span>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>
        </section>
    );
}
