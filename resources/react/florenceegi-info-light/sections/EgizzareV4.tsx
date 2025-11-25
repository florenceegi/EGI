import React from 'react';
import './EgizzareV4.css';

/**
 * Blocco "Che significa Egizzare?"
 * Definizione + esempi pratici
 */
export default function EgizzareV4() {
    return (
        <section className="egizzare-v4" id="scopri">
            <div className="egizzare-v4__container">

                {/* Intro */}
                <div className="egizzare-v4__intro">
                    <h2 className="egizzare-v4__title">
                        Ma esattamente... che significa <span className="text-gold">"Egizzare"</span>?
                    </h2>

                    <p className="egizzare-v4__definition">
                        <strong>Egizzare</strong> significa trasformare qualcosa in un <strong>EGI</strong>.
                    </p>

                    <p className="egizzare-v4__explanation">
                        Cioè: prendere una tua creazione (un'opera, un prodotto, un documento, qualsiasi cosa)
                        e registrarla sulla blockchain come oggetto digitale certificato, con tutti i diritti
                        e i benefici che ne derivano.
                    </p>
                </div>

                {/* Cosa ottieni */}
                <div className="egizzare-v4__benefits">
                    <h3 className="egizzare-v4__benefits-title">Una volta Egizzato, quello che hai creato diventa:</h3>
                    <div className="egizzare-v4__benefits-grid">
                        <div className="egizzare-v4__benefit">
                            <span className="egizzare-v4__check">✓</span>
                            <div>
                                <strong>Certificato</strong>
                                <span>la prova che è tuo, per sempre</span>
                            </div>
                        </div>
                        <div className="egizzare-v4__benefit">
                            <span className="egizzare-v4__check">✓</span>
                            <div>
                                <strong>Tracciabile</strong>
                                <span>ogni passaggio di proprietà è registrato</span>
                            </div>
                        </div>
                        <div className="egizzare-v4__benefit">
                            <span className="egizzare-v4__check">✓</span>
                            <div>
                                <strong>Vendibile</strong>
                                <span>nel tuo negozio, a tutto il mondo</span>
                            </div>
                        </div>
                        <div className="egizzare-v4__benefit">
                            <span className="egizzare-v4__check">✓</span>
                            <div>
                                <strong>Redditizio</strong>
                                <span>guadagni a ogni rivendita, per sempre</span>
                            </div>
                        </div>
                        <div className="egizzare-v4__benefit">
                            <span className="egizzare-v4__check">✓</span>
                            <div>
                                <strong>Ecologico</strong>
                                <span>contribuisce alla tutela dell'ambiente</span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Esempi */}
                <div className="egizzare-v4__examples">
                    <div className="egizzare-v4__example">
                        <span className="egizzare-v4__emoji">🎨</span>
                        <span>Hai dipinto un quadro? → <strong>Egizzalo.</strong></span>
                    </div>
                    <div className="egizzare-v4__example">
                        <span className="egizzare-v4__emoji">📸</span>
                        <span>Hai scattato una foto pazzesca? → <strong>Egizzala.</strong></span>
                    </div>
                    <div className="egizzare-v4__example">
                        <span className="egizzare-v4__emoji">🎵</span>
                        <span>Hai composto una canzone? → <strong>Egizzala.</strong></span>
                    </div>
                    <div className="egizzare-v4__example">
                        <span className="egizzare-v4__emoji">🏺</span>
                        <span>Hai creato un vaso artigianale? → <strong>Egizzalo.</strong></span>
                    </div>
                    <div className="egizzare-v4__example">
                        <span className="egizzare-v4__emoji">📄</span>
                        <span>Hai scritto un contratto importante? → <strong>Egizzalo.</strong></span>
                    </div>
                </div>

            </div>
        </section>
    );
}
