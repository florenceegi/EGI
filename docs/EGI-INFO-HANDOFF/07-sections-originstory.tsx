// ============================================
// OriginStoryV4.tsx - LA STORIA PERSONALE DI FABIO
// "Cinque anni, cinque riscritture, cinque percento"
// ============================================
import React from 'react';
import './OriginStoryV4.css';

export default function OriginStoryV4() {
    return (
        <section className="origin-story-v4">
            <div className="origin-story-v4__container">
                
                <h2 className="origin-story-v4__title">
                    Come nasce <span className="text-gold">FlorenceEGI</span>
                </h2>

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

                <div className="origin-story-v4__motto">
                    <p className="origin-story-v4__motto-line">
                        Se esiste, <span className="text-gold">Egizzalo</span>.
                    </p>
                    <p className="origin-story-v4__motto-line origin-story-v4__motto-line--emphasis">
                        Perché se lo Egizzi... <span className="text-gold">vale</span>.
                    </p>
                </div>

                <div className="origin-story-v4__signature">
                    <p>— Fabio Cherici, Fondatore</p>
                </div>

            </div>
        </section>
    );
}

// ============================================
// OriginStoryV4.css
// ============================================
/*
.origin-story-v4 {
    min-height: 100vh;
    background: linear-gradient(180deg, #0a0a0f 0%, #0d0d14 50%, #0a0a0f 100%);
    padding: 80px 24px 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.origin-story-v4__container {
    max-width: 800px;
    width: 100%;
}

.origin-story-v4__title {
    font-size: clamp(2rem, 5vw, 3rem);
    font-weight: 700;
    color: #ffffff;
    text-align: center;
    margin-bottom: 60px;
}

.origin-story-v4__story {
    display: flex;
    flex-direction: column;
    gap: 40px;
}

.origin-story-v4__opening {
    font-size: clamp(1.2rem, 3vw, 1.5rem);
    color: #c0c0c8;
    line-height: 1.8;
    text-align: center;
}

.origin-story-v4__opening strong {
    color: #ffffff;
}

.origin-story-v4__quote {
    margin: 20px 0;
    padding: 40px;
    background: rgba(255, 255, 255, 0.02);
    border-left: 4px solid #d4af37;
    border-radius: 0 16px 16px 0;
}

.origin-story-v4__quote p {
    font-size: clamp(1.5rem, 4vw, 2.2rem);
    font-style: italic;
    color: #ffffff;
    margin: 0;
}

.origin-story-v4__quote cite {
    display: block;
    margin-top: 16px;
    font-size: 1rem;
    color: #707078;
    font-style: normal;
}

.origin-story-v4__impact {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    padding: 40px 0;
}

.origin-story-v4__highlight {
    font-size: clamp(1.1rem, 3vw, 1.4rem);
    color: #808088;
    font-weight: 500;
    opacity: 0;
    animation: fadeInSequence 0.6s ease-out forwards;
}

.origin-story-v4__highlight:nth-child(1) { animation-delay: 0.2s; }
.origin-story-v4__highlight:nth-child(2) { animation-delay: 0.6s; }
.origin-story-v4__highlight:nth-child(3) { animation-delay: 1s; }

.origin-story-v4__highlight--gold {
    font-size: clamp(1.3rem, 4vw, 1.8rem);
    color: #d4af37 !important;
    font-weight: 700;
}

@keyframes fadeInSequence {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.origin-story-v4__reaction {
    font-size: clamp(1.2rem, 3vw, 1.5rem);
    color: #c0c0c8;
    text-align: center;
    line-height: 1.8;
}

.origin-story-v4__reaction strong {
    color: #d4af37;
    font-size: 1.2em;
}

.origin-story-v4__evolution {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    padding: 40px 0;
}

.origin-story-v4__evolution-step {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    max-width: 600px;
    padding: 24px;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 16px;
    transition: all 0.3s ease;
}

.origin-story-v4__evolution-step:hover {
    background: rgba(212, 175, 55, 0.05);
    border-color: rgba(212, 175, 55, 0.2);
}

.origin-story-v4__evolution-icon {
    font-size: 2rem;
    flex-shrink: 0;
}

.origin-story-v4__evolution-step p {
    margin: 0;
    font-size: 1.1rem;
    color: #c0c0c8;
    line-height: 1.6;
}

.origin-story-v4__evolution-step strong {
    color: #ffffff;
}

.origin-story-v4__evolution-arrow {
    font-size: 1.5rem;
    color: #d4af37;
    opacity: 0.6;
}

.origin-story-v4__question {
    text-align: center;
    padding: 40px;
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.08), rgba(212, 175, 55, 0.02));
    border-radius: 20px;
    border: 1px solid rgba(212, 175, 55, 0.15);
}

.origin-story-v4__question > p:first-child {
    font-size: clamp(1.3rem, 3vw, 1.6rem);
    color: #ffffff;
    font-weight: 600;
    margin: 0 0 20px 0;
}

.origin-story-v4__question-detail {
    font-size: 1.1rem;
    color: #a0a0a8;
    line-height: 1.8;
    margin: 0;
}

.origin-story-v4__question-detail em {
    color: #c0c0c8;
}

.origin-story-v4__solution {
    font-size: clamp(1.2rem, 3vw, 1.5rem);
    color: #c0c0c8;
    text-align: center;
    line-height: 1.6;
}

.origin-story-v4__solution strong {
    color: #d4af37;
}

.origin-story-v4__conclusion {
    font-size: clamp(1.1rem, 2.5vw, 1.3rem);
    color: #a0a0a8;
    text-align: center;
    line-height: 1.8;
    padding: 20px 0;
}

.origin-story-v4__conclusion strong {
    color: #d4af37;
}

.origin-story-v4__conclusion em {
    color: #ffffff;
}

.origin-story-v4__motto {
    margin-top: 60px;
    padding: 60px 40px;
    background: rgba(10, 10, 15, 0.8);
    border: 2px solid rgba(212, 175, 55, 0.3);
    border-radius: 24px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.origin-story-v4__motto::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.08) 0%, transparent 50%);
    pointer-events: none;
}

.origin-story-v4__motto-line {
    position: relative;
    font-size: clamp(1.5rem, 4vw, 2rem);
    color: #ffffff;
    margin: 0;
    line-height: 1.6;
}

.origin-story-v4__motto-line--emphasis {
    font-size: clamp(1.8rem, 5vw, 2.5rem);
    font-weight: 700;
    margin-top: 16px;
}

.origin-story-v4__signature {
    margin-top: 40px;
    text-align: center;
}

.origin-story-v4__signature p {
    font-size: 1rem;
    color: #707078;
    font-style: italic;
    margin: 0;
}

@media (max-width: 768px) {
    .origin-story-v4 {
        padding: 60px 16px 80px;
    }
    .origin-story-v4__title {
        margin-bottom: 40px;
    }
    .origin-story-v4__story {
        gap: 30px;
    }
    .origin-story-v4__quote {
        padding: 24px;
    }
    .origin-story-v4__evolution-step {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 12px;
    }
    .origin-story-v4__question {
        padding: 24px;
    }
    .origin-story-v4__motto {
        padding: 40px 24px;
        margin-top: 40px;
    }
}
*/
