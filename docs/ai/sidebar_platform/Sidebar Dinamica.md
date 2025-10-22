# Documentazione Sistema Sidebar Dinamica e Contestuale di FlorenceEGI

**(Versione 2.0 - Ottobre 2025)**

## Abstract

Il sistema di sidebar di FlorenceEGI è progettato per fornire una navigazione utente dinamica, contestuale e basata sui permessi. Utilizza un **Blade component (`enterprise-sidebar`)** che integra la logica di menu contestuali direttamente nel rendering. La sidebar si adatta automaticamente al contesto dell'applicazione (es. "dashboard", "pa.dashboard", "superadmin.dashboard") mostrando solo i gruppi di menu e gli item pertinenti, rispettando i permessi dell'utente autenticato. Le icone sono gestite centralmente tramite `IconRepository` con supporto per SVG colorati e caching.

## 1. Flusso Generale di Funzionamento

1.  **Richiesta Pagina:** L'utente naviga verso una pagina che include la sidebar.
2.  **Inclusione Component:** La vista Blade include il component `<x-enterprise-sidebar>` passando i props `logo`, `badge` e `theme`.
3.  **Determinazione Contesto (Automatica):** Il component `enterprise-sidebar.blade.php` legge automaticamente la route corrente (`Route::currentRouteName()`) e determina il contesto estraendo i primi due segmenti (es. `pa.dashboard` → context `'pa.dashboard'`).
4.  **Recupero Struttura Menu:** Il component invoca direttamente `App\Services\Menu\ContextMenus::getMenusForContext($context)` per ottenere un array di oggetti `App\Services\Menu\MenuGroup`.
    -   Ogni `MenuGroup` contiene un nome (tradotto), una chiave per l'icona, e un array di oggetti `App\Services\Menu\MenuItem`.
    -   Ogni `MenuItem` contiene una chiave di traduzione per il nome, una rotta Laravel, una chiave per l'icona, un permesso opzionale.
5.  **Filtraggio per Permessi:** Il component itera sulla struttura menu e utilizza `MenuConditionEvaluator` per verificare i permessi di ogni `MenuItem` prima di includerlo nel rendering.
6.  **Arricchimento Icone (Inline):** Durante il rendering, per ogni chiave icona:
    -   Chiama `App\Repositories\IconRepository::getDefaultIcon($iconKey)` per recuperare l'HTML SVG completo e processato dell'icona.
    -   Le definizioni SVG (con colori intrinseci) sono memorizzate nel database (popolate da `config/icons.php` via `IconSeeder`).
7.  **Rendering Vista:** Il component renderizza l'HTML della sidebar:
    -   Itera sui menu filtrati per permessi.
    -   Renderizza le icone usando `{!! $iconHtml !!}`.
    -   Gestisce la visualizzazione di gruppi collassabili (`<details>`) con evidenziazione dello stato attivo.
    -   Applica temi dinamici tramite `$themeColors` mapping (pa, inspector, company, superadmin).

## 2. Componenti Chiave del Sistema

### 2.1. PHP - Logica e Definizione dei Menu

-   **`app/Services/Menu/MenuItem.php`**:

    -   Classe base per ogni singola voce di menu.
    -   Proprietà: `$translationKey`, `$name` (tradotto), `$route`, `$icon` (chiave), `$permission`, `$isModalAction`, `$modalAction`.
    -   Metodi: `getHref()`, `getHtmlAttributes()`.

-   **`app/Services/Menu/MenuGroup.php`**:

    -   Raggruppa `MenuItem`s.
    -   Proprietà: `$name` (tradotto), `$icon` (chiave), `$items` (array di `MenuItem`).

-   **`app/Services/Menu/Items/*.php`** (es. `SuperadminDashboardMenu.php`, `PAActsMenu.php`):

    -   Classi concrete che estendono `MenuItem`.
    -   Definiscono i parametri specifici (chiave traduzione, rotta, icona, permesso) per ogni voce di menu.

-   **`app/Services/Menu/ContextMenus.php`**:

    -   Classe "factory" principale.
    -   Metodo statico `getMenusForContext(string $context): array` che costruisce e restituisce l'array di `MenuGroup` appropriato per il contesto fornito.
    -   Gestisce tutti i context: `dashboard`, `pa.*`, `superadmin.*`, `statistics`, etc.

-   **`app/Services/Menu/MenuConditionEvaluator.php`**:

    -   Contiene la logica `shouldDisplay(MenuItem $menuItem): bool` per verificare se un `MenuItem` dovrebbe essere visualizzato in base ai permessi dell'utente.
    -   Utilizza `Gate::allows()` o logica personalizzata.

-   **`app/Repositories/IconRepository.php`**:

    -   Gestisce il recupero, il processamento (sostituzione `%class%`) e il caching delle icone SVG.
    -   Interagisce con il modello `App\Models\Icon` per recuperare le definizioni SVG dal database.
    -   Metodi principali: `getIcon($name, $style, $class)`, `getDefaultIcon($name)`.

