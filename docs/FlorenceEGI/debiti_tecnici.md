# Debiti Tecnici e Note di Sviluppo

Questo documento raccoglie note su debiti tecnici, inconsistenze e aree di miglioramento individuate durante lo sviluppo.

## 1. Localizzazione e Traduzioni

### Inconsistenza Files di Traduzione (`resources/lang`)
- **Problema**: Il file `resources/lang/it/profile.php` è significativamente più grande rispetto alle versioni nelle altre lingue (EN, DE, ES, FR, PT).
- **Dettaglio**: Molte stringhe e chiavi presenti in Italiano non sono state tradotte o riportate negli altri file di lingua, causando potenziali fallback non corretti o chiavi mancanti per gli utenti internazionali.
- **Azione Richiesta**: Allineare tutti i file di traduzione assicurandosi che ogni chiave presente in `it/profile.php` abbia un corrispettivo (anche se in inglese/fallback) negli altri file.

---

## 2. Notification System (Push Notifications)

### 🔴 Push Notifications (Non-Critical)

**Status**: Not Implemented  
**Priority**: Low (deferred)  
**Created**: 2026-02-04

### Description
Le notifiche push (browser/mobile) non sono attualmente implementate nel sistema V3. Il sistema attuale supporta solo notifiche in-app (database) visualizzate tramite il Notification Center.

### Impact
- Gli utenti devono accedere manualmente al Notification Center per vedere le nuove notifiche
- Nessun alert real-time quando l'utente non è attivamente sulla piattaforma
- Possibile ritardo nella risposta a notifiche urgenti (es. vendite, spedizioni)

### Technical Context
Il sistema V3 è già predisposto per l'integrazione di canali aggiuntivi:
- `CustomDatabaseChannel` è modulare e separato dalla logica di business
- Le classi Notification supportano il metodo `via()` per specificare canali multipli
- Laravel supporta nativamente broadcast notifications via Pusher/Echo

### Proposed Solution (Future)
1. Implementare `BroadcastChannel` per notifiche real-time via WebSocket
2. Integrare servizio push (Firebase Cloud Messaging per mobile, Web Push API per browser)
3. Aggiungere preferenze utente per gestire canali di notifica
4. Implementare queue worker dedicato per push notifications

### Workaround Attuale
Gli utenti ricevono notifiche via:
- Email (per eventi critici come vendite/spedizioni)
- In-app notification center (richiede refresh manuale o polling)
- Badge counter nel menu principale (aggiornato via Livewire)

### Notes
Questo debito tecnico è stato documentato su richiesta esplicita dell'utente per dare priorità allo sviluppo di nuove feature. Non è considerato bloccante per il rilascio in produzione.

---
*Aggiungere qui ulteriori voci man mano che vengono individuate.*
