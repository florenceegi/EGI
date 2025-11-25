import React from 'react';
import './Section7PaymentMethods.css';

// Traduzioni con fallback
const getTranslation = (key: string, fallback: string): string => {
  const translations = (window as any).florenceEgiTranslations || {};
  return translations[key] || fallback;
};

interface PaymentLevel {
  level: number;
  title: string;
  icon: string;
  description: string;
  features: string[];
  percentage: string;
}

export default function Section7PaymentMethods() {
  const paymentLevels: PaymentLevel[] = [
    {
      level: 1,
      title: getTranslation('payment_level1_title', 'Zero Crypto'),
      icon: '💳',
      description: getTranslation('payment_level1_desc', '90% degli utenti - Nessuna conoscenza crypto richiesta'),
      percentage: '90%',
      features: [
        getTranslation('payment_level1_feat1', 'Paga con carta o bonifico (come Amazon)'),
        getTranslation('payment_level1_feat2', 'Ricevi EGI in wallet auto-generato'),
        getTranslation('payment_level1_feat3', 'Export seed phrase quando vuoi'),
        getTranslation('payment_level1_feat4', 'Zero complessità blockchain'),
      ],
    },
    {
      level: 2,
      title: getTranslation('payment_level2_title', 'Ho un Wallet'),
      icon: '🏦',
      description: getTranslation('payment_level2_desc', 'Utenti con wallet crypto esistente'),
      percentage: '7%',
      features: [
        getTranslation('payment_level2_feat1', 'Paga in FIAT'),
        getTranslation('payment_level2_feat2', 'EGI arriva diretto nel TUO wallet'),
        getTranslation('payment_level2_feat3', 'Supporto Pera e Defly'),
        getTranslation('payment_level2_feat4', 'Controllo totale (non-custodial)'),
      ],
    },
    {
      level: 3,
      title: getTranslation('payment_level3_title', 'Accetto Crypto'),
      icon: '₿',
      description: getTranslation('payment_level3_desc', 'Pagamenti in criptovaluta'),
      percentage: '2%',
      features: [
        getTranslation('payment_level3_feat1', 'Paga in BTC/ETH/USDC'),
        getTranslation('payment_level3_feat2', 'PSP Partner (Coinbase/MoonPay)'),
        getTranslation('payment_level3_feat3', 'KYC/AML gestito da CASP autorizzato'),
        getTranslation('payment_level3_feat4', 'Conversione automatica'),
      ],
    },
    {
      level: 4,
      title: getTranslation('payment_level4_title', 'Egili Token'),
      icon: '🪙',
      description: getTranslation('payment_level4_desc', 'Token interno della piattaforma'),
      percentage: '1%',
      features: [
        getTranslation('payment_level4_feat1', 'Paga con Egili guadagnati'),
        getTranslation('payment_level4_feat2', '1 Egilo = €0.01'),
        getTranslation('payment_level4_feat3', 'Zero fee bancarie'),
        getTranslation('payment_level4_feat4', 'Gamification: guadagni vendendo e referral'),
      ],
    },
  ];

  return (
    <section className="section-7-payment-methods">
      <div className="container">
        <div className="section-header">
          <span className="section-number">07</span>
          <h2>{getTranslation('section7_title', '4 Modi di Pagare')}</h2>
          <p className="section-subtitle">
            {getTranslation('section7_subtitle', 'Dal nonno al crypto-native: tutti possono comprare')}
          </p>
        </div>

        <div className="payment-timeline">
          {paymentLevels.map((level, index) => (
            <div key={level.level} className={`payment-level level-${level.level}`}>
              <div className="level-connector">
                <div className="level-dot">
                  <span className="level-icon">{level.icon}</span>
                </div>
                {index < paymentLevels.length - 1 && <div className="level-line" />}
              </div>
              
              <div className="level-content">
                <div className="level-header">
                  <span className="level-badge">Livello {level.level}</span>
                  <span className="level-percentage">{level.percentage} utenti</span>
                </div>
                <h3>{level.title}</h3>
                <p className="level-description">{level.description}</p>
                
                <ul className="level-features">
                  {level.features.map((feature, i) => (
                    <li key={i}>
                      <span className="check-icon">✓</span>
                      {feature}
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          ))}
        </div>

        <div className="payment-highlight">
          <div className="highlight-icon">💡</div>
          <p>
            {getTranslation('payment_highlight', 
              'Dal nonno che usa la PostePay al trader che paga in stablecoin: NESSUNO è escluso.'
            )}
          </p>
        </div>
      </div>
    </section>
  );
}
