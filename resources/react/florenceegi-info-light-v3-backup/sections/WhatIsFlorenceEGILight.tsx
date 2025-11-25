import React from 'react';
import { getTranslation } from '../../florenceegi-info/utils/translations';
import './WhatIsFlorenceEGILight.css';

/**
 * Cos'è FlorenceEGI, in parole semplici
 * Blocco esplicativo subito dopo l'Hero
 */
export default function WhatIsFlorenceEGILight() {
    return (
        <section className="what-florence-light">
            <div className="what-florence-light__container">
                <div className="what-florence-light__box">
                    <h2 className="what-florence-light__title">
                        {getTranslation('what_florence.title', "Cos'è FlorenceEGI, in parole semplici")}
                    </h2>

                    <p className="what-florence-light__intro">
                        {getTranslation(
                            'what_florence.intro',
                            "FlorenceEGI è un sito dove prendi un'opera (quadro, foto, prodotto, evento…) e le crei un certificato digitale chiamato EGI."
                        )}
                    </p>

                    <div className="what-florence-light__benefits">
                        <h3 className="what-florence-light__benefits-title">
                            {getTranslation('what_florence.benefits_title', 'Quel certificato:')}
                        </h3>
                        <ul className="what-florence-light__list">
                            <li>
                                <span className="what-florence-light__icon">✓</span>
                                <span>{getTranslation('what_florence.benefit1', 'Prova chi è il proprietario')}</span>
                            </li>
                            <li>
                                <span className="what-florence-light__icon">✓</span>
                                <span>{getTranslation('what_florence.benefit2', "Segue l'opera in tutte le vendite")}</span>
                            </li>
                            <li>
                                <span className="what-florence-light__icon">✓</span>
                                <span>{getTranslation('what_florence.benefit3', 'Stacca automaticamente il 20% per un progetto ambientale collegato')}</span>
                            </li>
                        </ul>
                    </div>

                    <p className="what-florence-light__note">
                        {getTranslation(
                            'what_florence.note',
                            'Stop. Niente tecnicismi. È come avere un notaio digitale che lavora per te 24/7.'
                        )}
                    </p>
                </div>
            </div>
        </section>
    );
}
