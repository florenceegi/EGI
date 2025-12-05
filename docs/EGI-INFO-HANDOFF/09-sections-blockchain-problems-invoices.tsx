// ============================================
// BlockchainSimpleV4.tsx
// ============================================
import React, { useState } from 'react';
import './BlockchainSimpleV4.css';

interface BlockchainSimpleV4Props {
    className?: string;
}

const BlockchainSimpleV4: React.FC<BlockchainSimpleV4Props> = ({ className = '' }) => {
    const [expertMode, setExpertMode] = useState(false);

    return (
        <section className={`blockchain-v4 ${className}`}>
            <div className="blockchain-v4__container">
                <h2 className="blockchain-v4__title">
                    Non ti accorgerai nemmeno che sotto c'è la blockchain
                </h2>
                <p className="blockchain-v4__subtitle">Per te sarà come usare Amazon</p>

                <div className="blockchain-v4__features">
                    <div className="blockchain-v4__feature">
                        <span className="blockchain-v4__feature-icon">💶</span>
                        <span className="blockchain-v4__feature-text">Paghi in euro</span>
                    </div>
                    <div className="blockchain-v4__feature">
                        <span className="blockchain-v4__feature-icon">🚫</span>
                        <span className="blockchain-v4__feature-text">Nessun wallet</span>
                    </div>
                    <div className="blockchain-v4__feature">
                        <span className="blockchain-v4__feature-icon">🔓</span>
                        <span className="blockchain-v4__feature-text">Nessuna chiave segreta</span>
                    </div>
                    <div className="blockchain-v4__feature">
                        <span className="blockchain-v4__feature-icon">₿</span>
                        <span className="blockchain-v4__feature-text blockchain-v4__feature-text--strike">Nessuna crypto</span>
                    </div>
                </div>

                <div className="blockchain-v4__expert-section">
                    <div className="blockchain-v4__expert-question">
                        <span className="blockchain-v4__expert-icon">🎓</span>
                        <span>Ma se sei un esperto?</span>
                    </div>

                    <button
                        className={`blockchain-v4__toggle ${expertMode ? 'blockchain-v4__toggle--active' : ''}`}
                        onClick={() => setExpertMode(!expertMode)}
                    >
                        <span className="blockchain-v4__toggle-label">Modalità Esperto</span>
                        <span className={`blockchain-v4__toggle-switch ${expertMode ? 'blockchain-v4__toggle-switch--on' : ''}`}>
                            <span className="blockchain-v4__toggle-dot"></span>
                        </span>
                    </button>

                    {expertMode && (
                        <div className="blockchain-v4__expert-features">
                            <p className="blockchain-v4__expert-intro">La piattaforma si adatta alle tue competenze:</p>
                            <ul className="blockchain-v4__expert-list">
                                <li>Collega il tuo wallet Algorand</li>
                                <li>Visualizza le transazioni on-chain</li>
                                <li>Esporta le chiavi dei tuoi asset</li>
                                <li>Accedi alle API avanzate</li>
                                <li>Interagisci direttamente con gli smart contract</li>
                            </ul>
                        </div>
                    )}
                </div>

                <div className="blockchain-v4__comparison">
                    <div className="blockchain-v4__comparison-side blockchain-v4__comparison-side--them">
                        <h4>Altri servizi blockchain</h4>
                        <ul>
                            <li>❌ Devi capire cosa sono gli NFT</li>
                            <li>❌ Devi creare un wallet</li>
                            <li>❌ Devi comprare crypto</li>
                            <li>❌ Rischi di perdere le chiavi</li>
                            <li>❌ Gas fee imprevedibili</li>
                        </ul>
                    </div>
                    <div className="blockchain-v4__comparison-side blockchain-v4__comparison-side--us">
                        <h4>FlorenceEGI</h4>
                        <ul>
                            <li>✓ Ti registri come su qualsiasi sito</li>
                            <li>✓ Paghi con carta o PayPal</li>
                            <li>✓ Tutto automatico dietro le quinte</li>
                            <li>✓ Blockchain Algorand: veloce, ecologica</li>
                            <li>✓ Costi fissi e trasparenti</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default BlockchainSimpleV4;

// ============================================
// ProblemsV4.tsx
// ============================================
import React from 'react';
import './ProblemsV4.css';

interface Problem {
    emoji: string;
    problem: string;
    solution: string;
}

const problems: Problem[] = [
    { emoji: '💸', problem: 'Vendi un\'opera e chi la rivende guadagna senza darti niente', solution: 'Royalties automatiche su ogni rivendita, per sempre' },
    { emoji: '🖼️', problem: 'Qualcuno copia il tuo lavoro e non puoi dimostrare che è tuo', solution: 'Certificazione blockchain con data e ora immutabili' },
    { emoji: '📱', problem: 'Vendite su Instagram con rischio di truffe e pagamenti incerti', solution: 'Pagamento sicuro: il cliente paga prima, tu consegni dopo' },
    { emoji: '📣', problem: 'Fare marketing costa e non sai da dove cominciare', solution: 'Marketplace integrato con visibilità automatica' },
    { emoji: '🪙', problem: 'I tuoi clienti non hanno crypto e non sanno cosa sono', solution: 'Pagano in euro con carta o PayPal, come su Amazon' },
    { emoji: '🌍', problem: 'Vendere all\'estero è complicato: spedizioni, dogane, tasse', solution: 'EGI digitali: niente spedizioni, niente dogana, tutto istantaneo' },
    { emoji: '🏚️', problem: 'Paura che la piattaforma chiuda e perdi tutto', solution: 'I tuoi EGI esistono sulla blockchain, permanenti e tuoi' },
    { emoji: '💰', problem: 'Aprire un\'attività costa: sito, e-commerce, hosting', solution: 'Gratis. Zero costi fissi. Paghi solo sulle vendite' }
];

const ProblemsV4: React.FC<{className?: string}> = ({ className = '' }) => {
    return (
        <section className={`problems-v4 ${className}`}>
            <div className="problems-v4__container">
                <h2 className="problems-v4__title">8 Problemi Che Non Avrai Mai Più</h2>
                <p className="problems-v4__intro">Ogni problema reale che abbiamo risolto</p>

                <div className="problems-v4__grid">
                    {problems.map((item, index) => (
                        <div key={index} className="problems-v4__card">
                            <div className="problems-v4__card-header">
                                <span className="problems-v4__emoji">{item.emoji}</span>
                                <span className="problems-v4__number">#{index + 1}</span>
                            </div>
                            <div className="problems-v4__content">
                                <div className="problems-v4__problem">
                                    <span className="problems-v4__label problems-v4__label--problem">PROBLEMA</span>
                                    <p>{item.problem}</p>
                                </div>
                                <div className="problems-v4__arrow">→</div>
                                <div className="problems-v4__solution">
                                    <span className="problems-v4__label problems-v4__label--solution">SOLUZIONE</span>
                                    <p>{item.solution}</p>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                <div className="problems-v4__bottom">
                    <p className="problems-v4__tagline">
                        <span className="problems-v4__highlight">Non promesse.</span> Soluzioni già funzionanti.
                    </p>
                </div>
            </div>
        </section>
    );
};

export default ProblemsV4;

// ============================================
// InvoicesV4.tsx
// ============================================
import React from 'react';
import './InvoicesV4.css';

const InvoicesV4: React.FC<{className?: string}> = ({ className = '' }) => {
    return (
        <section className={`invoices-v4 ${className}`}>
            <div className="invoices-v4__container">
                <div className="invoices-v4__content">
                    <div className="invoices-v4__icon">📋</div>
                    <h2 className="invoices-v4__title">E le Fatture? Tutto in Ordine.</h2>
                    <p className="invoices-v4__text">
                        Per ogni vendita hai a disposizione tutta la documentazione che il tuo commercialista necessita.
                    </p>

                    <div className="invoices-v4__features">
                        <div className="invoices-v4__feature">
                            <span className="invoices-v4__feature-icon">✓</span>
                            <span>Tutto in ordine</span>
                        </div>
                        <div className="invoices-v4__feature">
                            <span className="invoices-v4__feature-icon">✓</span>
                            <span>Tutto scaricabile</span>
                        </div>
                        <div className="invoices-v4__feature">
                            <span className="invoices-v4__feature-icon">✓</span>
                            <span>Senza grattacapi per te</span>
                        </div>
                    </div>

                    <div className="invoices-v4__note">
                        <span className="invoices-v4__note-icon">💡</span>
                        <p>FlorenceEGI non è il tuo commercialista, ma ti fornisce tutto ciò che serve per essere in regola.</p>
                    </div>
                </div>

                <div className="invoices-v4__visual">
                    <div className="invoices-v4__document">
                        <div className="invoices-v4__doc-header">
                            <span className="invoices-v4__doc-icon">📄</span>
                            <span className="invoices-v4__doc-title">Riepilogo Vendita</span>
                        </div>
                        <div className="invoices-v4__doc-lines">
                            <div className="invoices-v4__doc-line"></div>
                            <div className="invoices-v4__doc-line invoices-v4__doc-line--short"></div>
                            <div className="invoices-v4__doc-line"></div>
                            <div className="invoices-v4__doc-line invoices-v4__doc-line--medium"></div>
                        </div>
                        <div className="invoices-v4__doc-button">
                            <span>⬇️ Scarica PDF</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default InvoicesV4;
