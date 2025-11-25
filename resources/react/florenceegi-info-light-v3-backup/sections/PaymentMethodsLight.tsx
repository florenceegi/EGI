import React from 'react';
import { getTranslation } from '../../florenceegi-info/utils/translations';
import './PaymentMethodsLight.css';

/**
 * Payment Methods Light
 * Sempre visibile - 4 Modi di Pagare
 */
export default function PaymentMethodsLight() {
    const paymentMethods = [
        {
            level: getTranslation('payments.lvl1_title', 'Zero Crypto'),
            icon: '💳',
            description: getTranslation('payments.lvl1_desc', 'Carta/Bonifico, wallet invisibile.'),
            details: 'Non sai cos\'è una blockchain? Perfetto. Paga come su Amazon.',
            highlight: true
        },
        {
            level: getTranslation('payments.lvl2_title', 'Ho un Wallet'),
            icon: '📱',
            description: getTranslation('payments.lvl2_desc', 'Paga FIAT, ricevi su Pera/Defly.'),
            details: 'Hai già un wallet Algorand? Collegalo e ricevi lì.'
        },
        {
            level: getTranslation('payments.lvl3_title', 'Crypto Native'),
            icon: '🔗',
            description: getTranslation('payments.lvl3_desc', 'USDC/ETH via PSP autorizzati.'),
            details: 'Sei un crypto native? Paga in stablecoin, zero problemi.'
        },
        {
            level: getTranslation('payments.lvl4_title', 'Egili'),
            icon: '⭐',
            description: getTranslation('payments.lvl4_desc', 'Token interno fedeltà (no gas fee).'),
            details: 'Il nostro token di loyalty per pagamenti interni.'
        }
    ];

    return (
        <section className="payments-light">
            <div className="payments-light__container">
                {/* Header */}
                <header className="payments-light__header">
                    <h2 className="payments-light__title">
                        {getTranslation('payments.title', 'Inclusione Finanziaria Totale')}
                    </h2>
                    <p className="payments-light__subtitle">
                        4 modi di pagare. Scegli quello che preferisci.
                    </p>
                </header>

                {/* Payment cards */}
                <div className="payments-light__grid">
                    {paymentMethods.map((method, index) => (
                        <div 
                            key={index} 
                            className={`payments-light__card ${method.highlight ? 'payments-light__card--highlight' : ''}`}
                        >
                            <div className="payments-light__card-icon">{method.icon}</div>
                            <h3 className="payments-light__card-level">{method.level}</h3>
                            <p className="payments-light__card-description">{method.description}</p>
                            <p className="payments-light__card-details">{method.details}</p>
                        </div>
                    ))}
                </div>

                {/* Trust badge */}
                <div className="payments-light__trust">
                    <span className="payments-light__trust-icon">🔒</span>
                    <span>Tutti i pagamenti processati tramite PSP autorizzati e conformi PSD2</span>
                </div>
            </div>
        </section>
    );
}