-   **`app/Models/Icon.php`**:

    -   Modello Eloquent per la tabella `icons` del database.

-   **`config/icons.php`**:

    -   File di configurazione centrale che definisce tutte le icone dell'applicazione.
    -   Struttura: `['styles' => ['elegant' => [...]]]`.
    -   Ogni icona ha: `name`, `type`, `class` (default), `html` (SVG con placeholder `%class%` e colori intrinseci).

-   **`database/seeders/IconSeeder.php`**:
    -   Legge `config('icons.styles.elegant')` e popola/aggiorna la tabella `icons` nel database.

### 2.2. Blade Component - Vista di Rendering

-   **`resources/views/components/enterprise-sidebar.blade.php`**:
    -   **Blade component** (NON Livewire) responsabile del rendering della sidebar.
    -   **Props:**
        -   `logo` (string): Testo logo (default: 'FlorenceEGI')
        -   `badge` (string|null): Badge sotto il logo (es. 'Ente PA', 'SuperAdmin')
        -   `theme` (string): Identificatore tema (pa|inspector|company|superadmin|dashboard)
    -   **Logica PHP interna (`@php`):**
        -   Determina il `$context` dalla route corrente.
        -   Invoca `ContextMenus::getMenusForContext($context)`.
        -   Filtra i menu per permessi usando `MenuConditionEvaluator`.
        -   Arricchisce le icone tramite `IconRepository`.
        -   Determina lo stato attivo confrontando `$currentRouteName` con le route dei menu.
        -   Applica il tema tramite `$themeColors` mapping.
    -   **Rendering HTML:**
        -   Header con logo e badge/context title.
        -   Menu navigation con gruppi collassabili (`<details>`).
        -   Logout button.
        -   Footer.
    -   **Styling:** Tailwind CSS + DaisyUI con gradienti per i temi.

## 3. Gestione dei Temi

Il component `enterprise-sidebar` supporta temi visuali dinamici tramite la prop `theme`:

```php
$themeColors = [
    'pa' => 'bg-gradient-to-b from-[#1B365D] to-[#0F2342]', // Blu Algoritmo
    'inspector' => 'bg-gradient-to-b from-[#2D5016] to-[#1F3810]', // Verde Rinascita
    'company' => 'bg-gradient-to-b from-[#8E44AD] to-[#6C3483]', // Viola Innovazione
    'superadmin' => 'bg-gradient-to-b from-[#0B1F3A] to-[#123C7A]', // Blu Istituzionale (WCAG AA)
    'dashboard' => 'bg-neutral', // Default DaisyUI
];
```

**Esempio utilizzo:**

```blade
<x-enterprise-sidebar logo="FlorenceEGI" badge="SuperAdmin" theme="superadmin">
    <div class="p-6">
        <!-- Contenuto pagina -->
    </div>
</x-enterprise-sidebar>
```

## 4. Gestione delle Icone

-   Le icone sono definite come stringhe SVG nel file `config/icons.php` dentro `['styles']['elegant']`.
-   **Per icone colorate:** Gli attributi `fill` o `stroke` all'interno degli elementi SVG (`<path>`, `<circle>`, etc.) devono essere impostati con valori esadecimali specifici (es. `fill="#D4AF37"` per oro SuperAdmin).
-   La classe di default per le icone in `config/icons.php` dovrebbe specificare solo le dimensioni (es. `w-5 h-5`) senza opacità.
-   L'opacità per stati (attivo/inattivo/hover) viene gestita dalle classi CSS nel component sidebar.
-   **Workflow:**
    1. Aggiungere icona in `config/icons.php` dentro `['styles']['elegant']`.
    2. Eseguire `php artisan db:seed --class=IconSeeder --force`.
    3. Pulire cache: `php artisan config:clear && php artisan view:clear`.

## 5. Aggiungere/Modificare Voci di Menu

### 5.1. Aggiungere un Nuovo Item di Menu Semplice:

1.  **Creare la Classe Item:** In `app/Services/Menu/Items/`, creare una nuova classe PHP che estende `App\Services\Menu\MenuItem`.

    ```php
    // Esempio: app/Services/Menu/Items/MyNewFeatureMenu.php
    namespace App\Services\Menu\Items;
    use App\Services\Menu\MenuItem;

    class MyNewFeatureMenu extends MenuItem
    {
        public function __construct()
        {
            parent::__construct(
                translationKey: 'menu.my_new_feature', // Chiave per traduzione
                route: 'route.name.for.feature',       // Nome rotta Laravel
                icon: 'icon_key_for_feature',          // Chiave icona
                permission: 'permission_to_view'       // Permesso (null per pubblico)
            );
        }
    }
    ```

2.  **Definire Traduzione:** Aggiungere `'my_new_feature' => 'Nuova Funzionalità'` in `resources/lang/it/menu.php`.

