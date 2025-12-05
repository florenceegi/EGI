// ============================================
// HeroV4.tsx
// ============================================
import React from 'react';
import './HeroV4.css';

export default function HeroV4() {
    return (
        <section className="hero-v4">
            <div className="hero-v4__container">
                <h1 className="hero-v4__logo">
                    <span className="hero-v4__logo-florence">Florence</span>
                    <span className="hero-v4__logo-egi">EGI</span>
                </h1>

                <h2 className="hero-v4__motto">
                    Se Esiste, <span className="text-gold">EGIZZALO</span>.
                    <br />
                    Se lo EGIZZI, <span className="text-gold">Vale</span>.
                </h2>

                <p className="hero-v4__definition">
                    <strong>FlorenceEGI</strong> è la piattaforma dove chiunque può crearsi la propria
                    attività digitale basata su blockchain e intelligenza artificiale.
                    Senza competenze tecniche, senza costi di avvio, con guadagni che continuano nel tempo.
                </p>

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

            <div className="hero-v4__scroll">
                <span>Esplora</span>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>
        </section>
    );
}

// ============================================
// HeroV4.css
// ============================================
/*
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,500;1,600&display=swap');

.hero-v4 {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 80px 20px 40px;
    position: relative;
    background: radial-gradient(ellipse at center top, rgba(212, 175, 55, 0.08) 0%, transparent 50%),
        linear-gradient(180deg, #0a0a0f 0%, #0d0d14 100%);
}

.hero-v4__container {
    max-width: 900px;
    text-align: center;
}

.hero-v4__logo {
    font-size: clamp(4rem, 12vw, 8rem);
    font-weight: 900;
    line-height: 1;
    margin-bottom: 24px;
    letter-spacing: -0.02em;
}

.hero-v4__logo-florence {
    color: #ffffff;
    display: block;
}

.hero-v4__logo-egi {
    background: linear-gradient(135deg, #d4af37, #f4e4bc, #d4af37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: block;
    font-size: 1.2em;
}

.hero-v4__motto {
    font-family: 'Playfair Display', Georgia, serif;
    font-style: italic;
    font-size: clamp(1.8rem, 5vw, 3rem);
    font-weight: 500;
    line-height: 1.4;
    margin-bottom: 40px;
    color: #e8e8f0;
    letter-spacing: 0.02em;
}

.hero-v4__motto .text-gold {
    background: linear-gradient(135deg, #d4af37, #f4e4bc, #d4af37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 600;
}

.hero-v4__definition {
    font-size: 1.3rem;
    color: #c0c0d0;
    line-height: 1.7;
    max-width: 800px;
    margin: 0 auto 40px;
}

.hero-v4__definition strong {
    color: #d4af37;
}

.hero-v4__cta-group {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 48px;
}

.hero-v4__trust {
    display: flex;
    gap: 32px;
    justify-content: center;
    flex-wrap: wrap;
}

.hero-v4__trust-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #808090;
    font-size: 0.95rem;
}

.hero-v4__scroll {
    position: absolute;
    bottom: 40px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    color: #606070;
    font-size: 0.9rem;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(8px); }
}

@media (max-width: 768px) {
    .hero-v4 {
        padding: 60px 16px 30px;
    }
    .hero-v4__definition {
        font-size: 1.1rem;
    }
    .hero-v4__trust {
        gap: 20px;
    }
}
*/
