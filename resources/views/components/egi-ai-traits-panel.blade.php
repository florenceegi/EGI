<?php
// File: resources/views/components/egi-ai-traits-panel.blade.php
?>
{{--
    Component: EGI AI Traits Panel OS3.0
    Panel for AI-powered trait generation with Claude Vision

    @package Components
    @author Padmin D. Curtis (AI Partner OS3.0)
    @version 1.0.0 (FlorenceEGI - AI Traits Generation)
    @date 2025-10-21
    @purpose Allow creators to generate NFT traits using N.A.T.A.N AI

    Props:
    - egi: Egi model instance
    - isCreator: Boolean indicating if current user is the creator
--}}

@props(['egi', 'isCreator'])

@if (!$isCreator)
    @php return; @endphp
@endif

<div class="overflow-hidden rounded-2xl border-2 border-purple-200 bg-white shadow-lg"
    id="ai-traits-panel-{{ $egi->id }}">
    {{-- Header with N.A.T.A.N AI Branding --}}
    <div class="bg-gradient-to-r from-purple-700 to-indigo-600 px-6 py-4">
        <div class="flex items-center gap-3">
            <i class="fas fa-brain text-2xl text-white"></i>
            <div>
                <h3 class="text-xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
                    N.A.T.A.N AI Traits
                </h3>
                <p class="text-sm text-purple-100">Genera automaticamente traits con intelligenza artificiale</p>
            </div>
        </div>
    </div>

    <div class="space-y-6 p-6">
        {{-- Info Box --}}
        <div class="rounded-xl border-2 border-purple-200 bg-gradient-to-br from-purple-50 to-indigo-50 p-5">
            <div class="flex items-start gap-4">
                <i class="fas fa-magic mt-1 text-2xl text-purple-600"></i>
                <div>
                    <h4 class="mb-2 text-lg font-bold text-purple-900">
                        Genera Traits Automaticamente
                    </h4>
                    <p class="mb-2 text-sm text-purple-800">
                        N.A.T.A.N analizzerà visivamente l'immagine della tua opera e proporrà traits NFT
                        personalizzati.
                        L'AI identifica materiali, colori, stile, dimensioni e caratteristiche uniche.
                    </p>
                    <ul class="list-inside list-disc space-y-1 text-xs text-purple-700">
                        <li>Analisi visiva con Claude Vision</li>
                        <li>Match automatico con traits esistenti</li>
                        <li>Proposta di nuovi traits personalizzati</li>
                        <li>Puoi approvare, rifiutare o modificare ogni proposta</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Generation Form --}}
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
            <form id="ai-traits-generate-form-{{ $egi->id }}"
                onsubmit="return handleTraitsGenerate(event, {{ $egi->id }});">
                @csrf

                <label for="requested_count_{{ $egi->id }}"
                    class="mb-2 block text-sm font-medium text-gray-700">
                    {{ __('egi_dual_arch.ai.requested_count') }}
                </label>

                <div class="mb-4 flex items-center gap-3">
                    <input type="range" id="requested_count_{{ $egi->id }}" name="requested_count"
                        min="1" max="10" value="5"
                        class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-purple-200"
                        oninput="document.getElementById('count_display_{{ $egi->id }}').textContent = this.value;">

                    <span id="count_display_{{ $egi->id }}"
                        class="inline-flex h-12 w-12 items-center justify-center rounded-lg bg-purple-600 text-xl font-bold text-white">
                        5
                    </span>
                </div>

                <button type="submit"
                    class="flex w-full items-center justify-center gap-3 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 font-bold text-white shadow-md transition-all duration-200 hover:from-purple-700 hover:to-indigo-700 hover:shadow-lg">
                    <i class="fas fa-wand-magic-sparkles"></i>
                    {{ __('egi_dual_arch.ai.generate_traits') }}
                </button>
            </form>
        </div>

        {{-- Pending Generations (if any) --}}
        <div id="pending-generations-{{ $egi->id }}" class="hidden space-y-4">
            <div class="border-t border-gray-200 pt-4">
                <h5 class="mb-3 font-semibold text-gray-900">
                    <i class="fas fa-clock-rotate-left mr-2"></i>
                    Generazioni in corso
                </h5>
                <div id="generation-list-{{ $egi->id }}" class="space-y-3">
                    {{-- Populated via JavaScript --}}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for AI Traits Generation --}}
@push('scripts')
    <script>
        /**
         * Handle AI Traits Generation Request
         */
        async function handleTraitsGenerate(event, egiId) {
            event.preventDefault();

            const form = document.getElementById(`ai-traits-generate-form-${egiId}`);
            const formData = new FormData(form);
            const requestedCount = formData.get('requested_count');

            // Confirm
            const result = await Swal.fire({
                title: 'Genera Traits con AI?',
                html: `<p>N.A.T.A.N analizzerà l'immagine e proporrà <strong>${requestedCount} traits</strong>.</p>
                       <p class="text-sm text-gray-600 mt-2">Potrai revisiona re ogni proposta prima dell'applicazione.</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-wand-magic-sparkles mr-2"></i>Genera',
                cancelButtonText: 'Annulla',
                confirmButtonColor: '#9333ea',
                cancelButtonColor: '#6b7280',
            });

            if (!result.isConfirmed) return false;

            // Show loading
            Swal.fire({
                title: 'Analizzando...',
                html: '<p>N.A.T.A.N sta analizzando l\'immagine con Claude Vision...</p><p class="text-sm text-gray-600 mt-2">Questo può richiedere 30-60 secondi.</p>',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch(`/egi/${egiId}/dual-arch/traits/generate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        requested_count: parseInt(requestedCount)
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    // Success - show proposals
                    await Swal.fire({
                        title: 'Analisi Completata!',
                        html: `<p>${data.message}</p><p class="text-sm text-gray-600 mt-2">Ricarica la pagina per vedere le proposte.</p>`,
                        icon: 'success',
                        confirmButtonText: 'Ricarica Pagina',
                        confirmButtonColor: '#9333ea',
                    });

                    // Reload page to show proposals
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Errore sconosciuto');
                }
            } catch (error) {
                console.error('AI Traits Generation Error:', error);

                Swal.fire({
                    title: 'Errore',
                    text: error.message || 'Si è verificato un errore durante la generazione dei traits.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc2626',
                });
            }

            return false;
        }
    </script>
@endpush

