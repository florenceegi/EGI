Cosa c'è da fare-

Su egi-card non viene mostrato il Co-creatore dopo il mint, per ora solo dopo prenotazine, questa cosa ovviamente va modifcata, da ora in poi chi fa prenotazsione non diventa Co-creatore, è semplicemente uno che ha fatto un'offerta, invece chi ha fatto il mint è il vero co-creatore, questa logica va modificata
Adeguare egi-card-list DI CONSEGUENZA.

Nella vista del mint si devono vedere utility e traits.

Per egis.show IL cocreatoir (owner) deve poter fare CRUD con il campo pryce.

Poi questo vale per per creator e owner

Deve poter gestire l'asta. Quella che prima si chiamava prenotazione ora diventa asta. quindi
tutto quello che deve avere un asta: 
prezzo minimo,
data start
data end

nella vista creator e owner devono avere una osrta di monitor su cui vedonmo cosa pagano a chi, in base ai wallet collegati alla collection a cui l'egi appartiene. (model Wallet)

Poi ci deve essere una sezione con tutti i riferimenti di legge a proposito del diritto di seguito. 
la legge sul diritto di proprietà del creator anche dopo la vendita, 
su cosa acquista realmente l'aquirente, quali sono le cose che può e che non può fare.
Poi, cosa succede se l'opera si altera, cosa occorre fare da un punto di vista di CoA e chi deve fare cosa...

Inoltre qui l'oner deve avere la possibilità di comunicare con il Creator. Quindi occorre creare una chat push tra i due (questo mettiamolo da ultimo in quanto penso ci sia un bel po' di lavoro da fare)

Poi occorre modificare la gestione del mint, da ora in poi, quando si registra un nuovo user di tipo Merchat, la procedura deve creare in automaticoi un wallet reale su Algorand. Quì occorre implementare la protezione fortssima per la frase segreta. E infine Implementatre la possibilità di far riscattare il Wallet all'user con cancellazione perenne della chiave segreta, con flusso sicuro in più passaggi, in modo che utente non combini guai... e prevedere possibilità comunque di fare roolback dell aprocederua fino a quando no è finita con successo.
Creare un vista apposita per il riscatto del Wallet
la vista dovrà essere raggiungibile mediante il menu: resources/views/components/navigation/vanilla-desktop-menu.blade.php sezione: <!-- Account Management Card -->




