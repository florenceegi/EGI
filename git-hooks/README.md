# Git Hooks - FlorenceEGI

Questa directory contiene i file sorgente per i Git hooks del progetto.

## 🚀 Installazione Rapida

```bash
# Dalla root del progetto
cd /home/fabio/EGI
bash scripts/install-git-hooks.sh
```

## 📋 Hook Disponibili

- **pre-commit** - Previene eliminazione accidentale di codice (>100 righe per file, >500 totali)
- **pre-push** - Doppia verifica prima del push (>500 righe per commit)

## 📚 Documentazione Completa

Per documentazione dettagliata, regole, troubleshooting e best practices:

```bash
cat docs/git-hooks/README.md
```

O apri: `/home/fabio/EGI/docs/git-hooks/README.md`

## 🔧 Bypass Temporaneo

Se necessario (solo per modifiche intenzionali):

```bash
git commit --no-verify -m "[TAG] Messaggio"
git push --no-verify
```

## 📝 Formato Commit Richiesto

I commit message devono iniziare con un tag valido:

```
[FEAT]     - nuova feature
[FIX]      - bug risolto
[REFACTOR] - refactoring
[DOC]      - documentazione
[TEST]     - test
[CHORE]    - maintenance
```

Esempio: `[FEAT] Aggiunta funzionalità export dati`

## ⚠️ Note Importanti

1. Questi file sono **sorgenti versionati** - modificali qui, non in `.git/hooks/`
2. Dopo modifiche, esegui `bash scripts/install-git-hooks.sh` per applicare
3. Gli hook **non** si sincronizzano automaticamente con git pull

## 🆘 Supporto

Se gli hook causano problemi, consulta la sezione Troubleshooting nella documentazione completa.

