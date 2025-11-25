import React from 'react';
import './Section11Governance.css';

const getTranslation = (key: string, fallback: string): string => {
  const translations = (window as any).florenceEgiTranslations || {};
  return translations[key] || fallback;
};

export default function Section11Governance() {
  const srlFeatures = [
    getTranslation('gov_srl_1', 'Innovazione prodotto'),
    getTranslation('gov_srl_2', 'Rapporti commerciali'),
    getTranslation('gov_srl_3', 'Scaling tecnologico'),
    getTranslation('gov_srl_4', 'Partnership strategiche'),
  ];

  const apsFeatures = [
    getTranslation('gov_aps_1', 'Veto su decisioni anti-missione'),
    getTranslation('gov_aps_2', 'Protezione sostenibilità'),
    getTranslation('gov_aps_3', 'Trasparenza governance'),
    getTranslation('gov_aps_4', 'Tutela community'),
  ];

  return (
    <section className="section-11-governance">
      <div className="container">
        <div className="section-header">
          <span className="section-number">11</span>
          <h2>{getTranslation('section11_title', 'Governance Duale')}</h2>
          <p className="section-subtitle">
            {getTranslation('section11_subtitle', 'Due entità, una missione: il tuo successo con impatto reale')}
          </p>
        </div>

        {/* Dual Structure */}
        <div className="governance-structure">
          {/* SRL Card */}
          <div className="governance-card srl-card">
            <div className="card-header">
              <span className="card-icon">🏢</span>
              <div>
                <h3>Florence EGI SRL</h3>
                <span className="card-label">{getTranslation('gov_srl_label', 'Operatività')}</span>
              </div>
            </div>
            <ul className="card-features">
              {srlFeatures.map((feature, index) => (
                <li key={index}>
                  <span className="bullet">→</span>
                  {feature}
                </li>
              ))}
            </ul>
          </div>

          {/* Connection */}
          <div className="governance-connection">
            <div className="connection-line"></div>
            <div className="connection-icon">⚖️</div>
            <div className="connection-line"></div>
          </div>

          {/* APS Card */}
          <div className="governance-card aps-card">
            <div className="card-header">
              <span className="card-icon">🏛️</span>
              <div>
                <h3>Frangette APS</h3>
                <span className="card-label">{getTranslation('gov_aps_label', 'Custode Valori')}</span>
              </div>
            </div>
            <ul className="card-features">
              {apsFeatures.map((feature, index) => (
                <li key={index}>
                  <span className="bullet">→</span>
                  {feature}
                </li>
              ))}
            </ul>
          </div>
        </div>

        {/* Example Scenario */}
        <div className="scenario-box">
          <h4>{getTranslation('gov_scenario_title', 'Esempio Pratico')}</h4>
          <div className="scenario-content">
            <div className="scenario-step">
              <span className="step-icon">📋</span>
              <p><strong>SCENARIO:</strong> {getTranslation('gov_scenario_1', 'Florence EGI SRL propone partnership con fast-fashion')}</p>
            </div>
            <div className="scenario-step">
              <span className="step-icon">🔍</span>
              <p><strong>FRANGETTE VALUTA:</strong> {getTranslation('gov_scenario_2', 'Incompatibile con missione sostenibilità')}</p>
            </div>
            <div className="scenario-step">
              <span className="step-icon">❌</span>
              <p><strong>VETO:</strong> {getTranslation('gov_scenario_3', 'Partnership bloccata')}</p>
            </div>
            <div className="scenario-result">
              <span className="result-icon">✓</span>
              <p>{getTranslation('gov_scenario_result', 'Risultato: Valore piattaforma protetto long-term')}</p>
            </div>
          </div>
        </div>

        {/* Quote */}
        <div className="governance-quote">
          <blockquote>
            {getTranslation('gov_quote', 
              '"Non siamo qui per la exit a 18 mesi. Siamo qui per costruire il Rinascimento Digitale."'
            )}
          </blockquote>
        </div>
      </div>
    </section>
  );
}
