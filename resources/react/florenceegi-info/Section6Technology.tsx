import React from 'react';
import './Section6Technology.css';

interface UserFeature {
  icon: string;
  title: string;
  description: string;
}

interface SystemFeature {
  icon: string;
  title: string;
  description: string;
}

interface PerformanceMetric {
  value: string;
  label: string;
  description: string;
}

export default function Section6Technology() {
  const t = window.florenceEgiTranslations?.technology;

  const userFeatures: UserFeature[] = [
    {
      icon: '🖱️',
      title: t?.user_dragdrop_title || 'Interfaccia drag&drop',
      description: t?.user_dragdrop_desc || 'Carica opere intuitivamente'
    },
    {
      icon: '📱',
      title: t?.user_mobile_title || 'Dashboard mobile-responsive',
      description: t?.user_mobile_desc || 'Gestisci da qualsiasi dispositivo'
    },
    {
      icon: '🔔',
      title: t?.user_notifications_title || 'Notifiche real-time',
      description: t?.user_notifications_desc || 'Mai perdere una vendita'
    },
    {
      icon: '📊',
      title: t?.user_analytics_title || 'Analytics comprensibili',
      description: t?.user_analytics_desc || 'Dati senza complessità'
    }
  ];

  const systemFeatures: SystemFeature[] = [
    {
      icon: '⛓️',
      title: t?.system_blockchain_title || 'Blockchain Algorand',
      description: t?.system_blockchain_desc || '1000 TPS, <5s finalità, €0.001/tx'
    },
    {
      icon: '🔐',
      title: t?.system_smart_title || 'Smart contract TEAL',
      description: t?.system_smart_desc || 'Verificabili e sicuri'
    },
    {
      icon: '📜',
      title: t?.system_ulm_title || 'ULM Audit Trail',
      description: t?.system_ulm_desc || 'Log immutabile 10 anni'
    },
    {
      icon: '🛡️',
      title: t?.system_encryption_title || 'AES-256 Encryption',
      description: t?.system_encryption_desc || 'Chiavi wallet protette'
    },
    {
      icon: '🌐',
      title: t?.system_multitenant_title || 'Multi-tenant SaaS',
      description: t?.system_multitenant_desc || 'Scalabile infinitamente'
    },
    {
      icon: '🤖',
      title: t?.system_natan_title || 'NATAN AI',
      description: t?.system_natan_desc || 'RAG, Valuation, Activation'
    },
    {
      icon: '📨',
      title: t?.system_eventbus_title || 'Event Bus pub/sub',
      description: t?.system_eventbus_desc || 'Comunicazione asincrona'
    },
    {
      icon: '🔍',
      title: t?.system_observability_title || 'Observability completa',
      description: t?.system_observability_desc || 'UEM/ULM/GDPR monitoring'
    }
  ];

  const performanceMetrics: PerformanceMetric[] = [
    {
      value: '<5s',
      label: t?.perf_minting_label || 'Minting',
      description: t?.perf_minting_desc || 'Certificazione istantanea'
    },
    {
      value: '99.9%',
      label: t?.perf_uptime_label || 'Uptime SLA',
      description: t?.perf_uptime_desc || 'Disponibilità garantita'
    },
    {
      value: 'SOC 2',
      label: t?.perf_security_label || 'Sicurezza',
      description: t?.perf_security_desc || 'Type II compliant'
    },
    {
      value: '10k+',
      label: t?.perf_scalability_label || 'Utenti concurrent',
      description: t?.perf_scalability_desc || 'Scalabilità enterprise'
    }
  ];

  return (
    <section className="section6-technology">
      <div className="container">
        {/* Header */}
        <div className="section6-header">
          <div className="section6-header-content">
            <h2 className="section6-title">
              {t?.title_line1 || 'Stack Enterprise,'} <br />
              <span className="gradient-text">{t?.title_line2 || 'Esperienza Consumer.'}</span>
            </h2>
            <p className="section6-subtitle">
              {t?.subtitle || 'Tecnologia di livello enterprise nascosta dietro un\'interfaccia semplice come un\'app. Tu vedi semplicità, noi gestiamo la complessità.'}
            </p>
          </div>
        </div>

        {/* Due colonne: Tu Vedi / Sistema Fa */}
        <div className="tech-columns">
          {/* Colonna User */}
          <div className="tech-column user-column">
            <h3 className="column-title">
              <span className="column-icon">👁️</span>
              {t?.user_column_title || 'TU VEDI'}
            </h3>
            <div className="features-list">
              {userFeatures.map((feature, index) => (
                <div key={index} className="feature-item user-feature">
                  <div className="feature-icon">{feature.icon}</div>
                  <div className="feature-content">
                    <h4 className="feature-title">{feature.title}</h4>
                    <p className="feature-description">{feature.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Colonna System */}
          <div className="tech-column system-column">
            <h3 className="column-title">
              <span className="column-icon">⚙️</span>
              {t?.system_column_title || 'SISTEMA FA'}
            </h3>
            <div className="features-list">
              {systemFeatures.map((feature, index) => (
                <div key={index} className="feature-item system-feature">
                  <div className="feature-icon">{feature.icon}</div>
                  <div className="feature-content">
                    <h4 className="feature-title">{feature.title}</h4>
                    <p className="feature-description">{feature.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Performance Box */}
        <div className="performance-section">
          <h3 className="subsection-title">⚡ {t?.performance_title || 'PERFORMANCE GARANTITE'}</h3>
          <div className="performance-grid">
            {performanceMetrics.map((metric, index) => (
              <div key={index} className="performance-card">
                <div className="metric-value">{metric.value}</div>
                <div className="metric-label">{metric.label}</div>
                <div className="metric-description">{metric.description}</div>
              </div>
            ))}
          </div>
        </div>

        {/* CTA Box */}
        <div className="tech-cta-box">
          <h3>🔧 {t?.cta_title || 'Tecnologia invisibile, risultati visibili.'}</h3>
          <p>{t?.cta_subtitle || 'Non devi capire la blockchain. Devi solo EGIZZARE.'}</p>
          <button className="tech-cta-button">
            {t?.cta_button || 'Scopri la Documentazione Tecnica'}
          </button>
        </div>
      </div>
    </section>
  );
}
