/**
 * InvoicesV4 - Block 8
 * Documentazione fiscale e fatture
 */
import React from 'react';
import './InvoicesV4.css';

interface InvoicesV4Props {
    className?: string;
}

const InvoicesV4: React.FC<InvoicesV4Props> = ({ className = '' }) => {
    return (
        <section className={`invoices-v4 ${className}`}>
            <div className="invoices-v4__container">
                <div className="invoices-v4__content">
                    <div className="invoices-v4__icon">📋</div>

                    <h2 className="invoices-v4__title">
                        E le Fatture? Tutto in Ordine.
                    </h2>

                    <p className="invoices-v4__text">
                        Per ogni vendita hai a disposizione tutta la documentazione che il tuo
                        commercialista necessita.
                    </p>

                    <div className="invoices-v4__features">
                        <div className="invoices-v4__feature">
                            <span className="invoices-v4__feature-icon">✓</span>
                            <span>Tutto in ordine</span>
                        </div>
                        <div className="invoices-v4__feature">
                            <span className="invoices-v4__feature-icon">✓</span>
                            <span>Tutto scaricabile</span>
                        </div>
                        <div className="invoices-v4__feature">
                            <span className="invoices-v4__feature-icon">✓</span>
                            <span>Senza grattacapi per te</span>
                        </div>
                    </div>

                    <div className="invoices-v4__note">
                        <span className="invoices-v4__note-icon">💡</span>
                        <p>
                            FlorenceEGI non è il tuo commercialista, ma ti fornisce tutto
                            ciò che serve per essere in regola.
                        </p>
                    </div>
                </div>

                {/* Visual representation */}
                <div className="invoices-v4__visual">
                    <div className="invoices-v4__document">
                        <div className="invoices-v4__doc-header">
                            <span className="invoices-v4__doc-icon">📄</span>
                            <span className="invoices-v4__doc-title">Riepilogo Vendita</span>
                        </div>
                        <div className="invoices-v4__doc-lines">
                            <div className="invoices-v4__doc-line"></div>
                            <div className="invoices-v4__doc-line invoices-v4__doc-line--short"></div>
                            <div className="invoices-v4__doc-line"></div>
                            <div className="invoices-v4__doc-line invoices-v4__doc-line--medium"></div>
                        </div>
                        <div className="invoices-v4__doc-button">
                            <span>⬇️ Scarica PDF</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default InvoicesV4;
