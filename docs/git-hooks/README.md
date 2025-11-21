# Git Hooks - FlorenceEGI

**Versione:** 1.0.0  
**Data:** 2025-11-21  
**Autore:** Fabio Cherici  
**Scopo:** Protezione codice critico e prevenzione errori accidentali

---

## 🎯 Panoramica

I Git Hooks sono script automatici che vengono eseguiti in momenti specifici del workflow Git. FlorenceEGI utilizza due hook principali per proteggere il codice da eliminazioni accidentali:

1. **pre-commit** - Verifica modifiche prima del commit
2. **pre-push** - Verifica commit prima del push

---

## 🔒 Hook Disponibili

### 1. PRE-COMMIT HOOK

**File:** `.git/hooks/pre-commit`  
**Esecuzione:** Automatica prima di ogni `git commit`  
**Scopo:** Previene eliminazione accidentale di codice

#### Regole di Protezione

Il pre-commit hook implementa 4 regole di sicurezza:

##### ⚠️ REGOLA 1: Blocco eliminazioni massive per file
```
TRIGGER: File rimuove più di 100 righe
ACTION: BLOCCA commit
BYPASS: git commit --no-verify
```

Esempio output:
```
❌ ERRORE CRITICO: File 'app/Services/UserService.php' rimuove 150 righe!
⚠️  Questo potrebbe essere un errore accidentale

Statistiche:
- Righe aggiunte: 10
- Righe rimosse: 150

Se è INTENZIONALE, usa:
git commit --no-verify
(solo se sei assolutamente sicuro!)
```

##### ⚠️ REGOLA 2: Warning eliminazioni moderate
```
TRIGGER: File rimuove tra 50 e 100 righe
ACTION: WARNING (commit permesso)
```

Esempio output:
```
⚠️  WARNING: File 'app/Http/Controllers/EgiController.php' rimuove 75 righe
Verifica che sia intenzionale
```

##### ⚠️ REGOLA 3: Blocco eliminazioni percentuali
```
TRIGGER: File rimuove più del 50% del contenuto
ACTION: BLOCCA commit
BYPASS: git commit --no-verify
```

Esempio output:
```
❌ ERRORE CRITICO: File 'config/app.php' rimuove 60% del contenuto!
Righe totali: 200
Righe rimosse: 120

Se è INTENZIONALE, usa:
git commit --no-verify
```

##### ⚠️ REGOLA 4: Blocco eliminazioni globali
```
TRIGGER: Commit rimuove più di 500 righe totali
ACTION: BLOCCA commit
BYPASS: git commit --no-verify
```

Esempio output:
```
❌ ERRORE CRITICO: Commit rimuove 650 righe totali!
⚠️  Questo è un cambiamento MASSICCIO

Verifica che:
1. Non hai fatto un reset accidentale
2. Non hai sovrascritto file con versioni vecchie
3. Hai verificato ogni file modificato

Se è INTENZIONALE, usa:
git commit --no-verify
```

#### Azioni Consigliate in Caso di Blocco

```bash
# 1. Verifica cosa stai committando
git diff --cached

# 2. Verifica statistiche dettagliate
git diff --cached --stat

# 3. Se è un errore, rimuovi file dallo stage
git restore --staged <file>

# 4. Se è intenzionale, bypassa il check
git commit --no-verify -m "Messaggio commit"
```

---

### 2. PRE-PUSH HOOK

**File:** `.git/hooks/pre-push`  
**Esecuzione:** Automatica prima di ogni `git push`  
**Scopo:** Doppia verifica prima di pushare commit pericolosi

#### Regola di Protezione

Il pre-push hook analizza tutti i commit che stai per pushare:

```
TRIGGER: Commit rimuove più di 500 righe
ACTION: BLOCCA push
BYPASS: git push --no-verify
```

#### Funzionamento

1. Identifica il branch remoto corrente
2. Trova tutti i commit da pushare (non ancora su remote)
3. Analizza statistiche di ogni commit
4. Blocca se trova commit pericolosi

Esempio output:
```
🔒 PRE-PUSH HOOK: Verifica protezione codice...

❌ ERRORE CRITICO: Commit a1b2c3d4 rimuove 650 righe!
Messaggio: Refactoring UserService

Verifica che sia intenzionale prima di pushare

════════════════════════════════════════════════════════════════
❌ PUSH BLOCCATO - COMMIT PERICOLOSI TROVATI
════════════════════════════════════════════════════════════════

Per sicurezza, il push è stato BLOCCATO.

Azioni consigliate:
1. Verifica commit: git log --stat
2. Se è un errore, ripristina: git reset HEAD~N
3. Se è intenzionale, usa: git push --no-verify
```

