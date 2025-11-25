import React from 'react';
import './WhatIsEGIV4.css';

/**
 * Blocco "Cosa sono gli EGI"
 * Definizione EGI = Ecological Goods Invent
 */
export default function WhatIsEGIV4() {
    return (
        <section className="what-egi-v4">
            <div className="what-egi-v4__container">

                <h2 className="what-egi-v4__title">
                    Cosa sono gli <span className="text-gold">EGI</span>?
                </h2>

                <div className="what-egi-v4__acronym">
                    <div className="what-egi-v4__letter">
                        <span className="what-egi-v4__letter-big">E</span>
                        <span className="what-egi-v4__letter-word">cological</span>
                    </div>
                    <div className="what-egi-v4__letter">
                        <span className="what-egi-v4__letter-big">G</span>
                        <span className="what-egi-v4__letter-word">oods</span>
                    </div>
                    <div className="what-egi-v4__letter">
                        <span className="what-egi-v4__letter-big">I</span>
                        <span className="what-egi-v4__letter-word">nvent</span>
                    </div>
                </div>

                <p className="what-egi-v4__subtitle">
                    (Beni Ecologici d'Ingegno)
                </p>

                <div className="what-egi-v4__description">
                    <p>
                        Un <strong>EGI</strong> è un oggetto digitale certificato su blockchain
                        che rappresenta la tua creazione. Non è un semplice file: è un
                        <strong> certificato di proprietà</strong> impossibile da falsificare.
                    </p>
                </div>

                <div className="what-egi-v4__features">
                    <div className="what-egi-v4__feature">
                        <span className="what-egi-v4__feature-icon">🎨</span>
                        <div>
                            <strong>Invent</strong>
                            <span>Un'opera creata da una persona vera, non da un computer</span>
                        </div>
                    </div>
                    <div className="what-egi-v4__feature">
                        <span className="what-egi-v4__feature-icon">💎</span>
                        <div>
                            <strong>Goods</strong>
                            <span>Un bene con valore reale, che si può comprare e rivendere</span>
                        </div>
                    </div>
                    <div className="what-egi-v4__feature">
                        <span className="what-egi-v4__feature-icon">🌱</span>
                        <div>
                            <strong>Ecological</strong>
                            <span>Almeno il 20% di ogni vendita va a progetti per l'ambiente</span>
                        </div>
                    </div>
                </div>

                <p className="what-egi-v4__conclusion">
                    Può essere un'opera d'arte, una foto, un video, un documento,
                    un prodotto artigianale, un servizio... <strong>qualsiasi cosa abbia valore</strong>.
                </p>

            </div>
        </section>
    );
}
