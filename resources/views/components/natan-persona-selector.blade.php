{{-- N.A.T.A.N. Persona Selector Component --}}
@php
    use App\Config\NatanPersonas;
    $personas = NatanPersonas::getAll();
@endphp

<div class="natan-persona-selector mb-2 rounded-xl border border-gray-200 bg-white p-2 shadow-sm sm:mb-4 sm:p-4">
    <div class="mb-2 flex w-full items-center justify-between sm:mb-3">
        <button type="button" id="personaSelectorToggle"
            class="flex flex-1 items-center gap-1.5 sm:cursor-default sm:gap-2">
            <span class="material-icons text-sm text-[#1B365D] sm:text-lg">psychology</span>
            <h3 class="text-xs font-semibold text-[#1B365D] sm:text-sm">Scegli il Consulente</h3>
            <span id="personaSelectorIcon" class="material-icons text-sm text-gray-500 sm:hidden">expand_more</span>
        </button>
        <button type="button" id="personaInfoToggle" class="rounded-lg p-0.5 transition-colors hover:bg-gray-100 sm:p-1"
            title="Maggiori informazioni">
            <span class="material-icons text-xs text-gray-400 sm:text-sm">info</span>
        </button>
    </div>

    {{-- Collapsible Content - Nascosto su mobile, sempre visibile su desktop --}}
    <div id="personaSelectorContent" class="hidden sm:block">
        {{-- Info Panel (collapsible) - Ottimizzato mobile --}}
        <div id="personaInfoPanel"
            class="mb-2 hidden rounded-lg bg-blue-50 p-2 text-[10px] text-blue-800 sm:mb-3 sm:p-3 sm:text-xs">
            <p class="mb-1"><strong>Modalità Auto:</strong> N.A.T.A.N. sceglie automaticamente l'esperto più adatto.
            </p>
            <p><strong>Selezione Manuale:</strong> Scegli tu quale tipo di consulenza ricevere.</p>
        </div>

        {{-- Persona Pills - Ottimizzato mobile --}}
        <div class="flex flex-wrap gap-1.5 sm:gap-2">
            {{-- Auto Mode --}}
            <button data-persona="auto"
                class="persona-pill active flex items-center gap-1 rounded-full border-2 px-2.5 py-1.5 text-xs font-medium transition-all sm:gap-2 sm:px-4 sm:py-2 sm:text-sm"
                title="Selezione automatica basata sulla tua domanda">
                <span class="text-base sm:text-lg">✨</span>
                <span>Auto</span>
            </button>

            {{-- Individual Personas --}}
            @foreach ($personas as $personaId => $persona)
                <button data-persona="{{ $personaId }}"
                    class="persona-pill flex items-center gap-1 rounded-full border-2 px-2.5 py-1.5 text-xs font-medium transition-all sm:gap-2 sm:px-4 sm:py-2 sm:text-sm"
                    title="{{ $persona['description'] }}">
                    <span class="text-base sm:text-lg">{{ $persona['icon'] }}</span>
                    <span>{{ $persona['name'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- Selected Persona Details - Ottimizzato mobile --}}
        <div id="selectedPersonaDetails" class="mt-2 hidden rounded-lg bg-gray-50 p-2 sm:mt-3 sm:p-3">
            <div class="flex items-start gap-1.5 sm:gap-2">
                <span id="selectedPersonaIcon" class="text-xl sm:text-2xl"></span>
                <div class="flex-1">
                    <div class="text-sm font-medium text-[#1B365D] sm:text-base" id="selectedPersonaName"></div>
                    <div class="text-[10px] text-gray-600 sm:text-xs" id="selectedPersonaDesc"></div>
                    <div class="mt-1.5 flex flex-wrap gap-1 sm:mt-2">
                        <span class="text-[10px] text-gray-500 sm:text-xs">Esperto in:</span>
                        <div id="selectedPersonaExpertise" class="flex flex-wrap gap-1"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- End Collapsible Content --}}
</div>

<style>
    .persona-pill {
        border-color: #d1d5db;
        background-color: white;
        color: #374151;
        cursor: pointer;
    }

    .persona-pill:hover {
        border-color: #2D5016;
        background-color: #f0fdf4;
        color: #2D5016;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .persona-pill.active {
        border-color: #2D5016;
        background-color: #2D5016;
        color: white;
    }

    .persona-pill.active:hover {
        background-color: #3D6026;
        border-color: #3D6026;
    }
</style>

<script>
    // Persona selector data
    const personasData = @json($personas);

    // Initialize persona selector
    document.addEventListener('DOMContentLoaded', function() {
        const pills = document.querySelectorAll('.persona-pill');
        const infoToggle = document.getElementById('personaInfoToggle');
        const infoPanel = document.getElementById('personaInfoPanel');
        const detailsPanel = document.getElementById('selectedPersonaDetails');

        // Toggle persona selector content on mobile
        const selectorToggle = document.getElementById('personaSelectorToggle');
        const selectorContent = document.getElementById('personaSelectorContent');
        const selectorIcon = document.getElementById('personaSelectorIcon');

        if (selectorToggle && selectorContent && selectorIcon) {
            selectorToggle.addEventListener('click', function(e) {
                e.preventDefault();
                const isHidden = selectorContent.classList.contains('hidden');

                if (isHidden) {
                    selectorContent.classList.remove('hidden');
                    selectorIcon.textContent = 'expand_less';
                } else {
                    selectorContent.classList.add('hidden');
                    selectorIcon.textContent = 'expand_more';
                }
            });
        }

        // Toggle info panel
        if (infoToggle && infoPanel) {
            infoToggle.addEventListener('click', () => {
                infoPanel.classList.toggle('hidden');
            });
        }

        // Handle persona selection
        pills.forEach(pill => {
            pill.addEventListener('click', function() {
                const selectedPersona = this.getAttribute('data-persona');

                // Update active state
                pills.forEach(p => p.classList.remove('active'));
                this.classList.add('active');

                // Update details panel
                if (selectedPersona === 'auto') {
                    detailsPanel.classList.add('hidden');
                } else {
                    showPersonaDetails(selectedPersona);
                }

                // Store selection (will be used by chat JS)
                window.selectedPersona = selectedPersona === 'auto' ? null : selectedPersona;

                console.log('[Persona Selector] Selected:', selectedPersona);
            });
        });

        // Initialize with Auto mode
        window.selectedPersona = null;
    });

    function showPersonaDetails(personaId) {
        const persona = personasData[personaId];
        if (!persona) return;

        const detailsPanel = document.getElementById('selectedPersonaDetails');
        const iconEl = document.getElementById('selectedPersonaIcon');
        const nameEl = document.getElementById('selectedPersonaName');
        const descEl = document.getElementById('selectedPersonaDesc');
        const expertiseEl = document.getElementById('selectedPersonaExpertise');

        iconEl.textContent = persona.icon;
        nameEl.textContent = persona.name;
        descEl.textContent = persona.description;

        // Show expertise tags - Ottimizzato mobile
        expertiseEl.innerHTML = persona.expertise.map(exp =>
            `<span class="inline-block rounded bg-gray-200 px-1.5 py-0.5 text-[10px] text-gray-700 sm:px-2 sm:text-xs">${exp}</span>`
        ).join('');

        detailsPanel.classList.remove('hidden');
    }
</script>
