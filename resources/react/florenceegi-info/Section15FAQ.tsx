import React, { useState } from 'react';
import './Section15FAQ.css';

const getTranslation = (key: string, fallback: string): string => {
  const translations = (window as any).florenceEgiTranslations || {};
  return translations[key] || fallback;
};

interface FAQItem {
  question: string;
  answer: string;
}

export default function Section15FAQ() {
  const [openIndex, setOpenIndex] = useState<number | null>(0);

  const faqItems: FAQItem[] = [
    {
      question: getTranslation('faq_1_q', 'Devo capire la blockchain per usare FlorenceEGI?'),
      answer: getTranslation('faq_1_a', 'NO. Usiamo blockchain per sicurezza e trasparenza, ma tu vedi solo un e-commerce normale. Nessuna conoscenza tecnica richiesta.'),
    },
    {
      question: getTranslation('faq_2_q', 'Quanto costa creare un EGI?'),
      answer: getTranslation('faq_2_a', 'Minting GRATUITO. Paghi solo quando vendi (10% fee default, dinamica fino a 5% con il volume).'),
    },
    {
      question: getTranslation('faq_3_q', 'Posso vendere opere fisiche?'),
      answer: getTranslation('faq_3_a', 'SÌ. Certificato blockchain + QR code/NFC per verifica autenticità. L\'opera fisica rimane tua, il certificato digitale ne attesta l\'autenticità.'),
    },
    {
      question: getTranslation('faq_4_q', 'Le royalty sono garantite?'),
      answer: getTranslation('faq_4_a', 'SÌ. Smart contract automatico 4.5% su OGNI rivendita, forever, trustless. Non dipende dalla buona volontà di nessuno.'),
    },
    {
      question: getTranslation('faq_5_q', 'Cosa succede se la piattaforma chiude?'),
      answer: getTranslation('faq_5_a', 'Gli EGI sono registrati su blockchain Algorand (decentralizzata). Anche se chiudiamo, gli EGI esistono per sempre e rimangono verificabili.'),
    },
    {
      question: getTranslation('faq_6_q', 'Posso usare il mio wallet crypto?'),
      answer: getTranslation('faq_6_a', 'SÌ. Supportiamo wallet esterni come Pera e Defly. Puoi anche esportare le chiavi del wallet auto-generato quando vuoi.'),
    },
    {
      question: getTranslation('faq_7_q', 'Gli Egili si possono vendere?'),
      answer: getTranslation('faq_7_a', 'NO. Gli Egili sono account-bound (punti fedeltà interni), non trasferibili né scambiabili. Servono solo per acquisti sulla piattaforma.'),
    },
    {
      question: getTranslation('faq_8_q', 'Come funziona il 20% a EPP?'),
      answer: getTranslation('faq_8_a', 'Automatico via smart contract. Tu scegli il progetto ambientale, il sistema split istantaneo on-chain. Zero intermediari.'),
    },
    {
      question: getTranslation('faq_9_q', 'FlorenceEGI è MiCA-compliant?'),
      answer: getTranslation('faq_9_a', 'SÌ (MiCA-safe). Non custodiamo crypto, non facciamo exchange. I PSP autorizzati gestiscono i pagamenti.'),
    },
    {
      question: getTranslation('faq_10_q', 'Posso creare il MIO marketplace?'),
      answer: getTranslation('faq_10_a', 'SÌ. Con AMMk puoi avere tenant dedicato, brand custom, fee configurabili e workflow personalizzati.'),
    },
  ];

  const toggleFAQ = (index: number) => {
    setOpenIndex(openIndex === index ? null : index);
  };

  return (
    <section className="section-15-faq">
      <div className="container">
        <div className="section-header">
          <span className="section-number">15</span>
          <h2>{getTranslation('section15_title', 'FAQ')}</h2>
          <p className="section-subtitle">
            {getTranslation('section15_subtitle', 'Le 10 domande più frequenti')}
          </p>
        </div>

        <div className="faq-list">
          {faqItems.map((item, index) => (
            <div 
              key={index} 
              className={`faq-item ${openIndex === index ? 'open' : ''}`}
            >
              <button 
                className="faq-question"
                onClick={() => toggleFAQ(index)}
              >
                <span className="question-number">{String(index + 1).padStart(2, '0')}</span>
                <span className="question-text">{item.question}</span>
                <span className="question-icon">{openIndex === index ? '−' : '+'}</span>
              </button>
              <div className="faq-answer">
                <p>{item.answer}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
