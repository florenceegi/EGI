{{--
    /partials/uploading_form_content.blade.php
    Questo partial contiene SOLO il contenuto centrale della form di upload EGI.
    È pensato per essere incluso all'interno di una modale o di un altro contenitore
    nella Home page o in un'altra vista.

    NON include:
    - HTML/HEAD/BODY tags
    - Layout a colonne
    - Animazione Matrix/sfondi animati (gestiti dalla vista contenitore)
    - Script di setup globale (config loading, DOMContentLoaded listeners, Vite)
    - Componenti Livewire esterni (Navbar, Sidebar)

    Richiede che gli asset (CSS, JS di UUM, Livewire JS, global config script, Alpine.js)
    siano caricati nella pagina che lo include.
--}}

{{--
    Schema.org Markup (JSON-LD)
    Questo partial rappresenta una sezione di un form all'interno di una pagina più ampia.
    Il markup Schema.org per l'intera pagina (es. tipo WebPage o CollectionPage)
    è stato aggiunto nel partial genitore (/partials/uploading_form_content.blade.php).
    Aggiungere un blocco Schema.org qui specificamente per questa sezione del form
    non è semanticamente appropriato secondo le linee guida Schema.org per i dati strutturati
    relativi al contenuto della pagina (articoli, prodotti, eventi, ecc.).
    Pertanto, non aggiungiamo markup Schema.org in questo partial,
    basandoci sul markup già aggiunto al livello superiore e sul fatto che Schema.org
    descrive le entità del contenuto principale, non i controlli interni di un form.
--}}

