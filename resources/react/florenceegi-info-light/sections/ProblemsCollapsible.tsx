import React from 'react';
import { getTranslation } from '../../florenceegi-info/utils/translations';
import Collapsible from '../components/Collapsible';
import './ProblemsCollapsible.css';

/**
 * Problems Collapsible
 * Primi 4-6 problemi sempre visibili, resto in collapse
 */
export default function ProblemsCollapsible() {
    const problems = [
        {
            icon: '💸',
            title: getTranslation('problems.p1_title', 'Commissioni Esorbitanti'),
            old: getTranslation('problems.p1_old', 'Fee 15-30%, zero controllo'),
            new: getTranslation('problems.p1_new', 'Fee 10% dinamica (fino a 5%), royalty 4.5% perpetue'),
            priority: true
        },
        {
            icon: '🔍',
            title: getTranslation('problems.p2_title', 'Autenticità Dubbia'),
            old: getTranslation('problems.p2_old', 'Certificati cartacei falsificabili'),
            new: getTranslation('problems.p2_new', 'Certificato blockchain immutabile, QR verificabile'),
            priority: true
        },
        {
            icon: '🔐',
            title: getTranslation('problems.p3_title', 'Incubo Wallet'),
            old: getTranslation('problems.p3_old', 'Seed phrase, rischio perdita'),
            new: getTranslation('problems.p3_new', 'Wallet invisibile o export, paghi con carta'),
            priority: true
        },
        {
            icon: '📈',
            title: getTranslation('problems.p4_title', 'Speculazione Vuota'),
            old: getTranslation('problems.p4_old', 'Pump & dump, zero utilità'),
            new: getTranslation('problems.p4_new', 'Asset REALI, 20% impatto ambientale automatico'),
            priority: true
        },
        {
            icon: '🎭',
            title: getTranslation('problems.p5_title', 'Furto Opere'),
            old: getTranslation('problems.p5_old', 'Provenance opaca'),
            new: getTranslation('problems.p5_new', 'Storia on-chain, tracciabilità totale'),
            priority: true
        },
        {
            icon: '💰',
            title: getTranslation('problems.p6_title', 'Royalty Fantasma'),
            old: getTranslation('problems.p6_old', 'Accordi verbali non rispettati'),
            new: getTranslation('problems.p6_new', 'Smart contract trustless, pagamenti istantanei'),
            priority: true
        },
        // --- Gli altri visibili solo con expand ---
        {
            icon: '🔒',
            title: getTranslation('problems.p7_title', 'Labirinto GDPR'),
            old: getTranslation('problems.p7_old', 'Dati venduti, privacy zero'),
            new: getTranslation('problems.p7_new', 'GDPR by design, export 1-click, oblio')
        },
        {
            icon: '⚖️',
            title: getTranslation('problems.p8_title', 'Blocco Normativo'),
            old: getTranslation('problems.p8_old', 'Rischi legali, MiCA, KYC'),
            new: getTranslation('problems.p8_new', 'MiCA-safe, PSP autorizzati, zero rischi')
        },
        {
            icon: '⏱️',
            title: getTranslation('problems.p9_title', 'Pagamenti Lenti'),
            old: getTranslation('problems.p9_old', 'Bonifici T+7, fee alte'),
            new: getTranslation('problems.p9_new', 'Settlement T+2, 4 metodi pagamento')
        },
        {
            icon: '❓',
            title: getTranslation('problems.p10_title', 'Valore Ignoto'),
            old: getTranslation('problems.p10_old', 'Prezzi a caso'),
            new: getTranslation('problems.p10_new', 'NATAN AI valuation e market analysis')
        },
        {
            icon: '👤',
            title: getTranslation('problems.p11_title', 'Sei un Numero'),
            old: getTranslation('problems.p11_old', 'Algoritmi opachi'),
            new: getTranslation('problems.p11_new', 'Mecenatismo certificato, community reale')
        },
        {
            icon: '📋',
            title: getTranslation('problems.p12_title', 'Caos Fiscale'),
            old: getTranslation('problems.p12_old', 'Gestione manuale errori'),
            new: getTranslation('problems.p12_new', 'Fatturazione SDI auto, report commercialista')
        }
    ];

    // Primi 6 sempre visibili
    const visibleProblems = problems.filter(p => p.priority);
    const hiddenProblems = problems.filter(p => !p.priority);

    const renderProblemCard = (problem: typeof problems[0], index: number) => (
        <div key={index} className="problems-coll__card">
            <div className="problems-coll__card-icon">{problem.icon}</div>
            <h3 className="problems-coll__card-title">{problem.title}</h3>
            <div className="problems-coll__comparison">
                <div className="problems-coll__old">
                    <span className="problems-coll__label">❌ Prima</span>
                    <span className="problems-coll__text">{problem.old}</span>
                </div>
                <div className="problems-coll__new">
                    <span className="problems-coll__label">✅ Adesso</span>
                    <span className="problems-coll__text">{problem.new}</span>
                </div>
            </div>
        </div>
    );

    return (
        <section className="problems-coll">
            <div className="problems-coll__container">
                {/* Header */}
                <header className="problems-coll__header">
                    <h2 className="problems-coll__title">
                        {getTranslation('problems.title', 'Cosa Risolviamo Davvero')}
                    </h2>
                    <p className="problems-coll__subtitle">
                        {getTranslation('problems.subtitle', 'E Perché Ti Serve')}
                    </p>
                </header>

                {/* Problemi sempre visibili (primi 6) */}
                <div className="problems-coll__grid">
                    {visibleProblems.map((problem, index) => renderProblemCard(problem, index))}
                </div>

                {/* Problemi nascosti (altri 6) */}
                <Collapsible
                    triggerTextClosed="Vedi tutti i 12 problemi che risolviamo"
                    triggerTextOpen="Nascondi problemi aggiuntivi"
                >
                    <div className="problems-coll__grid problems-coll__grid--hidden">
                        {hiddenProblems.map((problem, index) => renderProblemCard(problem, index + 6))}
                    </div>
                </Collapsible>
            </div>
        </section>
    );
}
