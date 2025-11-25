import React, { useState } from 'react';
import './Section8Compliance.css';

const getTranslation = (key: string, fallback: string): string => {
  const translations = (window as any).florenceEgiTranslations || {};
  return translations[key] || fallback;
};

interface ComplianceSection {
  id: string;
  icon: string;
  title: string;
  features: string[];
}

export default function Section8Compliance() {
  const [activeSection, setActiveSection] = useState<string>('gdpr');

  const complianceSections: ComplianceSection[] = [
    {
      id: 'gdpr',
      icon: '🛡️',
      title: getTranslation('compliance_gdpr_title', 'GDPR by Design'),
      features: [
        getTranslation('compliance_gdpr_1', 'Consenso granulare (cookie/analytics/marketing separati)'),
        getTranslation('compliance_gdpr_2', 'Export dati completo (JSON/XML) in 1 click'),
        getTranslation('compliance_gdpr_3', 'Cancellazione account con pseudonimizzazione blockchain'),
        getTranslation('compliance_gdpr_4', 'Audit log immutabile 10 anni (firma SHA-256)'),
        getTranslation('compliance_gdpr_5', 'Diritti Art. 15-20 automatizzati'),
      ],
    },
    {
      id: 'mica',
      icon: '⚖️',
      title: getTranslation('compliance_mica_title', 'MiCA-safe (Fuori Perimetro Crypto)'),
      features: [
        getTranslation('compliance_mica_1', 'NON custodiamo fondi crypto'),
        getTranslation('compliance_mica_2', 'NON facciamo exchange crypto/FIAT'),
        getTranslation('compliance_mica_3', 'NON processiamo pagamenti crypto diretti'),
        getTranslation('compliance_mica_4', 'PSP/CASP autorizzati gestiscono tutto'),
        getTranslation('compliance_mica_5', 'Solo certificati NFT unici (no criptovalute)'),
      ],
    },
    {
      id: 'fiscal',
      icon: '💰',
      title: getTranslation('compliance_fiscal_title', 'Fiscalità Automatica'),
      features: [
        getTranslation('compliance_fiscal_1', 'Fatturazione elettronica SDI (FatturaPA 1.6.1)'),
        getTranslation('compliance_fiscal_2', 'IVA automatica (IT/UE OSS/Extra-UE)'),
        getTranslation('compliance_fiscal_3', 'Report trimestrale CSV/XML per commercialista'),
        getTranslation('compliance_fiscal_4', 'Conservazione digitale 10 anni'),
        getTranslation('compliance_fiscal_5', 'DAC7 reporting automatico'),
      ],
    },
    {
      id: 'copyright',
      icon: '📜',
      title: getTranslation('compliance_copyright_title', 'Diritti Autore Protetti'),
      features: [
        getTranslation('compliance_copyright_1', 'Royalty 4.5% contrattuale (smart contract trustless)'),
        getTranslation('compliance_copyright_2', 'Diritto di Seguito legale 4%-0.25% (se >€3k via SIAE)'),
        getTranslation('compliance_copyright_3', 'Diritti morali inalienabili (paternità/integrità)'),
        getTranslation('compliance_copyright_4', 'Copyright NON si trasferisce con vendita NFT'),
        getTranslation('compliance_copyright_5', 'Licenze configurabili (CC, uso esclusivo, etc.)'),
      ],
    },
  ];

  const activeContent = complianceSections.find(s => s.id === activeSection);

  return (
    <section className="section-8-compliance">
      <div className="container">
        <div className="section-header">
          <span className="section-number">08</span>
          <h2>{getTranslation('section8_title', 'Compliance Totale')}</h2>
          <p className="section-subtitle">
            {getTranslation('section8_subtitle', 'GDPR, MiCA, Fisco: pensaci tu a creare, al resto pensiamo noi')}
          </p>
        </div>

        <div className="compliance-container">
          {/* Tab Navigation */}
          <div className="compliance-tabs">
            {complianceSections.map(section => (
              <button
                key={section.id}
                className={`compliance-tab ${activeSection === section.id ? 'active' : ''}`}
                onClick={() => setActiveSection(section.id)}
              >
                <span className="tab-icon">{section.icon}</span>
                <span className="tab-title">{section.title}</span>
              </button>
            ))}
          </div>

          {/* Tab Content */}
          <div className="compliance-content">
            {activeContent && (
              <div className="content-panel active">
                <div className="panel-header">
                  <span className="panel-icon">{activeContent.icon}</span>
                  <h3>{activeContent.title}</h3>
                </div>
                <ul className="feature-list">
                  {activeContent.features.map((feature, index) => (
                    <li key={index} className="feature-item">
                      <span className="feature-check">✓</span>
                      <span>{feature}</span>
                    </li>
                  ))}
                </ul>
              </div>
            )}
          </div>
        </div>

        {/* Trust Badges */}
        <div className="trust-badges">
          <div className="badge">
            <span className="badge-icon">🔒</span>
            <span className="badge-text">SOC 2 Type II</span>
          </div>
          <div className="badge">
            <span className="badge-icon">🇪🇺</span>
            <span className="badge-text">GDPR Compliant</span>
          </div>
          <div className="badge">
            <span className="badge-icon">⚖️</span>
            <span className="badge-text">MiCA Safe</span>
          </div>
          <div className="badge">
            <span className="badge-icon">🏛️</span>
            <span className="badge-text">ISO 27001</span>
          </div>
        </div>
      </div>
    </section>
  );
}
