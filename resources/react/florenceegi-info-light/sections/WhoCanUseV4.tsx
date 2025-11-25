/**
 * WhoCanUseV4 - Sezione 9
 * Chi può usare FlorenceEGI - Creator, Collector, EPP
 */
import React from 'react';
import './WhoCanUseV4.css';

interface WhoCanUseV4Props {
    className?: string;
}

const WhoCanUseV4: React.FC<WhoCanUseV4Props> = ({ className = '' }) => {
    return (
        <section className={`who-can-use-v4 ${className}`}>
            <div className="who-can-use-v4__container">
                
                {/* Titolo */}
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

                {/* Chiusura */}
                <div className="who-can-use-v4__closing">
                    <p>Tre mondi diversi. Un'unica piattaforma.</p>
                    <p>Perché alla fine, tutti vogliamo la stessa cosa: <strong>dare valore a ciò che conta</strong>.</p>
                </div>

                {/* Motto */}
                <div className="who-can-use-v4__motto">
                    <p>Se Esiste, <span className="text-gold">Egizzalo</span>.</p>
                    <p>Se lo Egizzi, <span className="text-gold">Vale</span>.</p>
                </div>

            </div>
        </section>
    );
};

export default WhoCanUseV4;
