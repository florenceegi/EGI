#!/bin/bash

################################################################################
# Script: clean-creator-images.sh
# Descrizione: Cancella tutti i file immagine dalla radice delle cartelle
#              collections_{id}/creator_{id}/ mantenendo intatta la cartella
#              head/ e tutte le altre sottocartelle.
#
# Percorso: storage/app/public/users_files/collections_*/creator_*/
#
# CANCELLA:
#   ✅ Tutti i file immagine (jpg, jpeg, png, webp, gif, bmp, svg) nella radice
#      di collections_{id}/creator_{id}/
#
# MANTIENE:
#   ❌ Cartella head/ e tutto il suo contenuto
#   ❌ Qualsiasi altra sottocartella
#   ❌ File non-immagine
#
# Uso: bash bash_files/clean-creator-images.sh [--dry-run] [--force]
#
# Opzioni:
#   --dry-run    Mostra cosa verrebbe cancellato senza effettuare la cancellazione
#   --force      Cancella senza chiedere conferma (per Forge/automazione)
#
# Autore: EGI Team
# Data: 2025-10-29
# Aggiornato: 2025-10-31 (aggiunto --force per Forge)
################################################################################

set -e  # Exit on error

# Colori per output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Directory base
BASE_DIR="storage/app/public/users_files"

# Check se la directory esiste
if [ ! -d "$BASE_DIR" ]; then
    echo -e "${RED}❌ Errore: Directory $BASE_DIR non trovata!${NC}"
    exit 1
fi

# Modalità dry-run e force
DRY_RUN=false
FORCE=false

# Parse parametri
for arg in "$@"; do
    case $arg in
        --dry-run)
            DRY_RUN=true
            ;;
        --force|-f)
            FORCE=true
            ;;
        *)
            echo -e "${RED}❌ Parametro non riconosciuto: $arg${NC}"
            echo -e "${YELLOW}Uso: $0 [--dry-run] [--force]${NC}"
            exit 1
            ;;
    esac
done

if [ "$DRY_RUN" = true ]; then
    echo -e "${YELLOW}🔍 Modalità DRY-RUN attivata - nessun file verrà cancellato${NC}"
    echo ""
fi

if [ "$FORCE" = true ] && [ "$DRY_RUN" = false ]; then
    echo -e "${YELLOW}⚡ Modalità FORCE attivata - cancellazione senza conferma${NC}"
    echo ""
