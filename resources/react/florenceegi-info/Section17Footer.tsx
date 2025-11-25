import React from 'react';
import './Section17Footer.css';

const getTranslation = (key: string, fallback: string): string => {
  const translations = (window as any).florenceEgiTranslations || {};
  return translations[key] || fallback;
};

export default function Section17Footer() {
  return (
    <footer className="section-17-footer">
      <div className="container">
        {/* Trust Badges */}
        <div className="trust-badges">
          <div className="badge">
            <span className="badge-icon">✓</span>
            <span>GDPR Compliant</span>
          </div>
          <div className="badge">
            <span className="badge-icon">✓</span>
            <span>MiCA Ready</span>
          </div>
          <div className="badge">
            <span className="badge-icon">✓</span>
            <span>Powered by Algorand</span>
          </div>
        </div>

        {/* Footer Links */}
        <div className="footer-links">
          <div className="links-column">
            <h4>{getTranslation('footer_legal', 'Legale')}</h4>
            <ul>
              <li><span className="coming-soon">{getTranslation('footer_privacy', 'Privacy Policy')}</span></li>
              <li><span className="coming-soon">{getTranslation('footer_terms', 'Termini & Condizioni')}</span></li>
              <li><span className="coming-soon">{getTranslation('footer_cookies', 'Cookie Policy')}</span></li>
            </ul>
          </div>
          <div className="links-column">
            <h4>{getTranslation('footer_support', 'Supporto')}</h4>
            <ul>
              <li><span className="coming-soon">{getTranslation('footer_contact', 'Contattaci')}</span></li>
              <li><span className="coming-soon">{getTranslation('footer_faq', 'FAQ')}</span></li>
            </ul>
          </div>
        </div>

        {/* Copyright */}
        <div className="footer-bottom">
          <div className="footer-logo">
            <span className="logo-text">FlorenceEGI</span>
          </div>
          <p className="copyright">
            © {new Date().getFullYear()} Florence EGI SRL. {getTranslation('footer_rights', 'Tutti i diritti riservati.')}
          </p>
        </div>
      </div>
    </footer>
  );
}
