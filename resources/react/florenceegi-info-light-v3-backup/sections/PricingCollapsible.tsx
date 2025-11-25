import React from 'react';
import { getTranslation } from '../../florenceegi-info/utils/translations';
import Collapsible from '../components/Collapsible';
import './PricingCollapsible.css';

/**
 * Pricing Collapsible
 * Fee 10% e royalty 4.5% sempre visibili, dettagli numerici in collapse
 */
export default function PricingCollapsible() {
    return (
        <section className="pricing-coll">
            <div className="pricing-coll__container">
                {/* Header */}
                <header className="pricing-coll__header">
                    <h2 className="pricing-coll__title">
                        {getTranslation('pricing.title', 'Fee Dinamiche')}
                    </h2>
                    <p className="pricing-coll__subtitle">
                        {getTranslation('pricing.subtitle', 'Più vendi, meno paghi.')}
                    </p>
                </header>

                {/* Pricing summary sempre visibile */}
                <div className="pricing-coll__summary">
                    <div className="pricing-coll__main-fee">
                        <div className="pricing-coll__fee-box">
                            <span className="pricing-coll__fee-value">10%</span>
                            <span className="pricing-coll__fee-label">Fee standard</span>
                            <span className="pricing-coll__fee-note">Scende fino al 5% con i volumi</span>
                        </div>
                        <div className="pricing-coll__royalty-box">
                            <span className="pricing-coll__royalty-value">4.5%</span>
                            <span className="pricing-coll__royalty-label">Royalty perpetue</span>
                            <span className="pricing-coll__royalty-note">Su ogni rivendita futura, per sempre</span>
                        </div>
                    </div>

                    {/* Split preview */}
                    <div className="pricing-coll__split-preview">
                        <span className="pricing-coll__split-title">Come viene diviso il ricavato</span>
                        <div className="pricing-coll__split-items">
                            <div className="pricing-coll__split-item pricing-coll__split-item--creator">
                                <span className="pricing-coll__split-percent">68%</span>
                                <span className="pricing-coll__split-label">Creator</span>
                            </div>
                            <div className="pricing-coll__split-item pricing-coll__split-item--epp">
                                <span className="pricing-coll__split-percent">20%</span>
                                <span className="pricing-coll__split-label">EPP</span>
                            </div>
                            <div className="pricing-coll__split-item pricing-coll__split-item--platform">
                                <span className="pricing-coll__split-percent">10%</span>
                                <span className="pricing-coll__split-label">Piattaforma</span>
                            </div>
                            <div className="pricing-coll__split-item pricing-coll__split-item--aps">
                                <span className="pricing-coll__split-percent">2%</span>
                                <span className="pricing-coll__split-label">Associazione</span>
                            </div>
                        </div>
                    </div>

                    {/* Key benefit */}
                    <div className="pricing-coll__benefit">
                        <span className="pricing-coll__benefit-icon">✨</span>
                        <span>Minting gratuito. Paghi solo quando vendi.</span>
                    </div>
                </div>

                {/* Dettagli in collapse */}
                <Collapsible
                    triggerTextClosed="Vedi esempio numerico completo"
                    triggerTextOpen="Nascondi esempio"
                >
                    <div className="pricing-coll__details">
                        {/* Esempio numerico */}
                        <div className="pricing-coll__example">
                            <h4 className="pricing-coll__example-title">
                                Esempio: Vendita di €1.000
                            </h4>
                            <div className="pricing-coll__example-breakdown">
                                <div className="pricing-coll__example-row">
                                    <span className="pricing-coll__example-label">💰 Prezzo vendita</span>
                                    <span className="pricing-coll__example-value">€1.000,00</span>
                                </div>
                                <div className="pricing-coll__example-row pricing-coll__example-row--highlight">
                                    <span className="pricing-coll__example-label">🎨 Creator (68%)</span>
                                    <span className="pricing-coll__example-value pricing-coll__example-value--creator">€680,00</span>
                                </div>
                                <div className="pricing-coll__example-row">
                                    <span className="pricing-coll__example-label">🌱 EPP - Ambiente (20%)</span>
                                    <span className="pricing-coll__example-value pricing-coll__example-value--epp">€200,00</span>
                                </div>
                                <div className="pricing-coll__example-row">
                                    <span className="pricing-coll__example-label">⚙️ Piattaforma (10%)</span>
                                    <span className="pricing-coll__example-value">€100,00</span>
                                </div>
                                <div className="pricing-coll__example-row">
                                    <span className="pricing-coll__example-label">🏛️ Associazione (2%)</span>
                                    <span className="pricing-coll__example-value">€20,00</span>
                                </div>
                            </div>
                        </div>

                        {/* Royalty example */}
                        <div className="pricing-coll__royalty-example">
                            <h4 className="pricing-coll__example-title">
                                Esempio Royalty: Opera rivenduta a €2.000
                            </h4>
                            <div className="pricing-coll__royalty-calc">
                                <div className="pricing-coll__royalty-step">
                                    <span className="pricing-coll__royalty-step-label">Rivendita</span>
                                    <span className="pricing-coll__royalty-step-value">€2.000</span>
                                </div>
                                <span className="pricing-coll__royalty-arrow">×</span>
                                <div className="pricing-coll__royalty-step">
                                    <span className="pricing-coll__royalty-step-label">Royalty</span>
                                    <span className="pricing-coll__royalty-step-value">4.5%</span>
                                </div>
                                <span className="pricing-coll__royalty-arrow">=</span>
                                <div className="pricing-coll__royalty-step pricing-coll__royalty-step--result">
                                    <span className="pricing-coll__royalty-step-label">Al Creator</span>
                                    <span className="pricing-coll__royalty-step-value">€90</span>
                                </div>
                            </div>
                            <p className="pricing-coll__royalty-note">
                                💡 Questo avviene automaticamente via smart contract, per sempre, su ogni rivendita.
                            </p>
                        </div>

                        {/* Fee dinamica info */}
                        <div className="pricing-coll__dynamic">
                            <h4 className="pricing-coll__example-title">Fee Dinamica</h4>
                            <p className="pricing-coll__dynamic-text">
                                La fee standard del 10% può scendere fino al 5% in base ai volumi di vendita cumulativi.
                                Più vendi sulla piattaforma, più la percentuale si riduce.
                            </p>
                        </div>
                    </div>
                </Collapsible>
            </div>
        </section>
    );
}
