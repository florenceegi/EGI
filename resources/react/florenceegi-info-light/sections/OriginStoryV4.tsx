import React from 'react';
import './OriginStoryV4.css';

/**
 * Origin Story Section V4
 * La storia personale di Fabio e la nascita di FlorenceEGI
 * "Cinque anni, cinque riscritture, cinque percento"
 */
export default function OriginStoryV4() {
    return (
        <section className="origin-story-v4">
            <div className="origin-story-v4__container">
                
                {/* Titolo */}
                <h2 className="origin-story-v4__title">
                    Come nasce <span className="text-gold">FlorenceEGI</span>
                </h2>

                {/* La storia */}
                <div className="origin-story-v4__story">
                    
                    <p className="origin-story-v4__opening">
                        Era il mio primo libro. 
                        <br />
                        Ci avevo lavorato <strong>cinque anni</strong>. 
                        <br />
                        Riscritto completamente <strong>cinque volte</strong>.
                    </p>

                    <blockquote className="origin-story-v4__quote">
                        <p>"Ti tieni il 5%"</p>
                        <cite>— L'editore</cite>
                    </blockquote>

                    <p className="origin-story-v4__impact">
                        <span className="origin-story-v4__highlight">Cinque anni di lavoro.</span>
                        <span className="origin-story-v4__highlight">Cinque riscritture.</span>
                        <span className="origin-story-v4__highlight origin-story-v4__highlight--gold">Cinque percento.</span>
                    </p>

                    <p className="origin-story-v4__reaction">
                        Quella rabbia non l'ho mandata giù.
                        <br />
                        <strong>L'ho trasformata.</strong>
                    </p>

                    <div className="origin-story-v4__evolution">
                        <div className="origin-story-v4__evolution-step">
                            <span className="origin-story-v4__evolution-icon">📚</span>
                            <p>Nacque <strong>AutoBookNft</strong> — un modo per gli autori di possedere davvero le proprie opere.</p>
                        </div>
                        <div className="origin-story-v4__evolution-arrow">↓</div>
                        <div className="origin-story-v4__evolution-step">
                            <span className="origin-story-v4__evolution-icon">💡</span>
                            <p>Poi ho capito: il problema non era solo mio, non era solo degli scrittori.</p>
                        </div>
                        <div className="origin-story-v4__evolution-arrow">↓</div>
                        <div className="origin-story-v4__evolution-step">
                            <span className="origin-story-v4__evolution-icon">🌍</span>
                            <p>Era di <strong>chiunque</strong> avesse qualcosa di valore da proteggere.</p>
                        </div>
                    </div>

                    <div className="origin-story-v4__question">
                        <p>
                            Hai mai avuto un'idea che sapevi valere qualcosa?
                        </p>
                        <p className="origin-story-v4__question-detail">
                            Il primo pensiero: <em>"Quanto mi costa metterla sul mercato?"</em>
                            <br />
                            Il secondo: <em>"E se me la copiano?"</em>
                        </p>
                    </div>

                    <p className="origin-story-v4__solution">
                        Ho costruito <strong>FlorenceEGI</strong> per eliminare questi ostacoli.
                    </p>

                    <p className="origin-story-v4__conclusion">
                        Oggi, se hai qualcosa che vale, puoi trasformarlo in un <strong>EGI</strong>: 
                        <br />
                        un certificato digitale che lo rende tuo, protetto, verificabile — <em>in pochi click</em>.
                    </p>

                </div>

                {/* Motto finale */}
                <div className="origin-story-v4__motto">
                    <p className="origin-story-v4__motto-line">
                        Se esiste, <span className="text-gold">Egizzalo</span>.
                    </p>
                    <p className="origin-story-v4__motto-line origin-story-v4__motto-line--emphasis">
                        Perché se lo Egizzi... <span className="text-gold">vale</span>.
                    </p>
                </div>

                {/* Firma */}
                <div className="origin-story-v4__signature">
                    <p>— Fabio Cherici, Fondatore</p>
                </div>

            </div>
        </section>
    );
}
