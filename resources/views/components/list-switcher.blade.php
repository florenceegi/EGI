@props(['defaultTab' => 'collections', 'collections' => null])

<div class="mb-4 mt-4 w-full" id="main-list-switcher">

    <!-- Tab Navigation -->
    <div class="mb-6 flex justify-center space-x-4 sm:space-x-8">
        <!-- Collezioni -->
        <button type="button"
            class="tab-button rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 hover:bg-gray-700 sm:px-4 sm:text-base"
            data-tab="collections" title="{{ __('common.tab_collections') }}">
            {{ __('common.tab_collections') }}
        </button>

        <!-- Creatori -->
        <button type="button"
            class="tab-button rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 hover:bg-gray-700 sm:px-4 sm:text-base"
            data-tab="creators" title="{{ __('common.tab_creators') }}">
            {{ __('common.tab_creators') }}
        </button>

        <!-- Collezionisti -->
        <button type="button"
            class="tab-button rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 hover:bg-gray-700 sm:px-4 sm:text-base"
            data-tab="collectors" title="{{ __('common.tab_collectors') }}">
            {{ __('common.tab_collectors') }}
        </button>
    </div>


    <!-- Tab Content -->
    <div class="tab-content min-h-[400px] rounded-lg bg-gray-800 p-4">
        <!-- Collections Tab -->
        <div id="main-content-collections" class="tab-content-panel">
            <x-collection-list :collections="$collections" />
        </div>

        <!-- Creators Tab -->
        <div id="main-content-creators" class="tab-content-panel hidden">
            <x-user-list userType="creator" />
        </div>

        <!-- Collectors Tab -->
        <div id="main-content-collectors" class="tab-content-panel hidden">
            <x-user-list userType="collector" />
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const switcher = document.getElementById('main-list-switcher');
        if (!switcher) {
            console.error('List switcher non trovato!');
            return;
        }

        const tabButtons = switcher.querySelectorAll('.tab-button');
        const tabContents = switcher.querySelectorAll('.tab-content-panel');
        const defaultTab = '{{ $defaultTab }}';

        console.log('List Switcher inizializzato:', {
            switcher: switcher,
            buttons: tabButtons.length,
            contents: tabContents.length,
            defaultTab: defaultTab
        });

        // Funzione per attivare un tab
        function activateTab(targetTab) {
            console.log('🔥 Attivando tab:', targetTab);

            // Nasconde tutti i contenuti
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            // Mostra il contenuto del tab selezionato
            const targetContent = document.getElementById(`main-content-${targetTab}`);
            console.log('🎯 Elemento target trovato:', targetContent);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                console.log('✅ Mostrato contenuto per:', targetTab);
            } else {
                console.error('❌ Elemento target NON trovato per:', targetTab);
            }

            // Aggiorna l'opacità dei bottoni per indicare quello attivo
            tabButtons.forEach(button => {
                const tabName = button.getAttribute('data-tab');
                if (tabName === targetTab) {
                    button.classList.add('active');
                    console.log('🔵 Attivato bottone:', tabName);
                } else {
                    button.classList.remove('active');
                }
            });
        }

        // Event listeners per i bottoni
        tabButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const tabName = this.getAttribute('data-tab');
                console.log('Click su tab:', tabName);
                activateTab(tabName);
            });

            // Debug hover
            button.addEventListener('mouseenter', function() {
                console.log('🐭 Mouse entra su:', this.getAttribute('data-tab'));
            });

            button.addEventListener('mouseleave', function() {
                console.log('🐭 Mouse esce da:', this.getAttribute('data-tab'));
            });
        });

        // Inizializza il tab di default
        console.log('Inizializzando tab di default:', defaultTab);
        activateTab(defaultTab);
    });
</script>

<style>
    .tab-content-panel {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .tab-button {
        color: rgba(255, 255, 255, 0.6);
        border: 1px solid transparent;
        transition: all 0.2s ease-in-out;
    }

    .tab-button:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    }

    .tab-button:hover {
        color: rgba(255, 255, 255, 0.9);
        background-color: rgba(55, 65, 81, 0.5);
    }

    .tab-button.active,
    .tab-button[style*="opacity: 1"] {
        color: #fff;
        background-color: rgba(59, 130, 246, 0.3);
        border-color: rgba(59, 130, 246, 0.5);
    }

    /* Forza visibilità del contenuto */
    .tab-content {
        display: block !important;
        visibility: visible !important;
        height: auto !important;
        overflow: visible !important;
    }

    .tab-content-panel {
        display: block;
    }

    .tab-content-panel.hidden {
        display: none !important;
    }
</style>