3.  **Definire Icona (se nuova):** Aggiungere la definizione in `config/icons.php` dentro `['styles']['elegant']`:

    ```php
    [
        'name' => 'icon_key_for_feature',
        'type' => 'heroicon',
        'class' => 'w-5 h-5',
        'host' => '',
        'name_on_host' => '',
        'html' => '<svg fill="#60A5FA" viewBox="0 0 24 24" class="%class%">
            <path d="..." />
        </svg>',
    ],
    ```

    Poi eseguire `IconSeeder`.

4.  **Aggiungere a `ContextMenus.php`:**
    ```php
    // In app/Services/Menu/ContextMenus.php
    use App\Services\Menu\Items\MyNewFeatureMenu;
    // ...
    case 'dashboard':
        $someGroup = new MenuGroup(__('menu.some_group'), 'group_icon', [
            new ExistingItemMenu(),
            new MyNewFeatureMenu(), // ← Aggiunto
        ]);
        $menus[] = $someGroup;
        break;
    ```

### 5.2. Aggiungere un Nuovo Context:

Per creare un nuovo contesto (es. `inspector.dashboard`):

1.  **Definire il case in `ContextMenus.php`:**

    ```php
    case 'inspector.dashboard':
    case 'inspector.reports':
        $inspectorMenu = new MenuGroup(__('menu.inspector_main'), 'inspector-icon', [
            new InspectorDashboardMenu(),
            new InspectorReportsMenu(),
        ]);
        $menus[] = $inspectorMenu;
        break;
    ```

2.  **Creare le route con naming convention:** `inspector.dashboard`, `inspector.reports`, etc.

3.  **Usare il component con tema appropriato:**
    ```blade
    <x-enterprise-sidebar logo="FlorenceEGI" badge="Ispettore" theme="inspector">
        <!-- Contenuto -->
    </x-enterprise-sidebar>
    ```

## 6. Differenze Rispetto a Versione Precedente (Livewire)

| Aspetto               | Versione Livewire (v1.0)      | Versione Blade Component (v2.0)    |
| --------------------- | ----------------------------- | ---------------------------------- |
| **Tecnologia**        | Livewire component            | Blade component puro               |
| **Context detection** | Passato dal controller        | Automatico da route corrente       |
| **Filtro permessi**   | Nel controller                | Nel component (@php)               |
| **Icone**             | Pre-arricchite nel controller | Arricchite inline nel component    |
| **Reattività**        | Wire:navigate / live updates  | Navigazione standard               |
| **Stato attivo**      | Gestito via Livewire state    | Gestito via PHP comparison route   |
| **Props**             | Componente dinamico           | Props statici (logo, badge, theme) |
| **Temi**              | Non implementati              | Mapping dinamico per context       |

## 7. Test da Integrare

### 7.1. Test Unitari

-   **`MenuItemTest.php`**: Testare costruttore, proprietà, `getHref()`, `getHtmlAttributes()`.
-   **`MenuGroupTest.php`**: Testare costruttore, `addItem()`.
-   **`ContextMenusTest.php`**: Per ogni context, verificare che restituisca `MenuGroup[]` corretto.
-   **`MenuConditionEvaluatorTest.php`**: Testare `shouldDisplay()` con vari permessi mockati.
-   **`IconRepositoryTest.php`**: Testare recupero, caching, sostituzione `%class%`, fallback.

### 7.2. Test di Integrazione / Funzionali

-   **Sidebar Rendering Test:**
    -   Creare utenti con permessi diversi.
    -   Verificare che i menu visibili/nascosti siano corretti per ogni utente/context.
    -   Verificare presenza icone SVG e link corretti.
-   **Test Stato Attivo:**
    -   Navigare verso una route specifica.
    -   Verificare che l'item corrispondente abbia classi CSS `bg-primary` o equivalenti.
-   **Test Temi:**
    -   Verificare che ogni tema applichi i gradienti corretti nella sidebar.

### 7.3. Test di Visual Regression

-   Utilizzare Percy o BackstopJS per catturare screenshot della sidebar in vari stati/temi.
-   Confrontare con baseline per rilevare regressioni visive.

## 8. Considerazioni Future e Miglioramenti

-   **Caching Menu Structure:** Se `ContextMenus::getMenusForContext()` diventa costoso, cachare l'output (invalidare su cambio permessi).
-   **Permessi a Livello di Gruppo:** Formalizzare attributo `$permission` in `MenuGroup`.
-   **Multi-Language Icons:** Supportare icone diverse per lingua (se necessario).
-   **Accessibility (A11y):** Verificare ARIA labels, keyboard navigation, screen reader compatibility.
-   **Mobile Optimization:** Implementare drawer/hamburger menu per mobile (attualmente desktop-first).

---

**Nota per sviluppatori futuri:**
Questa sidebar è progettata per scalare facilmente. Per aggiungere nuovi contesti, temi o menu, seguire i pattern stabiliti in `ContextMenus.php` e `enterprise-sidebar.blade.php`. Consultare sempre il doc OS3 per le convenzioni Oracode.
