# Guida Importazione Modulo "Esperienza 3D Olafur" nel Progetto Principale EGI

Questa guida descrive come integrare il codice presente nella cartella `Olafur/` dentro il progetto principale **EGI** (repository adiacente nella stessa root). L'obiettivo è trasformare l'esperienza 3D in un **package Laravel interno** modulare e versionabile.

---
## 1. Strategia di Integrazione
Approccio scelto: **Package interno PSR-4 + integrazione Vite**.

Vantaggi:
- Isolamento logico (facile aggiornare / rimuovere / riusare)
- Nessun inquinamento immediato di `app/` principale
- Possibilità futura di pubblicare come pacchetto privato

Struttura target (dentro root progetto EGI):
```
packages/
  experience3d/
    composer.json
    src/
      Providers/Experience3DServiceProvider.php
      Http/Controllers/ExperienceImagesController.php
    routes/
      api.php
    config/experience3d.php (facolt.)
    resources/js/experience/... (moduli copiati)
    public/experience/ (eventuali asset statici post-build)
    database/migrations/ (SOLO se tabella artworks manca)
    README.md
```

---
## 2. Preparazione Workspace
1. Posizionati nella root del progetto principale EGI (accanto alla cartella `Olafur/`).
2. Crea la cartella package:
   ```bash
   mkdir -p packages/experience3d/{src/Providers,src/Http/Controllers,routes,resources/js,public/experience,database/migrations,config}
   ```
3. Copia i moduli JS:
   ```bash
   cp -R ../Olafur/resources/js/experience packages/experience3d/resources/js/
   ```
4. (Opzionale) Copia alcune immagini di test se vuoi un fallback locale:
   ```bash
   mkdir -p public/experience-images
   cp ../Olafur/public/images/img_0*.jpg public/experience-images/ 2>/dev/null || true
   ```

---
## 3. composer.json del Package
`packages/experience3d/composer.json`:
```json
{
  "name": "egi/experience3d",
  "description": "Modulo Esperienza 3D Olafur",
  "type": "library",
  "autoload": { "psr-4": { "Experience3D\\": "src/" } },
  "extra": { "laravel": { "providers": ["Experience3D\\Providers\\Experience3DServiceProvider"] } }
}
```

Root `composer.json` del progetto principale – aggiungi repository path se non esiste già sezione simile:
```jsonc
  "repositories": [
    { "type": "path", "url": "packages/experience3d", "options": { "symlink": true } }
  ]
```
e aggiungi tra i requires (version on demand):
```jsonc
  "egi/experience3d": "*@dev"
```
Poi:
```bash
composer update egi/experience3d
```

---
## 4. Service Provider
`packages/experience3d/src/Providers/Experience3DServiceProvider.php`:
```php
<?php
namespace Experience3D\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class Experience3DServiceProvider extends ServiceProvider {
    public function register() {}
    public function boot() {
        // Rotte API del modulo
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        // Config (facoltativo)
        if (is_dir(__DIR__.'/../../config')) {
            $this->mergeConfigFrom(__DIR__.'/../../config/experience3d.php','experience3d');
        }
        // Migrations (SOLO se serve tabella artworks dedicata)
        if (is_dir(__DIR__.'/../../database/migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        }
        // Pubblica asset buildati (vite dist) opzionale
        $this->publishes([
          __DIR__.'/../../public/experience' => public_path('vendor/experience3d'),
        ], 'experience3d-assets');
    }
}
```

---
## 5. Rotte API
`packages/experience3d/routes/api.php`:
```php
<?php
use Illuminate\Support\Facades\Route;
use Experience3D\Http\Controllers\ExperienceImagesController;

Route::prefix('experience')
  ->group(function(){
     Route::get('images', [ExperienceImagesController::class,'index']);
  });
```

---
## 6. Controller Immagini
`packages/experience3d/src/Http/Controllers/ExperienceImagesController.php`:
```php
<?php
namespace Experience3D\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class ExperienceImagesController extends Controller {
    public function index(Request $req) {
        // Sorgente: se usi MediaLibrary sostituire con query su model Artwork / media collection
        $disk = config('experience3d.disk','public');
        $path = config('experience3d.path','experience-images');
        $files = collect(Storage::disk($disk)->files($path))
            ->filter(fn($f)=>preg_match('/\.(jpe?g|png|webp|avif)$/i',$f))
            ->values();
        $data = $files->map(function($f){
            $name = basename($f);
            return [
              'filename'=>$name,
              'url'=>Storage::url($f),
              'slug'=>strtolower(preg_replace('/[^a-z0-9]+/','-', pathinfo($name, PATHINFO_FILENAME)))
            ];
        });
        return response()->json(['count'=>$data->count(),'data'=>$data]);
    }
}
```

