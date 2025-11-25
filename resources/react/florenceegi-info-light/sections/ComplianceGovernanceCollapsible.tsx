import React from 'react';
import { getTranslation } from '../../florenceegi-info/utils/translations';
import Collapsible from '../components/Collapsible';
import './ComplianceGovernanceCollapsible.css';

/**
 * Compliance + Governance Collapsible
 * 3 bullet visibili, resto in collapse
 */
export default function ComplianceGovernanceCollapsible() {
    const complianceItems = [
        {
            icon: '🇪🇺',
            title: getTranslation('compliance.gdpr_title', 'GDPR by Design'),
            shortDesc: 'Consenso granulare, export 1-click, diritto oblio',
            fullDesc: getTranslation('compliance.gdpr_desc', 'Consenso granulare, export 1-click, diritto oblio.'),
            priority: true
        },
        {
            icon: '⚖️',
            title: getTranslation('compliance.mica_title', 'MiCA-Safe'),
            shortDesc: 'No custodia crypto, no exchange, solo asset unici',
            fullDesc: getTranslation('compliance.mica_desc', 'No custodia crypto, no exchange, solo asset unici.'),
            priority: true
        },
        {
            icon: '📋',
            title: getTranslation('compliance.tax_title', 'Fiscalità Auto'),
            shortDesc: 'Fatturazione SDI, report per commercialista',
            fullDesc: getTranslation('compliance.tax_desc', 'Fatturazione SDI, report commercialista, IVA auto.'),
            priority: true
        },
        {
            icon: '©️',
            title: getTranslation('compliance.ip_title', 'Diritti d\'Autore'),
            shortDesc: 'Tutela morale e patrimoniale',
            fullDesc: getTranslation('compliance.ip_desc', 'Tutela morale e patrimoniale, Diritto di Seguito.')
        }
    ];

    const governanceInfo = {
        srl: {
            icon: '🏢',
            name: 'Florence EGI SRL',
            role: getTranslation('governance.srl_title', 'SRL Operativa'),
            desc: getTranslation('governance.srl_desc', 'Innovazione, scaling, business.'),
            items: [
                'Innovazione prodotto',
                'Rapporti commerciali',
                'Scaling tecnologico',
                'Partnership strategiche'
            ]
        },
        aps: {
            icon: '🏛️',
            name: 'Frangette APS',
            role: getTranslation('governance.aps_title', 'APS Valoriale'),
            desc: getTranslation('governance.aps_desc', 'Custode della missione, potere di veto etico.'),
            items: [
                'Veto su decisioni anti-missione',
                'Protezione sostenibilità',
                'Trasparenza governance',
                'Tutela community'
            ]
        }
    };

    const visibleCompliance = complianceItems.filter(item => item.priority);
    const hiddenCompliance = complianceItems.filter(item => !item.priority);

    return (
        <section className="compliance-coll">
            <div className="compliance-coll__container">
                {/* Header */}
                <header className="compliance-coll__header">
                    <h2 className="compliance-coll__title">
                        {getTranslation('compliance.title', 'Compliance Totale')}
                    </h2>
                    <p className="compliance-coll__subtitle">
                        {getTranslation('compliance.subtitle', 'Pensaci tu a creare, al resto pensiamo noi.')}
                    </p>
                </header>

                {/* Compliance badges sempre visibili */}
                <div className="compliance-coll__badges">
                    {visibleCompliance.map((item, index) => (
                        <div key={index} className="compliance-coll__badge">
                            <span className="compliance-coll__badge-icon">{item.icon}</span>
                            <div className="compliance-coll__badge-content">
                                <span className="compliance-coll__badge-title">{item.title}</span>
                                <span className="compliance-coll__badge-desc">{item.shortDesc}</span>
                            </div>
                        </div>
                    ))}
                </div>

                {/* Governance dual preview */}
                <div className="compliance-coll__governance-preview">
                    <h3 className="compliance-coll__section-title">
                        {getTranslation('governance.title', 'Governance Duale')}
                    </h3>
                    <div className="compliance-coll__dual">
                        <div className="compliance-coll__entity">
                            <span className="compliance-coll__entity-icon">{governanceInfo.srl.icon}</span>
                            <span className="compliance-coll__entity-name">{governanceInfo.srl.name}</span>
                            <span className="compliance-coll__entity-role">{governanceInfo.srl.role}</span>
                        </div>
                        <div className="compliance-coll__separator">⚖️</div>
                        <div className="compliance-coll__entity">
                            <span className="compliance-coll__entity-icon">{governanceInfo.aps.icon}</span>
                            <span className="compliance-coll__entity-name">{governanceInfo.aps.name}</span>
                            <span className="compliance-coll__entity-role">{governanceInfo.aps.role}</span>
                        </div>
                    </div>
                </div>

                {/* Dettagli in collapse */}
                <Collapsible
                    triggerTextClosed="Approfondisci governance & compliance"
                    triggerTextOpen="Nascondi dettagli"
                >
                    <div className="compliance-coll__details">
                        {/* Compliance completa */}
                        <div className="compliance-coll__full-compliance">
                            <h4 className="compliance-coll__details-title">Compliance Completa</h4>
                            <div className="compliance-coll__compliance-grid">
                                {complianceItems.map((item, index) => (
                                    <div key={index} className="compliance-coll__compliance-card">
                                        <span className="compliance-coll__compliance-icon">{item.icon}</span>
                                        <h5 className="compliance-coll__compliance-title">{item.title}</h5>
                                        <p className="compliance-coll__compliance-desc">{item.fullDesc}</p>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Governance dettagli */}
                        <div className="compliance-coll__governance-detail">
                            <h4 className="compliance-coll__details-title">Dettaglio Governance Duale</h4>
                            <div className="compliance-coll__governance-grid">
                                <div className="compliance-coll__gov-card">
                                    <div className="compliance-coll__gov-header">
                                        <span className="compliance-coll__gov-icon">{governanceInfo.srl.icon}</span>
                                        <div>
                                            <h5 className="compliance-coll__gov-name">{governanceInfo.srl.name}</h5>
                                            <span className="compliance-coll__gov-role">{governanceInfo.srl.role}</span>
                                        </div>
                                    </div>
                                    <p className="compliance-coll__gov-desc">{governanceInfo.srl.desc}</p>
                                    <ul className="compliance-coll__gov-list">
                                        {governanceInfo.srl.items.map((item, i) => (
                                            <li key={i}>{item}</li>
                                        ))}
                                    </ul>
                                </div>

                                <div className="compliance-coll__gov-card compliance-coll__gov-card--aps">
                                    <div className="compliance-coll__gov-header">
                                        <span className="compliance-coll__gov-icon">{governanceInfo.aps.icon}</span>
                                        <div>
                                            <h5 className="compliance-coll__gov-name">{governanceInfo.aps.name}</h5>
                                            <span className="compliance-coll__gov-role">{governanceInfo.aps.role}</span>
                                        </div>
                                    </div>
                                    <p className="compliance-coll__gov-desc">{governanceInfo.aps.desc}</p>
                                    <ul className="compliance-coll__gov-list">
                                        {governanceInfo.aps.items.map((item, i) => (
                                            <li key={i}>{item}</li>
                                        ))}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </Collapsible>
            </div>
        </section>
    );
}