#### Azioni Consigliate in Caso di Blocco

```bash
# 1. Verifica commit da pushare
git log --stat origin/main..HEAD

# 2. Verifica dettagli commit specifico
git show <commit-hash> --stat

# 3. Se è un errore, rimuovi commit
git reset HEAD~1  # Rimuove ultimo commit (mantiene modifiche)
git reset --hard HEAD~1  # Rimuove ultimo commit (distruttivo)

# 4. Se è intenzionale, bypassa il check
git push --no-verify
```

---

## 📥 Installazione Hook

### Opzione 1: Installazione Manuale (Singola Stazione)

#### Step 1: Crea il file pre-commit

```bash
# Vai nella directory hooks
cd /home/fabio/EGI/.git/hooks

# Crea il file pre-commit
nano pre-commit
```

Copia il contenuto del file `pre-commit` (vedi Appendice A) e salva.

```bash
# Rendi eseguibile
chmod +x pre-commit
```

#### Step 2: Crea il file pre-push

```bash
# Crea il file pre-push
nano pre-push
```

Copia il contenuto del file `pre-push` (vedi Appendice B) e salva.

```bash
# Rendi eseguibile
chmod +x pre-push
```

#### Step 3: Verifica installazione

```bash
# Verifica che gli hook esistano e siano eseguibili
ls -la .git/hooks/pre-commit
ls -la .git/hooks/pre-push

# Output atteso:
# -rwxr-xr-x 1 fabio fabio 4321 Nov 21 10:30 pre-commit
# -rwxr-xr-x 1 fabio fabio 1234 Nov 21 10:30 pre-push
```

---

### Opzione 2: Script di Installazione Automatica

Per installare gli hook su tutte le stazioni di lavoro rapidamente, usa lo script di installazione automatica.

#### Step 1: Crea lo script di installazione

Salva questo script come `/home/fabio/EGI/scripts/install-git-hooks.sh`:

```bash
#!/bin/bash
#
# SCRIPT INSTALLAZIONE GIT HOOKS
# Installa pre-commit e pre-push hooks per protezione codice
#

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo ""
echo "════════════════════════════════════════════════════════════════"
echo "  GIT HOOKS INSTALLER - FlorenceEGI"
echo "════════════════════════════════════════════════════════════════"
echo ""

# Verifica di essere nella root del progetto
if [ ! -d ".git" ]; then
    echo -e "${RED}❌ ERRORE: Esegui questo script dalla root del progetto Git${NC}"
    echo "   cd /home/fabio/EGI"
    echo "   bash scripts/install-git-hooks.sh"
    exit 1
fi

HOOKS_DIR=".git/hooks"

# Backup hook esistenti se presenti
if [ -f "$HOOKS_DIR/pre-commit" ]; then
    echo -e "${YELLOW}⚠️  Hook pre-commit esistente trovato${NC}"
    cp "$HOOKS_DIR/pre-commit" "$HOOKS_DIR/pre-commit.backup.$(date +%Y%m%d_%H%M%S)"
    echo "   Backup creato: pre-commit.backup"
fi

if [ -f "$HOOKS_DIR/pre-push" ]; then
    echo -e "${YELLOW}⚠️  Hook pre-push esistente trovato${NC}"
    cp "$HOOKS_DIR/pre-push" "$HOOKS_DIR/pre-push.backup.$(date +%Y%m%d_%H%M%S)"
    echo "   Backup creato: pre-push.backup"
fi

echo ""
echo "📥 Installazione hooks..."
echo ""

# Copia hooks dalla directory sorgente
if [ -f "git-hooks/pre-commit" ]; then
    cp "git-hooks/pre-commit" "$HOOKS_DIR/pre-commit"
    chmod +x "$HOOKS_DIR/pre-commit"
    echo -e "${GREEN}✅ pre-commit hook installato${NC}"
else
    echo -e "${RED}❌ File git-hooks/pre-commit non trovato${NC}"
    exit 1
fi

if [ -f "git-hooks/pre-push" ]; then
    cp "git-hooks/pre-push" "$HOOKS_DIR/pre-push"
    chmod +x "$HOOKS_DIR/pre-push"
    echo -e "${GREEN}✅ pre-push hook installato${NC}"
else
    echo -e "${RED}❌ File git-hooks/pre-push non trovato${NC}"
    exit 1
fi

echo ""
echo "════════════════════════════════════════════════════════════════"
echo -e "${GREEN}✅ INSTALLAZIONE COMPLETATA${NC}"
echo "════════════════════════════════════════════════════════════════"
echo ""
echo "Hooks installati in: $HOOKS_DIR"
echo ""
echo "Test hook:"
echo "  git commit -m 'test'  # Testa pre-commit"
echo "  git push              # Testa pre-push"
echo ""
echo "Per disabilitare temporaneamente:"
echo "  git commit --no-verify"
echo "  git push --no-verify"
echo ""
```

