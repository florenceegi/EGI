/**
 * BlockchainSimpleV4 - Block 5
 * "Non ti accorgerai nemmeno che sotto c'è la blockchain"
 */
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

                <p className="blockchain-v4__subtitle">
                    Per te sarà come usare Amazon
                </p>

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

                {/* Expert Toggle */}
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
                            <p className="blockchain-v4__expert-intro">
                                La piattaforma si adatta alle tue competenze:
                            </p>
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

                {/* Comparison */}
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
