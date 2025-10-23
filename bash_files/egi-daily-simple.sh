#!/bin/bash

# 📅 EGI Daily Code Counter - Simple Version
# Author: Padmin D. Curtis (AI Partner OS3.0)
# Version: 1.1.0 (FlorenceEGI - Simple Daily Tracker)
# Date: 2025-09-29
# Purpose: Conta le righe di codice scritte oggi (versione semplificata)

# Colori
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

# Parametri
DATE_FROM="${1:-$(date +%Y-%m-%d)}"
AUTHOR_FILTER="${2:-$(git config user.name)}"

echo -e "${BOLD}${BLUE}"
echo "═══════════════════════════════════════════════════════════════════"
echo "📅 FLORENCE EGI - PRODUTTIVITÀ GIORNALIERA"
echo "═══════════════════════════════════════════════════════════════════"
echo -e "${NC}"

echo -e "${CYAN}📅 Data: ${DATE_FROM}${NC}"
echo -e "${CYAN}👤 Autore: ${AUTHOR_FILTER}${NC}"
echo ""

if ! git rev-parse --git-dir > /dev/null 2>&1; then
    echo -e "${RED}❌ Non siamo in un repository Git!${NC}"
    exit 1
fi

# Statistiche semplici
commits_today=$(git log --since="${DATE_FROM} 00:00:00" --until="${DATE_FROM} 23:59:59" \
                   --author="${AUTHOR_FILTER}" --oneline | wc -l)

files_modified=$(git log --since="${DATE_FROM} 00:00:00" --until="${DATE_FROM} 23:59:59" \
                    --author="${AUTHOR_FILTER}" --name-only --pretty=format: \
                    | sort -u | wc -l)

# Calcolo righe totali (ESCLUDENDO commit di cleanup massicci come .history)
# Lista commit da escludere (grosse rimozioni tipo .history folder)
EXCLUDE_COMMITS="6756853"

# Costruisci il filtro per escludere commit
EXCLUDE_FILTER=""
for commit in $EXCLUDE_COMMITS; do
    EXCLUDE_FILTER="$EXCLUDE_FILTER | grep -v $commit"
done

# Calcola statistiche escludendo commit di cleanup E file .history
total_stats=$(git log --since="${DATE_FROM} 00:00:00" --until="${DATE_FROM} 23:59:59" \
                 --author="${AUTHOR_FILTER}" --pretty=format:"%H" | \
                 grep -v -E "(6756853)" | \
                 xargs -I {} git show {} --numstat --format="" 2>/dev/null | \
                 grep -v "^[0-9-]*[[:space:]]*[0-9-]*[[:space:]]*\.history/" | \
                 awk '{added+=$1; removed+=$2} END {print added+0, removed+0}')

read total_added total_removed <<< "$total_stats"
total_net=$((total_added - total_removed))

echo -e "${BOLD}${GREEN}📊 RISULTATI GIORNALIERI${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${GREEN}📝 Commit: ${BOLD}${commits_today}${NC}"
echo -e "${BLUE}📁 File modificati: ${BOLD}${files_modified}${NC}"
echo -e "${GREEN}➕ Righe aggiunte: ${BOLD}${total_added}${NC}"
echo -e "${YELLOW}➖ Righe rimosse: ${BOLD}${total_removed}${NC}"
echo -e "${BOLD}${CYAN}🚀 RIGHE NETTE: ${total_net}${NC}"

# Valutazione produttività INTELLIGENTE basata su commit E righe
echo ""
echo -e "${BOLD}${BLUE}📊 VALUTAZIONE GIORNATA:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Analisi multifattoriale
if [ $commits_today -eq 0 ]; then
    echo -e "${BOLD}${YELLOW}😴 GIORNO DI RIPOSO${NC}"
