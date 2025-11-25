import React from 'react';
import './Section12Pricing.css';

const getTranslation = (key: string, fallback: string): string => {
  const translations = (window as any).florenceEgiTranslations || {};
  return translations[key] || fallback;
};

export default function Section12Pricing() {
  return (
    <section className="section-12-pricing">
      <div className="container">
        <div className="section-header">
          <span className="section-number">12</span>
          <h2>{getTranslation('section12_title', 'Pricing Trasparente')}</h2>
          <p className="section-subtitle">
            {getTranslation('section12_subtitle', 'Fee chiare, nessuna sorpresa')}
          </p>
        </div>

        <div className="pricing-grid">
          {/* Fee Info */}
          <div className="pricing-card fee-info-card">
            <h3>{getTranslation('pricing_fee_title', 'Fee Piattaforma')}</h3>
            <div className="fee-highlight">
              <span className="fee-main">10%</span>
              <span className="fee-note">{getTranslation('pricing_fee_default', 'Fee standard su vendita primaria')}</span>
            </div>
            <p className="fee-description">
              {getTranslation('pricing_fee_dynamic', 
                'Fee dinamiche fino al 5% per volumi elevati. Condizioni personalizzate per creator professionisti e business.'
              )}
            </p>
          </div>

          {/* Primary Sale Distribution */}
          <div className="pricing-card distribution-card">
            <h3>{getTranslation('pricing_primary_title', 'Vendita Primaria (esempio €1,000)')}</h3>
            <div className="distribution-chart">
              <div className="chart-bar creator" style={{ width: '68%' }}>
                <span className="bar-label">Creator</span>
                <span className="bar-value">€680 (68%)</span>
              </div>
              <div className="chart-bar epp" style={{ width: '20%' }}>
                <span className="bar-label">EPP</span>
                <span className="bar-value">€200 (20%)</span>
              </div>
              <div className="chart-bar platform" style={{ width: '10%' }}>
                <span className="bar-label">Piattaforma</span>
                <span className="bar-value">€100 (10%)</span>
              </div>
              <div className="chart-bar association" style={{ width: '2%' }}>
                <span className="bar-label">APS</span>
                <span className="bar-value">€20 (2%)</span>
              </div>
            </div>
          </div>

          {/* Secondary Sale Distribution */}
          <div className="pricing-card distribution-card secondary">
            <h3>{getTranslation('pricing_secondary_title', 'Rivendita Secondaria (esempio €1,000)')}</h3>
            <div className="distribution-chart">
              <div className="chart-bar seller" style={{ width: '93%' }}>
                <span className="bar-label">Venditore</span>
                <span className="bar-value">€930 (93%)</span>
              </div>
              <div className="chart-bar royalty" style={{ width: '4.5%' }}>
                <span className="bar-label">Royalty Creator</span>
                <span className="bar-value">€45 (4.5%)</span>
              </div>
              <div className="chart-bar epp-sec" style={{ width: '1%' }}>
                <span className="bar-label">EPP</span>
                <span className="bar-value">€10 (1%)</span>
              </div>
              <div className="chart-bar platform-sec" style={{ width: '1.5%' }}>
                <span className="bar-label">Piattaforma</span>
                <span className="bar-value">€15 (1.5%)</span>
              </div>
            </div>
          </div>
        </div>

        {/* Comparison Highlight */}
        <div className="comparison-box">
          <div className="comparison-icon">💰</div>
          <div className="comparison-content">
            <p>
              <strong>{getTranslation('pricing_comparison_title', 'Creator guadagna 68% + 4.5% royalty PERPETUA')}</strong>
            </p>
            <p className="comparison-detail">
              {getTranslation('pricing_comparison', 
                'Confronto: OpenSea 2.5% (no royalty garantite), Saatchi Art 35% (no royalty).'
              )}
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}
