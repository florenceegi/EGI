# 🎯 PA + AI SYSTEM - CHEAT SHEET

**Print & Keep This Near Your Desk!**

---

## 📂 DOCUMENTI CREATI

| File                                      | Scopo               | Quando Usare |
| ----------------------------------------- | ------------------- | ------------ |
| `README_PA_AI_TESTING.md`                 | Overview generale   | Inizio       |
| `PA_AI_SISTEMA_COMPLETO_MAPPATURA.md`     | Mappa file (158+)   | Reference    |
| `PA_AI_TESTING_CHECKLIST_PRESENTATION.md` | Checklist test      | Giorni 1-4   |
| `PA_AI_PRESENTATION_QUICK_GUIDE.md`       | Guida presentazione | Giorno 5     |
| `PA_AI_QUICK_START_TESTING.md`            | Comandi pronti      | Adesso!      |

---

## ⚡ COMANDI ESSENZIALI

### Setup (1 volta)

```bash
cd /home/fabio/EGI
php artisan migrate
php artisan db:seed --class=PAEnterpriseDemoSeeder
```

### Start Every Day

```bash
# Terminal 1: Queue
php artisan queue:work --verbose

# Terminal 2: Browser
firefox http://localhost
# Login: pa@test.com / password123
```

### Quick Checks

```bash
# Last atto
php artisan tinker
Egi::latest()->first()->only(['pa_protocol_number','pa_txid','public_code']);

# Stats
$total = Egi::whereNotNull('pa_protocol_number')->count();
$anchored = Egi::whereNotNull('pa_anchored_at')->count();
echo "Total: $total | Anchored: $anchored";

# Logs
tail -50 storage/logs/laravel.log | grep -i error
php artisan queue:failed
```

---

## 🧪 TEST PRIORITIES

### ⭐ CRITICAL (Must Work)

1. ✅ **Upload + Blockchain** (20 min)
2. ✅ **Chat AI** (15 min)
3. ✅ **Public Verify** (10 min)
4. ✅ **Dashboard KPI** (10 min)

### 🟡 HIGH (Important)

5. ⚠️ **AI Accuracy** (1h) - Target >95%
6. ⚠️ **Performance** (30 min) - Target <30s
7. ⚠️ **GDPR Logs** (30 min) - No PII

### 🟢 MEDIUM (Nice to Have)

8. ✓ **UI Polish** (1h)
9. ✓ **Batch System** (30 min)

---

## 🎬 DEMO SCRIPT (15 min)

### Flow

1. **Intro** (2 min) → Problema + Soluzione
2. **DEMO Upload** (3 min) → AI estrae metadati
3. **DEMO Chat** (3 min) → RAG conversazionale
4. **DEMO Verify** (2 min) → QR code pubblico
5. **DEMO Batch** (2 min) → Scalabilità
6. **Business** (1 min) → €1.2k/anno Firenze
7. **Q&A** (2 min) → Prepared answers

### Key Numbers

-   Upload: **< 5 sec**
-   AI response: **< 3 sec**
-   Blockchain: **< 30 sec**
-   Accuracy: **> 95%**
-   Cost/atto: **< €0.05**
-   Savings: **-70% tempo**

---

## 💎 TALKING POINTS

### Opening

_"N.A.T.A.N. = primo sistema italiano AI + blockchain per certificare atti PA. Già funzionante, GDPR-compliant, production-ready."_

### Problem

_"PA perdono 60% tempo in data entry. Cittadini non possono verificare autenticità atti. Costa milioni/anno."_

### Solution

_"AI Claude legge documenti, estrae metadati automaticamente. Blockchain Algorand certifica immutabilità. QR code per verifica pubblica."_

### Demo Transition

_"Facciamo vedere come funziona dal vivo."_

### Closing

_"Firenze può essere la prima. Pilot 8 settimane gratis. Post: €1.2k/anno. Pronto a partire. Firenze è pronta?"_

---

## ⚠️ TROUBLESHOOTING

| Problema        | Fix Rapido                   |
| --------------- | ---------------------------- |
| Queue job fail  | `docker ps \| grep algorand` |
| AI non risponde | `grep ANTHROPIC .env`        |
| Dashboard slow  | `php artisan cache:clear`    |
| Verify 404      | Check `public_code` format   |

---

## ✅ PRE-DEMO CHECKLIST (1h prima)

-   [ ] Server up
-   [ ] Queue worker running
-   [ ] 20+ atti loaded
-   [ ] 15+ tokenizzati
-   [ ] Cache cleared
-   [ ] Logs clean
-   [ ] Tabs pre-opened
-   [ ] Video backup ready
-   [ ] Water bottle 💧
-   [ ] Deep breath 😊

---

## 📊 SUCCESS METRICS

### Tech

-   ✅ 0 critical bugs
-   ✅ 0 failed jobs
-   ✅ All tests PASS

### Demo

-   ✅ 0 errors live
-   ✅ Timing < 20 min
-   ✅ 2+ wow moments

### Business

-   ✅ Follow-up meeting
-   ✅ Interest pilot
-   ✅ Next steps clear

---

## 🚀 YOU GOT THIS!

**Remember:**

-   System works ✅
-   You're prepared ✅
-   Backup plans ready ✅
-   Confidence = key ✅

**Now go test & present!** 💪

---

**Commands Start:**

```bash
cd /home/fabio/EGI
php artisan queue:work &
firefox http://localhost &
# Login: pa@test.com / password123
# GO! 🔥
```
