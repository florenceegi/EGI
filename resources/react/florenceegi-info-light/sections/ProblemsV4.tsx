/**
 * ProblemsV4 - Block 7
 * Gli 8 problemi che FlorenceEGI risolve
 */
import React from 'react';
import './ProblemsV4.css';

interface ProblemsV4Props {
    className?: string;
}

interface Problem {
    emoji: string;
    problem: string;
    solution: string;
}

const problems: Problem[] = [
    {
        emoji: '💸',
        problem: 'Vendi un\'opera e chi la rivende guadagna senza darti niente',
        solution: 'Royalties automatiche su ogni rivendita, per sempre'
    },
    {
        emoji: '🖼️',
        problem: 'Qualcuno copia il tuo lavoro e non puoi dimostrare che è tuo',
        solution: 'Certificazione blockchain con data e ora immutabili'
    },
    {
        emoji: '📱',
        problem: 'Vendite su Instagram con rischio di truffe e pagamenti incerti',
        solution: 'Pagamento sicuro: il cliente paga prima, tu consegni dopo'
    },
    {
        emoji: '📣',
        problem: 'Fare marketing costa e non sai da dove cominciare',
        solution: 'Marketplace integrato con visibilità automatica'
    },
    {
        emoji: '🪙',
        problem: 'I tuoi clienti non hanno crypto e non sanno cosa sono',
        solution: 'Pagano in euro con carta o PayPal, come su Amazon'
    },
    {
        emoji: '🌍',
        problem: 'Vendere all\'estero è complicato: spedizioni, dogane, tasse',
        solution: 'EGI digitali: niente spedizioni, niente dogana, tutto istantaneo'
    },
    {
        emoji: '🏚️',
        problem: 'Paura che la piattaforma chiuda e perdi tutto',
        solution: 'I tuoi EGI esistono sulla blockchain, permanenti e tuoi'
    },
    {
        emoji: '💰',
        problem: 'Aprire un\'attività costa: sito, e-commerce, hosting',
        solution: 'Gratis. Zero costi fissi. Paghi solo sulle vendite'
    }
];

const ProblemsV4: React.FC<ProblemsV4Props> = ({ className = '' }) => {
    return (
        <section className={`problems-v4 ${className}`}>
            <div className="problems-v4__container">
                <h2 className="problems-v4__title">
                    8 Problemi Che Non Avrai Mai Più
                </h2>

                <p className="problems-v4__intro">
                    Ogni problema reale che abbiamo risolto
                </p>

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
                        <span className="problems-v4__highlight">Non promesse.</span>{' '}
                        Soluzioni già funzionanti.
                    </p>
                </div>
            </div>
        </section>
    );
};

export default ProblemsV4;
