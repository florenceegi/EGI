#!/bin/bash

# 📅 EGI Daily Code Counter - Righe Scritte Oggi
# Author: Padmin D. Curtis (AI Partner OS3.0)
# Version: 1.0.0 (FlorenceEGI - Daily Productivity Tracker)
# Date: 2025-09-29
# Purpose: Conta solo le righe di codice scritte in un periodo specifico

# Colori per output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m' # No Color

# Parametri
DATE_FROM="${1:-$(date +%Y-%m-%d)}"
DATE_TO="${2:-$(date +%Y-%m-%d)}"
AUTHOR_FILTER="${3:-$(git config user.name)}"

# Header
echo -e "${BOLD}${BLUE}"
echo "═══════════════════════════════════════════════════════════════════"
echo "📅 FLORENCE EGI - CONTEGGIO RIGHE GIORNALIERO"
echo "═══════════════════════════════════════════════════════════════════"
echo -e "${NC}"

echo -e "${CYAN}📂 Repository: $(basename $(pwd))${NC}"
echo -e "${CYAN}📅 Periodo: ${DATE_FROM}${NC}"
if [[ "$DATE_TO" != "$DATE_FROM" ]]; then
    echo -e "${CYAN}📅 Fino a: ${DATE_TO}${NC}"
fi
echo -e "${CYAN}👤 Autore: ${AUTHOR_FILTER}${NC}"
echo ""

# Verifica che siamo in un repo git
if ! git rev-parse --git-dir > /dev/null 2>&1; then
    echo -e "${RED}❌ Non siamo in un repository Git!${NC}"
    exit 1
fi

# Funzione per contare righe aggiunte/rimosse per tipo file
count_lines_by_extension() {
    local ext="$1"
    local desc="$2"
    local color="$3"
    
    # Trova i file modificati oggi con quella estensione
    local files=$(git log --since="${DATE_FROM} 00:00:00" --until="${DATE_TO} 23:59:59" \
                     --author="${AUTHOR_FILTER}" --name-only --pretty=format: \
                     | grep -E "\\.${ext}$" | sort -u | tr '\n' ' ')
    
    if [ -z "$files" ]; then
        return 0
    fi
    
    # Conta le righe aggiunte e rimosse
    local stats=$(git log --since="${DATE_FROM} 00:00:00" --until="${DATE_TO} 23:59:59" \
                     --author="${AUTHOR_FILTER}" --numstat --pretty=format:"" \
                     -- ${files} | awk '{added+=$1; removed+=$2} END {print added+0, removed+0}')
    
    read added removed <<< "$stats"
    local net=$((added - removed))
    
    if [ $added -gt 0 ] || [ $removed -gt 0 ]; then
        echo -e "${color}${desc}:${NC}" >&2
        echo -e "   • Righe aggiunte: ${GREEN}+${added}${NC}" >&2
        echo -e "   • Righe rimosse: ${RED}-${removed}${NC}" >&2
        echo -e "   • ${BOLD}Netto: ${net}${NC}" >&2
        echo "" >&2
    fi
    
    echo $net
}

echo -e "${BOLD}${GREEN}📊 BREAKDOWN PER TIPO DI FILE${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Conteggi per tipo di file
php_net=$(count_lines_by_extension "php" "🐘 PHP" "$YELLOW" 2>/dev/null | tail -n 1)
blade_net=$(count_lines_by_extension "blade\\.php" "🌐 Blade Templates" "$PURPLE" 2>/dev/null | tail -n 1)
js_net=$(count_lines_by_extension "js" "📜 JavaScript" "$CYAN" 2>/dev/null | tail -n 1)
ts_net=$(count_lines_by_extension "ts" "⚡ TypeScript" "$BLUE" 2>/dev/null | tail -n 1)
css_net=$(count_lines_by_extension "css" "🎨 CSS" "$GREEN" 2>/dev/null | tail -n 1)
json_net=$(count_lines_by_extension "json" "⚙️ JSON" "$RED" 2>/dev/null | tail -n 1)
sh_net=$(count_lines_by_extension "sh" "🛠️ Bash Scripts" "$PURPLE" 2>/dev/null | tail -n 1)
html_net=$(count_lines_by_extension "html" "📄 HTML" "$CYAN" 2>/dev/null | tail -n 1)

