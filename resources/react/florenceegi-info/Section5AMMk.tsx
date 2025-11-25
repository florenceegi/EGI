import React, { useState } from 'react';
import './Section5AMMk.css';

interface UserCategory {
  icon: string;
  title: string;
  description: string;
}

interface Engine {
  number: string;
  name: string;
  description: string;
}

interface CustomizationFeature {
  icon: string;
  title: string;
  description: string;
}

export default function Section5AMMk() {
  const [selectedCategory, setSelectedCategory] = useState<number | null>(null);
  const t = window.florenceEgiTranslations?.ammk;

  const userCategories: UserCategory[] = [
    {
      icon: '🎨',
      title: t?.user_artists_title || 'Artisti',
      description: t?.user_artists_desc || 'Galleria personale con royalty automatiche'
    },
    {
      icon: '🏛️',
      title: t?.user_museums_title || 'Musei',
      description: t?.user_museums_desc || 'Collezione digitale + merchandising tokenizzato'
    },
    {
      icon: '🏢',
      title: t?.user_brands_title || 'Brand',
      description: t?.user_brands_desc || 'Prodotti limited edition certificati blockchain'
    },
    {
      icon: '🌱',
      title: t?.user_ngo_title || 'Onlus',
      description: t?.user_ngo_desc || 'Progetti ambientali con donazioni tracciabili'
    },
    {
      icon: '🎓',
      title: t?.user_universities_title || 'Università',
      description: t?.user_universities_desc || 'Ricerca scientifica + pubblicazioni tokenizzate'
    },
    {
      icon: '🏛️',
      title: t?.user_municipalities_title || 'Comuni',
      description: t?.user_municipalities_desc || 'Patrimonio culturale digitalizzato'
    },
    {
      icon: '💼',
      title: t?.user_agencies_title || 'Agenzie',
      description: t?.user_agencies_desc || 'Servizi creativi + IP licensing'
    },
    {
      icon: '⚖️',
      title: t?.user_legal_title || 'Studi legali/Notai',
      description: t?.user_legal_desc || 'Notarizzazione documenti via NATAN'
    }
  ];

  const engines: Engine[] = [
    {
      number: '1',
      name: t?.engine1_name || 'NATAN Market Engine',
      description: t?.engine1_desc || 'AI valuta mercato, suggerisce prezzi, identifica trend'
    },
    {
      number: '2',
      name: t?.engine2_name || 'Asset Management',
      description: t?.engine2_desc || 'Mint, transfer, royalty automatici'
    },
    {
      number: '3',
      name: t?.engine3_name || 'Distribution Engine',
      description: t?.engine3_desc || 'Split trustless multi-wallet (Creator/EPP/Platform)'
    },
    {
      number: '4',
      name: t?.engine4_name || 'Co-Creation Engine',
      description: t?.engine4_desc || 'Gestione collaborazioni multi-artista'
    },
    {
      number: '5',
      name: t?.engine5_name || 'Compliance Engine',
      description: t?.engine5_desc || 'GDPR/MiCA/Fiscale automatico'
    }
  ];

  const customizationFeatures: CustomizationFeature[] = [
    {
      icon: '🎨',
      title: t?.custom_brand_title || 'Brand personalizzato',
      description: t?.custom_brand_desc || 'Logo, colori, dominio custom'
    },
    {
      icon: '💰',
      title: t?.custom_fees_title || 'Fee configurabili',
      description: t?.custom_fees_desc || 'Default 10%, dinamiche fino a 5%'
    },
    {
      icon: '🏢',
      title: t?.custom_tenant_title || 'Tenant isolato',
      description: t?.custom_tenant_desc || 'Dati separati, RBAC dedicato'
    },
    {
      icon: '⚙️',
      title: t?.custom_workflow_title || 'Workflow custom',
      description: t?.custom_workflow_desc || 'Approvazioni, moderazione, drops'
    }
  ];

  return (
    <section className="section5-ammk">
      <div className="container">
        {/* Header */}
        <div className="section5-header">
          <div className="section5-header-content">
              <h2 className="section5-title">
                {t?.title_line1 || 'Non un Marketplace.'} <br />
                <span className="gradient-text">{t?.title_line2 || 'Un Generatore di Marketplace.'}</span>
              </h2>
              <p className="section5-subtitle">
                {t?.subtitle || 'FlorenceEGI è un'} <strong>Asset Market Maker (AMMk)</strong>: {t?.subtitle_rest || 'una piattaforma che ti permette di creare il TUO marketplace personalizzato in cui QUALSIASI prodotto/servizio diventa un EGI.'}
              </p>
            </div>
          </div>

        {/* Chi può usare AMMk */}
        <div className="ammk-users">
          <h3 className="subsection-title">🎯 {t?.users_title || 'Chi può usare AMMk'}</h3>
          <div className="users-grid">
            {userCategories.map((category, index) => (
              <div
                key={index}
                className={`user-card ${selectedCategory === index ? 'active' : ''}`}
                onClick={() => setSelectedCategory(selectedCategory === index ? null : index)}
              >
                <div className="user-icon">{category.icon}</div>
                <h4 className="user-title">{category.title}</h4>
                <p className="user-description">{category.description}</p>
              </div>
            ))}
          </div>
        </div>

        {/* 5 Engine Integrati */}
        <div className="ammk-engines">
          <h3 className="subsection-title">💡 {t?.engines_title || '5 Engine Integrati'}</h3>
          <div className="engines-container">
            {engines.map((engine, index) => (
              <div key={index} className="engine-item">
                <div className="engine-number">{engine.number}</div>
                <div className="engine-content">
                  <h4 className="engine-name">{engine.name}</h4>
                  <p className="engine-description">{engine.description}</p>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Personalizzazione Totale */}
        <div className="ammk-customization">
          <h3 className="subsection-title">🔧 {t?.customization_title || 'Personalizzazione Totale'}</h3>
          <div className="customization-grid">
            {customizationFeatures.map((feature, index) => (
              <div key={index} className="customization-card">
                <div className="customization-icon">{feature.icon}</div>
                <h4 className="customization-title">{feature.title}</h4>
                <p className="customization-description">{feature.description}</p>
              </div>
            ))}
          </div>
        </div>

        {/* CTA Box */}
        <div className="ammk-cta-box">
          <h3>🚀 {t?.cta_title || 'Vuoi creare il tuo marketplace EGI?'}</h3>
          <p>{t?.cta_subtitle || 'Inizia oggi e trasforma qualsiasi asset in un\'opportunità di valore.'}</p>
          <button className="ammk-cta-button">
            {t?.cta_button || 'Richiedi Demo AMMk'}
          </button>
        </div>
      </div>
    </section>
  );
}
