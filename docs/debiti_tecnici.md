# Debiti Tecnici e Note di Sviluppo

Questo documento raccoglie note su debiti tecnici, inconsistenze e aree di miglioramento individuate durante lo sviluppo.

## 1. Localizzazione e Traduzioni

### Inconsistenza Files di Traduzione (`resources/lang`)
- **Problema**: Il file `resources/lang/it/profile.php` è significativamente più grande rispetto alle versioni nelle altre lingue (EN, DE, ES, FR, PT).
- **Dettaglio**: Molte stringhe e chiavi presenti in Italiano non sono state tradotte o riportate negli altri file di lingua, causando potenziali fallback non corretti o chiavi mancanti per gli utenti internazionali.
- **Azione Richiesta**: Allineare tutti i file di traduzione assicurandosi che ogni chiave presente in `it/profile.php` abbia un corrispettivo (anche se in inglese/fallback) negli altri file.

---
*Aggiungere qui ulteriori voci man mano che vengono individuate.*
