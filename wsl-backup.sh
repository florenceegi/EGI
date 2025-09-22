#!/bin/bash
# Percorsi delle cartelle di backup
backup_base_dir="/mnt/c/wsl_backup"
timestamp=$(date +%Y%m%d_%H%M)
backup_dest_c="${backup_base_dir}/${timestamp}"
LOG_FILE="../backup_log/logfile.log"  # Modificato per essere relativo alla directory EGI

# Funzione per verificare se un drive è disponibile
check_drive() {
    local drive_path="$1"
    local drive_letter="$2"
    if [ ! -d "$(dirname "$drive_path")" ]; then
        echo "ATTENZIONE: Drive $drive_letter non disponibile, skip della copia" >> "$LOG_FILE"
        return 1
    fi
    return 0
}

# Percorsi delle cartelle di backup aggiuntive
drive_d="/mnt/d/Il\ mio\ Drive/EGI_backup/${timestamp}"
drive_e="/mnt/e/EGI_backup/${timestamp}"
drive_h="/mnt/h/EGI_backup/${timestamp}"

# Directory da escludere dal backup
exclude_dirs=(
    "--exclude=.venv/"
    "--exclude=.vapor/"
    "--exclude=vendor/"
    "--exclude=storage/"
    "--exclude=node_modules/"
    "--exclude=.git/"
    "--exclude=.config/"
    "--exclude=.cache/"
    "--exclude=.history/"
    "--exclude=.vscode/"
    "--exclude=public/images/"
)

# Inizio log operazione
mkdir -p "$(dirname "$LOG_FILE")"  # Crea la directory del log se non esiste
echo "===== Inizio backup $(date) =====" >> "$LOG_FILE"

# Verifica e creazione directory principale
if [ ! -d "$backup_base_dir" ]; then
    echo "ERRORE: Directory base di backup non accessibile: $backup_base_dir" >> "$LOG_FILE"
    exit 1
fi

mkdir -p "$backup_dest_c"

# Comando rsync principale
if rsync -avz "${exclude_dirs[@]}" ./ "$backup_dest_c"; then  # Modificato per usare ./ come source
    echo "✓ Backup principale completato in: $backup_dest_c" >> "$LOG_FILE"
else
    echo "✗ ERRORE nel backup principale!" >> "$LOG_FILE"
    exit 1
fi

# Copia nelle altre destinazioni
# Drive D
if check_drive "$drive_d" "D:"; then
    mkdir -p "$drive_d"
    if rsync -av "$backup_dest_c/" "$drive_d"; then
        echo "✓ Copia su D: completata in $drive_d" >> "$LOG_FILE"
    else
        echo "✗ ERRORE nella copia su D:" >> "$LOG_FILE"
    fi
fi

# Drive E
if check_drive "$drive_e" "E:"; then
    mkdir -p "$drive_e"
    if rsync -av "$backup_dest_c/" "$drive_e"; then
        echo "✓ Copia su E: completata in $drive_e" >> "$LOG_FILE"
    else
        echo "✗ ERRORE nella copia su E:" >> "$LOG_FILE"
    fi
fi

# Drive H
if check_drive "$drive_h" "H:"; then
    mkdir -p "$drive_h"
    if rsync -av "$backup_dest_c/" "$drive_h"; then
        echo "✓ Copia su H: completata in $drive_h" >> "$LOG_FILE"
    else
        echo "✗ ERRORE nella copia su H:" >> "$LOG_FILE"
    fi
fi

# Fine log operazione
echo "===== Fine backup $(date) =====" >> "$LOG_FILE"
echo "" >> "$LOG_FILE"  # Linea vuota per separare le sessioni di backup
