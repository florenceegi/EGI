import React from 'react';
import './Section13UseCases.css';

const getTranslation = (key: string, fallback: string): string => {
  const translations = (window as any).florenceEgiTranslations || {};
  return translations[key] || fallback;
};

interface UseCase {
  icon: string;
  title: string;
  description: string;
  benefits: string[];
}

export default function Section13UseCases() {
  const useCases: UseCase[] = [
    {
      icon: '🎨',
      title: getTranslation('usecase_1_title', 'Artisti e Creativi'),
      description: getTranslation('usecase_1_desc', 'Proteggi e monetizza le tue opere digitali con autenticità garantita su blockchain.'),
      benefits: [
        getTranslation('usecase_1_benefit1', 'Certificazione di autenticità immutabile'),
        getTranslation('usecase_1_benefit2', 'Royalty automatiche del 4.5% su ogni rivendita'),
        getTranslation('usecase_1_benefit3', 'Guadagno del 68% sulla vendita primaria'),
      ],
    },
    {
      icon: '🏛️',
      title: getTranslation('usecase_2_title', 'Musei e Istituzioni Culturali'),
      description: getTranslation('usecase_2_desc', 'Digitalizza il patrimonio culturale e crea nuove forme di engagement.'),
      benefits: [
        getTranslation('usecase_2_benefit1', 'Collezioni digitali certificate'),
        getTranslation('usecase_2_benefit2', 'Nuova fonte di revenue sostenibile'),
        getTranslation('usecase_2_benefit3', 'Impatto ambientale positivo con EPP'),
      ],
    },
    {
      icon: '👗',
      title: getTranslation('usecase_3_title', 'Brand e Aziende'),
      description: getTranslation('usecase_3_desc', 'Certifica prodotti, traccia supply chain e crea esperienze phygital.'),
      benefits: [
        getTranslation('usecase_3_benefit1', 'Tracciabilità completa del prodotto'),
        getTranslation('usecase_3_benefit2', 'Certificati di autenticità digitali'),
        getTranslation('usecase_3_benefit3', 'Marketing sostenibile con EPP'),
      ],
    },
    {
      icon: '🌳',
      title: getTranslation('usecase_4_title', 'Onlus e Organizzazioni'),
      description: getTranslation('usecase_4_desc', 'Trasparenza totale sulle donazioni con tracciamento blockchain.'),
      benefits: [
        getTranslation('usecase_4_benefit1', 'Donazioni tracciabili e verificabili'),
        getTranslation('usecase_4_benefit2', 'Report di impatto automatici'),
        getTranslation('usecase_4_benefit3', 'Fiducia aumentata dei donatori'),
      ],
    },
    {
      icon: '🎵',
      title: getTranslation('usecase_5_title', 'Musicisti e Content Creator'),
      description: getTranslation('usecase_5_desc', 'Distribuisci musica e contenuti con controllo totale sui diritti.'),
      benefits: [
        getTranslation('usecase_5_benefit1', 'Proprietà diretta dei contenuti'),
        getTranslation('usecase_5_benefit2', 'Distribuzione senza intermediari'),
        getTranslation('usecase_5_benefit3', 'Revenue sharing trasparente'),
      ],
    },
  ];

  return (
    <section className="section-13-usecases">
      <div className="container">
        <div className="section-header">
          <span className="section-number">13</span>
          <h2>{getTranslation('section13_title', 'Scenari di Utilizzo')}</h2>
          <p className="section-subtitle">
            {getTranslation('section13_subtitle', 'Esempi di come FlorenceEGI può trasformare il tuo lavoro')}
          </p>
        </div>

        {/* Disclaimer */}
        <div className="examples-disclaimer">
          <span className="disclaimer-icon">💡</span>
          <p>
            {getTranslation('section13_disclaimer', 
              'Questi sono scenari possibili e casi d\'uso ipotizzati. FlorenceEGI è attualmente in fase di lancio.'
            )}
          </p>
        </div>

        <div className="usecases-grid">
          {useCases.map((useCase, index) => (
            <div key={index} className="usecase-card">
              <div className="usecase-header">
                <span className="usecase-icon">{useCase.icon}</span>
                <h3>{useCase.title}</h3>
              </div>
              
              <p className="usecase-description">{useCase.description}</p>
              
              <ul className="usecase-benefits">
                {useCase.benefits.map((benefit, i) => (
                  <li key={i}>
                    <span className="benefit-check">✓</span>
                    {benefit}
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
