#!/bin/bash

# 📊 EGI Commit Range Statistics
# Author: Fabio Cherici
# Purpose: Analyze commits for each day in a given date range

# Funzione per mostrare l'uso
show_usage() {
    echo "📊 EGI Commit Range Statistics"
    echo "================================"
    echo ""
    echo "Usage: $0 [START_DATE] [END_DATE]"
    echo ""
    echo "Esempi:"
    echo "  $0 2025-08-01 2025-08-19    # Agosto 1-19"
    echo "  $0 2025-08-15               # Dal 15 agosto ad oggi"
    echo "  $0                          # Ultimi 7 giorni"
    echo ""
    echo "Formato date: YYYY-MM-DD"
    exit 1
}

# Funzione per validare le date
validate_date() {
    date -d "$1" >/dev/null 2>&1
    return $?
}

# Parametri input
START_DATE="$1"
END_DATE="$2"

# Se non specificato, usa gli ultimi 7 giorni
if [ -z "$START_DATE" ]; then
    START_DATE=$(date -d "7 days ago" +%Y-%m-%d)
    END_DATE=$(date +%Y-%m-%d)
    echo "ℹ️  Nessuna data specificata, usando ultimi 7 giorni"
elif [ -z "$END_DATE" ]; then
    # Se solo start_date è specificata, usa oggi come end_date
    END_DATE=$(date +%Y-%m-%d)
    echo "ℹ️  End date non specificata, usando oggi ($END_DATE)"
fi

# Validazione date
if ! validate_date "$START_DATE"; then
    echo "❌ Data di inizio non valida: $START_DATE"
    show_usage
fi

if ! validate_date "$END_DATE"; then
    echo "❌ Data di fine non valida: $END_DATE"
    show_usage
fi

# Controllo che start_date <= end_date
if [[ "$START_DATE" > "$END_DATE" ]]; then
    echo "❌ La data di inizio deve essere precedente o uguale alla data di fine"
    exit 1
fi

echo "📊 EGI Commit Statistics - Range Analysis"
echo "========================================"
echo "📅 Periodo: $START_DATE → $END_DATE"
echo ""

# Variabili per statistiche totali
TOTAL_COMMITS=0
TOTAL_DAYS=0
MAX_COMMITS=0
MAX_DATE=""
MIN_COMMITS=999999
MIN_DATE=""
PRODUCTIVE_DAYS=0

# Array per memorizzare i dati
declare -a daily_stats

# Loop attraverso ogni giorno nell'intervallo
current_date="$START_DATE"

echo "📈 Dettaglio giornaliero:"
echo "------------------------"

while [[ "$current_date" != $(date -d "$END_DATE + 1 day" +%Y-%m-%d) ]]; do
    # Calcola i commit per il giorno corrente
    commits_count=$(git log --oneline --since="$current_date 00:00:00" --until="$current_date 23:59:59" | wc -l)

    # Nome del giorno
    day_name=$(date -d "$current_date" +"%A")

    # Emoji basata sul numero di commit
    if [ $commits_count -eq 0 ]; then
        emoji="😴"
    elif [ $commits_count -le 3 ]; then
        emoji="📊"
    elif [ $commits_count -le 7 ]; then
        emoji="🔥"
    elif [ $commits_count -le 15 ]; then
        emoji="🚀"
    else
        emoji="⚡"
    fi

    # Mostra il risultato
    printf "%s %s (%s): %2d commit\n" "$emoji" "$current_date" "$day_name" "$commits_count"

    # Aggiorna statistiche
    TOTAL_COMMITS=$((TOTAL_COMMITS + commits_count))
    TOTAL_DAYS=$((TOTAL_DAYS + 1))

    if [ $commits_count -gt 0 ]; then
        PRODUCTIVE_DAYS=$((PRODUCTIVE_DAYS + 1))
    fi

    if [ $commits_count -gt $MAX_COMMITS ]; then
        MAX_COMMITS=$commits_count
        MAX_DATE=$current_date
    fi

    if [ $commits_count -lt $MIN_COMMITS ] && [ $commits_count -gt 0 ]; then
        MIN_COMMITS=$commits_count
        MIN_DATE=$current_date
    fi

    # Memorizza per grafico
    daily_stats+=("$current_date:$commits_count")

    # Prossimo giorno
    current_date=$(date -d "$current_date + 1 day" +%Y-%m-%d)
done

echo ""
echo "📊 Statistiche del periodo:"
echo "---------------------------"
echo "📅 Giorni totali: $TOTAL_DAYS"
echo "📈 Commit totali: $TOTAL_COMMITS"
echo "🎯 Giorni produttivi: $PRODUCTIVE_DAYS/$TOTAL_DAYS"

