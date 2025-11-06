{{-- resources/views/egis/partials/sidebar/utility-manager-section.blade.php --}}
{{--
    Sezione utility manager dell'EGI (solo per creator)
    ORIGINE: righe 158-163 di show.blade.php
    VARIABILI: $egi
    
    MODALE: Apre utility-manager in modale full-screen
--}}

{{-- Utility Manager Modal Trigger (solo per creator non pubblicato) --}}
@if(auth()->id() === $egi->user_id && $egi->is_published == 0)
    <div class="pt-6 border-t border-gray-700/50">
        {{-- Trigger Button per aprire modale --}}
        <button type="button"
            onclick="openUtilityManager{{ $egi->id }}()"
            class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-orange-600/80 to-orange-700/80 hover:from-orange-600 hover:to-orange-700 text-white px-4 py-3 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span>⚡ {{ __('utility.manage_utility') ?? 'Gestisci Utility' }}</span>
        </button>
        
        {{-- Utility Manager Modal Component (nascosto fino all'apertura) --}}
        <x-utility.utility-manager :egi="$egi" />
    </div>
@endif