fi

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}  🗑️  Clean Creator Images - EGI Storage Cleanup${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Conta i file prima della cancellazione
echo -e "${YELLOW}📊 Scansione file da rimuovere...${NC}"
echo ""

# Trova tutti i file immagine nella radice di creator_*/ (escludendo head/)
FILES=$(find "$BASE_DIR"/collections_*/creator_*/ \
    -maxdepth 1 \
    -type f \
    \( -iname "*.jpg" -o \
       -iname "*.jpeg" -o \
       -iname "*.png" -o \
       -iname "*.webp" -o \
       -iname "*.gif" -o \
       -iname "*.bmp" -o \
       -iname "*.svg" \) \
    2>/dev/null)

# Conta i file trovati
FILE_COUNT=$(echo "$FILES" | grep -c . 2>/dev/null || echo "0")

if [ "$FILE_COUNT" -eq 0 ]; then
    echo -e "${GREEN}✅ Nessun file immagine trovato da cancellare.${NC}"
    echo ""
    exit 0
fi

# Calcola dimensione totale
TOTAL_SIZE=$(echo "$FILES" | xargs -r du -ch 2>/dev/null | tail -1 | cut -f1)

echo -e "${YELLOW}📦 File trovati: ${FILE_COUNT}${NC}"
echo -e "${YELLOW}💾 Dimensione totale: ${TOTAL_SIZE}${NC}"
echo ""

# Mostra esempi di file da cancellare (primi 10)
echo -e "${BLUE}📋 Esempi di file che verranno cancellati (primi 10):${NC}"
echo "$FILES" | head -10 | while read -r file; do
    SIZE=$(du -h "$file" 2>/dev/null | cut -f1)
    echo -e "  ${RED}├─${NC} $(basename "$file") ${YELLOW}(${SIZE})${NC}"
done

if [ "$FILE_COUNT" -gt 10 ]; then
    echo -e "  ${RED}└─${NC} ... e altri $((FILE_COUNT - 10)) file"
fi
echo ""

# Conferma (solo se non è dry-run e non è force)
if [ "$DRY_RUN" = false ] && [ "$FORCE" = false ]; then
    echo -e "${YELLOW}⚠️  ATTENZIONE: Questa operazione cancellerà ${FILE_COUNT} file per un totale di ${TOTAL_SIZE}!${NC}"
    echo -e "${YELLOW}   Le cartelle head/ e le altre sottocartelle NON saranno toccate.${NC}"
    echo ""
    read -p "Sei sicuro di voler procedere? (yes/no): " CONFIRM

    if [ "$CONFIRM" != "yes" ]; then
        echo -e "${BLUE}❌ Operazione annullata dall'utente.${NC}"
        exit 0
    fi
    echo ""
fi

# Cancellazione file
echo -e "${GREEN}🗑️  Cancellazione in corso...${NC}"
echo ""

DELETED=0
ERRORS=0

echo "$FILES" | while read -r file; do
    if [ -f "$file" ]; then
        if [ "$DRY_RUN" = true ]; then
            echo -e "  ${BLUE}[DRY-RUN]${NC} Cancellerebbe: $file"
            DELETED=$((DELETED + 1))
        else
            if rm "$file" 2>/dev/null; then
                echo -e "  ${GREEN}✓${NC} Cancellato: $(basename "$file")"
                DELETED=$((DELETED + 1))
            else
                echo -e "  ${RED}✗${NC} Errore cancellazione: $(basename "$file")"
                ERRORS=$((ERRORS + 1))
            fi
        fi
    fi
done

echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

# Ricalcola file rimanenti (solo se non è dry-run)
if [ "$DRY_RUN" = false ]; then
    REMAINING=$(find "$BASE_DIR"/collections_*/creator_*/ \
        -maxdepth 1 \
        -type f \
        \( -iname "*.jpg" -o \
           -iname "*.jpeg" -o \
           -iname "*.png" -o \
           -iname "*.webp" -o \
           -iname "*.gif" -o \
           -iname "*.bmp" -o \
           -iname "*.svg" \) \
        2>/dev/null | wc -l)

    echo -e "${GREEN}✅ Operazione completata!${NC}"
    echo ""
    echo -e "${BLUE}📊 Statistiche:${NC}"
    echo -e "  • File trovati inizialmente: ${FILE_COUNT}"
    echo -e "  • File cancellati: ${GREEN}${FILE_COUNT}${NC}"
    echo -e "  • Errori: ${RED}${ERRORS}${NC}"
    echo -e "  • File immagine rimanenti: ${YELLOW}${REMAINING}${NC}"
    echo -e "  • Spazio liberato: ${GREEN}${TOTAL_SIZE}${NC}"
else
    echo -e "${YELLOW}🔍 DRY-RUN completato - nessun file è stato effettivamente cancellato${NC}"
    echo ""
    echo -e "${BLUE}📊 Statistiche (simulazione):${NC}"
    echo -e "  • File che verrebbero cancellati: ${FILE_COUNT}"
    echo -e "  • Spazio che verrebbe liberato: ${TOTAL_SIZE}"
    echo ""
    echo -e "${GREEN}💡 Esegui senza --dry-run per cancellare effettivamente i file${NC}"
fi

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Mostra cartelle head/ mantenute
HEAD_DIRS=$(find "$BASE_DIR"/collections_*/creator_*/head -type d 2>/dev/null | wc -l)
if [ "$HEAD_DIRS" -gt 0 ]; then
    echo -e "${GREEN}✅ Cartelle head/ mantenute intatte: ${HEAD_DIRS}${NC}"
fi

echo ""
echo -e "${GREEN}🎉 Script completato con successo!${NC}"
echo ""