# Analisi commit per TAG (nuova funzionalità)
echo ""
echo "🏷️  Analisi per categorie (TAG):"
echo "--------------------------------"

# Contatori per i TAG
FEAT_COUNT=0
FIX_COUNT=0
REFACTOR_COUNT=0
DOC_COUNT=0
TEST_COUNT=0
CHORE_COUNT=0
OTHER_COUNT=0

# Analizza tutti i commit nel range per TAG
while read -r commit_message; do
    if [[ "$commit_message" =~ ^\[FEAT\] ]]; then
        FEAT_COUNT=$((FEAT_COUNT + 1))
    elif [[ "$commit_message" =~ ^\[FIX\] ]]; then
        FIX_COUNT=$((FIX_COUNT + 1))
    elif [[ "$commit_message" =~ ^\[REFACTOR\] ]]; then
        REFACTOR_COUNT=$((REFACTOR_COUNT + 1))
    elif [[ "$commit_message" =~ ^\[DOC\] ]]; then
        DOC_COUNT=$((DOC_COUNT + 1))
    elif [[ "$commit_message" =~ ^\[TEST\] ]]; then
        TEST_COUNT=$((TEST_COUNT + 1))
    elif [[ "$commit_message" =~ ^\[CHORE\] ]]; then
        CHORE_COUNT=$((CHORE_COUNT + 1))
    else
        OTHER_COUNT=$((OTHER_COUNT + 1))
    fi
done < <(git log --since="$START_DATE 00:00:00" --until="$END_DATE 23:59:59" --pretty=format:"%s")

# Mostra statistiche TAG se ci sono commit con TAG
TAGGED_COMMITS=$((FEAT_COUNT + FIX_COUNT + REFACTOR_COUNT + DOC_COUNT + TEST_COUNT + CHORE_COUNT))

if [ $TAGGED_COMMITS -gt 0 ]; then
    echo "🔥 [FEAT]     - Nuove funzionalità: $FEAT_COUNT"
    echo "🔧 [FIX]      - Correzioni bug: $FIX_COUNT"
    echo "♻️  [REFACTOR] - Refactoring: $REFACTOR_COUNT"
    echo "📚 [DOC]      - Documentazione: $DOC_COUNT"
    echo "🧪 [TEST]     - Test: $TEST_COUNT"
    echo "🛠️  [CHORE]    - Manutenzione: $CHORE_COUNT"
    echo "---"
    echo "📊 Commit con TAG: $TAGGED_COMMITS/$TOTAL_COMMITS"
    if [ $OTHER_COUNT -gt 0 ]; then
        echo "❓ Commit senza TAG: $OTHER_COUNT"
    fi

    # Percentuali
    if [ $TOTAL_COMMITS -gt 0 ]; then
        TAG_PERCENTAGE=$(echo "scale=1; $TAGGED_COMMITS * 100 / $TOTAL_COMMITS" | bc -l 2>/dev/null || echo "0")
        echo "📈 Copertura TAG: $TAG_PERCENTAGE%"
    fi

    # Tipo più comune
    MAX_TAG_COUNT=0
    MAX_TAG_TYPE=""

    if [ $FEAT_COUNT -gt $MAX_TAG_COUNT ]; then
        MAX_TAG_COUNT=$FEAT_COUNT
        MAX_TAG_TYPE="FEAT (funzionalità)"
    fi
    if [ $FIX_COUNT -gt $MAX_TAG_COUNT ]; then
        MAX_TAG_COUNT=$FIX_COUNT
        MAX_TAG_TYPE="FIX (correzioni)"
    fi
    if [ $REFACTOR_COUNT -gt $MAX_TAG_COUNT ]; then
        MAX_TAG_COUNT=$REFACTOR_COUNT
        MAX_TAG_TYPE="REFACTOR (ristrutturazione)"
    fi
    if [ $DOC_COUNT -gt $MAX_TAG_COUNT ]; then
        MAX_TAG_COUNT=$DOC_COUNT
        MAX_TAG_TYPE="DOC (documentazione)"
    fi
    if [ $TEST_COUNT -gt $MAX_TAG_COUNT ]; then
        MAX_TAG_COUNT=$TEST_COUNT
        MAX_TAG_TYPE="TEST (testing)"
    fi
    if [ $CHORE_COUNT -gt $MAX_TAG_COUNT ]; then
        MAX_TAG_COUNT=$CHORE_COUNT
        MAX_TAG_TYPE="CHORE (manutenzione)"
    fi

    if [ $MAX_TAG_COUNT -gt 0 ]; then
        echo "🏆 Tipo più frequente: $MAX_TAG_TYPE ($MAX_TAG_COUNT commit)"
    fi
