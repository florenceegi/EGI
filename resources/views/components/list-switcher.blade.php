@props(['defaultTab' => 'collections', 'collections' => null])

<div class="w-full mt-4 mb-4" id="main-list-switcher">

    <!-- Tab Navigation -->
    <div class="flex justify-center mb-6 space-x-8">
        <!-- Collezioni -->
        <button type="button"
                class="text-3xl transition-all duration-200 tab-button hover:scale-110"
                data-tab="collections"
                title="Collezioni">
            🖼️
        </button>

        <!-- Creatori -->
        <button type="button"
                class="text-3xl transition-all duration-200 tab-button hover:scale-110"
                data-tab="creators"
                title="Creatori">
            🎨
        </button>

        <!-- Collezionisti -->
        <button type="button"
                class="text-3xl transition-all duration-200 tab-button hover:scale-110"
                data-tab="collectors"
                title="Collezionisti">
            📚
        </button>
    </div>


    <!-- Tab Content -->
    <div class="tab-content min-h-[400px] bg-gray-800 rounded-lg p-4">
        <!-- Collections Tab -->
        <div id="main-content-collections" class="tab-content-panel">
            <x-collection-list :collections="$collections" />
        </div>

        <!-- Creators Tab -->
        <div id="main-content-creators" class="hidden tab-content-panel">
            <x-user-list userType="creator" />
        </div>

        <!-- Collectors Tab -->
        <div id="main-content-collectors" class="hidden tab-content-panel">
            <x-user-list userType="activator" />
        </div>

        <!-- EPP Tab -->
        {{-- <div id="main-content-epp" class="hidden tab-content-panel">
            <x-user-list userType="epp" />
        </div> --}}
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
                button.style.opacity = '1';
                button.style.filter = 'brightness(1.2)';
                console.log('🔵 Attivato bottone:', tabName);
            } else {
                button.style.opacity = '0.5';
                button.style.filter = 'brightness(0.7)';
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
    transition: transform 0.2s ease-in-out !important;
}

.tab-button:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

.tab-button:hover {
    transform: scale(1.18) !important;
}

.tab-button:active {
    transform: scale(1.10) !important;
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
