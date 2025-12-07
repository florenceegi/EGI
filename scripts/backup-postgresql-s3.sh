#!/bin/bash
# ============================================
# PostgreSQL Backup Script → AWS S3
# FlorenceEGI Production Database
# ============================================
# Questo script:
# 1. Esegue pg_dump del database PostgreSQL
# 2. Comprime il backup con gzip
# 3. Lo carica su AWS S3
# 4. Elimina backup locali vecchi di 7 giorni
# 5. Logga tutte le operazioni
#
# Installazione su Forge:
# 1. Copia questo file in /home/forge/scripts/
# 2. chmod +x /home/forge/scripts/backup-postgresql-s3.sh
# 3. Installa AWS CLI: sudo apt install awscli -y
# 4. Configura cron (vedi sotto)
#
# Cron (backup giornaliero alle 3:00):
# 0 3 * * * /home/forge/scripts/backup-postgresql-s3.sh >> /home/forge/logs/backup.log 2>&1
# ============================================

set -e

# ==========================================
# CONFIGURAZIONE - MODIFICA QUESTI VALORI
# ==========================================

# Database PostgreSQL
DB_HOST="127.0.0.1"
DB_PORT="5432"
DB_NAME="fegi_prod"
DB_USER="fegi_natan"
DB_PASSWORD="LEtMxeYbCUmyuGhPuvrS"

# AWS S3 - Leggi da variabili d'ambiente o imposta manualmente sul server
AWS_ACCESS_KEY_ID="${AWS_ACCESS_KEY_ID_BACKUP:-YOUR_AWS_ACCESS_KEY_ID}"
AWS_SECRET_ACCESS_KEY="${AWS_SECRET_ACCESS_KEY_BACKUP:-YOUR_AWS_SECRET_ACCESS_KEY}"
AWS_REGION="${AWS_REGION_BACKUP:-eu-west-1}"
S3_BUCKET="${S3_BUCKET_BACKUP:-florenceegi-db-backups}"
S3_PREFIX="postgresql"  # Cartella nel bucket

# Directory locali
BACKUP_DIR="/home/forge/backups"
LOG_FILE="/home/forge/logs/backup.log"

# Retention
LOCAL_RETENTION_DAYS=7
S3_RETENTION_DAYS=30  # Gestito da S3 Lifecycle Rules

# ==========================================
# NON MODIFICARE DA QUI IN POI
# ==========================================

# Colori per output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Timestamp
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DATE=$(date +"%Y-%m-%d %H:%M:%S")

# Nome file backup
BACKUP_FILENAME="${DB_NAME}_${TIMESTAMP}.sql.gz"
BACKUP_PATH="${BACKUP_DIR}/${BACKUP_FILENAME}"

# Funzione log
log() {
    echo -e "[${DATE}] $1" | tee -a "${LOG_FILE}"
}

log_success() {
    echo -e "[${DATE}] ${GREEN}✓ $1${NC}" | tee -a "${LOG_FILE}"
}

log_error() {
    echo -e "[${DATE}] ${RED}✗ $1${NC}" | tee -a "${LOG_FILE}"
}

log_warn() {
    echo -e "[${DATE}] ${YELLOW}⚠ $1${NC}" | tee -a "${LOG_FILE}"
}

# ==========================================
# INIZIO BACKUP
# ==========================================

log "=========================================="
log "Avvio backup PostgreSQL → S3"
log "Database: ${DB_NAME}"
log "=========================================="

# Crea directory se non esistono
mkdir -p "${BACKUP_DIR}"
mkdir -p "$(dirname "${LOG_FILE}")"

# Esporta credenziali AWS
export AWS_ACCESS_KEY_ID="${AWS_ACCESS_KEY_ID}"
export AWS_SECRET_ACCESS_KEY="${AWS_SECRET_ACCESS_KEY}"
export AWS_DEFAULT_REGION="${AWS_REGION}"

# Esporta password PostgreSQL
export PGPASSWORD="${DB_PASSWORD}"

# 1. Esegui pg_dump
log "Esecuzione pg_dump..."
if pg_dump -h "${DB_HOST}" -p "${DB_PORT}" -U "${DB_USER}" -d "${DB_NAME}" --format=plain --no-owner --no-acl | gzip > "${BACKUP_PATH}"; then
    BACKUP_SIZE=$(du -h "${BACKUP_PATH}" | cut -f1)
    log_success "Backup locale creato: ${BACKUP_PATH} (${BACKUP_SIZE})"
else
    log_error "Errore durante pg_dump!"
    exit 1
fi

# 2. Verifica che il file non sia vuoto
if [ ! -s "${BACKUP_PATH}" ]; then
    log_error "Il file di backup è vuoto!"
    rm -f "${BACKUP_PATH}"
    exit 1
fi

# 3. Upload su S3
log "Upload su S3: s3://${S3_BUCKET}/${S3_PREFIX}/${BACKUP_FILENAME}"
if aws s3 cp "${BACKUP_PATH}" "s3://${S3_BUCKET}/${S3_PREFIX}/${BACKUP_FILENAME}" --storage-class STANDARD_IA; then
    log_success "Upload S3 completato!"
else
    log_error "Errore durante upload S3!"
    exit 1
fi

# 4. Verifica upload
log "Verifica file su S3..."
if aws s3 ls "s3://${S3_BUCKET}/${S3_PREFIX}/${BACKUP_FILENAME}" > /dev/null 2>&1; then
    S3_SIZE=$(aws s3 ls "s3://${S3_BUCKET}/${S3_PREFIX}/${BACKUP_FILENAME}" | awk '{print $3}')
    log_success "File verificato su S3 (${S3_SIZE} bytes)"
else
    log_error "File non trovato su S3 dopo upload!"
    exit 1
fi

# 5. Pulizia backup locali vecchi
log "Pulizia backup locali più vecchi di ${LOCAL_RETENTION_DAYS} giorni..."
DELETED_COUNT=$(find "${BACKUP_DIR}" -name "*.sql.gz" -type f -mtime +${LOCAL_RETENTION_DAYS} -delete -print | wc -l)
if [ "${DELETED_COUNT}" -gt 0 ]; then
    log_success "Eliminati ${DELETED_COUNT} backup locali vecchi"
else
    log "Nessun backup locale da eliminare"
fi

# 6. Statistiche finali
log "=========================================="
log_success "BACKUP COMPLETATO CON SUCCESSO"
log "File: ${BACKUP_FILENAME}"
log "Dimensione: ${BACKUP_SIZE}"
log "S3: s3://${S3_BUCKET}/${S3_PREFIX}/${BACKUP_FILENAME}"
log "=========================================="

# Pulisci variabile password
unset PGPASSWORD
unset AWS_ACCESS_KEY_ID
unset AWS_SECRET_ACCESS_KEY

exit 0