---
## 7. Config (facoltativo)
`packages/experience3d/config/experience3d.php`:
```php
<?php
return [
  'disk' => 'public',
  'path' => 'experience-images',
  'lod' => [
     'near_max' => 1.0,
     'mid_max'  => 0.7,
     'far_max'  => 0.25,
  ],
  'fog' => [ 'start_density'=>0.035, 'end_density'=>0.012 ],
];
```

---
## 8. Integrazione Vite
Nel `vite.config.js` del progetto principale:
```js
import path from 'path';
export default defineConfig({
  resolve:{
    alias:{
      '@experience3d': path.resolve(__dirname,'packages/experience3d/resources/js/experience')
    }
  },
  // ...resto config
});
```

Entry point (es. `resources/js/experience3d-entry.js`):
```js
import '@experience3d/index.js';
```

In una Blade (es. layout dedicato):
```blade
@vite('resources/js/experience3d-entry.js')
<div id="experience-root" class="w-full h-full fixed inset-0"></div>
```

Build:
```bash
npm run build   # oppure npm run dev
php artisan vendor:publish --tag=experience3d-assets --force  # se usi publishing
```

---
## 9. Migrazione Tabella `artworks`
Se il progetto principale ha già la tabella, salta. Se no, copia la migration da `Olafur/database/migrations/...` dentro `packages/experience3d/database/migrations/` e lanciala:
```bash
php artisan migrate
```
Integra poi un modello `App\Models\Artwork` o uno namespace separato (ad es. `Experience3D\Models\Artwork`).

> In futuro integra Spatie MediaLibrary per generare LOD automatici (conversioni: low, mid, full).

---
## 10. Adattamento Codice JS Post-Porting
Modifiche minime nel file `index.js` (ora referenziato via alias):
1. Cambia fetch immagini: `fetch('/api/experience/images')`
2. Opzionale: leggere config runtime via endpoint `/api/experience/config` (se aggiunto) o injection `<script>window.__expCfg = {...}</script>`
3. Prepara wrapper per handoff controlli utente → con `pointerlock` / `orbit-like` custom.

---
## 11. Commit Guidelines (TAG System)
Sequenza suggerita (un commit per step):
1. `[CHORE] Creazione package experience3d struttura base`
2. `[FEAT] Service provider + rotte API immagini esperienza`
3. `[FEAT] Alias Vite e entrypoint experience3d`
4. `[FEAT] Config experience3d + parametri fog/LOD`
5. `[DOC] Guida utilizzo modulo experience3d`
6. `[FEAT] Integrazione fetch immagini nel renderer 3D`

---
## 12. Test Rapido Post-Import
```bash
composer dump-autoload
php artisan route:list | grep experience
npm run dev
php artisan serve
# Apri / (o la pagina dedicata) e verifica console: nessun errore import alias
curl -s http://localhost:8000/api/experience/images | jq '.count'
```

Checklist visiva:
- Canvas WebGL visibile e planes generati
- Nessun 404 su `/api/experience/images`
- Nessun warning severe in console JS

---
## 13. Future Enhancements Post-Porting
| Ambito | Azione |
|--------|--------|
| Media | Integra MediaLibrary per LOD |
| Performance | Instancing / frustum culling manuale |
| UX | UI debug overlay (reveal, fog, counts) |
| Accessibilità | Modal info artwork al focus |
| Networking | Cache tag ETag immagini JSON |
| Rendering | Shader per desaturazione distanza |

---
## 14. Rollback Semplice
Per rimuovere il modulo: delete `packages/experience3d`, rimuovi voce da `composer.json`, `composer update --lock`, elimina alias Vite, ripulisci assets pubblicati.

---
## 15. Nota su Ambiente
Assicurarsi che la cartella sorgente immagini (`storage/app/public/experience-images` o altra) sia linkata con:
```bash
php artisan storage:link
```
e che i file siano effettivamente raggiungibili via URL.

---
## 16. Stato Attuale Moduli Copiati
- IntroDirector: stato reveal + handoff
- AtmosphereLayer: fog baseline (da rifinire con THREE.FogExp2)
- ImageFieldBuilder: distribuzione posizioni
- OpacityController: curve progressive (placeholder)
- CameraPathController: CatmullRom + easing
- ResourceLoader: caching texture base
- LODManager: stub (da completare con thresholds reali e swap LOD progressivo)

---
## 17. Azioni Immediate Suggerite Dopo Import
1. Verifica build Vite alias → risoluzione path
2. Sostituisci colori seed con texture reali (applica texture loader al near cluster)
3. Introduci parametro `revealProgress` esposto in `window.__expDebug`
4. Aggiungi pannello debug (tasti: `D` toggle) con sliders per fogDensity e counts
5. Implementa blocco input utente fino a stato `INTERACTIVE`

---
## 18. Contatti / Maintainer Interno
Mantieni questo package con commit granulari e rispetto del sistema TAG introdotto (19 ago 2025). Ogni modifica significativa ai parametri di reveal deve avere nota nel README del package.

---
_Fine documento._
