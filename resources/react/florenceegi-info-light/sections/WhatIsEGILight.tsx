import React from 'react';
import { getTranslation } from '../../florenceegi-info/utils/translations';
import './WhatIsEGILight.css';

/**
 * Cos'è un EGI?
 * Mini blocco esplicativo prima dei "3 Click"
 */
export default function WhatIsEGILight() {
    return (
        <section className="what-egi-light">
            <div className="what-egi-light__container">
                <div className="what-egi-light__content">
                    <h2 className="what-egi-light__title">
                        {getTranslation('what_egi.title', "Cos'è un EGI?")}
                    </h2>

                    <ul className="what-egi-light__list">
                        <li>
                            <span className="what-egi-light__bullet">📜</span>
                            <span>
                                {getTranslation(
                                    'what_egi.point1',
                                    'È un certificato digitale unico, registrato su blockchain'
                                )}
                            </span>
                        </li>
                        <li>
                            <span className="what-egi-light__bullet">🔗</span>
                            <span>
                                {getTranslation(
                                    'what_egi.point2',
                                    'È collegato a un oggetto reale o digitale (opera, prodotto, documento, esperienza)'
                                )}
                            </span>
                        </li>
                        <li>
                            <span className="what-egi-light__bullet">📋</span>
                            <span>
                                {getTranslation(
                                    'what_egi.point3',
                                    'Dentro ci sono: autore, proprietario, storia delle vendite, quota destinata ai progetti ambientali'
                                )}
                            </span>
                        </li>
                    </ul>

                    <div className="what-egi-light__reassurance">
                        <span className="what-egi-light__reassurance-icon">💚</span>
                        <p>
                            {getTranslation(
                                'what_egi.reassurance',
                                'Non devi capire la blockchain: per te è come un normale certificato di proprietà online.'
                            )}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    );
}
