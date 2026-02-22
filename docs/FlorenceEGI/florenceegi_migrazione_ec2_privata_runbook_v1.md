# FlorenceEGI — Migrazione a EC2 privata dietro ALB (Runbook) — v2.0

> Obiettivo: rendere l'istanza EC2 **non raggiungibile da Internet** (niente IP pubblico), lasciando l'**ALB come unico entrypoint pubblico**.
> Strategia scelta: **migrazione manuale pulita** (nuova EC2 privata + configurazione + switch Target Group).
> Ultimo aggiornamento: **20 febbraio 2026**

---

## 0) Inventario risorse AWS

### Risorse attive
| Risorsa | Identificativo | Note |
|---------|---------------|-------|
| VPC | `vpc-019e351bf6db868ab` (florenceegi-vpc) | CIDR `10.0.0.0/16` |
| **Nuova EC2** | `i-0940cdb7b955d1632` (florenceegi-private) | t3.small, Ubuntu 24.04, Private IP `10.0.3.21` |
| Vecchia EC2 | `i-0e50d9a88c7682f20` (florenceegi-staging) | **DEREGISTRATA da TG**, ancora running |
| ALB | florenceegi-alb | DNS: `florenceegi-alb-1267759660.eu-north-1.elb.amazonaws.com` |
| Target Group | tg-florenceegi-prod-http-80 | Solo nuova EC2 registrata, Healthy |
| Private Subnet | `subnet-0ef6c94cac66c8273` (Private Subnet 2) | CIDR `10.0.3.0/24`, eu-north-1b |
| NAT Gateway | `nat-1c9842c63defd4591` (florenceegi-nat) | In public subnet |
| S3 Bucket | florenceegi-media | eu-north-1, media storage |
| CloudFront | media.florenceegi.com | CDN per media S3 |
| IAM Role | florenceegi-ec2-role | AmazonSSMManagedInstanceCore + florenceegi-media-access |
| IAM User | egi-kms-app | KMS + S3 credentials (usate in .env) |
| ACM Certificate | florenceegi.com + *.florenceegi.com + .it variants | Emesso, attivo su ALB HTTPS:443 |

### ALB Listeners
- **HTTP:80** → Redirect a HTTPS:443
- **HTTPS:443** → Forward a TG + regole redirect www/it→com

### DNS Route 53
| Record | Tipo | Target |
|--------|------|--------|
| florenceegi.com | A (Alias) | ALB |
| www.florenceegi.com | A (Alias) | ALB |
| art.florenceegi.com | A (Alias) | ALB |
| hub.florenceegi.com | A (Alias) | ALB |
| info.florenceegi.com | A (Alias) | ALB |
| natan-loc.florenceegi.com | A (Alias) | ALB |
| media.florenceegi.com | CNAME | CloudFront |

---

## 1) Stato completamento migrazione

### Fase 1 — S3 + CloudFront (COMPLETATA)
- [x] Bucket S3 `florenceegi-media` creato
- [x] CloudFront distribution per `media.florenceegi.com`
- [x] IAM user `egi-kms-app` con policy S3 + KMS
- [x] Spatie Media Library configurata su disco `s3`
- [x] Migrazione file media da locale a S3
- [x] Commit: `[CHORE] Migrazione storage media su AWS S3 + CloudFront CDN`

### Fase 2 — EC2 Privata (IN CORSO)
- [x] Private Subnet creata (10.0.3.0/24)
- [x] Route Table privata con NAT Gateway
- [x] IAM Role con SSM + S3
- [x] Nuova EC2 lanciata in subnet privata (no IP pubblico)
- [x] SSM Session Manager verificato funzionante
- [x] Server setup completo (PHP 8.3, Nginx, Node 20, Redis, Supervisor, Composer)
- [x] Utente `forge` creato, directory per 5 siti
- [x] Nginx vhosts per tutti e 5 i siti + `/health` endpoint
- [x] EC2 registrata in Target Group, health check Healthy
- [x] SSH deploy key (ed25519) aggiunta a GitHub (AutobookNft)
- [x] Tutti e 5 i repo clonati su EC2
- [x] DNS aggiornato: tutti i sottodomini puntano ad ALB (non piu vecchio IP)
- [x] Vecchia EC2 deregistrata da Target Group

### Deploy applicazioni
- [x] **florenceegi.com** — EGI-HUB-HOME-REACT (React 18 + Vite + Three.js) — LIVE
- [x] **art.florenceegi.com** — EGI (Laravel 11 + PHP 8.3) — LIVE
- [x] **info.florenceegi.com** — EGI-INFO (React 19 + Vite) — LIVE
- [ ] **hub.florenceegi.com** — EGI-HUB (Laravel 11 API + React 18 SPA)
- [ ] **natan-loc.florenceegi.com** — NATAN_LOC (Laravel 12 + Python FastAPI + MongoDB)

