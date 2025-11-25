import React from 'react';
import { getTranslation } from '../../florenceegi-info/utils/translations';
import './CTAFinalLight.css';

/**
 * CTA Final Light
 * Sempre visibile - "Sei un creator / collector / azienda?"
 */
export default function CTAFinalLight() {
    const ctaOptions = [
        {
            icon: '🎨',
            title: getTranslation('cta_final.btn_creator', 'Sono un Creator'),
            description: 'Monetizza le tue opere, ottieni royalty perpetue',
            link: '/register?type=creator',
            color: '#D4AF37'
        },
        {
            icon: '🏛️',
            title: getTranslation('cta_final.btn_collector', 'Sono un Collector'),
            description: 'Colleziona arte con impatto, supporta i creatori',
            link: '/register?type=collector',
            color: '#3498DB'
        },
        {
            icon: '🏢',
            title: getTranslation('cta_final.btn_company', 'Sono un\'Azienda'),
            description: 'Crea il tuo marketplace, certifica i tuoi prodotti',
            link: '/contact?type=business',
            color: '#2E8B57'
        }
    ];

    return (
        <section className="cta-light">
            <div className="cta-light__container">
                {/* Header */}
                <header className="cta-light__header">
                    <h2 className="cta-light__title">
                        {getTranslation('cta_final.title', 'Il Rinascimento Digitale Inizia Qui')}
                    </h2>
                    <p className="cta-light__subtitle">
                        Scegli il tuo percorso e inizia a EGIZZARE
                    </p>
                </header>

                {/* CTA Options */}
                <div className="cta-light__options">
                    {ctaOptions.map((option, index) => (
                        <a 
                            key={index} 
                            href={option.link}
                            className="cta-light__option"
                            style={{ '--option-color': option.color } as React.CSSProperties}
                        >
                            <div className="cta-light__option-icon">{option.icon}</div>
                            <h3 className="cta-light__option-title">{option.title}</h3>
                            <p className="cta-light__option-description">{option.description}</p>
                            <span className="cta-light__option-arrow">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </span>
                        </a>
                    ))}
                </div>

                {/* Footer mini */}
                <footer className="cta-light__footer">
                    <div className="cta-light__badges">
                        <span className="cta-light__badge">🇪🇺 MiCA Compliant</span>
                        <span className="cta-light__badge">🔒 GDPR by Design</span>
                        <span className="cta-light__badge">🌱 Algorand Zero CO₂</span>
                    </div>
                    <p className="cta-light__copyright">
                        © 2025 FlorenceEGI. {getTranslation('footer.rights', 'All rights reserved.')}
                    </p>
                </footer>
            </div>
        </section>
    );
}