#### Step 2: Crea la directory git-hooks con i file sorgente

```bash
# Crea directory per hooks versionati
mkdir -p /home/fabio/EGI/git-hooks

# Copia hooks da NATAN_LOC
cp /home/fabio/NATAN_LOC/.git/hooks/pre-commit /home/fabio/EGI/git-hooks/pre-commit
cp /home/fabio/NATAN_LOC/.git/hooks/pre-push /home/fabio/EGI/git-hooks/pre-push

# Rendi eseguibili
chmod +x /home/fabio/EGI/git-hooks/pre-commit
chmod +x /home/fabio/EGI/git-hooks/pre-push
```

#### Step 3: Rendi eseguibile lo script di installazione

```bash
chmod +x /home/fabio/EGI/scripts/install-git-hooks.sh
```

#### Step 4: Esegui l'installazione

```bash
cd /home/fabio/EGI
bash scripts/install-git-hooks.sh
```

Output atteso:
```
════════════════════════════════════════════════════════════════
  GIT HOOKS INSTALLER - FlorenceEGI
════════════════════════════════════════════════════════════════

📥 Installazione hooks...

✅ pre-commit hook installato
✅ pre-push hook installato

════════════════════════════════════════════════════════════════
✅ INSTALLAZIONE COMPLETATA
════════════════════════════════════════════════════════════════

Hooks installati in: .git/hooks

Test hook:
  git commit -m 'test'  # Testa pre-commit
  git push              # Testa pre-push
```

---

## 🧪 Test degli Hook

### Test 1: Pre-commit con file normale (passa)

```bash
# Modifica un file con poche righe
echo "// Test comment" >> app/Models/User.php

# Aggiungi e committa
git add app/Models/User.php
git commit -m "Test commit"

# Output atteso:
# 🔒 PRE-COMMIT HOOK: Verifica protezione codice...
# ✅ Pre-commit checks passati
```

### Test 2: Pre-commit con molte rimozioni (blocca)

```bash
# Simula eliminazione di molte righe
# (crea un file di test con 200 righe)
seq 1 200 > test_file.txt
git add test_file.txt
git commit -m "Add test file"

# Ora rimuovi molte righe
head -n 50 test_file.txt > temp && mv temp test_file.txt
git add test_file.txt
git commit -m "Remove many lines"

# Output atteso:
# ❌ ERRORE CRITICO: File 'test_file.txt' rimuove 150 righe!
# COMMIT BLOCCATO
```

### Test 3: Bypass del hook (quando intenzionale)

```bash
# Usa --no-verify per bypassare
git commit --no-verify -m "Intentional large deletion"

# Commit procede senza controlli
```

### Test 4: Pre-push (blocca se commit pericoloso)

```bash
# Se hai commit che rimuovono >500 righe
git push

# Output atteso (se commit pericoloso):
# ❌ ERRORE CRITICO: Commit a1b2c3d4 rimuove 650 righe!
# PUSH BLOCCATO
```

---

## 🚀 Distribuzione su Tutte le Stazioni

### Procedura per Setup Nuovo Developer

Quando un nuovo developer entra nel team:

```bash
# 1. Clona repository
git clone <repository-url> /home/developer/EGI
cd /home/developer/EGI

# 2. Installa hooks automaticamente
bash scripts/install-git-hooks.sh

# 3. Verifica installazione
ls -la .git/hooks/pre-commit
ls -la .git/hooks/pre-push

# 4. Test rapido
git commit --allow-empty -m "Test hooks"
```

### Aggiornamento Hook su Stazioni Esistenti

Se modifichi gli hook e vuoi aggiornarli su tutte le stazioni:

