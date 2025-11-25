import React, { useState } from 'react';
import AMMkAnimation3D from './AMMkAnimation3D';
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

  const userCategories: UserCategory[] = [
    {
      icon: '🎨',
      title: 'Artisti',
      description: 'Galleria personale con royalty automatiche'
    },
    {
      icon: '🏛️',
      title: 'Musei',
      description: 'Collezione digitale + merchandising tokenizzato'
    },
    {
      icon: '🏢',
      title: 'Brand',
      description: 'Prodotti limited edition certificati blockchain'
    },
    {
      icon: '🌱',
      title: 'Onlus',
      description: 'Progetti ambientali con donazioni tracciabili'
    },
    {
      icon: '🎓',
      title: 'Università',
      description: 'Ricerca scientifica + pubblicazioni tokenizzate'
    },
    {
      icon: '🏛️',
      title: 'Comuni',
      description: 'Patrimonio culturale digitalizzato'
    },
    {
      icon: '💼',
      title: 'Agenzie',
      description: 'Servizi creativi + IP licensing'
    },
    {
      icon: '⚖️',
      title: 'Studi legali/Notai',
      description: 'Notarizzazione documenti via NATAN'
    }
  ];

  const engines: Engine[] = [
    {
      number: '1',
      name: 'NATAN Market Engine',
      description: 'AI valuta mercato, suggerisce prezzi, identifica trend'
    },
    {
      number: '2',
      name: 'Asset Management',
      description: 'Mint, transfer, royalty automatici'
    },
    {
      number: '3',
      name: 'Distribution Engine',
      description: 'Split trustless multi-wallet (Creator/EPP/Platform)'
    },
    {
      number: '4',
      name: 'Co-Creation Engine',
      description: 'Gestione collaborazioni multi-artista'
    },
    {
      number: '5',
      name: 'Compliance Engine',
      description: 'GDPR/MiCA/Fiscale automatico'
    }
  ];

  const customizationFeatures: CustomizationFeature[] = [
    {
      icon: '🎨',
      title: 'Brand personalizzato',
      description: 'Logo, colori, dominio custom'
    },
    {
      icon: '💰',
      title: 'Fee configurabili',
      description: 'Default 10%, dinamiche fino a 5%'
    },
    {
      icon: '🏢',
      title: 'Tenant isolato',
      description: 'Dati separati, RBAC dedicato'
    },
    {
      icon: '⚙️',
      title: 'Workflow custom',
      description: 'Approvazioni, moderazione, drops'
    }
  ];

  return (
    <section className="section5-ammk">
      <div className="container">
        {/* Header with 3D Animation */}
        <div className="section5-header">
          {/* 3D Animation Background */}
          <div className="ammk-animation-wrapper">
            <AMMkAnimation3D />
          </div>
          
          {/* Text Content */}
          <div className="section5-header-content">
            <h2 className="section5-title">
              Non Solo un Marketplace. <br />
              <span className="gradient-text">Un Generatore di Marketplace.</span>
            </h2>
            <p className="section5-subtitle">
              FlorenceEGI è un <strong>Asset Market Maker (AMMk)</strong>: una piattaforma che ti permette di creare
              il TUO marketplace personalizzato in cui QUALSIASI prodotto/servizio diventa un EGI.
            </p>
          </div>
        </div>

        {/* Chi può usare AMMk */}
        <div className="ammk-users">
          <h3 className="subsection-title">🎯 Chi può usare AMMk</h3>
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
          <h3 className="subsection-title">💡 5 Engine Integrati</h3>
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
          <h3 className="subsection-title">🔧 Personalizzazione Totale</h3>
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
          <h3>🚀 Vuoi creare il tuo marketplace EGI?</h3>
          <p>Inizia oggi e trasforma qualsiasi asset in un'opportunità di valore.</p>
          <button className="ammk-cta-button">
            Richiedi Demo AMMk
          </button>
        </div>
      </div>
    </section>
  );
}
