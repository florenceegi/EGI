// ============================================
// WhoCanUseV4.tsx
// ============================================
import React from 'react';
import './WhoCanUseV4.css';

interface WhoCanUseV4Props {
    className?: string;
}

const WhoCanUseV4: React.FC<WhoCanUseV4Props> = ({ className = '' }) => {
    return (
        <section className={`who-can-use-v4 ${className}`}>
            <div className="who-can-use-v4__container">
                
                <h2 className="who-can-use-v4__title">E tu, chi sei?</h2>
                
                <p className="who-can-use-v4__intro">
                    FlorenceEGI non è per tutti. È per chi ha qualcosa da offrire, 
                    qualcosa da cercare, o qualcosa da proteggere.
                    <br /><br />
                    Vediamo se ti riconosci.
                </p>

                {/* Creator */}
                <div className="who-can-use-v4__persona">
                    <h3 className="who-can-use-v4__persona-title">Sei qualcuno che crea.</h3>
                    <p>
                        Non importa cosa. Dipingi quadri o scatti fotografie. Scrivi romanzi 
                        o componi musica. Cucini ricette o progetti mobili. Insegni yoga o 
                        ripari biciclette.
                    </p>
                    <p>
                        Quello che fai ha valore. Ma finora, quel valore restava chiuso tra 
                        te e chi ti conosceva. FlorenceEGI lo apre al mondo — e lo protegge 
                        mentre viaggia.
                    </p>
                    <p className="who-can-use-v4__persona-name">
                        Qui ti chiamiamo <strong>Creator</strong>.
                    </p>
                </div>

                {/* Collector */}
                <div className="who-can-use-v4__persona">
                    <h3 className="who-can-use-v4__persona-title">Sei qualcuno che cerca.</h3>
                    <p>
                        Vuoi possedere qualcosa di autentico. Non l'ennesima copia, non 
                        l'ennesimo file scaricato. Qualcosa che abbia una storia, un autore, 
                        un certificato che dice: <em>questo è tuo, e nessun altro ce l'ha uguale.</em>
                    </p>
                    <p>
                        Magari vuoi supportare un artista che ami. O investire in qualcosa 
                        che potrebbe crescere di valore. O semplicemente avere la certezza 
                        che quello che compri è vero.
                    </p>
                    <p className="who-can-use-v4__persona-name">
                        Qui ti chiamiamo <strong>Collector</strong>.
                    </p>
                </div>

                {/* EPP */}
                <div className="who-can-use-v4__persona">
                    <h3 className="who-can-use-v4__persona-title">Sei qualcuno che rappresenta.</h3>
                    <p>
                        Un museo con opere da digitalizzare. Un comune con un patrimonio da 
                        valorizzare. Un'azienda con asset da certificare. Un'associazione 
                        con una storia da raccontare.
                    </p>
                    <p>
                        Hai bisogno di strumenti professionali, ma senza la complessità che 
                        di solito li accompagna. E hai bisogno che tutto sia in regola — 
                        GDPR, fatturazione, tracciabilità.
                    </p>
                    <p className="who-can-use-v4__persona-name">
                        Qui ti chiamiamo <strong>EPP</strong> — Ente Pubblico o Privato.
                    </p>
                </div>

                <div className="who-can-use-v4__closing">
                    <p>Tre mondi diversi. Un'unica piattaforma.</p>
                    <p>Perché alla fine, tutti vogliamo la stessa cosa: <strong>dare valore a ciò che conta</strong>.</p>
                </div>

                <div className="who-can-use-v4__motto">
                    <p>Se Esiste, <span className="text-gold">Egizzalo</span>.</p>
                    <p>Se lo Egizzi, <span className="text-gold">Vale</span>.</p>
                </div>

            </div>
        </section>
    );
};

export default WhoCanUseV4;

// ============================================
// CTAFinalV4.tsx
// ============================================
import React from 'react';
import './CTAFinalV4.css';

interface CTAFinalV4Props {
    className?: string;
}