```bash
# 1. Committa nuove versioni degli hook in git-hooks/
cd /home/fabio/EGI
git add git-hooks/
git commit -m "[CHORE] Update git hooks"
git push

# 2. Su ogni stazione, pull e reinstalla
cd /home/developer/EGI
git pull
bash scripts/install-git-hooks.sh

# Output: backup automatico + installazione nuove versioni
```

---

## ⚙️ Configurazione Avanzata

### Personalizzazione Soglie

Puoi modificare le soglie di protezione editando i file hook:

#### Pre-commit Thresholds

Modifica `/home/fabio/EGI/git-hooks/pre-commit`:

```bash
# REGOLA 1: Blocco eliminazioni massive per file
# Linea 58 - Default: 100 righe
if [ "$DELETED" -gt 100 ]; then
# Cambia 100 con il valore desiderato

# REGOLA 2: Warning eliminazioni moderate
# Linea 74 - Default: 50-100 righe
if [ "$DELETED" -gt 50 ] && [ "$DELETED" -le 100 ]; then
# Cambia 50 e 100 con i valori desiderati

# REGOLA 3: Blocco eliminazioni percentuali
# Linea 85 - Default: 50%
if [ "$PERCENTAGE" -gt 50 ]; then
# Cambia 50 con la percentuale desiderata

# REGOLA 4: Blocco eliminazioni globali
# Linea 104 - Default: 500 righe totali
if [ "$TOTAL_DELETED" -gt 500 ]; then
# Cambia 500 con il valore desiderato
```

#### Pre-push Threshold

Modifica `/home/fabio/EGI/git-hooks/pre-push`:

```bash
# Linea 40 - Default: 500 righe per commit
if [ "$TOTAL_DELETED" -gt 500 ]; then
# Cambia 500 con il valore desiderato
```

Dopo le modifiche, redistribuisci con `bash scripts/install-git-hooks.sh`.

---

## 🔧 Troubleshooting

### Problema: Hook non si esegue

**Sintomo:** Commit procede senza output del hook

**Soluzioni:**

```bash
# 1. Verifica che il file esista
ls -la .git/hooks/pre-commit

# 2. Verifica che sia eseguibile
chmod +x .git/hooks/pre-commit

# 3. Verifica shebang (prima riga)
head -n 1 .git/hooks/pre-commit
# Output atteso: #!/bin/bash

# 4. Test manuale
.git/hooks/pre-commit
# Dovrebbe eseguirsi senza errori
```

---

### Problema: Hook blocca erroneamente

**Sintomo:** Hook blocca commit legittimi

**Soluzioni:**

```bash
# Opzione 1: Bypass temporaneo
git commit --no-verify -m "Messaggio"

# Opzione 2: Verifica cosa sta bloccando
git diff --cached --stat

# Opzione 3: Staged solo file specifici
git add file1.php file2.php
git commit -m "Partial commit"
# Poi: git add altri_file.php && git commit

# Opzione 4: Modifica soglie hook
# Edita git-hooks/pre-commit e aumenta i limiti
```

---

### Problema: Hook troppo permissivi

**Sintomo:** Hook non blocca modifiche pericolose

**Soluzioni:**

```bash
# Riduci le soglie in git-hooks/pre-commit

# REGOLA 1: Rimuove >100 righe → cambia a 50
if [ "$DELETED" -gt 50 ]; then

# REGOLA 4: Rimuove >500 righe totali → cambia a 200
if [ "$TOTAL_DELETED" -gt 200 ]; then

# Reinstalla
bash scripts/install-git-hooks.sh
```

---

### Problema: Performance lenta

**Sintomo:** Hook impiega molto tempo

**Causa:** Repository molto grande con migliaia di file

**Soluzioni:**

```bash
# 1. Escludi file binari (già implementato)
# Gli hook saltano automaticamente file binari

# 2. Commit incrementali più piccoli
git add src/
git commit -m "Update src"
git add tests/
git commit -m "Update tests"

# 3. Bypass hook per commit safe
git commit --no-verify -m "Safe refactor"
```

---

## 📊 Statistiche e Monitoraggio

### Verifica Protezioni Attive

```bash
# Verifica hook installati
ls -la .git/hooks/ | grep -E "pre-commit|pre-push"

# Output atteso:
# -rwxr-xr-x 1 fabio fabio 4321 Nov 21 10:30 pre-commit
# -rwxr-xr-x 1 fabio fabio 1234 Nov 21 10:30 pre-push
```

### Log Bypass Hook (--no-verify)

