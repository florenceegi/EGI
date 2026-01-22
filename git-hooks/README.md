# Git Hooks - FlorenceEGI

**Versione:** 2.0.0  
**Data:** 2026-01-22  
**Compatibile con:** TAG System v2.0

Questa directory contiene i file sorgente per i Git hooks del progetto.

## 🚀 Installazione Rapida

```bash
# Dalla root del progetto
cd /home/fabio/EGI
bash scripts/install-git-hooks.sh
```

## 📋 Hook Disponibili

-   **pre-commit** - Previene eliminazione accidentale di codice
-   **pre-push** - Doppia verifica prima del push

## 🔒 Regole di Protezione

### Pre-Commit Hook

| Regola | Trigger                          | Azione           |
| ------ | -------------------------------- | ---------------- |
| **R1** | File rimuove >100 righe          | 🛑 BLOCCA commit |
| **R2** | File rimuove 50-100 righe        | ⚠️ WARNING       |
| **R3** | File rimuove >50% contenuto      | 🛑 BLOCCA commit |
| **R4** | Commit rimuove >500 righe totali | 🛑 BLOCCA commit |

### Pre-Push Hook

| Regola | Trigger                   | Azione         |
| ------ | ------------------------- | -------------- |
| **R5** | Commit rimuove >500 righe | 🛑 BLOCCA push |

## 🔧 Bypass Temporaneo

Se necessario (solo per modifiche intenzionali):

```bash
git commit --no-verify -m "[TAG] Messaggio"
git push --no-verify
```

## 📝 TAG System v2.0 - Formato Commit Richiesto

I commit message devono iniziare con un tag valido. **16 tags disponibili** con pesi per analytics:

### Tags Principali (Development)

| Tag          | Peso | Uso                          | Icona |
| ------------ | ---- | ---------------------------- | ----- |
| `[FEAT]`     | 1.0  | Nuova funzionalità           | ✨    |
| `[FIX]`      | 1.5  | Bug fix                      | 🐛    |
| `[REFACTOR]` | 2.0  | Refactoring (debt repayment) | ♻️    |

### Tags Quality & Testing

| Tag       | Peso | Uso                | Icona |
| --------- | ---- | ------------------ | ----- |
| `[TEST]`  | 1.2  | Test code          | 🧪    |
| `[DEBUG]` | 1.3  | Sessione debugging | 🔍    |

### Tags Documentation & Config

| Tag        | Peso | Uso            | Icona |
| ---------- | ---- | -------------- | ----- |
| `[DOC]`    | 0.8  | Documentazione | 📚    |
| `[CONFIG]` | 0.7  | Configurazione | 🔧    |

### Tags Maintenance

| Tag       | Peso | Uso               | Icona |
| --------- | ---- | ----------------- | ----- |
| `[CHORE]` | 0.6  | Maintenance tasks | 🔨    |
| `[I18N]`  | 0.7  | Traduzioni/locale | 🌍    |

### Tags Special Categories

| Tag          | Peso | Uso                      | Icona |
| ------------ | ---- | ------------------------ | ----- |
| `[PERF]`     | 1.4  | Performance optimization | ⚡    |
| `[SECURITY]` | 1.8  | Security fix/enhancement | 🔒    |
| `[WIP]`      | 0.3  | Work in progress         | 🚧    |
| `[REVERT]`   | 0.5  | Revert commit            | ⏪    |
| `[MERGE]`    | 0.4  | Merge commit             | 🔀    |
| `[DEPLOY]`   | 0.8  | Deployment               | 🚀    |
| `[UPDATE]`   | 0.6  | Generic update           | 📦    |

### Alias Supportati

-   **Bracket**: `[FEAT]`, `[FIX]`, `[REFACTOR]`
-   **Conventional**: `feat:`, `fix:`, `refactor:`
-   **Emoji**: ✨, 🐛, ♻️

### Esempi

```bash
# Bracket format (preferito)
git commit -m "[FEAT] Aggiunta funzionalità export dati"

# Conventional commits
git commit -m "fix: corretto bug nella validazione"

# Con emoji
git commit -m "✨ Nuova dashboard analytics"
```

## 📚 Documentazione Completa

Per documentazione dettagliata, regole, troubleshooting e best practices:

```bash
cat docs/git-hooks/README.md
```

O apri: `/home/fabio/EGI/docs/git-hooks/README.md`

## ⚠️ Note Importanti

1. Questi file sono **sorgenti versionati** - modificali qui, non in `.git/hooks/`
2. Dopo modifiche, esegui `bash scripts/install-git-hooks.sh` per applicare
3. Gli hook **non** si sincronizzano automaticamente con git pull

## 🆘 Supporto

Se gli hook causano problemi, consulta la sezione Troubleshooting nella documentazione completa.