### Fase 3 — Automazione e cleanup (DA FARE)
- [ ] Setup Supervisor (queue worker Laravel + FastAPI)
- [ ] Setup cron (Laravel scheduler)
- [ ] Setup GitHub Actions + SSM per deploy automatici
- [ ] Stop/terminate vecchia EC2 (florenceegi-staging)
- [ ] Cancellazione subscription Laravel Forge
- [ ] Hardening: WAF, ALB access logs, CloudWatch alarms

---

## 2) Architettura finale

```
Internet
  ↓
Route 53 (DNS)
  ↓
ALB (florenceegi-alb) — Public Subnets
  │  HTTP:80 → redirect HTTPS
  │  HTTPS:443 → forward to TG
  ↓
Target Group (tg-florenceegi-prod-http-80) — Health check /health
  ↓
EC2 (florenceegi-private) — Private Subnet 10.0.3.0/24, NO Public IP
  │  IP: 10.0.3.21
  ↓
Nginx (vhost routing per Host header)
  ├─ florenceegi.com        → /home/forge/florenceegi.com/dist        (React SPA)
  ├─ art.florenceegi.com    → /home/forge/art.florenceegi.com/public  (Laravel PHP-FPM)
  ├─ hub.florenceegi.com    → /home/forge/hub.florenceegi.com/        (Laravel API + React dist/)
  ├─ natan-loc.florenceegi.com → /home/forge/natan-loc.florenceegi.com/laravel_backend/public (Laravel)
  └─ info.florenceegi.com   → /home/forge/info.florenceegi.com/dist   (React SPA)

Egress: EC2 → NAT Gateway → Internet (apt, composer, npm, git, API esterne)
Accesso admin: AWS SSM Session Manager (zero porte SSH aperte)
Media: S3 (florenceegi-media) → CloudFront (media.florenceegi.com)
```

---

## 3) Stack software su EC2

| Software | Versione | Note |
|----------|---------|-------|
| Ubuntu | 24.04 LTS | AMI ufficiale |
| PHP | 8.3 + estensioni (fpm, cli, pgsql, redis, gd, etc.) | PPA ondrej/php |
| Nginx | latest | Reverse proxy + static files |
| Node.js | 20 LTS | NodeSource |
| npm | (bundled con Node 20) | |
| Composer | 2.x | Globale |
| Redis | latest | Cache + sessioni |
| Supervisor | latest | Process manager |
| Git | latest | Deploy via SSH |

### Utente e permessi
- Utente app: `forge` (home: `/home/forge/`)
- Web server: `www-data` (membro gruppo `forge`)
- `/home/forge/` ha permessi `755` (necessario per PHP-FPM/www-data)

### Directory siti
```
/home/forge/
├── florenceegi.com/          → EGI-HUB-HOME-REACT (React)
├── art.florenceegi.com/      → EGI (Laravel)
├── hub.florenceegi.com/      → EGI-HUB (Laravel + React)
├── natan-loc.florenceegi.com/ → NATAN_LOC (Laravel + FastAPI)
└── info.florenceegi.com/     → EGI-INFO (React)
```

---

## 4) Dominio → App mapping

| Dominio | Repo GitHub | Stack | Web Root |
|---------|------------|-------|----------|
| florenceegi.com | AutobookNft/EGI-HUB-HOME-REACT | React 18 + Vite + Three.js | dist/ |
| art.florenceegi.com | AutobookNft/EGI | Laravel 11 + PHP 8.3 | public/ |
| hub.florenceegi.com | AutobookNft/EGI-HUB | Laravel 11 API + React 18 SPA | dist/ (React) + public/ (API) |
| natan-loc.florenceegi.com | AutobookNft/NATAN_LOC | Laravel 12 + Python FastAPI + MongoDB | laravel_backend/public/ |
| info.florenceegi.com | AutobookNft/EGI-INFO | React 19 + Vite | dist/ |

---

## 5) Comandi deploy per app

### 5.1 React apps (florenceegi.com, info.florenceegi.com)
```bash
sudo -u forge bash -c "cd /home/forge/<dominio> && git pull origin main && npm install && npm run build"
```

### 5.2 Laravel app (art.florenceegi.com)
```bash
sudo -u forge bash -c "cd /home/forge/art.florenceegi.com && git pull origin develop && composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache"
sudo systemctl restart php8.3-fpm
```

### 5.3 Hybrid Laravel+React (hub.florenceegi.com) — DA CONFIGURARE
```bash
sudo -u forge bash -c "cd /home/forge/hub.florenceegi.com && git pull origin main && composer install --no-dev --optimize-autoloader && npm install && npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache"
sudo systemctl restart php8.3-fpm
```

### 5.4 NATAN_LOC (natan-loc.florenceegi.com) — DA CONFIGURARE
```bash
# Laravel backend
sudo -u forge bash -c "cd /home/forge/natan-loc.florenceegi.com/laravel_backend && git pull origin main && composer install --no-dev --optimize-autoloader && php artisan config:cache && php artisan route:cache && php artisan view:cache"
sudo systemctl restart php8.3-fpm
# Python FastAPI (se presente)
# pip install -r requirements.txt + supervisor restart
```

---

## 6) Accesso amministrativo