else
    echo "ℹ️  Nessun commit con TAG trovato nel periodo"
    echo "💡 I TAG verranno tracciati dai prossimi commit"
fi

if [ $TOTAL_DAYS -gt 0 ]; then
    AVERAGE=$(echo "scale=1; $TOTAL_COMMITS / $TOTAL_DAYS" | bc -l 2>/dev/null || echo "N/A")
    echo "📊 Media commit/giorno: $AVERAGE"
fi

if [ $PRODUCTIVE_DAYS -gt 0 ]; then
    AVG_PRODUCTIVE=$(echo "scale=1; $TOTAL_COMMITS / $PRODUCTIVE_DAYS" | bc -l 2>/dev/null || echo "N/A")
    echo "🔥 Media nei giorni produttivi: $AVG_PRODUCTIVE"
fi

echo ""
echo "🏆 Record del periodo:"
echo "---------------------"
if [ $MAX_COMMITS -gt 0 ]; then
    echo "🥇 Giorno più produttivo: $MAX_DATE ($MAX_COMMITS commit)"
fi

if [ $MIN_COMMITS -lt 999999 ]; then
    echo "🥉 Giorno meno produttivo: $MIN_DATE ($MIN_COMMITS commit)"
fi

# Calcola produttività percentuale
if [ $TOTAL_DAYS -gt 0 ]; then
    PRODUCTIVITY_PERCENT=$(echo "scale=1; $PRODUCTIVE_DAYS * 100 / $TOTAL_DAYS" | bc -l 2>/dev/null || echo "0")
    echo "📈 Produttività: $PRODUCTIVITY_PERCENT% ($PRODUCTIVE_DAYS giorni su $TOTAL_DAYS)"
fi

echo ""
echo "📊 Grafico a barre semplice:"
echo "-----------------------------"

# Trova il massimo per scalare il grafico
max_for_graph=$MAX_COMMITS
if [ $max_for_graph -eq 0 ]; then
    max_for_graph=1
fi

# Mostra grafico semplice
for stat in "${daily_stats[@]}"; do
    date_part=$(echo "$stat" | cut -d: -f1)
    count_part=$(echo "$stat" | cut -d: -f2)

    # Calcola lunghezza barra (max 20 caratteri)
    if [ $count_part -gt 0 ] && [ $max_for_graph -gt 0 ]; then
        bar_length=$(echo "scale=0; $count_part * 20 / $max_for_graph" | bc -l 2>/dev/null || echo "1")
        if [ $bar_length -eq 0 ] && [ $count_part -gt 0 ]; then
            bar_length=1
        fi
    else
        bar_length=0
    fi

    # Crea barra
    bar=""
    for ((i=1; i<=bar_length; i++)); do
        bar+="█"
    done

    printf "%s │%s %d\n" "$date_part" "$bar" "$count_part"
done

echo ""
echo "📝 Statistiche righe di codice:"
echo "--------------------------------"

# Lista commit da escludere (grosse rimozioni tipo .history folder)
EXCLUDE_COMMITS="6756853"

# Calcola statistiche ESCLUDENDO commit di cleanup
CODE_STATS=$(git log --since="$START_DATE 00:00:00" --until="$END_DATE 23:59:59" \
             --pretty=format:"%H" | \
             grep -v -E "(6756853)" | \
             xargs -I {} git show {} --numstat --format="" 2>/dev/null | \
             awk '{files++; added+=$1; removed+=$2} END {print files+0, added+0, removed+0}')

read FILES_CHANGED INSERTIONS DELETIONS <<< "$CODE_STATS"
NET_LINES=$((INSERTIONS - DELETIONS))

if [ $TOTAL_COMMITS -gt 0 ]; then
    echo "📁 File modificati: $FILES_CHANGED"
    echo "➕ Righe aggiunte: $INSERTIONS"
    echo "➖ Righe rimosse: $DELETIONS"

    if [ $NET_LINES -ge 0 ]; then
        echo "🚀 Righe nette: +$NET_LINES"
    else
        echo "📉 Righe nette: $NET_LINES"
    fi

    echo "ℹ️  Nota: Commit di cleanup massicci (es. .history) esclusi dalle statistiche"
else
    echo "ℹ️  Nessun commit nel range specificato"
fi

echo ""
echo "💡 Suggerimenti Intelligenti:"
echo "----------------------------"

