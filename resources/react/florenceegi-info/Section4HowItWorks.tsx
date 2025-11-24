import React, { useEffect, useRef } from 'react';
import './Section4HowItWorks.css';

interface Step {
  number: number;
  icon: string;
  title: string;
  items: string[];
  timing?: string;
}

export default function Section4HowItWorks() {
  const sectionRef = useRef<HTMLElement>(null);
  const t = window.florenceEgiTranslations?.how;

  const steps: Step[] = [
    {
      number: 1,
      icon: '📸',
      title: t?.step1_title || 'Carica',
      items: [
        t?.step1_upload || 'Upload foto/video opera',
        t?.step1_description || 'Compila descrizione (NATAN AI ti aiuta)',
        t?.step1_price || 'Imposta prezzo (suggerimenti basati mercato)',
        t?.step1_epp || 'Scegli EPP (progetto ambientale da sostenere)',
      ],
    },
    {
      number: 2,
      icon: '⚡',
      title: t?.step2_title || 'Egizza',
      items: [
        t?.step2_certificate || 'Sistema genera certificato blockchain',
        t?.step2_hash || 'Hash immutabile su Algorand',
        t?.step2_qr || 'QR code verifica pubblica',
        t?.step2_preview || 'Anteprima marketplace pronta',
      ],
      timing: t?.step2_timing || '< 5 secondi',
    },
    {
      number: 3,
      icon: '💰',
      title: t?.step3_title || 'Vendi',
      items: [
        t?.step3_payment || 'Cliente paga (carta/bonifico/crypto/Egili)',
        t?.step3_split || 'Split automatico (Creator 68% / EPP 20% / Piattaforma 10% / Ass. 2%)',
        t?.step3_transfer || 'EGI trasferito wallet cliente (o auto-generato)',
        t?.step3_royalty || 'Royalty 4.5% per sempre su rivendite',
      ],
    },
  ];

  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
          }
        });
      },
      { threshold: 0.2 }
    );

    const stepCards = sectionRef.current?.querySelectorAll('.step-card');
    stepCards?.forEach((card) => observer.observe(card));

    return () => observer.disconnect();
  }, []);

  return (
    <section ref={sectionRef} className="section-how-it-works">
      <div className="container">
        <header className="section-header">
          <h2 className="section-title">{t?.title || '3 Click, 0 Complicazioni'}</h2>
          <p className="section-subtitle">{t?.subtitle || 'Semplice come dovrebbe essere'}</p>
        </header>

        <div className="steps-timeline">
          {steps.map((step, index) => (
            <React.Fragment key={step.number}>
              <div className="step-card" style={{ animationDelay: `${index * 0.2}s` }}>
                <div className="step-number">{step.number}</div>
                <div className="step-icon">{step.icon}</div>
                <h3 className="step-title">{step.title}</h3>
                {step.timing && <div className="step-timing">{step.timing}</div>}
                <ul className="step-items">
                  {step.items.map((item, i) => (
                    <li key={i} style={{ animationDelay: `${index * 0.2 + i * 0.1}s` }}>
                      <span className="item-bullet">→</span>
                      <span className="item-text">{item}</span>
                    </li>
                  ))}
                </ul>
              </div>
              
              {index < steps.length - 1 && (
                <div className="step-connector" style={{ animationDelay: `${index * 0.2 + 0.3}s` }}>
                  <div className="connector-line"></div>
                  <div className="connector-arrow">→</div>
                </div>
              )}
            </React.Fragment>
          ))}
        </div>

        <div className="cta-container">
          <button className="cta-button primary">
            {t?.cta_start || 'Inizia Ora Gratis'}
          </button>
          <button className="cta-button secondary">
            {t?.cta_demo || 'Guarda Demo Live'}
          </button>
        </div>
      </div>
    </section>
  );
}