### SSM Session Manager (metodo scelto)
1. AWS Console → Systems Manager → Session Manager
2. Start session → seleziona `florenceegi-private`
3. Sei dentro come `ssm-user`
4. Per operare come forge: `sudo -u forge bash -c "cd /path && comandi"`

### SSH Deploy Key su EC2
- Chiave: `/home/forge/.ssh/id_ed25519`
- Fingerprint: `ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIPtmivLpiAhwDXppo+XVYS76bNDX70UTMGY0nWNc2gtH`
- Aggiunta a GitHub account AutobookNft
- Test: `sudo -u forge ssh -T git@github.com` → "successfully authenticated"

---

## 7) File .env chiave (art.florenceegi.com)

Variabili critiche adattate per staging/produzione:
```env
APP_ENV=staging
APP_DEBUG=false
APP_URL=https://art.florenceegi.com
SESSION_DOMAIN=.florenceegi.com
DEFAULT_HOSTING=AWS
FILESYSTEM_DISK=s3
MEDIA_DISK=s3
LOG_LEVEL=error
PROJECT_BASE_FOLDER=/home/forge/
```
DB: PostgreSQL su RDS (`florenceegi-postgres-dev.c1i0048yu660.eu-north-1.rds.amazonaws.com`)

---

## 8) Problemi risolti durante migrazione

| Problema | Causa | Soluzione |
|----------|-------|-----------|
| `cd` fallisce in SSM | ssm-user non puo traversare /home/forge | `sudo -u forge bash -c "cd ... && ..."` |
| Laravel "File not found" | PHP-FPM (www-data) non accede a /home/forge/ | `chmod 755 /home/forge` + restart php8.3-fpm |
| art.florenceegi.com timeout | DNS puntava a vecchio IP EC2 | Aggiornato Route 53 con Alias → ALB |
| Placeholder invece di app | Vecchia EC2 ancora nel TG | Deregistrata vecchia EC2 |
| URL sslip.io in EGI-HUB-HOME-REACT | URL hardcoded in 6+ file | Sostituiti con `config` da `@/utils/config` (env-driven) |
| Build TS fallito | Import `Stars` non usato + cast `window` | Rimosso import, usato `as unknown as` double assertion |

---

## 9) Prossimi passi (TODO)

### Priorita 1 — Deploy app rimanenti
1. **hub.florenceegi.com**: creare .env, composer install, npm build, verificare Nginx vhost
2. **natan-loc.florenceegi.com**: creare .env Laravel, setup Python venv + FastAPI, MongoDB connection

### Priorita 2 — Automazione
3. **Supervisor**: configurare queue worker Laravel (art + hub) + FastAPI service
4. **Cron**: `* * * * * forge php artisan schedule:run` per Laravel scheduler
5. **GitHub Actions + SSM**: workflow per deploy automatico su push

### Priorita 3 — Cleanup e hardening
6. **Stop vecchia EC2** (florenceegi-staging) — verificare che nessun servizio dipenda da essa
7. **Cancellare subscription Laravel Forge**
8. **WAF su ALB** — managed rules + rate limiting
9. **ALB access logs** su S3
10. **CloudWatch alarms** — 5xx, unhealthy targets, CPU spike
11. **Backup strategy** — snapshot EBS periodici o AMI automatiche

---

## 10) Checklist per nuova chat (incolla e riparti)

```
Stato migrazione FlorenceEGI (aggiornato 20/02/2026):

COMPLETATO:
- EC2 privata (i-0940cdb7b955d1632, 10.0.3.21) dietro ALB, no IP pubblico
- SSM Session Manager per accesso admin
- NAT Gateway per egress
- 5 Nginx vhosts configurati con /health endpoint
- 3 app live: florenceegi.com (React 3D), art.florenceegi.com (Laravel), info.florenceegi.com (React)
- S3 + CloudFront per media (media.florenceegi.com)
- DNS Route 53: tutti i sottodomini → ALB
- Vecchia EC2 deregistrata da TG (ancora running)

DA FARE:
- Deploy hub.florenceegi.com (Laravel 11 API + React 18 SPA)
- Deploy natan-loc.florenceegi.com (Laravel 12 + Python FastAPI + MongoDB)
- Supervisor per queue worker + FastAPI
- Cron per Laravel scheduler
- GitHub Actions + SSM per CI/CD
- Stop/terminate vecchia EC2
- Cancellare Laravel Forge
- Hardening (WAF, logs, alarms)

INFRA:
- VPC: vpc-019e351bf6db868ab, CIDR 10.0.0.0/16
- EC2: i-0940cdb7b955d1632, t3.small, eu-north-1b, private 10.0.3.21
- ALB: florenceegi-alb-1267759660.eu-north-1.elb.amazonaws.com
- TG: tg-florenceegi-prod-http-80
- S3: florenceegi-media, CDN: media.florenceegi.com
- RDS: florenceegi-postgres-dev.c1i0048yu660.eu-north-1.rds.amazonaws.com
- IAM Role: florenceegi-ec2-role (SSM + S3)
- SSH key: /home/forge/.ssh/id_ed25519 (GitHub: AutobookNft)
```
