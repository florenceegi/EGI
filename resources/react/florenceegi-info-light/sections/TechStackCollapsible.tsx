import React from 'react';
import { getTranslation } from '../../florenceegi-info/utils/translations';
import Collapsible from '../components/Collapsible';
import './TechStackCollapsible.css';

/**
 * Tech Stack Collapsible
 * Titolo e descrizione breve sempre visibili, dettagli in collapse
 */
export default function TechStackCollapsible() {
    const engines = [
        {
            icon: '🧠',
            name: getTranslation('ammk.engine1_name', 'NATAN Market Engine'),
            desc: getTranslation('ammk.engine1_desc', 'AI valuta mercato, suggerisce prezzi, identifica trend')
        },
        {
            icon: '📦',
            name: getTranslation('ammk.engine2_name', 'Asset Management'),
            desc: getTranslation('ammk.engine2_desc', 'Mint, transfer, royalty automatici')
        },
        {
            icon: '🔀',
            name: getTranslation('ammk.engine3_name', 'Distribution Engine'),
            desc: getTranslation('ammk.engine3_desc', 'Split trustless multi-wallet (Creator/EPP/Platform)')
        },
        {
            icon: '👥',
            name: getTranslation('ammk.engine4_name', 'Co-Creation Engine'),
            desc: getTranslation('ammk.engine4_desc', 'Gestione collaborazioni multi-artista')
        },
        {
            icon: '⚖️',
            name: getTranslation('ammk.engine5_name', 'Compliance Engine'),
            desc: getTranslation('ammk.engine5_desc', 'GDPR/MiCA/Fiscale automatico')
        }
    ];

    const techSpecs = [
        { label: 'Blockchain', value: 'Algorand', detail: '1000 TPS, <5s finalità, €0.001/tx' },
        { label: 'Smart Contracts', value: 'TEAL', detail: 'Verificabili e sicuri' },
        { label: 'Encryption', value: 'AES-256', detail: 'Chiavi wallet protette' },
        { label: 'Architecture', value: 'Multi-tenant SaaS', detail: 'Scalabile infinitamente' },
        { label: 'AI', value: 'NATAN', detail: 'RAG, Valuation, Activation' },
        { label: 'Audit', value: 'ULM Trail', detail: 'Log immutabile 10 anni' }
    ];

    const performance = [
        { metric: 'Minting', value: '< 5s', icon: '⚡' },
        { metric: 'Uptime SLA', value: '99.9%', icon: '🟢' },
        { metric: 'Sicurezza', value: 'Enterprise', icon: '🔒' }
    ];

    return (
        <section className="tech-coll">
            <div className="tech-coll__container">
                {/* Header */}
                <header className="tech-coll__header">
                    <h2 className="tech-coll__title">
                        Stack Enterprise, Esperienza Consumer
                    </h2>
                    <p className="tech-coll__subtitle">
                        Tecnologia di livello enterprise nascosta dietro un'interfaccia semplice come un'app
                    </p>
                </header>

                {/* Preview visibile: 5 Engine cards */}
                <div className="tech-coll__preview">
                    <h3 className="tech-coll__section-title">5 Engine Integrati</h3>
                    <div className="tech-coll__engines">
                        {engines.map((engine, index) => (
                            <div key={index} className="tech-coll__engine">
                                <span className="tech-coll__engine-icon">{engine.icon}</span>
                                <span className="tech-coll__engine-name">{engine.name}</span>
                            </div>
                        ))}
                    </div>

                    {/* Performance badges sempre visibili */}
                    <div className="tech-coll__performance">
                        {performance.map((perf, index) => (
                            <div key={index} className="tech-coll__perf-badge">
                                <span className="tech-coll__perf-icon">{perf.icon}</span>
                                <span className="tech-coll__perf-value">{perf.value}</span>
                                <span className="tech-coll__perf-label">{perf.metric}</span>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Dettagli tecnici in collapse */}
                <Collapsible
                    triggerTextClosed="Mostra dettagli tecnici"
                    triggerTextOpen="Nascondi dettagli tecnici"
                >
                    <div className="tech-coll__details">
                        {/* Engine descriptions */}
                        <div className="tech-coll__engine-details">
                            <h4 className="tech-coll__details-title">Descrizione Engine</h4>
                            <div className="tech-coll__engine-grid">
                                {engines.map((engine, index) => (
                                    <div key={index} className="tech-coll__engine-card">
                                        <div className="tech-coll__engine-header">
                                            <span className="tech-coll__engine-icon">{engine.icon}</span>
                                            <span className="tech-coll__engine-name">{engine.name}</span>
                                        </div>
                                        <p className="tech-coll__engine-desc">{engine.desc}</p>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Tech specs */}
                        <div className="tech-coll__specs">
                            <h4 className="tech-coll__details-title">Specifiche Tecniche</h4>
                            <div className="tech-coll__specs-grid">
                                {techSpecs.map((spec, index) => (
                                    <div key={index} className="tech-coll__spec">
                                        <span className="tech-coll__spec-label">{spec.label}</span>
                                        <span className="tech-coll__spec-value">{spec.value}</span>
                                        <span className="tech-coll__spec-detail">{spec.detail}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </Collapsible>
            </div>
        </section>
    );
}