# Assicurati che siano numeri
php_net=${php_net:-0}
blade_net=${blade_net:-0}
js_net=${js_net:-0}
ts_net=${ts_net:-0}
css_net=${css_net:-0}
json_net=${json_net:-0}
sh_net=${sh_net:-0}
html_net=${html_net:-0}

# Calcolo totale netto
total_net=$((php_net + blade_net + js_net + ts_net + css_net + json_net + sh_net + html_net))

# Statistiche generali git per oggi
echo -e "${BOLD}${BLUE}📈 STATISTICHE GENERALI${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Numero di commit oggi
commits_today=$(git log --since="${DATE_FROM} 00:00:00" --until="${DATE_TO} 23:59:59" \
                   --author="${AUTHOR_FILTER}" --oneline | wc -l)

# File modificati oggi
files_modified=$(git log --since="${DATE_FROM} 00:00:00" --until="${DATE_TO} 23:59:59" \
                    --author="${AUTHOR_FILTER}" --name-only --pretty=format: \
                    | sort -u | wc -l)

# Statistiche totali
total_stats=$(git log --since="${DATE_FROM} 00:00:00" --until="${DATE_TO} 23:59:59" \
                 --author="${AUTHOR_FILTER}" --numstat --pretty=format:"" \
                 | awk '{added+=$1; removed+=$2} END {print added+0, removed+0}')

read total_added total_removed <<< "$total_stats"

echo -e "${GREEN}📝 Commit oggi: ${BOLD}${commits_today}${NC}"
echo -e "${BLUE}📁 File modificati: ${BOLD}${files_modified}${NC}"
echo -e "${GREEN}➕ Totale righe aggiunte: ${BOLD}${total_added}${NC}"
echo -e "${RED}➖ Totale righe rimosse: ${BOLD}${total_removed}${NC}"
echo ""

# Risultato finale
echo -e "${BOLD}${YELLOW}"
echo "═══════════════════════════════════════════════════════════════════"
echo "🎯 PRODUTTIVITÀ GIORNALIERA"
echo "═══════════════════════════════════════════════════════════════════"
echo -e "${NC}"

if [ $total_net -gt 0 ]; then
    echo -e "${BOLD}${GREEN}🚀 RIGHE NETTE SVILUPPATE OGGI: +${total_net}${NC}"
    
    # Calcola la produttività
    if [ $total_net -gt 1000 ]; then
        echo -e "${BOLD}${GREEN}💪 PRODUTTIVITÀ: ULTRA ECCELLENZA!${NC}"
    elif [ $total_net -gt 500 ]; then
        echo -e "${BOLD}${YELLOW}⚡ PRODUTTIVITÀ: ECCELLENTE!${NC}"
    elif [ $total_net -gt 200 ]; then
        echo -e "${BOLD}${BLUE}👍 PRODUTTIVITÀ: BUONA!${NC}"
    else
        echo -e "${BOLD}${CYAN}📝 PRODUTTIVITÀ: STANDARD${NC}"
    fi
elif [ $total_net -lt 0 ]; then
    echo -e "${BOLD}${RED}🧹 GIORNATA DI REFACTORING: ${total_net} righe nette${NC}"
    echo -e "${BOLD}${PURPLE}✨ Codice ottimizzato e pulito!${NC}"
else
    echo -e "${BOLD}${CYAN}📊 NESSUNA MODIFICA NETTA OGGI${NC}"
fi

echo ""

# Lista dei commit di oggi
if [ $commits_today -gt 0 ]; then
    echo -e "${BOLD}${BLUE}📝 COMMIT DI OGGI:${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    git log --since="${DATE_FROM} 00:00:00" --until="${DATE_TO} 23:59:59" \
           --author="${AUTHOR_FILTER}" --oneline --decorate | head -10
fi

echo ""
echo -e "${BOLD}${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BOLD}Generated by: Padmin D. Curtis OS3.0 | $(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo -e "${BOLD}${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

# Help per uso futuro
echo ""
echo -e "${CYAN}💡 USO:${NC}"
echo -e "   ${0} [data-da] [data-a] [autore]"
echo -e "   ${0} 2025-09-28                    # Solo ieri"
echo -e "   ${0} 2025-09-01 2025-09-30        # Tutto settembre"
echo -e "   ${0} \$(date +%Y-%m-%d) \$(date +%Y-%m-%d) \"Fabio Cherici\"  # Oggi, autore specifico"