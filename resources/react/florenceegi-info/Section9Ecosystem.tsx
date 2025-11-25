import React from 'react';
import './Section9Ecosystem.css';

const getTranslation = (key: string, fallback: string): string => {
  const translations = (window as any).florenceEgiTranslations || {};
  return translations[key] || fallback;
};

interface EcosystemRole {
  id: string;
  icon: string;
  title: string;
  subtitle: string;
  color: string;
  features: string[];
}

export default function Section9Ecosystem() {
  const roles: EcosystemRole[] = [
    {
      id: 'creator',
      icon: '🎨',
      title: getTranslation('eco_creator_title', 'CREATOR'),
      subtitle: getTranslation('eco_creator_subtitle', 'Tu Crei'),
      color: '#f59e0b',
      features: [
        getTranslation('eco_creator_1', 'Royalty 4.5% per sempre'),
        getTranslation('eco_creator_2', 'Diritti morali conservati'),
        getTranslation('eco_creator_3', 'Dashboard analytics avanzata'),
        getTranslation('eco_creator_4', 'NATAN AI suggerisce strategie'),
        getTranslation('eco_creator_5', 'Fee dinamiche (10% → 5%)'),
        getTranslation('eco_creator_6', 'Egili bonus per vendite'),
      ],
    },
    {
      id: 'cocreator',
      icon: '✨',
      title: getTranslation('eco_cocreator_title', 'CO-CREATORE'),
      subtitle: getTranslation('eco_cocreator_subtitle', "Attiva l'Opera"),
      color: '#8b5cf6',
      features: [
        getTranslation('eco_cocreator_1', 'Firma perpetua on-chain'),
        getTranslation('eco_cocreator_2', 'Visibilità certificata blockchain'),
        getTranslation('eco_cocreator_3', 'Riconoscimento community'),
        getTranslation('eco_cocreator_4', 'Badge impatto speciali'),
        getTranslation('eco_cocreator_5', 'Mecenatismo tracciato'),
        getTranslation('eco_cocreator_6', 'Ranking pubblico supporter'),
      ],
    },
    {
      id: 'collector',
      icon: '💎',
      title: getTranslation('eco_collector_title', 'COLLECTOR'),
      subtitle: getTranslation('eco_collector_subtitle', 'Custodisce Valore'),
      color: '#3b82f6',
      features: [
        getTranslation('eco_collector_1', 'Certificato autenticità immutabile'),
        getTranslation('eco_collector_2', 'Provenance completa trasparente'),
        getTranslation('eco_collector_3', 'Rivendita MiCA-safe'),
        getTranslation('eco_collector_4', 'Royalty Creator automatiche'),
        getTranslation('eco_collector_5', 'Portfolio valorizzato NATAN'),
        getTranslation('eco_collector_6', 'Storico proprietà on-chain'),
      ],
    },
    {
      id: 'epp',
      icon: '🌱',
      title: getTranslation('eco_epp_title', 'EPP'),
      subtitle: getTranslation('eco_epp_subtitle', 'Riceve 20% Automatico'),
      color: '#10b981',
      features: [
        getTranslation('eco_epp_1', 'Donazioni on-chain istantanee'),
        getTranslation('eco_epp_2', 'Dashboard pubblica KPI impatto'),
        getTranslation('eco_epp_3', 'Progetti verificati Gold Standard'),
        getTranslation('eco_epp_4', 'Zero intermediari'),
        getTranslation('eco_epp_5', 'Smart contract trustless'),
        getTranslation('eco_epp_6', 'Report trasparente donatori'),
      ],
    },
  ];

  return (
    <section className="section-9-ecosystem">
      <div className="container">
        <div className="section-header">
          <span className="section-number">09</span>
          <h2>{getTranslation('section9_title', 'Ecosistema Virtuoso')}</h2>
          <p className="section-subtitle">
            {getTranslation('section9_subtitle', '3 Ruoli, 1 Circolo Virtuoso')}
          </p>
        </div>

        {/* Ecosystem Diagram */}
        <div className="ecosystem-diagram">
          <div className="diagram-center">
            <div className="center-logo">
              <span>EGI</span>
            </div>
            <p>{getTranslation('eco_center_text', 'Ecosistema Circolare')}</p>
          </div>

          <div className="roles-grid">
            {roles.map(role => (
              <div 
                key={role.id} 
                className={`role-card role-${role.id}`}
                style={{ '--role-color': role.color } as React.CSSProperties}
              >
                <div className="role-header">
                  <span className="role-icon">{role.icon}</span>
                  <div>
                    <h3>{role.title}</h3>
                    <span className="role-subtitle">{role.subtitle}</span>
                  </div>
                </div>
                
                <ul className="role-features">
                  {role.features.map((feature, index) => (
                    <li key={index}>
                      <span className="feature-bullet">✓</span>
                      {feature}
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        </div>

        {/* Impact Callout */}
        <div className="impact-callout">
          <div className="callout-icon">🌍</div>
          <div className="callout-content">
            <p>
              {getTranslation('eco_callout', 
                'Ogni EGI venduto pianta 2 alberi, rimuove 1kg plastica dagli oceani, o protegge 10m² di foresta. Non è greenwashing. È tracciato on-chain.'
              )}
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}
