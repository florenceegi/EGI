import React from 'react';
import { getTranslation } from '../../florenceegi-info/utils/translations';
import './EcosystemLight.css';

/**
 * Ecosystem Light
 * Sempre visibile - Ecosistema Virtuoso
 */
export default function EcosystemLight() {
    const roles = [
        {
            icon: '🎨',
            title: getTranslation('ecosystem.creator', 'Creator'),
            description: getTranslation('ecosystem.creator_desc', 'Crea valore, guadagna royalty perpetue.'),
            percentage: '68%',
            color: '#D4AF37'
        },
        {
            icon: '✨',
            title: getTranslation('ecosystem.cocreator', 'Co-Creatore'),
            description: getTranslation('ecosystem.cocreator_desc', 'Attiva l\'opera, ottiene visibilità eterna.'),
            percentage: null,
            color: '#9B59B6'
        },
        {
            icon: '🏛️',
            title: getTranslation('ecosystem.collector', 'Collector'),
            description: getTranslation('ecosystem.collector_desc', 'Custodisce valore, investe in sostenibilità.'),
            percentage: null,
            color: '#3498DB'
        },
        {
            icon: '🌱',
            title: getTranslation('ecosystem.epp', 'EPP'),
            description: getTranslation('ecosystem.epp_desc', 'Riceve fondi automatici per rigenerazione.'),
            percentage: '20%',
            color: '#2E8B57'
        }
    ];

    return (
        <section className="ecosystem-light">
            <div className="ecosystem-light__container">
                {/* Header */}
                <header className="ecosystem-light__header">
                    <h2 className="ecosystem-light__title">
                        {getTranslation('ecosystem.title', '3 Ruoli, 1 Circolo Virtuoso')}
                    </h2>
                    <p className="ecosystem-light__subtitle">
                        Ogni transazione genera valore per tutti i partecipanti
                    </p>
                </header>

                {/* Roles */}
                <div className="ecosystem-light__roles">
                    {roles.map((role, index) => (
                        <div 
                            key={index} 
                            className="ecosystem-light__role"
                            style={{ '--role-color': role.color } as React.CSSProperties}
                        >
                            <div className="ecosystem-light__role-icon">{role.icon}</div>
                            <h3 className="ecosystem-light__role-title">{role.title}</h3>
                            {role.percentage && (
                                <span className="ecosystem-light__role-percentage">{role.percentage}</span>
                            )}
                            <p className="ecosystem-light__role-description">{role.description}</p>
                        </div>
                    ))}
                </div>

                {/* Flow visualization */}
                <div className="ecosystem-light__flow">
                    <div className="ecosystem-light__flow-item">
                        <span className="ecosystem-light__flow-label">Vendita</span>
                        <span className="ecosystem-light__flow-arrow">→</span>
                    </div>
                    <div className="ecosystem-light__flow-item">
                        <span className="ecosystem-light__flow-label">Split Automatico</span>
                        <span className="ecosystem-light__flow-arrow">→</span>
                    </div>
                    <div className="ecosystem-light__flow-item">
                        <span className="ecosystem-light__flow-label">Valore Distribuito</span>
                    </div>
                </div>

                {/* Impact message */}
                <div className="ecosystem-light__impact">
                    <span className="ecosystem-light__impact-icon">💚</span>
                    <span>Il 20% di ogni transazione va automaticamente a progetti di rigenerazione ambientale (EPP)</span>
                </div>
            </div>
        </section>
    );
}
