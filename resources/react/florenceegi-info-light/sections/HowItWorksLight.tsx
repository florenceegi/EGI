import React from 'react';
import { getTranslation } from '../../florenceegi-info/utils/translations';
import './HowItWorksLight.css';

/**
 * How It Works Light
 * Sempre visibile - 3 Click, 0 Complicazioni
 */
export default function HowItWorksLight() {
    const steps = [
        {
            number: '01',
            icon: '📤',
            title: getTranslation('how.step1_title', 'Carica'),
            description: getTranslation('how.step1_desc', 'Upload foto/video, descrizione AI, prezzo suggerito.'),
            details: [
                getTranslation('how.step1_upload', 'Upload foto/video opera (drag & drop)'),
                getTranslation('how.step1_description', 'Compila descrizione (NATAN AI ti aiuta)'),
                getTranslation('how.step1_epp', 'Scegli progetto ambientale da sostenere'),
            ]
        },
        {
            number: '02',
            icon: '⚡',
            title: getTranslation('how.step2_title', 'Egizza'),
            description: getTranslation('how.step2_desc', 'Certificazione blockchain istantanea (<5s), hash immutabile.'),
            badge: '< 5 secondi',
            details: [
                getTranslation('how.step2_certificate', 'Certificato blockchain immutabile'),
                getTranslation('how.step2_qr', 'QR code per verifica pubblica'),
                'Zero emissioni CO₂ (Algorand)',
            ]
        },
        {
            number: '03',
            icon: '💰',
            title: getTranslation('how.step3_title', 'Vendi'),
            description: getTranslation('how.step3_desc', 'Pagamenti globali, split automatico ricavi, royalty perpetue.'),
            details: [
                'Cliente paga con carta/bonifico/crypto',
                'Split automatico: Creator 68% • EPP 20% • Piattaforma 10%',
                'Royalty 4.5% per sempre su rivendite',
            ]
        }
    ];

    return (
        <section id="how-it-works" className="how-light">
            <div className="how-light__container">
                {/* Header */}
                <header className="how-light__header">
                    <h2 className="how-light__title">
                        {getTranslation('how.title', '3 Click, 0 Complicazioni')}
                    </h2>
                    <p className="how-light__subtitle">
                        {getTranslation('how.subtitle', 'Semplice come dovrebbe essere')}
                    </p>
                </header>

                {/* Steps */}
                <div className="how-light__steps">
                    {steps.map((step, index) => (
                        <div key={index} className="how-light__step">
                            <div className="how-light__step-number">{step.number}</div>
                            <div className="how-light__step-icon">{step.icon}</div>
                            <h3 className="how-light__step-title">{step.title}</h3>
                            {step.badge && (
                                <span className="how-light__step-badge">{step.badge}</span>
                            )}
                            <p className="how-light__step-description">{step.description}</p>
                            <ul className="how-light__step-details">
                                {step.details.map((detail, i) => (
                                    <li key={i}>{detail}</li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </div>

                {/* Connectors (visual) */}
                <div className="how-light__connectors" aria-hidden="true">
                    <div className="how-light__connector"></div>
                    <div className="how-light__connector"></div>
                </div>
            </div>
        </section>
    );
}
