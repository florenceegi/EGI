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
            description: getTranslation('how.step1_desc_light', "Carichi foto/video dell'opera e la descrivi."),
            details: [
                getTranslation('how.step1_upload_light', 'Upload come su Instagram (drag & drop)'),
                getTranslation('how.step1_ai_light', 'NATAN ti aiuta con titolo, tag e descrizione'),
                getTranslation('how.step1_choice_light', 'Scegli quanto vuoi vendere (pezzo unico / serie limitata)'),
            ]
        },
        {
            number: '02',
            icon: '⚡',
            title: getTranslation('how.step2_title', 'Egizza'),
            description: getTranslation('how.step2_desc_light', "In 5 secondi l'opera diventa un EGI (certificato digitale)."),
            badge: '< 5 secondi',
            details: [
                getTranslation('how.step2_cert_light', 'Creiamo il certificato digitale EGI'),
                getTranslation('how.step2_algo_light', 'Lo registriamo su Algorand (blockchain a zero CO₂)'),
                getTranslation('how.step2_qr_light', 'Generiamo un QR code per verificare tutto in pubblico'),
            ]
        },
        {
            number: '03',
            icon: '💰',
            title: getTranslation('how.step3_title', 'Vendi'),
            description: getTranslation('how.step3_desc_light', 'Vendi come in un e-commerce, il resto lo fa il sistema.'),
            details: [
                getTranslation('how.step3_pay_light', 'Il cliente paga con carta o bonifico (niente crypto obbligatorie)'),
                getTranslation('how.step3_split_light', 'Split automatico: 68% a te, 20% al progetto ambientale, 10% piattaforma, 2% associazione'),
                getTranslation('how.step3_royalty_light', 'Ogni rivendita futura ti paga royalty 4.5% senza che tu faccia nulla'),
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
