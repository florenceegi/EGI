import React from 'react';
import './TransparencyV4.css';

/**
 * Blocco "E noi cosa ci guadagniamo?"
 * Trasparenza sulle percentuali
 */
export default function TransparencyV4() {
    return (
        <section className="transparency-v4">
            <div className="transparency-v4__container">

                <h2 className="transparency-v4__title">
                    E noi cosa ci guadagniamo?
                </h2>

                <p className="transparency-v4__intro">
                    Bella domanda. Ecco come funziona, in totale trasparenza:
                </p>

                <div className="transparency-v4__distribution">
                    <h3 className="transparency-v4__distribution-title">
                        Quando un EGI viene venduto, il ricavato si distribuisce così:
                    </h3>

                    <div className="transparency-v4__bars">
                        <div className="transparency-v4__bar transparency-v4__bar--creator">
                            <div className="transparency-v4__bar-fill" style={{width: '68%'}}></div>
                            <div className="transparency-v4__bar-info">
                                <span className="transparency-v4__bar-percent">68%</span>
                                <span className="transparency-v4__bar-label">Creatore (tu)</span>
                            </div>
                        </div>

                        <div className="transparency-v4__bar transparency-v4__bar--epp">
                            <div className="transparency-v4__bar-fill" style={{width: '20%'}}></div>
                            <div className="transparency-v4__bar-info">
                                <span className="transparency-v4__bar-percent">20%</span>
                                <span className="transparency-v4__bar-label">Progetto Ambientale (EPP)</span>
                            </div>
                        </div>

                        <div className="transparency-v4__bar transparency-v4__bar--natan">
                            <div className="transparency-v4__bar-fill" style={{width: '10%'}}></div>
                            <div className="transparency-v4__bar-info">
                                <span className="transparency-v4__bar-percent">10%</span>
                                <span className="transparency-v4__bar-label">Natan (piattaforma)</span>
                            </div>
                        </div>

                        <div className="transparency-v4__bar transparency-v4__bar--frangette">
                            <div className="transparency-v4__bar-fill" style={{width: '2%'}}></div>
                            <div className="transparency-v4__bar-info">
                                <span className="transparency-v4__bar-percent">2%</span>
                                <span className="transparency-v4__bar-label">Frangette (associazione)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="transparency-v4__promise">
                    <span className="transparency-v4__promise-icon">🤝</span>
                    <p>
                        <strong>Nessun costo nascosto. Nessun abbonamento.</strong><br />
                        Guadagniamo solo quando guadagni tu.
                    </p>
                </div>

            </div>
        </section>
    );
}