{{-- Il div centrale con tutto il contenuto del form --}}
{{-- Questo div agisce come contenitore semantico per i metadati rapidi. --}}
{{-- Aggiunti role="group" e aria-label per l'accessibilità (ARIA) per definire questa sezione come un gruppo di campi correlati. --}}
<div class="p-3 mb-4 border rounded-lg bg-gray-800/50 border-purple-500/30" role="group" aria-label="{{ trans('uploadmanager::uploadmanager.quick_egi_metadata') }}">
    {{-- Header collassabile per mobile - titolo come bottone --}}
    <div class="mb-3">
        {{-- Titolo cliccabile su mobile, statico su desktop --}}
        <button type="button" 
                class="flex items-center justify-between w-full text-base font-semibold text-left text-white md:pointer-events-none"
                id="metadata-toggle"
                aria-expanded="false"
                aria-controls="metadata-content"
                aria-label="Mostra/Nascondi metadata EGI rapidi">
            <span>{{ trans('uploadmanager::uploadmanager.quick_egi_metadata') }}</span>
            <svg class="w-4 h-4 transition-transform duration-300 md:hidden" id="metadata-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
    </div>
    
    {{-- Contenuto metadata - collassabile su mobile --}}
    <div class="hidden md:block" id="metadata-content">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">

            {{-- Riga 1: Titolo e Floor Price --}}
            <div>
                {{-- Label associata all'input "egi-title" tramite l'attributo 'for'. Questa è la modalità standard e preferita per l'accessibilità. --}}
                <label for="egi-title" class="block text-xs font-medium text-gray-300 mb-0.5">{{ trans('uploadmanager::uploadmanager.egi_title') }}</label>
                {{-- Input per il titolo. Aggiunto aria-label come ridondanza per tecnologie assistive, usando lo stesso testo della label visibile. --}}
                <input type="text" id="egi-title" name="egi-title" placeholder="{{ trans('uploadmanager::uploadmanager.egi_title_placeholder') }}"
                    class="w-full px-2 py-1.5 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500 text-sm"
                    aria-label="{{ trans('uploadmanager::uploadmanager.egi_title') }}">
                {{-- Testo informativo. Non direttamente collegato all'input con ARIA in questo contesto, per non modificare la struttura. --}}
                <p class="text-[10px] text-gray-400 mt-0.5">{{ trans('uploadmanager::uploadmanager.egi_title_info') }}</p>
            </div>

            <div>
                {{-- Label associata all'input "egi-floor-price" tramite l'attributo 'for'. --}}
                <label for="egi-floor-price" class="block text-xs font-medium text-gray-300 mb-0.5">{{ trans('uploadmanager::uploadmanager.floor_price') }}</label>
                {{-- Input per il floor price. Aggiunto aria-label usando il testo della label. --}}
                {{-- Gli attributi di validazione HTML5 (step, min) sono già presenti e utili per l'accessibilità. --}}
                <input type="number" step="0.01" min="0" id="egi-floor-price" name="egi-floor-price" placeholder="{{ trans('uploadmanager::uploadmanager.floor_price_placeholder') }}"
                    class="w-full px-2 py-1.5 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500 text-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                    aria-label="{{ trans('uploadmanager::uploadmanager.floor_price') }}">
                {{-- Testo informativo. --}}
                <p class="text-[10px] text-gray-400 mt-0.5">{{ trans('uploadmanager::uploadmanager.floor_price_info') }}</p>
            </div>

            {{-- Riga 2: Data e Posizione --}}
            <div>
                {{-- Label associata all'input "egi-date" tramite l'attributo 'for'. --}}
                <label for="egi-date" class="block text-xs font-medium text-gray-300 mb-0.5">{{ trans('uploadmanager::uploadmanager.creation_date') }}</label>
                {{-- Input per la data. Aggiunto aria-label usando il testo della label. --}}
                <input type="date" id="egi-date" name="egi-date"
                    class="w-full px-2 py-1.5 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500 text-sm"
                    style="color-scheme: dark;"
                    aria-label="{{ trans('uploadmanager::uploadmanager.creation_date') }}">
                {{-- Testo informativo. --}}
                <p class="text-[10px] text-gray-400 mt-0.5">{{ trans('uploadmanager::uploadmanager.creation_date_info') }}</p>
            </div>

           {{-- Riga 3: Descrizione (occupa 2 colonne) --}}
            <div class="md:col-span-2">
                {{-- Label associata alla textarea "egi-description" tramite l'attributo 'for'. --}}
                <label for="egi-description" class="block text-xs font-medium text-gray-300 mb-0.5">{{ trans('uploadmanager::uploadmanager.egi_description') }}</label>
                {{-- Textarea per la descrizione. Aggiunto aria-label usando il testo della label. --}}
                <textarea id="egi-description" name="egi-description" rows="2" placeholder="{{ trans('uploadmanager::uploadmanager.egi_description_placeholder') }}"
                        class="w-full px-2 py-1.5 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500 text-sm"
                        aria-label="{{ trans('uploadmanager::uploadmanager.egi_description') }}"></textarea>
                {{-- Testo informativo. --}}
                <p class="text-[10px] text-gray-400 mt-0.5">{{ trans('uploadmanager::uploadmanager.metadata_notice') }}</p>
            </div>

            {{-- Riga 4: EGI Type Selector (occupa 2 colonne) --}}
            <div class="md:col-span-2 mt-3">
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    {{ trans('uploadmanager::uploadmanager.egi_type_label') }}
                    <span class="text-xs text-gray-400 ml-1">({{ trans('uploadmanager::uploadmanager.egi_type_help') }})</span>
                </label>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {{-- ASA Classic Option --}}
                    <label class="relative flex items-start p-3 border-2 border-gray-600 rounded-lg cursor-pointer transition-all duration-200 hover:border-blue-500 hover:bg-gray-700/30 group">
                        <input type="radio" 
                               name="egi-type" 
                               id="egi-type-asa"
                               value="ASA" 
                               checked
                               class="mt-1 h-4 w-4 text-blue-500 focus:ring-blue-500 focus:ring-offset-gray-900"
                               aria-label="{{ trans('uploadmanager::uploadmanager.egi_type_asa') }}">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-white">
                                    🛡️ {{ trans('uploadmanager::uploadmanager.egi_type_asa') }}
                                </span>
                                <span class="px-2 py-0.5 text-[10px] font-medium bg-blue-600 text-white rounded-full">
                                    {{ trans('uploadmanager::uploadmanager.free') }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ trans('uploadmanager::uploadmanager.egi_type_asa_desc') }}
                            </p>
                        </div>
                    </label>

                    {{-- SmartContract Living Option --}}
                    <label class="relative flex items-start p-3 border-2 border-gray-600 rounded-lg cursor-pointer transition-all duration-200 hover:border-purple-500 hover:bg-gray-700/30 group">
                        <input type="radio" 
                               name="egi-type" 
                               id="egi-type-sc"
                               value="SmartContract"
                               class="mt-1 h-4 w-4 text-purple-500 focus:ring-purple-500 focus:ring-offset-gray-900"
                               aria-label="{{ trans('uploadmanager::uploadmanager.egi_type_smart_contract') }}">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-white">
                                    🧠 {{ trans('uploadmanager::uploadmanager.egi_type_smart_contract') }}
                                </span>
                                <span class="px-2 py-0.5 text-[10px] font-medium bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-full">
                                    PREMIUM
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ trans('uploadmanager::uploadmanager.egi_type_smart_contract_desc') }}
                            </p>
                        </div>
                    </label>
                </div>
                <p class="text-[10px] text-gray-400 mt-1.5">
                    {{ trans('uploadmanager::uploadmanager.egi_type_notice') }}
                </p>
            </div>
        </div>
        
        <script>
        // Listen to SmartContract selection → Open payment modal
        document.addEventListener('DOMContentLoaded', function() {
            const smartContractRadio = document.getElementById('egi-type-sc');
            
            if (smartContractRadio) {
                smartContractRadio.addEventListener('change', function() {
                    if (this.checked) {
                        // Open Feature Purchase Modal
                        if (typeof openFeaturePurchaseModal === 'function') {
                            openFeaturePurchaseModal('egi_living_subscription');
                        } else {
                            console.error('openFeaturePurchaseModal function not found');
                        }
                    }
                });
            }
        });
        </script>
        <div class="flex items-center justify-end gap-2 my-4">
            {{-- Switch publish. Ha già role="switch" e l'attributo 'checked' per lo stato iniziale. --}}
            {{-- Aggiunto aria-checked per comunicare lo stato iniziale alle tecnologie assistive. --}}
            {{-- Aggiunto aria-label usando il testo del 'title' per una descrizione concisa del controllo. --}}
            <input
                class="me-1 h-3 w-6 appearance-none rounded-full bg-gray-600 before:pointer-events-none before:absolute before:h-3 before:w-3 before:rounded-full before:bg-transparent after:absolute after:z-[2] after:-mt-0.25 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow-sm after:transition-all checked:bg-green-500 checked:after:ms-3 checked:after:bg-green-300 checked:after:shadow-sm hover:cursor-pointer focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                type="checkbox"
                role="switch"
                id="egi-publish"
                name="egi-publish"
                checked
                title="{{ trans('uploadmanager::uploadmanager.toggle_publish_status') }}"
                aria-checked="true" {{-- Lo stato iniziale è checked="true" --}}
                aria-label="{{ trans('uploadmanager::uploadmanager.toggle_publish_status') }}" {{-- Usa il testo del title per l'etichetta accessibile --}}
            />
            {{-- Etichetta per lo switch publish. Associata all'input tramite l'attributo 'for'. --}}
            <label
                class="text-xs font-medium text-green-300 hover:pointer-events-none"
                id="egi-publish_label" {{-- Mantenuto l'id per consistenza, anche se non strettamente necessario per l'associazione label/input standard --}}
                for="egi-publish"
            >{{ trans('uploadmanager::uploadmanager.publish_egi') }}</label>
        </div>
    </div>
</div>

{{-- NOTA: Questo partial non contiene script.
     Tutto il codice JS che interagisce con questi elementi (id=...)
     deve essere caricato e inizializzato nella pagina che include questo partial.
     Il JS dovrà anche aggiornare l'attributo ARIA dinamico aria-checked per lo switch
     quando il suo stato cambia. --}}

{{-- JavaScript per gestire il toggle dei metadata su mobile --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('metadata-toggle');
    const content = document.getElementById('metadata-content');
    const chevron = document.getElementById('metadata-chevron');
    
    if (toggleButton && content && chevron) {
        toggleButton.addEventListener('click', function() {
            const isExpanded = toggleButton.getAttribute('aria-expanded') === 'true';
            
            // Toggle visibility
            if (isExpanded) {
                content.classList.add('hidden');
                toggleButton.setAttribute('aria-expanded', 'false');
                chevron.style.transform = 'rotate(0deg)';
            } else {
                content.classList.remove('hidden');
                toggleButton.setAttribute('aria-expanded', 'true');
                chevron.style.transform = 'rotate(180deg)';
            }
        });
        
        console.log('✅ Metadata collapsible toggle initialized');
    }
});
</script>