Git non logga automaticamente l'uso di `--no-verify`. Per tracciarlo, aggiungi questo al tuo `.bashrc`:

```bash
# Git alias con logging
git() {
    if [[ "$*" == *"--no-verify"* ]]; then
        echo "[$(date)] Git hook bypass: $*" >> ~/.git-hook-bypass.log
    fi
    command git "$@"
}
```

Poi verifica:
```bash
cat ~/.git-hook-bypass.log
```

---

## 🔐 Best Practices

### ✅ DO (Raccomandato)

1. **Installa hook su tutte le stazioni di lavoro**
   - Protezione consistente per tutto il team

2. **Test modifiche prima del commit**
   ```bash
   git diff --stat  # Verifica statistiche
   git diff         # Verifica modifiche dettagliate
   ```

3. **Commit incrementali**
   - Commit piccoli e frequenti invece di mega-commit

4. **Usa branch feature**
   - Branch separati per refactoring grandi
   - Merge dopo review accurata

5. **Documenta bypass**
   ```bash
   git commit --no-verify -m "[REFACTOR] Major cleanup - Reviewed by: @fabio"
   ```

6. **Update hook quando necessario**
   - Committa modifiche in `git-hooks/`
   - Comunica al team di reinstallare

---

### ❌ DON'T (Evita)

1. **Non bypassare hook di routine**
   - `--no-verify` dovrebbe essere raro

2. **Non modificare `.git/hooks/` direttamente**
   - Usa `git-hooks/` sorgente + script installazione
   - Modifiche dirette non versionabili

3. **Non disabilitare hook permanentemente**
   ```bash
   # ❌ SBAGLIATO
   rm .git/hooks/pre-commit
   ```

4. **Non committare file `.git/hooks/`**
   - `.git/` è in .gitignore per design
   - Usa `git-hooks/` per versionare

5. **Non ignorare warning**
   - Warning = verifica necessaria
   - Anche se commit procede, verifica il codice

---

## 📚 Riferimenti e Risorse

### Git Hooks Documentation

- [Git Hooks Official Docs](https://git-scm.com/book/en/v2/Customizing-Git-Git-Hooks)
- [Git Hooks Tutorial](https://www.atlassian.com/git/tutorials/git-hooks)

### Hook Management Tools (Alternative)

Se il progetto cresce, considera:

- **Husky** - Hook manager per Node.js projects
- **Lefthook** - Fast multi-language hook manager
- **pre-commit framework** - Python-based hook framework

---

## 📝 Changelog

### Version 1.0.0 (2025-11-21)

**Initial Release:**
- ✅ Pre-commit hook con 4 regole di protezione
- ✅ Pre-push hook per verifica commit pericolosi
- ✅ Script di installazione automatica
- ✅ Documentazione completa
- ✅ Procedure test e troubleshooting

---

## 👥 Contributi

Per suggerire modifiche agli hook:

1. Modifica file in `git-hooks/`
2. Testa localmente:
   ```bash
   bash scripts/install-git-hooks.sh
   # Test commit/push
   ```
3. Committa modifiche:
   ```bash
   git add git-hooks/
   git commit -m "[CHORE] Update git hooks: [descrizione]"
   ```
4. Comunica al team di aggiornare:
   ```
   @team: Ho aggiornato i git hooks. 
   Eseguite: git pull && bash scripts/install-git-hooks.sh
   ```

---

## 🆘 Supporto

Per problemi con gli hook:

1. **Controlla troubleshooting** in questo documento
2. **Verifica installazione**:
   ```bash
   ls -la .git/hooks/
   .git/hooks/pre-commit  # Test manuale
   ```
3. **Contatta team lead** se problema persiste

---

## ⚖️ Licenza

Questi hook fanno parte del progetto FlorenceEGI e seguono la stessa licenza del progetto principale.

---

**Documento creato:** 2025-11-21  
**Ultima modifica:** 2025-11-21  
**Versione:** 1.0.0  
**Maintainer:** Fabio Cherici (@fabio)

---

## 📎 Appendici

### Appendice A: Codice Completo pre-commit

Vedi file: `/home/fabio/EGI/git-hooks/pre-commit`

### Appendice B: Codice Completo pre-push

Vedi file: `/home/fabio/EGI/git-hooks/pre-push`

### Appendice C: Script di Installazione

Vedi file: `/home/fabio/EGI/scripts/install-git-hooks.sh`

---

**Fine Documentazione** 🎯

