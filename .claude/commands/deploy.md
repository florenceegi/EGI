# /deploy — Checklist Deploy Produzione EGI

Protocollo sicuro per deploy su EC2 eu-north-1 (i-0940cdb7b955d1632).
URL produzione: art.florenceegi.com
Path EC2: /home/forge/art.florenceegi.com/

**IMPORTANTE**: Fornisci i comandi da eseguire via SSM. NON eseguirli direttamente — li esegue Fabio.

## Prerequisiti obbligatori prima del deploy

```
□ Tutti i test locali passano
□ npm run build senza errori TypeScript (se frontend modificato)
□ php artisan config:cache OK in locale
□ git status pulito (no unstaged changes)
□ Branch corretto: git branch
□ Commit firmato con tag corretto ([FEAT]/[FIX]/[REFACTOR]/etc.)
□ Nessun file [LEGACY] toccato senza piano approvato
□ GDPR audit trail presente su nuove operazioni dati personali
□ MiCA-SAFE verificato (NO wallet custody introdotta)
```

## Sequenza deploy — EGI (Laravel + React)

### Step 1 — Git pull + cache Laravel

```bash
sudo -u forge bash -c 'cd /home/forge/art.florenceegi.com && \
  git pull origin develop && \
  php artisan cache:clear && \
  php artisan config:cache && \
  php artisan view:clear && \
  php artisan route:clear'
```

### Step 2 — Migration database (SOLO se ci sono nuove migration)

```bash
sudo -u forge bash -c 'cd /home/forge/art.florenceegi.com && \
  php artisan migrate --force'
```

**ATTENZIONE**: Migration su DB production = approvazione esplicita di Fabio OBBLIGATORIA.

### Step 3 — Build frontend React (SOLO se modificati file TS/CSS/TSX)

```bash
sudo -u forge bash -c 'cd /home/forge/art.florenceegi.com && \
  npm run build'
```

Nota: `public/build/` è in `.gitignore` → rebuilda sempre dopo pull se tocchi frontend.

### Step 4 — Restart queue worker (se modificati Jobs/Events)

```bash
sudo supervisorctl restart egi-queue
```

### Step 5 — Verifica health

```bash
# Laravel risponde
curl -s -o /dev/null -w "%{http_code}" https://art.florenceegi.com/

# AlgoKit microservice (se coinvolto)
curl -s http://localhost:3000/health
```

## Accesso SSM (NO SSH diretto)

```bash
aws ssm send-command \
  --instance-ids i-0940cdb7b955d1632 \
  --document-name AWS-RunShellScript \
  --parameters 'commands=["COMANDO_QUI"]' \
  --region eu-north-1 \
  --query 'Command.CommandId' --output text

# Poi verifica con:
aws ssm get-command-invocation \
  --command-id "[ID_RESTITUITO]" \
  --instance-id i-0940cdb7b955d1632 \
  --region eu-north-1 \
  --query '[Status,StandardOutputContent,StandardErrorContent]' \
  --output text
```

## Post-deploy checklist

```
□ Homepage risponde: https://art.florenceegi.com
□ Login funziona (Sanctum + Jetstream)
□ Asset listing funziona
□ Nessun errore nei log Laravel (ultimi 50 righe)
□ AlgoKit microservice UP (se tocchi blockchain)
□ DOC-SYNC completato se necessario
□ Commit [DEPLOY] su EGI-DOC se architettura cambiata
```

## Rollback rapido

```bash
sudo -u forge bash -c 'cd /home/forge/art.florenceegi.com && \
  git reset --hard HEAD~1 && \
  php artisan config:cache && \
  php artisan view:clear'
```

**Rollback migration**: richiede piano separato — MAI rollback migration senza approvazione Fabio.

## Note critiche

- Branch main/production → approvazione esplicita Fabio prima di merge
- Migration irreversibili → backup DB prima di procedere
- AlgoKit microservice (porta 3000) → separato da EGI Laravel, gestione indipendente
- Algorand testnet → OK. Mainnet → SOLO con approvazione Fabio + documentazione
