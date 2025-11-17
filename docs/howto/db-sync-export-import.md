# Sincronizzazione manuale del database FlorenceEGI

Questa procedura permette di condividere un dump del database tra postazioni diverse (desktop, portatili, staging) quando lavori da solo e decidi di aggiornare una macchina alla volta.

## Prerequisiti

- Accesso shell al progetto (`/home/fabio/EGI`).
- Credenziali DB già configurate in `.env`.
- Accesso a `mysqldump` e `mysql` sul sistema.

## 1. Esportare il database dalla postazione sorgente

```bash
./scripts/db/export-shared-db.sh
```

Il comando:

- legge host/porta/credenziali da `.env`;
- crea un dump compresso `storage/db-sync/<database>_YYYYmmdd_HHMMSS.sql.gz`.

## 2. Trasferire il dump

Copi il file generato (es. con `scp`, chiavetta USB, ecc.) sulla macchina di destinazione. Mantieni i dump nella cartella `storage/db-sync/` per comodità e versionamento manuale.

## 3. Importare il database sulla macchina di destinazione

```bash
./scripts/db/import-shared-db.sh storage/db-sync/<nome_dump>.sql.gz
```

Il comando:

- legge le stesse variabili da `.env`;
- chiede conferma (`YES` testuale) prima di sovrascrivere i dati;
- supporta dump `.sql` e `.sql.gz`.

## Raccomandazioni operative

- **Regola d’oro**: lavora su **una sola** macchina per volta. Prima di passare alla successiva esporta e reimporta, così eviti conflitti.
- Conserva gli ultimi dump per sicurezza (almeno 2 versioni).
- Prima dell’import su ambienti critici (es. staging) verifica che non ci siano processi attivi che scrivono sul database.
- Puoi aggiungere versioni nel nome file (es. `staging-before-demo.sql.gz`) per tracciare i momenti importanti.


