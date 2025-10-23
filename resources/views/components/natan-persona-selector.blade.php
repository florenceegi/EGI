{{-- N.A.T.A.N. Persona Selector Component --}}
@php
    use App\Config\NatanPersonas;
    $personas = NatanPersonas::getAll();
@endphp

<div class="natan-persona-selector mb-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
    <div class="mb-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="material-icons text-lg text-[#1B365D]">psychology</span>
            <h3 class="text-sm font-semibold text-[#1B365D]">Scegli il Consulente</h3>
        </div>
        <button id="personaInfoToggle" class="rounded-lg p-1 transition-colors hover:bg-gray-100"
            title="Maggiori informazioni">
            <span class="material-icons text-sm text-gray-400">info</span>
        </button>
    </div>

    {{-- Info Panel (collapsible) --}}
    <div id="personaInfoPanel" class="mb-3 hidden rounded-lg bg-blue-50 p-3 text-xs text-blue-800">
        <p class="mb-1"><strong>Modalità Auto:</strong> N.A.T.A.N. sceglie automaticamente l'esperto più adatto alla tua domanda.</p>
        <p><strong>Selezione Manuale:</strong> Scegli tu quale tipo di consulenza ricevere.</p>
    </div>

    {{-- Persona Pills --}}
    <div class="flex flex-wrap gap-2">
        {{-- Auto Mode --}}
        <button data-persona="auto" 
                class="persona-pill active flex items-center gap-2 rounded-full border-2 px-4 py-2 text-sm font-medium transition-all"
                title="Selezione automatica basata sulla tua domanda">
            <span class="text-lg">✨</span>
            <span>Auto</span>
        </button>

        {{-- Individual Personas --}}
        @foreach ($personas as $personaId => $persona)
            <button data-persona="{{ $personaId }}" 
                    class="persona-pill flex items-center gap-2 rounded-full border-2 px-4 py-2 text-sm font-medium transition-all"
                    title="{{ $persona['description'] }}">
                <span class="text-lg">{{ $persona['icon'] }}</span>
                <span>{{ $persona['name'] }}</span>
            </button>
        @endforeach
    </div>

    {{-- Selected Persona Details --}}
    <div id="selectedPersonaDetails" class="mt-3 hidden rounded-lg bg-gray-50 p-3">
        <div class="flex items-start gap-2">
            <span id="selectedPersonaIcon" class="text-2xl"></span>
            <div class="flex-1">
                <div class="font-medium text-[#1B365D]" id="selectedPersonaName"></div>
                <div class="text-xs text-gray-600" id="selectedPersonaDesc"></div>
                <div class="mt-2 flex flex-wrap gap-1">
                    <span class="text-xs text-gray-500">Esperto in:</span>
                    <div id="selectedPersonaExpertise" class="flex flex-wrap gap-1"></div>
                </div>
            </div>
        </div>
    </div>
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
        
        // Show expertise tags
        expertiseEl.innerHTML = persona.expertise.map(exp => 
            `<span class="inline-block rounded bg-gray-200 px-2 py-0.5 text-xs text-gray-700">${exp}</span>`
        ).join('');
        
        detailsPanel.classList.remove('hidden');
    }
</script>