elif [ $total_net -lt -15000 ]; then
    echo -e "${BOLD}${CYAN}🧹 REFACTORING MASSICCIO (-$((total_net * -1)) righe)${NC}"
    echo -e "   ${CYAN}Ottimo lavoro di pulizia e riorganizzazione!${NC}"
elif [ $total_net -lt 0 ]; then
    echo -e "${BOLD}${BLUE}♻️  REFACTORING ($commits_today commit)${NC}"
    echo -e "   ${BLUE}Codice più pulito e manutenibile${NC}"
elif [ $total_net -gt 15000 ] && [ $commits_today -gt 20 ]; then
    echo -e "${BOLD}${GREEN}🚀 GIORNATA EPICA! (+$total_net righe, $commits_today commit)${NC}"
    echo -e "   ${GREEN}Produttività eccezionale su feature complesse!${NC}"
elif [ $total_net -gt 10000 ]; then
    echo -e "${BOLD}${GREEN}💪 ULTRA ECCELLENZA! (+$total_net righe)${NC}"
    echo -e "   ${GREEN}Feature major o integrazioni importanti${NC}"
elif [ $total_net -gt 5000 ]; then
    echo -e "${BOLD}${GREEN}⚡ ECCELLENTE! (+$total_net righe)${NC}"
    echo -e "   ${GREEN}Ottimo progresso su feature significative${NC}"
elif [ $total_net -gt 2500 ]; then
    echo -e "${BOLD}${YELLOW}👍 MOLTO BUONA (+$total_net righe)${NC}"
    echo -e "   ${YELLOW}Solido avanzamento del progetto${NC}"
elif [ $total_net -gt 1000 ]; then
    echo -e "${BOLD}${CYAN}📈 BUONA (+$total_net righe)${NC}"
    echo -e "   ${CYAN}Progressi costanti e stabili${NC}"
elif [ $total_net -gt 200 ]; then
    echo -e "${BOLD}${CYAN}📝 STANDARD (+$total_net righe)${NC}"
    echo -e "   ${CYAN}Modifiche mirate e precise${NC}"
else
    echo -e "${BOLD}${BLUE}🔧 MAINTENANCE (+$total_net righe)${NC}"
    echo -e "   ${BLUE}Fix, test o piccoli miglioramenti${NC}"
fi

# Suggerimenti intelligenti basati sui pattern
echo ""
if [ $commits_today -gt 30 ] && [ $total_net -lt 2500 ]; then
    echo -e "${YELLOW}💡 Molti commit piccoli: considera di raggruppare modifiche correlate${NC}"
elif [ $commits_today -lt 5 ] && [ $total_net -gt 10000 ]; then
    echo -e "${YELLOW}💡 Pochi commit grandi: considera commit più frequenti per migliore tracciabilità${NC}"
elif [ $commits_today -gt 0 ] && [ $total_net -gt 2500 ]; then
    echo -e "${GREEN}✅ Ottimo bilanciamento tra commit e codice prodotto!${NC}"
fi

# Top file modificati oggi
echo ""
echo -e "${BOLD}${BLUE}📁 FILE PIÙ MODIFICATI OGGI:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

git log --since="${DATE_FROM} 00:00:00" --until="${DATE_FROM} 23:59:59" \
       --author="${AUTHOR_FILTER}" --name-only --pretty=format: \
       | sort | uniq -c | sort -nr | head -5 | \
       while read count file; do
           if [ ! -z "$file" ]; then
               echo -e "${GREEN}${count}x${NC} ${file}"
           fi
       done

# Commit di oggi
if [ $commits_today -gt 0 ]; then
    echo ""
    echo -e "${BOLD}${BLUE}📝 COMMIT DI OGGI:${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    git log --since="${DATE_FROM} 00:00:00" --until="${DATE_FROM} 23:59:59" \
           --author="${AUTHOR_FILTER}" --oneline --decorate
fi

echo ""
echo -e "${BOLD}${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BOLD}Generated by: Padmin D. Curtis OS3.0 | $(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo -e "${BOLD}${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