const CTAFinalV4: React.FC<CTAFinalV4Props> = ({ className = '' }) => {
    return (
        <section className={`cta-final-v4 ${className}`}>
            <div className="cta-final-v4__container">
                
                <h2 className="cta-final-v4__title">E adesso?</h2>
                
                <p className="cta-final-v4__intro">
                    Hai letto fin qui. Forse ti stai chiedendo: <em>"Ma io cosa potrei egizzare?"</em>
                    <br />
                    Facciamo alcuni esempi concreti.
                </p>

                {/* Esempio 1: L'idea */}
                <div className="cta-final-v4__example">
                    <h3 className="cta-final-v4__example-title">Hai un'idea.</h3>
                    <p>
                        Sai che potrebbe avere valore. Vorresti proteggerla, ma non puoi certo 
                        spendere migliaia di euro in avvocati e brevetti prima ancora di sapere 
                        se funzionerà.
                    </p>
                    <p className="cta-final-v4__highlight">Egizza la tua idea.</p>
                    
                    <div className="cta-final-v4__objection">
                        <p className="cta-final-v4__objection-text">
                            "Ma che valore avrebbe? Registrare su blockchain non ha valore legale..."
                        </p>
                        <p className="cta-final-v4__response">Ti sbagli.</p>
                    </div>
                    
                    <div className="cta-final-v4__legal">
                        <p>
                            L'<strong>articolo 8-ter del Decreto Semplificazioni</strong> (DL 135/2018) 
                            riconosce che la memorizzazione di un documento su blockchain ha gli stessi 
                            effetti di una validazione temporale elettronica. Il <strong>Tribunale di Roma</strong> (2022) 
                            e il <strong>Tribunale di Brescia</strong> (2023) hanno già riconosciuto valore 
                            probatorio ai timestamp blockchain.
                        </p>
                        <p className="cta-final-v4__legal-result">
                            In pratica: <strong>se domani qualcuno copia la tua idea, tu hai la prova 
                            — legalmente riconosciuta — che l'avevi creata prima.</strong>
                        </p>
                    </div>
                    
                    <div className="cta-final-v4__comparison">
                        <div className="cta-final-v4__comparison-item cta-final-v4__comparison-item--expensive">
                            <span>Con un avvocato</span>
                            <strong>Centinaia di euro</strong>
                        </div>
                        <div className="cta-final-v4__comparison-item cta-final-v4__comparison-item--cheap">
                            <span>Su FlorenceEGI</span>
                            <strong>Pochi Egili</strong>
                        </div>
                    </div>
                </div>

                {/* Esempio 2: L'artista */}
                <div className="cta-final-v4__example">
                    <h3 className="cta-final-v4__example-title">Sei un artista.</h3>
                    <p>
                        Dipingi, fotografi, scrivi, componi. Ogni volta che pubblichi un'opera 
                        online, rischi che qualcuno la copi e dica che è sua.
                    </p>
                    <p className="cta-final-v4__highlight">Egizza la tua opera.</p>
                    <p>
                        Nel momento in cui lo fai, la blockchain certifica: <em>questa opera 
                        esisteva in questa data, creata da questa persona.</em>
                    </p>
                    <p className="cta-final-v4__siae">
                        La <strong>SIAE</strong> stessa ha scelto <strong>Algorand</strong> — la stessa 
                        blockchain che usiamo noi — per gestire i diritti d'autore dei suoi iscritti.
                    </p>
                </div>

                {/* Esempio 3: L'attività */}
                <div className="cta-final-v4__example">
                    <h3 className="cta-final-v4__example-title">Hai un'attività.</h3>
                    <p>
                        Un ristorante con ricette segrete. Un artigiano con tecniche uniche. 
                        Un consulente con metodologie proprietarie.
                    </p>
                    <p className="cta-final-v4__highlight">Egizza il tuo know-how.</p>
                    <p>
                        Non per venderlo (se non vuoi), ma per <strong>dimostrare che è tuo</strong>. Per sempre.
                    </p>
                </div>

                {/* Chiusura narrativa */}
                <div className="cta-final-v4__closing">
                    <p>Non ti chiediamo soldi per iniziare.</p>
                    <p>Non ti chiediamo di capire la blockchain.</p>
                    <p>Non ti chiediamo nemmeno di fidarti subito.</p>
                    <p className="cta-final-v4__closing-ask">
                        Ti chiediamo solo di <strong>provare</strong>.
                    </p>
                    <p>
                        Registrati, guarda come funziona, fai domande a Natan.
                        <br />
                        E quando sarai pronto — <strong>egizza</strong> la tua prima creazione.
                    </p>
                </div>

                {/* Motto */}
                <div className="cta-final-v4__motto">
                    <p>Se Esiste, <span className="text-gold">Egizzalo</span>.</p>
                    <p>Se lo Egizzi, <span className="text-gold">Vale</span>.</p>
                </div>

                {/* CTA Button */}
                <div className="cta-final-v4__action">
                    <a href="/register" className="cta-final-v4__button">
                        Inizia da qui
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                    <p className="cta-final-v4__subtext">
                        Nessun costo. Nessun impegno. Solo possibilità.
                    </p>
                </div>

            </div>
        </section>
    );
};

export default CTAFinalV4;