# Analisi pattern di lavoro
COMMITS_PER_DAY=$(echo "scale=1; $TOTAL_COMMITS / $PRODUCTIVE_DAYS" | bc -l 2>/dev/null || echo "0")
LINES_PER_COMMIT=$(echo "scale=0; $NET_LINES / $TOTAL_COMMITS" | bc -l 2>/dev/null || echo "0")

# Valutazione costanza INTELLIGENTE (considera anche la produttività)
if [ $NET_LINES -gt 20000 ]; then
    # Se hai fatto +20k righe, sei un GENIO indipendentemente dai giorni
    echo "🚀 PRODUTTIVITÀ ECCEZIONALE: +$NET_LINES righe in $PRODUCTIVE_DAYS giorni!"
    echo "   💪 Concentrazione intensiva e risultati straordinari!"
elif [ $NET_LINES -gt 10000 ]; then
    # Se hai fatto +10k righe, sei molto produttivo
    echo "💪 PRODUTTIVITÀ ELEVATA: +$NET_LINES righe in $PRODUCTIVE_DAYS giorni!"
    echo "   ⚡ Ottima efficienza e focus!"
elif [ $PRODUCTIVITY_PERCENT -lt 40 ]; then
    # Solo se produttività bassa E costanza bassa
    echo "📅 Costanza bassa ($PRODUCTIVITY_PERCENT%): considera sessioni più regolari"
elif [ $PRODUCTIVITY_PERCENT -lt 70 ]; then
    echo "📊 Costanza media ($PRODUCTIVITY_PERCENT%): buon equilibrio lavoro/riposo"
elif [ $PRODUCTIVITY_PERCENT -lt 90 ]; then
    echo "⭐ Ottima costanza ($PRODUCTIVITY_PERCENT%): ritmo sostenibile"
else
    echo "🔥 Costanza eccellente ($PRODUCTIVITY_PERCENT%): produttività massima!"
fi

# Valutazione dimensione commit
if [ $(echo "$COMMITS_PER_DAY > 15" | bc -l 2>/dev/null || echo "0") -eq 1 ]; then
    echo "📝 Molti commit/giorno ($COMMITS_PER_DAY): ottimo per tracciabilità!"
elif [ $(echo "$COMMITS_PER_DAY < 3" | bc -l 2>/dev/null || echo "0") -eq 1 ] && [ $NET_LINES -gt 1000 ]; then
    echo "⚡ Pochi commit/giorno ($COMMITS_PER_DAY): considera commit più frequenti"
fi

# Nessun suggerimento ridondante - già coperto sopra

# Suggerimenti basati sui TAG
if [ $TAGGED_COMMITS -gt 0 ]; then
    echo ""
    echo "🏷️  Suggerimenti sui TAG:"
    echo "------------------------"

    if [ $TAG_PERCENTAGE != "N/A" ] && [ $(echo "$TAG_PERCENTAGE < 80" | bc -l 2>/dev/null || echo "1") -eq 1 ]; then
        echo "📝 Considera di aggiungere TAG ai commit esistenti per migliore tracciabilità"
    fi

    # Suggerimenti sul bilanciamento
    if [ $FIX_COUNT -gt $((FEAT_COUNT * 2)) ]; then
        echo "🔧 Molte correzioni: considera più testing preventivo"
    elif [ $FEAT_COUNT -gt $((FIX_COUNT * 3)) ] && [ $TEST_COUNT -eq 0 ]; then
        echo "🧪 Molte funzionalità: aggiungi più test per stabilità"
    fi

    if [ $DOC_COUNT -eq 0 ] && [ $FEAT_COUNT -gt 3 ]; then
        echo "📚 Aggiungi documentazione per le nuove funzionalità"
    fi

    if [ $REFACTOR_COUNT -eq 0 ] && [ $TOTAL_COMMITS -gt 10 ]; then
        echo "♻️  Considera periodici refactoring per mantenere il codice pulito"
    fi

    # Suggerimento sul focus
    if [[ "$MAX_TAG_TYPE" == "FEAT (funzionalità)" ]]; then
        echo "🚀 Focus su sviluppo: ottimo per crescita del progetto!"
    elif [[ "$MAX_TAG_TYPE" == "FIX (correzioni)" ]]; then
        echo "🔧 Focus su stabilità: buono per consolidamento"
    elif [[ "$MAX_TAG_TYPE" == "REFACTOR (ristrutturazione)" ]]; then
        echo "♻️  Focus su qualità: eccellente per manutenibilità"
    fi
fi

echo ""
echo "========================================="
echo "📊 Report completato - $(date +"%Y-%m-%d %H:%M:%S")"
