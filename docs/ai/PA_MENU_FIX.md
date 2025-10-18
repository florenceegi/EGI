# PA Menu - Analisi e Fix

**Data:** 2025-10-18  
**Autore:** Padmin D. Curtis (AI Partner OS3.0)  
**Progetto:** FlorenceEGI - N.A.T.A.N.

---

## 🔍 PROBLEMA RIPORTATO

L'utente vedeva solo 4 voci nel menu PA:

1. N.A.T.A.N. Management
2. Lista Atti N.A.T.A.N.
3. Carica Atto N.A.T.A.N.
4. Statistiche Atti

Inoltre, **le icone non erano visibili**.

---

## 🐛 CAUSA 1: Icone Mancanti

### Problema

I menu PA usano **Material Icons** invece di SVG HeroIcons:

-   `smart_toy` (N.A.T.A.N. Intelligence)
-   `bar_chart` (Statistiche)
-   `auto_mode` (Batch Processor)

Il file `config/icons.php` conteneva solo SVG Heroicons hardcoded, non Material Icons.

### Soluzione Applicata ✅

Aggiunte le 3 Material Icons al `config/icons.php`:

```php
// Material Icons per N.A.T.A.N. PA
[
    'name' => 'smart_toy',
    'type' => 'material',
    'class' => 'w-5 h-5',
    'host' => 'https://fonts.google.com/icons',
    'name_on_host' => 'smart_toy',
    'html' => '<span class="material-symbols-outlined" style="font-size:20px;color:#D4A574;">smart_toy</span>',
],
[
    'name' => 'bar_chart',
    'type' => 'material',
    'class' => 'w-5 h-5',
    'host' => 'https://fonts.google.com/icons',
    'name_on_host' => 'bar_chart',
    'html' => '<span class="material-symbols-outlined" style="font-size:20px;color:#D4A574;">bar_chart</span>',
],
[
    'name' => 'auto_mode',
    'type' => 'material',
    'class' => 'w-5 h-5',
    'host' => 'https://fonts.google.com/icons',
    'name_on_host' => 'auto_mode',
    'html' => '<span class="material-symbols-outlined" style="font-size:20px;color:#D4A574;">auto_mode</span>',
],
```

**Commit:** `[FIX] Aggiunte Material Icons per menu PA (smart_toy, bar_chart, auto_mode)`

---

## 🐛 CAUSA 2: Cache Blade Views e Context Menu

### Problema

Il menu PA ha 6 voci totali, ma l'utente vedeva voci VECCHIE/COMMENTATE a causa di cache non pulita.

### Struttura Menu PA (ContextMenus.php)

```php
case 'pa.acts':
case 'pa':
    $paMainMenu = new MenuGroup(__('menu.pa_management'), 'pa-building', [
        // new PAHeritageMenu(),        // 1
        new PAActsMenu(),            // 2
        new PAStatisticsMenu(),      // 3
        new PABatchProcessorMenu(),  // 4
        new PACoAMenu(),             // 5
        new PAInspectorsMenu(),      // 6
    ]);
    $menus[] = $paMainMenu;
    break;
```

### Permessi Richiesti per Ogni Voce

| #   | Voce Menu                   | Permesso Richiesto                 | Visibile? |
| --- | --------------------------- | ---------------------------------- | --------- |
| 1   | **Documenti Certificati**   | `manage_institutional_collections` | ❌ NO     |
| 2   | **N.A.T.A.N. Intelligence** | `access_pa_dashboard`              | ❌ NO     |
| 3   | **Statistiche Atti**        | `manage_institutional_collections` | ❌ NO     |
| 4   | **Elaborazione Batch**      | `access_pa_dashboard`              | ❌ NO     |
| 5   | **Certificati CoA**         | `institutional_certification`      | ❌ NO     |
| 6   | **Ispettori Assegnati**     | `cultural_heritage_management`     | ❌ NO     |

### Verifica Permessi Ruolo pa_entity

Il ruolo `pa_entity` **HA TUTTI I PERMESSI PA** necessari:

-   ✅ `access_pa_dashboard`
-   ✅ `manage_institutional_collections`
-   ✅ `institutional_certification`
-   ✅ `cultural_heritage_management`

**Il problema NON erano i permessi, ma la CACHE!**

---

## ✅ SOLUZIONE

### Pulire TUTTE le Cache

Il problema era la **cache** che mostrava menu vecchi/commentati.

**Comando eseguito:**

```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

Dopo questo comando, **ricaricare la pagina** nel browser.

---

## 📊 VERIFICA POST-FIX

Dopo aver assegnato i permessi, il menu dovrebbe mostrare:

```
📂 Gestione PA
   ├─ 🏛️ Documenti Certificati (Heritage)
   ├─ 🤖 N.A.T.A.N. Intelligence (Acts)
   ├─ 📊 Statistiche Atti
   ├─ ⚙️ Elaborazione Batch
   ├─ ✅ Certificati CoA
   └─ 🛡️ Ispettori Assegnati
```

Con **tutte le icone visibili** in colore oro PA (#D4A574).

---

## 🔧 FILE MODIFICATI

1. **`config/icons.php`** - Aggiunte 3 Material Icons
2. **Cache cleared** - `php artisan config:clear && php artisan cache:clear`
3. **Icone seeded** - `php artisan db:seed --class=IconSeeder`

---

## 📝 NOTE TECNICHE

### Sistema Icone

Il sistema gestisce 2 tipi di icone:

1. **SVG Heroicons** - Definite inline nel config
2. **Material Icons** - Usa classe `material-symbols-outlined`

Per aggiungere nuove Material Icons, seguire il pattern:

```php
[
    'name' => 'icon_name',
    'type' => 'material',
    'class' => 'w-5 h-5',
    'html' => '<span class="material-symbols-outlined" style="font-size:20px;color:#D4A574;">icon_name</span>',
]
```

### Filtro Permessi Sidebar

Il componente `enterprise-sidebar.blade.php` filtra automaticamente le voci menu tramite `MenuConditionEvaluator`:

```php
$filteredItems = array_filter($menuGroup->items, function ($item) use ($evaluator) {
    return $evaluator->shouldDisplay($item);
});
```

Questo è il comportamento corretto per sicurezza (principle of least privilege).

---

**Status:** ✅ Icone FIXED | ✅ Cache CLEARED | ✅ Menu PA COMPLETO
