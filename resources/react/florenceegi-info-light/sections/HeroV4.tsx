import React from 'react';
import './HeroV4.css';

/**
 * Hero Section V4
 * Frase madre + motto "Se esiste, Egizzalo"
 */
export default function HeroV4() {
    return (
        <section className="hero-v4">
            <div className="hero-v4__container">
                {/* Logo/Titolo grande */}
                <h1 className="hero-v4__logo">
                    <span className="hero-v4__logo-florence">Florence</span>
                    <span className="hero-v4__logo-egi">EGI</span>
                </h1>

                {/* Motto principale */}
                <h2 className="hero-v4__motto">
                    Se Esiste, <span className="text-gold">EGIZZALO</span>.
                    <br />
                    Se lo EGIZZI, <span className="text-gold">Vale</span>.
                </h2>

                {/* Frase madre - definizione */}
                <p className="hero-v4__definition">
                    <strong>FlorenceEGI</strong> è la piattaforma dove chiunque può crearsi la propria
                    attività digitale basata su blockchain e intelligenza artificiale.
                    Senza competenze tecniche, senza costi di avvio, con guadagni che continuano nel tempo.
                </p>

                {/* CTA Buttons */}
                <div className="hero-v4__cta-group">
                    <a href="/register" className="btn-v4 btn-v4--primary">
                        Inizia Gratis
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                    <a href="#scopri" className="btn-v4 btn-v4--secondary">
                        Scopri di più
                    </a>
                </div>

                {/* Trust indicators */}
                <div className="hero-v4__trust">
                    <div className="hero-v4__trust-item">
                        <span>🌱</span>
                        <span>Zero CO₂</span>
                    </div>
                    <div className="hero-v4__trust-item">
                        <span>🆓</span>
                        <span>Gratis per iniziare</span>
                    </div>
                    <div className="hero-v4__trust-item">
                        <span>🇮🇹</span>
                        <span>Made in Italy</span>
                    </div>
                </div>
            </div>

            {/* Scroll indicator */}
            <div className="hero-v4__scroll">
                <span>Esplora</span>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>
        </section>
    );
}
